<div class="modal fade" id="editProductModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header bg-light">
                <h4 class="modal-title fw-bold">Редактировать товар</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="" enctype="multipart/form-data" id="editProductForm">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row">
                        <!-- Left Column - Image Section -->
                        <div class="col-md-4 border-end pe-4">
                            <div class="text-center mb-4">
                                <div class="position-relative d-inline-block">
                                    <img id="editPreview" src="" alt="Image Preview" 
                                         class="img-thumbnail rounded-circle border" 
                                         style="width: 150px; height: 150px; object-fit: cover;">
                                    <div id="editImagePlaceholder" 
                                         class="rounded-circle border d-flex align-items-center justify-content-center" 
                                         style="width: 150px; height: 150px; background-color: #f8f9fa; display: none;">
                                        <i class="bi bi-camera fs-1 text-muted"></i>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <input type="file" name="photo" class="form-control form-control-sm" 
                                           id="editPhoto" onchange="editPreviewImage(event)" accept="image/*">
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label text-muted small mb-1">Штрих-код</label>
                                <div class="bg-light p-2 rounded text-center">
                                    <img src="" alt="" id="editBarcode" style="max-width: 100%; height: 50px;">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Column - Form Fields -->
                        <div class="col-md-8 ps-4">
                            <div class="row g-3">
                                <!-- Basic Info -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" name="name" id="editName" required>
                                        <label for="editName">Название товара</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input name="short_description" class="form-control" id="editShortDescription">
                                        <label for="editShortDescription">Краткое описание</label>
                                    </div>
                                </div>
                                
                                <!-- Brand and Category -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select name="brand_id" class="form-select" id="editBrand" required>
                                            @foreach ($brands as $b)
                                                <option value="{{ $b->id }}">{{ $b->name }}</option>
                                            @endforeach
                                        </select>
                                        <label for="editBrand">Бренд</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select name="category_id" class="form-select" id="editCategory" required>
                                            @foreach ($categories as $c)
                                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                                            @endforeach
                                        </select>
                                        <label for="editCategory">Категория</label>
                                    </div>
                                </div>
                                
                                <!-- Pricing Section -->
                                <div class="col-12 mt-4">
                                    <h5 class="fw-bold text-uppercase text-muted mb-3">Цены</h5>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="text" class="form-control decimal-input" name="price_usd" id="editPriceUsd" required>
                                                <label for="editPriceUsd">USD</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="text" class="form-control decimal-input" name="price_uzs" id="editPriceUzs" required>
                                                <label for="editPriceUzs">UZS</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="number" class="form-control decimal-input" name="sale_price" id="editSalePrice" required>
                                                <label for="editSalePrice">Sale</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="d-flex align-items-center text-muted small">
                                                <i class="bi bi-arrow-left-right me-2"></i>
                                                1 USD = <strong id="editUsdUzsRate" class="ms-1">Загрузка...</strong> UZS
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Units Section -->
                                <div class="col-12 mt-3">
                                    <h5 class="fw-bold text-uppercase text-muted mb-3">Единицы измерения</h5>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <select name="unit" class="form-select" id="editUnit" required>
                                                    <option value="кг">кг</option>
                                                    <option value="г">г</option>
                                                    <option value="л">л</option>
                                                    <option value="мл">мл</option>
                                                    <option value="м">м</option>
                                                    <option value="см">см</option>
                                                    <option value="шт">шт</option>
                                                    <option value="пара">пара</option>
                                                    <option value="набор">набор</option>
                                                </select>
                                                <label for="editUnit">Малая единица</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <select name="stock_unit" class="form-select" id="editStockUnit">
                                                    <option value="коробка">коробка</option>
                                                    <option value="упаковка">упаковка</option>
                                                    <option value="рулон">рулон</option>
                                                    <option value="набор">набор</option>
                                                    <option value="дюжина">дюжина</option>
                                                    <option value="шт">шт</option>
                                                </select>
                                                <label for="editStockUnit">Складская единица</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-floating">
                                                <input type="number" min="1" name="units_per_stock" class="form-control" id="editUnitsPerStock">
                                                <label for="editUnitsPerStock">Количество в складской единице</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>Обновить
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Image Preview Function for Edit Modal
    function editPreviewImage(event) {
        const input = event.target;
        const preview = document.getElementById('editPreview');
        const placeholder = document.getElementById('editImagePlaceholder');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                placeholder.style.display = 'none';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // // Function to populate edit form with product data
    // function populateEditForm(product) {
    //     document.getElementById('editProductForm').action = `/products/${product.id}`;
    //     document.getElementById('editName').value = product.name;
    //     document.getElementById('editShortDescription').value = product.short_description || '';
    //     document.getElementById('editBrand').value = product.brand_id;
    //     document.getElementById('editCategory').value = product.category_id;
    //     document.getElementById('editPriceUsd').value = product.price_usd;
    //     document.getElementById('editPriceUzs').value = product.price_uzs;
    //     document.getElementById('editSalePrice').value = product.sale_price;
    //     document.getElementById('editUnit').value = product.unit;
    //     document.getElementById('editStockUnit').value = product.stock_unit || '';
    //     document.getElementById('editUnitsPerStock').value = product.units_per_stock || '';
        
    //     // Set image preview
    //     const preview = document.getElementById('editPreview');
    //     const placeholder = document.getElementById('editImagePlaceholder');
    //     if (product.photo) {
    //         preview.src = `/storage/${product.photo}`;
    //         placeholder.style.display = 'none';
    //     } else {
    //         preview.src = '';
    //         placeholder.style.display = 'flex';
    //     }
        
    //     // Set barcode
    //     if (product.barcode) {
    //         document.getElementById('editBarcode').src = product.barcode;
    //         document.getElementById('editBarcode').style.display = 'block';
    //     }
    // }

    Initialize currency conversion for edit modal
    document.addEventListener('DOMContentLoaded', () => {
        const editUsdInput = document.querySelector('#editPriceUsd');
        const editUzsInput = document.querySelector('#editPriceUzs');
        
        editUsdInput.addEventListener('input', () => {
            if (usdToUzsRate) {
                const usdValue = parseFloat(editUsdInput.value);
                if (!isNaN(usdValue)) {
                    const uzsValue = usdValue * usdToUzsRate;
                    editUzsInput.value = uzsValue.toFixed(2);
                } else {
                    editUzsInput.value = '';
                }
            }
        });
    });
</script>