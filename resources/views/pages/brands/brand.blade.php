@extends('layouts.admin')

@section('content')
    {{-- Brand header --}}
    <div class="card mb-2">
        <div class="card-body">
            @include('pages.brands.partials.brand-header')
        </div>
    </div>
    {{-- Brand table --}}
    <div class="card">
        <div class="card-body">
            @include('pages.brands.partials.brand-table')
            @include('pages.brands.partials.brand-footer')
        </div>
    </div>
    <!-- Modals -->
    @include('pages.brands.modals.delete-brand')
    @include('pages.brands.modals.edit-brand')
    @include('pages.brands.modals.new-brand')
    @include('pages.brands.js.script')
    <script src="{{ asset('admin/assets/js/phone-number-format.js') }}"></script>

@endsection
