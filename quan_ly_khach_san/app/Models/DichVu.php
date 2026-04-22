<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DichVu extends Model
{
    use HasFactory;

    protected $table = 'dich_vu';

    protected $fillable = [
        'ma_dich_vu',
        'ten_dich_vu',
        'loai_dich_vu',
        'don_vi_tinh',
        'don_gia',
        'mo_ta',
        'trang_thai',
    ];

    protected function casts(): array
    {
        return [
            'don_gia' => 'decimal:2',
        ];
    }

    public function suDungDichVu(): HasMany
    {
        return $this->hasMany(SuDungDichVu::class, 'dich_vu_id');
    }

    public static function taoMaMoi(): string
    {
        do {
            $maDichVu = 'DV' . now()->format('ymdHis') . random_int(10, 99);
        } while (static::query()->where('ma_dich_vu', $maDichVu)->exists());

        return $maDichVu;
    }
}
