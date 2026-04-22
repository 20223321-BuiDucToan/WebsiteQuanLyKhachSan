@extends('layouts.admin')

@section('title', 'Quan ly dich vu')

@push('styles')
    <style>
        .service-hero {
            border: 1px solid #e5edf6;
            background: #fff;
            color: #173652;
        }

        .service-hero .section-title,
        .service-hero .section-subtitle {
            color: inherit;
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
            font-size: 1.55rem;
            line-height: 1.1;
            font-weight: 800;
        }

        .hero-stat-note {
            margin-top: 6px;
            font-size: 0.82rem;
            color: #6b8298;
        }

        .service-name {
            font-size: 0.98rem;
            font-weight: 700;
            color: #12304d;
        }

        .table-subtext {
            color: #6b8298;
            font-size: 0.83rem;
        }
    </style>
@endpush

@section('content')
    <div class="premium-card service-hero mb-4">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <h2 class="section-title">Quan ly dich vu</h2>
                    <p class="section-subtitle">Xay dung danh muc dich vu phuc vu van hanh va dong bo doanh thu vao hoa don.</p>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('dich-vu.create') }}" class="btn btn-gradient">
                        <i class="fa-solid fa-plus me-2"></i>Them dich vu
                    </a>
                </div>
            </div>

            <div class="hero-stat-grid">
                <div class="hero-stat-card">
                    <div class="hero-stat-label">Tong dich vu</div>
                    <div class="hero-stat-value">{{ $thongKe['tong'] }}</div>
                    <div class="hero-stat-note">Toan bo dich vu dang co trong he thong</div>
                </div>
                <div class="hero-stat-card">
                    <div class="hero-stat-label">Hoat dong</div>
                    <div class="hero-stat-value">{{ $thongKe['hoat_dong'] }}</div>
                    <div class="hero-stat-note">Co the chon de ghi nhan vao don dat phong</div>
                </div>
                <div class="hero-stat-card">
                    <div class="hero-stat-label">Tam ngung</div>
                    <div class="hero-stat-value">{{ $thongKe['tam_ngung'] }}</div>
                    <div class="hero-stat-note">Tam khoa de tranh tiep tuc ban nham</div>
                </div>
                <div class="hero-stat-card">
                    <div class="hero-stat-label">Da phat sinh</div>
                    <div class="hero-stat-value">{{ $thongKe['da_phat_sinh'] }}</div>
                    <div class="hero-stat-note">Da co lich su su dung thuc te trong van hanh</div>
                </div>
            </div>
        </div>
    </div>

    <div class="premium-card mb-4">
        <div class="card-body p-4">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-xl-4">
                    <label class="form-label">Tu khoa</label>
                    <input type="text" name="tu_khoa" class="form-control" value="{{ $tuKhoa }}" placeholder="Ma dich vu, ten dich vu, loai dich vu">
                </div>

                <div class="col-xl-3">
                    <label class="form-label">Loai dich vu</label>
                    <select name="loai_dich_vu" class="form-select">
                        <option value="">Tat ca</option>
                        @foreach($danhSachLoaiDichVu as $loai)
                            <option value="{{ $loai }}" @selected($loaiDichVu === $loai)>{{ $loai }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xl-3">
                    <label class="form-label">Trạng thái</label>
                    <select name="trang_thai" class="form-select">
                        <option value="">Tat ca</option>
                        <option value="hoat_dong" @selected($trangThai === 'hoat_dong')>Hoat dong</option>
                        <option value="tam_ngung" @selected($trangThai === 'tam_ngung')>Tam ngung</option>
                    </select>
                </div>

                <div class="col-xl-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-gradient w-100">Loc</button>
                </div>

                <div class="col-xl-1 d-flex align-items-end">
                    <a href="{{ route('dich-vu.index') }}" class="btn btn-soft w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="premium-card">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                <div>
                    <h5 class="fw-bold mb-1">Danh sach dich vu</h5>
                    <div class="text-muted small">{{ $danhSachDichVu->total() }} dich vu phu hop voi bo loc hien tai</div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Dich vu</th>
                            <th>Loai</th>
                            <th>Gia ban</th>
                            <th>Trạng thái</th>
                            <th>Phat sinh</th>
                            <th class="text-end">Thao tac</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($danhSachDichVu as $dichVu)
                            @php
                                $chipTrangThai = $dichVu->trang_thai === 'hoat_dong' ? 'chip chip-success' : 'chip chip-neutral';
                            @endphp
                            <tr>
                                <td style="min-width: 220px;">
                                    <div class="service-name">{{ $dichVu->ten_dich_vu }}</div>
                                    <div class="table-subtext">{{ $dichVu->ma_dich_vu }} • {{ $dichVu->don_vi_tinh }}</div>
                                </td>
                                <td>{{ $dichVu->loai_dich_vu ?: '-' }}</td>
                                <td class="fw-semibold">{{ number_format((float) $dichVu->don_gia, 0, ',', '.') }} VNĐ / {{ $dichVu->don_vi_tinh }}</td>
                                <td><span class="{{ $chipTrangThai }}">{{ \App\Support\HienThiGiaTri::nhanGiaTri($dichVu->trang_thai) }}</span></td>
                                <td>{{ $dichVu->su_dung_dich_vu_count }} lan</td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        <a href="{{ route('dich-vu.edit', $dichVu) }}" class="btn btn-sm btn-outline-primary">Sua</a>
                                        <form method="POST" action="{{ route('dich-vu.destroy', $dichVu) }}" onsubmit="return confirm('Ban co chac muon xoa dich vu nay?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Xoa</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Chua co dich vu nao trong danh muc.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">{{ $danhSachDichVu->links() }}</div>
        </div>
    </div>
@endsection
