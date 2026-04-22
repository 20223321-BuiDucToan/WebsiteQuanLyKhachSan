@extends('layouts.admin')

@section('title', 'Quản lý phòng')

@push('styles')
    <style>
        .room-thumb-wrap {
            position: relative;
            width: 88px;
            height: 68px;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #d8e2ec;
            background: linear-gradient(180deg, #f7fbff, #edf4fb);
        }

        .room-thumb-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .room-thumb-empty {
            width: 100%;
            height: 100%;
            display: grid;
            place-items: center;
            color: #68839f;
            font-size: 1.15rem;
        }

        .room-thumb-count {
            position: absolute;
            right: 8px;
            bottom: 8px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            border-radius: 999px;
            padding: 4px 8px;
            font-size: 0.7rem;
            font-weight: 700;
            color: #fff;
            background: rgba(14, 31, 53, 0.78);
        }

        .room-code {
            font-weight: 700;
        }

        .room-subtext {
            margin-top: 4px;
            color: #68839f;
            font-size: 0.86rem;
        }
    </style>
@endpush

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="section-title">Quản lý phòng</h2>
            <p class="section-subtitle">Theo dõi trạng thái khai thác, vệ sinh, hoạt động và hình ảnh của từng phòng.</p>
        </div>

        <a href="{{ route('phong.create') }}" class="btn btn-gradient">
            <i class="fa-solid fa-plus me-2"></i>Thêm phòng
        </a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-label">Tổng số phòng</div>
                <div class="metric-value">{{ $thongKe['tong'] }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-label">Phòng trống</div>
                <div class="metric-value text-success">{{ $thongKe['trong'] }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-label">Đang sử dụng</div>
                <div class="metric-value text-primary">{{ $thongKe['dang_su_dung'] }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-label">Bảo trì</div>
                <div class="metric-value text-warning">{{ $thongKe['bao_tri'] }}</div>
            </div>
        </div>
    </div>

    <div class="premium-card mb-4">
        <div class="card-body p-4">
            <form method="GET" class="row g-3">
                <div class="col-lg-3">
                    <label class="form-label">Từ khóa</label>
                    <input type="text" name="tu_khoa" class="form-control" value="{{ $tuKhoa }}" placeholder="Mã phòng, số phòng">
                </div>

                <div class="col-lg-3">
                    <label class="form-label">Loại phòng</label>
                    <select name="loai_phong_id" class="form-select">
                        <option value="">Tất cả</option>
                        @foreach($danhSachLoaiPhong as $loaiPhong)
                            <option value="{{ $loaiPhong->id }}" @selected((string) $loaiPhongId === (string) $loaiPhong->id)>{{ $loaiPhong->ten_loai_phong }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2">
                    <label class="form-label">Tầng</label>
                    <input type="number" min="0" name="tang" class="form-control" value="{{ $tang }}">
                </div>

                <div class="col-lg-2">
                    <label class="form-label">Trạng thái phòng</label>
                    <select name="trang_thai" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="trong" @selected($trangThai === 'trong')>Trống</option>
                        <option value="da_dat" @selected($trangThai === 'da_dat')>Đã đặt</option>
                        <option value="dang_su_dung" @selected($trangThai === 'dang_su_dung')>Đang sử dụng</option>
                        <option value="don_dep" @selected($trangThai === 'don_dep')>Dọn dẹp</option>
                        <option value="bao_tri" @selected($trangThai === 'bao_tri')>Bảo trì</option>
                    </select>
                </div>

                <div class="col-lg-2">
                    <label class="form-label">Tình trạng hoạt động</label>
                    <select name="tinh_trang_hoat_dong" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="hoat_dong" @selected($tinhTrangHoatDong === 'hoat_dong')>Hoạt động</option>
                        <option value="tam_ngung" @selected($tinhTrangHoatDong === 'tam_ngung')>Tạm ngưng</option>
                    </select>
                </div>

                <div class="col-lg-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-gradient w-100">Lọc</button>
                </div>
                <div class="col-lg-2 d-flex align-items-end">
                    <a href="{{ route('phong.index') }}" class="btn btn-soft w-100">Đặt lại</a>
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
                            <th>Ảnh</th>
                            <th>Mã phòng</th>
                            <th>Số phòng</th>
                            <th>Loại phòng</th>
                            <th>Tầng</th>
                            <th>Giá / đêm</th>
                            <th>Trạng thái</th>
                            <th>Hoạt động</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($danhSachPhong as $phong)
                            @php
                                $anhDauTien = $phong->layAnhPhongDauTienUrl();
                                $tongSoAnh = count($phong->layDanhSachAnhPhong());
                            @endphp
                            <tr>
                                <td>
                                    <div class="room-thumb-wrap">
                                        @if($anhDauTien)
                                            <img src="{{ $anhDauTien }}" alt="Ảnh phòng {{ $phong->so_phong }}">
                                            <span class="room-thumb-count">
                                                <i class="fa-regular fa-images"></i>{{ $tongSoAnh }}
                                            </span>
                                        @else
                                            <div class="room-thumb-empty">
                                                <i class="fa-regular fa-image"></i>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="room-code">{{ $phong->ma_phong }}</div>
                                    <div class="room-subtext">
                                        {{ $tongSoAnh > 0 ? $tongSoAnh . ' ảnh đã đăng' : 'Chưa có ảnh' }}
                                    </div>
                                </td>
                                <td class="fw-bold">{{ $phong->so_phong }}</td>
                                <td>{{ $phong->loaiPhong?->ten_loai_phong ?? '-' }}</td>
                                <td>{{ $phong->tang ?? '-' }}</td>
                                <td>{{ number_format((float) ($phong->gia_mac_dinh ?? $phong->loaiPhong?->gia_mot_dem ?? 0), 0, ',', '.') }} VNĐ</td>
                                <td><span class="chip chip-neutral">{{ \App\Support\HienThiGiaTri::nhanGiaTri($phong->trang_thai) }}</span></td>
                                <td>
                                    @if($phong->tinh_trang_hoat_dong === 'hoat_dong')
                                        <span class="chip chip-success">Hoạt động</span>
                                    @else
                                        <span class="chip chip-warning">Tạm ngưng</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('phong.edit', $phong) }}" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pen"></i></a>

                                    <form action="{{ route('phong.destroy', $phong) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc muốn xóa phòng này?')">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">Chưa có phòng nào trong hệ thống.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">{{ $danhSachPhong->links() }}</div>
        </div>
    </div>
@endsection
