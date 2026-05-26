@extends('layouts.admin')

@section('title', 'Cập nhật người dùng')

@section('content')
    <div class="form-page">
        <div class="form-page-header">
            <div>
                <h2 class="form-page-title">Cập nhật người dùng</h2>
                <p class="form-page-subtitle">Giảm bớt chi tiết thừa để việc sửa thông tin, đổi vai trò và trạng thái diễn ra nhanh hơn.</p>
            </div>
        </div>

        <div class="form-shell">
            <div class="form-shell__body">
                <form action="{{ route('nguoi-dung.update', $nguoiDung->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @include('nguoi_dung._form')

                    <div class="form-actions">
                        <button type="submit" class="btn btn-gradient">
                            <i class="fa-solid fa-floppy-disk me-2"></i>Lưu thay đổi
                        </button>
                        <a href="{{ route('nguoi-dung.index') }}" class="btn btn-soft">Quay lại</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
