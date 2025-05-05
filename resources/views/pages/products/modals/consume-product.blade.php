<div class="modal fade" id="consumeProductModal" tabindex="-1" aria-labelledby="consumeProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="consumeProductModalLabel">Оформление продажи</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <form id="consumeForm" method="POST" action="{{ route('consume') }}">
                    @csrf
                    
                    <!-- Transaction Type Selector -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="transactionType" class="form-label">Тип операции</label>
                            <select class="form-select" id="transactionType" name="type" required>
                                <option value="sale" selected>Продажа</option>
                                <option value="loan">Продажа в долг</option>
                                <option value="return">Возврат товара</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="paymentMethod" class="form-label">Способ оплаты</label>
                            <select class="form-select" id="paymentMethod" name="payment_type" required>
                                <option value="cash" selected>Наличные</option>
                                <option value="card">Безналичные</option>
                                <option value="mixed">Смешанная оплата</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Mixed Payment Fields (hidden by default) -->
                    <div id="mixedPaymentFields" class="row mb-3" style="display: none;">
                        <div class="col-md-6">
                            <label for="cashAmount" class="form-label">Сумма наличными</label>
                            <input type="number" class="form-control" id="cashAmount" name="cash_amount" value="0" min="0">
                        </div>
                        <div class="col-md-6">
                            <label for="cardAmount" class="form-label">Сумма картой</label>
                            <input type="number" class="form-control" id="cardAmount" name="card_amount" value="0" min="0">
                        </div>
                    </div>
                    
                    <!-- Client Info Fields (hidden by default) -->
                    <div id="clientInfoFields" class="row mb-3" style="display: none;">
                        <div class="col-md-6">
                            <label for="clientName" class="form-label">Имя клиента</label>
                            <input type="text" class="form-control" id="clientName" name="client_name" placeholder="Введите имя">
                        </div>
                        <div class="col-md-6">
                            <label for="clientPhone" class="form-label">Телефон клиента</label>
                            <input type="tel" class="form-control" id="clientPhone" name="client_phone" placeholder="+998 __ ___ __ __">
                        </div>
                    </div>
                    
                    <!-- Return Reason Field (hidden by default) -->
                    <div id="returnReasonField" class="mb-3" style="display: none;">
                        <label for="returnReason" class="form-label">Причина возврата</label>
                        <textarea class="form-control" id="returnReason" name="return_reason" rows="2" placeholder="Укажите причину возврата"></textarea>
                    </div>
                    
                    <!-- Product Search and Selection -->
                    <div class="mb-3">
                        <label for="productSearch" class="form-label">Добавить товар</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="productSearch" placeholder="Поиск по названию или штрихкоду">
                            <button class="btn btn-outline-secondary" type="button" id="scanBarcodeBtn">
                                <i class="mdi mdi-barcode-scan"></i>
                            </button>
                        </div>
                        <div id="searchResults" class="mt-2" style="display: none;">
                            <div class="list-group" id="searchResultsList"></div>
                        </div>
                    </div>
                    
                    <!-- Selected Products Table -->
                    <div class="table-responsive mb-3">
                        <table class="table table-hover" id="selectedProductsTable">
                            <thead>
                                <tr>
                                    <th width="40%">Товар</th>
                                    <th width="20%">Цена</th>
                                    <th width="20%">Кол-во</th>
                                    <th width="15%">Сумма</th>
                                    <th width="5%"></th>
                                </tr>
                            </thead>
                            <tbody id="selectedProductsBody">
                                <!-- Products will be added here dynamically -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Итого:</th>
                                    <th id="subtotalAmount">0</th>
                                    <th></th>
                                </tr>
                                <tr id="discountRow" style="display: none;">
                                    <th colspan="3" class="text-end">Скидка:</th>
                                    <th id="discountAmount">0</th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-end">К оплате:</th>
                                    <th id="totalAmount">0</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <!-- Discount and Notes -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="discountInput" class="form-label">Скидка</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="discountInput" min="0" value="0">
                                <select class="form-select" id="discountType" style="max-width: 80px;">
                                    <option value="fixed">сум</option>
                                    <option value="percent">%</option>
                                </select>
                                <button class="btn btn-outline-secondary" type="button" id="applyDiscountBtn">Применить</button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="saleNotes" class="form-label">Примечание</label>
                            <input type="text" class="form-control" id="saleNotes" name="notes" placeholder="Дополнительная информация">
                        </div>
                    </div>
                    
                    <!-- Print Options -->
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="printReceipt" name="print_receipt" checked>
                        <label class="form-check-label" for="printReceipt">Печатать чек</label>
                    </div>
                    
                    <!-- Hidden inputs for form submission -->
                    <input type="hidden" id="discountValue" name="discount" value="0">
                    <input type="hidden" id="discountValueType" name="discount_type" value="fixed">
                </form>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" id="confirmSaleBtn">Подтвердить продажу</button>
            </div>
        </div>
    </div>
</div>

<!-- Barcode Scanner Modal -->
<div class="modal fade" id="barcodeScannerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Сканирование штрихкода</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <video id="barcodeScanner" width="100%" height="200" style="border: 1px solid #ddd;"></video>
                <p class="mt-2 text-muted">Наведите камеру на штрихкод товара</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/quagga/dist/quagga.min.js"></script>
