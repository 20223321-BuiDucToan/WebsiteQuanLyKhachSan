<?php

namespace Tests\Feature;

use App\Models\DatPhong;
use App\Models\DichVu;
use App\Models\HoaDon;
use App\Models\KhachHang;
use App\Models\LoaiPhong;
use App\Models\NguoiDung;
use App\Models\Phong;
use App\Models\ThanhToan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DichVuVanHanhTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_service_management_page(): void
    {
        $admin = $this->taoAdmin();

        $response = $this->actingAs($admin)->get(route('dich-vu.index'));

        $response->assertOk();
        $response->assertSee('Quan ly dich vu');
    }

    public function test_recording_service_usage_syncs_existing_invoice_total(): void
    {
        $admin = $this->taoAdmin();
        $datPhong = $this->taoDatPhongDangO($admin);
        $hoaDon = $this->taoHoaDonChoDatPhong($datPhong, $admin, 1000000);
        $dichVu = $this->taoDichVu('Minibar', 50000);

        $response = $this->from(route('dat-phong.show', $datPhong))
            ->actingAs($admin)
            ->post(route('dat-phong.dich-vu.store', $datPhong), [
                'dich_vu_id' => $dichVu->id,
                'so_luong' => 2,
                'thoi_diem_su_dung' => now()->format('Y-m-d H:i:s'),
                'ghi_chu' => 'Nuoc va snack',
            ]);

        $response->assertRedirect(route('dat-phong.show', $datPhong));
        $this->assertDatabaseHas('su_dung_dich_vu', [
            'dat_phong_id' => $datPhong->id,
            'dich_vu_id' => $dichVu->id,
            'so_luong' => 2,
        ]);

        $hoaDon->refresh();

        $this->assertSame(100000.0, (float) $hoaDon->tong_tien_dich_vu);
        $this->assertSame(1100000.0, (float) $hoaDon->tong_tien);

        $hoaDonResponse = $this->actingAs($admin)->get(route('hoa-don.show', $hoaDon));

        $hoaDonResponse->assertOk();
        $hoaDonResponse->assertSee('Minibar');
        $hoaDonResponse->assertSee($hoaDon->ma_hoa_don);
    }

    public function test_paid_invoice_blocks_new_service_usage(): void
    {
        $admin = $this->taoAdmin();
        $datPhong = $this->taoDatPhongDangO($admin);
        $hoaDon = $this->taoHoaDonChoDatPhong($datPhong, $admin, 1000000, 'da_thanh_toan');
        $dichVu = $this->taoDichVu('Giat ui', 120000);

        ThanhToan::query()->create([
            'ma_thanh_toan' => 'TTTEST001',
            'hoa_don_id' => $hoaDon->id,
            'so_tien' => 1000000,
            'phuong_thuc_thanh_toan' => 'tien_mat',
            'thoi_diem_thanh_toan' => now(),
            'trang_thai' => 'thanh_cong',
            'nguoi_tao_id' => $admin->id,
        ]);

        $response = $this->from(route('dat-phong.show', $datPhong))
            ->actingAs($admin)
            ->post(route('dat-phong.dich-vu.store', $datPhong), [
                'dich_vu_id' => $dichVu->id,
                'so_luong' => 1,
                'thoi_diem_su_dung' => now()->format('Y-m-d H:i:s'),
            ]);

        $response->assertRedirect(route('dat-phong.show', $datPhong));
        $response->assertSessionHasErrors('dich_vu_id');
        $this->assertDatabaseCount('su_dung_dich_vu', 0);
    }

    private function taoAdmin(): NguoiDung
    {
        return NguoiDung::query()->create([
            'ho_ten' => 'Admin Test',
            'ten_dang_nhap' => 'admin_test',
            'email' => 'admin@example.com',
            'password' => 'password',
            'so_dien_thoai' => '0900000000',
            'vai_tro' => 'admin',
            'trang_thai' => 'hoat_dong',
        ]);
    }

    private function taoDatPhongDangO(NguoiDung $admin): DatPhong
    {
        $loaiPhong = LoaiPhong::query()->create([
            'ma_loai_phong' => 'LPTEST',
            'ten_loai_phong' => 'Phong test',
            'gia_mot_dem' => 500000,
            'so_nguoi_toi_da' => 2,
            'so_giuong' => 1,
            'so_phong_tam' => 1,
            'trang_thai' => 'hoat_dong',
        ]);

        $phong = Phong::query()->create([
            'ma_phong' => 'PHTEST01',
            'so_phong' => '101',
            'loai_phong_id' => $loaiPhong->id,
            'tang' => 1,
            'trang_thai' => 'dang_su_dung',
            'tinh_trang_ve_sinh' => 'sach',
            'tinh_trang_hoat_dong' => 'hoat_dong',
            'gia_mac_dinh' => 500000,
        ]);

        $khachHang = KhachHang::query()->create([
            'ma_khach_hang' => 'KHTEST01',
            'ho_ten' => 'Khach test',
            'so_dien_thoai' => '0911111111',
            'email' => 'khach@example.com',
            'hang_khach_hang' => 'thuong',
            'trang_thai' => 'hoat_dong',
        ]);

        $datPhong = DatPhong::query()->create([
            'ma_dat_phong' => 'DPTEST01',
            'khach_hang_id' => $khachHang->id,
            'nguoi_tao_id' => $admin->id,
            'ngay_dat' => now()->subDay(),
            'ngay_nhan_phong_du_kien' => now()->toDateString(),
            'ngay_tra_phong_du_kien' => now()->addDay()->toDateString(),
            'ngay_nhan_phong_thuc_te' => now()->subHours(2),
            'so_nguoi_lon' => 1,
            'so_tre_em' => 0,
            'trang_thai' => 'da_nhan_phong',
            'nguon_dat' => 'truc_tiep',
        ]);

        $datPhong->chiTietDatPhong()->create([
            'phong_id' => $phong->id,
            'gia_phong' => 500000,
            'so_dem' => 2,
            'so_nguoi_lon' => 1,
            'so_tre_em' => 0,
            'ngay_nhan_phong_thuc_te' => now()->subHours(2),
            'trang_thai' => 'dang_o',
        ]);

        return $datPhong;
    }

    private function taoHoaDonChoDatPhong(DatPhong $datPhong, NguoiDung $admin, float $tongTien, string $trangThai = 'chua_thanh_toan'): HoaDon
    {
        return HoaDon::query()->create([
            'ma_hoa_don' => 'HDTEST01' . random_int(10, 99),
            'dat_phong_id' => $datPhong->id,
            'tong_tien_phong' => $tongTien,
            'tong_tien_dich_vu' => 0,
            'giam_gia' => 0,
            'thue' => 0,
            'tong_tien' => $tongTien,
            'trang_thai' => $trangThai,
            'thoi_diem_xuat' => now(),
            'nguoi_tao_id' => $admin->id,
        ]);
    }

    private function taoDichVu(string $tenDichVu, float $donGia): DichVu
    {
        return DichVu::query()->create([
            'ma_dich_vu' => 'DVTEST' . random_int(10, 99),
            'ten_dich_vu' => $tenDichVu,
            'loai_dich_vu' => 'Bo sung',
            'don_vi_tinh' => 'lan',
            'don_gia' => $donGia,
            'trang_thai' => 'hoat_dong',
        ]);
    }
}
