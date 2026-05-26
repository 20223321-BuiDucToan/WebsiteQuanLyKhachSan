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
use Illuminate\Support\Facades\Artisan;
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

    public function test_confirming_booking_auto_creates_invoice_for_deposit_tracking(): void
    {
        $admin = $this->taoAdmin();
        $datPhong = $this->taoDatPhong($admin, [
            'trang_thai' => DatPhong::TRANG_THAI_CHO_XAC_NHAN,
        ]);

        $response = $this->from(route('dat-phong.show', $datPhong))
            ->actingAs($admin)
            ->patch(route('dat-phong.cap-nhat-trang-thai', $datPhong), [
                'trang_thai' => DatPhong::TRANG_THAI_DA_XAC_NHAN,
            ]);

        $response->assertRedirect(route('dat-phong.show', $datPhong));
        $this->assertDatabaseHas('dat_phong', [
            'id' => $datPhong->id,
            'trang_thai' => DatPhong::TRANG_THAI_DA_XAC_NHAN,
        ]);
        $this->assertDatabaseHas('hoa_don', [
            'dat_phong_id' => $datPhong->id,
            'tong_tien_phong' => 850000,
            'tong_tien' => 850000,
            'trang_thai' => 'chua_thanh_toan',
        ]);
    }

    public function test_staff_can_mark_confirmed_booking_as_no_show_and_sync_invoice(): void
    {
        $admin = $this->taoAdmin();
        [, $phong] = $this->taoPhong();
        $datPhong = $this->taoDatPhong($admin, [
            'trang_thai' => DatPhong::TRANG_THAI_DA_XAC_NHAN,
            'ngay_nhan_phong_du_kien' => now()->toDateString(),
            'ngay_tra_phong_du_kien' => now()->addDay()->toDateString(),
        ], $phong);

        $response = $this->from(route('dat-phong.show', $datPhong))
            ->actingAs($admin)
            ->patch(route('dat-phong.cap-nhat-trang-thai', $datPhong), [
                'trang_thai' => DatPhong::TRANG_THAI_KHONG_DEN,
                'phi_khong_den' => 300000,
                'ly_do_khong_den' => 'Khach khong den truoc 23:00.',
            ]);

        $response->assertRedirect(route('dat-phong.show', $datPhong));
        $this->assertDatabaseHas('dat_phong', [
            'id' => $datPhong->id,
            'trang_thai' => DatPhong::TRANG_THAI_KHONG_DEN,
            'phi_khong_den' => 300000,
        ]);
        $this->assertDatabaseHas('chi_tiet_dat_phong', [
            'dat_phong_id' => $datPhong->id,
            'trang_thai' => DatPhong::TRANG_THAI_KHONG_DEN,
        ]);
        $this->assertDatabaseHas('hoa_don', [
            'dat_phong_id' => $datPhong->id,
            'tong_tien_phong' => 300000,
            'tong_tien' => 300000,
        ]);
        $this->assertSame(Phong::TRANG_THAI_TRONG, $phong->fresh()->trang_thai);
    }

    public function test_staff_cannot_mark_booking_as_no_show_before_arrival_date(): void
    {
        $admin = $this->taoAdmin();
        $datPhong = $this->taoDatPhong($admin, [
            'trang_thai' => DatPhong::TRANG_THAI_DA_XAC_NHAN,
            'ngay_nhan_phong_du_kien' => now()->addDay()->toDateString(),
            'ngay_tra_phong_du_kien' => now()->addDays(2)->toDateString(),
        ]);

        $response = $this->from(route('dat-phong.show', $datPhong))
            ->actingAs($admin)
            ->patch(route('dat-phong.cap-nhat-trang-thai', $datPhong), [
                'trang_thai' => DatPhong::TRANG_THAI_KHONG_DEN,
                'phi_khong_den' => 300000,
            ]);

        $response->assertRedirect(route('dat-phong.show', $datPhong));
        $response->assertSessionHasErrors('trang_thai');
        $this->assertDatabaseHas('dat_phong', [
            'id' => $datPhong->id,
            'trang_thai' => DatPhong::TRANG_THAI_DA_XAC_NHAN,
        ]);
    }

    public function test_overdue_online_booking_waiting_confirmation_is_auto_cancelled(): void
    {
        $admin = $this->taoAdmin();
        [, $phong] = $this->taoPhong();
        $datPhong = $this->taoDatPhong($admin, [
            'nguoi_tao_id' => null,
            'nguon_dat' => 'website',
            'trang_thai' => DatPhong::TRANG_THAI_CHO_XAC_NHAN,
            'ngay_nhan_phong_du_kien' => now()->subDay()->toDateString(),
            'ngay_tra_phong_du_kien' => now()->toDateString(),
        ], $phong);

        Artisan::call('booking:process-overdue-arrivals');

        $this->assertDatabaseHas('dat_phong', [
            'id' => $datPhong->id,
            'trang_thai' => DatPhong::TRANG_THAI_DA_HUY,
        ]);
        $this->assertDatabaseHas('chi_tiet_dat_phong', [
            'dat_phong_id' => $datPhong->id,
            'trang_thai' => DatPhong::TRANG_THAI_DA_HUY,
        ]);
        $this->assertSame(Phong::TRANG_THAI_TRONG, $phong->fresh()->trang_thai);
    }

    public function test_overdue_online_confirmed_booking_is_auto_marked_no_show(): void
    {
        $admin = $this->taoAdmin();
        [, $phong] = $this->taoPhong();
        $datPhong = $this->taoDatPhong($admin, [
            'nguoi_tao_id' => null,
            'nguon_dat' => 'website',
            'trang_thai' => DatPhong::TRANG_THAI_DA_XAC_NHAN,
            'ngay_nhan_phong_du_kien' => now()->subDay()->toDateString(),
            'ngay_tra_phong_du_kien' => now()->toDateString(),
        ], $phong);

        Artisan::call('booking:process-overdue-arrivals');

        $this->assertDatabaseHas('dat_phong', [
            'id' => $datPhong->id,
            'trang_thai' => DatPhong::TRANG_THAI_KHONG_DEN,
            'phi_khong_den' => 850000,
        ]);
        $this->assertDatabaseHas('chi_tiet_dat_phong', [
            'dat_phong_id' => $datPhong->id,
            'trang_thai' => DatPhong::TRANG_THAI_KHONG_DEN,
        ]);
        $this->assertDatabaseHas('hoa_don', [
            'dat_phong_id' => $datPhong->id,
            'tong_tien_phong' => 850000,
            'tong_tien' => 850000,
            'trang_thai' => 'chua_thanh_toan',
        ]);
        $this->assertSame(Phong::TRANG_THAI_TRONG, $phong->fresh()->trang_thai);
    }

    public function test_online_booking_is_not_auto_processed_before_deadline(): void
    {
        $this->travelTo(now()->startOfDay()->setTime(20, 0));

        $admin = $this->taoAdmin();
        $datPhong = $this->taoDatPhong($admin, [
            'nguoi_tao_id' => null,
            'nguon_dat' => 'website',
            'trang_thai' => DatPhong::TRANG_THAI_DA_XAC_NHAN,
            'ngay_nhan_phong_du_kien' => now()->toDateString(),
            'ngay_tra_phong_du_kien' => now()->addDay()->toDateString(),
        ]);

        Artisan::call('booking:process-overdue-arrivals');

        $this->assertDatabaseHas('dat_phong', [
            'id' => $datPhong->id,
            'trang_thai' => DatPhong::TRANG_THAI_DA_XAC_NHAN,
        ]);

        $this->travelBack();
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
        $response->assertSee('data-room-id="' . $phongTrong->id . '"', false);
        $response->assertDontSee('data-room-id="' . $phongDangCoKhach->id . '"', false);
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

    public function test_customer_can_filter_rooms_by_price_and_amenities(): void
    {
        $loaiPhongSeaView = LoaiPhong::query()->create([
            'ma_loai_phong' => 'LPSV' . random_int(1000, 9999),
            'ten_loai_phong' => 'Sea View Suite ' . random_int(1000, 9999),
            'gia_mot_dem' => 1800000,
            'so_nguoi_toi_da' => 3,
            'dien_tich' => 42,
            'so_giuong' => 2,
            'so_phong_tam' => 2,
            'co_ban_cong' => true,
            'co_bep_rieng' => true,
            'co_huong_bien' => true,
            'trang_thai' => 'hoat_dong',
        ]);

        $phongSeaView = Phong::query()->create([
            'ma_phong' => 'PHSV' . random_int(1000, 9999),
            'so_phong' => 'SV' . random_int(100, 999),
            'loai_phong_id' => $loaiPhongSeaView->id,
            'tang' => 8,
            'trang_thai' => Phong::TRANG_THAI_TRONG,
            'tinh_trang_ve_sinh' => 'sach',
            'tinh_trang_hoat_dong' => 'hoat_dong',
            'gia_mac_dinh' => 1900000,
        ]);

        $loaiPhongStandard = LoaiPhong::query()->create([
            'ma_loai_phong' => 'LPST' . random_int(1000, 9999),
            'ten_loai_phong' => 'Standard ' . random_int(1000, 9999),
            'gia_mot_dem' => 900000,
            'so_nguoi_toi_da' => 2,
            'dien_tich' => 24,
            'so_giuong' => 1,
            'so_phong_tam' => 1,
            'co_ban_cong' => false,
            'co_bep_rieng' => false,
            'co_huong_bien' => false,
            'trang_thai' => 'hoat_dong',
        ]);

        $phongStandard = Phong::query()->create([
            'ma_phong' => 'PHST' . random_int(1000, 9999),
            'so_phong' => 'ST' . random_int(100, 999),
            'loai_phong_id' => $loaiPhongStandard->id,
            'tang' => 3,
            'trang_thai' => Phong::TRANG_THAI_TRONG,
            'tinh_trang_ve_sinh' => 'sach',
            'tinh_trang_hoat_dong' => 'hoat_dong',
            'gia_mac_dinh' => 950000,
        ]);

        $response = $this->get(route('booking.index', [
            'gia_tu' => 1500000,
            'so_giuong' => 2,
            'co_ban_cong' => 1,
            'co_huong_bien' => 1,
        ]));

        $response->assertOk();
        $response->assertSee((string) $phongSeaView->so_phong);
        $response->assertDontSee((string) $phongStandard->so_phong);
    }

    public function test_customer_can_filter_rooms_by_bed_type_from_advanced_filters(): void
    {
        $loaiPhongKing = LoaiPhong::query()->create([
            'ma_loai_phong' => 'LPKG' . random_int(1000, 9999),
            'ten_loai_phong' => 'King Room ' . random_int(1000, 9999),
            'gia_mot_dem' => 1350000,
            'so_nguoi_toi_da' => 2,
            'dien_tich' => 30,
            'so_giuong' => 1,
            'loai_giuong' => 'King',
            'so_phong_tam' => 1,
            'trang_thai' => 'hoat_dong',
        ]);

        $phongKing = Phong::query()->create([
            'ma_phong' => 'PHKG' . random_int(1000, 9999),
            'so_phong' => 'KG' . random_int(100, 999),
            'loai_phong_id' => $loaiPhongKing->id,
            'tang' => 4,
            'trang_thai' => Phong::TRANG_THAI_TRONG,
            'tinh_trang_ve_sinh' => 'sach',
            'tinh_trang_hoat_dong' => 'hoat_dong',
            'gia_mac_dinh' => 1400000,
        ]);

        $loaiPhongTwin = LoaiPhong::query()->create([
            'ma_loai_phong' => 'LPTW' . random_int(1000, 9999),
            'ten_loai_phong' => 'Twin Room ' . random_int(1000, 9999),
            'gia_mot_dem' => 1280000,
            'so_nguoi_toi_da' => 2,
            'dien_tich' => 28,
            'so_giuong' => 2,
            'loai_giuong' => 'Twin',
            'so_phong_tam' => 1,
            'trang_thai' => 'hoat_dong',
        ]);

        $phongTwin = Phong::query()->create([
            'ma_phong' => 'PHTW' . random_int(1000, 9999),
            'so_phong' => 'TW' . random_int(100, 999),
            'loai_phong_id' => $loaiPhongTwin->id,
            'tang' => 6,
            'trang_thai' => Phong::TRANG_THAI_TRONG,
            'tinh_trang_ve_sinh' => 'sach',
            'tinh_trang_hoat_dong' => 'hoat_dong',
            'gia_mac_dinh' => 1280000,
        ]);

        $response = $this->get(route('booking.index', [
            'loai_giuong' => 'King',
        ]));

        $response->assertOk();
        $response->assertSee((string) $phongKing->so_phong);
        $response->assertDontSee((string) $phongTwin->so_phong);
    }

    public function test_customer_can_filter_rooms_by_area_and_floor_range(): void
    {
        $loaiPhongLarge = LoaiPhong::query()->create([
            'ma_loai_phong' => 'LPLG' . random_int(1000, 9999),
            'ten_loai_phong' => 'Large Room ' . random_int(1000, 9999),
            'gia_mot_dem' => 1550000,
            'so_nguoi_toi_da' => 3,
            'dien_tich' => 38,
            'so_giuong' => 2,
            'so_phong_tam' => 1,
            'trang_thai' => 'hoat_dong',
        ]);

        $phongPhuHop = Phong::query()->create([
            'ma_phong' => 'PHLG' . random_int(1000, 9999),
            'so_phong' => 'LG' . random_int(100, 999),
            'loai_phong_id' => $loaiPhongLarge->id,
            'tang' => 5,
            'trang_thai' => Phong::TRANG_THAI_TRONG,
            'tinh_trang_ve_sinh' => 'sach',
            'tinh_trang_hoat_dong' => 'hoat_dong',
            'gia_mac_dinh' => 1600000,
        ]);

        $loaiPhongSmall = LoaiPhong::query()->create([
            'ma_loai_phong' => 'LPSM' . random_int(1000, 9999),
            'ten_loai_phong' => 'Small Room ' . random_int(1000, 9999),
            'gia_mot_dem' => 950000,
            'so_nguoi_toi_da' => 2,
            'dien_tich' => 22,
            'so_giuong' => 1,
            'so_phong_tam' => 1,
            'trang_thai' => 'hoat_dong',
        ]);

        $phongKhongPhuHop = Phong::query()->create([
            'ma_phong' => 'PHSM' . random_int(1000, 9999),
            'so_phong' => 'SM' . random_int(100, 999),
            'loai_phong_id' => $loaiPhongSmall->id,
            'tang' => 9,
            'trang_thai' => Phong::TRANG_THAI_TRONG,
            'tinh_trang_ve_sinh' => 'sach',
            'tinh_trang_hoat_dong' => 'hoat_dong',
            'gia_mac_dinh' => 980000,
        ]);

        $response = $this->get(route('booking.index', [
            'dien_tich_tu' => 30,
            'dien_tich_den' => 45,
            'tang_tu' => 4,
            'tang_den' => 6,
        ]));

        $response->assertOk();
        $response->assertSee((string) $phongPhuHop->so_phong);
        $response->assertDontSee((string) $phongKhongPhuHop->so_phong);
    }

    public function test_customer_can_filter_rooms_that_have_images(): void
    {
        $loaiPhong = LoaiPhong::query()->create([
            'ma_loai_phong' => 'LPHA' . random_int(1000, 9999),
            'ten_loai_phong' => 'Image Room ' . random_int(1000, 9999),
            'gia_mot_dem' => 1100000,
            'so_nguoi_toi_da' => 2,
            'dien_tich' => 26,
            'so_giuong' => 1,
            'so_phong_tam' => 1,
            'trang_thai' => 'hoat_dong',
        ]);

        $phongCoAnh = Phong::query()->create([
            'ma_phong' => 'PHANH' . random_int(1000, 9999),
            'so_phong' => 'ANH' . random_int(100, 999),
            'loai_phong_id' => $loaiPhong->id,
            'tang' => 3,
            'trang_thai' => Phong::TRANG_THAI_TRONG,
            'tinh_trang_ve_sinh' => 'sach',
            'tinh_trang_hoat_dong' => 'hoat_dong',
            'gia_mac_dinh' => 1150000,
            'anh_phong' => ['uploads/phong/demo-1.jpg'],
        ]);

        $phongKhongAnh = Phong::query()->create([
            'ma_phong' => 'PHNO' . random_int(1000, 9999),
            'so_phong' => 'NO' . random_int(100, 999),
            'loai_phong_id' => $loaiPhong->id,
            'tang' => 4,
            'trang_thai' => Phong::TRANG_THAI_TRONG,
            'tinh_trang_ve_sinh' => 'sach',
            'tinh_trang_hoat_dong' => 'hoat_dong',
            'gia_mac_dinh' => 1120000,
            'anh_phong' => [],
        ]);

        $response = $this->get(route('booking.index', [
            'co_anh' => 1,
        ]));

        $response->assertOk();
        $response->assertSee((string) $phongCoAnh->so_phong);
        $response->assertDontSee((string) $phongKhongAnh->so_phong);
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
                DatPhong::TRANG_THAI_KHONG_DEN => DatPhong::TRANG_THAI_KHONG_DEN,
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
