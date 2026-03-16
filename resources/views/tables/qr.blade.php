{{-- @extends('layouts.app')

@section('content')
<div class="container text-center">
    <h1>Scan QR untuk Pesan</h1>
    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ url('/order?table='.$table) }}" alt="QR Code">
</div>
@endsection --}}

{{-- @extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <h1>Scan QR untuk Pesan</h1>

            <div class="d-flex justify-content-center">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ url('/order?table='.$table) }}" alt="QR Code">
            </div>
        </div>
    </div>
</div>
@endsection --}}

@extends('layouts.app')

@section('content')
<div class="container" style="padding-top: 80px;">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <h1 style="margin-bottom: 50px;">Scan QR untuk Pesan</h1>
            <div class="d-flex justify-content-center">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ url('/order?table='.$table) }}" alt="QR Code">
            </div>
        </div>
    </div>
</div>
@endsection