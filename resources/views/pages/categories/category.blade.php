@extends('layouts.admin')

@section('content')
    {{-- Category header --}}
    <div class="card mb-2">
        <div class="card-body">
            @include('pages.categories.partials.category-header')
        </div>
    </div>
    {{-- Category table --}}
    <div class="card">
        <div class="card-body">
            @include('pages.categories.partials.category-table')
            @include('pages.categories.partials.category-footer')
        </div>
    </div>
    {{-- Modals & Js --}}
    @include('pages.categories.modals.delete-category')
    @include('pages.categories.modals.edit-category')
    @include('pages.categories.modals.new-category')
    @include('pages.categories.js.script')
@endsection
