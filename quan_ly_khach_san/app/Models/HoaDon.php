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

    public function tinhTongTienPhongTuDatPhong(): float
    {
        $this->loadMissing('datPhong.chiTietDatPhong');

        return $this->datPhong ? $this->datPhong->tinhTongTienPhong() : 0;
    }

    public function tinhTongTienDichVuTuDatPhong(): float
    {
        $this->loadMissing('datPhong.suDungDichVu');

        return $this->datPhong ? $this->datPhong->tinhTongTienDichVu() : 0;
    }

    public function tinhTongTienDaThu(): float
    {
        if ($this->relationLoaded('thanhToan')) {
            return (float) $this->thanhToan
                ->where('trang_thai', 'thanh_cong')
                ->sum('so_tien');
        }

        return (float) $this->thanhToan()
            ->where('trang_thai', 'thanh_cong')
            ->sum('so_tien');
    }

    public function tinhTongTienChoXuLy(): float
    {
        if ($this->relationLoaded('thanhToan')) {
            return (float) $this->thanhToan
                ->where('trang_thai', 'cho_xu_ly')
                ->sum('so_tien');
        }

        return (float) $this->thanhToan()
            ->where('trang_thai', 'cho_xu_ly')
            ->sum('so_tien');
    }

    public function coThanhToanThanhCong(): bool
    {
        if ($this->relationLoaded('thanhToan')) {
            return $this->thanhToan
                ->where('trang_thai', 'thanh_cong')
                ->isNotEmpty();
        }

        return $this->thanhToan()
            ->where('trang_thai', 'thanh_cong')
            ->exists();
    }

    public function dongBoGiaTriTuDatPhong(bool $luu = true): void
    {
        $tongTienPhong = $this->tinhTongTienPhongTuDatPhong();
        $tongTienDichVu = $this->tinhTongTienDichVuTuDatPhong();
        $tongTien = max(0, $tongTienPhong + $tongTienDichVu - (float) $this->giam_gia + (float) $this->thue);

        $duLieuCapNhat = [
            'tong_tien_phong' => $tongTienPhong,
            'tong_tien_dich_vu' => $tongTienDichVu,
            'tong_tien' => $tongTien,
        ];

        if ($this->trang_thai !== 'da_huy') {
            $soTienDaThu = $this->tinhTongTienDaThu();
            $duLieuCapNhat['trang_thai'] = 'chua_thanh_toan';

            if ($soTienDaThu >= $tongTien) {
                $duLieuCapNhat['trang_thai'] = 'da_thanh_toan';
            } elseif ($soTienDaThu > 0) {
                $duLieuCapNhat['trang_thai'] = 'thanh_toan_mot_phan';
            }
        }

        $this->forceFill($duLieuCapNhat);

        if ($luu && $this->isDirty(array_keys($duLieuCapNhat))) {
            $this->saveQuietly();
        }
    }

    public function coTheGiaiPhongSauThanhToan(): bool
    {
        $this->loadMissing('datPhong.chiTietDatPhong.phong');

        return $this->trang_thai === 'da_thanh_toan'
            && $this->datPhong
            && $this->datPhong->trang_thai === DatPhong::TRANG_THAI_DA_TRA_PHONG;
    }

    public function giaiPhongSauKhiHoanTatThanhToan(): void
    {
        if (! $this->coTheGiaiPhongSauThanhToan()) {
            return;
        }

        foreach ($this->datPhong->chiTietDatPhong as $chiTiet) {
            $phong = $chiTiet->phong;

            if (! $phong) {
                continue;
            }

            $phong->forceFill([
                'tinh_trang_ve_sinh' => 'sach',
            ])->saveQuietly();

            $phong->refresh()->dongBoTrangThaiHeThong();
        }
    }
}
