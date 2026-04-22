<?php

namespace Tests\Feature;

use App\Models\DatPhong;
use App\Models\KhachHang;
use App\Models\LoaiPhong;
use App\Models\NguoiDung;
use App\Models\Phong;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuanTriHeThongTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_active_admin_cannot_demote_own_account(): void
    {
        $admin = $this->taoNguoiDung('admin', 'hoat_dong');

        $response = $this->from(route('nguoi-dung.edit', $admin))
            ->actingAs($admin)
            ->put(route('nguoi-dung.update', $admin), [
                'ho_ten' => $admin->ho_ten,
                'ten_dang_nhap' => $admin->ten_dang_nhap,
                'email' => $admin->email,
                'so_dien_thoai' => $admin->so_dien_thoai,
                'dia_chi' => $admin->dia_chi,
                'vai_tro' => 'nhan_vien',
                'trang_thai' => 'hoat_dong',
            ]);

        $response->assertRedirect(route('nguoi-dung.index'));
        $this->assertDatabaseHas('nguoi_dung', [
            'id' => $admin->id,
            'vai_tro' => 'admin',
            'trang_thai' => 'hoat_dong',
        ]);
    }

    public function test_only_active_admin_cannot_deactivate_own_account(): void
    {
        $admin = $this->taoNguoiDung('admin', 'hoat_dong');

        $response = $this->from(route('nguoi-dung.edit', $admin))
            ->actingAs($admin)
            ->put(route('nguoi-dung.update', $admin), [
                'ho_ten' => $admin->ho_ten,
                'ten_dang_nhap' => $admin->ten_dang_nhap,
                'email' => $admin->email,
                'so_dien_thoai' => $admin->so_dien_thoai,
                'dia_chi' => $admin->dia_chi,
                'vai_tro' => 'admin',
                'trang_thai' => 'tam_khoa',
            ]);

        $response->assertRedirect(route('nguoi-dung.index'));
        $this->assertDatabaseHas('nguoi_dung', [
            'id' => $admin->id,
            'vai_tro' => 'admin',
            'trang_thai' => 'hoat_dong',
        ]);
    }

    public function test_invoice_cannot_be_created_for_booking_that_is_not_yet_confirmed(): void
    {
        $admin = $this->taoNguoiDung('admin', 'hoat_dong');
        $datPhong = $this->taoDatPhong($admin, DatPhong::TRANG_THAI_CHO_XAC_NHAN);

        $response = $this->from(route('hoa-don.create'))
            ->actingAs($admin)
            ->post(route('hoa-don.store'), [
                'dat_phong_id' => $datPhong->id,
                'giam_gia' => 0,
                'thue' => 0,
            ]);

        $response->assertRedirect(route('hoa-don.create'));
        $response->assertSessionHasErrors('dat_phong_id');
        $this->assertDatabaseCount('hoa_don', 0);
    }

    public function test_admin_updating_linked_customer_profile_syncs_login_account(): void
    {
        $admin = $this->taoNguoiDung('admin', 'hoat_dong');
        [$taiKhoanKhach, $khachHang] = $this->taoKhachHangCoTaiKhoan();

        $response = $this->from(route('khach-hang.edit', $khachHang))
            ->actingAs($admin)
            ->put(route('khach-hang.update', $khachHang), [
                'ho_ten' => 'Khach da dong bo',
                'gioi_tinh' => 'nu',
                'ngay_sinh' => now()->subYears(24)->toDateString(),
                'so_dien_thoai' => '0988555666',
                'email' => 'khach_dong_bo@example.com',
                'so_giay_to' => '079000111222',
                'loai_giay_to' => 'cccd',
                'dia_chi' => '45 Duong Dong Bo',
                'quoc_tich' => 'Viet Nam',
                'hang_khach_hang' => 'vang',
                'trang_thai' => 'hoat_dong',
                'ghi_chu' => 'Cap nhat tu admin',
            ]);

        $response->assertRedirect(route('khach-hang.show', $khachHang));
        $this->assertDatabaseHas('khach_hang', [
            'id' => $khachHang->id,
            'ho_ten' => 'Khach da dong bo',
            'email' => 'khach_dong_bo@example.com',
            'so_dien_thoai' => '0988555666',
            'dia_chi' => '45 Duong Dong Bo',
            'trang_thai' => 'hoat_dong',
        ]);
        $this->assertDatabaseHas('nguoi_dung', [
            'id' => $taiKhoanKhach->id,
            'ho_ten' => 'Khach da dong bo',
            'email' => 'khach_dong_bo@example.com',
            'so_dien_thoai' => '0988555666',
            'dia_chi' => '45 Duong Dong Bo',
            'trang_thai' => 'hoat_dong',
        ]);
    }

    public function test_toggling_customer_status_syncs_linked_login_account(): void
    {
        $admin = $this->taoNguoiDung('admin', 'hoat_dong');
        [$taiKhoanKhach, $khachHang] = $this->taoKhachHangCoTaiKhoan();

        $response = $this->from(route('khach-hang.index'))
            ->actingAs($admin)
            ->patch(route('khach-hang.doi-trang-thai', $khachHang));

        $response->assertRedirect(route('khach-hang.index'));
        $this->assertDatabaseHas('khach_hang', [
            'id' => $khachHang->id,
            'trang_thai' => 'tam_khoa',
        ]);
        $this->assertDatabaseHas('nguoi_dung', [
            'id' => $taiKhoanKhach->id,
            'trang_thai' => 'tam_khoa',
        ]);
    }

    private function taoNguoiDung(string $vaiTro, string $trangThai): NguoiDung
    {
        $soNgauNhien = random_int(1000, 9999);

        return NguoiDung::query()->create([
            'ho_ten' => ucfirst($vaiTro) . ' ' . $soNgauNhien,
            'ten_dang_nhap' => $vaiTro . '_' . $soNgauNhien,
            'email' => $vaiTro . '_' . $soNgauNhien . '@example.com',
            'password' => 'password',
            'so_dien_thoai' => '0909' . $soNgauNhien,
            'dia_chi' => 'Dia chi test',
            'vai_tro' => $vaiTro,
            'trang_thai' => $trangThai,
        ]);
    }

    private function taoDatPhong(NguoiDung $nguoiTao, string $trangThai): DatPhong
    {
        $soNgauNhien = random_int(1000, 9999);

        $khachHang = KhachHang::query()->create([
            'ma_khach_hang' => 'KHQTH' . $soNgauNhien,
            'ho_ten' => 'Khach QTH ' . $soNgauNhien,
            'so_dien_thoai' => '0911' . $soNgauNhien,
            'email' => 'khach_qth_' . $soNgauNhien . '@example.com',
            'hang_khach_hang' => 'thuong',
            'trang_thai' => 'hoat_dong',
        ]);

        $loaiPhong = LoaiPhong::query()->create([
            'ma_loai_phong' => 'LPQTH' . $soNgauNhien,
            'ten_loai_phong' => 'Phong QTH ' . $soNgauNhien,
            'gia_mot_dem' => 800000,
            'so_nguoi_toi_da' => 2,
            'so_giuong' => 1,
            'so_phong_tam' => 1,
            'trang_thai' => 'hoat_dong',
        ]);

        $phong = Phong::query()->create([
            'ma_phong' => 'PHQTH' . $soNgauNhien,
            'so_phong' => (string) $soNgauNhien,
            'loai_phong_id' => $loaiPhong->id,
            'tang' => 2,
            'trang_thai' => Phong::TRANG_THAI_TRONG,
            'tinh_trang_ve_sinh' => 'sach',
            'tinh_trang_hoat_dong' => 'hoat_dong',
            'gia_mac_dinh' => 800000,
        ]);

        $datPhong = DatPhong::query()->create([
            'ma_dat_phong' => 'DPQTH' . $soNgauNhien,
            'khach_hang_id' => $khachHang->id,
            'nguoi_tao_id' => $nguoiTao->id,
            'ngay_dat' => now(),
            'ngay_nhan_phong_du_kien' => now()->addDay()->toDateString(),
            'ngay_tra_phong_du_kien' => now()->addDays(2)->toDateString(),
            'so_nguoi_lon' => 1,
            'so_tre_em' => 0,
            'trang_thai' => $trangThai,
            'nguon_dat' => 'truc_tiep',
        ]);

        $datPhong->chiTietDatPhong()->create([
            'phong_id' => $phong->id,
            'gia_phong' => 800000,
            'so_dem' => 1,
            'so_nguoi_lon' => 1,
            'so_tre_em' => 0,
            'trang_thai' => 'da_dat',
        ]);

        return $datPhong;
    }

    private function taoKhachHangCoTaiKhoan(): array
    {
        $soNgauNhien = random_int(1000, 9999);
        $taiKhoanKhach = NguoiDung::query()->create([
            'ho_ten' => 'Khach lien ket ' . $soNgauNhien,
            'ten_dang_nhap' => 'khach_lien_ket_' . $soNgauNhien,
            'email' => 'khach_lien_ket_' . $soNgauNhien . '@example.com',
            'password' => 'password',
            'so_dien_thoai' => '0988' . $soNgauNhien,
            'dia_chi' => 'Dia chi cu',
            'vai_tro' => 'khach_hang',
            'trang_thai' => 'hoat_dong',
        ]);

        $khachHang = KhachHang::query()->create([
            'nguoi_dung_id' => $taiKhoanKhach->id,
            'ma_khach_hang' => 'KHLK' . $soNgauNhien,
            'ho_ten' => $taiKhoanKhach->ho_ten,
            'so_dien_thoai' => $taiKhoanKhach->so_dien_thoai,
            'email' => $taiKhoanKhach->email,
            'dia_chi' => $taiKhoanKhach->dia_chi,
            'hang_khach_hang' => 'thuong',
            'trang_thai' => 'hoat_dong',
        ]);

        return [$taiKhoanKhach, $khachHang];
    }
}
