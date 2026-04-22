@extends('layouts.admin')

@section('title', 'Chi tiet dat phong')

@push('styles')
    <style>
        .booking-detail-hero {
            border: 1px solid #e5edf6;
            background: #fff;
            color: #173652;
        }

        .booking-detail-hero .section-title,
        .booking-detail-hero .section-subtitle {
            color: inherit;
        }

        .hero-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: #f8fbff;
            border: 1px solid #dbe7f2;
            color: #173652;
            font-size: 0.84rem;
            font-weight: 700;
        }

        .hero-stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 16px;
            margin-top: 24px;
        }

        .hero-stat-card {
            border-radius: 18px;
            padding: 16px;
            background: #f8fbff;
            border: 1px solid #dbe7f2;
        }

        .hero-stat-label {
            font-size: 0.76rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #6b8298;
        }

        .hero-stat-value {
            margin-top: 6px;
            font-size: 1.45rem;
            line-height: 1.1;
            font-weight: 800;
        }

        .hero-stat-note {
            margin-top: 6px;
            font-size: 0.82rem;
            color: #6b8298;
        }

        .info-block {
            padding: 16px;
            border-radius: 18px;
            border: 1px solid #e5edf6;
            background: #fbfdff;
        }

        .info-label {
            color: #68839f;
            font-size: 0.82rem;
            margin-bottom: 4px;
        }

        .info-value {
            color: #173652;
            font-weight: 700;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #edf2f8;
        }

        .summary-row:last-child {
            padding-bottom: 0;
            border-bottom: none;
        }

        .timeline-list {
            display: grid;
            gap: 16px;
        }

        .timeline-item {
            display: flex;
            gap: 12px;
        }

        .timeline-dot {
            width: 14px;
            height: 14px;
            border-radius: 999px;
            margin-top: 4px;
            background: #0f766e;
            box-shadow: 0 0 0 6px rgba(15, 118, 110, 0.12);
            flex-shrink: 0;
        }

        .service-editor-table .form-control,
        .service-editor-table .form-select {
            min-width: 120px;
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

        $chipTrangThai = $mapTrangThai[$datPhong->trang_thai] ?? 'chip chip-neutral';
        $chipUuTien = $datPhong->muc_do_uu_tien === 'cao'
            ? 'chip chip-danger'
            : ($datPhong->muc_do_uu_tien === 'trung_binh' ? 'chip chip-warning' : 'chip chip-neutral');
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
        $hanhDongXuLy = $hanhDongTheoTrangThai[$datPhong->trang_thai] ?? [];
        $tieuDeXuLy = match ($datPhong->trang_thai) {
            'cho_xac_nhan', 'da_xac_nhan', 'da_nhan_phong' => 'Xu ly tiep theo',
            'da_tra_phong' => 'Hoan tat',
            'da_huy' => 'Da dung',
            default => 'Xu ly',
        };
    @endphp

    <div class="premium-card booking-detail-hero mb-4">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <h2 class="section-title">Chi tiet don {{ $datPhong->ma_dat_phong }}</h2>
                    <p class="section-subtitle">
                        Tao luc {{ optional($datPhong->ngay_dat)->format('d/m/Y H:i') ?? '-' }}
                        @if($datPhong->nguoiTao)
                            • Nguoi tao: {{ $datPhong->nguoiTao->ho_ten }}
                        @endif
                    </p>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <span class="hero-chip"><i class="fa-solid fa-circle-dot"></i>{{ \App\Support\HienThiGiaTri::nhanGiaTri($datPhong->trang_thai) }}</span>
                    <span class="hero-chip"><i class="fa-solid fa-bolt"></i>{{ ucfirst($datPhong->muc_do_uu_tien) }}</span>
                    <a href="{{ route('dat-phong.index') }}" class="btn btn-light fw-semibold">
                        <i class="fa-solid fa-list me-2"></i>Quay lại danh sách
                    </a>
                </div>
            </div>

            <div class="hero-stat-grid">
                <div class="hero-stat-card">
                    <div class="hero-stat-label">Tien phong</div>
                    <div class="hero-stat-value">{{ number_format((float) $tongTienPhong, 0, ',', '.') }}</div>
                    <div class="hero-stat-note">VNĐ tien phong cua don hien tai</div>
                </div>
                <div class="hero-stat-card">
                    <div class="hero-stat-label">Tien dich vu</div>
                    <div class="hero-stat-value">{{ number_format((float) $tongTienDichVu, 0, ',', '.') }}</div>
                    <div class="hero-stat-note">{{ $datPhong->suDungDichVu->count() }} dong dich vu da ghi nhan</div>
                </div>
                <div class="hero-stat-card">
                    <div class="hero-stat-label">Tong tam tinh</div>
                    <div class="hero-stat-value">{{ number_format((float) $tongThanhToanDuKien, 0, ',', '.') }}</div>
                    <div class="hero-stat-note">Tien phong + dich vu truoc giam gia va thue</div>
                </div>
                <div class="hero-stat-card">
                    <div class="hero-stat-label">Hóa đơn</div>
                    <div class="hero-stat-value">{{ $hoaDonHienTai ? $hoaDonHienTai->ma_hoa_don : 'Chua co' }}</div>
                    <div class="hero-stat-note">{{ $hoaDonHienTai ? number_format((float) $datPhong->so_tien_con_lai_hoa_don, 0, ',', '.') . ' VNĐ con lai' : 'Co the tao thu cong hoac tao tu dong khi tra phong' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="premium-card mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">Thông tin khách hàng</h5>
                            <p class="text-muted small mb-0">Ho so lien he chinh cua khach trong don dat phong nay.</p>
                        </div>
                        @if($datPhong->khachHang)
                            <a href="{{ route('khach-hang.show', $datPhong->khachHang) }}" class="btn btn-sm btn-outline-primary">Xem ho so khach</a>
                        @endif
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="info-block">
                                <div class="info-label">Ho ten</div>
                                <div class="info-value">{{ $datPhong->khachHang?->ho_ten ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-block">
                                <div class="info-label">So dien thoai</div>
                                <div class="info-value">{{ $datPhong->khachHang?->so_dien_thoai ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-block">
                                <div class="info-label">Email</div>
                                <div class="info-value">{{ $datPhong->khachHang?->email ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="premium-card mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">Danh sach phong trong don</h5>
                            <p class="text-muted small mb-0">Tong hop loai phong, gia dem va trang thai tung phong da gan.</p>
                        </div>
                        <div class="fw-bold text-primary">{{ number_format((float) $tongTienPhong, 0, ',', '.') }} VNĐ</div>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Phong</th>
                                    <th>Loai phong</th>
                                    <th>Gia / dem</th>
                                    <th>So dem</th>
                                    <th>Thanh tien</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($datPhong->chiTietDatPhong as $chiTiet)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $chiTiet->phong?->so_phong ? 'Phong ' . $chiTiet->phong->so_phong : '-' }}</div>
                                            <div class="small text-muted">Tang {{ $chiTiet->phong?->tang ?? '-' }}</div>
                                        </td>
                                        <td>{{ $chiTiet->phong?->loaiPhong?->ten_loai_phong ?? '-' }}</td>
                                        <td>{{ number_format((float) $chiTiet->gia_phong, 0, ',', '.') }} VNĐ</td>
                                        <td>{{ $chiTiet->so_dem }}</td>
                                        <td class="fw-semibold">{{ number_format((float) $chiTiet->gia_phong * (int) $chiTiet->so_dem, 0, ',', '.') }} VNĐ</td>
                                        <td><span class="chip chip-neutral">{{ \App\Support\HienThiGiaTri::nhanGiaTri($chiTiet->trang_thai) }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">Chua co chi tiet phong.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="premium-card mb-4">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">Dich vu su dung</h5>
                            <p class="text-muted small mb-0">Nhan vien ghi nhan dich vu thuc te va he thong se tu dong cap nhat hoa don neu da ton tai.</p>
                        </div>
                        <div class="fw-bold text-success">{{ number_format((float) $tongTienDichVu, 0, ',', '.') }} VNĐ</div>
                    </div>

                    @if(!$coTheCapNhatDichVu)
                        <div class="alert alert-warning">
                            Khong the cap nhat dich vu cho don nay luc nay. Nguyen nhan co the la don chua xac nhan, da huy hoac hoa don da thanh toan day du.
                        </div>
                    @endif

                    @if($danhSachDichVuHoatDong->isEmpty())
                        <div class="alert alert-light border">
                            Chua co dich vu nao dang hoat dong. Hay vao muc Quan ly dich vu de tao danh muc truoc.
                        </div>
                    @endif

                    <form action="{{ route('dat-phong.dich-vu.store', $datPhong) }}" method="POST" class="row g-3 mb-4">
                        @csrf

                        <div class="col-lg-4">
                            <label class="form-label">Dich vu</label>
                            <select name="dich_vu_id" class="form-select @error('dich_vu_id') is-invalid @enderror" @disabled(!$coTheCapNhatDichVu || $danhSachDichVuHoatDong->isEmpty()) required>
                                <option value="">-- Chon dich vu --</option>
                                @foreach($danhSachDichVuHoatDong as $dichVu)
                                    <option value="{{ $dichVu->id }}" @selected((string) old('dich_vu_id') === (string) $dichVu->id)>
                                        {{ $dichVu->ten_dich_vu }} - {{ number_format((float) $dichVu->don_gia, 0, ',', '.') }} VNĐ / {{ $dichVu->don_vi_tinh }}
                                    </option>
                                @endforeach
                            </select>
                            @error('dich_vu_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-lg-2">
                            <label class="form-label">So luong</label>
                            <input type="number" min="1" max="999" name="so_luong" class="form-control @error('so_luong') is-invalid @enderror" value="{{ old('so_luong', 1) }}" @disabled(!$coTheCapNhatDichVu) required>
                            @error('so_luong')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-lg-2">
                            <label class="form-label">Don gia</label>
                            <input type="number" min="0" step="1000" name="don_gia" class="form-control @error('don_gia') is-invalid @enderror" value="{{ old('don_gia') }}" @disabled(!$coTheCapNhatDichVu) placeholder="Mac dinh">
                            @error('don_gia')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-lg-4">
                            <label class="form-label">Thoi diem su dung</label>
                            <input type="datetime-local" name="thoi_diem_su_dung" class="form-control @error('thoi_diem_su_dung') is-invalid @enderror" value="{{ old('thoi_diem_su_dung', now()->format('Y-m-d\TH:i')) }}" @disabled(!$coTheCapNhatDichVu)>
                            @error('thoi_diem_su_dung')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Ghi chu</label>
                            <input type="text" name="ghi_chu" class="form-control @error('ghi_chu') is-invalid @enderror" value="{{ old('ghi_chu') }}" placeholder="Noi dung bo sung neu can" @disabled(!$coTheCapNhatDichVu)>
                            @error('ghi_chu')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-gradient" @disabled(!$coTheCapNhatDichVu || $danhSachDichVuHoatDong->isEmpty())>
                                <i class="fa-solid fa-plus me-2"></i>Ghi nhận dịch vụ
                            </button>
                        </div>
                    </form>

                    <div class="table-responsive service-editor-table">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Dich vu</th>
                                    <th>So luong</th>
                                    <th>Don gia</th>
                                    <th>Thoi diem</th>
                                    <th>Thanh tien</th>
                                    <th>Ghi chu</th>
                                    <th>Nguoi ghi</th>
                                    <th class="text-end">Thao tac</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($datPhong->suDungDichVu->sortByDesc('thoi_diem_su_dung') as $suDungDichVu)
                                    @php
                                        $formCapNhatId = 'cap-nhat-dich-vu-' . $suDungDichVu->id;
                                        $formXoaId = 'xoa-dich-vu-' . $suDungDichVu->id;
                                        $coDichVuHienTaiTrongDanhSach = $danhSachDichVuHoatDong->contains('id', $suDungDichVu->dich_vu_id);
                                    @endphp
                                    <form id="{{ $formCapNhatId }}" method="POST" action="{{ route('dat-phong.dich-vu.update', [$datPhong, $suDungDichVu]) }}">
                                        @csrf
                                        @method('PATCH')
                                    </form>
                                    <form id="{{ $formXoaId }}" method="POST" action="{{ route('dat-phong.dich-vu.destroy', [$datPhong, $suDungDichVu]) }}">
                                        @csrf
                                        @method('DELETE')
                                    </form>

                                    <tr>
                                        <td style="min-width: 220px;">
                                            <select form="{{ $formCapNhatId }}" name="dich_vu_id" class="form-select form-select-sm" @disabled(!$coTheCapNhatDichVu)>
                                                @if($suDungDichVu->dichVu && !$coDichVuHienTaiTrongDanhSach)
                                                    <option value="{{ $suDungDichVu->dichVu->id }}" selected>{{ $suDungDichVu->dichVu->ten_dich_vu }} - tam dung</option>
                                                @endif
                                                @foreach($danhSachDichVuHoatDong as $dichVu)
                                                    <option value="{{ $dichVu->id }}" @selected($suDungDichVu->dich_vu_id === $dichVu->id)>{{ $dichVu->ten_dich_vu }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td style="min-width: 110px;">
                                            <input form="{{ $formCapNhatId }}" type="number" min="1" max="999" name="so_luong" class="form-control form-control-sm" value="{{ (int) $suDungDichVu->so_luong }}" @disabled(!$coTheCapNhatDichVu)>
                                        </td>
                                        <td style="min-width: 130px;">
                                            <input form="{{ $formCapNhatId }}" type="number" min="0" step="1000" name="don_gia" class="form-control form-control-sm" value="{{ (float) $suDungDichVu->don_gia }}" @disabled(!$coTheCapNhatDichVu)>
                                        </td>
                                        <td style="min-width: 180px;">
                                            <input form="{{ $formCapNhatId }}" type="datetime-local" name="thoi_diem_su_dung" class="form-control form-control-sm" value="{{ optional($suDungDichVu->thoi_diem_su_dung)->format('Y-m-d\TH:i') }}" @disabled(!$coTheCapNhatDichVu)>
                                        </td>
                                        <td class="fw-semibold">{{ number_format((float) $suDungDichVu->thanh_tien, 0, ',', '.') }} VNĐ</td>
                                        <td style="min-width: 180px;">
                                            <input form="{{ $formCapNhatId }}" type="text" name="ghi_chu" class="form-control form-control-sm" value="{{ $suDungDichVu->ghi_chu }}" @disabled(!$coTheCapNhatDichVu)>
                                        </td>
                                        <td>{{ $suDungDichVu->nguoiTao?->ho_ten ?? '-' }}</td>
                                        <td class="text-end">
                                            <div class="d-inline-flex gap-2">
                                                <button type="submit" form="{{ $formCapNhatId }}" class="btn btn-sm btn-outline-primary" @disabled(!$coTheCapNhatDichVu)>Luu</button>
                                                <button type="submit" form="{{ $formXoaId }}" class="btn btn-sm btn-outline-danger" onclick="return confirm('Ban co chac muon xoa dong dich vu nay?')" @disabled(!$coTheCapNhatDichVu)>Xoa</button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">Chua co dich vu nao duoc ghi nhan cho don nay.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="premium-card">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Yeu cau va ghi chu</h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="info-block h-100">
                                <div class="info-label">Yeu cau dac biet</div>
                                <div class="info-value">{{ $datPhong->yeu_cau_dac_biet ?: '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-block h-100">
                                <div class="info-label">Ghi chu noi bo</div>
                                <div class="info-value">{{ $datPhong->ghi_chu ?: '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="premium-card mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Tong hop luu tru</h5>

                    <div class="summary-row">
                        <span class="text-muted">Trạng thái</span>
                        <span class="{{ $chipTrangThai }}">{{ \App\Support\HienThiGiaTri::nhanGiaTri($datPhong->trang_thai) }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="text-muted">Uu tien xu ly</span>
                        <span class="{{ $chipUuTien }}">{{ ucfirst($datPhong->muc_do_uu_tien) }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="text-muted">Ngay nhan du kien</span>
                        <span class="fw-semibold">{{ optional($datPhong->ngay_nhan_phong_du_kien)->format('d/m/Y') ?? '-' }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="text-muted">Ngay tra du kien</span>
                        <span class="fw-semibold">{{ optional($datPhong->ngay_tra_phong_du_kien)->format('d/m/Y') ?? '-' }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="text-muted">Nhan phong thuc te</span>
                        <span class="fw-semibold">{{ optional($datPhong->ngay_nhan_phong_thuc_te)->format('d/m/Y H:i') ?? '-' }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="text-muted">Tra phong thuc te</span>
                        <span class="fw-semibold">{{ optional($datPhong->ngay_tra_phong_thuc_te)->format('d/m/Y H:i') ?? '-' }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="text-muted">So nguoi o</span>
                        <span class="fw-semibold">{{ (int) $datPhong->so_nguoi_lon }} NL • {{ (int) $datPhong->so_tre_em }} TE</span>
                    </div>
                    <div class="summary-row">
                        <span class="text-muted">Nguon dat</span>
                        <span class="fw-semibold">{{ \App\Support\HienThiGiaTri::nhanGiaTri($datPhong->nguon_dat ?? '-') }}</span>
                    </div>
                </div>
            </div>

            <div class="premium-card mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Hóa đơn liên quan</h5>

                    @if($hoaDonHienTai)
                        <div class="summary-row">
                            <span class="text-muted">Ma hoa don</span>
                            <span class="fw-semibold">{{ $hoaDonHienTai->ma_hoa_don }}</span>
                        </div>
                        <div class="summary-row">
                            <span class="text-muted">Tổng tiền</span>
                            <span class="fw-semibold">{{ number_format((float) $hoaDonHienTai->tong_tien, 0, ',', '.') }} VNĐ</span>
                        </div>
                        <div class="summary-row">
                            <span class="text-muted">Tien dich vu</span>
                            <span class="fw-semibold">{{ number_format((float) $hoaDonHienTai->tong_tien_dich_vu, 0, ',', '.') }} VNĐ</span>
                        </div>
                        <div class="summary-row">
                            <span class="text-muted">Da thu</span>
                            <span class="fw-semibold text-success">{{ number_format((float) $datPhong->so_tien_da_thu_hoa_don, 0, ',', '.') }} VNĐ</span>
                        </div>
                        <div class="summary-row">
                            <span class="text-muted">Còn lại</span>
                            <span class="fw-semibold text-danger">{{ number_format((float) $datPhong->so_tien_con_lai_hoa_don, 0, ',', '.') }} VNĐ</span>
                        </div>

                        <a href="{{ route('hoa-don.show', $hoaDonHienTai) }}" class="btn btn-outline-success w-100 mt-3">
                            <i class="fa-solid fa-file-invoice-dollar me-2"></i>Xem hoa don
                        </a>
                    @else
                        <div class="text-muted small">Don nay chua co hoa don. He thong se tu tao khi trang thai chuyen sang da tra phong, hoac ban co the tao thu cong ngay bay gio.</div>

                        <a href="{{ route('hoa-don.create', ['dat_phong_id' => $datPhong->id]) }}" class="btn btn-outline-success w-100 mt-3">
                            <i class="fa-solid fa-file-invoice-dollar me-2"></i>Tạo hóa đơn cho đơn này
                        </a>
                    @endif
                </div>
            </div>

            <div class="premium-card mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Cap nhat trang thai don</h5>

                    <div class="text-uppercase small text-muted fw-semibold mb-3">{{ $tieuDeXuLy }}</div>

                    @if($hanhDongXuLy !== [])
                        <div class="d-grid gap-2 mb-3">
                            @foreach($hanhDongXuLy as $hanhDong)
                                <form method="POST" action="{{ route('dat-phong.cap-nhat-trang-thai', $datPhong) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="trang_thai" value="{{ $hanhDong['trang_thai'] }}">
                                    <button type="submit" class="btn w-100 {{ $hanhDong['class'] }}">
                                        {{ $hanhDong['label'] }}
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-light border mb-0">
                            {{ $datPhong->trang_thai === 'da_huy' ? 'Don da huy, khong con buoc xu ly.' : 'Don da hoan tat, khong can doi trang thai nua.' }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="premium-card">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Moc xu ly</h5>

                    <div class="timeline-list">
                        @foreach($timeline as $moc)
                            <div class="timeline-item">
                                <div class="timeline-dot"></div>
                                <div>
                                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                        <div class="fw-semibold">{{ $moc['label'] }}</div>
                                        <span class="{{ $moc['class'] }}">{{ $moc['label'] }}</span>
                                    </div>
                                    <div class="text-muted small">
                                        @if($moc['thoi_gian'])
                                            {{ $moc['co_gio'] ? $moc['thoi_gian']->format('d/m/Y H:i') : $moc['thoi_gian']->format('d/m/Y') }}
                                        @else
                                            -
                                        @endif
                                    </div>
                                    <div class="small mt-1">{{ $moc['ghi_chu'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
