<?php

namespace Tests\Feature;

use App\Models\LoaiPhong;
use App\Models\NguoiDung;
use App\Models\Phong;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoaiPhongVanHanhTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_room_type_management_page(): void
    {
        $admin = $this->taoAdmin();

        $response = $this->actingAs($admin)->get(route('loai-phong.index'));

        $response->assertOk();
        $response->assertSee('Quản lý loại phòng');
    }

    public function test_admin_can_create_room_type(): void
    {
        $admin = $this->taoAdmin();

        $response = $this->actingAs($admin)->post(route('loai-phong.store'), [
            'ten_loai_phong' => 'Deluxe hướng hồ bơi',
            'mo_ta' => 'Phong rong va sang.',
            'gia_mot_dem' => 1250000,
            'so_nguoi_toi_da' => 3,
            'dien_tich' => 38.5,
            'so_giuong' => 2,
            'loai_giuong' => 'Twin',
            'so_phong_tam' => 1,
            'co_ban_cong' => 1,
            'co_bep_rieng' => 0,
            'co_huong_bien' => 0,
            'trang_thai' => 'hoat_dong',
        ]);

        $response->assertRedirect(route('loai-phong.index'));
        $this->assertDatabaseHas('loai_phong', [
            'ten_loai_phong' => 'Deluxe hướng hồ bơi',
            'gia_mot_dem' => 1250000,
            'so_nguoi_toi_da' => 3,
            'so_giuong' => 2,
            'co_ban_cong' => true,
            'trang_thai' => 'hoat_dong',
        ]);
    }

    public function test_room_type_in_use_cannot_be_deleted(): void
    {
        $admin = $this->taoAdmin();
        $loaiPhong = LoaiPhong::query()->create([
            'ma_loai_phong' => 'LPTEST01',
            'ten_loai_phong' => 'Suite test',
            'gia_mot_dem' => 1800000,
            'so_nguoi_toi_da' => 4,
            'so_giuong' => 2,
            'so_phong_tam' => 2,
            'trang_thai' => 'hoat_dong',
        ]);

        Phong::query()->create([
            'ma_phong' => 'PHTEST01',
            'so_phong' => '801',
            'loai_phong_id' => $loaiPhong->id,
            'tang' => 8,
            'trang_thai' => 'trong',
            'tinh_trang_ve_sinh' => 'sach',
            'tinh_trang_hoat_dong' => 'hoat_dong',
            'gia_mac_dinh' => 1800000,
        ]);

        $response = $this->actingAs($admin)->delete(route('loai-phong.destroy', $loaiPhong));

        $response->assertRedirect(route('loai-phong.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('loai_phong', [
            'id' => $loaiPhong->id,
        ]);
    }

    private function taoAdmin(): NguoiDung
    {
        return NguoiDung::query()->create([
            'ho_ten' => 'Admin Test',
            'ten_dang_nhap' => 'admin_test_' . random_int(100, 999),
            'email' => 'admin' . random_int(100, 999) . '@example.com',
            'password' => 'password',
            'so_dien_thoai' => '0900000000',
            'vai_tro' => 'admin',
            'trang_thai' => 'hoat_dong',
        ]);
    }
}
