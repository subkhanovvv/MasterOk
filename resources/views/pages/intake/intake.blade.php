@extends('layouts.admin')

@section('content')
<div class="card mb-4">
    <div class="card-header">
        <h4>New Product Intake</h4>
    </div>
    <div class="card-body">
        <form>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Product</label>
                    <select class="form-select">
                        <option selected>Select Product</option>
                        @foreach ($products as $p)
                            
                        <option value="{{$p->id}}">{{$p->name}}</option>
                        @endforeach
                   
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Quantity</label>
                    <input type="number" class="form-control" value="1">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Unit</label>
                    <select class="form-select">
                        <option selected>pcs</option>
                        <option>box</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Price per Unit</label>
                    <input type="text" class="form-control" value="150000">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Supplier</label>
                    <input type="text" class="form-control" value="Sample Supplier">
                </div>
            </div>

            <div class="mt-4 text-end">
                <button type="button" class="btn btn-success">➕ Add to Table</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h4>Items to be Submitted</h4>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-striped mb-0 text-center">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>Price/Unit</th>
                        <th>Total</th>
                        <th>Supplier</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>USB-C Charger</td>
                        <td>USBC-65W</td>
                        <td>
                            <div class="d-flex justify-content-center align-items-center">
                                <button class="btn btn-sm btn-outline-secondary me-1">−</button>
                                <span class="px-2">3</span>
                                <button class="btn btn-sm btn-outline-secondary ms-1">+</button>
                            </div>
                        </td>
                        <td>pcs</td>
                        <td>150,000 UZS</td>
                        <td>450,000 UZS</td>
                        <td>ChargerPro</td>
                        <td>
                            <button class="btn btn-sm btn-danger">🗑 Remove</button>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Samsung Galaxy S22</td>
                        <td>SGS22-WHT</td>
                        <td>
                            <div class="d-flex justify-content-center align-items-center">
                                <button class="btn btn-sm btn-outline-secondary me-1">−</button>
                                <span class="px-2">1</span>
                                <button class="btn btn-sm btn-outline-secondary ms-1">+</button>
                            </div>
                        </td>
                        <td>box</td>
                        <td>8,200,000 UZS</td>
                        <td>8,200,000 UZS</td>
                        <td>Samsung Distributors</td>
                        <td>
                            <button class="btn btn-sm btn-danger">🗑 Remove</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="p-3 text-end">
            <strong>Total: 8,650,000 UZS</strong><br>
            <button class="btn btn-primary mt-2">✅ Submit All</button>
        </div>
    </div>
</div>
@endsection
