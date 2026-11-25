<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\PostGallery;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FinancialSeoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Update slug SEO untuk kategori finansial yang ada
        $categories = Category::all();
        foreach ($categories as $cat) {
            if (empty($cat->category_seo)) {
                $cat->category_seo = Str::slug($cat->name);
                $cat->save();
            }
        }

        // 2. Data Topik Edukasi Finansial FinTrack dengan SEO Slugs dan Infografis
        $financialTopics = [
            [
                'title' => 'Strategi Manajemen Arus Kas dan Dana Darurat FinTrack',
                'category' => 'Arus Kas',
                'description' => "Mengelola arus kas (cash flow) adalah fondasi utama kestabilan keuangan pribadi maupun bisnis.\n\nPelajari cara mencatat pos pengeluaran harian, memisahkan kebutuhan vs keinginan, dan mengalokasikan minimal 10-20% pendapatan bulanan untuk dana darurat cair yang mudah diakses saat situasi mendesak.",
                'picture' => 'financial_cashflow.png',
                'galleries' => [
                    [
                        'nama_galeri' => 'Diagram Alir Arus Kas Harian',
                        'keterangan' => 'Skema pemisahan arus kas masuk dari gaji dan pengeluaran tetap bulanan.',
                        'foto' => 'infografis_1.png',
                    ],
                    [
                        'nama_galeri' => 'Piramida Alokasi Dana Darurat',
                        'keterangan' => 'Panduan bertahap mengumpulkan dana darurat 3x, 6x, hingga 12x pengeluaran.',
                        'foto' => 'infografis_2.jpg',
                    ],
                    [
                        'nama_galeri' => 'Checklist Audit Pengeluaran Bocor Halus',
                        'keterangan' => 'Daftar pengeluaran mikro langganan digital yang sering tidak disadari.',
                        'foto' => 'infografis_3.png',
                    ],
                ],
            ],
            [
                'title' => 'Panduan Praktis Budgeting Gaji dengan Metode 50-30-20',
                'category' => 'Budgeting',
                'description' => "Metode 50/30/20 membagi penghasilan bersih bulanan menjadi 3 bagian yang proporsional:\n\n• 50% untuk kebutuhan pokok (Needs) seperti sewa, cicilan, makan, dan utilitas.\n• 30% untuk keinginan (Wants) seperti rekreasi dan hobi.\n• 20% untuk tabungan dan investasi masa depan (Savings & Investment).",
                'picture' => 'financial_budget.jpg',
                'galleries' => [
                    [
                        'nama_galeri' => 'Infografis Rasio Pos Anggaran 50-30-20',
                        'keterangan' => 'Visualisasi pembagian proporsi ideal penghasilan bulanan.',
                        'foto' => 'infografis_2.jpg',
                    ],
                    [
                        'nama_galeri' => 'Lembar Kerja Simulasi Penggajian',
                        'keterangan' => 'Contoh perhitungan alokasi gaji Rp 8.000.000 ke dalam masing-masing pos.',
                        'foto' => 'infografis_1.png',
                    ],
                ],
            ],
            [
                'title' => 'Tips Optimasi Portofolio Investasi dan Pengelolaan Aset',
                'category' => 'Investasi',
                'description' => "Membangun kekayaan jangka panjang memerlukan strategi investasi yang terarah dan disiplin.\n\nDiversifikasi portofolio ke dalam instrumen pasar uang, obligasi negara (SBN), dan reksadana saham membantu meminimalkan risiko volatilitas pasar sekaligus mengalahkan laju inflasi tahunan.",
                'picture' => 'financial_invest.png',
                'galleries' => [
                    [
                        'nama_galeri' => 'Matriks Profil Risiko dan Pilihan Aset',
                        'keterangan' => 'Perbandingan profil konservatif, moderat, dan agresif terhadap instrumen investasi.',
                        'foto' => 'infografis_3.png',
                    ],
                    [
                        'nama_galeri' => 'Kekuatan Bunga Berbunga (Compound Interest)',
                        'keterangan' => 'Grafik pertumbuhan modal investasi rutin selama horizon waktu 10 hingga 20 tahun.',
                        'foto' => 'infografis_1.png',
                    ],
                ],
            ],
        ];

        foreach ($financialTopics as $topicData) {
            $galleries = $topicData['galleries'];
            unset($topicData['galleries']);

            $post = Post::updateOrCreate(
                ['title' => $topicData['title']],
                [
                    'post_seo' => Str::slug($topicData['title']),
                    'category' => $topicData['category'],
                    'description' => $topicData['description'],
                    'picture' => $topicData['picture'],
                ]
            );

            foreach ($galleries as $g) {
                PostGallery::updateOrCreate(
                    [
                        'post_id' => $post->id,
                        'nama_galeri' => $g['nama_galeri'],
                    ],
                    [
                        'galeri_seo' => Str::slug($g['nama_galeri']),
                        'keterangan' => $g['keterangan'],
                        'foto' => $g['foto'],
                    ]
                );
            }
        }
    }
}
