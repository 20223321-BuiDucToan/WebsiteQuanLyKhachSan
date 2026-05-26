@extends('layouts.admin')

@section('title', 'Thêm dịch vụ')

@section('content')
    <div class="form-page">
        <div class="form-page-header">
            <div>
                <h2 class="form-page-title">Thêm dịch vụ</h2>
                <p class="form-page-subtitle">Biểu mẫu được rút gọn để nhân viên nhập nhanh tên dịch vụ, đơn giá và trạng thái sử dụng.</p>
            </div>
        </div>

        <div class="form-shell">
            <div class="form-shell__body">
                <form action="{{ route('dich-vu.store') }}" method="POST" class="row g-4">
                    @csrf

                    @include('dich_vu._form', ['dichVu' => $dichVu])

                    <div class="col-12 form-actions">
                        <button type="submit" class="btn btn-gradient">
                            <i class="fa-solid fa-floppy-disk me-2"></i>Lưu dịch vụ
                        </button>
                        <a href="{{ route('dich-vu.index') }}" class="btn btn-soft">Quay lại</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
