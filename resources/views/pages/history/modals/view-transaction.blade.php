<!-- Modal -->
<div class="modal fade" id="transactionDetailsModal" tabindex="-1" aria-labelledby="transactionDetailsLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content p-3" style="font-family: monospace; max-width: 400px; margin: auto;">
            <div class="modal-header border-0 pb-1">
                <h5 class="modal-title" id="transactionDetailsLabel">Транзакция #<span id="modalId"></span></h5>
                <button type="button" class="btn-close p-1" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body" style="font-size: 0.9rem; line-height: 1.3;">

                <div style="border-bottom: 1px dashed #ccc; padding-bottom: 8px; margin-bottom: 8px;">
                    <div><strong>Дата создания:</strong> <span id="modalCreatedAtFull"></span></div>
                    <div><strong>Дата обновления:</strong> <span id="modalUpdatedAt"></span></div>
                    <div><strong>Тип:</strong> <span id="modalType"></span></div>
                    <div><strong>Клиент:</strong> <span id="modalClientName"></span></div>
                    <div><strong>Телефон:</strong> <span id="modalClientPhone"></span></div>
                    <div><strong>Статус:</strong> <span id="modalStatus"></span></div>
                    <div><strong>Заем:</strong> <span id="modalLoanDirection"></span></div>
                    <div><strong>Поставщик:</strong> <span id="modalSupplier"></span></div>
                </div>

                <div style="border-bottom: 1px dashed #ccc; padding-bottom: 8px; margin-bottom: 8px;">
                    <div><strong>Всего:</strong> <span id="modalTotalPrice"></span></div>
                    <div><strong>Оплата:</strong> <span id="modalPaymentType"></span></div>
                    <div><strong>Остаток займа:</strong> <span id="modalLoanDueTo"></span></div>
                    <div><strong>Причина возврата:</strong> <span id="modalReturnReason"></span></div>
                    <div><strong>Примечание:</strong> <span id="modalNote"></span></div>
                </div>


                <div style="border-top: 1px dashed #ccc; padding-top: 6px;">
                    <strong>Товары:</strong>
                    <ul id="modalItemsList"
                        style="list-style:none; padding-left:0; max-height: 180px; overflow-y:auto; margin-top:6px; font-size:0.85rem;">
                        <!-- Items here -->
                    </ul>
                </div>

                <div style="text-align:center; margin-bottom: 12px;">
                    <div id="modalQrCode"></div>
                </div>

            </div>
        </div>
    </div>
</div>
