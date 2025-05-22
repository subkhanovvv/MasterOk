@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1 class="mb-4">📦 Product Intake</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('intake.store') }}">
            @csrf

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="supplier_id" class="form-label">Supplier</label>
                    <select class="form-select" id="supplier_id" name="supplier_id">
                        <option value="">Select Supplier</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="payment_type" class="form-label">Payment Type</label>
                    <select class="form-select" id="payment_type" name="payment_type" required>
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="bank_transfer">Bank transfer</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="type" class="form-label">Transaction Type</label>
                    <select class="form-select" id="type" name="type" required>
                        <option value="intake">Intake</option>
                        <option value="intake_loan">Loan</option>
                        <option value="intake_return">Return</option>
                    </select>
                </div>
            </div>

            <!-- Additional fields that will show/hide based on transaction type -->
            <div id="return-fields" class="row mb-3" style="display: none;">
                <div class="col-md-12">
                    <label for="return_reason" class="form-label">Return Reason</label>
                    <textarea class="form-control" name="return_reason" id="return_reason" rows="2">{{ old('return_reason') }}</textarea>
                </div>
            </div>

            <div id="loan-fields" class="row mb-3" style="display: none;">
                <div class="col-md-6">
                    <label for="loan_amount" class="form-label">Loan Amount (UZS)</label>
                    <input type="number" class="form-control" name="loan_amount" id="loan_amount" step="0.01">
                </div>
                <div class="col-md-6">
                    <label for="loan_direction" class="form-label">Loan direction</label>
                    <select name="loan_direction" id="loan_direction" class="form-select">
                        <option value="given">Given</option>
                        <option value="taken">Taken</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="loan_due_to" class="form-label">Due Date</label>
                    <input type="date" class="form-control" name="loan_due_to" id="loan_due_to">
                </div>
            </div>

            <div class="mb-3">
                <label for="note" class="form-label">Note</label>
                <textarea class="form-control" name="note" id="note" rows="2">{{ old('note') }}</textarea>
            </div>
            <!-- Add this hidden field before your submit button -->
            <input type="hidden" name="total_price" id="total-price-hidden" value="0">
            <input type="hidden" name="total_usd" id="total-usd-hidden" value="0">
            <!-- Barcode scanner input with button -->
            <div class="mb-4 d-flex gap-2">
                <input type="text" id="barcode" class="form-control" placeholder="Scan or enter barcode..."
                    autocomplete="off" autofocus>
                <button type="button" class="btn btn-success" id="scan-button">
                    <i class="fas fa-barcode"></i> Scan
                </button>
            </div>

            <h4 class="mb-3">🧾 Product List</h4>

            <div id="products-container" class="mb-3">
                <!-- Dynamic rows will be added here -->
            </div>

            <div class="mb-3 d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" id="add-product">
                    <i class="fas fa-plus"></i> Add Product
                </button>
                <div>
                    <strong>Total UZS:</strong> <span id="total-uzs">0</span> |
                    <strong>Total USD:</strong> <span id="total-usd">0</span>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-check"></i> Submit Intake
            </button>
        </form>
    </div>

    @include('pages.consumption.js.script')
@endsection
