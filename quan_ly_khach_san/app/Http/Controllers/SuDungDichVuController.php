<?php

namespace App\Http\Controllers;

use App\Models\DatPhong;
use App\Models\DichVu;
use App\Models\HoaDon;
use App\Models\SuDungDichVu;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SuDungDichVuController extends Controller
{
    private const TRANG_THAI_CO_THE_GHI_NHAN = [
        'da_xac_nhan',
        'da_nhan_phong',
        'da_tra_phong',
    ];

    public function store(Request $request, DatPhong $datPhong)
    {
        $hoaDonHienTai = $this->xacThucChoPhepCapNhatDichVu($datPhong);
        $duLieu = $this->xacThucSuDungDichVu($request);
        $dichVu = DichVu::query()->findOrFail((int) $duLieu['dich_vu_id']);

        if ($dichVu->trang_thai !== 'hoat_dong') {
            throw ValidationException::withMessages([
                'dich_vu_id' => 'Dich vu nay hien khong con hoat dong.',
            ]);
        }

        $donGia = array_key_exists('don_gia', $duLieu) && $duLieu['don_gia'] !== null
            ? (float) $duLieu['don_gia']
            : (float) $dichVu->don_gia;
        $soLuong = (int) $duLieu['so_luong'];

        $datPhong->suDungDichVu()->create([
            'dich_vu_id' => $dichVu->id,
            'so_luong' => $soLuong,
            'don_gia' => $donGia,
            'thanh_tien' => $donGia * $soLuong,
            'thoi_diem_su_dung' => $duLieu['thoi_diem_su_dung'] ?? now(),
            'nguoi_tao_id' => auth()->id(),
            'ghi_chu' => $duLieu['ghi_chu'] ?? null,
        ]);

        $this->dongBoHoaDonNeuCo($hoaDonHienTai, $datPhong);

        return redirect()
            ->back()
            ->with('success', 'Da ghi nhan dich vu cho don dat phong.');
    }

    public function update(Request $request, DatPhong $datPhong, SuDungDichVu $suDungDichVu)
    {
        $this->xacNhanThuocDonDatPhong($datPhong, $suDungDichVu);

        $hoaDonHienTai = $this->xacThucChoPhepCapNhatDichVu($datPhong);
        $duLieu = $this->xacThucSuDungDichVu($request);
        $dichVu = DichVu::query()->findOrFail((int) $duLieu['dich_vu_id']);

        if ($dichVu->trang_thai !== 'hoat_dong' && $dichVu->id !== $suDungDichVu->dich_vu_id) {
            throw ValidationException::withMessages([
                'dich_vu_id' => 'Dich vu moi chon hien khong con hoat dong.',
            ]);
        }

        $donGia = array_key_exists('don_gia', $duLieu) && $duLieu['don_gia'] !== null
            ? (float) $duLieu['don_gia']
            : (float) $dichVu->don_gia;
        $soLuong = (int) $duLieu['so_luong'];

        $suDungDichVu->update([
            'dich_vu_id' => $dichVu->id,
            'so_luong' => $soLuong,
            'don_gia' => $donGia,
            'thanh_tien' => $donGia * $soLuong,
            'thoi_diem_su_dung' => $duLieu['thoi_diem_su_dung'] ?? now(),
            'ghi_chu' => $duLieu['ghi_chu'] ?? null,
        ]);

        $this->dongBoHoaDonNeuCo($hoaDonHienTai, $datPhong);

        return redirect()
            ->back()
            ->with('success', 'Cap nhat dich vu su dung thanh cong.');
    }

    public function destroy(DatPhong $datPhong, SuDungDichVu $suDungDichVu)
    {
        $this->xacNhanThuocDonDatPhong($datPhong, $suDungDichVu);

        $hoaDonHienTai = $this->xacThucChoPhepCapNhatDichVu($datPhong);

        $suDungDichVu->delete();

        $this->dongBoHoaDonNeuCo($hoaDonHienTai, $datPhong);

        return redirect()
            ->back()
            ->with('success', 'Da xoa dong dich vu khoi don dat phong.');
    }

    private function xacThucSuDungDichVu(Request $request): array
    {
        return $request->validate([
            'dich_vu_id' => ['required', 'integer', 'exists:dich_vu,id'],
            'so_luong' => ['required', 'integer', 'min:1', 'max:999'],
            'don_gia' => ['nullable', 'numeric', 'min:0'],
            'thoi_diem_su_dung' => ['nullable', 'date'],
            'ghi_chu' => ['nullable', 'string', 'max:1000'],
        ]);
    }

    private function xacThucChoPhepCapNhatDichVu(DatPhong $datPhong): ?HoaDon
    {
        if (!in_array($datPhong->trang_thai, self::TRANG_THAI_CO_THE_GHI_NHAN, true)) {
            throw ValidationException::withMessages([
                'dich_vu_id' => 'Chi duoc ghi nhan dich vu khi don da xac nhan, dang luu tru hoac vua tra phong.',
            ]);
        }

        $hoaDonHienTai = $datPhong->hoaDon()
            ->where('trang_thai', '!=', 'da_huy')
            ->latest('id')
            ->first();

        if ($hoaDonHienTai?->trang_thai === 'da_thanh_toan') {
            throw ValidationException::withMessages([
                'dich_vu_id' => 'Hoa don hien tai da thanh toan day du. Khong the thay doi dich vu nua.',
            ]);
        }

        return $hoaDonHienTai;
    }

    private function xacNhanThuocDonDatPhong(DatPhong $datPhong, SuDungDichVu $suDungDichVu): void
    {
        if ($suDungDichVu->dat_phong_id !== $datPhong->id) {
            abort(404);
        }
    }

    private function dongBoHoaDonNeuCo(?HoaDon $hoaDonHienTai, DatPhong $datPhong): void
    {
        $hoaDonCanDongBo = $hoaDonHienTai
            ?? $datPhong->hoaDon()
                ->where('trang_thai', '!=', 'da_huy')
                ->latest('id')
                ->first();

        if ($hoaDonCanDongBo) {
            $hoaDonCanDongBo->loadMissing([
                'datPhong.chiTietDatPhong',
                'datPhong.suDungDichVu',
                'thanhToan',
            ]);
            $hoaDonCanDongBo->dongBoGiaTriTuDatPhong();
        }
    }
}
