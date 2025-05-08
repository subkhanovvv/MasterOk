@extends('layouts.admin')

@section('content')
    {{-- Product header --}}
    <div class="card mb-2">
        <div class="card-body">
            @include('pages.products.partials.product-header')
        </div>
    </div>
    {{-- Product table --}}
    <div class="card">
        <div class="card-body">
            @include('pages.products.partials.product-table')
            @include('pages.products.partials.product-footer')
        </div>
    </div>
    {{-- Modals & Js --}}
    @include('pages.products.modals.delete-product')
    @include('pages.products.modals.edit-product')
    @include('pages.products.modals.new-product')
    @include('pages.products.modals.view-product')
    @include('pages.products.js.script')
@endsection
