@extends('layouts.admin')

@section('title', 'Quản lý đặt phòng')

@push('styles')
    <style>
        .booking-toolbar {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
        }

        .booking-code {
            font-size: 0.98rem;
            font-weight: 700;
            color: #12304d;
        }

        .table-subtext {
            color: #6b8298;
            font-size: 0.83rem;
        }

        .action-panel {
            min-width: 220px;
        }

        .action-title {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #6b8298;
            margin-bottom: 10px;
        }

        .action-stack {
            display: grid;
            gap: 8px;
        }

        .action-empty {
            padding: 12px 14px;
            border: 1px dashed #cad8e6;
            border-radius: 14px;
            background: #fbfdff;
            color: #68839f;
            font-size: 0.84rem;
            font-weight: 600;
        }

        .empty-state {
            border: 1px dashed #cad8e6;
            border-radius: 18px;
            padding: 24px;
            text-align: center;
            color: #68839f;
            background: #fbfdff;
        }

        .booking-note {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: #eef8f6;
            color: #0f5f58;
            font-size: 0.84rem;
            font-weight: 600;
        }
    </style>
@endpush

@section('content')
    @php
        $mapTrangThai = [
            'cho_xac_nhan' => 'chip chip-warning',
            'da_xac_nhan' => 'chip chip-info',
            'da_nhan_phong' => 'chip chip-neutral',
            'da_tra_phong' => 'chip chip-success',
            'da_huy' => 'chip chip-danger',
        ];
        $hanhDongTheoTrangThai = [
            'cho_xac_nhan' => [
                ['trang_thai' => 'da_xac_nhan', 'label' => 'Xac nhan don', 'class' => 'btn-gradient'],
                ['trang_thai' => 'da_huy', 'label' => 'Hủy đơn', 'class' => 'btn-outline-danger'],
            ],
            'da_xac_nhan' => [
                ['trang_thai' => 'da_nhan_phong', 'label' => 'Nhan phong', 'class' => 'btn-gradient'],
                ['trang_thai' => 'da_huy', 'label' => 'Hủy đơn', 'class' => 'btn-outline-danger'],
            ],
            'da_nhan_phong' => [
                ['trang_thai' => 'da_tra_phong', 'label' => 'Tra phong', 'class' => 'btn-gradient'],
            ],
            'da_tra_phong' => [],
            'da_huy' => [],
        ];
        $tieuDeXuLyTheoTrangThai = [
            'cho_xac_nhan' => 'Xu ly tiep theo',
            'da_xac_nhan' => 'Xu ly tiep theo',
            'da_nhan_phong' => 'Xu ly tiep theo',
            'da_tra_phong' => 'Hoan tat',
            'da_huy' => 'Da dung',
        ];
    @endphp

    <div class="booking-toolbar mb-4">
        <div>
            <h2 class="section-title">Quản lý đặt phòng</h2>
            <p class="section-subtitle">Tập trung xử lý danh sách đơn, cập nhật trạng thái và theo dõi hóa đơn liên quan.</p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('dat-phong.create') }}" class="btn btn-gradient">
                <i class="fa-solid fa-plus me-2"></i>Tạo đơn đặt phòng
            </a>
        </div>
    </div>

    <div class="premium-card mb-4">
        <div class="card-body p-4">
            <form method="GET" class="row g-3">
                <div class="col-xl-4">
                    <label class="form-label">Từ khóa</label>
                    <input
                        type="text"
                        name="tu_khoa"
                        class="form-control"
                        value="{{ $tuKhoa }}"
                        placeholder="Mã đơn, tên khách, số điện thoại, email"
                    >
                </div>

                <div class="col-xl-2 col-md-4">
                    <label class="form-label">Trạng thái</label>
                    <select name="trang_thai" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="cho_xac_nhan" @selected($trangThai === 'cho_xac_nhan')>Chờ xác nhận</option>
                        <option value="da_xac_nhan" @selected($trangThai === 'da_xac_nhan')>Đã xác nhận</option>
                        <option value="da_nhan_phong" @selected($trangThai === 'da_nhan_phong')>Đã nhận phòng</option>
                        <option value="da_tra_phong" @selected($trangThai === 'da_tra_phong')>Đã trả phòng</option>
                        <option value="da_huy" @selected($trangThai === 'da_huy')>Đã hủy</option>
                    </select>
                </div>

                <div class="col-xl-2 col-md-4">
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

                <div class="col-xl-2 col-md-4">
                    <label class="form-label">Từ ngày</label>
                    <input type="date" class="form-control" name="tu_ngay" value="{{ $tuNgay }}">
                </div>

                <div class="col-xl-2 col-md-4">
                    <label class="form-label">Đến ngày</label>
                    <input type="date" class="form-control" name="den_ngay" value="{{ $denNgay }}">
                </div>

                <div class="col-xl-2 col-md-6 d-flex align-items-end">
                    <button class="btn btn-gradient w-100" type="submit">Lọc</button>
                </div>

                <div class="col-xl-2 col-md-6 d-flex align-items-end">
                    <a href="{{ route('dat-phong.index') }}" class="btn btn-soft w-100">Đặt lại</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-label">Tổng đơn</div>
                <div class="metric-value">{{ $thongKe['tong_don'] }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-label">Chờ xác nhận</div>
                <div class="metric-value text-warning">{{ $thongKe['cho_xac_nhan'] }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-label">Đang lưu trú</div>
                <div class="metric-value text-primary">{{ $thongKe['dang_luu_tru'] }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-label">Cần xử lý ngay</div>
                <div class="metric-value text-danger">{{ $thongKe['can_xu_ly_ngay'] }}</div>
            </div>
        </div>
    </div>

    @if(false && $thongKe['can_xu_ly_ngay'] > 0)
        <div class="mb-4">
            <span class="booking-note">
                <i class="fa-solid fa-bell"></i>{{ $thongKe['can_xu_ly_ngay'] }} đơn đang cần ưu tiên xử lý trong danh sách này.
            </span>
        </div>
    @endif

    <div class="premium-card">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                <div>
                    <h5 class="fw-bold mb-1">Danh sách đơn đặt phòng</h5>
                    <div class="text-muted small">Theo dõi, cập nhật trạng thái và mở chi tiết khi cần xử lý sâu hơn.</div>
                </div>
                <div class="text-end">
                    <div class="small text-muted">Doanh thu tạm tính</div>
                    <div class="fw-bold">{{ number_format((float) $thongKe['tong_doanh_thu_tam_tinh'], 0, ',', '.') }} VNĐ</div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Đơn và khách</th>
                            <th>Lịch ở</th>
                            <th>Phòng</th>
                            <th>Trạng thái</th>
                            <th>Tiền và hóa đơn</th>
                            <th style="min-width: 250px;">Xử lý</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($danhSachDatPhong as $datPhong)
                            @php
                                $chiTietDauTien = $datPhong->chiTietDatPhong->first();
                                $phong = $chiTietDauTien?->phong;
                                $chip = $mapTrangThai[$datPhong->trang_thai] ?? 'chip chip-neutral';
                                $hanhDongXuLy = $hanhDongTheoTrangThai[$datPhong->trang_thai] ?? [];
                            @endphp

                            <tr>
                                <td style="min-width: 240px;">
                                    <div class="booking-code">{{ $datPhong->ma_dat_phong }}</div>
                                    <div class="fw-semibold">{{ $datPhong->khachHang?->ho_ten ?? 'Khách lẻ' }}</div>
                                    <div class="table-subtext">{{ $datPhong->khachHang?->so_dien_thoai ?? '-' }} • {{ $datPhong->khachHang?->email ?? '-' }}</div>
                                </td>

                                <td style="min-width: 180px;">
                                    <div class="fw-semibold">
                                        {{ optional($datPhong->ngay_nhan_phong_du_kien)->format('d/m/Y') ?? '-' }}
                                        -
                                        {{ optional($datPhong->ngay_tra_phong_du_kien)->format('d/m/Y') ?? '-' }}
                                    </div>
                                    <div class="table-subtext">{{ $datPhong->tong_so_dem }} đêm • {{ \App\Support\HienThiGiaTri::nhanGiaTri($datPhong->nguon_dat ?? 'truc_tiep') }}</div>
                                </td>

                                <td style="min-width: 210px;">
                                    @if($phong)
                                        <div class="fw-semibold">Phòng {{ $phong->so_phong }}</div>
                                        <div class="table-subtext">{{ $phong->loaiPhong?->ten_loai_phong ?? 'Không rõ loại' }} • Tầng {{ $phong->tang ?? '-' }}</div>
                                    @else
                                        <div class="fw-semibold">Chưa có chi tiết phòng</div>
                                    @endif
                                    <div class="table-subtext">{{ (int) $datPhong->so_nguoi_lon }} người lớn • {{ (int) $datPhong->so_tre_em }} trẻ em</div>
                                </td>

                                <td style="min-width: 220px;">
                                    <div class="mb-2"><span class="{{ $chip }}">{{ \App\Support\HienThiGiaTri::nhanGiaTri($datPhong->trang_thai) }}</span></div>
                                    @if(false && $datPhong->can_xu_ly_ngay)
                                        <div class="mb-2"><span class="chip chip-danger">Cần xử lý</span></div>
                                    @endif
                                    <div class="table-subtext">{{ $datPhong->ghi_chu_van_hanh }}</div>
                                </td>

                                <td style="min-width: 220px;">
                                    <div class="fw-semibold">{{ number_format((float) $datPhong->tong_tien_tam_tinh, 0, ',', '.') }} VNĐ</div>
                                    @if($datPhong->hoa_don_hien_tai)
                                        <div class="table-subtext">Hóa đơn {{ $datPhong->hoa_don_hien_tai->ma_hoa_don }}</div>
                                        <div class="table-subtext">Còn thu {{ number_format((float) $datPhong->so_tien_con_lai_hoa_don, 0, ',', '.') }} VNĐ</div>
                                    @else
                                        <div class="table-subtext">Chưa có hóa đơn</div>
                                    @endif
                                </td>

                                <td>
                                    <div class="action-panel">
                                        <div class="action-title">{{ $tieuDeXuLyTheoTrangThai[$datPhong->trang_thai] ?? 'Xu ly' }}</div>

                                        @if($hanhDongXuLy !== [])
                                            <div class="action-stack">
                                                @foreach($hanhDongXuLy as $hanhDong)
                                                    <form method="POST" action="{{ route('dat-phong.cap-nhat-trang-thai', $datPhong) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="trang_thai" value="{{ $hanhDong['trang_thai'] }}">
                                                        <button type="submit" class="btn btn-sm w-100 {{ $hanhDong['class'] }}">
                                                            {{ $hanhDong['label'] }}
                                                        </button>
                                                    </form>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="action-empty">
                                                {{ $datPhong->trang_thai === 'da_huy' ? 'Don da huy, khong con buoc xu ly.' : 'Don da hoan tat, chi can xem chi tiet hoac hoa don.' }}
                                            </div>
                                        @endif
                                    </div>

                                    <form method="POST" action="{{ route('dat-phong.cap-nhat-trang-thai', $datPhong) }}" class="status-form d-none">
                                        @csrf
                                        @method('PATCH')

                                        <div class="d-flex gap-2">
                                            <select name="trang_thai" class="form-select form-select-sm">
                                                <option value="cho_xac_nhan" @selected($datPhong->trang_thai === 'cho_xac_nhan')>Chờ xác nhận</option>
                                                <option value="da_xac_nhan" @selected($datPhong->trang_thai === 'da_xac_nhan')>Đã xác nhận</option>
                                                <option value="da_nhan_phong" @selected($datPhong->trang_thai === 'da_nhan_phong')>Đã nhận phòng</option>
                                                <option value="da_tra_phong" @selected($datPhong->trang_thai === 'da_tra_phong')>Đã trả phòng</option>
                                                <option value="da_huy" @selected($datPhong->trang_thai === 'da_huy')>Đã hủy</option>
                                            </select>
                                            <button type="submit" class="btn btn-outline-primary btn-sm">Lưu</button>
                                        </div>
                                    </form>

                                    <div class="d-flex gap-2 mt-2 flex-wrap">
                                        <a href="{{ route('dat-phong.show', $datPhong) }}" class="btn btn-outline-secondary btn-sm">Xem chi tiết</a>
                                        @if($datPhong->hoa_don_hien_tai)
                                            <a href="{{ route('hoa-don.show', $datPhong->hoa_don_hien_tai) }}" class="btn btn-outline-success btn-sm">Hóa đơn</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-4">
                                    <div class="empty-state">Chưa có đơn đặt phòng theo bộ lọc hiện tại.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">{{ $danhSachDatPhong->links() }}</div>
        </div>
    </div>
@endsection
