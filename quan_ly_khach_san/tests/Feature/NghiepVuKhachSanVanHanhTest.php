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

class NghiepVuKhachSanVanHanhTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_cannot_create_checked_in_booking_for_future_arrival_date(): void
    {
        $admin = $this->taoAdmin();
        [, $phong] = $this->taoPhong();

        $response = $this->from(route('dat-phong.create'))
            ->actingAs($admin)
            ->post(route('dat-phong.store'), [
                'phong_id' => $phong->id,
                'ho_ten' => 'Khach dat nhanh',
                'so_dien_thoai' => '0912345678',
                'email' => 'khachnhanh@example.com',
                'ngay_nhan' => now()->addDay()->toDateString(),
                'ngay_tra' => now()->addDays(2)->toDateString(),
                'so_nguoi_lon' => 1,
                'so_tre_em' => 0,
                'trang_thai' => DatPhong::TRANG_THAI_DA_NHAN_PHONG,
                'nguon_dat' => 'truc_tiep',
            ]);

        $response->assertRedirect(route('dat-phong.create'));
        $response->assertSessionHasErrors('trang_thai');
        $this->assertDatabaseCount('dat_phong', 0);
    }

    public function test_future_booking_cannot_be_checked_in_from_status_update(): void
    {
        $admin = $this->taoAdmin();
        $datPhong = $this->taoDatPhong($admin, [
            'ngay_nhan_phong_du_kien' => now()->addDay()->toDateString(),
            'ngay_tra_phong_du_kien' => now()->addDays(2)->toDateString(),
            'trang_thai' => DatPhong::TRANG_THAI_DA_XAC_NHAN,
        ]);

        $response = $this->from(route('dat-phong.show', $datPhong))
            ->actingAs($admin)
            ->patch(route('dat-phong.cap-nhat-trang-thai', $datPhong), [
                'trang_thai' => DatPhong::TRANG_THAI_DA_NHAN_PHONG,
            ]);

        $response->assertRedirect(route('dat-phong.show', $datPhong));
        $response->assertSessionHasErrors('trang_thai');
        $this->assertDatabaseHas('dat_phong', [
            'id' => $datPhong->id,
            'trang_thai' => DatPhong::TRANG_THAI_DA_XAC_NHAN,
        ]);
    }

    public function test_cancelling_booking_auto_cancels_unpaid_invoice(): void
    {
        $admin = $this->taoAdmin();
        $datPhong = $this->taoDatPhong($admin, [
            'trang_thai' => DatPhong::TRANG_THAI_DA_XAC_NHAN,
        ]);
        $hoaDon = $this->taoHoaDon($datPhong, $admin);

        $response = $this->from(route('dat-phong.show', $datPhong))
            ->actingAs($admin)
            ->patch(route('dat-phong.cap-nhat-trang-thai', $datPhong), [
                'trang_thai' => DatPhong::TRANG_THAI_DA_HUY,
            ]);

        $response->assertRedirect(route('dat-phong.show', $datPhong));
        $this->assertDatabaseHas('dat_phong', [
            'id' => $datPhong->id,
            'trang_thai' => DatPhong::TRANG_THAI_DA_HUY,
        ]);
        $this->assertDatabaseHas('hoa_don', [
            'id' => $hoaDon->id,
            'trang_thai' => 'da_huy',
        ]);
    }

    public function test_cancelling_booking_is_blocked_when_successful_payment_exists(): void
    {
        $admin = $this->taoAdmin();
        $datPhong = $this->taoDatPhong($admin, [
            'trang_thai' => DatPhong::TRANG_THAI_DA_XAC_NHAN,
        ]);
        $hoaDon = $this->taoHoaDon($datPhong, $admin);
        $this->taoThanhToan($hoaDon, $admin);

        $response = $this->from(route('dat-phong.show', $datPhong))
            ->actingAs($admin)
            ->patch(route('dat-phong.cap-nhat-trang-thai', $datPhong), [
                'trang_thai' => DatPhong::TRANG_THAI_DA_HUY,
            ]);

        $response->assertRedirect(route('dat-phong.show', $datPhong));
        $response->assertSessionHasErrors('trang_thai');
        $this->assertDatabaseHas('dat_phong', [
            'id' => $datPhong->id,
            'trang_thai' => DatPhong::TRANG_THAI_DA_XAC_NHAN,
        ]);
        $this->assertDatabaseHas('hoa_don', [
            'id' => $hoaDon->id,
            'trang_thai' => 'chua_thanh_toan',
        ]);
    }

    public function test_invoice_cannot_be_cancelled_after_successful_payment(): void
    {
        $admin = $this->taoAdmin();
        $datPhong = $this->taoDatPhong($admin, [
            'trang_thai' => DatPhong::TRANG_THAI_DA_TRA_PHONG,
            'ngay_nhan_phong_thuc_te' => now()->subDay(),
            'ngay_tra_phong_thuc_te' => now(),
        ]);
        $hoaDon = $this->taoHoaDon($datPhong, $admin);
        $this->taoThanhToan($hoaDon, $admin);

        $response = $this->from(route('hoa-don.show', $hoaDon))
            ->actingAs($admin)
            ->patch(route('hoa-don.cap-nhat-trang-thai', $hoaDon), [
                'trang_thai' => 'da_huy',
            ]);

        $response->assertRedirect(route('hoa-don.show', $hoaDon));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('hoa_don', [
            'id' => $hoaDon->id,
            'trang_thai' => 'chua_thanh_toan',
        ]);
    }

    public function test_future_booking_does_not_mark_room_as_reserved_today(): void
    {
        $admin = $this->taoAdmin();
        [, $phong] = $this->taoPhong();

        $this->taoDatPhong($admin, [
            'phong_id' => $phong->id,
            'ngay_nhan_phong_du_kien' => now()->addDays(3)->toDateString(),
            'ngay_tra_phong_du_kien' => now()->addDays(5)->toDateString(),
            'trang_thai' => DatPhong::TRANG_THAI_DA_XAC_NHAN,
        ], $phong);

        $phong->refresh()->dongBoTrangThaiHeThong();

        $this->assertSame(Phong::TRANG_THAI_TRONG, $phong->fresh()->trang_thai);
    }

    public function test_customer_only_sees_rooms_that_are_currently_empty(): void
    {
        $admin = $this->taoAdmin();
        [, $phongDangCoKhach] = $this->taoPhong();
        [, $phongTrong] = $this->taoPhong();

        $this->taoDatPhong($admin, [
            'trang_thai' => DatPhong::TRANG_THAI_DA_NHAN_PHONG,
            'ngay_nhan_phong_du_kien' => now()->toDateString(),
            'ngay_tra_phong_du_kien' => now()->addDay()->toDateString(),
            'ngay_nhan_phong_thuc_te' => now()->subHour(),
        ], $phongDangCoKhach);

        $phongDangCoKhach->refresh()->dongBoTrangThaiHeThong();
        $phongTrong->refresh()->dongBoTrangThaiHeThong();

        $response = $this->get(route('booking.index', [
            'ngay_nhan' => now()->toDateString(),
            'ngay_tra' => now()->addDay()->toDateString(),
        ]));

        $response->assertOk();
        $response->assertSee((string) $phongTrong->so_phong);
        $response->assertDontSee((string) $phongDangCoKhach->so_phong);
    }

    public function test_customer_cannot_book_room_that_is_not_currently_empty(): void
    {
        $admin = $this->taoAdmin();
        [, $phongDangCoKhach] = $this->taoPhong();
        $taiKhoanKhach = $this->taoTaiKhoanKhachHang();

        $this->taoDatPhong($admin, [
            'trang_thai' => DatPhong::TRANG_THAI_DA_NHAN_PHONG,
            'ngay_nhan_phong_du_kien' => now()->toDateString(),
            'ngay_tra_phong_du_kien' => now()->addDay()->toDateString(),
            'ngay_nhan_phong_thuc_te' => now()->subHour(),
        ], $phongDangCoKhach);

        $response = $this->from(route('booking.index'))
            ->actingAs($taiKhoanKhach)
            ->post(route('booking.store'), [
                'phong_id' => $phongDangCoKhach->id,
                'ho_ten' => $taiKhoanKhach->ho_ten,
                'so_dien_thoai' => $taiKhoanKhach->so_dien_thoai,
                'email' => $taiKhoanKhach->email,
                'ngay_nhan' => now()->addDay()->toDateString(),
                'ngay_tra' => now()->addDays(2)->toDateString(),
                'so_nguoi_lon' => 1,
                'so_tre_em' => 0,
            ]);

        $response->assertRedirect(route('booking.index'));
        $response->assertSessionHasErrors('phong_id');
        $this->assertDatabaseCount('dat_phong', 1);
    }

    public function test_room_returns_to_empty_after_checkout_invoice_is_fully_paid(): void
    {
        $admin = $this->taoAdmin();
        [, $phong] = $this->taoPhong();
        $datPhong = $this->taoDatPhong($admin, [
            'trang_thai' => DatPhong::TRANG_THAI_DA_TRA_PHONG,
            'ngay_nhan_phong_du_kien' => now()->subDay()->toDateString(),
            'ngay_tra_phong_du_kien' => now()->toDateString(),
            'ngay_nhan_phong_thuc_te' => now()->subDay(),
            'ngay_tra_phong_thuc_te' => now()->subHour(),
        ], $phong);

        $phong->forceFill([
            'tinh_trang_ve_sinh' => 'can_don',
        ])->saveQuietly();
        $phong->refresh()->dongBoTrangThaiHeThong();

        $this->assertSame(Phong::TRANG_THAI_DON_DEP, $phong->fresh()->trang_thai);

        $hoaDon = $this->taoHoaDon($datPhong, $admin);

        $response = $this->actingAs($admin)->post(route('thanh-toan.store'), [
            'hoa_don_id' => $hoaDon->id,
            'so_tien' => 850000,
            'phuong_thuc_thanh_toan' => 'tien_mat',
            'trang_thai' => 'thanh_cong',
            'thoi_diem_thanh_toan' => now()->toDateTimeString(),
        ]);

        $response->assertRedirect(route('thanh-toan.index'));
        $this->assertDatabaseHas('hoa_don', [
            'id' => $hoaDon->id,
            'trang_thai' => 'da_thanh_toan',
        ]);
        $this->assertSame(Phong::TRANG_THAI_TRONG, $phong->fresh()->trang_thai);
        $this->assertSame('sach', $phong->fresh()->tinh_trang_ve_sinh);
    }

    private function taoAdmin(): NguoiDung
    {
        $soNgauNhien = random_int(1000, 9999);

        return NguoiDung::query()->create([
            'ho_ten' => 'Admin Van Hanh',
            'ten_dang_nhap' => 'admin_van_hanh_' . $soNgauNhien,
            'email' => 'admin_van_hanh_' . $soNgauNhien . '@example.com',
            'password' => 'password',
            'so_dien_thoai' => '0900' . $soNgauNhien,
            'vai_tro' => 'admin',
            'trang_thai' => 'hoat_dong',
        ]);
    }

    private function taoPhong(): array
    {
        $soNgauNhien = random_int(1000, 9999);

        $loaiPhong = LoaiPhong::query()->create([
            'ma_loai_phong' => 'LP' . $soNgauNhien,
            'ten_loai_phong' => 'Phong test ' . $soNgauNhien,
            'gia_mot_dem' => 850000,
            'so_nguoi_toi_da' => 2,
            'so_giuong' => 1,
            'so_phong_tam' => 1,
            'trang_thai' => 'hoat_dong',
        ]);

        $phong = Phong::query()->create([
            'ma_phong' => 'PH' . $soNgauNhien,
            'so_phong' => (string) $soNgauNhien,
            'loai_phong_id' => $loaiPhong->id,
            'tang' => 3,
            'trang_thai' => Phong::TRANG_THAI_TRONG,
            'tinh_trang_ve_sinh' => 'sach',
            'tinh_trang_hoat_dong' => 'hoat_dong',
            'gia_mac_dinh' => 850000,
        ]);

        return [$loaiPhong, $phong];
    }

    private function taoKhachHang(): KhachHang
    {
        $soNgauNhien = random_int(1000, 9999);

        return KhachHang::query()->create([
            'ma_khach_hang' => 'KH' . $soNgauNhien,
            'ho_ten' => 'Khach test ' . $soNgauNhien,
            'so_dien_thoai' => '0912' . $soNgauNhien,
            'email' => 'khach_' . $soNgauNhien . '@example.com',
            'hang_khach_hang' => 'thuong',
            'trang_thai' => 'hoat_dong',
        ]);
    }

    private function taoTaiKhoanKhachHang(): NguoiDung
    {
        $soNgauNhien = random_int(1000, 9999);

        return NguoiDung::query()->create([
            'ho_ten' => 'Khach dat online ' . $soNgauNhien,
            'ten_dang_nhap' => 'khach_online_' . $soNgauNhien,
            'email' => 'khach_online_' . $soNgauNhien . '@example.com',
            'password' => 'password',
            'so_dien_thoai' => '0933' . $soNgauNhien,
            'vai_tro' => 'khach_hang',
            'trang_thai' => 'hoat_dong',
        ]);
    }

    private function taoDatPhong(NguoiDung $admin, array $thuocTinh = [], ?Phong $phong = null): DatPhong
    {
        if (! $phong) {
            [, $phong] = $this->taoPhong();
        }

        $khachHang = $this->taoKhachHang();
        $soNgauNhien = random_int(1000, 9999);

        $duLieuMacDinh = [
            'ma_dat_phong' => 'DP' . $soNgauNhien,
            'khach_hang_id' => $khachHang->id,
            'nguoi_tao_id' => $admin->id,
            'ngay_dat' => now()->subDay(),
            'ngay_nhan_phong_du_kien' => now()->toDateString(),
            'ngay_tra_phong_du_kien' => now()->addDay()->toDateString(),
            'ngay_nhan_phong_thuc_te' => null,
            'ngay_tra_phong_thuc_te' => null,
            'so_nguoi_lon' => 1,
            'so_tre_em' => 0,
            'trang_thai' => DatPhong::TRANG_THAI_DA_XAC_NHAN,
            'nguon_dat' => 'truc_tiep',
        ];

        if (array_key_exists('phong_id', $thuocTinh)) {
            unset($thuocTinh['phong_id']);
        }

        $datPhong = DatPhong::query()->create(array_merge($duLieuMacDinh, $thuocTinh));

        $datPhong->chiTietDatPhong()->create([
            'phong_id' => $phong->id,
            'gia_phong' => 850000,
            'so_dem' => 1,
            'so_nguoi_lon' => 1,
            'so_tre_em' => 0,
            'ngay_nhan_phong_thuc_te' => $datPhong->ngay_nhan_phong_thuc_te,
            'ngay_tra_phong_thuc_te' => $datPhong->ngay_tra_phong_thuc_te,
            'trang_thai' => match ($datPhong->trang_thai) {
                DatPhong::TRANG_THAI_DA_NHAN_PHONG => 'dang_o',
                DatPhong::TRANG_THAI_DA_TRA_PHONG => 'da_tra_phong',
                DatPhong::TRANG_THAI_DA_HUY => 'da_huy',
                default => 'da_dat',
            },
        ]);

        $phong->refresh()->dongBoTrangThaiHeThong();

        return $datPhong;
    }

    private function taoHoaDon(DatPhong $datPhong, NguoiDung $admin): HoaDon
    {
        $soNgauNhien = random_int(1000, 9999);

        return HoaDon::query()->create([
            'ma_hoa_don' => 'HD' . $soNgauNhien,
            'dat_phong_id' => $datPhong->id,
            'tong_tien_phong' => 850000,
            'tong_tien_dich_vu' => 0,
            'giam_gia' => 0,
            'thue' => 0,
            'tong_tien' => 850000,
            'trang_thai' => 'chua_thanh_toan',
            'thoi_diem_xuat' => now(),
            'nguoi_tao_id' => $admin->id,
        ]);
    }

    private function taoThanhToan(HoaDon $hoaDon, NguoiDung $admin): ThanhToan
    {
        $soNgauNhien = random_int(1000, 9999);

        return ThanhToan::query()->create([
            'ma_thanh_toan' => 'TT' . $soNgauNhien,
            'hoa_don_id' => $hoaDon->id,
            'so_tien' => 300000,
            'phuong_thuc_thanh_toan' => 'tien_mat',
            'thoi_diem_thanh_toan' => now(),
            'trang_thai' => 'thanh_cong',
            'nguoi_tao_id' => $admin->id,
        ]);
    }
}
