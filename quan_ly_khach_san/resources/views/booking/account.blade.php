@extends('layouts.app')

@section('title', 'Tài khoản khách hàng')

@push('styles')
    <style>
        .account-hero {
            border: 1px solid #d8e3ef;
            border-radius: 26px;
            background:
                radial-gradient(circle at top left, #dff5ef 0, transparent 28%),
                linear-gradient(145deg, #ffffff, #f7fbff);
            box-shadow: 0 20px 42px rgba(16, 42, 67, 0.08);
            padding: 28px;
        }

        .account-stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 14px;
            margin-top: 22px;
        }

        .account-stat-card,
        .account-panel {
            border: 1px solid #dbe6f1;
            border-radius: 20px;
            background: #fff;
            box-shadow: 0 16px 34px rgba(16, 42, 67, 0.06);
        }

        .account-stat-card {
            padding: 16px;
            background: #f8fbff;
        }

        .account-stat-label {
            color: #68839f;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .account-stat-value {
            margin-top: 6px;
            font-size: 1.45rem;
            font-weight: 800;
            color: #10243e;
        }

        .account-panel {
            padding: 22px;
            height: 100%;
        }

        .account-panel-title {
            font-size: 1.05rem;
            font-weight: 800;
            color: #163552;
            margin-bottom: 4px;
        }

        .account-panel-subtitle {
            color: #68839f;
            font-size: 0.88rem;
            margin-bottom: 18px;
        }

        .profile-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .profile-item {
            border-bottom: 1px dashed #d8e3ef;
            padding-bottom: 12px;
        }

        .profile-item-label {
            color: #68839f;
            font-size: 0.78rem;
            margin-bottom: 4px;
        }

        .profile-item-value {
            font-weight: 700;
            color: #10243e;
        }

        .account-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 0.84rem;
            font-weight: 700;
        }

        .account-chip--success {
            background: #e8f8ef;
            color: #166534;
        }

        .account-chip--warning {
            background: #fff4e8;
            color: #b45309;
        }

        .account-chip--info {
            background: #eaf4ff;
            color: #0f5f92;
        }

        .missing-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .missing-tag {
            border-radius: 999px;
            padding: 6px 10px;
            background: #fff7ed;
            color: #b45309;
            font-size: 0.82rem;
            font-weight: 600;
        }

        .section-anchor {
            scroll-margin-top: 90px;
        }

        @media (max-width: 767px) {
            .profile-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $tongTienConLai = (float) $thongKe['tong_con_lai'];
        $tongTienChoXuLy = (float) $thongKe['tong_cho_xu_ly'];
    @endphp

    <section class="account-hero mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <h1 class="h3 mb-2">Tài khoản khách hàng</h1>
                <p class="text-muted mb-0">
                    Quản lý hồ sơ cá nhân, bổ sung thông tin lưu trú và theo dõi toàn bộ hóa đơn, thanh toán của bạn.
                </p>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('booking.index') }}" class="btn btn-outline-secondary rounded-3">Đặt phòng</a>
                <a href="{{ route('booking.account') }}#payment-section" class="btn btn-brand rounded-3">Xem thanh toán</a>
            </div>
        </div>

        <div class="account-stat-grid">
            <div class="account-stat-card">
                <div class="account-stat-label">Hồ sơ</div>
                <div class="account-stat-value">{{ $thongKe['phan_tram_ho_so'] }}%</div>
                <div class="small text-muted mt-2">Mức độ đầy đủ thông tin khách hàng</div>
            </div>
            <div class="account-stat-card">
                <div class="account-stat-label">Đơn đặt phòng</div>
                <div class="account-stat-value">{{ $thongKe['tong_luot_dat'] }}</div>
                <div class="small text-muted mt-2">{{ $thongKe['don_sap_toi'] }} đơn sắp tới hoặc đang lưu trú</div>
            </div>
            <div class="account-stat-card">
                <div class="account-stat-label">Đã thanh toán</div>
                <div class="account-stat-value text-success">{{ number_format((float) $thongKe['tong_da_thanh_toan'], 0, ',', '.') }} VNĐ</div>
                <div class="small text-muted mt-2">Tổng đã được ghi nhận thành công</div>
            </div>
            <div class="account-stat-card">
                <div class="account-stat-label">Chờ đối soát</div>
                <div class="account-stat-value text-warning">{{ number_format($tongTienChoXuLy, 0, ',', '.') }} VNĐ</div>
                <div class="small text-muted mt-2">Giao dịch đã gửi và đang chờ nội bộ xác nhận</div>
            </div>
            <div class="account-stat-card">
                <div class="account-stat-label">Công nợ còn lại</div>
                <div class="account-stat-value {{ $tongTienConLai > 0 ? 'text-danger' : 'text-success' }}">{{ number_format($tongTienConLai, 0, ',', '.') }} VNĐ</div>
                <div class="small text-muted mt-2">Tổng cần thanh toán trên các hóa đơn</div>
            </div>
        </div>
    </section>

    <div class="row g-4 mb-4 section-anchor" id="customer-info-section">
        <div class="col-xl-5">
            <section class="account-panel">
                <div class="account-panel-title">Thông tin khách hàng</div>
                <div class="account-panel-subtitle">Phần xem thông tin hiện tại của bạn trong hệ thống.</div>

                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="account-chip account-chip--info">{{ \App\Support\HienThiGiaTri::nhanGiaTri($khachHang->hang_khach_hang) }}</span>
                    <span class="account-chip {{ $khachHang->co_du_thong_tin_lien_he ? 'account-chip--success' : 'account-chip--warning' }}">
                        {{ $khachHang->co_du_thong_tin_lien_he ? 'Liên hệ đầy đủ' : 'Cần bổ sung liên hệ' }}
                    </span>
                </div>

                <div class="profile-grid">
                    <div class="profile-item">
                        <div class="profile-item-label">Mã khách hàng</div>
                        <div class="profile-item-value">{{ $khachHang->ma_khach_hang }}</div>
                    </div>
                    <div class="profile-item">
                        <div class="profile-item-label">Tên đăng nhập</div>
                        <div class="profile-item-value">{{ $taiKhoan->ten_dang_nhap }}</div>
                    </div>
                    <div class="profile-item">
                        <div class="profile-item-label">Họ tên</div>
                        <div class="profile-item-value">{{ $khachHang->ho_ten }}</div>
                    </div>
                    <div class="profile-item">
                        <div class="profile-item-label">Email</div>
                        <div class="profile-item-value">{{ $khachHang->email ?: '-' }}</div>
                    </div>
                    <div class="profile-item">
                        <div class="profile-item-label">Số điện thoại</div>
                        <div class="profile-item-value">{{ $khachHang->so_dien_thoai ?: '-' }}</div>
                    </div>
                    <div class="profile-item">
                        <div class="profile-item-label">Ngày sinh</div>
                        <div class="profile-item-value">{{ optional($khachHang->ngay_sinh)->format('d/m/Y') ?? '-' }}</div>
                    </div>
                    <div class="profile-item">
                        <div class="profile-item-label">Quốc tịch</div>
                        <div class="profile-item-value">{{ $khachHang->quoc_tich ?: '-' }}</div>
                    </div>
                    <div class="profile-item">
                        <div class="profile-item-label">Giấy tờ</div>
                        <div class="profile-item-value">
                            {{ $khachHang->loai_giay_to ? strtoupper((string) $khachHang->loai_giay_to) : '-' }}
                            @if($khachHang->so_giay_to)
                                - {{ $khachHang->so_giay_to }}
                            @endif
                        </div>
                    </div>
                    <div class="profile-item" style="grid-column: 1 / -1;">
                        <div class="profile-item-label">Địa chỉ</div>
                        <div class="profile-item-value">{{ $khachHang->dia_chi ?: '-' }}</div>
                    </div>
                    <div class="profile-item" style="grid-column: 1 / -1; border-bottom: 0; padding-bottom: 0;">
                        <div class="profile-item-label">Lần đăng nhập cuối</div>
                        <div class="profile-item-value">{{ optional($taiKhoan->lan_dang_nhap_cuoi)->format('d/m/Y H:i') ?? '-' }}</div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="account-panel-title h6 mb-2">Thông tin cần bổ sung</div>
                @if(empty($khachHang->thieu_ho_so))
                    <div class="alert alert-success mb-0">Hồ sơ của bạn đã tương đối đầy đủ.</div>
                @else
                    <div class="missing-list">
                        @foreach($khachHang->thieu_ho_so as $muc)
                            <span class="missing-tag">{{ $muc }}</span>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>

        <div class="col-xl-7">
            <section class="account-panel">
                <div class="account-panel-title">Sửa và bổ sung thông tin</div>
                <div class="account-panel-subtitle">Bạn có thể cập nhật hồ sơ cá nhân để thủ tục nhận phòng và đối soát thanh toán nhanh hơn.</div>

                <form method="POST" action="{{ route('booking.account.update') }}" class="row g-3">
                    @csrf
                    @method('PATCH')

                    <div class="col-md-6">
                        <label class="form-label">Họ tên</label>
                        <input type="text" name="ho_ten" class="form-control" value="{{ old('ho_ten', $khachHang->ho_ten) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $khachHang->email) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="so_dien_thoai" class="form-control" value="{{ old('so_dien_thoai', $khachHang->so_dien_thoai) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Giới tính</label>
                        <select name="gioi_tinh" class="form-select">
                            <option value="">-- Chọn --</option>
                            @foreach(\App\Models\KhachHang::GIOI_TINH as $giaTri => $nhan)
                                <option value="{{ $giaTri }}" @selected(old('gioi_tinh', $khachHang->gioi_tinh) === $giaTri)>{{ $nhan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Ngày sinh</label>
                        <input type="date" name="ngay_sinh" class="form-control" value="{{ old('ngay_sinh', optional($khachHang->ngay_sinh)->toDateString()) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Quốc tịch</label>
                        <input type="text" name="quoc_tich" class="form-control" value="{{ old('quoc_tich', $khachHang->quoc_tich) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Loại giấy tờ</label>
                        <select name="loai_giay_to" class="form-select">
                            <option value="">-- Chọn --</option>
                            @foreach(\App\Models\KhachHang::LOAI_GIAY_TO as $giaTri => $nhan)
                                <option value="{{ $giaTri }}" @selected(old('loai_giay_to', $khachHang->loai_giay_to) === $giaTri)>{{ $nhan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Số giấy tờ</label>
                        <input type="text" name="so_giay_to" class="form-control" value="{{ old('so_giay_to', $khachHang->so_giay_to) }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Địa chỉ</label>
                        <input type="text" name="dia_chi" class="form-control" value="{{ old('dia_chi', $khachHang->dia_chi) }}">
                    </div>

                    <div class="col-12 d-flex gap-2 flex-wrap">
                        <button type="submit" class="btn btn-brand">Lưu thông tin</button>
                        <a href="{{ route('booking.account') }}#payment-section" class="btn btn-outline-secondary">Xem thanh toán của tôi</a>
                    </div>
                </form>
            </section>
        </div>
    </div>

    <div class="row g-4 section-anchor" id="payment-section">
        <div class="col-xl-8">
            <section class="account-panel">
                <div class="d-flex flex-wrap justify-content-between gap-3 mb-3">
                    <div>
                        <div class="account-panel-title">Thanh toán và hóa đơn của tôi</div>
                        <div class="account-panel-subtitle">Xem từng hóa đơn, số tiền đã thu, phần đang đối soát và công nợ còn lại.</div>
                    </div>
                    <a href="{{ route('booking.account') }}#customer-info-section" class="btn btn-outline-secondary rounded-3">Sửa thông tin</a>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Hóa đơn</th>
                                <th>Đơn đặt phòng</th>
                                <th>Tổng tiền</th>
                                <th>Đã thu</th>
                                <th>Chờ đối soát</th>
                                <th>Còn lại</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($danhSachHoaDon as $hoaDon)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $hoaDon->ma_hoa_don }}</div>
                                        <div class="small text-muted">{{ \App\Support\HienThiGiaTri::nhanGiaTri($hoaDon->trang_thai) }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $hoaDon->datPhong?->ma_dat_phong ?? '-' }}</div>
                                        <div class="small text-muted">
                                            @php $phong = $hoaDon->datPhong?->chiTietDatPhong?->first()?->phong; @endphp
                                            {{ $phong ? 'Phòng ' . $phong->so_phong : 'Không gán phòng' }}
                                        </div>
                                    </td>
                                    <td class="fw-semibold">{{ number_format((float) $hoaDon->tong_tien, 0, ',', '.') }} VNĐ</td>
                                    <td class="text-success fw-semibold">{{ number_format((float) $hoaDon->so_tien_da_thanh_toan, 0, ',', '.') }} VNĐ</td>
                                    <td class="text-warning fw-semibold">{{ number_format((float) $hoaDon->so_tien_cho_xu_ly, 0, ',', '.') }} VNĐ</td>
                                    <td class="fw-semibold {{ (float) $hoaDon->so_tien_con_lai > 0 ? 'text-danger' : 'text-success' }}">
                                        {{ number_format((float) $hoaDon->so_tien_con_lai, 0, ',', '.') }} VNĐ
                                    </td>
                                    <td>
                                        <a href="{{ route('booking.hoa-don.show', $hoaDon) }}" class="btn btn-sm btn-outline-primary rounded-3">
                                            {{ (float) $hoaDon->so_tien_con_lai > 0 || (float) $hoaDon->so_tien_cho_xu_ly > 0 ? 'Xem thanh toán' : 'Xem chi tiết' }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Bạn chưa có hóa đơn nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div class="col-xl-4">
            <section class="account-panel mb-4">
                <div class="account-panel-title">Lịch sử giao dịch gần đây</div>
                <div class="account-panel-subtitle">Theo dõi các giao dịch thanh toán và trạng thái xử lý mới nhất.</div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Mã TT</th>
                                <th>Số tiền</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($danhSachThanhToan as $thanhToan)
                                @php
                                    $mauTrangThai = match ($thanhToan->trang_thai) {
                                        'thanh_cong' => 'text-success',
                                        'cho_xu_ly' => 'text-warning',
                                        'that_bai' => 'text-danger',
                                        default => 'text-muted',
                                    };
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $thanhToan->ma_thanh_toan }}</div>
                                        <div class="small text-muted">{{ $thanhToan->hoaDon?->ma_hoa_don ?? '-' }}</div>
                                    </td>
                                    <td class="fw-semibold">{{ number_format((float) $thanhToan->so_tien, 0, ',', '.') }} VNĐ</td>
                                    <td>
                                        <div class="{{ $mauTrangThai }} fw-semibold">{{ \App\Support\HienThiGiaTri::nhanGiaTri($thanhToan->trang_thai) }}</div>
                                        <div class="small text-muted">{{ optional($thanhToan->thoi_diem_thanh_toan)->format('d/m/Y H:i') ?? '-' }}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">Chưa có giao dịch thanh toán.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="account-panel">
                <div class="account-panel-title">Đơn đặt phòng gần đây</div>
                <div class="account-panel-subtitle">Thông tin lưu trú để đối chiếu với hóa đơn và thanh toán.</div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Mã đơn</th>
                                <th>Lịch ở</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($danhSachDatPhong as $datPhong)
                                <tr>
                                    <td class="fw-semibold">{{ $datPhong->ma_dat_phong }}</td>
                                    <td>
                                        {{ optional($datPhong->ngay_nhan_phong_du_kien)->format('d/m/Y') ?? '-' }}
                                        <div class="small text-muted">{{ optional($datPhong->ngay_tra_phong_du_kien)->format('d/m/Y') ?? '-' }}</div>
                                    </td>
                                    <td>{{ \App\Support\HienThiGiaTri::nhanGiaTri($datPhong->trang_thai) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">Bạn chưa có đơn đặt phòng nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
@endsection
