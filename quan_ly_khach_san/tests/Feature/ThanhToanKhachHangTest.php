<?php

namespace Tests\Feature;

use App\Models\DatPhong;
use App\Models\HoaDon;
use App\Models\KhachHang;
use App\Models\LoaiPhong;
use App\Models\NguoiDung;
use App\Models\Phong;
use App\Models\ThanhToan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThanhToanKhachHangTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_submit_pending_payment_request_for_own_invoice(): void
    {
        [$taiKhoanKhach, $khachHang] = $this->taoKhachHangCoTaiKhoan();
        $hoaDon = $this->taoHoaDonChoKhachHang($taiKhoanKhach, $khachHang);

        $response = $this->from(route('booking.hoa-don.show', $hoaDon))
            ->actingAs($taiKhoanKhach)
            ->post(route('booking.thanh-toan.store', $hoaDon), [
                'so_tien' => 300000,
                'phuong_thuc_thanh_toan' => 'chuyen_khoan',
                'ma_tham_chieu' => 'BANK-001',
                'ghi_chu' => 'Da chuyen khoan dat coc',
            ]);

        $response->assertRedirect(route('booking.hoa-don.show', $hoaDon));

        $this->assertDatabaseHas('thanh_toan', [
            'hoa_don_id' => $hoaDon->id,
            'so_tien' => 300000,
            'phuong_thuc_thanh_toan' => 'chuyen_khoan',
            'ma_tham_chieu' => 'BANK-001',
            'trang_thai' => 'cho_xu_ly',
            'nguon_tao' => ThanhToan::NGUON_TAO_KHACH_HANG,
            'nguoi_tao_id' => $taiKhoanKhach->id,
        ]);

        $this->assertDatabaseHas('hoa_don', [
            'id' => $hoaDon->id,
            'trang_thai' => 'chua_thanh_toan',
        ]);
    }

    public function test_customer_cannot_submit_payment_request_beyond_remaining_after_pending_requests(): void
    {
        [$taiKhoanKhach, $khachHang] = $this->taoKhachHangCoTaiKhoan();
        $hoaDon = $this->taoHoaDonChoKhachHang($taiKhoanKhach, $khachHang);

        ThanhToan::query()->create([
            'ma_thanh_toan' => 'TTCHO001',
            'hoa_don_id' => $hoaDon->id,
            'so_tien' => 700000,
            'phuong_thuc_thanh_toan' => 'chuyen_khoan',
            'ma_tham_chieu' => 'OLD-PENDING',
            'thoi_diem_thanh_toan' => now(),
            'trang_thai' => 'cho_xu_ly',
            'nguon_tao' => ThanhToan::NGUON_TAO_KHACH_HANG,
            'nguoi_tao_id' => $taiKhoanKhach->id,
        ]);

        $response = $this->from(route('booking.hoa-don.show', $hoaDon))
            ->actingAs($taiKhoanKhach)
            ->post(route('booking.thanh-toan.store', $hoaDon), [
                'so_tien' => 400000,
                'phuong_thuc_thanh_toan' => 'chuyen_khoan',
            ]);

        $response->assertRedirect(route('booking.hoa-don.show', $hoaDon));
        $response->assertSessionHasErrors('so_tien');
        $this->assertDatabaseCount('thanh_toan', 1);
    }

    public function test_staff_can_approve_pending_customer_payment_and_sync_invoice(): void
    {
        $admin = $this->taoAdmin();
        [$taiKhoanKhach, $khachHang] = $this->taoKhachHangCoTaiKhoan();
        $hoaDon = $this->taoHoaDonChoKhachHang($admin, $khachHang);
        $thanhToan = ThanhToan::query()->create([
            'ma_thanh_toan' => 'TTCHO002',
            'hoa_don_id' => $hoaDon->id,
            'so_tien' => 400000,
            'phuong_thuc_thanh_toan' => 'chuyen_khoan',
            'ma_tham_chieu' => 'APPROVE-001',
            'thoi_diem_thanh_toan' => now(),
            'trang_thai' => 'cho_xu_ly',
            'nguon_tao' => ThanhToan::NGUON_TAO_KHACH_HANG,
            'nguoi_tao_id' => $taiKhoanKhach->id,
        ]);

        $response = $this->from(route('thanh-toan.index'))
            ->actingAs($admin)
            ->patch(route('thanh-toan.cap-nhat-trang-thai', $thanhToan), [
                'trang_thai' => 'thanh_cong',
            ]);

        $response->assertRedirect(route('thanh-toan.index'));
        $this->assertDatabaseHas('thanh_toan', [
            'id' => $thanhToan->id,
            'trang_thai' => 'thanh_cong',
            'nguoi_xu_ly_id' => $admin->id,
        ]);
        $this->assertDatabaseHas('hoa_don', [
            'id' => $hoaDon->id,
            'trang_thai' => 'thanh_toan_mot_phan',
        ]);
    }

    public function test_staff_can_reject_pending_customer_payment_without_changing_invoice_balance(): void
    {
        $admin = $this->taoAdmin();
        [$taiKhoanKhach, $khachHang] = $this->taoKhachHangCoTaiKhoan();
        $hoaDon = $this->taoHoaDonChoKhachHang($admin, $khachHang);
        $thanhToan = ThanhToan::query()->create([
            'ma_thanh_toan' => 'TTCHO003',
            'hoa_don_id' => $hoaDon->id,
            'so_tien' => 400000,
            'phuong_thuc_thanh_toan' => 'chuyen_khoan',
            'ma_tham_chieu' => 'REJECT-001',
            'thoi_diem_thanh_toan' => now(),
            'trang_thai' => 'cho_xu_ly',
            'nguon_tao' => ThanhToan::NGUON_TAO_KHACH_HANG,
            'nguoi_tao_id' => $taiKhoanKhach->id,
        ]);

        $response = $this->from(route('thanh-toan.index'))
            ->actingAs($admin)
            ->patch(route('thanh-toan.cap-nhat-trang-thai', $thanhToan), [
                'trang_thai' => 'that_bai',
            ]);

        $response->assertRedirect(route('thanh-toan.index'));
        $this->assertDatabaseHas('thanh_toan', [
            'id' => $thanhToan->id,
            'trang_thai' => 'that_bai',
            'nguoi_xu_ly_id' => $admin->id,
        ]);
        $this->assertDatabaseHas('hoa_don', [
            'id' => $hoaDon->id,
            'trang_thai' => 'chua_thanh_toan',
        ]);
    }

    public function test_staff_can_see_pending_customer_payment_requests_on_payment_management_page(): void
    {
        $admin = $this->taoAdmin();
        [$taiKhoanKhach, $khachHang] = $this->taoKhachHangCoTaiKhoan();
        $hoaDon = $this->taoHoaDonChoKhachHang($admin, $khachHang);

        $thanhToan = ThanhToan::query()->create([
            'ma_thanh_toan' => 'TTCHO004',
            'hoa_don_id' => $hoaDon->id,
            'so_tien' => 350000,
            'phuong_thuc_thanh_toan' => 'chuyen_khoan',
            'ma_tham_chieu' => 'VISIBLE-001',
            'thoi_diem_thanh_toan' => now(),
            'trang_thai' => 'cho_xu_ly',
            'nguon_tao' => ThanhToan::NGUON_TAO_KHACH_HANG,
            'nguoi_tao_id' => $taiKhoanKhach->id,
        ]);

        $response = $this->actingAs($admin)->get(route('thanh-toan.index'));

        $response->assertOk();
        $response->assertSee('Yêu cầu thanh toán khách hàng chờ xác nhận');
        $response->assertSee($thanhToan->ma_thanh_toan);
        $response->assertSee('Duyệt thanh toán');
    }

    public function test_admin_dashboard_highlights_pending_customer_payment_queue(): void
    {
        $admin = $this->taoAdmin();
        [$taiKhoanKhach, $khachHang] = $this->taoKhachHangCoTaiKhoan();
        $hoaDon = $this->taoHoaDonChoKhachHang($admin, $khachHang);

        ThanhToan::query()->create([
            'ma_thanh_toan' => 'TTCHO005',
            'hoa_don_id' => $hoaDon->id,
            'so_tien' => 250000,
            'phuong_thuc_thanh_toan' => 'chuyen_khoan',
            'ma_tham_chieu' => 'DASHBOARD-001',
            'thoi_diem_thanh_toan' => now(),
            'trang_thai' => 'cho_xu_ly',
            'nguon_tao' => ThanhToan::NGUON_TAO_KHACH_HANG,
            'nguoi_tao_id' => $taiKhoanKhach->id,
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Thanh toán khách đang chờ duyệt');
        $response->assertSee('TTCHO005');
    }

    public function test_customer_invoice_page_hides_payment_request_form_after_invoice_is_fully_paid(): void
    {
        [$taiKhoanKhach, $khachHang] = $this->taoKhachHangCoTaiKhoan();
        $hoaDon = $this->taoHoaDonChoKhachHang($taiKhoanKhach, $khachHang);

        ThanhToan::query()->create([
            'ma_thanh_toan' => 'TTDONE001',
            'hoa_don_id' => $hoaDon->id,
            'so_tien' => 1000000,
            'phuong_thuc_thanh_toan' => 'chuyen_khoan',
            'ma_tham_chieu' => 'DONE-001',
            'thoi_diem_thanh_toan' => now(),
            'trang_thai' => 'thanh_cong',
            'nguon_tao' => ThanhToan::NGUON_TAO_KHACH_HANG,
            'nguoi_tao_id' => $taiKhoanKhach->id,
        ]);

        $hoaDon->refresh()->update([
            'trang_thai' => 'da_thanh_toan',
        ]);

        $response = $this->actingAs($taiKhoanKhach)->get(route('booking.hoa-don.show', $hoaDon));

        $response->assertOk();
        $response->assertSee('Thanh toán đã hoàn tất');
        $response->assertDontSee('Gửi yêu cầu thanh toán');
        $response->assertDontSee('name="so_tien"', false);
    }

    public function test_payment_management_page_does_not_offer_fully_paid_invoice_for_new_payment(): void
    {
        $admin = $this->taoAdmin();
        [$taiKhoanKhach, $khachHang] = $this->taoKhachHangCoTaiKhoan();
        $hoaDon = $this->taoHoaDonChoKhachHang($admin, $khachHang);

        ThanhToan::query()->create([
            'ma_thanh_toan' => 'TTDONE002',
            'hoa_don_id' => $hoaDon->id,
            'so_tien' => 1000000,
            'phuong_thuc_thanh_toan' => 'tien_mat',
            'thoi_diem_thanh_toan' => now(),
            'trang_thai' => 'thanh_cong',
            'nguon_tao' => ThanhToan::NGUON_TAO_NOI_BO,
            'nguoi_tao_id' => $admin->id,
            'nguoi_xu_ly_id' => $admin->id,
            'thoi_diem_xu_ly' => now(),
        ]);

        $hoaDon->refresh()->update([
            'trang_thai' => 'da_thanh_toan',
        ]);

        $response = $this->actingAs($admin)->get(route('thanh-toan.index'));

        $response->assertOk();
        $response->assertDontSee('value="' . $hoaDon->id . '"', false);
    }

    private function taoAdmin(): NguoiDung
    {
        $soNgauNhien = random_int(1000, 9999);

        return NguoiDung::query()->create([
            'ho_ten' => 'Admin Thu Ngan',
            'ten_dang_nhap' => 'admin_thu_ngan_' . $soNgauNhien,
            'email' => 'admin_thu_ngan_' . $soNgauNhien . '@example.com',
            'password' => 'password',
            'so_dien_thoai' => '0901' . $soNgauNhien,
            'vai_tro' => 'admin',
            'trang_thai' => 'hoat_dong',
        ]);
    }

    private function taoKhachHangCoTaiKhoan(): array
    {
        $soNgauNhien = random_int(1000, 9999);
        $email = 'khach_thanh_toan_' . $soNgauNhien . '@example.com';
        $soDienThoai = '0912' . $soNgauNhien;

        $taiKhoan = NguoiDung::query()->create([
            'ho_ten' => 'Khach Thanh Toan ' . $soNgauNhien,
            'ten_dang_nhap' => 'khach_thanh_toan_' . $soNgauNhien,
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
            'ma_loai_phong' => 'LP' . $soNgauNhien,
            'ten_loai_phong' => 'Phong thanh toan ' . $soNgauNhien,
            'gia_mot_dem' => 1000000,
            'so_nguoi_toi_da' => 2,
            'so_giuong' => 1,
            'so_phong_tam' => 1,
            'trang_thai' => 'hoat_dong',
        ]);

        $phong = Phong::query()->create([
            'ma_phong' => 'PH' . $soNgauNhien,
            'so_phong' => (string) $soNgauNhien,
            'loai_phong_id' => $loaiPhong->id,
            'tang' => 2,
            'trang_thai' => Phong::TRANG_THAI_DA_DAT,
            'tinh_trang_ve_sinh' => 'sach',
            'tinh_trang_hoat_dong' => 'hoat_dong',
            'gia_mac_dinh' => 1000000,
        ]);

        $datPhong = DatPhong::query()->create([
            'ma_dat_phong' => 'DP' . $soNgauNhien,
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
            'gia_phong' => 1000000,
            'so_dem' => 1,
            'so_nguoi_lon' => 1,
            'so_tre_em' => 0,
            'trang_thai' => 'da_dat',
        ]);

        return HoaDon::query()->create([
            'ma_hoa_don' => 'HD' . $soNgauNhien,
            'dat_phong_id' => $datPhong->id,
            'tong_tien_phong' => 1000000,
            'tong_tien_dich_vu' => 0,
            'giam_gia' => 0,
            'thue' => 0,
            'tong_tien' => 1000000,
            'trang_thai' => 'chua_thanh_toan',
            'thoi_diem_xuat' => now(),
            'nguoi_tao_id' => $nguoiTao->id,
        ]);
    }
}
