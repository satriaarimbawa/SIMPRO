<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Perusahaan;
use App\Models\Spk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpkDuplicateTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;
    private Perusahaan $perusahaan;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an Admin user for auth
        $this->admin = Admin::create([
            'username' => 'admin_test',
            'password' => bcrypt('password'),
        ]);

        // Create a Perusahaan for SPK relations
        $this->perusahaan = Perusahaan::create([
            'nama_perusahaan' => 'PT Test Indonesia',
            'nama_direktur' => 'Direktur Test',
            'jabatan_penandatangan' => 'Direktur',
            'pakai_kertas_kop_fisik' => false,
        ]);
    }

    public function test_check_duplicate_returns_false_for_non_existent_spk(): void
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/spk/check-duplicate?no_spk=SPK-NOT-EXIST-123');

        $response->assertStatus(200);
        $response->assertJson(['exists' => false]);
    }

    public function test_check_duplicate_returns_true_for_existing_spk(): void
    {
        // Create an existing SPK
        Spk::create([
            'no_spk' => 'SPK-EXIST-123',
            'perusahaan_id' => $this->perusahaan->id,
            'nama_dinas' => 'Dinas Test',
            'kabupaten' => 'Kabupaten Test',
            'nama_ppk' => 'PPK Test',
            'tanggal_spk' => '2026-08-15',
            'jumlah_termin' => 1,
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson('/spk/check-duplicate?no_spk=SPK-EXIST-123');

        $response->assertStatus(200);
        $response->assertJson(['exists' => true]);
    }

    public function test_store_fails_validation_for_duplicate_no_spk(): void
    {
        // Create an existing SPK
        Spk::create([
            'no_spk' => 'SPK-DUPLICATE-456',
            'perusahaan_id' => $this->perusahaan->id,
            'nama_dinas' => 'Dinas Test',
            'kabupaten' => 'Kabupaten Test',
            'nama_ppk' => 'PPK Test',
            'tanggal_spk' => '2026-08-15',
            'jumlah_termin' => 1,
        ]);

        // Attempt to store another SPK with the same no_spk
        $response = $this->actingAs($this->admin)
            ->post('/spk', [
                'perusahaan_id' => $this->perusahaan->id,
                'no_spk' => 'SPK-DUPLICATE-456', // DUPLICATE!
                'tanggal_spk' => '2026-08-15',
                'nama_dinas' => 'Dinas Baru',
                'kabupaten' => 'Kabupaten Baru',
                'nama_ppk' => 'PPK Baru',
                'termins' => [
                    [
                        'tanggal_mulai' => '2026-08-15',
                        'tanggal_akhir' => '2026-09-15',
                        'nilai' => 1000000,
                        'kena_ppn' => 'on',
                        'items' => [
                            [
                                'nama_barang' => 'Nitrogen',
                                'jumlah' => 10,
                                'satuan' => 'Tabung',
                                'merk' => 'Nusa Penida',
                                'harga_satuan' => 100000
                            ]
                        ]
                    ]
                ]
            ]);

        $response->assertStatus(302); // Redirects back due to validation failure
        $response->assertSessionHasErrors('no_spk');
    }
}
