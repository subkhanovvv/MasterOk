  <!-- Modal -->
  <div class="modal fade" id="consumeProductModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Расход товара</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">
                <input type="hidden" name="product_id" id="product_id" value="">
                <input type="hidden" name="quantity" id="quantity" value="1">

                <img src="" id="product_photo" alt="Product photo" style="width: 200px; height: 200px;"
                    class="border">

                <h5 id="product_name" class="mt-3 text-capitalize"></h5>
                <p id="product_sale_price" class="text-muted"></p>

                <div class="d-flex justify-content-center align-items-center my-3 gap-3">
                    <button type="button" class="btn btn-outline-secondary text-dark btn-sm" onclick="decreaseQty()">
                        <i class="mdi mdi-minus"></i>
                    </button>

                    <input type="text" id="qty" class="form-control text-center" style="width: 100px;" readonly
                        oninput="updateTotal()">

                    <button type="button" class="btn btn-outline-secondary text-dark btn-sm" onclick="increaseQty()">
                        <i class="mdi mdi-plus"></i>
                    </button>
                </div>
                <select id="transaction_type" class="form-select" onchange="onTransactionTypeChange()">
                    <option value="consume">Продажа (расход)</option>
                    <option value="loan">В долг (клиент берет)</option>
                    <option value="return">Возврат товара</option>
                </select>

                <div id="client_phone_group" class="my-3" style="display: none;">
                    <label for="client_phone" class="form-label">Номер клиента</label>
                    <input type="text" id="client_phone" name="client_phone" class="form-control"
                        placeholder="+998901234567">
                </div>

                <h5>Итого: <span id="total_price" style="transition: all 0.3s ease-in-out;">0</span> сум</h5>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-lg text-white">Расход</button>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('admin/assets/js/phone-number-format.js') }}"></script>
<script>
    const clientPhoneInput = document.getElementById('client_phone');
    maskUzPhoneInput(clientPhoneInput);
</script>
