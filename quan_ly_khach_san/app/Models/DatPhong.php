<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DatPhong extends Model
{
    use HasFactory;

    protected $table = 'dat_phong';

    protected $fillable = [
        'ma_dat_phong',
        'khach_hang_id',
        'nguoi_tao_id',
        'ngay_dat',
        'ngay_nhan_phong_du_kien',
        'ngay_tra_phong_du_kien',
        'ngay_nhan_phong_thuc_te',
        'ngay_tra_phong_thuc_te',
        'so_nguoi_lon',
        'so_tre_em',
        'trang_thai',
        'nguon_dat',
        'yeu_cau_dac_biet',
        'ghi_chu',
    ];

    protected function casts(): array
    {
        return [
            'ngay_dat' => 'datetime',
            'ngay_nhan_phong_du_kien' => 'date',
            'ngay_tra_phong_du_kien' => 'date',
            'ngay_nhan_phong_thuc_te' => 'datetime',
            'ngay_tra_phong_thuc_te' => 'datetime',
        ];
    }

    public function khachHang(): BelongsTo
    {
        return $this->belongsTo(KhachHang::class, 'khach_hang_id');
    }

    public function nguoiTao(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_tao_id');
    }

    public function chiTietDatPhong(): HasMany
    {
        return $this->hasMany(ChiTietDatPhong::class, 'dat_phong_id');
    }

    public function hoaDon(): HasMany
    {
        return $this->hasMany(HoaDon::class, 'dat_phong_id');
    }
}
