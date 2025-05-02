@extends('layouts.admin')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-sm-flex justify-content-between align-items-start mb-4">
                <div>
                    <h4 class="card-title card-title-dash">Brand Management</h4>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <button class="btn btn-primary text-white mb-0 me-0" data-bs-toggle="modal" data-bs-target="#newBrandModal"
                        type="button">
                        <i class="mdi mdi-plus"></i> Add New
                    </button>
                    <a href="#" class="btn btn-primary text-sm">
                        <i class="mdi mdi-download"></i> Export
                    </a>
                    <a href="#" class="btn btn-secondary text-sm">
                        <i class="mdi mdi-printer-outline"></i> Print
                    </a>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="mb-4">
                <form method="GET" action="{{ route('brands.index') }}">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" name="search" class="form-control" placeholder="Search by name or phone"
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                        @if (request()->filled('search'))
                            <div class="col-md-2">
                                <a href="{{ route('brands.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                            </div>
                        @endif
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Photo</th>
                            <th>Phone</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($brands as $brand)
                            <tr>
                                <td>{{ $loop->iteration + ($brands->currentPage() - 1) * $brands->perPage() }}</td>
                                <td>{{ $brand->name }}</td>
                                <td>
                                    <img src="{{ $brand->photo ? Storage::url($brand->photo) : asset('admin/assets/images/default_product.png') }}"
                                        alt="{{ $brand->name }}" class="img-thumbnail"
                                        style="width: 60px; height: 60px; object-fit: cover;">
                                </td>
                                <td>{{ $brand->phone ?? '-' }}</td>
                                <td>{{ Str::limit($brand->description, 50) }}</td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#viewBrandModal"
                                            data-brand="{{ json_encode($brand) }}" onclick="viewBrand(this)">
                                            <i class="mdi mdi-eye icon-sm text-info"></i>
                                        </a>
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#editBrandModal"
                                            data-brand="{{ json_encode($brand) }}" onclick="editBrand(this)">
                                            <i class="mdi mdi-pencil icon-sm text-primary"></i>
                                        </a>
                                        <a href="javascript:void(0);" title="Удалить" data-bs-toggle="modal"
                                            data-bs-target="#deleteBrandModal" data-id="{{ $brand->id }}"
                                            onclick="openModal(this)">
                                            <i class="mdi mdi-delete icon-sm text-danger"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No brands found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($brands->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <p class="text-muted mb-0">
                        Showing {{ $brands->firstItem() }} to {{ $brands->lastItem() }} of
                        {{ $brands->total() }} results
                    </p>
                    <div class="pagination mb-0">
                        {{ $brands->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    
    @include('pages.brands.modals.delete-brand')
    @include('pages.brands.modals.new-brand')
    @include('pages.brands.modals.edit-brand')
    {{-- @include('pages.brands.modals.view-brand') --}}


    <script>
        function openModal(element) {
            var id = element.getAttribute('data-id');
            var photo = element.getAttribute('data-photo');
            var name = element.getAttribute('data-name');
            const modalId = element.getAttribute('data-bs-target');

           
            if (modalId === '#deleteBrandModal') {
                document.getElementById('delete-brand-form').action = `/brands/${id}`;
            }
        }
    </script>
@endsection
