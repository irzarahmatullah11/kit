<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Insert Data Role[cite: 2]
        $roles = [
            ['role_name' => 'pm'],      
            ['role_name' => 'pmo'],     
            ['role_name' => 'manager'], 
        ];
        foreach ($roles as $role) {
            DB::table('role')->insert($role);
        }

        // 2. Insert Data Employee[cite: 1]
        $employees = [
            // PM (Role ID = 1)
            ['employ_name' => 'Mr A', 'email' => 'mra@example.com', 'password' => Hash::make('password123'), 'role' => 1],
            ['employ_name' => 'Mr B', 'email' => 'mrb@example.com', 'password' => Hash::make('password123'), 'role' => 1],
            ['employ_name' => 'Mr C', 'email' => 'mrc@example.com', 'password' => Hash::make('password123'), 'role' => 1],
            
            // PMO (Role ID = 2)
            ['employ_name' => 'Ms A', 'email' => 'msa@example.com', 'password' => Hash::make('password123'), 'role' => 2],
            ['employ_name' => 'Ms B', 'email' => 'msb@example.com', 'password' => Hash::make('password123'), 'role' => 2],
            ['employ_name' => 'Ms C', 'email' => 'msc@example.com', 'password' => Hash::make('password123'), 'role' => 2],
            
            // Manager (Role ID = 3)
            ['employ_name' => 'Manager 1', 'email' => 'manager@example.com', 'password' => Hash::make('password123'), 'role' => 3],
        ];
        foreach ($employees as $employ) {
            DB::table('employ')->insert($employ);
        }

        // 3. Insert Data Project[cite: 3]
        $projects = [
            // PROJECT DENGAN LAYANAN MS (Memiliki PMO)
            ['project_name' => 'Project ABC', 'user' => 'PT ABC', 'cost_center' => 'KDXXXX', 'no_kontrak' => 'INFRA/XXX/XXX/XXX', 'nilai_kontrak' => 1478849641.00, 'tgl_kontrak' => '2026-03-01', 'pm_id' => 1, 'pmo_id' => 4, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['project_name' => 'Project DEF', 'user' => 'PT DEF', 'cost_center' => 'KDXXXX', 'no_kontrak' => 'INFRA/XXX/XXX/XXX', 'nilai_kontrak' => 3948730168.00, 'tgl_kontrak' => '2026-04-01', 'pm_id' => 2, 'pmo_id' => 5, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['project_name' => 'Project STU', 'user' => 'PT XYZ', 'cost_center' => 'KDXXXX', 'no_kontrak' => 'INFRA/XXX/XXX/007', 'nilai_kontrak' => 1500000000.00, 'tgl_kontrak' => '2026-06-01', 'pm_id' => 1, 'pmo_id' => 4, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['project_name' => 'Project VWX', 'user' => 'PT LMN', 'cost_center' => 'KDXXXX', 'no_kontrak' => 'INFRA/XXX/XXX/008', 'nilai_kontrak' => 2100000000.00, 'tgl_kontrak' => '2026-06-15', 'pm_id' => 2, 'pmo_id' => 5, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['project_name' => 'Project YZA', 'user' => 'PT OPQ', 'cost_center' => 'KDXXXX', 'no_kontrak' => 'INFRA/XXX/XXX/009', 'nilai_kontrak' => 1750000000.00, 'tgl_kontrak' => '2026-07-01', 'pm_id' => 3, 'pmo_id' => 6, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['project_name' => 'Project BCD', 'user' => 'PT RST', 'cost_center' => 'KDXXXX', 'no_kontrak' => 'INFRA/XXX/XXX/010', 'nilai_kontrak' => 3200000000.00, 'tgl_kontrak' => '2026-07-10', 'pm_id' => 1, 'pmo_id' => 4, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['project_name' => 'Project EFG', 'user' => 'PT UVW', 'cost_center' => 'KDXXXX', 'no_kontrak' => 'INFRA/XXX/XXX/011', 'nilai_kontrak' => 1800500000.00, 'tgl_kontrak' => '2026-08-05', 'pm_id' => 2, 'pmo_id' => 5, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            
            // PROJECT DENGAN LAYANAN ONE TIME CHARGE (PMO di-set null)
            ['project_name' => 'Project HIJ', 'user' => 'PT XYZ', 'cost_center' => 'KDXXXX', 'no_kontrak' => 'INFRA/XXX/XXX/012', 'nilai_kontrak' => 2950000000.00, 'tgl_kontrak' => '2026-08-20', 'pm_id' => 3, 'pmo_id' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['project_name' => 'Project KLM', 'user' => 'PT LMN', 'cost_center' => 'KDXXXX', 'no_kontrak' => 'INFRA/XXX/XXX/013', 'nilai_kontrak' => 4100000000.00, 'tgl_kontrak' => '2026-09-01', 'pm_id' => 1, 'pmo_id' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['project_name' => 'Project NOP', 'user' => 'PT OPQ', 'cost_center' => 'KDXXXX', 'no_kontrak' => 'INFRA/XXX/XXX/014', 'nilai_kontrak' => 1250000000.00, 'tgl_kontrak' => '2026-09-15', 'pm_id' => 2, 'pmo_id' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['project_name' => 'Project QRS', 'user' => 'PT RST', 'cost_center' => 'KDXXXX', 'no_kontrak' => 'INFRA/XXX/XXX/015', 'nilai_kontrak' => 3400000000.00, 'tgl_kontrak' => '2026-10-01', 'pm_id' => 3, 'pmo_id' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['project_name' => 'Project TUV', 'user' => 'PT UVW', 'cost_center' => 'KDXXXX', 'no_kontrak' => 'INFRA/XXX/XXX/016', 'nilai_kontrak' => 2200000000.00, 'tgl_kontrak' => '2026-10-10', 'pm_id' => 1, 'pmo_id' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['project_name' => 'Project WXY', 'user' => 'PT XYZ', 'cost_center' => 'KDXXXX', 'no_kontrak' => 'INFRA/XXX/XXX/017', 'nilai_kontrak' => 5100000000.00, 'tgl_kontrak' => '2026-11-05', 'pm_id' => 2, 'pmo_id' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['project_name' => 'Project ZAB', 'user' => 'PT LMN', 'cost_center' => 'KDXXXX', 'no_kontrak' => 'INFRA/XXX/XXX/018', 'nilai_kontrak' => 1150000000.00, 'tgl_kontrak' => '2026-11-20', 'pm_id' => 3, 'pmo_id' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['project_name' => 'Project CDE', 'user' => 'PT OPQ', 'cost_center' => 'KDXXXX', 'no_kontrak' => 'INFRA/XXX/XXX/019', 'nilai_kontrak' => 2800000000.00, 'tgl_kontrak' => '2026-12-01', 'pm_id' => 1, 'pmo_id' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['project_name' => 'Project FGH', 'user' => 'PT RST', 'cost_center' => 'KDXXXX', 'no_kontrak' => 'INFRA/XXX/XXX/020', 'nilai_kontrak' => 1950000000.00, 'tgl_kontrak' => '2026-12-10', 'pm_id' => 2, 'pmo_id' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['project_name' => 'Project IJK', 'user' => 'PT UVW', 'cost_center' => 'KDXXXX', 'no_kontrak' => 'INFRA/XXX/XXX/021', 'nilai_kontrak' => 3600000000.00, 'tgl_kontrak' => '2026-12-20', 'pm_id' => 3, 'pmo_id' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ];
        foreach ($projects as $project) {
            DB::table('project')->insert($project);
        }

        // 4. Insert Data Project Billing[cite: 4]
        $billings = [
            // --- BILLING MS ---
            ['project_id' => 1, 'kategori_layanan' => 'MS', 'tipe_pengadaan' => null, 'priode' => 'Juni', 'nilai_bulan' => 270006425.00, 'due_date_kontrak' => null, 'tgl_pembuatan_ba' => '2026-03-01', 'tgl_paraf_pm' => '2026-03-01', 'tgl_submit_dokumen' => '2026-03-01', 'tgl_permintaan_invoice' => '2026-03-01', 'status' => 'Done', 'note' => null],
            ['project_id' => 2, 'kategori_layanan' => 'MS', 'tipe_pengadaan' => null, 'priode' => 'Juli', 'nilai_bulan' => 404252825.00, 'due_date_kontrak' => null, 'tgl_pembuatan_ba' => '2026-04-01', 'tgl_paraf_pm' => '2026-04-01', 'tgl_submit_dokumen' => '2026-04-01', 'tgl_permintaan_invoice' => '2026-04-01', 'status' => 'Done', 'note' => null],
            ['project_id' => 3, 'kategori_layanan' => 'MS', 'tipe_pengadaan' => null, 'priode' => 'Agustus', 'nilai_bulan' => 250000000.00, 'due_date_kontrak' => null, 'tgl_pembuatan_ba' => '2026-08-01', 'tgl_paraf_pm' => '2026-08-02', 'tgl_submit_dokumen' => '2026-08-05', 'tgl_permintaan_invoice' => '2026-08-10', 'status' => 'Done', 'note' => null],
            ['project_id' => 4, 'kategori_layanan' => 'MS', 'tipe_pengadaan' => null, 'priode' => 'September', 'nilai_bulan' => 350000000.00, 'due_date_kontrak' => null, 'tgl_pembuatan_ba' => '2026-09-01', 'tgl_paraf_pm' => '2026-09-02', 'tgl_submit_dokumen' => null, 'tgl_permintaan_invoice' => null, 'status' => 'In Progress', 'note' => 'ba/lhp belum dikirim'],
            ['project_id' => 5, 'kategori_layanan' => 'MS', 'tipe_pengadaan' => null, 'priode' => 'Oktober', 'nilai_bulan' => 150000000.00, 'due_date_kontrak' => null, 'tgl_pembuatan_ba' => '2026-10-01', 'tgl_paraf_pm' => '2026-10-02', 'tgl_submit_dokumen' => '2026-10-05', 'tgl_permintaan_invoice' => null, 'status' => 'In Progress', 'note' => 'invoice belum keluar'],
            ['project_id' => 6, 'kategori_layanan' => 'MS', 'tipe_pengadaan' => null, 'priode' => 'November', 'nilai_bulan' => 450000000.00, 'due_date_kontrak' => null, 'tgl_pembuatan_ba' => '2026-11-01', 'tgl_paraf_pm' => '2026-11-02', 'tgl_submit_dokumen' => '2026-11-05', 'tgl_permintaan_invoice' => '2026-11-10', 'status' => 'Done', 'note' => null],
            ['project_id' => 7, 'kategori_layanan' => 'MS', 'tipe_pengadaan' => null, 'priode' => 'Desember', 'nilai_bulan' => 275000000.00, 'due_date_kontrak' => null, 'tgl_pembuatan_ba' => '2026-12-01', 'tgl_paraf_pm' => '2026-12-02', 'tgl_submit_dokumen' => null, 'tgl_permintaan_invoice' => null, 'status' => 'In Progress', 'note' => 'ba/lhp belum dikirim'],

            // --- BILLING ONE TIME CHARGE ---
            ['project_id' => 8, 'kategori_layanan' => 'OTM', 'tipe_pengadaan' => 'Pengadaan Baru', 'priode' => 'Agustus', 'nilai_bulan' => null, 'due_date_kontrak' => '2026-08-31', 'tgl_pembuatan_ba' => '2026-08-10', 'tgl_paraf_pm' => '2026-08-12', 'tgl_submit_dokumen' => '2026-08-15', 'tgl_permintaan_invoice' => '2026-08-20', 'status' => 'Done', 'note' => null],
            ['project_id' => 9, 'kategori_layanan' => 'OTM', 'tipe_pengadaan' => 'Renewal', 'priode' => 'September', 'nilai_bulan' => null, 'due_date_kontrak' => '2026-09-30', 'tgl_pembuatan_ba' => '2026-09-10', 'tgl_paraf_pm' => '2026-09-12', 'tgl_submit_dokumen' => null, 'tgl_permintaan_invoice' => null, 'status' => 'In Progress', 'note' => 'ba/lhp belum dikirim'],
            ['project_id' => 10, 'kategori_layanan' => 'OTM', 'tipe_pengadaan' => 'Pengadaan Baru', 'priode' => 'Oktober', 'nilai_bulan' => null, 'due_date_kontrak' => '2026-10-31', 'tgl_pembuatan_ba' => '2026-10-10', 'tgl_paraf_pm' => '2026-10-12', 'tgl_submit_dokumen' => '2026-10-15', 'tgl_permintaan_invoice' => null, 'status' => 'In Progress', 'note' => 'invoice belum keluar'],
            ['project_id' => 11, 'kategori_layanan' => 'OTM', 'tipe_pengadaan' => 'Renewal', 'priode' => 'November', 'nilai_bulan' => null, 'due_date_kontrak' => '2026-11-30', 'tgl_pembuatan_ba' => '2026-11-10', 'tgl_paraf_pm' => '2026-11-12', 'tgl_submit_dokumen' => '2026-11-15', 'tgl_permintaan_invoice' => '2026-11-20', 'status' => 'Done', 'note' => null],
            ['project_id' => 12, 'kategori_layanan' => 'OTM', 'tipe_pengadaan' => 'Pengadaan Baru', 'priode' => 'Desember', 'nilai_bulan' => null, 'due_date_kontrak' => '2026-12-31', 'tgl_pembuatan_ba' => '2026-12-10', 'tgl_paraf_pm' => '2026-12-12', 'tgl_submit_dokumen' => '2026-12-15', 'tgl_permintaan_invoice' => '2026-12-20', 'status' => 'Done', 'note' => null],
            ['project_id' => 13, 'kategori_layanan' => 'OTM', 'tipe_pengadaan' => 'Renewal', 'priode' => 'Januari', 'nilai_bulan' => null, 'due_date_kontrak' => '2027-01-31', 'tgl_pembuatan_ba' => '2027-01-10', 'tgl_paraf_pm' => '2027-01-12', 'tgl_submit_dokumen' => null, 'tgl_permintaan_invoice' => null, 'status' => 'In Progress', 'note' => 'ba/lhp belum dikirim'],
            ['project_id' => 14, 'kategori_layanan' => 'OTM', 'tipe_pengadaan' => 'Pengadaan Baru', 'priode' => 'Februari', 'nilai_bulan' => null, 'due_date_kontrak' => '2027-02-28', 'tgl_pembuatan_ba' => '2027-02-10', 'tgl_paraf_pm' => '2027-02-12', 'tgl_submit_dokumen' => '2027-02-15', 'tgl_permintaan_invoice' => null, 'status' => 'In Progress', 'note' => 'invoice belum keluar'],
            ['project_id' => 15, 'kategori_layanan' => 'OTM', 'tipe_pengadaan' => 'Renewal', 'priode' => 'Maret', 'nilai_bulan' => null, 'due_date_kontrak' => '2027-03-31', 'tgl_pembuatan_ba' => '2027-03-10', 'tgl_paraf_pm' => '2027-03-12', 'tgl_submit_dokumen' => '2027-03-15', 'tgl_permintaan_invoice' => '2027-03-20', 'status' => 'Done', 'note' => null],
            ['project_id' => 16, 'kategori_layanan' => 'OTM', 'tipe_pengadaan' => 'Pengadaan Baru', 'priode' => 'April', 'nilai_bulan' => null, 'due_date_kontrak' => '2027-04-30', 'tgl_pembuatan_ba' => '2027-04-10', 'tgl_paraf_pm' => '2027-04-12', 'tgl_submit_dokumen' => '2027-04-15', 'tgl_permintaan_invoice' => '2027-04-20', 'status' => 'Done', 'note' => null],
            ['project_id' => 17, 'kategori_layanan' => 'OTM', 'tipe_pengadaan' => 'Renewal', 'priode' => 'Mei', 'nilai_bulan' => null, 'due_date_kontrak' => '2027-05-31', 'tgl_pembuatan_ba' => '2027-05-10', 'tgl_paraf_pm' => '2027-05-12', 'tgl_submit_dokumen' => null, 'tgl_permintaan_invoice' => null, 'status' => 'In Progress', 'note' => 'ba/lhp belum dikirim'],
        ];
        
        foreach ($billings as $billing) {
            DB::table('project_billing')->insert($billing);
        }
    }
}