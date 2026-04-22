<?php

namespace Tests\Feature;

use App\Models\DatPhong;
use App\Models\HoaDon;
use App\Models\KhachHang;
use App\Models\LoaiPhong;
use App\Models\NguoiDung;
use App\Models\Phong;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaiKhoanKhachHangPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_view_account_page_with_profile_and_payment_sections(): void
    {
        [$taiKhoan, $khachHang] = $this->taoKhachHangCoTaiKhoan();
        $hoaDon = $this->taoHoaDonChoKhachHang($taiKhoan, $khachHang);

        $response = $this->actingAs($taiKhoan)->get(route('booking.account'));

        $response->assertOk();
        $response->assertSee('Thông tin khách hàng');
        $response->assertSee('Thanh toán và hóa đơn của tôi');
        $response->assertSee($hoaDon->ma_hoa_don);
    }

    public function test_customer_can_update_their_own_profile_information(): void
    {
        [$taiKhoan, $khachHang] = $this->taoKhachHangCoTaiKhoan();

        $response = $this->from(route('booking.account'))
            ->actingAs($taiKhoan)
            ->patch(route('booking.account.update'), [
                'ho_ten' => 'Nguyen Khach Moi',
                'email' => 'nguyenkhachmoi@example.com',
                'so_dien_thoai' => '0988123456',
                'gioi_tinh' => 'nu',
                'ngay_sinh' => now()->subYears(25)->toDateString(),
                'quoc_tich' => 'Viet Nam',
                'loai_giay_to' => 'cccd',
                'so_giay_to' => '079123456789',
                'dia_chi' => '123 Duong Le Loi',
            ]);

        $response->assertRedirect(route('booking.account'));

        $this->assertDatabaseHas('nguoi_dung', [
            'id' => $taiKhoan->id,
            'ho_ten' => 'Nguyen Khach Moi',
            'email' => 'nguyenkhachmoi@example.com',
            'so_dien_thoai' => '0988123456',
            'dia_chi' => '123 Duong Le Loi',
        ]);

        $this->assertDatabaseHas('khach_hang', [
            'id' => $khachHang->id,
            'ho_ten' => 'Nguyen Khach Moi',
            'email' => 'nguyenkhachmoi@example.com',
            'so_dien_thoai' => '0988123456',
            'gioi_tinh' => 'nu',
            'quoc_tich' => 'Viet Nam',
            'loai_giay_to' => 'cccd',
            'so_giay_to' => '079123456789',
            'dia_chi' => '123 Duong Le Loi',
        ]);
    }

    public function test_customer_portal_still_uses_linked_customer_record_when_contact_info_differs(): void
    {
        [$taiKhoan, $khachHang] = $this->taoKhachHangCoTaiKhoan();
        $hoaDon = $this->taoHoaDonChoKhachHang($taiKhoan, $khachHang);

        $taiKhoan->update([
            'email' => 'tai_khoan_moi@example.com',
            'so_dien_thoai' => '0988000111',
        ]);

        $khachHang->update([
            'email' => 'ho_so_cu@example.com',
            'so_dien_thoai' => '0913000222',
        ]);

        $response = $this->actingAs($taiKhoan)->get(route('booking.hoa-don.show', $hoaDon));

        $response->assertOk();
        $response->assertSee($hoaDon->ma_hoa_don);
        $this->assertDatabaseHas('khach_hang', [
            'id' => $khachHang->id,
            'nguoi_dung_id' => $taiKhoan->id,
        ]);
    }

    private function taoKhachHangCoTaiKhoan(): array
    {
        $soNgauNhien = random_int(1000, 9999);
        $email = 'portal_khach_' . $soNgauNhien . '@example.com';
        $soDienThoai = '0913' . $soNgauNhien;

        $taiKhoan = NguoiDung::query()->create([
            'ho_ten' => 'Khach Portal ' . $soNgauNhien,
            'ten_dang_nhap' => 'khach_portal_' . $soNgauNhien,
            'email' => $email,
            'password' => 'password',
            'so_dien_thoai' => $soDienThoai,
            'vai_tro' => 'khach_hang',
            'trang_thai' => 'hoat_dong',
        ]);

        $khachHang = KhachHang::query()->create([
            'nguoi_dung_id' => $taiKhoan->id,
            'ma_khach_hang' => 'KH' . $soNgauNhien,
            'ho_ten' => $taiKhoan->ho_ten,
            'so_dien_thoai' => $soDienThoai,
            'email' => $email,
            'hang_khach_hang' => 'thuong',
            'trang_thai' => 'hoat_dong',
        ]);

        return [$taiKhoan, $khachHang];
    }

    private function taoHoaDonChoKhachHang(NguoiDung $nguoiTao, KhachHang $khachHang): HoaDon
    {
        $soNgauNhien = random_int(1000, 9999);

        $loaiPhong = LoaiPhong::query()->create([
            'ma_loai_phong' => 'LPPORTAL' . $soNgauNhien,
            'ten_loai_phong' => 'Phong portal ' . $soNgauNhien,
            'gia_mot_dem' => 900000,
            'so_nguoi_toi_da' => 2,
            'so_giuong' => 1,
            'so_phong_tam' => 1,
            'trang_thai' => 'hoat_dong',
        ]);

        $phong = Phong::query()->create([
            'ma_phong' => 'PHPORTAL' . $soNgauNhien,
            'so_phong' => (string) $soNgauNhien,
            'loai_phong_id' => $loaiPhong->id,
            'tang' => 5,
            'trang_thai' => Phong::TRANG_THAI_DA_DAT,
            'tinh_trang_ve_sinh' => 'sach',
            'tinh_trang_hoat_dong' => 'hoat_dong',
            'gia_mac_dinh' => 900000,
        ]);

        $datPhong = DatPhong::query()->create([
            'ma_dat_phong' => 'DPPORTAL' . $soNgauNhien,
            'khach_hang_id' => $khachHang->id,
            'nguoi_tao_id' => $nguoiTao->id,
            'ngay_dat' => now()->subDay(),
            'ngay_nhan_phong_du_kien' => now()->toDateString(),
            'ngay_tra_phong_du_kien' => now()->addDay()->toDateString(),
            'so_nguoi_lon' => 1,
            'so_tre_em' => 0,
            'trang_thai' => DatPhong::TRANG_THAI_DA_XAC_NHAN,
            'nguon_dat' => 'website',
        ]);

        $datPhong->chiTietDatPhong()->create([
            'phong_id' => $phong->id,
            'gia_phong' => 900000,
            'so_dem' => 1,
            'so_nguoi_lon' => 1,
            'so_tre_em' => 0,
            'trang_thai' => 'da_dat',
        ]);

        return HoaDon::query()->create([
            'ma_hoa_don' => 'HDPORTAL' . $soNgauNhien,
            'dat_phong_id' => $datPhong->id,
            'tong_tien_phong' => 900000,
            'tong_tien_dich_vu' => 0,
            'giam_gia' => 0,
            'thue' => 0,
            'tong_tien' => 900000,
            'trang_thai' => 'chua_thanh_toan',
            'thoi_diem_xuat' => now(),
            'nguoi_tao_id' => $nguoiTao->id,
        ]);
    }
}
