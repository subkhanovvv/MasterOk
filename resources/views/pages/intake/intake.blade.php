@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Product Intake</h1>
    
    <form method="POST" action="{{ route('product-activities.store') }}">
        @csrf
        
        <div class="card mb-4">
            <div class="card-header">Intake Information</div>
            <div class="card-body">
                <!-- Intake Type Selection -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="type" class="form-label">Intake Type*</label>
                        <select class="form-select" name="type" id="type" required>
                            <option value="intake">Regular Intake</option>
                            <option value="intake_loan">Loan Intake</option>
                            <option value="intake_return">Return Intake</option>
                        </select>
                    </div>
                </div>
                
                <!-- Loan Information (shown only for intake_loan) -->
                <div id="loan-info" style="display:none;">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="loan_direction" class="form-label">Loan Direction*</label>
                            <select class="form-select" name="loan_direction" id="loan_direction">
                                <option value="given">Given (to client)</option>
                                <option value="taken">Taken (from individual)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="client_name" class="form-label">Client Name</label>
                            <input type="text" class="form-control" name="client_name" id="client_name">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="client_phone" class="form-label">Client Phone</label>
                            <input type="text" class="form-control" name="client_phone" id="client_phone">
                        </div>
                        <div class="col-md-6">
                            <label for="loan_amount" class="form-label">Loan Amount</label>
                            <input type="number" step="0.01" class="form-control" name="loan_amount" id="loan_amount">
                        </div>
                    </div>
                </div>
                
                <!-- Return Information (shown only for intake_return) -->
                <div id="return-info" style="display:none;">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="return_reason" class="form-label">Return Reason</label>
                            <input type="text" class="form-control" name="return_reason" id="return_reason">
                        </div>
                    </div>
                </div>
                
                <!-- Payment Information -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="payment_type" class="form-label">Payment Type*</label>
                        <select class="form-select" name="payment_type" id="payment_type" required>
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="paid_amount" class="form-label">Paid Amount</label>
                        <input type="number" step="0.01" class="form-control" name="paid_amount" id="paid_amount" required>
                    </div>
                </div>
                
                <!-- Supplier Information -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="supplier_id" class="form-label">Supplier</label>
                        <select class="form-select" name="supplier_id" id="supplier_id">
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <!-- Notes -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="note" class="form-label">Notes</label>
                        <textarea class="form-control" name="note" id="note" rows="3"></textarea>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Products Section -->
        <div class="card mb-4">
            <div class="card-header">Products</div>
            <div class="card-body">
                <!-- Barcode Input -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <label for="barcode" class="form-label">Scan Barcode</label>
                        <input type="text" class="form-control" name="barcode" id="barcode" placeholder="Scan or enter barcode">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary" name="action" value="add_barcode">Add Product</button>
                    </div>
                </div>
                
                <!-- Manual Product Add -->
                <div class="row mb-4">
                    <div class="col-md-5">
                        <label for="product_name" class="form-label">Product Name</label>
                        <input type="text" class="form-control" name="product_name" id="product_name">
                    </div>
                    <div class="col-md-3">
                        <label for="product_qty" class="form-label">Quantity</label>
                        <input type="number" step="0.01" class="form-control" name="product_qty" id="product_qty">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-secondary" name="action" value="add_manual">Add Product</button>
                    </div>
                </div>
                
                <!-- Products Table -->
                @if(session('products'))
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Unit</th>
                                    <th>Price (UZS)</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(session('products') as $index => $product)
                                <tr>
                                    <td>{{ $product['name'] }}</td>
                                    <td>{{ $product['qty'] }}</td>
                                    <td>{{ $product['unit'] }}</td>
                                    <td>{{ number_format($product['price_uzs'], 2) }}</td>
                                    <td>{{ number_format($product['qty'] * $product['price_uzs'], 2) }}</td>
                                    <td>
                                        <button type="submit" class="btn btn-sm btn-danger" name="action" value="remove_{{ $index }}">Remove</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p>No products added yet.</p>
                @endif
            </div>
        </div>
        
        <!-- Submit Button -->
        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-success btn-lg" name="action" value="submit">Complete Intake</button>
        </div>
    </form>
</div>

<!-- Simple CSS-based show/hide for loan/return sections -->
<style>
    #type[value="intake_loan"] ~ #loan-info,
    #type[value="intake_return"] ~ #return-info {
        display: block;
    }
</style>

<!-- Simple form handling for showing/hiding sections -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const loanInfo = document.getElementById('loan-info');
    const returnInfo = document.getElementById('return-info');
    
    function toggleSections() {
        if (typeSelect.value === 'intake_loan') {
            loanInfo.style.display = 'block';
            returnInfo.style.display = 'none';
        } else if (typeSelect.value === 'intake_return') {
            loanInfo.style.display = 'none';
            returnInfo.style.display = 'block';
        } else {
            loanInfo.style.display = 'none';
            returnInfo.style.display = 'none';
        }
    }
    
    typeSelect.addEventListener('change', toggleSections);
    toggleSections(); // Initialize on page load
});
</script>
@endsection