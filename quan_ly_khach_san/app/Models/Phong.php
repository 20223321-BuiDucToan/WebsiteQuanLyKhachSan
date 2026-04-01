<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Phong extends Model
{
    use HasFactory;

    protected $table = 'phong';

    protected $fillable = [
        'ma_phong',
        'so_phong',
        'loai_phong_id',
        'tang',
        'trang_thai',
        'tinh_trang_ve_sinh',
        'tinh_trang_hoat_dong',
        'gia_mac_dinh',
        'ghi_chu',
    ];

    protected function casts(): array
    {
        return [
            'gia_mac_dinh' => 'decimal:2',
        ];
    }

    public function loaiPhong(): BelongsTo
    {
        return $this->belongsTo(LoaiPhong::class, 'loai_phong_id');
    }

    public function chiTietDatPhong(): HasMany
    {
        return $this->hasMany(ChiTietDatPhong::class, 'phong_id');
    }
}
