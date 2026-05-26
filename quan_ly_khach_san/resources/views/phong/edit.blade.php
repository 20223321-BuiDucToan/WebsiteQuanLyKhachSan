@extends('layouts.admin')

@section('title', 'Cập nhật phòng')

@section('content')
    <div class="form-page">
        <div class="form-page-header">
            <div>
                <h2 class="form-page-title">Cập nhật phòng {{ $phong->so_phong }}</h2>
                <p class="form-page-subtitle">Chỉ hiển thị những phần cần sửa trực tiếp để thao tác nhanh và bớt rối mắt hơn.</p>
            </div>
        </div>

        <div class="form-shell">
            <div class="form-shell__body">
                <form action="{{ route('phong.update', $phong) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    @include('phong._form')

                    <div class="form-actions">
                        <button type="submit" class="btn btn-gradient">
                            <i class="fa-solid fa-floppy-disk me-2"></i>Lưu thay đổi
                        </button>
                        <a href="{{ route('phong.index') }}" class="btn btn-soft">Quay lại</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
