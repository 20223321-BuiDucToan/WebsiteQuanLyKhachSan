@extends('layouts.admin')

@section('title', 'Quản lý hóa đơn')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="section-title">Quản lý hóa đơn</h2>
            <p class="section-subtitle">Theo dõi doanh thu, công nợ và tiến độ thanh toán theo từng đơn đặt phòng.</p>
        </div>

        <a href="{{ route('hoa-don.create') }}" class="btn btn-gradient">
            <i class="fa-solid fa-plus me-2"></i>Tạo hóa đơn
        </a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-label">Tổng hóa đơn</div>
                <div class="metric-value">{{ $thongKe['tong'] }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-label">Chưa thanh toán</div>
                <div class="metric-value text-danger">{{ $thongKe['chua_thanh_toan'] }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-label">Thanh toán một phần</div>
                <div class="metric-value text-warning">{{ $thongKe['thanh_toan_mot_phan'] }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-label">Đã thanh toán</div>
                <div class="metric-value text-success">{{ $thongKe['da_thanh_toan'] }}</div>
            </div>
        </div>
    </div>

    <div class="premium-card mb-4">
        <div class="card-body p-4">
            <form method="GET" class="row g-3">
                <div class="col-lg-4">
                    <label class="form-label">Từ khóa</label>
                    <input type="text" name="tu_khoa" class="form-control" value="{{ $tuKhoa }}" placeholder="Mã hóa đơn, mã đặt phòng, tên khách">
                </div>

                <div class="col-lg-2">
                    <label class="form-label">Trạng thái</label>
                    <select name="trang_thai" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="chua_thanh_toan" @selected($trangThai === 'chua_thanh_toan')>Chưa thanh toán</option>
                        <option value="thanh_toan_mot_phan" @selected($trangThai === 'thanh_toan_mot_phan')>Thanh toán một phần</option>
                        <option value="da_thanh_toan" @selected($trangThai === 'da_thanh_toan')>Đã thanh toán</option>
                        <option value="da_huy" @selected($trangThai === 'da_huy')>Đã hủy</option>
                    </select>
                </div>

                <div class="col-lg-2">
                    <label class="form-label">Từ ngày</label>
                    <input type="date" name="tu_ngay" class="form-control" value="{{ $tuNgay }}">
                </div>

                <div class="col-lg-2">
                    <label class="form-label">Đến ngày</label>
                    <input type="date" name="den_ngay" class="form-control" value="{{ $denNgay }}">
                </div>

                <div class="col-lg-1 d-flex align-items-end">
                    <button class="btn btn-gradient w-100">Lọc</button>
                </div>

                <div class="col-lg-1 d-flex align-items-end">
                    <a href="{{ route('hoa-don.index') }}" class="btn btn-soft w-100">Đặt lại</a>
                </div>
            </form>
        </div>
    </div>

    <div class="premium-card">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Mã hóa đơn</th>
                            <th>Đơn đặt phòng</th>
                            <th>Khách hàng</th>
                            <th>Tổng tiền</th>
                            <th>Đã thu</th>
                            <th>Còn lại</th>
                            <th>Trạng thái</th>
                            <th>Xuất lúc</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($danhSachHoaDon as $hoaDon)
                            @php
                                $khachHang = $hoaDon->datPhong?->khachHang;
                                $soTienDaThu = (float) $hoaDon->thanhToan->where('trang_thai', 'thanh_cong')->sum('so_tien');
                                $soTienConLai = max(0, (float) $hoaDon->tong_tien - $soTienDaThu);

                                $mapTrangThai = [
                                    'chua_thanh_toan' => 'chip chip-danger',
                                    'thanh_toan_mot_phan' => 'chip chip-warning',
                                    'da_thanh_toan' => 'chip chip-success',
                                    'da_huy' => 'chip chip-neutral',
                                ];
                                $chip = $mapTrangThai[$hoaDon->trang_thai] ?? 'chip chip-neutral';
                            @endphp

                            <tr>
                                <td class="fw-semibold">{{ $hoaDon->ma_hoa_don }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $hoaDon->datPhong?->ma_dat_phong ?? '-' }}</div>
                                    <div class="small text-muted">{{ optional($hoaDon->datPhong?->ngay_nhan_phong_du_kien)->format('d/m/Y') ?? '-' }} - {{ optional($hoaDon->datPhong?->ngay_tra_phong_du_kien)->format('d/m/Y') ?? '-' }}</div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $khachHang?->ho_ten ?? '-' }}</div>
                                    <div class="small text-muted">{{ $khachHang?->so_dien_thoai ?? '-' }}</div>
                                </td>
                                <td class="fw-semibold">{{ number_format((float) $hoaDon->tong_tien, 0, ',', '.') }} VNĐ</td>
                                <td>{{ number_format($soTienDaThu, 0, ',', '.') }} VNĐ</td>
                                <td class="fw-semibold {{ $soTienConLai > 0 ? 'text-danger' : 'text-success' }}">{{ number_format($soTienConLai, 0, ',', '.') }} VNĐ</td>
                                <td><span class="{{ $chip }}">{{ \App\Support\HienThiGiaTri::nhanGiaTri($hoaDon->trang_thai) }}</span></td>
                                <td>{{ optional($hoaDon->thoi_diem_xuat)->format('d/m/Y H:i') ?? '-' }}</td>
                                <td class="text-end"><a href="{{ route('hoa-don.show', $hoaDon) }}" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-eye"></i></a></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">Chưa có hóa đơn theo bộ lọc hiện tại.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">{{ $danhSachHoaDon->links() }}</div>
        </div>
    </div>
@endsection
