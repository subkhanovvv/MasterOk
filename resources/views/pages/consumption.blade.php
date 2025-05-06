@extends('layouts.admin')


@section('content')
<div class="container-fluid py-4">
    <h4 class="mb-4">📦 Расход продуктов</h4>

    {{-- Barcode Input --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form id="scanner-form">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label>Штрихкод продукта</label>
                        <input type="text" id="barcode" class="form-control" placeholder="Сканируйте или введите..." autofocus>
                    </div>
                    <div class="col-md-3">
                        <label>Единица измерения</label>
                        <select id="unit" class="form-select">
                            <option disabled selected>— выберите продукт —</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Количество</label>
                        <input type="number" id="quantity" class="form-control" min="1" value="1">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-primary w-100" id="add-to-list">Добавить</button>
                    </div>
                </div>

                <input type="hidden" id="product_id">
                <input type="hidden" id="product_name">
                <input type="hidden" id="product_sale_price">
                <input type="hidden" id="product_barcode">
            </form>
        </div>
    </div>

    {{-- Consumption Table --}}
    <div class="card shadow-sm">
        <div class="card-header">
            <strong>📝 Список расходов</strong>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover" id="consumption-table">
                <thead class="table-light">
                    <tr>
                        <th>Название</th>
                        <th>Штрихкод</th>
                        <th>Единица</th>
                        <th>Кол-во</th>
                        <th>Цена продажи</th>
                        <th>Итого</th>
                        <th>Удалить</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="text-end"><strong>Общая сумма:</strong></td>
                        <td colspan="2"><strong id="total-sum">0</strong> UZS</td>
                    </tr>
                </tfoot>
            </table>

            <div class="text-end">
                <button class="btn btn-success mt-3" id="submit-consumption">Сохранить расход</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const products = @json($products);

    function findProduct(barcode) {
        return products.find(p => p.barcode === barcode);
    }

    document.getElementById('barcode').addEventListener('change', function () {
        const product = findProduct(this.value);
        const unitSelect = document.getElementById('unit');
        unitSelect.innerHTML = '';

        if (product) {
            document.getElementById('product_id').value = product.id;
            document.getElementById('product_name').value = product.name;
            document.getElementById('product_sale_price').value = product.sale_price;
            document.getElementById('product_barcode').value = product.barcode;

            for (const [unit, multiplier] of Object.entries(product.unit)) {
                const option = document.createElement('option');
                option.value = unit;
                option.text = `${unit} (x${multiplier})`;
                option.dataset.multiplier = multiplier;
                unitSelect.appendChild(option);
            }
        } else {
            unitSelect.innerHTML = '<option disabled selected>Продукт не найден</option>';
        }
    });

    document.getElementById('add-to-list').addEventListener('click', function () {
        const name = document.getElementById('product_name').value;
        const barcode = document.getElementById('product_barcode').value;
        const salePrice = parseFloat(document.getElementById('product_sale_price').value);
        const quantity = parseFloat(document.getElementById('quantity').value);
        const unit = document.getElementById('unit').value;
        const multiplier = parseFloat(document.getElementById('unit').selectedOptions[0]?.dataset.multiplier || 1);
        const total = salePrice * quantity * multiplier;

        if (!name || !unit || !quantity || quantity <= 0) return;

        const tbody = document.querySelector('#consumption-table tbody');
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${name}</td>
            <td>${barcode}</td>
            <td>${unit}</td>
            <td>${quantity}</td>
            <td>${salePrice.toFixed(2)}</td>
            <td class="row-total">${total.toFixed(2)}</td>
            <td><button class="btn btn-danger btn-sm remove-row">✖</button></td>
        `;
        tbody.appendChild(row);
        updateTotal();
        document.getElementById('scanner-form').reset();
        document.getElementById('unit').innerHTML = '<option disabled selected>— выберите продукт —</option>';
        document.getElementById('barcode').focus();
    });

    document.querySelector('#consumption-table tbody').addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('tr').remove();
            updateTotal();
        }
    });

    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.row-total').forEach(td => {
            total += parseFloat(td.textContent);
        });
        document.getElementById('total-sum').textContent = total.toFixed(2);
    }
</script>
@endsection
