<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoaiPhong extends Model
{
    use HasFactory;

    protected $table = 'loai_phong';

    protected $fillable = [
        'ma_loai_phong',
        'ten_loai_phong',
        'mo_ta',
        'gia_mot_dem',
        'so_nguoi_toi_da',
        'dien_tich',
        'so_giuong',
        'loai_giuong',
        'so_phong_tam',
        'co_ban_cong',
        'co_bep_rieng',
        'co_huong_bien',
        'trang_thai',
    ];

    protected function casts(): array
    {
        return [
            'gia_mot_dem' => 'decimal:2',
            'dien_tich' => 'decimal:2',
            'co_ban_cong' => 'boolean',
            'co_bep_rieng' => 'boolean',
            'co_huong_bien' => 'boolean',
        ];
    }

    public function phong(): HasMany
    {
        return $this->hasMany(Phong::class, 'loai_phong_id');
    }
}
