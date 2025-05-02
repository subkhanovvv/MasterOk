@extends('layouts.admin')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-sm-flex justify-content-between align-items-start mb-4">
                <div>
                    <h4 class="card-title card-title-dash">Brand Management</h4>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <button class="btn btn-primary text-white mb-0 me-0" data-bs-toggle="modal"
                        data-bs-target="#newBrandModal" type="button">
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
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Search by name or phone" value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                        @if(request()->filled('search'))
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
                                        alt="{{ $brand->name }}" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
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
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#deleteBrandModal"
                                           data-brand-id="{{ $brand->id }}">
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

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteBrandModal" tabindex="-1" aria-labelledby="deleteBrandModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteBrandModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this brand? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <form id="deleteBrandForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('pages.brands.modals.new-brand')
    @include('pages.brands.modals.edit-brand')
    {{-- @include('pages.brands.modals.view-brand') --}}

    @push('scripts')
    <script>
        // Delete Brand Modal Setup
        $('#deleteBrandModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const brandId = button.data('brand-id');
            const form = $('#deleteBrandForm');
            form.attr('action', `/admin/brands/${brandId}`);
        });

        // View Brand Modal
        function viewBrand(element) {
            const brand = JSON.parse(element.dataset.brand);
            const modal = document.getElementById('viewBrandModal');
            
            modal.querySelector('#brandName').textContent = brand.name;
            modal.querySelector('#brandPhone').textContent = brand.phone || '-';
            modal.querySelector('#brandDescription').textContent = brand.description || '-';
            
            const img = modal.querySelector('#brandPhoto');
            img.src = brand.photo 
                ? `/storage/${brand.photo}` 
                : '{{ asset("admin/assets/images/default_product.png") }}';
            img.alt = brand.name;
        }

        // Edit Brand Modal
        function editBrand(element) {
            const brand = JSON.parse(element.dataset.brand);
            const form = document.getElementById('editBrandForm');
            
            form.action = `/admin/brands/${brand.id}`;
            form.querySelector('[name="name"]').value = brand.name;
            form.querySelector('[name="phone"]').value = brand.phone || '';
            form.querySelector('[name="description"]').value = brand.description || '';
            
            const imgPreview = form.querySelector('#editPhotoPreview');
            imgPreview.src = brand.photo 
                ? `/storage/${brand.photo}` 
                : '{{ asset("admin/assets/images/default_product.png") }}';
            imgPreview.alt = brand.name;
        }

        // Image Preview for New Brand
        document.getElementById('photo').addEventListener('change', function(e) {
            const [file] = e.target.files;
            if (file) {
                document.getElementById('photoPreview').src = URL.createObjectURL(file);
            }
        });

        // Image Preview for Edit Brand
        document.getElementById('editPhoto').addEventListener('change', function(e) {
            const [file] = e.target.files;
            if (file) {
                document.getElementById('editPhotoPreview').src = URL.createObjectURL(file);
            }
        });
    </script>
    @endpush
@endsection