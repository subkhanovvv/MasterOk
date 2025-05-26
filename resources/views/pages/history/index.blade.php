@extends('layouts.admin')

@section('content')
    {{-- History header --}}
    <div class="card mb-2">
        <div class="card-body">
            @include('pages.history.partials.history-header')
        </div>
    </div>
    {{-- History table --}}
    <div class="card">
        <div class="card-body">
            @include('pages.history.partials.history-table')
            @include('pages.history.partials.history-footer')
        </div>
    </div>
    {{-- Modals & Js --}}
    @include('pages.history.modals.view-transaction')
    @include('pages.history.js.script')
@endsection
