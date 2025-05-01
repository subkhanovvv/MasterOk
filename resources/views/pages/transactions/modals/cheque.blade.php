<!-- Transaction Details Modal -->
<div class="modal fade" id="transactionDetailsModal" tabindex="-1" aria-labelledby="transactionDetailsLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border">
            <div class="modal-header">
                <h5 class="modal-title" id="transactionDetailsLabel">Чек транзакции</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <!-- Centered info -->
                <div class="text-center">
                    <h4 class="mb-1">MasterOK</h4>
                    <p class="fw-bold mb-0">Транзакция №<span id="td_id"></span></p>
                        <small class="text-muted" id="td_created_at"></small>
                        {{-- {{ \Carbon\Carbon::parse($p->created_at)->format('d-m-Y') }} --}}

                    <hr>
                </div>
                <!-- QR code + table -->

                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>Тип:</strong> <span id="td_type"></span></li>
                    <li class="list-group-item"><strong>ID продукта:</strong> <span id="td_product_id"></span></li>
                    <li class="list-group-item"><strong>Количество:</strong> <span id="td_qty"></span></li>
                    <li class="list-group-item"><strong>Общая сумма:</strong> <span id="td_total_price"></span> сум</li>
                    <li class="list-group-item"><strong>Оплачено:</strong> <span id="td_paid_amount"></span> сум</li>
                    <li class="list-group-item" id="td_return_reason_row" style="display:none;">
                        <strong>Причина возврата:</strong> <span id="td_return_reason"></span>
                    </li>
                    <li class="list-group-item" id="td_number_row" style="display:none;">
                        <strong>Phone number:</strong> <span id="td_number"></span>
                    </li>
                </ul>
                <hr>
                <div class="text-center mb-3" id="qrCodePreview">
                    <!-- QR/barcode gets injected here -->
                </div>
                <div class="text-center mt-3">
                    <p>OOO "MasterOK" MCHJ</p>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
