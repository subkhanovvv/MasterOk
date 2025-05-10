@extends('layouts.admin')

@section('content')
    {{-- Barcode header --}}
    <div class="card mb-2">
        <div class="card-body">
            @include('pages.barcodes.partials.barcode-header')
        </div>
    </div>
    {{-- Brand table --}}
    <div class="card">
        <div class="card-body">
            @include('pages.barcodes.partials.barcode-table')
            @include('pages.barcodes.partials.barcode-footer')
        </div>
    </div>
    @include('pages.barcodes.modals.print-barcode')
    @include('pages.barcodes.js.script')
@endsection
