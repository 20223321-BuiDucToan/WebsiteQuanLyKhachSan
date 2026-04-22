@extends('layouts.admin')

@section('title', 'Tạo hóa đơn')

@section('content')
    <div class="mb-4">
        <h2 class="section-title">Tạo hóa đơn</h2>
        <p class="section-subtitle">Lap hoa don tu du lieu phong va dich vu da phat sinh thuc te, khong nhap tay tien dich vu nua.</p>
    </div>

    <div class="premium-card mb-4">
        <div class="card-body p-4">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-lg-8">
                    <label class="form-label">Don dat phong</label>
                    <select name="dat_phong_id" class="form-select" required>
                        <option value="">-- Chon don dat phong --</option>
                        @foreach($danhSachDatPhong as $datPhong)
                            @php
                                $chiTiet = $datPhong->chiTietDatPhong->first();
                            @endphp
                            <option value="{{ $datPhong->id }}" @selected((string) request('dat_phong_id') === (string) $datPhong->id)>
                                {{ $datPhong->ma_dat_phong }} - {{ $datPhong->khachHang?->ho_ten ?? 'Khach le' }}
                                @if($chiTiet?->phong)
                                    - Phong {{ $chiTiet->phong->so_phong }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2">
                    <button type="submit" class="btn btn-soft w-100">Tai thong tin</button>
                </div>

                <div class="col-lg-2">
                    <a href="{{ route('hoa-don.index') }}" class="btn btn-outline-secondary w-100">Quay lại</a>
                </div>
            </form>
        </div>
    </div>

    @php
        $giamGiaOld = (float) old('giam_gia', 0);
        $thueOld = (float) old('thue', 0);
        $tongTamTinh = max(0, (float) $tongTienPhong + (float) $tongTienDichVu - $giamGiaOld + $thueOld);
    @endphp

    <div class="premium-card">
        <div class="card-body p-4">
            @if(!$datPhongDuocChon)
                <div class="alert alert-info mb-0">Vui long chon don dat phong o tren de tao hoa don.</div>
            @else
                <form action="{{ route('hoa-don.store') }}" method="POST" class="row g-4">
                    @csrf
                    <input type="hidden" name="dat_phong_id" value="{{ $datPhongDuocChon->id }}">

                    <div class="col-12">
                        <h5 class="fw-bold mb-3">Thông tin đơn đặt phòng</h5>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="text-muted small">Ma dat phong</div>
                                <div class="fw-semibold">{{ $datPhongDuocChon->ma_dat_phong }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-muted small">Khách hàng</div>
                                <div class="fw-semibold">{{ $datPhongDuocChon->khachHang?->ho_ten ?? '-' }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-muted small">So dien thoai</div>
                                <div class="fw-semibold">{{ $datPhongDuocChon->khachHang?->so_dien_thoai ?? '-' }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-muted small">Trạng thái đơn</div>
                                <div class="fw-semibold">{{ \App\Support\HienThiGiaTri::nhanGiaTri($datPhongDuocChon->trang_thai) }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Phong</th>
                                        <th>Gia / dem</th>
                                        <th>So dem</th>
                                        <th>Thanh tien</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($datPhongDuocChon->chiTietDatPhong as $chiTiet)
                                        <tr>
                                            <td>{{ $chiTiet->phong?->so_phong ? 'Phong ' . $chiTiet->phong->so_phong : '-' }}</td>
                                            <td>{{ number_format((float) $chiTiet->gia_phong, 0, ',', '.') }} VNĐ</td>
                                            <td>{{ (int) $chiTiet->so_dem }}</td>
                                            <td class="fw-semibold">{{ number_format((float) $chiTiet->gia_phong * (int) $chiTiet->so_dem, 0, ',', '.') }} VNĐ</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">Khong co chi tiet phong.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-12">
                        <h5 class="fw-bold mb-3">Dich vu da ghi nhan</h5>

                        @if($datPhongDuocChon->suDungDichVu->isEmpty())
                            <div class="alert alert-light border mb-0">Don nay chua co dich vu phat sinh. Neu can, hay vao chi tiet don dat phong de ghi nhan truoc khi lap hoa don.</div>
                        @else
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead>
                                        <tr>
                                            <th>Dich vu</th>
                                            <th>Thoi diem</th>
                                            <th>So luong</th>
                                            <th>Don gia</th>
                                            <th>Thanh tien</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($datPhongDuocChon->suDungDichVu as $suDungDichVu)
                                            <tr>
                                                <td>{{ $suDungDichVu->dichVu?->ten_dich_vu ?? 'Dich vu da xoa' }}</td>
                                                <td>{{ optional($suDungDichVu->thoi_diem_su_dung)->format('d/m/Y H:i') ?? '-' }}</td>
                                                <td>{{ (int) $suDungDichVu->so_luong }} {{ $suDungDichVu->dichVu?->don_vi_tinh ?? '' }}</td>
                                                <td>{{ number_format((float) $suDungDichVu->don_gia, 0, ',', '.') }} VNĐ</td>
                                                <td class="fw-semibold">{{ number_format((float) $suDungDichVu->thanh_tien, 0, ',', '.') }} VNĐ</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Tổng tiền phòng</label>
                        <input type="text" class="form-control" value="{{ number_format((float) $tongTienPhong, 0, ',', '.') }} VNĐ" disabled>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Tổng tiền dịch vụ</label>
                        <input type="text" class="form-control" value="{{ number_format((float) $tongTienDichVu, 0, ',', '.') }} VNĐ" disabled>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Giam gia</label>
                        <input type="number" min="0" step="1000" name="giam_gia" class="form-control @error('giam_gia') is-invalid @enderror" value="{{ old('giam_gia', 0) }}">
                        @error('giam_gia')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Thue</label>
                        <input type="number" min="0" step="1000" name="thue" class="form-control @error('thue') is-invalid @enderror" value="{{ old('thue', 0) }}">
                        @error('thue')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">Ghi chu</label>
                        <textarea name="ghi_chu" rows="3" class="form-control @error('ghi_chu') is-invalid @enderror">{{ old('ghi_chu') }}</textarea>
                        @error('ghi_chu')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <div class="alert alert-light border mb-0">
                            Tong thanh toan tam tinh: <span class="fw-bold fs-5">{{ number_format((float) $tongTamTinh, 0, ',', '.') }} VNĐ</span>
                        </div>
                    </div>

                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-gradient"><i class="fa-solid fa-file-invoice-dollar me-2"></i>Tạo hóa đơn</button>
                        <a href="{{ route('dat-phong.show', $datPhongDuocChon) }}" class="btn btn-soft">Mo chi tiet don</a>
                    </div>
                </form>
            @endif
        </div>
    </div>
@endsection
