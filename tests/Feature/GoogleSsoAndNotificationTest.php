<?php

namespace Tests\Feature;

use App\Jobs\KirimEmailSelamatDatang;
use App\Mail\LoginBaruMail;
use App\Mail\SelamatDatangMail;
use App\Models\User;
use App\Notifications\UserBaruNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Tests\TestCase;

class GoogleSsoAndNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_and_register_pages_display_google_sso_button(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee(route('auth.google'))
            ->assertSee('Masuk dengan Google');

        $this->get(route('register'))
            ->assertOk()
            ->assertSee(route('auth.google'))
            ->assertSee('Google');
    }

    public function test_google_redirect_includes_gmail_hosted_domain_parameter(): void
    {
        config([
            'services.google.client_id' => 'test-client-id',
            'services.google.client_secret' => 'test-client-secret',
            'services.google.redirect' => 'http://localhost:8000/auth/google/callback',
        ]);

        $response = $this->get(route('auth.google'));

        $response->assertRedirect();
        $location = (string) $response->headers->get('Location');
        $this->assertStringContainsString('accounts.google.com', $location);
        $this->assertStringContainsString('hd=gmail.com', $location);
    }

    public function test_google_callback_rejects_non_gmail_email(): void
    {
        Socialite::fake('google', SocialiteUser::fake([
            'id' => 'google-999',
            'name' => 'Non Gmail User',
            'email' => 'mahasiswa@yahoo.com',
            'avatar' => 'https://example.com/avatar.jpg',
        ]));

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'mahasiswa@yahoo.com']);
    }

    public function test_google_callback_handles_cancelled_login_gracefully(): void
    {
        $response = $this->get(route('auth.google.callback', ['error' => 'access_denied']));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_new_google_user_registration_dispatches_job_notification_and_login_mail(): void
    {
        Bus::fake();
        Mail::fake();
        Notification::fake();

        $admin = User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@ssologin.test',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        Socialite::fake('google', SocialiteUser::fake([
            'id' => 'google-12345',
            'name' => 'Siti Mahasiswa',
            'email' => 'siti.mahasiswa@gmail.com',
            'avatar' => 'https://lh3.googleusercontent.com/a/default-avatar',
        ]));

        $response = $this->withServerVariables(['REMOTE_ADDR' => '127.0.0.55'])
            ->get(route('auth.google.callback'));

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();

        $user = User::where('email', 'siti.mahasiswa@gmail.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('google-12345', $user->google_id);
        $this->assertSame('https://lh3.googleusercontent.com/a/default-avatar', $user->avatar);
        $this->assertNull($user->password);
        $this->assertSame('user', $user->role);
        $this->assertTrue($user->accounts()->exists());
        $this->assertTrue($user->categories()->exists());

        Bus::assertDispatched(KirimEmailSelamatDatang::class, function (KirimEmailSelamatDatang $job) use ($user): bool {
            return $job->user->is($user);
        });

        Notification::assertSentTo($admin, UserBaruNotification::class, function (UserBaruNotification $notification) use ($user): bool {
            return $notification->newUser->is($user);
        });

        Mail::assertQueued(LoginBaruMail::class, function (LoginBaruMail $mail) use ($user): bool {
            return $mail->hasTo($user->email) && $mail->ipAddress === '127.0.0.55';
        });
    }

    public function test_existing_google_user_login_only_queues_login_baru_mail(): void
    {
        Bus::fake();
        Mail::fake();
        Notification::fake();

        $existingUser = User::create([
            'name' => 'Budi Gmail',
            'email' => 'budi.gmail@gmail.com',
            'google_id' => 'google-777',
            'password' => null,
            'role' => 'user',
        ]);

        Socialite::fake('google', SocialiteUser::fake([
            'id' => 'google-777',
            'name' => 'Budi Gmail',
            'email' => 'budi.gmail@gmail.com',
            'avatar' => 'https://lh3.googleusercontent.com/a/updated-avatar',
        ]));

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($existingUser);

        Bus::assertNotDispatched(KirimEmailSelamatDatang::class);
        Notification::assertNothingSent();

        Mail::assertQueued(LoginBaruMail::class, function (LoginBaruMail $mail) use ($existingUser): bool {
            return $mail->hasTo($existingUser->email);
        });
    }

    public function test_regular_login_also_queues_login_baru_mail(): void
    {
        Mail::fake();

        $user = User::create([
            'name' => 'Regular User',
            'email' => 'regular.mail@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);

        $this->withServerVariables(['REMOTE_ADDR' => '192.168.10.25'])
            ->post(route('login.post'), [
                'email' => 'regular.mail@example.com',
                'password' => 'password123',
            ])
            ->assertRedirect(route('dashboard'));

        Mail::assertQueued(LoginBaruMail::class, function (LoginBaruMail $mail) use ($user): bool {
            return $mail->hasTo($user->email) && $mail->ipAddress === '192.168.10.25';
        });
    }

    public function test_markdown_mailables_and_welcome_job_render_and_send_properly(): void
    {
        Mail::fake();

        $user = User::create([
            'name' => 'Ani Markdown',
            'email' => 'ani.markdown@gmail.com',
            'password' => null,
            'role' => 'user',
        ]);

        (new KirimEmailSelamatDatang($user))->handle();

        Mail::assertSent(SelamatDatangMail::class, function (SelamatDatangMail $mail) use ($user): bool {
            return $mail->hasTo($user->email);
        });

        $welcomeMailable = new SelamatDatangMail($user);
        $welcomeMailable->assertSeeInHtml('Ani Markdown');
        $welcomeMailable->assertSeeInHtml('ani.markdown@gmail.com');
        $welcomeMailable->assertSeeInHtml('Buka Dashboard');

        $loginMailable = new LoginBaruMail($user, '25 Nov 2026 21:45:00', '10.20.30.40');
        $loginMailable->assertSeeInHtml('25 Nov 2026 21:45:00');
        $loginMailable->assertSeeInHtml('10.20.30.40');
    }

    public function test_admin_navbar_displays_unread_notifications_badge_and_marks_all_as_read(): void
    {
        Mail::fake();

        $admin = User::create([
            'name' => 'Admin Notif',
            'email' => 'admin.notif@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $newUser = User::create([
            'name' => 'Pengguna Baru',
            'email' => 'pengguna.baru@gmail.com',
            'password' => null,
            'role' => 'user',
        ]);

        $admin->notify(new UserBaruNotification($newUser, 'Google SSO'));

        $this->assertSame(1, $admin->fresh()->unreadNotifications()->count());

        // Regular user cannot see admin badge or hit mark-all-read endpoint
        $this->actingAs($newUser)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('admin-unread-badge');

        $this->actingAs($newUser)
            ->post(route('admin.notifications.markAllRead'))
            ->assertRedirect('/');

        // Admin sees unread badge and mark-all-read button
        $this->actingAs($admin)
            ->get(route('admin.index'))
            ->assertOk()
            ->assertSee('admin-unread-badge')
            ->assertSee('Tandai semua dibaca')
            ->assertSee('pengguna.baru@gmail.com');

        // Mark all as read
        $this->actingAs($admin)
            ->post(route('admin.notifications.markAllRead'))
            ->assertRedirect()
            ->assertSessionHas('pesan');

        $this->assertSame(0, $admin->fresh()->unreadNotifications()->count());

        $this->actingAs($admin)
            ->get(route('admin.index'))
            ->assertOk()
            ->assertDontSee('admin-unread-badge')
            ->assertDontSee('Tandai semua dibaca');
    }
}
