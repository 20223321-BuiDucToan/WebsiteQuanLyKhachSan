<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DatPhong extends Model
{
    use HasFactory;

    public const TRANG_THAI_CHO_XAC_NHAN = 'cho_xac_nhan';
    public const TRANG_THAI_DA_XAC_NHAN = 'da_xac_nhan';
    public const TRANG_THAI_DA_NHAN_PHONG = 'da_nhan_phong';
    public const TRANG_THAI_DA_TRA_PHONG = 'da_tra_phong';
    public const TRANG_THAI_DA_HUY = 'da_huy';

    public const DANH_SACH_TRANG_THAI = [
        self::TRANG_THAI_CHO_XAC_NHAN,
        self::TRANG_THAI_DA_XAC_NHAN,
        self::TRANG_THAI_DA_NHAN_PHONG,
        self::TRANG_THAI_DA_TRA_PHONG,
        self::TRANG_THAI_DA_HUY,
    ];

    private const CHUYEN_TRANG_THAI_HOP_LE = [
        self::TRANG_THAI_CHO_XAC_NHAN => [
            self::TRANG_THAI_DA_XAC_NHAN,
            self::TRANG_THAI_DA_HUY,
        ],
        self::TRANG_THAI_DA_XAC_NHAN => [
            self::TRANG_THAI_DA_NHAN_PHONG,
            self::TRANG_THAI_DA_HUY,
        ],
        self::TRANG_THAI_DA_NHAN_PHONG => [
            self::TRANG_THAI_DA_TRA_PHONG,
        ],
        self::TRANG_THAI_DA_TRA_PHONG => [],
        self::TRANG_THAI_DA_HUY => [],
    ];

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

    public function suDungDichVu(): HasMany
    {
        return $this->hasMany(SuDungDichVu::class, 'dat_phong_id');
    }

    public function tinhTongTienPhong(): float
    {
        $this->loadMissing('chiTietDatPhong');

        return (float) $this->chiTietDatPhong->sum(function (ChiTietDatPhong $chiTiet) {
            return (float) $chiTiet->gia_phong * (int) $chiTiet->so_dem;
        });
    }

    public function tinhTongTienDichVu(): float
    {
        $this->loadMissing('suDungDichVu');

        return (float) $this->suDungDichVu->sum('thanh_tien');
    }

    public static function layTrangThaiKeTiepHopLe(string $trangThaiHienTai): array
    {
        return self::CHUYEN_TRANG_THAI_HOP_LE[$trangThaiHienTai] ?? [];
    }

    public function layTrangThaiKeTiep(): array
    {
        return self::layTrangThaiKeTiepHopLe((string) $this->trang_thai);
    }
}
