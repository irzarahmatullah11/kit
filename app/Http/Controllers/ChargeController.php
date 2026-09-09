<?php

namespace App\Http\Controllers;

use App\Models\ProjectBilling;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChargeController extends Controller
{
    public function index(): View
    {
        return $this->page('dashboard', ProjectBilling::query());
    }

    public function oneTime(): View
    {
        return $this->page('one_time', ProjectBilling::where('kategori_layanan', 'OTM'));
    }

    public function monthly(): View
    {
        return $this->page('monthly', ProjectBilling::where('kategori_layanan', 'MS'));
    }

    public function edit(ProjectBilling $charge): View
    {
        $charge->load('project.pm');

        return view('charges.edit', compact('charge'));
    }

    public function update(Request $request, ProjectBilling $charge): RedirectResponse
    {
        $data = $request->validate([
            'project_name' => ['required', 'string', 'max:120'],
            'user' => ['required', 'string', 'max:120'],
            'cost_center' => ['required', 'string', 'max:80'],
            'no_kontrak' => ['required', 'string', 'max:120'],
            'nilai_kontrak' => ['required', 'numeric', 'min:0'],
            'tgl_kontrak' => ['required', 'date'],
            'kategori_layanan' => ['required', 'in:MS,OTM'],
            'tipe_pengadaan' => ['nullable', 'string', 'max:80'],
            'priode' => ['required', 'string', 'max:30'],
            'due_date_kontrak' => ['nullable', 'date'],
            'tgl_pembuatan_ba' => ['nullable', 'date'],
            'tgl_paraf_pm' => ['nullable', 'date'],
            'tgl_submit_dokumen' => ['nullable', 'date'],
            'tgl_permintaan_invoice' => ['nullable', 'date'],
            'status' => ['required', 'in:Done,In Progress'],
            'note' => ['nullable', 'string'],
        ]);

        $charge->project->update([
            'project_name' => $data['project_name'],
            'user' => $data['user'],
            'cost_center' => $data['cost_center'],
            'no_kontrak' => $data['no_kontrak'],
            'nilai_kontrak' => $data['nilai_kontrak'],
            'tgl_kontrak' => $data['tgl_kontrak'],
        ]);
        $charge->update(collect($data)->except(['project_name', 'user', 'cost_center', 'no_kontrak', 'nilai_kontrak', 'tgl_kontrak'])->all());

        $destination = $data['kategori_layanan'] === 'OTM' ? 'charges.one-time' : 'charges.monthly';

        return to_route($destination)->with('success', 'Pembayaran berhasil diperbarui.');
    }

    private function page(string $page, $query): View
    {
        $charges = $query->with('project.pm')->latest('billing_id')->paginate(8);
        $monthly = ProjectBilling::where('kategori_layanan', 'MS');
        $oneTime = ProjectBilling::where('kategori_layanan', 'OTM');

        return view('welcome', [
            'charges' => $charges,
            'page' => $page,
            'monthlyTotal' => $monthly->sum('nilai_bulan'),
            'monthlyCount' => $monthly->count(),
            'oneTimeTotal' => $oneTime->sum('nilai_bulan'),
            'oneTimeCount' => $oneTime->count(),
        ]);
    }
}
