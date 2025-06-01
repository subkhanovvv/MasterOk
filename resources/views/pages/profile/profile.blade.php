@extends('layouts.admin')

@section('content')
    <div class="card w-50 justify-content-center mx-auto pb-5 pt-2 mb-5">
        <div class="card-body">
            <div class="d-flex align-items-center mb-4 gap-3">
                <a href="javascript::void(0);" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                <img src="{{ asset('admin/assets/images/edit_profile.png') }}" class="rounded-circle" alt="Profile Image"
                    width="100" height="100"></a>
                <h3>Информация о профиле</h3>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <tr>
                        <th>Имя пользователя</th>
                        <td>{{ Auth::user()->name }}</td>
                    </tr>
                    <tr>
                        <th>Создано в</th>
                        <td>{{ Auth::user()->created_at ?? ''  }}</td>
                    </tr>
                    <tr>
                        <th>Обновлено в</th>
                        <td>{{ Auth::user()->updated_at ?? ''}}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    @include('pages.profile.modals.edit-profile')

@endsection
