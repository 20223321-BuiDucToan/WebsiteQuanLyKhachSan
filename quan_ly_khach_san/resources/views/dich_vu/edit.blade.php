@extends('layouts.admin')

@section('title', 'Cập nhật dịch vụ')

@section('content')
    <div class="form-page">
        <div class="form-page-header">
            <div>
                <h2 class="form-page-title">Cập nhật dịch vụ {{ $dichVu->ma_dich_vu }}</h2>
                <p class="form-page-subtitle">Giữ lại những trường cần thao tác thường xuyên để chỉnh tên, giá và trạng thái nhanh hơn.</p>
            </div>
        </div>

        <div class="form-shell">
            <div class="form-shell__body">
                <form action="{{ route('dich-vu.update', $dichVu) }}" method="POST" class="row g-4">
                    @csrf
                    @method('PATCH')

                    @include('dich_vu._form', ['dichVu' => $dichVu])

                    <div class="col-12 form-actions">
                        <button type="submit" class="btn btn-gradient">
                            <i class="fa-solid fa-floppy-disk me-2"></i>Lưu thay đổi
                        </button>
                        <a href="{{ route('dich-vu.index') }}" class="btn btn-soft">Quay lại</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
