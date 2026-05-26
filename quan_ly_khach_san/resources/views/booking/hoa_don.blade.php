@extends('layouts.app')

@section('title', 'Hóa đơn của tôi')

@push('styles')
    <style>
        .invoice-shell {
            display: grid;
            gap: 20px;
        }

        .invoice-hero,
        .invoice-card {
            border: 1px solid #d9e4ef;
            border-radius: 22px;
            background: #fff;
            box-shadow: 0 18px 36px rgba(16, 42, 67, 0.08);
        }

        .invoice-hero {
            padding: 26px;
            background:
                radial-gradient(circle at top left, #dff4ef 0, transparent 28%),
                linear-gradient(145deg, #ffffff, #f7fbff);
        }

        .hero-grid,
        .summary-grid,
        .deposit-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 14px;
        }

        .hero-box,
        .summary-box,
        .deposit-box {
            border-radius: 18px;
            border: 1px solid #dbe7f2;
            background: #f9fcff;
            padding: 16px;
        }

        .hero-label,
        .summary-label,
        .deposit-label {
            color: #68839f;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .hero-value,
        .summary-value,
        .deposit-value {
            margin-top: 6px;
            font-size: 1.25rem;
            font-weight: 800;
            color: #10243e;
        }

        .invoice-card {
            padding: 22px;
        }

        .chip-inline {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 0.84rem;
            font-weight: 700;
        }

        .chip-success {
            background: #e8f8ef;
            color: #166534;
        }

        .chip-warning {
            background: #fff5e7;
            color: #b45309;
        }

        .chip-danger {
            background: #feeceb;
            color: #b42318;
        }

        .chip-info {
            background: #e9f5ff;
            color: #0f5f92;
        }

        .chip-neutral {
            background: #eef3f8;
            color: #4d6278;
        }

        .intent-grid {
            display: grid;
            gap: 12px;
            margin-bottom: 16px;
        }

        .intent-card {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            border: 1px solid #d7e3ef;
            border-radius: 16px;
            padding: 14px 16px;
            background: #fbfdff;
        }

        .quick-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 12px;
        }

        .note-inline {
            color: #68839f;
            font-size: 0.86rem;
        }
    </style>
@endpush

@section('content')
    @php
        $mapTrangThaiHoaDon = [
            'chua_thanh_toan' => 'chip-danger',
            'thanh_toan_mot_phan' => 'chip-warning',
            'da_thanh_toan' => 'chip-success',
            'da_huy' => 'chip-neutral',
        ];
        $mapTrangThaiThanhToan = [
            'thanh_cong' => 'chip-success',
            'cho_xu_ly' => 'chip-warning',
            'that_bai' => 'chip-danger',
        ];
        $chipHoaDon = $mapTrangThaiHoaDon[$hoaDon->trang_thai] ?? 'chip-neutral';
        $coTheGuiThanhToan = $hoaDon->trang_thai !== 'da_huy' && $soTienConLaiCoTheGuiYeuCau > 0;
        $loaiYeuCauDangChon = old('loai_yeu_cau', $cheDoMacDinhThanhToan);
        $soTienMacDinh = $loaiYeuCauDangChon === 'coc_phong'
            ? (int) $soTienMacDinhChoCoc
            : (int) $soTienMacDinhChoThanhToan;
        $soTienDaGhiNhanChoCoc = max(0, (float) $goiYDatCoc - (float) $soTienConThieuCoc);
    @endphp

    <div class="invoice-shell">
        <section class="invoice-hero">
            <div class="d-flex flex-wrap justify-content-between gap-3 mb-4">
                <div>
                    <h1 class="h3 mb-2">Hóa đơn {{ $hoaDon->ma_hoa_don }}</h1>
                    <p class="text-muted mb-0">
                        Đơn đặt phòng {{ $hoaDon->datPhong?->ma_dat_phong ?? '-' }}
                        @if($hoaDon->datPhong?->ngay_nhan_phong_du_kien && $hoaDon->datPhong?->ngay_tra_phong_du_kien)
                            | {{ $hoaDon->datPhong->ngay_nhan_phong_du_kien->format('d/m/Y') }} - {{ $hoaDon->datPhong->ngay_tra_phong_du_kien->format('d/m/Y') }}
                        @endif
                    </p>
                </div>

                <div class="d-flex flex-wrap gap-2 align-items-start">
                    <span class="chip-inline {{ $chipHoaDon }}">{{ \App\Support\HienThiGiaTri::nhanGiaTri($hoaDon->trang_thai) }}</span>
                    <a href="{{ route('booking.payments') }}" class="btn btn-outline-secondary rounded-3">Quay lại thanh toán</a>
                </div>
            </div>

            <div class="hero-grid">
                <div class="hero-box">
                    <div class="hero-label">Tổng tiền</div>
                    <div class="hero-value">{{ number_format((float) $hoaDon->tong_tien, 0, ',', '.') }} VNĐ</div>
                </div>
                <div class="hero-box">
                    <div class="hero-label">Đã thanh toán</div>
                    <div class="hero-value text-success">{{ number_format((float) $soTienDaThanhToan, 0, ',', '.') }} VNĐ</div>
                </div>
                <div class="hero-box">
                    <div class="hero-label">Đang chờ đối soát</div>
                    <div class="hero-value text-warning">{{ number_format((float) $soTienChoXuLy, 0, ',', '.') }} VNĐ</div>
                </div>
                <div class="hero-box">
                    <div class="hero-label">Còn có thể gửi</div>
                    <div class="hero-value {{ $soTienConLaiCoTheGuiYeuCau > 0 ? 'text-danger' : 'text-success' }}">
                        {{ number_format((float) $soTienConLaiCoTheGuiYeuCau, 0, ',', '.') }} VNĐ
                    </div>
                </div>
            </div>

            @if($hoaDon->datPhong?->trang_thai === 'khong_den')
                <div class="alert alert-warning mt-3 mb-0">
                    Đơn này đã được đánh dấu không đến. Tổng tiền hiện tại là phí no-show, không phải tiền lưu trú đủ kỳ ban đầu.
                </div>
            @endif
        </section>

        @if($goiYDatCoc > 0)
            <section class="invoice-card">
                <div class="d-flex flex-wrap justify-content-between gap-3 mb-3">
                    <div>
                        <h2 class="h5 mb-1">Cọc phòng</h2>
                        <p class="text-muted mb-0">Khoản cọc cho lịch ở này.</p>
                    </div>
                    @if($coTheDatCoc && $coTheGuiThanhToan)
                        <a href="{{ route('booking.hoa-don.show', ['hoaDon' => $hoaDon, 'che_do' => 'coc']) }}" class="btn btn-outline-primary rounded-3">Điền mức cọc</a>
                    @endif
                </div>

                <div class="deposit-grid">
                    <div class="deposit-box">
                        <div class="deposit-label">Mức cọc gợi ý</div>
                        <div class="deposit-value">{{ number_format((float) $goiYDatCoc, 0, ',', '.') }} VNĐ</div>
                    </div>
                    <div class="deposit-box">
                        <div class="deposit-label">Đã ghi nhận cho cọc</div>
                        <div class="deposit-value text-success">{{ number_format((float) $soTienDaGhiNhanChoCoc, 0, ',', '.') }} VNĐ</div>
                    </div>
                    <div class="deposit-box">
                        <div class="deposit-label">Còn thiếu để đủ cọc</div>
                        <div class="deposit-value {{ $soTienConThieuCoc > 0 ? 'text-warning' : 'text-success' }}">
                            {{ number_format((float) $soTienConThieuCoc, 0, ',', '.') }} VNĐ
                        </div>
                    </div>
                </div>

                <div class="note-inline mt-3">
                    @if($coTheDatCoc)
                        Vẫn có thể chọn thanh toán thêm ở form bên dưới.
                    @else
                        Khoản cọc này đã đủ.
                    @endif
                </div>
            </section>
        @endif

        <section class="invoice-card">
            <div class="d-flex flex-wrap justify-content-between gap-3 mb-3">
                <div>
                    <h2 class="h5 mb-1">Tổng hợp công nợ</h2>
                    <p class="text-muted mb-0">Các khoản trên hóa đơn.</p>
                </div>
                <div class="text-muted small">
                    Xuất lúc {{ optional($hoaDon->thoi_diem_xuat)->format('d/m/Y H:i') ?? '-' }}
                </div>
            </div>

            <div class="summary-grid">
                <div class="summary-box">
                    <div class="summary-label">Tiền phòng</div>
                    <div class="summary-value">{{ number_format((float) $hoaDon->tong_tien_phong, 0, ',', '.') }} VNĐ</div>
                </div>
                <div class="summary-box">
                    <div class="summary-label">Tiền dịch vụ</div>
                    <div class="summary-value">{{ number_format((float) $hoaDon->tong_tien_dich_vu, 0, ',', '.') }} VNĐ</div>
                </div>
                <div class="summary-box">
                    <div class="summary-label">Giảm giá và thuế</div>
                    <div class="summary-value">{{ number_format((float) $hoaDon->thue - (float) $hoaDon->giam_gia, 0, ',', '.') }} VNĐ</div>
                </div>
                <div class="summary-box">
                    <div class="summary-label">Còn lại theo hóa đơn</div>
                    <div class="summary-value {{ $soTienConLai > 0 ? 'text-danger' : 'text-success' }}">
                        {{ number_format((float) $soTienConLai, 0, ',', '.') }} VNĐ
                    </div>
                </div>
            </div>
        </section>

        <div class="row g-4">
            <div class="col-lg-5">
                <section class="invoice-card h-100">
                    @if($hoaDon->trang_thai === 'da_huy')
                        <h2 class="h5 mb-2">Trạng thái thanh toán</h2>
                        <p class="text-muted small">Hóa đơn đã hủy nên không thể phát sinh thêm yêu cầu thanh toán.</p>
                        <div class="alert alert-secondary mb-0">Hóa đơn đã hủy nên không thể tiếp tục gửi yêu cầu thanh toán.</div>
                    @elseif(!$coTheGuiThanhToan)
                        <h2 class="h5 mb-2">Thanh toán đã hoàn tất</h2>
                        <p class="text-muted small">Hóa đơn này đã đủ tiền.</p>
                        <div class="alert alert-success mb-0">Không còn khoản nào có thể gửi thêm.</div>
                    @else
                        <h2 class="h5 mb-2">Gửi yêu cầu thanh toán</h2>
                        <p class="text-muted small">
                            Chọn loại giao dịch rồi gửi yêu cầu.
                        </p>

                        <form method="POST" action="{{ route('booking.thanh-toan.store', $hoaDon) }}" class="row g-3">
                            @csrf

                            <div class="col-12">
                                <label class="form-label">Mục đích gửi tiền</label>
                                <div class="intent-grid">
                                    @if($coTheDatCoc)
                                        <label class="intent-card">
                                            <input type="radio" name="loai_yeu_cau" value="coc_phong" class="form-check-input mt-1" @checked($loaiYeuCauDangChon === 'coc_phong')>
                                            <div>
                                                <div class="fw-semibold">Cọc phòng</div>
                                                <div class="small text-muted">Tối đa {{ number_format((float) $soTienConThieuCoc, 0, ',', '.') }} VNĐ.</div>
                                            </div>
                                        </label>
                                    @endif

                                    <label class="intent-card">
                                        <input type="radio" name="loai_yeu_cau" value="thanh_toan_them" class="form-check-input mt-1" @checked($loaiYeuCauDangChon === 'thanh_toan_them')>
                                        <div>
                                            <div class="fw-semibold">Thanh toán thêm</div>
                                            <div class="small text-muted">Thanh toán thêm cho hóa đơn.</div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Số tiền gửi</label>
                                <input
                                    id="customer-payment-amount"
                                    type="number"
                                    min="1000"
                                    step="1000"
                                    name="so_tien"
                                    class="form-control"
                                    value="{{ old('so_tien', $soTienMacDinh) }}"
                                    required
                                >
                                <div class="form-text">Tối đa {{ number_format((float) $soTienConLaiCoTheGuiYeuCau, 0, ',', '.') }} VNĐ.</div>

                                <div class="quick-actions">
                                    @if($coTheDatCoc)
                                        <button type="button" class="btn btn-sm btn-outline-info js-apply-amount" data-amount="{{ (int) $soTienMacDinhChoCoc }}" data-mode="coc_phong">
                                            Điền mức cọc {{ number_format((float) $soTienMacDinhChoCoc, 0, ',', '.') }} VNĐ
                                        </button>
                                    @endif

                                    <button type="button" class="btn btn-sm btn-outline-secondary js-apply-amount" data-amount="{{ (int) $soTienMacDinhChoThanhToan }}" data-mode="thanh_toan_them">
                                        Điền phần còn lại
                                    </button>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Phương thức</label>
                                <select name="phuong_thuc_thanh_toan" class="form-select" required>
                                    <option value="chuyen_khoan" @selected(old('phuong_thuc_thanh_toan', 'chuyen_khoan') === 'chuyen_khoan')>Chuyển khoản</option>
                                    <option value="the" @selected(old('phuong_thuc_thanh_toan') === 'the')>Thẻ</option>
                                    <option value="vi_dien_tu" @selected(old('phuong_thuc_thanh_toan') === 'vi_dien_tu')>Ví điện tử</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Mã tham chiếu giao dịch</label>
                                <input type="text" name="ma_tham_chieu" class="form-control" value="{{ old('ma_tham_chieu') }}" placeholder="Ví dụ: mã ngân hàng, QR...">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Ghi chú</label>
                                <textarea name="ghi_chu" rows="3" class="form-control" placeholder="Mô tả thêm nếu cần">{{ old('ghi_chu') }}</textarea>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-brand w-100">Gửi yêu cầu thanh toán</button>
                            </div>
                        </form>
                    @endif
                </section>
            </div>

            <div class="col-lg-7">
                <section class="invoice-card h-100">
                    <div class="d-flex flex-wrap justify-content-between gap-3 mb-3">
                        <div>
                            <h2 class="h5 mb-1">Lịch sử giao dịch</h2>
                            <p class="text-muted small mb-0">Các giao dịch của hóa đơn.</p>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Giao dịch</th>
                                    <th>Số tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Xử lý</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($hoaDon->thanhToan->sortByDesc('id') as $thanhToan)
                                    @php
                                        $chipGiaoDich = $mapTrangThaiThanhToan[$thanhToan->trang_thai] ?? 'chip-neutral';
                                    @endphp
                                    <tr>
                                        <td style="min-width: 240px;">
                                            <div class="fw-semibold">{{ $thanhToan->ma_thanh_toan }}</div>
                                            <div class="small text-muted">
                                                {{ \App\Support\HienThiGiaTri::nhanGiaTri($thanhToan->nguon_tao) }} - {{ \App\Support\HienThiGiaTri::nhanGiaTri($thanhToan->phuong_thuc_thanh_toan) }}
                                            </div>
                                            @if($thanhToan->ma_tham_chieu)
                                                <div class="small text-muted">Tham chiếu: {{ $thanhToan->ma_tham_chieu }}</div>
                                            @endif
                                            @if($thanhToan->ghi_chu)
                                                <div class="small text-muted">{{ $thanhToan->ghi_chu }}</div>
                                            @endif
                                        </td>
                                        <td class="fw-semibold">{{ number_format((float) $thanhToan->so_tien, 0, ',', '.') }} VNĐ</td>
                                        <td><span class="chip-inline {{ $chipGiaoDich }}">{{ \App\Support\HienThiGiaTri::nhanGiaTri($thanhToan->trang_thai) }}</span></td>
                                        <td style="min-width: 220px;">
                                            <div class="small text-muted">Gửi lúc {{ optional($thanhToan->thoi_diem_thanh_toan)->format('d/m/Y H:i') ?? '-' }}</div>
                                            @if($thanhToan->nguoiXuLy)
                                                <div class="small text-muted">Xử lý bởi {{ $thanhToan->nguoiXuLy->ho_ten }}</div>
                                                <div class="small text-muted">{{ optional($thanhToan->thoi_diem_xu_ly)->format('d/m/Y H:i') ?? '-' }}</div>
                                            @elseif($thanhToan->trang_thai === 'cho_xu_ly')
                                                <div class="small text-warning">Đang chờ xác nhận</div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">Chưa có giao dịch nào cho hóa đơn này.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.js-apply-amount').forEach((button) => {
            button.addEventListener('click', () => {
                const amountInput = document.getElementById('customer-payment-amount');
                const amount = button.dataset.amount;
                const mode = button.dataset.mode;

                if (amountInput) {
                    amountInput.value = amount;
                }

                if (mode) {
                    const radio = document.querySelector(`input[name="loai_yeu_cau"][value="${mode}"]`);
                    if (radio) {
                        radio.checked = true;
                    }
                }
            });
        });
    </script>
@endpush
