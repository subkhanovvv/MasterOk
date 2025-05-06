@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-8">
            <h4 class="mb-4">Расход продуктов</h4>
        </div>
        <div class="col-md-4 text-end">
            <a href="#" class="btn btn-outline-primary">
                <i class="mdi mdi-history"></i> История расходов
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="input-group">
                        <input type="text" id="product-search" class="form-control" 
                               placeholder="Поиск по названию или штрихкоду..." style="height: 45px">
                        <button class="btn btn-primary" id="search-btn">
                            <i class="mdi mdi-magnify"></i>
                        </button>
                    </div>
                    
                    <div class="mt-3" id="product-list" style="max-height: 500px; overflow-y: auto;">
                        <!-- Products will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <strong>Список расходов</strong>
                </div>
                <div class="card-body">
                    <div class="table table-responsive">
                        <table class="table table-bordered table-hover" id="consumption-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Продукт</th>
                                    <th>Единица</th>
                                    <th>Кол-во</th>
                                    <th>Доступно</th>
                                    <th>Цена</th>
                                    <th>Сумма</th>
                                    <th width="50px"></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5" class="text-end"><strong>Общая сумма:</strong></td>
                                    <td colspan="2"><strong id="total-sum">0.00</strong> UZS</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        <label for="consumption-notes">Примечание</label>
                        <textarea id="consumption-notes" class="form-control" rows="2" 
                                  placeholder="Дополнительная информация..."></textarea>
                    </div>
                    
                    <div class="text-end mt-3">
                        <button class="btn btn-success" id="submit-consumption">
                            <i class="fas fa-save"></i> Сохранить расход
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Product Modal -->
<div class="modal fade" id="product-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Добавить продукт</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="product-form">
                    <input type="hidden" id="modal-product-id">
                    <input type="hidden" id="modal-product-name">
                    <input type="hidden" id="modal-product-price">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <img id="modal-product-image" src="" class="img-thumbnail" style="max-height: 150px; display: none;">
                        </div>
                        <div class="col-md-6">
                            <h5 id="modal-product-title"></h5>
                            <div class="text-muted" id="modal-product-barcode"></div>
                            <div class="mt-2">
                                <strong>Цена: </strong><span id="modal-product-price-text"></span> UZS
                            </div>
                            <div>
                                <strong>Остаток: </strong><span id="modal-product-stock"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>Единица измерения</label>
                            <select id="modal-product-unit" class="form-select">
                                <option disabled selected>— выберите —</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Количество</label>
                            <input type="number" id="modal-product-quantity" class="form-control" min="0.001" step="0.001" value="1">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" id="add-product-to-list">Добавить</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Load products on page load
    loadProducts();
    
    // Search products
    $('#product-search').on('keyup', function(e) {
        if (e.key === 'Enter') {
            loadProducts();
        }
    });
    
    $('#search-btn').on('click', function() {
        loadProducts();
    });
    
    function loadProducts() {
        const search = $('#product-search').val();
        
        $.ajax({
            url: "{{ route('consumption.products') }}",
            method: 'GET',
            data: { search: search },
            success: function(products) {
                const $productList = $('#product-list');
                $productList.empty();
                
                if (products.length === 0) {
                    $productList.append('<div class="alert alert-info">Продукты не найдены</div>');
                    return;
                }
                
                products.forEach(product => {
                    const stockText = product.stock > 0 
                        ? `<span class="text-success">${product.stock} в наличии</span>`
                        : '<span class="text-danger">Нет в наличии</span>';
                    
                    const productHtml = `
                        <div class="card mb-2 product-item ${product.stock <= 0 ? 'opacity-50' : ''}" 
                             data-id="${product.id}" 
                             data-name="${product.name}"
                             data-barcode="${product.barcode}"
                             data-price="${product.sale_price}"
                             data-stock="${product.stock}"
                             data-image="${product.image || ''}"
                             data-units='${JSON.stringify(product.unit)}'
                             style="cursor: pointer;">
                            <div class="card-body p-2">
                                <div class="d-flex">
                                    ${product.image ? `
                                        <img src="${product.image}" class="rounded me-2" 
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    ` : ''}
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">${product.name}</h6>
                                        <small class="text-muted">${product.barcode}</small>
                                        <div class="d-flex justify-content-between mt-1">
                                            <span>${product.sale_price} UZS</span>
                                            ${stockText}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    $productList.append(productHtml);
                });
                
                // Setup click handlers
                $('.product-item').on('click', function() {
                    if (parseFloat($(this).data('stock')) <= 0) return;
                    
                    const productId = $(this).data('id');
                    const productName = $(this).data('name');
                    const productPrice = $(this).data('price');
                    const productUnits = $(this).data('units');
                    const productImage = $(this).data('image');
                    const productBarcode = $(this).data('barcode');
                    const productStock = $(this).data('stock');
                    
                    // Set modal data
                    $('#modal-product-id').val(productId);
                    $('#modal-product-name').val(productName);
                    $('#modal-product-price').val(productPrice);
                    
                    // Update modal UI
                    $('#modal-product-title').text(productName);
                    $('#modal-product-barcode').text(productBarcode);
                    $('#modal-product-price-text').text(productPrice);
                    $('#modal-product-stock').text(productStock);
                    
                    if (productImage) {
                        $('#modal-product-image').attr('src', productImage).show();
                    } else {
                        $('#modal-product-image').hide();
                    }
                    
                    // Populate units
                    const $unitSelect = $('#modal-product-unit');
                    $unitSelect.empty().append('<option disabled selected>— выберите —</option>');
                    
                    for (const [unit, multiplier] of Object.entries(productUnits)) {
                        $unitSelect.append(new Option(`${unit} (x${multiplier})`, unit));
                    }
                    
                    // Reset quantity
                    $('#modal-product-quantity').val(1);
                    
                    // Show modal
                    const modal = new bootstrap.Modal(document.getElementById('product-modal'));
                    modal.show();
                });
            }
        });
    }
    
    // Add product to consumption list
    $('#add-product-to-list').on('click', function() {
        const productId = $('#modal-product-id').val();
        const productName = $('#modal-product-name').val();
        const productPrice = parseFloat($('#modal-product-price').val());
        const quantity = parseFloat($('#modal-product-quantity').val());
        const unit = $('#modal-product-unit').val();
        const units = JSON.parse($(`.product-item[data-id="${productId}"]`).data('units'));
        const multiplier = units[unit] || 1;
        const total = productPrice * quantity * multiplier;
        const baseQuantity = quantity * multiplier;
        const availableStock = parseFloat($(`.product-item[data-id="${productId}"]`).data('stock'));
        
        // Validation
        if (!unit) {
            toastr.error('Выберите единицу измерения');
            return;
        }
        
        if (!quantity || quantity <= 0) {
            toastr.error('Введите корректное количество');
            return;
        }
        
        if (baseQuantity > availableStock) {
            toastr.error(`Недостаточно товара. Доступно: ${availableStock / multiplier} ${unit}`);
            return;
        }
        
        // Check if product already exists in table
        const existingRow = $(`#consumption-table tbody tr[data-product-id="${productId}"][data-unit="${unit}"]`);
        
        if (existingRow.length > 0) {
            // Update existing row
            const existingQuantity = parseFloat(existingRow.data('quantity'));
            const newQuantity = existingQuantity + quantity;
            const newBaseQuantity = newQuantity * multiplier;
            
            if (newBaseQuantity > availableStock) {
                toastr.error(`Недостаточно товара. Доступно: ${availableStock / multiplier - existingQuantity} ${unit}`);
                return;
            }
            
            const newTotal = productPrice * newQuantity * multiplier;
            
            existingRow.data('quantity', newQuantity);
            existingRow.data('base-quantity', newBaseQuantity);
            existingRow.data('total', newTotal);
            
            existingRow.find('td:eq(2)').text(newQuantity.toFixed(3));
            existingRow.find('td:eq(4)').text(newTotal.toFixed(2));
        } else {
            // Add new row
            const row = `
                <tr data-product-id="${productId}" 
                    data-unit="${unit}" 
                    data-price="${productPrice}" 
                    data-quantity="${quantity}" 
                    data-base-quantity="${baseQuantity}" 
                    data-total="${total}">
                    <td>${productName}</td>
                    <td>${unit}</td>
                    <td>${quantity.toFixed(3)}</td>
                    <td>${(availableStock / multiplier).toFixed(3)}</td>
                    <td>${productPrice.toFixed(2)}</td>
                    <td class="row-total">${total.toFixed(2)}</td>
                    <td class="text-center">
                        <button class="btn btn-danger btn-sm remove-row">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            
            $('#consumption-table tbody').append(row);
        }
        
        updateTotal();
        
        // Close modal
        bootstrap.Modal.getInstance(document.getElementById('product-modal')).hide();
    });
    
    // Remove row
    $('#consumption-table').on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
        updateTotal();
    });
    
    // Submit consumption
    $('#submit-consumption').on('click', function() {
        const items = [];
        $('#consumption-table tbody tr').each(function() {
            items.push({
                product_id: $(this).data('product-id'),
                quantity: $(this).data('quantity'),
                unit: $(this).data('unit'),
                price: $(this).data('price'),
                total: $(this).data('total')
            });
        });
        
        if (items.length === 0) {
            toastr.error('Добавьте хотя бы один продукт');
            return;
        }
        
        const notes = $('#consumption-notes').val();
        
        $.ajax({
            url: "{{ route('consumption.store') }}",
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                items: items,
                notes: notes
            },
            beforeSend: function() {
                $('#submit-consumption').prop('disabled', true)
                    .html('<i class="fas fa-spinner fa-spin"></i> Сохранение...');
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    
                    // Reset form
                    $('#consumption-table tbody').empty();
                    $('#consumption-notes').val('');
                    updateTotal();
                    
                    // Show success message
                    const successHtml = `
                        <div class="alert alert-success mt-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Расход #${response.data.id}</strong> успешно сохранен
                                </div>
                                <div>
                                    ${response.data.total} UZS
                                </div>
                            </div>
                            <div class="text-end mt-1">
                                <small class="text-muted">${response.data.date}</small>
                            </div>
                        </div>
                    `;
                    
                    $('.card-body').prepend(successHtml);
                    
                    // Reload products to update stock
                    loadProducts();
                }
            },
            error: function(xhr) {
                let message = 'Произошла ошибка';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                } else if (xhr.statusText) {
                    message = xhr.statusText;
                }
                
                toastr.error(message);
            },
            complete: function() {
                $('#submit-consumption').prop('disabled', false)
                    .html('<i class="fas fa-save"></i> Сохранить расход');
            }
        });
    });
    
    function updateTotal() {
        let total = 0;
        $('.row-total').each(function() {
            total += parseFloat($(this).text());
        });
        $('#total-sum').text(total.toFixed(2));
    }
});
</script>
@endsection

@section('styles')
<style>
    .product-item:hover {
        background-color: #f8f9fa;
    }
    .product-item .card-body {
        padding: 0.5rem;
    }
    #product-list::-webkit-scrollbar {
        width: 8px;
    }
    #product-list::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    #product-list::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }
    #product-list::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>
@endsection