@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="container-fluid">
    <h2 class="fw-bold mb-4">Dashboard Admin</h2>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total User</p>
                        <h3 class="fw-bold">{{ $totalUsers }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                        <i class="fas fa-users fs-4 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Lokasi</p>
                        <h3 class="fw-bold">{{ $totalLokasi }}</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                        <i class="fas fa-map-marker-alt fs-4 text-success"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Penanggung Jawab</p>
                        <h3 class="fw-bold">{{ $totalPenanggungJawab }}</h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                        <i class="fas fa-user-tie fs-4 text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection