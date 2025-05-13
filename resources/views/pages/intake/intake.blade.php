@extends('layouts.admin')

@section('content')
<div class="container">
    <h2 class="mb-4">🛒 Product Intake</h2>

    <form action="{{ route('intake.store') }}" method="POST" id="intakeForm">
        @csrf

        {{-- Product Selection --}}
        <div class="row mb-3">
            <div class="col-md-4">
                <label>Product</label>
                <select id="productSelect" class="form-control">
                    <option value="">-- Select Product --</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}"
                            data-name="{{ $product->name }}"
                            data-price="{{ $product->price }}">
                            {{ $product->name }} ({{ $product->price }} UZS)
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label>Qty</label>
                <input type="number" id="productQty" class="form-control" min="1" value="1">
            </div>

            <div class="col-md-2">
                <label>&nbsp;</label>
                <button type="button" class="btn btn-success w-100" onclick="addProduct()">➕ Add</button>
            </div>
        </div>

        {{-- Product List Table --}}
        <div class="table-responsive mb-3">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="productList"></tbody>
            </table>
        </div>

        {{-- Transaction Details --}}
        <div class="row mb-4">
            <div class="col-md-3">
                <label>Transaction Type</label>
                <select name="type" class="form-control" required>
                    <option value="cash">Cash</option>
                    <option value="card">Card</option>
                    <option value="credit">Credit</option>
                </select>
            </div>

            <div class="col-md-3">
                <label>Supplier</label>
                <select name="supplier_id" class="form-control" required>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label>Paid Amount</label>
                <input type="number" name="paid_amount" class="form-control">
            </div>

            <div class="col-md-3">
                <label>Note</label>
                <input type="text" name="note" class="form-control">
            </div>
        </div>

        {{-- Total & Submit --}}
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4>Total: <span id="grandTotal">0</span> UZS</h4>
                <input type="hidden" name="total" id="totalInput">
                <input type="hidden" name="items" id="itemsInput">
            </div>
            <div>
                <button type="submit" class="btn btn-primary">💾 Save Intake</button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
let products = [];

function addProduct() {
    const select = document.getElementById('productSelect');
    const qtyInput = document.getElementById('productQty');
    const productId = select.value;
    const qty = parseInt(qtyInput.value);

    if (!productId || qty < 1) return;

    const selected = select.options[select.selectedIndex];
    const name = selected.dataset.name;
    const price = parseFloat(selected.dataset.price);

    const existing = products.find(p => p.id == productId);
    if (existing) {
        existing.qty += qty;
    } else {
        products.push({ id: productId, name: name, price: price, qty: qty });
    }

    qtyInput.value = 1;
    renderTable();
}

function changeQty(index, delta) {
    products[index].qty += delta;
    if (products[index].qty <= 0) {
        products.splice(index, 1);
    }
    renderTable();
}

function removeProduct(index) {
    products.splice(index, 1);
    renderTable();
}

function renderTable() {
    const tbody = document.getElementById('productList');
    tbody.innerHTML = '';
    let total = 0;

    products.forEach((p, i) => {
        const rowTotal = p.qty * p.price;
        total += rowTotal;

        tbody.innerHTML += `
            <tr>
                <td>
                    ${p.name}
                    <input type="hidden" name="items[${i}][id]" value="${p.id}">
                    <input type="hidden" name="items[${i}][name]" value="${p.name}">
                    <input type="hidden" name="items[${i}][price]" value="${p.price}">
                    <input type="hidden" name="items[${i}][qty]" value="${p.qty}">
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-light" onclick="changeQty(${i}, -1)">-</button>
                    <span class="mx-2">${p.qty}</span>
                    <button type="button" class="btn btn-sm btn-light" onclick="changeQty(${i}, 1)">+</button>
                </td>
                <td>${p.price}</td>
                <td>${rowTotal}</td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="removeProduct(${i})">Remove</button></td>
            </tr>
        `;
    });

    document.getElementById('grandTotal').innerText = total;
    document.getElementById('totalInput').value = total;
    document.getElementById('itemsInput').value = JSON.stringify(products);
}
</script>
@endsection
