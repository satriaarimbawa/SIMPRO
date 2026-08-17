<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed Admin
        DB::table('admins')->insert([
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Seed Perusahaan
        DB::table('perusahaans')->insert([
            [
                'nama_perusahaan' => 'WTM',
                'nama_direktur' => 'Direktur WTM',
                'jabatan_penandatangan' => 'Direktur Utama',
                'pakai_kertas_kop_fisik' => false,
                'file_template_surat_jalan' => null,
                'peta_sel_template' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama_perusahaan' => 'WKB',
                'nama_direktur' => 'Direktur WKB',
                'jabatan_penandatangan' => 'Direktur Utama',
                'pakai_kertas_kop_fisik' => false,
                'file_template_surat_jalan' => null,
                'peta_sel_template' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama_perusahaan' => 'WAHANA AGRO MANDIRI',
                'nama_direktur' => 'I MADE ALIT NEKER',
                'jabatan_penandatangan' => 'Direktur',
                'pakai_kertas_kop_fisik' => false,
                'file_template_surat_jalan' => null,
                'peta_sel_template' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);

        // Seed Pengaturan
        DB::table('pengaturans')->insert([
            'ambang_pengingat_hari' => 7,
            'tarif_ppn_persen' => 11,
            'tarif_pph_persen' => 1.5,
            'berlaku_sejak' => Carbon::now()->toDateString(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
