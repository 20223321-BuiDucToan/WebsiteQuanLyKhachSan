<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThanhToan extends Model
{
    use HasFactory;

    protected $table = 'thanh_toan';

    protected $fillable = [
        'ma_thanh_toan',
        'hoa_don_id',
        'so_tien',
        'phuong_thuc_thanh_toan',
        'thoi_diem_thanh_toan',
        'trang_thai',
        'nguoi_tao_id',
        'ghi_chu',
    ];

    protected function casts(): array
    {
        return [
            'so_tien' => 'decimal:2',
            'thoi_diem_thanh_toan' => 'datetime',
        ];
    }

    public function hoaDon(): BelongsTo
    {
        return $this->belongsTo(HoaDon::class, 'hoa_don_id');
    }

    public function nguoiTao(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_tao_id');
    }
}
