<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class ProjectBilling extends Model
{
	protected $table = 'project_billing';
	protected $primaryKey = 'billing_id';
	public $timestamps = false;
	protected $fillable = ['kategori_layanan', 'tipe_pengadaan', 'priode', 'due_date_kontrak', 'tgl_pembuatan_ba', 'tgl_paraf_pm', 'tgl_submit_dokumen', 'tgl_permintaan_invoice', 'status', 'note'];

	protected $casts = [
		'nilai_bulan' => 'decimal:2',
		'due_date_kontrak' => 'date',
		'tgl_pembuatan_ba' => 'date',
		'tgl_paraf_pm' => 'date',
		'tgl_submit_dokumen' => 'date',
		'tgl_permintaan_invoice' => 'date',
	];

	public function project()
	{
		return $this->belongsTo(Project::class, 'project_id', 'project_id');
	}

	protected function name(): Attribute
	{
		return Attribute::get(fn () => $this->project?->project_name ?? '-');
	}

	protected function type(): Attribute
	{
		return Attribute::get(fn () => $this->kategori_layanan === 'OTM' ? 'one_time' : 'monthly');
	}

	protected function amount(): Attribute
	{
		return Attribute::get(fn () => $this->project?->nilai_kontrak ?? $this->nilai_bulan ?? 0);
	}

	protected function pm(): Attribute
	{
		return Attribute::get(fn () => $this->project?->pm?->employ_name);
	}

	protected function userName(): Attribute
	{
		return Attribute::get(fn () => $this->project?->user);
	}

	protected function procurementType(): Attribute
	{
		return Attribute::get(fn () => $this->tipe_pengadaan ?: '-');
	}

	protected function costCenter(): Attribute
	{
		return Attribute::get(fn () => $this->project?->cost_center);
	}

	protected function contractReference(): Attribute
	{
		return Attribute::get(fn () => $this->project?->no_kontrak);
	}

	protected function procurementPeriod(): Attribute
	{
		return Attribute::get(fn () => $this->priode ?: '-');
	}

	protected function contractDate(): Attribute
	{
		return Attribute::get(fn () => $this->project?->tgl_kontrak);
	}

	protected function dueDate(): Attribute
	{
		return Attribute::get(fn () => $this->due_date_kontrak);
	}

	protected function occurredOn(): Attribute
	{
		return Attribute::get(fn () => $this->tgl_pembuatan_ba ?? now());
	}
}
