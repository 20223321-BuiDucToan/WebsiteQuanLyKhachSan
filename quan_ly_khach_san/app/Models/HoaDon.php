<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HoaDon extends Model
{
    use HasFactory;

    protected $table = 'hoa_don';

    protected $fillable = [
        'ma_hoa_don',
        'dat_phong_id',
        'tong_tien_phong',
        'tong_tien_dich_vu',
        'giam_gia',
        'thue',
        'tong_tien',
        'trang_thai',
        'thoi_diem_xuat',
        'nguoi_tao_id',
        'ghi_chu',
    ];

    protected function casts(): array
    {
        return [
            'tong_tien_phong' => 'decimal:2',
            'tong_tien_dich_vu' => 'decimal:2',
            'giam_gia' => 'decimal:2',
            'thue' => 'decimal:2',
            'tong_tien' => 'decimal:2',
            'thoi_diem_xuat' => 'datetime',
        ];
    }

    public function datPhong(): BelongsTo
    {
        return $this->belongsTo(DatPhong::class, 'dat_phong_id');
    }

    public function nguoiTao(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_tao_id');
    }

    public function thanhToan(): HasMany
    {
        return $this->hasMany(ThanhToan::class, 'hoa_don_id');
    }
}
