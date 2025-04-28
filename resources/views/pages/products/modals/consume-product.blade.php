<div class="modal fade" id="consumeProductModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Расход товара</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form action="{{ route('consume') }}" method="POST">
                    @csrf
                    <div class="row">
                        <!-- Left side: Product image -->
                        <div class="col-md-6 text-center">
                            <input type="hidden" name="product_id" id="consume_product_id" value="">
                            <input type="hidden" name="quantity" id="consume_quantity" value="1">
                            <input type="hidden" name="total_price" id="consume_hidden_total_price" value="">

                            <img src="" id="consume_product_photo" alt="Product photo"
                                style="width: 250px; height: 250px;" class="border rounded">

                            <h5 id="consume_product_name" class="mt-3 text-capitalize"></h5>
                            <p id="consume_product_sale_price" class="text-muted"></p>
                        </div>

                        <!-- Right side: Quantity, Transaction, etc -->
                        <div class="col-md-6">
                            <div class="d-flex justify-content-center align-items-center my-3 gap-3">
                                <button type="button" class="btn btn-outline-secondary text-dark btn-sm"
                                    onclick="decreaseQty('consume')">
                                    <i class="mdi mdi-minus"></i>
                                </button>

                                <input type="number" id="consume_qty" class="form-control text-center" style="width: 100px;"
                                    oninput="updateTotal('consume')" name="qty" min="1" value="1">

                                <button type="button" class="btn btn-outline-secondary text-dark btn-sm"
                                    onclick="increaseQty('consume')">
                                    <i class="mdi mdi-plus"></i>
                                </button>
                            </div>

                            <h5 class="text-center my-3">Итого: <span id="consume_total_price" name="total_price"></span> сум</h5>

                            <div class="mb-3">
                                <label for="consume_transaction_type" class="form-label">Тип транзакции</label>
                                <select id="consume_transaction_type" class="form-select" name="type"
                                    onchange="onTransactionTypeChange('consume')">
                                    <option value="consume">Продажа (расход)</option>
                                    <option value="loan">В долг (клиент берет)</option>
                                    <option value="return">Возврат товара</option>
                                </select>
                            </div>

                            <div id="consume_client_phone_group" class="mb-3" style="display: none;">
                                <label for="consume_client_phone" class="form-label">Номер клиента</label>
                                <input type="text" id="consume_client_phone" name="client_phone" class="form-control"
                                    placeholder="+998901234567">
                            </div>

                            <div id="consume_return_reason_group" class="mb-3" style="display: none;">
                                <label for="consume_return_reason" class="form-label">Причина возврата</label>
                                <textarea id="consume_return_reason" name="return_reason" class="form-control" rows="2"
                                    placeholder="Причина возврата товара"></textarea>
                            </div>

                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" id="consume_print_cheque">
                                <label class="form-check-label" for="consume_print_cheque">
                                    Печать чека
                                </label>
                            </div>
                        </div>

                    </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-lg text-white">Подтвердить</button>
            </div>
            </form>
        </div>
    </div>
</div>

<script src="{{ asset('admin/assets/js/phone-number-format.js') }}"></script>
<script>
    const clientPhoneInput = document.getElementById('consume_client_phone');
    maskUzPhoneInput(clientPhoneInput);
</script>
