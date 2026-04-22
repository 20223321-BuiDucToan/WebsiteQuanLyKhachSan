<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KhachHang extends Model
{
    use HasFactory;

    public const HANG_KHACH_HANG = [
        'thuong' => 'Thường',
        'bac' => 'Bạc',
        'vang' => 'Vàng',
        'kim_cuong' => 'Kim cương',
    ];

    public const TRANG_THAI = [
        'hoat_dong' => 'Hoạt động',
        'tam_khoa' => 'Tạm khóa',
    ];

    public const GIOI_TINH = [
        'nam' => 'Nam',
        'nu' => 'Nữ',
        'khac' => 'Khác',
    ];

    public const LOAI_GIAY_TO = [
        'cccd' => 'CCCD',
        'cmnd' => 'CMND',
        'passport' => 'Passport',
        'khac' => 'Khác',
    ];

    protected $table = 'khach_hang';

    protected $fillable = [
        'nguoi_dung_id',
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

    public function nguoiDung(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_dung_id');
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if (!$this->anh_dai_dien) {
            return null;
        }

        return asset($this->anh_dai_dien);
    }

    public function getTenVietTatAttribute(): string
    {
        $tu = preg_split('/\s+/u', trim((string) $this->ho_ten)) ?: [];
        $chuDau = collect($tu)
            ->filter()
            ->take(2)
            ->map(function (string $giaTri) {
                return mb_strtoupper(mb_substr($giaTri, 0, 1));
            })
            ->implode('');

        return $chuDau !== '' ? $chuDau : 'KH';
    }

    public static function timTheoThongTinLienHe(?string $email, ?string $soDienThoai): ?self
    {
        $email = trim((string) $email);
        $soDienThoai = trim((string) $soDienThoai);

        if ($email === '' && $soDienThoai === '') {
            return null;
        }

        return static::query()
            ->where(function ($query) use ($email, $soDienThoai) {
                if ($email !== '') {
                    $query->where('email', $email);
                }

                if ($soDienThoai !== '') {
                    $phuongThuc = $email !== '' ? 'orWhere' : 'where';
                    $query->{$phuongThuc}('so_dien_thoai', $soDienThoai);
                }
            })
            ->first();
    }

    public static function timTheoTaiKhoan(?NguoiDung $nguoiDung): ?self
    {
        if (! $nguoiDung) {
            return null;
        }

        $khachHang = static::query()
            ->where('nguoi_dung_id', $nguoiDung->id)
            ->first();

        if ($khachHang) {
            return $khachHang;
        }

        $khachHang = static::timTheoThongTinLienHe($nguoiDung->email, $nguoiDung->so_dien_thoai);

        if (! $khachHang || ($khachHang->nguoi_dung_id !== null && $khachHang->nguoi_dung_id !== $nguoiDung->id)) {
            return null;
        }

        $khachHang->forceFill([
            'nguoi_dung_id' => $nguoiDung->id,
        ])->saveQuietly();

        return $khachHang->fresh();
    }

    public static function dongBoTuTaiKhoan(NguoiDung $nguoiDung): ?self
    {
        if ($nguoiDung->vai_tro !== 'khach_hang') {
            $nguoiDung->khachHang()?->update([
                'trang_thai' => $nguoiDung->trang_thai === 'tam_khoa' ? 'tam_khoa' : 'hoat_dong',
            ]);

            return null;
        }

        $khachHang = static::timTheoTaiKhoan($nguoiDung);

        if (! $khachHang) {
            $khachHang = static::query()->create([
                'nguoi_dung_id' => $nguoiDung->id,
                'ma_khach_hang' => static::taoMaMoi(),
                'ho_ten' => $nguoiDung->ho_ten,
                'so_dien_thoai' => $nguoiDung->so_dien_thoai,
                'email' => $nguoiDung->email,
                'dia_chi' => $nguoiDung->dia_chi,
                'hang_khach_hang' => 'thuong',
                'trang_thai' => $nguoiDung->trang_thai === 'tam_khoa' ? 'tam_khoa' : 'hoat_dong',
            ]);

            return $khachHang;
        }

        $khachHang->fill([
            'nguoi_dung_id' => $nguoiDung->id,
            'ho_ten' => $nguoiDung->ho_ten,
            'so_dien_thoai' => $nguoiDung->so_dien_thoai,
            'email' => $nguoiDung->email,
            'dia_chi' => $nguoiDung->dia_chi ?? $khachHang->dia_chi,
            'trang_thai' => $nguoiDung->trang_thai === 'tam_khoa' ? 'tam_khoa' : 'hoat_dong',
        ]);

        if ($khachHang->isDirty()) {
            $khachHang->save();
        }

        return $khachHang;
    }

    public static function taoMaMoi(): string
    {
        do {
            $maKhachHang = 'KH' . now()->format('ymdHis') . random_int(10, 99);
        } while (static::query()->where('ma_khach_hang', $maKhachHang)->exists());

        return $maKhachHang;
    }
}
