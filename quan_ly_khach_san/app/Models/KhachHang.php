<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KhachHang extends Model
{
    use HasFactory;

    protected $table = 'khach_hang';

    protected $fillable = [
        'ma_khach_hang',
        'ho_ten',
        'gioi_tinh',
        'ngay_sinh',
        'so_dien_thoai',
        'email',
        'so_giay_to',
        'loai_giay_to',
        'dia_chi',
        'quoc_tich',
        'hang_khach_hang',
        'trang_thai',
        'anh_dai_dien',
        'ghi_chu',
    ];

    protected function casts(): array
    {
        return [
            'ngay_sinh' => 'date',
        ];
    }

    public function datPhong(): HasMany
    {
        return $this->hasMany(DatPhong::class, 'khach_hang_id');
    }
}
