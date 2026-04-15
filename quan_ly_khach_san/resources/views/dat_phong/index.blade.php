@extends('layouts.admin')

@section('title', 'Quản lý đặt phòng')

@push('styles')
    <style>
        .booking-hero {
            border: none;
            background:
                radial-gradient(circle at top right, rgba(255, 255, 255, 0.24), transparent 34%),
                linear-gradient(135deg, #173652, #0f766e 58%, #22c55e);
            color: #fff;
        }

        .booking-hero .section-title,
        .booking-hero .section-subtitle {
            color: #fff;
        }

        .booking-hero .section-subtitle {
            opacity: 0.82;
        }

        .hero-stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 16px;
            margin-top: 24px;
        }

        .hero-stat-card {
            border-radius: 18px;
            padding: 16px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.16);
            backdrop-filter: blur(10px);
        }

        .hero-stat-label {
            font-size: 0.76rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            opacity: 0.84;
        }

        .hero-stat-value {
            margin-top: 6px;
            font-size: 1.55rem;
            line-height: 1.1;
            font-weight: 800;
        }

        .hero-stat-note {
            margin-top: 6px;
            font-size: 0.82rem;
            opacity: 0.78;
        }

        .quick-filter {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            border: 1px solid #d5e3ef;
            background: #f8fbff;
            color: #35516d;
            font-size: 0.84rem;
            font-weight: 600;
        }

        .quick-filter:hover {
            background: #eef7ff;
            color: #14314d;
        }

        .active-filter-chip {
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

        .booking-code {
            font-size: 0.98rem;
            font-weight: 700;
            color: #12304d;
        }

        .table-subtext {
            color: #6b8298;
            font-size: 0.83rem;
        }

        .highlight-list {
            display: grid;
            gap: 12px;
        }

        .highlight-item {
            display: block;
            padding: 14px;
            border-radius: 16px;
            border: 1px solid #e3ebf3;
            background: #fbfdff;
            color: inherit;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .highlight-item:hover {
            transform: translateY(-2px);
            border-color: #c7dae9;
            box-shadow: 0 12px 24px rgba(15, 41, 68, 0.08);
        }

        .highlight-item--urgent {
            border-color: #fecdd3;
            background: linear-gradient(180deg, #fff7f7, #ffffff);
        }

        .metric-mini {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #edf2f7;
        }

        .metric-mini:last-child {
            padding-bottom: 0;
            border-bottom: none;
        }

        .metric-mini strong {
            font-size: 1.08rem;
        }

        .empty-state {
            border: 1px dashed #cad8e6;
            border-radius: 18px;
            padding: 24px;
            text-align: center;
            color: #68839f;
            background: #fbfdff;
        }

        .status-form {
            min-width: 220px;
        }

        @media (max-width: 767px) {
            .hero-stat-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $quickFilters = [
            [
                'label' => 'Nhận hôm nay',
                'query' => array_filter(array_merge(request()->only('tu_khoa', 'trang_thai', 'nguon_dat'), [
                    'tu_ngay' => now()->toDateString(),
                    'den_ngay' => now()->toDateString(),
                ])),
            ],
            [
                'label' => 'Chờ xác nhận',
                'query' => array_filter(array_merge(request()->only('tu_khoa', 'nguon_dat', 'tu_ngay', 'den_ngay'), [
                    'trang_thai' => 'cho_xac_nhan',
                ])),
            ],
            [
                'label' => 'Đang lưu trú',
                'query' => array_filter(array_merge(request()->only('tu_khoa', 'nguon_dat', 'tu_ngay', 'den_ngay'), [
                    'trang_thai' => 'da_nhan_phong',
                ])),
            ],
            [
                'label' => 'Nguồn website',
                'query' => array_filter(array_merge(request()->only('tu_khoa', 'trang_thai', 'tu_ngay', 'den_ngay'), [
                    'nguon_dat' => 'website',
                ])),
            ],
        ];

        $coBoLoc = filled($tuKhoa) || filled($trangThai) || filled($nguonDat) || filled($tuNgay) || filled($denNgay);
        $tongDon = max(1, $thongKe['tong_don']);

        $tongHopVanHanh = [
            ['label' => 'Chờ xác nhận', 'value' => $thongKe['cho_xac_nhan'], 'class' => 'chip chip-warning'],
            ['label' => 'Đang lưu trú', 'value' => $thongKe['dang_luu_tru'], 'class' => 'chip chip-info'],
            ['label' => 'Nhận hôm nay', 'value' => $thongKe['nhan_phong_hom_nay'], 'class' => 'chip chip-success'],
            ['label' => 'Sắp trả phòng', 'value' => $thongKe['sap_tra_phong'], 'class' => 'chip chip-neutral'],
        ];

        $mapTrangThai = [
            'cho_xac_nhan' => 'chip chip-warning',
            'da_xac_nhan' => 'chip chip-info',
            'da_nhan_phong' => 'chip chip-neutral',
            'da_tra_phong' => 'chip chip-success',
            'da_huy' => 'chip chip-danger',
        ];
    @endphp

    <div class="premium-card booking-hero mb-4">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex flex-wrap gap-3 justify-content-between align-items-start">
                <div>
                    <h2 class="section-title">Quản lý đặt phòng</h2>
                    <p class="section-subtitle">Theo dõi toàn bộ hành trình từ xác nhận, nhận phòng, lưu trú đến trả phòng và phát sinh hóa đơn.</p>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('khach-hang.index') }}" class="btn btn-light fw-semibold">
                        <i class="fa-solid fa-users me-2"></i>Khách hàng
                    </a>
                    <a href="{{ route('dat-phong.create') }}" class="btn btn-outline-light fw-semibold">
                        <i class="fa-solid fa-plus me-2"></i>Tạo đơn đặt phòng
                    </a>
                </div>
            </div>

            <div class="hero-stat-grid">
                <div class="hero-stat-card">
                    <div class="hero-stat-label">Tổng đơn trong bộ lọc</div>
                    <div class="hero-stat-value">{{ $thongKe['tong_don'] }}</div>
                    <div class="hero-stat-note">{{ $thongKe['can_xu_ly_ngay'] }} đơn cần xử lý ngay</div>
                </div>
                <div class="hero-stat-card">
                    <div class="hero-stat-label">Doanh thu tạm tính</div>
                    <div class="hero-stat-value">{{ number_format((float) $thongKe['tong_doanh_thu_tam_tinh'], 0, ',', '.') }}</div>
                    <div class="hero-stat-note">{{ $thongKe['co_hoa_don'] }} đơn đã có hóa đơn liên quan</div>
                </div>
                <div class="hero-stat-card">
                    <div class="hero-stat-label">Đang lưu trú</div>
                    <div class="hero-stat-value">{{ $thongKe['dang_luu_tru'] }}</div>
                    <div class="hero-stat-note">{{ $thongKe['sap_tra_phong'] }} đơn sắp trả phòng</div>
                </div>
                <div class="hero-stat-card">
                    <div class="hero-stat-label">Nguồn website</div>
                    <div class="hero-stat-value">{{ $thongKe['website'] }}</div>
                    <div class="hero-stat-note">{{ $thongKe['da_tra_phong'] }} đơn đã hoàn tất lưu trú</div>
                </div>
            </div>
        </div>
    </div>

    <div class="premium-card mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap gap-2 mb-3">
                @foreach($quickFilters as $filter)
                    <a href="{{ route('dat-phong.index', $filter['query']) }}" class="quick-filter">
                        <i class="fa-solid fa-filter"></i>{{ $filter['label'] }}
                    </a>
                @endforeach
            </div>

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

                <div class="col-xl-1 col-md-6 d-flex align-items-end">
                    <button class="btn btn-gradient w-100" type="submit">Lọc</button>
                </div>

                <div class="col-xl-1 col-md-6 d-flex align-items-end">
                    <a href="{{ route('dat-phong.index') }}" class="btn btn-soft w-100">Đặt lại</a>
                </div>
            </form>

            @if($coBoLoc)
                <div class="d-flex flex-wrap gap-2 mt-3 pt-3 border-top">
                    @if($tuKhoa)
                        <span class="active-filter-chip"><i class="fa-solid fa-magnifying-glass"></i>{{ $tuKhoa }}</span>
                    @endif
                    @if($trangThai)
                        <span class="active-filter-chip"><i class="fa-solid fa-circle-dot"></i>{{ \App\Support\HienThiGiaTri::nhanGiaTri($trangThai) }}</span>
                    @endif
                    @if($nguonDat)
                        <span class="active-filter-chip"><i class="fa-solid fa-share-nodes"></i>{{ \App\Support\HienThiGiaTri::nhanGiaTri($nguonDat) }}</span>
                    @endif
                    @if($tuNgay || $denNgay)
                        <span class="active-filter-chip"><i class="fa-regular fa-calendar"></i>{{ $tuNgay ?: '...' }} - {{ $denNgay ?: '...' }}</span>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xxl-8">
            <div class="premium-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                        <div>
                            <h5 class="fw-bold mb-1">Danh sách đơn đặt phòng</h5>
                            <div class="text-muted small">
                                {{ $thongKe['tong_don'] }} đơn
                                • Doanh thu tạm tính {{ number_format((float) $thongKe['tong_doanh_thu_tam_tinh'], 0, ',', '.') }} VNĐ
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="small text-muted">Đơn cần xử lý ngay</div>
                            <div class="fw-bold">{{ number_format(($thongKe['can_xu_ly_ngay'] / $tongDon) * 100, 1, ',', '.') }}%</div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Đơn và khách</th>
                                    <th>Lưu trú</th>
                                    <th>Phòng và khách lưu trú</th>
                                    <th>Giá trị và hóa đơn</th>
                                    <th>Trạng thái</th>
                                    <th style="min-width: 250px;">Xử lý</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($danhSachDatPhong as $datPhong)
                                    @php
                                        $chiTietDauTien = $datPhong->chiTietDatPhong->first();
                                        $phong = $chiTietDauTien?->phong;
                                        $chip = $mapTrangThai[$datPhong->trang_thai] ?? 'chip chip-neutral';
                                        $chipUuTien = $datPhong->muc_do_uu_tien === 'cao'
                                            ? 'chip chip-danger'
                                            : ($datPhong->muc_do_uu_tien === 'trung_binh' ? 'chip chip-warning' : 'chip chip-neutral');
                                    @endphp

                                    <tr>
                                        <td style="min-width: 230px;">
                                            <div class="booking-code">{{ $datPhong->ma_dat_phong }}</div>
                                            <div class="fw-semibold">{{ $datPhong->khachHang?->ho_ten ?? 'Khách lẻ' }}</div>
                                            <div class="table-subtext">{{ $datPhong->khachHang?->so_dien_thoai ?? '-' }} • {{ $datPhong->khachHang?->email ?? '-' }}</div>
                                        </td>
                                        <td style="min-width: 170px;">
                                            <div class="fw-semibold">
                                                {{ optional($datPhong->ngay_nhan_phong_du_kien)->format('d/m/Y') ?? '-' }}
                                                -
                                                {{ optional($datPhong->ngay_tra_phong_du_kien)->format('d/m/Y') ?? '-' }}
                                            </div>
                                            <div class="table-subtext">{{ $datPhong->tong_so_dem }} đêm • {{ \App\Support\HienThiGiaTri::nhanGiaTri($datPhong->nguon_dat ?? 'truc_tiep') }}</div>
                                        </td>
                                        <td style="min-width: 220px;">
                                            @if($phong)
                                                <div class="fw-semibold">Phòng {{ $phong->so_phong }} - {{ $phong->loaiPhong?->ten_loai_phong ?? 'Không rõ loại' }}</div>
                                                <div class="table-subtext">Tầng {{ $phong->tang ?? '-' }} • {{ $datPhong->tong_so_phong }} phòng</div>
                                            @else
                                                <div class="fw-semibold">Chưa có chi tiết phòng</div>
                                            @endif
                                            <div class="table-subtext">{{ (int) $datPhong->so_nguoi_lon }} người lớn • {{ (int) $datPhong->so_tre_em }} trẻ em</div>
                                        </td>
                                        <td style="min-width: 210px;">
                                            <div class="fw-semibold">{{ number_format((float) $datPhong->tong_tien_tam_tinh, 0, ',', '.') }} VNĐ</div>
                                            @if($datPhong->hoa_don_hien_tai)
                                                <div class="table-subtext">Hóa đơn {{ $datPhong->hoa_don_hien_tai->ma_hoa_don }}</div>
                                                <div class="table-subtext">Còn thu {{ number_format((float) $datPhong->so_tien_con_lai_hoa_don, 0, ',', '.') }} VNĐ</div>
                                            @else
                                                <div class="table-subtext">Chưa phát sinh hóa đơn</div>
                                            @endif
                                        </td>
                                        <td style="min-width: 220px;">
                                            <div class="mb-2"><span class="{{ $chip }}">{{ \App\Support\HienThiGiaTri::nhanGiaTri($datPhong->trang_thai) }}</span></div>
                                            <div class="mb-2"><span class="{{ $chipUuTien }}">{{ ucfirst($datPhong->muc_do_uu_tien) }}</span></div>
                                            <div class="table-subtext">{{ $datPhong->ghi_chu_van_hanh }}</div>
                                        </td>
                                        <td>
                                            <form method="POST" action="{{ route('dat-phong.cap-nhat-trang-thai', $datPhong) }}" class="status-form">
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

                                            <div class="d-flex gap-2 mt-2">
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
        </div>

        <div class="col-xxl-4">
            <div class="premium-card mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">Đơn cần chú ý</h5>
                            <p class="text-muted small mb-0">Ưu tiên xác nhận, nhận phòng hôm nay hoặc xử lý quá hạn.</p>
                        </div>
                        <span class="chip chip-danger">{{ $datPhongCanChuY->count() }} đơn</span>
                    </div>

                    <div class="highlight-list">
                        @forelse($datPhongCanChuY as $datPhong)
                            <a href="{{ route('dat-phong.show', $datPhong) }}" class="highlight-item {{ $datPhong->muc_do_uu_tien === 'cao' ? 'highlight-item--urgent' : '' }}">
                                <div class="d-flex justify-content-between align-items-start gap-3">
                                    <div>
                                        <div class="fw-bold">{{ $datPhong->ma_dat_phong }}</div>
                                        <div class="table-subtext">{{ $datPhong->khachHang?->ho_ten ?? 'Khách lẻ' }}</div>
                                    </div>
                                    <span class="{{ $datPhong->muc_do_uu_tien === 'cao' ? 'chip chip-danger' : 'chip chip-warning' }}">
                                        {{ ucfirst($datPhong->muc_do_uu_tien) }}
                                    </span>
                                </div>

                                <div class="fw-semibold mt-3">{{ number_format((float) $datPhong->tong_tien_tam_tinh, 0, ',', '.') }} VNĐ</div>
                                <div class="table-subtext mt-1">{{ $datPhong->ghi_chu_van_hanh }}</div>
                            </a>
                        @empty
                            <div class="empty-state">Hiện chưa có đơn cần ưu tiên xử lý.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="premium-card">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Tổng quan vận hành</h5>

                    @foreach($tongHopVanHanh as $item)
                        <div class="metric-mini">
                            <div>
                                <div class="fw-semibold">{{ $item['label'] }}</div>
                                <div class="table-subtext">{{ number_format(($item['value'] / $tongDon) * 100, 1, ',', '.') }}% danh sách</div>
                            </div>
                            <div class="text-end">
                                <strong>{{ $item['value'] }}</strong>
                                <div class="mt-1"><span class="{{ $item['class'] }}">{{ $item['label'] }}</span></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
