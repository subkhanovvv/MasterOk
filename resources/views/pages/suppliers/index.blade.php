@extends('layouts.admin')

@section('content')
    {{-- Supplier header --}}
    <div class="card mb-2">
        <div class="card-body">
            @include('pages.suppliers.partials.supplier-header')
        </div>
    </div>
    {{-- Supplier table --}}
    <div class="card">
        <div class="card-body">
            @include('pages.suppliers.partials.supplier-table')
            @include('pages.suppliers.partials.supplier-footer')
        </div>
    </div>
    {{-- Modals & Js --}}
    @include('pages.suppliers.modals.delete-supplier')
    @include('pages.suppliers.modals.edit-supplier')
    @include('pages.suppliers.modals.new-supplier')
    @include('pages.suppliers.js.script')
@endsection
