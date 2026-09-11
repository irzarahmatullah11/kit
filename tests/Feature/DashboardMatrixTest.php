<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DashboardMatrixTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_matrix_overview_and_rows(): void
    {
        DB::table('role')->insert([
            ['role_id' => 1, 'role_name' => 'pm'],
            ['role_id' => 2, 'role_name' => 'pmo'],
        ]);

        DB::table('employ')->insert([
            ['employ_id' => 1, 'employ_name' => 'Mr A', 'email' => 'mra@example.com', 'password' => bcrypt('password123'), 'role' => 1],
            ['employ_id' => 2, 'employ_name' => 'Ms A', 'email' => 'msa@example.com', 'password' => bcrypt('password123'), 'role' => 2],
        ]);

        DB::table('project')->insert([
            ['project_id' => 1, 'project_name' => 'Project ABC', 'user' => 'PT ABC', 'cost_center' => 'KDXXXX', 'no_kontrak' => 'INFRA/XXX/001', 'nilai_kontrak' => 1000000, 'tgl_kontrak' => '2026-03-01', 'pm_id' => 1, 'pmo_id' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('project_billing')->insert([
            ['billing_id' => 1, 'project_id' => 1, 'kategori_layanan' => 'MS', 'tipe_pengadaan' => null, 'priode' => 'Juni', 'nilai_bulan' => 150000, 'due_date_kontrak' => null, 'tgl_pembuatan_ba' => '2026-03-01', 'tgl_paraf_pm' => '2026-03-02', 'tgl_submit_dokumen' => '2026-03-03', 'tgl_permintaan_invoice' => '2026-03-04', 'status' => 'Done', 'note' => 'test note', 'file_kontrak' => null, 'file_ba' => null],
        ]);

        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Matrix Dashboard');
        $response->assertSee('Project billing matrix');
        $response->assertSee('Project ABC');
        $response->assertSee('Done');
    }

    public function test_dashboard_can_filter_rows_by_status(): void
    {
        DB::table('role')->insert([
            ['role_id' => 1, 'role_name' => 'pm'],
            ['role_id' => 2, 'role_name' => 'pmo'],
        ]);

        DB::table('employ')->insert([
            ['employ_id' => 1, 'employ_name' => 'Mr A', 'email' => 'mra@example.com', 'password' => bcrypt('password123'), 'role' => 1],
            ['employ_id' => 2, 'employ_name' => 'Ms A', 'email' => 'msa@example.com', 'password' => bcrypt('password123'), 'role' => 2],
        ]);

        DB::table('project')->insert([
            ['project_id' => 1, 'project_name' => 'Project ABC', 'user' => 'PT ABC', 'cost_center' => 'KDXXXX', 'no_kontrak' => 'INFRA/XXX/001', 'nilai_kontrak' => 1000000, 'tgl_kontrak' => '2026-03-01', 'pm_id' => 1, 'pmo_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['project_id' => 2, 'project_name' => 'Project DEF', 'user' => 'PT DEF', 'cost_center' => 'KDXXXX', 'no_kontrak' => 'INFRA/XXX/002', 'nilai_kontrak' => 2000000, 'tgl_kontrak' => '2026-03-02', 'pm_id' => 1, 'pmo_id' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('project_billing')->insert([
            ['billing_id' => 1, 'project_id' => 1, 'kategori_layanan' => 'MS', 'tipe_pengadaan' => null, 'priode' => 'Juni', 'nilai_bulan' => 150000, 'due_date_kontrak' => null, 'tgl_pembuatan_ba' => '2026-03-01', 'tgl_paraf_pm' => '2026-03-02', 'tgl_submit_dokumen' => '2026-03-03', 'tgl_permintaan_invoice' => '2026-03-04', 'status' => 'Done', 'note' => 'done note', 'file_kontrak' => null, 'file_ba' => null],
            ['billing_id' => 2, 'project_id' => 2, 'kategori_layanan' => 'OTM', 'tipe_pengadaan' => 'Pengadaan Baru', 'priode' => 'Juli', 'nilai_bulan' => 250000, 'due_date_kontrak' => '2026-03-10', 'tgl_pembuatan_ba' => '2026-03-05', 'tgl_paraf_pm' => '2026-03-06', 'tgl_submit_dokumen' => '2026-03-07', 'tgl_permintaan_invoice' => '2026-03-08', 'status' => 'In Progress', 'note' => 'progress note', 'file_kontrak' => null, 'file_ba' => null],
        ]);

        $response = $this->get('/dashboard?status=Done');

        $response->assertStatus(200);
        $response->assertSee('Project ABC');
        $response->assertDontSee('Project DEF');
    }

    public function test_dashboard_can_filter_rows_by_service_and_search(): void
    {
        DB::table('role')->insert([
            ['role_id' => 1, 'role_name' => 'pm'],
            ['role_id' => 2, 'role_name' => 'pmo'],
        ]);

        DB::table('employ')->insert([
            ['employ_id' => 1, 'employ_name' => 'Mr A', 'email' => 'mra@example.com', 'password' => bcrypt('password123'), 'role' => 1],
            ['employ_id' => 2, 'employ_name' => 'Ms A', 'email' => 'msa@example.com', 'password' => bcrypt('password123'), 'role' => 2],
        ]);

        DB::table('project')->insert([
            ['project_id' => 1, 'project_name' => 'Project ABC', 'user' => 'PT ABC', 'cost_center' => 'KDXXXX', 'no_kontrak' => 'INFRA/XXX/001', 'nilai_kontrak' => 1000000, 'tgl_kontrak' => '2026-03-01', 'pm_id' => 1, 'pmo_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['project_id' => 2, 'project_name' => 'Project DEF', 'user' => 'PT DEF', 'cost_center' => 'KDXXXX', 'no_kontrak' => 'INFRA/XXX/002', 'nilai_kontrak' => 2000000, 'tgl_kontrak' => '2026-03-02', 'pm_id' => 1, 'pmo_id' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('project_billing')->insert([
            ['billing_id' => 1, 'project_id' => 1, 'kategori_layanan' => 'MS', 'tipe_pengadaan' => null, 'priode' => 'Juni', 'nilai_bulan' => 150000, 'due_date_kontrak' => null, 'tgl_pembuatan_ba' => '2026-03-01', 'tgl_paraf_pm' => '2026-03-02', 'tgl_submit_dokumen' => '2026-03-03', 'tgl_permintaan_invoice' => '2026-03-04', 'status' => 'Done', 'note' => 'done note', 'file_kontrak' => null, 'file_ba' => null],
            ['billing_id' => 2, 'project_id' => 2, 'kategori_layanan' => 'OTM', 'tipe_pengadaan' => 'Pengadaan Baru', 'priode' => 'Juli', 'nilai_bulan' => 250000, 'due_date_kontrak' => '2026-03-10', 'tgl_pembuatan_ba' => '2026-03-05', 'tgl_paraf_pm' => '2026-03-06', 'tgl_submit_dokumen' => '2026-03-07', 'tgl_permintaan_invoice' => '2026-03-08', 'status' => 'Done', 'note' => 'done note 2', 'file_kontrak' => null, 'file_ba' => null],
        ]);

        $response = $this->get('/dashboard?service=OTM&search=Project DEF');

        $response->assertStatus(200);
        $response->assertSee('Project DEF');
        $response->assertDontSee('Project ABC');
    }

    public function test_one_time_list_can_filter_rows_by_search(): void
    {
        DB::table('role')->insert([
            ['role_id' => 1, 'role_name' => 'pm'],
            ['role_id' => 2, 'role_name' => 'pmo'],
        ]);

        DB::table('employ')->insert([
            ['employ_id' => 1, 'employ_name' => 'Mr A', 'email' => 'mra@example.com', 'password' => bcrypt('password123'), 'role' => 1],
            ['employ_id' => 2, 'employ_name' => 'Ms A', 'email' => 'msa@example.com', 'password' => bcrypt('password123'), 'role' => 2],
        ]);

        DB::table('project')->insert([
            ['project_id' => 1, 'project_name' => 'Project ABC', 'user' => 'PT ABC', 'cost_center' => 'KDXXXX', 'no_kontrak' => 'INFRA/XXX/001', 'nilai_kontrak' => 1000000, 'tgl_kontrak' => '2026-03-01', 'pm_id' => 1, 'pmo_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['project_id' => 2, 'project_name' => 'Project DEF', 'user' => 'PT DEF', 'cost_center' => 'KDXXXX', 'no_kontrak' => 'INFRA/XXX/002', 'nilai_kontrak' => 2000000, 'tgl_kontrak' => '2026-03-02', 'pm_id' => 1, 'pmo_id' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('project_billing')->insert([
            ['billing_id' => 1, 'project_id' => 1, 'kategori_layanan' => 'OTM', 'tipe_pengadaan' => 'Pengadaan Baru', 'priode' => 'Juni', 'nilai_bulan' => 150000, 'due_date_kontrak' => '2026-03-15', 'tgl_pembuatan_ba' => '2026-03-01', 'tgl_paraf_pm' => '2026-03-02', 'tgl_submit_dokumen' => '2026-03-03', 'tgl_permintaan_invoice' => '2026-03-04', 'status' => 'Done', 'note' => 'one time note', 'file_kontrak' => null, 'file_ba' => null],
            ['billing_id' => 2, 'project_id' => 2, 'kategori_layanan' => 'OTM', 'tipe_pengadaan' => 'Renewal', 'priode' => 'Juli', 'nilai_bulan' => 250000, 'due_date_kontrak' => '2026-03-20', 'tgl_pembuatan_ba' => '2026-03-05', 'tgl_paraf_pm' => '2026-03-06', 'tgl_submit_dokumen' => '2026-03-07', 'tgl_permintaan_invoice' => '2026-03-08', 'status' => 'In Progress', 'note' => 'other note', 'file_kontrak' => null, 'file_ba' => null],
        ]);

        $response = $this->get('/payments/one-time?search=Project DEF');

        $response->assertStatus(200);
        $response->assertSee('Project DEF');
        $response->assertDontSee('Project ABC');
    }
}
