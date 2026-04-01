@extends('layouts.admin')

@section('title', 'Quản lý đặt phòng')

@section('content')
    <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center mb-4">
        <div>
            <h2 class="section-title">Quản lý đặt phòng</h2>
            <p class="section-subtitle">Xử lý tập trung đơn đặt từ website và đơn tiếp nhận nội bộ.</p>
        </div>

        <a href="{{ route('dat-phong.create') }}" class="btn btn-gradient">
            <i class="fa-solid fa-plus me-2"></i>Tạo đơn đặt phòng
        </a>
    </div>

    <div class="premium-card mb-4">
        <div class="card-body p-4">
            <form method="GET" class="row g-3">
                <div class="col-lg-4">
                    <label class="form-label">Từ khóa</label>
                    <input
                        type="text"
                        name="tu_khoa"
                        class="form-control"
                        value="{{ $tuKhoa }}"
                        placeholder="Mã đơn, tên khách, số điện thoại, email"
                    >
                </div>

                <div class="col-lg-3">
                    <label class="form-label">Trạng thái đơn</label>
                    <select name="trang_thai" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="cho_xac_nhan" @selected($trangThai === 'cho_xac_nhan')>Chờ xác nhận</option>
                        <option value="da_xac_nhan" @selected($trangThai === 'da_xac_nhan')>Đã xác nhận</option>
                        <option value="da_nhan_phong" @selected($trangThai === 'da_nhan_phong')>Đã nhận phòng</option>
                        <option value="da_tra_phong" @selected($trangThai === 'da_tra_phong')>Đã trả phòng</option>
                        <option value="da_huy" @selected($trangThai === 'da_huy')>Đã hủy</option>
                    </select>
                </div>

                <div class="col-lg-3">
                    <label class="form-label">Nguồn đặt</label>
                    <select name="nguon_dat" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="website" @selected($nguonDat === 'website')>Website</option>
                        <option value="truc_tiep" @selected($nguonDat === 'truc_tiep')>Trực tiếp</option>
                        <option value="dien_thoai" @selected($nguonDat === 'dien_thoai')>Điện thoại</option>
                        <option value="zalo" @selected($nguonDat === 'zalo')>Zalo</option>
                        <option value="khac" @selected($nguonDat === 'khac')>Khác</option>
                    </select>
                </div>

                <div class="col-lg-2">
                    <label class="form-label">Từ ngày</label>
                    <input type="date" class="form-control" name="tu_ngay" value="{{ $tuNgay }}">
                </div>

                <div class="col-lg-2">
                    <label class="form-label">Đến ngày</label>
                    <input type="date" class="form-control" name="den_ngay" value="{{ $denNgay }}">
                </div>

                <div class="col-lg-2 d-flex align-items-end">
                    <button class="btn btn-gradient w-100" type="submit">Lọc dữ liệu</button>
                </div>

                <div class="col-lg-2 d-flex align-items-end">
                    <a href="{{ route('dat-phong.index') }}" class="btn btn-soft w-100">Đặt lại</a>
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
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Phòng</th>
                            <th>Lịch lưu trú</th>
                            <th>Nguồn đặt</th>
                            <th>Trạng thái</th>
                            <th style="min-width: 230px;">Xử lý</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($danhSachDatPhong as $datPhong)
                            @php
                                $chiTietDauTien = $datPhong->chiTietDatPhong->first();
                                $phong = $chiTietDauTien?->phong;

                                $mapTrangThai = [
                                    'cho_xac_nhan' => 'chip chip-warning',
                                    'da_xac_nhan' => 'chip chip-info',
                                    'da_nhan_phong' => 'chip chip-neutral',
                                    'da_tra_phong' => 'chip chip-success',
                                    'da_huy' => 'chip chip-danger',
                                ];
                                $chip = $mapTrangThai[$datPhong->trang_thai] ?? 'chip chip-neutral';
                            @endphp

                            <tr>
                                <td class="fw-bold">{{ $datPhong->ma_dat_phong }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $datPhong->khachHang?->ho_ten ?? 'Khách lẻ' }}</div>
                                    <div class="small text-muted">{{ $datPhong->khachHang?->so_dien_thoai ?? '-' }}</div>
                                    <div class="small text-muted">{{ $datPhong->khachHang?->email ?? '-' }}</div>
                                </td>
                                <td>
                                    @if($phong)
                                        <div class="fw-semibold">Phòng {{ $phong->so_phong }}</div>
                                        <div class="small text-muted">Tầng {{ $phong->tang ?? '-' }}</div>
                                    @else
                                        <span class="text-muted">Chưa có chi tiết</span>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ optional($datPhong->ngay_nhan_phong_du_kien)->format('d/m/Y') }}</div>
                                    <div class="small text-muted">đến {{ optional($datPhong->ngay_tra_phong_du_kien)->format('d/m/Y') }}</div>
                                </td>
                                <td>
                                    @if($datPhong->nguon_dat === 'website')
                                        <span class="chip chip-info">Website</span>
                                    @else
                                        <span class="chip chip-neutral">{{ \App\Support\HienThiGiaTri::nhanGiaTri($datPhong->nguon_dat ?? 'truc_tiep') }}</span>
                                    @endif
                                </td>
                                <td><span class="{{ $chip }}">{{ \App\Support\HienThiGiaTri::nhanGiaTri($datPhong->trang_thai) }}</span></td>
                                <td>
                                    <form method="POST" action="{{ route('dat-phong.cap-nhat-trang-thai', $datPhong) }}" class="d-flex gap-2">
                                        @csrf
                                        @method('PATCH')

                                        <select name="trang_thai" class="form-select form-select-sm">
                                            <option value="cho_xac_nhan" @selected($datPhong->trang_thai === 'cho_xac_nhan')>Chờ xác nhận</option>
                                            <option value="da_xac_nhan" @selected($datPhong->trang_thai === 'da_xac_nhan')>Đã xác nhận</option>
                                            <option value="da_nhan_phong" @selected($datPhong->trang_thai === 'da_nhan_phong')>Đã nhận phòng</option>
                                            <option value="da_tra_phong" @selected($datPhong->trang_thai === 'da_tra_phong')>Đã trả phòng</option>
                                            <option value="da_huy" @selected($datPhong->trang_thai === 'da_huy')>Đã hủy</option>
                                        </select>

                                        <button type="submit" class="btn btn-outline-primary btn-sm">Lưu</button>
                                    </form>

                                    <a href="{{ route('dat-phong.show', $datPhong) }}" class="btn btn-outline-secondary btn-sm mt-2">Xem chi tiết</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Chưa có đơn đặt phòng theo bộ lọc hiện tại.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">{{ $danhSachDatPhong->links() }}</div>
        </div>
    </div>
@endsection
