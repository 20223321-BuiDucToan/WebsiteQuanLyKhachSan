<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Phong extends Model
{
    use HasFactory;

    public const TRANG_THAI_TRONG = 'trong';
    public const TRANG_THAI_DA_DAT = 'da_dat';
    public const TRANG_THAI_DANG_SU_DUNG = 'dang_su_dung';
    public const TRANG_THAI_DON_DEP = 'don_dep';
    public const TRANG_THAI_BAO_TRI = 'bao_tri';

    private const TRANG_THAI_DAT_PHONG_GIU_CHO = [
        'cho_xac_nhan',
        'da_xac_nhan',
    ];

    private const TRANG_THAI_DAT_PHONG_DANG_O = [
        'da_nhan_phong',
    ];

    private const TINH_TRANG_VE_SINH_CAN_DON = [
        'can_don',
        'dang_don',
        'ban',
    ];

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
        'anh_phong',
    ];

    protected function casts(): array
    {
        return [
            'gia_mac_dinh' => 'decimal:2',
            'anh_phong' => 'array',
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

    public function layDanhSachAnhPhong(): array
    {
        return array_values(array_filter((array) $this->anh_phong, fn ($anh) => is_string($anh) && $anh !== ''));
    }

    public function layAnhPhongDauTien(): ?string
    {
        return $this->layDanhSachAnhPhong()[0] ?? null;
    }

    public function layAnhPhongDauTienUrl(): ?string
    {
        $duongDan = $this->layAnhPhongDauTien();

        return $duongDan ? asset($duongDan) : null;
    }

    public function scopeSanSangChoKhachDat(
        Builder $query,
        ?CarbonInterface $ngayNhan = null,
        ?CarbonInterface $ngayTra = null
    ): Builder {
        $homNay = now()->copy()->startOfDay();
        $ngayKetThucHienTai = $homNay->copy()->addDay();

        $query
            ->where('tinh_trang_hoat_dong', 'hoat_dong')
            ->where('tinh_trang_ve_sinh', 'sach')
            ->whereHas('loaiPhong', function (Builder $loaiPhongQuery) {
                $loaiPhongQuery->where('trang_thai', 'hoat_dong');
            });

        self::apDungBoLocXungDotDatPhong(
            $query,
            $homNay->toDateString(),
            $ngayKetThucHienTai->toDateString()
        );

        if ($ngayNhan && $ngayTra) {
            self::apDungBoLocXungDotDatPhong(
                $query,
                $ngayNhan->copy()->startOfDay()->toDateString(),
                $ngayTra->copy()->startOfDay()->toDateString()
            );
        }

        return $query;
    }

    public function coDatPhongDangO(): bool
    {
        return $this->chiTietDatPhong()
            ->whereHas('datPhong', function ($query) {
                $query->whereIn('trang_thai', self::TRANG_THAI_DAT_PHONG_DANG_O);
            })
            ->exists();
    }

    public function coDatPhongGiuCho(?Carbon $thoiDiem = null): bool
    {
        $ngayKiemTra = ($thoiDiem ?? now())->copy()->startOfDay()->toDateString();

        return $this->chiTietDatPhong()
            ->whereHas('datPhong', function ($query) use ($ngayKiemTra) {
                $query
                    ->whereIn('trang_thai', self::TRANG_THAI_DAT_PHONG_GIU_CHO)
                    ->whereDate('ngay_nhan_phong_du_kien', '<=', $ngayKiemTra)
                    ->whereDate('ngay_tra_phong_du_kien', '>', $ngayKiemTra);
            })
            ->exists();
    }

    public function coDatPhongHoatDong(?Carbon $thoiDiem = null): bool
    {
        return $this->coDatPhongDangO() || $this->coDatPhongGiuCho($thoiDiem);
    }

    public function tinhTrangThaiHeThong(?Carbon $thoiDiem = null): string
    {
        if ($this->tinh_trang_hoat_dong === 'tam_ngung') {
            return self::TRANG_THAI_BAO_TRI;
        }

        if ($this->coDatPhongDangO()) {
            return self::TRANG_THAI_DANG_SU_DUNG;
        }

        if (in_array($this->tinh_trang_ve_sinh, self::TINH_TRANG_VE_SINH_CAN_DON, true)) {
            return self::TRANG_THAI_DON_DEP;
        }

        if ($this->coDatPhongGiuCho($thoiDiem)) {
            return self::TRANG_THAI_DA_DAT;
        }

        return self::TRANG_THAI_TRONG;
    }

    public function dongBoTrangThaiHeThong(): void
    {
        $trangThaiMoi = $this->tinhTrangThaiHeThong();

        if ($this->trang_thai === $trangThaiMoi) {
            return;
        }

        $this->forceFill([
            'trang_thai' => $trangThaiMoi,
        ])->saveQuietly();
    }

    private static function apDungBoLocXungDotDatPhong(Builder $query, string $ngayNhan, string $ngayTra): void
    {
        $query->whereDoesntHave('chiTietDatPhong', function (Builder $chiTietQuery) use ($ngayNhan, $ngayTra) {
            $chiTietQuery->whereHas('datPhong', function (Builder $datPhongQuery) use ($ngayNhan, $ngayTra) {
                $datPhongQuery
                    ->whereIn('trang_thai', array_merge(self::TRANG_THAI_DAT_PHONG_GIU_CHO, self::TRANG_THAI_DAT_PHONG_DANG_O))
                    ->whereDate('ngay_nhan_phong_du_kien', '<', $ngayTra)
                    ->whereDate('ngay_tra_phong_du_kien', '>', $ngayNhan);
            });
        });
    }
}
