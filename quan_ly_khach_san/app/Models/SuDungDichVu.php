<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuDungDichVu extends Model
{
    use HasFactory;

    protected $table = 'su_dung_dich_vu';

    protected $fillable = [
        'dat_phong_id',
        'dich_vu_id',
        'so_luong',
        'don_gia',
        'thanh_tien',
        'thoi_diem_su_dung',
        'nguoi_tao_id',
        'ghi_chu',
    ];

    protected function casts(): array
    {
        return [
            'don_gia' => 'decimal:2',
            'thanh_tien' => 'decimal:2',
            'thoi_diem_su_dung' => 'datetime',
        ];
    }

    public function datPhong(): BelongsTo
    {
        return $this->belongsTo(DatPhong::class, 'dat_phong_id');
    }

    public function dichVu(): BelongsTo
    {
        return $this->belongsTo(DichVu::class, 'dich_vu_id');
    }

    public function nguoiTao(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_tao_id');
    }
}
