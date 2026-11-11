<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileCustomizationTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(array $attributes = []): User
    {
        return User::create(array_merge([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'phonenumber' => '08123456789',
            'theme_color' => 'primary',
        ], $attributes));
    }

    public function test_guest_cannot_access_profile_customization_endpoints(): void
    {
        $this->put(route('profile.update'), ['name' => 'Hacker'])->assertRedirect('/login');
        $this->put(route('profile.password'), [])->assertRedirect('/login');
        $this->delete(route('profile.photo.destroy'))->assertRedirect('/login');
    }

    public function test_user_can_update_profile_customization_fields(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'Budi Kustom',
            'email' => 'budi.kustom@example.com',
            'phonenumber' => '08987654321',
            'bio' => 'Saya suka mencatat keuangan harian di FinTrack.',
            'theme_color' => 'success',
        ]);

        $response->assertRedirect(route('profile'));
        $response->assertSessionHas('pesan');

        $user->refresh();
        $this->assertSame('Budi Kustom', $user->name);
        $this->assertSame('budi.kustom@example.com', $user->email);
        $this->assertSame('08987654321', $user->phonenumber);
        $this->assertSame('Saya suka mencatat keuangan harian di FinTrack.', $user->bio);
        $this->assertSame('success', $user->theme_color);
    }

    public function test_user_can_upload_profile_photo_and_view_it(): void
    {
        Storage::fake('public');
        $user = $this->createUser();
        $file = UploadedFile::fake()->image('avatar.png', 300, 300);

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'phonenumber' => $user->phonenumber,
            'bio' => 'Pengguna dengan foto profil',
            'theme_color' => 'info',
            'profile_photo' => $file,
        ]);

        $response->assertRedirect(route('profile'));
        $response->assertSessionHas('pesan');

        $user->refresh();
        $this->assertNotNull($user->profile_photo);
        $this->assertStringStartsWith('profile-photos/', $user->profile_photo);
        Storage::disk('public')->assertExists($user->profile_photo);

        $this->actingAs($user)
            ->get(route('profile'))
            ->assertOk()
            ->assertSee('storage/'.$user->profile_photo);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('storage/'.$user->profile_photo);
    }

    public function test_uploading_new_profile_photo_deletes_old_photo(): void
    {
        Storage::fake('public');
        $oldFile = UploadedFile::fake()->image('old.jpg');
        $oldPath = $oldFile->store('profile-photos', 'public');

        $user = $this->createUser(['profile_photo' => $oldPath]);
        Storage::disk('public')->assertExists($oldPath);

        $newFile = UploadedFile::fake()->image('new.jpg');

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'phonenumber' => $user->phonenumber,
            'theme_color' => 'primary',
            'profile_photo' => $newFile,
        ]);

        $response->assertRedirect(route('profile'));

        $user->refresh();
        $this->assertNotSame($oldPath, $user->profile_photo);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($user->profile_photo);
    }

    public function test_profile_photo_upload_validation_rejects_non_image_and_oversized_file(): void
    {
        Storage::fake('public');
        $user = $this->createUser();

        $invalidFile = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');
        $this->actingAs($user)
            ->put(route('profile.update'), [
                'name' => $user->name,
                'email' => $user->email,
                'theme_color' => 'primary',
                'profile_photo' => $invalidFile,
            ])
            ->assertSessionHasErrors('profile_photo');

        $oversizedImage = UploadedFile::fake()->image('huge.jpg')->size(2500);
        $this->actingAs($user)
            ->put(route('profile.update'), [
                'name' => $user->name,
                'email' => $user->email,
                'theme_color' => 'primary',
                'profile_photo' => $oversizedImage,
            ])
            ->assertSessionHasErrors('profile_photo');

        $user->refresh();
        $this->assertNull($user->profile_photo);
    }

    public function test_user_can_delete_profile_photo(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('avatar.webp');
        $path = $file->store('profile-photos', 'public');

        $user = $this->createUser(['profile_photo' => $path]);
        Storage::disk('public')->assertExists($path);

        $response = $this->actingAs($user)->delete(route('profile.photo.destroy'));
        $response->assertRedirect(route('profile'));
        $response->assertSessionHas('pesan');

        $user->refresh();
        $this->assertNull($user->profile_photo);
        Storage::disk('public')->assertMissing($path);

        // Menghapus saat tidak ada foto mengembalikan pesan error
        $this->actingAs($user)
            ->delete(route('profile.photo.destroy'))
            ->assertRedirect(route('profile'))
            ->assertSessionHas('error');
    }

    public function test_user_cannot_use_another_users_email_or_phonenumber(): void
    {
        $otherUser = $this->createUser([
            'email' => 'other@example.com',
            'phonenumber' => '08111111111',
        ]);

        $user = $this->createUser([
            'email' => 'mine@example.com',
            'phonenumber' => '08222222222',
        ]);

        $this->actingAs($user)
            ->put(route('profile.update'), [
                'name' => $user->name,
                'email' => $otherUser->email,
                'phonenumber' => $otherUser->phonenumber,
                'theme_color' => 'primary',
            ])
            ->assertSessionHasErrors(['email', 'phonenumber']);
    }

    public function test_user_can_update_password_with_valid_current_password(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)
            ->put(route('profile.password'), [
                'current_password' => 'wrong-password',
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ])
            ->assertSessionHasErrors('current_password', null, 'passwordUpdate');

        $this->actingAs($user)
            ->put(route('profile.password'), [
                'current_password' => 'password123',
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ])
            ->assertRedirect(route('profile'))
            ->assertSessionHas('pesan');

        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }
}
