@extends('layouts.app')

@section('title', 'Tambah Transaksi - FinTrack')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card app-card">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="bi bi-plus-circle-fill text-primary me-2"></i>Tambah Transaksi</h5>
                <span class="badge bg-primary-subtle text-primary">Akses Penuh User</span>
            </div>
            <div class="card-body p-4">
                @include('transactions._form', [
                    'action' => route('transactions.store'),
                    'method' => 'POST',
                    'submitLabel' => 'Simpan Transaksi',
                ])
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.date').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
        });
    });
</script>
@endsection
