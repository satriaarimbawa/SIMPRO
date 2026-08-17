<?php

namespace Tests\Feature;

use App\Helpers\HashId;
use App\Models\Admin;
use App\Models\Perusahaan;
use App\Models\Spk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpkHashIdTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;
    private Perusahaan $perusahaan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::create([
            'username' => 'admin_hash_test',
            'password' => bcrypt('password'),
        ]);

        $this->perusahaan = Perusahaan::create([
            'nama_perusahaan' => 'PT Hash Test',
            'nama_direktur' => 'Direktur Hash',
            'jabatan_penandatangan' => 'Direktur',
            'pakai_kertas_kop_fisik' => false,
        ]);
    }

    public function test_hashid_helper_encodes_and_decodes_correctly(): void
    {
        $id = 15;
        $hash = HashId::encode($id);

        $this->assertNotEmpty($hash);
        $this->assertNotEquals('15', $hash);
        $this->assertEquals(15, HashId::decode($hash));
    }

    public function test_hashid_helper_returns_null_for_invalid_tampered_hash(): void
    {
        $this->assertNull(HashId::decode('INVALIDHASH123'));
        $this->assertNull(HashId::decode('1'));
    }

    public function test_spk_show_route_accepts_hashed_id(): void
    {
        $spk = Spk::create([
            'no_spk' => 'SPK-HASH-001',
            'perusahaan_id' => $this->perusahaan->id,
            'nama_dinas' => 'Dinas Kesehatan',
            'kabupaten' => 'Badung',
            'nama_ppk' => 'PPK Test',
            'tanggal_spk' => '2026-08-15',
            'jumlah_termin' => 1,
        ]);

        $hashedId = $spk->hashed_id;

        $response = $this->actingAs($this->admin)
            ->get('/spk/' . $hashedId);

        $response->assertStatus(200);
        $response->assertSee('SPK-HASH-001');
    }

    public function test_spk_show_route_rejects_numeric_id_with_404(): void
    {
        $spk = Spk::create([
            'no_spk' => 'SPK-HASH-002',
            'perusahaan_id' => $this->perusahaan->id,
            'nama_dinas' => 'Dinas Pendidikan',
            'kabupaten' => 'Denpasar',
            'nama_ppk' => 'PPK Test',
            'tanggal_spk' => '2026-08-15',
            'jumlah_termin' => 1,
        ]);

        // Attempting to access using raw numeric ID must return 404
        $response = $this->actingAs($this->admin)
            ->get('/spk/' . $spk->id);

        $response->assertStatus(404);
    }
}
