@extends('layouts.admin')

{{-- @section('title', 'Расход продуктов') Product Consumption --}}
@section('content')
<div class="container">
    <h2 class="mb-4">Расход продуктов</h2>

    {{-- Product Selection --}}
    <div class="card mb-4">
        <div class="card-header">Добавить продукт</div>
        <div class="card-body">
            <form id="consume-form">
                <div class="row">
                    <div class="col-md-4">
                        <label>Штрихкод</label>
                        <input type="text" id="barcode" class="form-control" placeholder="Сканируйте или введите" autofocus>
                    </div>
                    <div class="col-md-4">
                        <label>Единица</label>
                        <select id="unit" class="form-control">
                            {{-- Will be filled dynamically --}}
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Количество</label>
                        <input type="number" id="quantity" class="form-control" value="1" min="1">
                    </div>
                </div>

                <input type="hidden" id="product_id">
                <input type="hidden" id="product_name">
                <input type="hidden" id="product_price">
                <input type="hidden" id="product_barcode">

                <button type="button" class="btn btn-success mt-3" id="add-to-list">Добавить</button>
            </form>
        </div>
    </div>

    {{-- Consumption List Table --}}
    <div class="card">
        <div class="card-header">Список расходов</div>
        <div class="card-body">
            <table class="table table-bordered" id="consumption-table">
                <thead>
                    <tr>
                        <th>Название</th>
                        <th>Штрихкод</th>
                        <th>Единица</th>
                        <th>Кол-во</th>
                        <th>Цена</th>
                        <th>Итого</th>
                        <th>Удалить</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

            <div class="text-end">
                <strong>Общая сумма: <span id="total-sum">0</span> UZS</strong>
            </div>

            <button class="btn btn-primary mt-3" id="submit-consumption">Сохранить</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let products = @json($products); // Pass this from controller

function findProductByBarcode(barcode) {
    return products.find(p => p.barcode === barcode);
}

document.getElementById('barcode').addEventListener('change', function () {
    const product = findProductByBarcode(this.value);
    if (product) {
        document.getElementById('product_id').value = product.id;
        document.getElementById('product_name').value = product.name;
        document.getElementById('product_price').value = product.sale_price;
        document.getElementById('product_barcode').value = product.barcode;

        const unitSelect = document.getElementById('unit');
        unitSelect.innerHTML = '';
        for (const [unit, multiplier] of Object.entries(product.unit)) {
            const option = document.createElement('option');
            option.value = unit;
            option.text = unit + ` (x${multiplier})`;
            option.dataset.multiplier = multiplier;
            unitSelect.appendChild(option);
        }
    }
});

document.getElementById('add-to-list').addEventListener('click', function () {
    const name = document.getElementById('product_name').value;
    const barcode = document.getElementById('product_barcode').value;
    const sale_price = parseFloat(document.getElementById('product_price').value);
    const quantity = parseFloat(document.getElementById('quantity').value);
    const unit = document.getElementById('unit').value;
    const multiplier = parseFloat(document.getElementById('unit').selectedOptions[0].dataset.multiplier || 1);
    const total = sale_price * quantity * multiplier;

    if (!name || !unit || !quantity) return;

    const tbody = document.querySelector('#consumption-table tbody');
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>${name}</td>
        <td>${barcode}</td>
        <td>${unit}</td>
        <td>${quantity}</td>
        <td>${sale_price}</td>
        <td class="row-total">${total}</td>
        <td><button class="btn btn-danger btn-sm remove-row">X</button></td>
    `;
    tbody.appendChild(row);
    updateTotal();

    document.getElementById('consume-form').reset();
});

document.querySelector('#consumption-table').addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-row')) {
        e.target.closest('tr').remove();
        updateTotal();
    }
});

function updateTotal() {
    let total = 0;
    document.querySelectorAll('.row-total').forEach(td => {
        total += parseFloat(td.textContent || 0);
    });
    document.getElementById('total-sum').textContent = total.toFixed(2);
}
</script>
@endsection
