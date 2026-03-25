<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NguoiDung extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'nguoi_dung';

    protected $fillable = [
        'ho_ten',
        'ten_dang_nhap',
        'email',
        'password',
        'so_dien_thoai',
        'dia_chi',
        'anh_dai_dien',
        'vai_tro',
        'trang_thai',
        'lan_dang_nhap_cuoi',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'lan_dang_nhap_cuoi' => 'datetime',
        ];
    }
}