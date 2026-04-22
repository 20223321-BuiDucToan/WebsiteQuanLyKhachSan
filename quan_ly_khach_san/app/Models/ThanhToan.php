<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThanhToan extends Model
{
    use HasFactory;     

    public const NGUON_TAO_NOI_BO = 'noi_bo';
    public const NGUON_TAO_KHACH_HANG = 'khach_hang';

    protected $table = 'thanh_toan';

    protected $fillable = [
        'ma_thanh_toan',
        'hoa_don_id',
        'so_tien',
        'phuong_thuc_thanh_toan',
        'ma_tham_chieu',
        'thoi_diem_thanh_toan',
        'trang_thai',
        'nguon_tao',
        'nguoi_tao_id',
        'nguoi_xu_ly_id',
        'thoi_diem_xu_ly',
        'ghi_chu',
    ];

    protected function casts(): array
    {
        return [
            'so_tien' => 'decimal:2',
            'thoi_diem_thanh_toan' => 'datetime',
            'thoi_diem_xu_ly' => 'datetime',
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

    public function nguoiXuLy(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_xu_ly_id');
    }

    public function laYeuCauTuKhachHang(): bool
    {
        return $this->nguon_tao === self::NGUON_TAO_KHACH_HANG;
    }
}
