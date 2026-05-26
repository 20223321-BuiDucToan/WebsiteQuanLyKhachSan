@extends('layouts.admin')

@section('title', 'Cập nhật loại phòng')

@section('content')
    <div class="form-page">
        <div class="form-page-header">
            <div>
                <h2 class="form-page-title">Cập nhật loại phòng {{ $loaiPhong->ten_loai_phong }}</h2>
                <p class="form-page-subtitle">Chỉ hiển thị những nhóm thông tin quan trọng để bạn chỉnh giá, sức chứa và tiện ích nhanh hơn.</p>
            </div>
        </div>

        <div class="form-shell">
            <div class="form-shell__body">
                <form action="{{ route('loai-phong.update', $loaiPhong) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @include('loai_phong._form')

                    <div class="form-actions">
                        <button type="submit" class="btn btn-gradient">
                            <i class="fa-solid fa-floppy-disk me-2"></i>Lưu thay đổi
                        </button>
                        <a href="{{ route('loai-phong.index') }}" class="btn btn-soft">Quay lại</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
