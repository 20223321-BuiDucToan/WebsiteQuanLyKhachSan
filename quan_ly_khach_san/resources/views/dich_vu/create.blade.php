@extends('layouts.admin')

@section('title', 'Them dich vu')

@section('content')
    <div class="mb-4">
        <h2 class="section-title">Them dich vu</h2>
        <p class="section-subtitle">Tao dich vu moi de nhan vien co the ghi nhan truc tiep vao tung don dat phong.</p>
    </div>

    <div class="premium-card">
        <div class="card-body p-4">
            <form action="{{ route('dich-vu.store') }}" method="POST" class="row g-4">
                @csrf

                @include('dich_vu._form', ['dichVu' => $dichVu])

                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-gradient">
                        <i class="fa-solid fa-floppy-disk me-2"></i>Luu dich vu
                    </button>
                    <a href="{{ route('dich-vu.index') }}" class="btn btn-soft">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
@endsection
