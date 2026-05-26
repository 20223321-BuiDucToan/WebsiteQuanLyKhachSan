<?php

namespace Database\Seeders;

use App\Models\DatPhong;
use App\Models\HoaDon;
use App\Models\KhachHang;
use App\Models\NguoiDung;
use App\Models\Phong;
use App\Models\DichVu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NghiepVuDemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $danhSachNguoiDung = NguoiDung::query()
                ->whereIn('ten_dang_nhap', ['admin', 'nhanvien1', 'nhanvien2', 'khach1', 'khach2', 'khach3', 'khach4'])
                ->get()
                ->keyBy('ten_dang_nhap');

            $danhSachKhachHang = KhachHang::query()
                ->whereIn('ma_khach_hang', ['KH001', 'KH002', 'KH003', 'KH004', 'KH005', 'KH006'])
                ->get()
                ->keyBy('ma_khach_hang');

            $danhSachPhong = Phong::query()
                ->whereIn('so_phong', ['104', '201', '203', '205', '301', '302', '303', '402', '501'])
                ->get()
                ->keyBy('so_phong');

            $danhSachDichVu = DichVu::query()
                ->whereIn('ma_dich_vu', ['DV001', 'DV002', 'DV003', 'DV004', 'DV006', 'DV007', 'DV009', 'DV010'])
                ->get()
                ->keyBy('ma_dich_vu');

            $homNay = now()->startOfDay();

            $danhSachDatPhong = [
                [
                    'ma_dat_phong' => 'DP0001',
                    'khach_hang_ma' => 'KH001',
                    'nguoi_tao' => 'khach1',
                    'ngay_dat' => $homNay->copy()->subDays(2)->setTime(9, 15),
                    'ngay_nhan_phong_du_kien' => $homNay->copy()->addDays(5),
                    'ngay_tra_phong_du_kien' => $homNay->copy()->addDays(7),
                    'ngay_nhan_phong_thuc_te' => null,
                    'ngay_tra_phong_thuc_te' => null,
                    'thoi_diem_khong_den' => null,
                    'phi_khong_den' => 0,
                    'ly_do_khong_den' => null,
                    'so_nguoi_lon' => 2,
                    'so_tre_em' => 0,
                    'trang_thai' => DatPhong::TRANG_THAI_CHO_XAC_NHAN,
                    'nguon_dat' => 'website',
                    'yeu_cau_dac_biet' => 'Ưu tiên phòng yên tĩnh và nhận phòng muộn.',
                    'ghi_chu' => 'Đơn mẫu chờ xác nhận để test bộ lọc.',
                    'chi_tiet_phong' => [
                        ['so_phong' => '203', 'gia_phong' => 980000, 'so_dem' => 2, 'trang_thai' => 'da_dat'],
                    ],
                    'dich_vu_su_dung' => [],
                    'hoa_don' => null,
                ],
                [
                    'ma_dat_phong' => 'DP0002',
                    'khach_hang_ma' => 'KH002',
                    'nguoi_tao' => 'khach2',
                    'ngay_dat' => $homNay->copy()->subDays(1)->setTime(20, 45),
                    'ngay_nhan_phong_du_kien' => $homNay->copy(),
                    'ngay_tra_phong_du_kien' => $homNay->copy()->addDays(2),
                    'ngay_nhan_phong_thuc_te' => null,
                    'ngay_tra_phong_thuc_te' => null,
                    'thoi_diem_khong_den' => null,
                    'phi_khong_den' => 0,
                    'ly_do_khong_den' => null,
                    'so_nguoi_lon' => 2,
                    'so_tre_em' => 0,
                    'trang_thai' => DatPhong::TRANG_THAI_DA_XAC_NHAN,
                    'nguon_dat' => 'website',
                    'yeu_cau_dac_biet' => 'Cần hóa đơn VAT và check-in nhanh.',
                    'ghi_chu' => 'Đơn có yêu cầu cọc từ khách hàng.',
                    'chi_tiet_phong' => [
                        ['so_phong' => '205', 'gia_phong' => 1220000, 'so_dem' => 2, 'trang_thai' => 'da_dat'],
                    ],
                    'dich_vu_su_dung' => [],
                    'hoa_don' => [
                        'ma_hoa_don' => 'HD0002',
                        'giam_gia' => 0,
                        'thue' => 60000,
                        'trang_thai' => 'chua_thanh_toan',
                        'thoi_diem_xuat' => $homNay->copy()->subHours(10),
                        'nguoi_tao' => 'nhanvien1',
                        'ghi_chu' => 'Hóa đơn theo dõi tiền cọc giữ phòng.',
                        'thanh_toan' => [
                            [
                                'ma_thanh_toan' => 'TT0002',
                                'so_tien' => 700000,
                                'phuong_thuc_thanh_toan' => 'chuyen_khoan',
                                'ma_tham_chieu' => 'CK-MTHU-700K',
                                'thoi_diem_thanh_toan' => $homNay->copy()->subHours(8),
                                'trang_thai' => 'cho_xu_ly',
                                'nguon_tao' => 'khach_hang',
                                'nguoi_tao' => 'khach2',
                                'nguoi_xu_ly' => null,
                                'thoi_diem_xu_ly' => null,
                                'ghi_chu' => '[Cọc phòng] Khách gửi cọc để giữ phòng.',
                            ],
                        ],
                    ],
                ],
                [
                    'ma_dat_phong' => 'DP0003',
                    'khach_hang_ma' => 'KH003',
                    'nguoi_tao' => 'nhanvien2',
                    'ngay_dat' => $homNay->copy()->subDays(3)->setTime(11, 5),
                    'ngay_nhan_phong_du_kien' => $homNay->copy()->subDay(),
                    'ngay_tra_phong_du_kien' => $homNay->copy()->addDay(),
                    'ngay_nhan_phong_thuc_te' => $homNay->copy()->subDay()->setTime(14, 20),
                    'ngay_tra_phong_thuc_te' => null,
                    'thoi_diem_khong_den' => null,
                    'phi_khong_den' => 0,
                    'ly_do_khong_den' => null,
                    'so_nguoi_lon' => 2,
                    'so_tre_em' => 0,
                    'trang_thai' => DatPhong::TRANG_THAI_DA_NHAN_PHONG,
                    'nguon_dat' => 'truc_tiep',
                    'yeu_cau_dac_biet' => 'Bổ sung thêm khăn và minibar mỗi ngày.',
                    'ghi_chu' => 'Đơn đang lưu trú để test dịch vụ phát sinh và thanh toán một phần.',
                    'chi_tiet_phong' => [
                        ['so_phong' => '303', 'gia_phong' => 2380000, 'so_dem' => 2, 'trang_thai' => 'dang_o'],
                    ],
                    'dich_vu_su_dung' => [
                        ['ma_dich_vu' => 'DV001', 'so_luong' => 2, 'don_gia' => 160000, 'thoi_diem_su_dung' => $homNay->copy()->subDay()->setTime(7, 30), 'nguoi_tao' => 'nhanvien1', 'ghi_chu' => 'Khách dùng thêm buffet sáng.'],
                        ['ma_dich_vu' => 'DV002', 'so_luong' => 2, 'don_gia' => 55000, 'thoi_diem_su_dung' => $homNay->copy()->subHours(6), 'nguoi_tao' => 'nhanvien1', 'ghi_chu' => 'Minibar trong phòng.'],
                        ['ma_dich_vu' => 'DV003', 'so_luong' => 3, 'don_gia' => 35000, 'thoi_diem_su_dung' => $homNay->copy()->subHours(4), 'nguoi_tao' => 'nhanvien2', 'ghi_chu' => 'Giặt ủi nhanh trong ngày.'],
                    ],
                    'hoa_don' => [
                        'ma_hoa_don' => 'HD0003',
                        'giam_gia' => 200000,
                        'thue' => 150000,
                        'trang_thai' => 'thanh_toan_mot_phan',
                        'thoi_diem_xuat' => $homNay->copy()->subDay()->setTime(15, 30),
                        'nguoi_tao' => 'nhanvien2',
                        'ghi_chu' => 'Hóa đơn đang theo dõi thanh toán trong thời gian lưu trú.',
                        'thanh_toan' => [
                            [
                                'ma_thanh_toan' => 'TT0003',
                                'so_tien' => 2000000,
                                'phuong_thuc_thanh_toan' => 'tien_mat',
                                'ma_tham_chieu' => null,
                                'thoi_diem_thanh_toan' => $homNay->copy()->subHours(3),
                                'trang_thai' => 'thanh_cong',
                                'nguon_tao' => 'noi_bo',
                                'nguoi_tao' => 'nhanvien2',
                                'nguoi_xu_ly' => 'nhanvien2',
                                'thoi_diem_xu_ly' => $homNay->copy()->subHours(3),
                                'ghi_chu' => 'Khách thanh toán trước một phần tại quầy.',
                            ],
                        ],
                    ],
                ],
                [
                    'ma_dat_phong' => 'DP0004',
                    'khach_hang_ma' => 'KH004',
                    'nguoi_tao' => 'admin',
                    'ngay_dat' => $homNay->copy()->subDays(8)->setTime(9, 0),
                    'ngay_nhan_phong_du_kien' => $homNay->copy()->subDays(5),
                    'ngay_tra_phong_du_kien' => $homNay->copy()->subDays(2),
                    'ngay_nhan_phong_thuc_te' => $homNay->copy()->subDays(5)->setTime(13, 45),
                    'ngay_tra_phong_thuc_te' => $homNay->copy()->subDays(2)->setTime(11, 20),
                    'thoi_diem_khong_den' => null,
                    'phi_khong_den' => 0,
                    'ly_do_khong_den' => null,
                    'so_nguoi_lon' => 2,
                    'so_tre_em' => 1,
                    'trang_thai' => DatPhong::TRANG_THAI_DA_TRA_PHONG,
                    'nguon_dat' => 'dien_thoai',
                    'yeu_cau_dac_biet' => 'Cần xe sân bay chiều về.',
                    'ghi_chu' => 'Đơn hoàn tất và đã thanh toán đủ.',
                    'chi_tiet_phong' => [
                        ['so_phong' => '402', 'gia_phong' => 860000, 'so_dem' => 3, 'trang_thai' => 'da_tra_phong'],
                    ],
                    'dich_vu_su_dung' => [
                        ['ma_dich_vu' => 'DV004', 'so_luong' => 1, 'don_gia' => 320000, 'thoi_diem_su_dung' => $homNay->copy()->subDays(2)->setTime(12, 0), 'nguoi_tao' => 'nhanvien1', 'ghi_chu' => 'Đưa khách ra sân bay.'],
                        ['ma_dich_vu' => 'DV001', 'so_luong' => 4, 'don_gia' => 160000, 'thoi_diem_su_dung' => $homNay->copy()->subDays(4)->setTime(7, 0), 'nguoi_tao' => 'nhanvien1', 'ghi_chu' => 'Buffet sáng thêm cho cả gia đình.'],
                    ],
                    'hoa_don' => [
                        'ma_hoa_don' => 'HD0004',
                        'giam_gia' => 140000,
                        'thue' => 100000,
                        'trang_thai' => 'da_thanh_toan',
                        'thoi_diem_xuat' => $homNay->copy()->subDays(2)->setTime(11, 25),
                        'nguoi_tao' => 'admin',
                        'ghi_chu' => 'Hóa đơn hoàn tất sau khi khách trả phòng.',
                        'thanh_toan' => [
                            [
                                'ma_thanh_toan' => 'TT0004A',
                                'so_tien' => 1500000,
                                'phuong_thuc_thanh_toan' => 'tien_mat',
                                'ma_tham_chieu' => null,
                                'thoi_diem_thanh_toan' => $homNay->copy()->subDays(3)->setTime(18, 30),
                                'trang_thai' => 'thanh_cong',
                                'nguon_tao' => 'noi_bo',
                                'nguoi_tao' => 'nhanvien1',
                                'nguoi_xu_ly' => 'nhanvien1',
                                'thoi_diem_xu_ly' => $homNay->copy()->subDays(3)->setTime(18, 30),
                                'ghi_chu' => 'Khách thanh toán trước khi trả phòng.',
                            ],
                            [
                                'ma_thanh_toan' => 'TT0004B',
                                'so_tien' => 2000000,
                                'phuong_thuc_thanh_toan' => 'the',
                                'ma_tham_chieu' => 'CARD-SETTLE-0404',
                                'thoi_diem_thanh_toan' => $homNay->copy()->subDays(2)->setTime(11, 25),
                                'trang_thai' => 'thanh_cong',
                                'nguon_tao' => 'noi_bo',
                                'nguoi_tao' => 'admin',
                                'nguoi_xu_ly' => 'admin',
                                'thoi_diem_xu_ly' => $homNay->copy()->subDays(2)->setTime(11, 25),
                                'ghi_chu' => 'Chốt phần còn lại qua máy POS.',
                            ],
                        ],
                    ],
                ],
                [
                    'ma_dat_phong' => 'DP0005',
                    'khach_hang_ma' => 'KH006',
                    'nguoi_tao' => 'nhanvien1',
                    'ngay_dat' => $homNay->copy()->subDays(2)->setTime(16, 40),
                    'ngay_nhan_phong_du_kien' => $homNay->copy(),
                    'ngay_tra_phong_du_kien' => $homNay->copy()->addDays(2),
                    'ngay_nhan_phong_thuc_te' => null,
                    'ngay_tra_phong_thuc_te' => null,
                    'thoi_diem_khong_den' => $homNay->copy()->setTime(20, 30),
                    'phi_khong_den' => 590000,
                    'ly_do_khong_den' => 'Khách báo trễ nhưng không đến nhận phòng.',
                    'so_nguoi_lon' => 2,
                    'so_tre_em' => 0,
                    'trang_thai' => DatPhong::TRANG_THAI_KHONG_DEN,
                    'nguon_dat' => 'dien_thoai',
                    'yeu_cau_dac_biet' => 'Giữ phòng đến 20:00.',
                    'ghi_chu' => 'Đơn mẫu nghiệp vụ no-show.',
                    'chi_tiet_phong' => [
                        ['so_phong' => '104', 'gia_phong' => 590000, 'so_dem' => 2, 'trang_thai' => 'khong_den'],
                    ],
                    'dich_vu_su_dung' => [],
                    'hoa_don' => [
                        'ma_hoa_don' => 'HD0005',
                        'giam_gia' => 0,
                        'thue' => 0,
                        'trang_thai' => 'da_thanh_toan',
                        'thoi_diem_xuat' => $homNay->copy()->setTime(20, 35),
                        'nguoi_tao' => 'nhanvien1',
                        'ghi_chu' => 'Hóa đơn phí no-show cho đêm đầu tiên.',
                        'thanh_toan' => [
                            [
                                'ma_thanh_toan' => 'TT0005',
                                'so_tien' => 590000,
                                'phuong_thuc_thanh_toan' => 'chuyen_khoan',
                                'ma_tham_chieu' => 'NSHOW-590K',
                                'thoi_diem_thanh_toan' => $homNay->copy()->setTime(20, 40),
                                'trang_thai' => 'thanh_cong',
                                'nguon_tao' => 'noi_bo',
                                'nguoi_tao' => 'nhanvien1',
                                'nguoi_xu_ly' => 'nhanvien1',
                                'thoi_diem_xu_ly' => $homNay->copy()->setTime(20, 40),
                                'ghi_chu' => 'Khấu trừ phí giữ phòng theo chính sách no-show.',
                            ],
                        ],
                    ],
                ],
                [
                    'ma_dat_phong' => 'DP0006',
                    'khach_hang_ma' => 'KH005',
                    'nguoi_tao' => 'nhanvien1',
                    'ngay_dat' => $homNay->copy()->subDays(1)->setTime(10, 10),
                    'ngay_nhan_phong_du_kien' => $homNay->copy()->addDays(10),
                    'ngay_tra_phong_du_kien' => $homNay->copy()->addDays(12),
                    'ngay_nhan_phong_thuc_te' => null,
                    'ngay_tra_phong_thuc_te' => null,
                    'thoi_diem_khong_den' => null,
                    'phi_khong_den' => 0,
                    'ly_do_khong_den' => null,
                    'so_nguoi_lon' => 2,
                    'so_tre_em' => 0,
                    'trang_thai' => DatPhong::TRANG_THAI_DA_HUY,
                    'nguon_dat' => 'zalo',
                    'yeu_cau_dac_biet' => 'Giữ phòng yên tĩnh gần thang máy.',
                    'ghi_chu' => 'Đơn bị hủy để test luồng hóa đơn đã hủy.',
                    'chi_tiet_phong' => [
                        ['so_phong' => '201', 'gia_phong' => 790000, 'so_dem' => 2, 'trang_thai' => 'da_huy'],
                    ],
                    'dich_vu_su_dung' => [],
                    'hoa_don' => [
                        'ma_hoa_don' => 'HD0006',
                        'giam_gia' => 0,
                        'thue' => 0,
                        'trang_thai' => 'da_huy',
                        'thoi_diem_xuat' => $homNay->copy()->subHours(12),
                        'nguoi_tao' => 'nhanvien1',
                        'ghi_chu' => 'Hóa đơn đã hủy cùng đơn đặt phòng.',
                        'thanh_toan' => [],
                    ],
                ],
                [
                    'ma_dat_phong' => 'DP0007',
                    'khach_hang_ma' => 'KH003',
                    'nguoi_tao' => 'nhanvien2',
                    'ngay_dat' => $homNay->copy()->subDays(4)->setTime(15, 20),
                    'ngay_nhan_phong_du_kien' => $homNay->copy()->subDays(3),
                    'ngay_tra_phong_du_kien' => $homNay->copy()->subDays(2),
                    'ngay_nhan_phong_thuc_te' => $homNay->copy()->subDays(3)->setTime(13, 0),
                    'ngay_tra_phong_thuc_te' => $homNay->copy()->subDays(2)->setTime(10, 40),
                    'thoi_diem_khong_den' => null,
                    'phi_khong_den' => 0,
                    'ly_do_khong_den' => null,
                    'so_nguoi_lon' => 2,
                    'so_tre_em' => 0,
                    'trang_thai' => DatPhong::TRANG_THAI_DA_TRA_PHONG,
                    'nguon_dat' => 'truc_tiep',
                    'yeu_cau_dac_biet' => 'Phòng gần thang máy để tiện di chuyển.',
                    'ghi_chu' => 'Đơn đã trả phòng nhưng còn công nợ.',
                    'chi_tiet_phong' => [
                        ['so_phong' => '501', 'gia_phong' => 1450000, 'so_dem' => 1, 'trang_thai' => 'da_tra_phong'],
                    ],
                    'dich_vu_su_dung' => [
                        ['ma_dich_vu' => 'DV009', 'so_luong' => 2, 'don_gia' => 290000, 'thoi_diem_su_dung' => $homNay->copy()->subDays(2)->setTime(19, 15), 'nguoi_tao' => 'nhanvien2', 'ghi_chu' => 'Khách gọi bữa tối tại phòng.'],
                        ['ma_dich_vu' => 'DV010', 'so_luong' => 6, 'don_gia' => 20000, 'thoi_diem_su_dung' => $homNay->copy()->subDays(2)->setTime(21, 0), 'nguoi_tao' => 'nhanvien2', 'ghi_chu' => 'Nước suối thêm cho khách.'],
                    ],
                    'hoa_don' => [
                        'ma_hoa_don' => 'HD0007',
                        'giam_gia' => 0,
                        'thue' => 70000,
                        'trang_thai' => 'chua_thanh_toan',
                        'thoi_diem_xuat' => $homNay->copy()->subDays(2)->setTime(10, 45),
                        'nguoi_tao' => 'nhanvien2',
                        'ghi_chu' => 'Hóa đơn còn công nợ để test màn hình thu tiền.',
                        'thanh_toan' => [
                            [
                                'ma_thanh_toan' => 'TT0007',
                                'so_tien' => 500000,
                                'phuong_thuc_thanh_toan' => 'the',
                                'ma_tham_chieu' => 'FAIL-500K',
                                'thoi_diem_thanh_toan' => $homNay->copy()->subDays(2)->setTime(10, 50),
                                'trang_thai' => 'that_bai',
                                'nguon_tao' => 'noi_bo',
                                'nguoi_tao' => 'nhanvien2',
                                'nguoi_xu_ly' => 'nhanvien2',
                                'thoi_diem_xu_ly' => $homNay->copy()->subDays(2)->setTime(10, 52),
                                'ghi_chu' => 'Máy POS báo lỗi nên giao dịch thất bại.',
                            ],
                        ],
                    ],
                ],
                [
                    'ma_dat_phong' => 'DP0008',
                    'khach_hang_ma' => 'KH005',
                    'nguoi_tao' => 'admin',
                    'ngay_dat' => $homNay->copy()->subDays(1)->setTime(8, 45),
                    'ngay_nhan_phong_du_kien' => $homNay->copy()->addDays(7),
                    'ngay_tra_phong_du_kien' => $homNay->copy()->addDays(10),
                    'ngay_nhan_phong_thuc_te' => null,
                    'ngay_tra_phong_thuc_te' => null,
                    'thoi_diem_khong_den' => null,
                    'phi_khong_den' => 0,
                    'ly_do_khong_den' => null,
                    'so_nguoi_lon' => 4,
                    'so_tre_em' => 2,
                    'trang_thai' => DatPhong::TRANG_THAI_DA_XAC_NHAN,
                    'nguon_dat' => 'dien_thoai',
                    'yeu_cau_dac_biet' => 'Cần hai phòng cùng tầng cho khách đoàn.',
                    'ghi_chu' => 'Đơn nhiều phòng để test tổng tiền lớn và cọc trước.',
                    'chi_tiet_phong' => [
                        ['so_phong' => '301', 'gia_phong' => 1650000, 'so_dem' => 3, 'trang_thai' => 'da_dat', 'so_nguoi_lon' => 2, 'so_tre_em' => 1],
                        ['so_phong' => '302', 'gia_phong' => 1720000, 'so_dem' => 3, 'trang_thai' => 'da_dat', 'so_nguoi_lon' => 2, 'so_tre_em' => 1],
                    ],
                    'dich_vu_su_dung' => [],
                    'hoa_don' => [
                        'ma_hoa_don' => 'HD0008',
                        'giam_gia' => 0,
                        'thue' => 250000,
                        'trang_thai' => 'thanh_toan_mot_phan',
                        'thoi_diem_xuat' => $homNay->copy()->subHours(4),
                        'nguoi_tao' => 'admin',
                        'ghi_chu' => 'Hóa đơn theo dõi cọc cho khách đoàn doanh nghiệp.',
                        'thanh_toan' => [
                            [
                                'ma_thanh_toan' => 'TT0008',
                                'so_tien' => 3000000,
                                'phuong_thuc_thanh_toan' => 'chuyen_khoan',
                                'ma_tham_chieu' => 'CORP-DEPOSIT-3000',
                                'thoi_diem_thanh_toan' => $homNay->copy()->subHours(3),
                                'trang_thai' => 'thanh_cong',
                                'nguon_tao' => 'noi_bo',
                                'nguoi_tao' => 'admin',
                                'nguoi_xu_ly' => 'admin',
                                'thoi_diem_xu_ly' => $homNay->copy()->subHours(3),
                                'ghi_chu' => 'Khách đoàn chuyển khoản cọc trước.',
                            ],
                        ],
                    ],
                ],
            ];

            foreach ($danhSachDatPhong as $duLieuDatPhong) {
                $khachHang = $danhSachKhachHang->get($duLieuDatPhong['khach_hang_ma']);
                $nguoiTao = $danhSachNguoiDung->get($duLieuDatPhong['nguoi_tao']);

                if (! $khachHang || ! $nguoiTao) {
                    continue;
                }

                $datPhong = DatPhong::query()->updateOrCreate(
                    ['ma_dat_phong' => $duLieuDatPhong['ma_dat_phong']],
                    [
                        'khach_hang_id' => $khachHang->id,
                        'nguoi_tao_id' => $nguoiTao->id,
                        'ngay_dat' => $duLieuDatPhong['ngay_dat'],
                        'ngay_nhan_phong_du_kien' => $duLieuDatPhong['ngay_nhan_phong_du_kien'],
                        'ngay_tra_phong_du_kien' => $duLieuDatPhong['ngay_tra_phong_du_kien'],
                        'ngay_nhan_phong_thuc_te' => $duLieuDatPhong['ngay_nhan_phong_thuc_te'],
                        'ngay_tra_phong_thuc_te' => $duLieuDatPhong['ngay_tra_phong_thuc_te'],
                        'thoi_diem_khong_den' => $duLieuDatPhong['thoi_diem_khong_den'],
                        'phi_khong_den' => $duLieuDatPhong['phi_khong_den'],
                        'ly_do_khong_den' => $duLieuDatPhong['ly_do_khong_den'],
                        'so_nguoi_lon' => $duLieuDatPhong['so_nguoi_lon'],
                        'so_tre_em' => $duLieuDatPhong['so_tre_em'],
                        'trang_thai' => $duLieuDatPhong['trang_thai'],
                        'nguon_dat' => $duLieuDatPhong['nguon_dat'],
                        'yeu_cau_dac_biet' => $duLieuDatPhong['yeu_cau_dac_biet'],
                        'ghi_chu' => $duLieuDatPhong['ghi_chu'],
                    ]
                );

                $datPhong->chiTietDatPhong()->delete();

                foreach ($duLieuDatPhong['chi_tiet_phong'] as $chiTietPhong) {
                    $phong = $danhSachPhong->get($chiTietPhong['so_phong']);

                    if (! $phong) {
                        continue;
                    }

                    $datPhong->chiTietDatPhong()->create([
                        'phong_id' => $phong->id,
                        'gia_phong' => $chiTietPhong['gia_phong'],
                        'so_dem' => $chiTietPhong['so_dem'],
                        'so_nguoi_lon' => $chiTietPhong['so_nguoi_lon'] ?? $duLieuDatPhong['so_nguoi_lon'],
                        'so_tre_em' => $chiTietPhong['so_tre_em'] ?? $duLieuDatPhong['so_tre_em'],
                        'ngay_nhan_phong_thuc_te' => $duLieuDatPhong['ngay_nhan_phong_thuc_te'],
                        'ngay_tra_phong_thuc_te' => $duLieuDatPhong['ngay_tra_phong_thuc_te'],
                        'trang_thai' => $chiTietPhong['trang_thai'],
                        'ghi_chu' => 'Chi tiết phòng demo cho dữ liệu kiểm thử.',
                    ]);

                    if ($duLieuDatPhong['trang_thai'] === DatPhong::TRANG_THAI_DA_TRA_PHONG) {
                        $phong->forceFill(['tinh_trang_ve_sinh' => 'can_don'])->saveQuietly();
                    }

                    if ($duLieuDatPhong['trang_thai'] === DatPhong::TRANG_THAI_DA_NHAN_PHONG) {
                        $phong->forceFill(['tinh_trang_ve_sinh' => 'sach'])->saveQuietly();
                    }
                }

                $datPhong->suDungDichVu()->delete();

                foreach ($duLieuDatPhong['dich_vu_su_dung'] as $dichVuSuDung) {
                    $dichVu = $danhSachDichVu->get($dichVuSuDung['ma_dich_vu']);
                    $nguoiTaoDichVu = $danhSachNguoiDung->get($dichVuSuDung['nguoi_tao']);

                    if (! $dichVu || ! $nguoiTaoDichVu) {
                        continue;
                    }

                    $soLuong = (int) $dichVuSuDung['so_luong'];
                    $donGia = (float) $dichVuSuDung['don_gia'];

                    $datPhong->suDungDichVu()->create([
                        'dich_vu_id' => $dichVu->id,
                        'so_luong' => $soLuong,
                        'don_gia' => $donGia,
                        'thanh_tien' => $soLuong * $donGia,
                        'thoi_diem_su_dung' => $dichVuSuDung['thoi_diem_su_dung'],
                        'nguoi_tao_id' => $nguoiTaoDichVu->id,
                        'ghi_chu' => $dichVuSuDung['ghi_chu'],
                    ]);
                }

                if ($duLieuDatPhong['hoa_don']) {
                    $nguoiTaoHoaDon = $danhSachNguoiDung->get($duLieuDatPhong['hoa_don']['nguoi_tao']);

                    if (! $nguoiTaoHoaDon) {
                        continue;
                    }

                    $hoaDon = HoaDon::query()->updateOrCreate(
                        ['ma_hoa_don' => $duLieuDatPhong['hoa_don']['ma_hoa_don']],
                        [
                            'dat_phong_id' => $datPhong->id,
                            'tong_tien_phong' => 0,
                            'tong_tien_dich_vu' => 0,
                            'giam_gia' => $duLieuDatPhong['hoa_don']['giam_gia'],
                            'thue' => $duLieuDatPhong['hoa_don']['thue'],
                            'tong_tien' => 0,
                            'trang_thai' => $duLieuDatPhong['hoa_don']['trang_thai'],
                            'thoi_diem_xuat' => $duLieuDatPhong['hoa_don']['thoi_diem_xuat'],
                            'nguoi_tao_id' => $nguoiTaoHoaDon->id,
                            'ghi_chu' => $duLieuDatPhong['hoa_don']['ghi_chu'],
                        ]
                    );

                    $hoaDon->thanhToan()->delete();

                    foreach ($duLieuDatPhong['hoa_don']['thanh_toan'] as $duLieuThanhToan) {
                        $nguoiTaoThanhToan = $danhSachNguoiDung->get($duLieuThanhToan['nguoi_tao']);
                        $nguoiXuLy = $duLieuThanhToan['nguoi_xu_ly']
                            ? $danhSachNguoiDung->get($duLieuThanhToan['nguoi_xu_ly'])
                            : null;

                        if (! $nguoiTaoThanhToan) {
                            continue;
                        }

                        $hoaDon->thanhToan()->create([
                            'ma_thanh_toan' => $duLieuThanhToan['ma_thanh_toan'],
                            'so_tien' => $duLieuThanhToan['so_tien'],
                            'phuong_thuc_thanh_toan' => $duLieuThanhToan['phuong_thuc_thanh_toan'],
                            'ma_tham_chieu' => $duLieuThanhToan['ma_tham_chieu'],
                            'thoi_diem_thanh_toan' => $duLieuThanhToan['thoi_diem_thanh_toan'],
                            'trang_thai' => $duLieuThanhToan['trang_thai'],
                            'nguon_tao' => $duLieuThanhToan['nguon_tao'],
                            'nguoi_tao_id' => $nguoiTaoThanhToan->id,
                            'nguoi_xu_ly_id' => $nguoiXuLy?->id,
                            'thoi_diem_xu_ly' => $duLieuThanhToan['thoi_diem_xu_ly'],
                            'ghi_chu' => $duLieuThanhToan['ghi_chu'],
                        ]);
                    }

                    $hoaDon = $hoaDon->fresh([
                        'datPhong.chiTietDatPhong',
                        'datPhong.suDungDichVu',
                        'thanhToan',
                    ]);

                    $hoaDon->dongBoGiaTriTuDatPhong();

                    if ($duLieuDatPhong['hoa_don']['trang_thai'] === 'da_huy') {
                        $hoaDon->forceFill(['trang_thai' => 'da_huy'])->saveQuietly();
                    }

                    $hoaDon->refresh()->giaiPhongSauKhiHoanTatThanhToan();
                }
            }

            Phong::query()->get()->each(function (Phong $phong) {
                $phong->refresh()->dongBoTrangThaiHeThong();
            });
        });
    }
}
