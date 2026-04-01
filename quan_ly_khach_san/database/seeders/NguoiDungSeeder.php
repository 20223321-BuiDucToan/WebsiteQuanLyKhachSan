<?php

namespace Database\Seeders;

use App\Models\NguoiDung;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class NguoiDungSeeder extends Seeder
{
    public function run(): void
    {
        NguoiDung::query()->updateOrCreate(
            ['ten_dang_nhap' => 'admin'],
            [
                'ho_ten' => 'Quan tri he thong',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('123456'),
                'so_dien_thoai' => '0900000000',
                'vai_tro' => 'admin',
                'trang_thai' => 'hoat_dong',
            ]
        );
    }
}
