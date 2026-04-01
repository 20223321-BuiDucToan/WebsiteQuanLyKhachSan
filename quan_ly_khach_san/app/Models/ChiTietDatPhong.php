<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChiTietDatPhong extends Model
{
    use HasFactory;

    protected $table = 'chi_tiet_dat_phong';

    protected $fillable = [
        'dat_phong_id',
        'phong_id',
        'gia_phong',
        'so_dem',
        'so_nguoi_lon',
        'so_tre_em',
        'ngay_nhan_phong_thuc_te',
        'ngay_tra_phong_thuc_te',
        'trang_thai',
        'ghi_chu',
    ];

    protected function casts(): array
    {
        return [
            'gia_phong' => 'decimal:2',
            'ngay_nhan_phong_thuc_te' => 'datetime',
            'ngay_tra_phong_thuc_te' => 'datetime',
        ];
    }

    public function datPhong(): BelongsTo
    {
        return $this->belongsTo(DatPhong::class, 'dat_phong_id');
    }

    public function phong(): BelongsTo
    {
        return $this->belongsTo(Phong::class, 'phong_id');
    }
}
