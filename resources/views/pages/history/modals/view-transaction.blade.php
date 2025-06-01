<style>
    #transactionDetailsModal .modal-dialog {
        margin: auto;
    }

    #transactionDetailsModal .modal-content {
        font-family: 'Courier New', monospace;
        padding: 12px;
        border: none;
        box-shadow: none;
    }

    #transactionDetailsModal .modal-header {
        border-bottom: 1px dashed #aaa;
        padding-bottom: 4px;
    }

    #transactionDetailsModal .modal-body {
        font-size: 14px;
        padding-top: 6px;
    }

    #transactionDetailsModal .line {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2px;
    }

    #transactionDetailsModal hr {
        border: none;
        border-top: 1px dashed #000;
        margin: 6px 0;
    }

    #transactionDetailsModal .item-line {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2px;
    }

    #transactionDetailsModal #modalItemsList {
        list-style: none;
        padding-left: 0;
        margin: 4px 0;
        font-size: 13px;
    }

    #transactionDetailsModal .qr-container {
        text-align: center;
        margin-top: 10px;
    }

    #transactionDetailsModal .qr-container img {
        width: 100px;
        height: 100px;
        object-fit: contain;
    }

    #transactionDetailsModal .notes {
        font-style: italic;
        margin-top: 4px;
        font-size: 13px;
    }
</style>


<div class="modal fade" id="transactionDetailsModal" tabindex="-1" aria-labelledby="transactionDetailsLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content p-3" style="font-family: monospace; max-width: 400px; margin: auto;">
            <div class="modal-header border-0 pb-1">
                <button type="button" class="btn-close p-1" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <h4 style="text-align: center;">Чек №<span id="modalId"></span></h4>
                <hr>

                <div class="line"><strong>Тип:</strong> <span id="modalType"></span></div>
                <div class="line"><strong>Статус:</strong> <span id="modalStatus"></span></div>
                <div class="line"><strong>Клиент:</strong> <span id="modalClientName"></span></div>
                <div class="line"><strong>Телефон:</strong> <span id="modalClientPhone"></span></div>
                <div class="line"><strong>Поставщик:</strong> <span id="modalSupplier"></span></div>
                <div class="line"><strong>Бренд: </strong><span id="modalBrand"></span></div>
                <hr>

                <div class="line"><strong>Дата:</strong> <span id="modalCreatedAtFull"></span></div>
                <div class="line"><strong>Обновлено:</strong> <span id="modalUpdatedAt"></span></div>

                <hr>

                <div class="line"><strong>Всего:</strong> <span id="modalTotalPrice"></span></div>
                <div class="line"><strong>Оплата:</strong> <span id="modalPaymentType"></span></div>
                <div class="line"><strong>Заем:</strong> <span id="modalLoanDirection"></span></div>

                <hr>

                <div><strong>Товары:</strong></div>
                <ul id="modalItemsList">
                    {{-- JS appends item lines here --}}
                </ul>

                <div class="notes"><strong>Причина:</strong> <span id="modalReturnReason"></span></div>
                <div class="notes"><strong>Примечание:</strong> <span id="modalNote"></span></div>

                <div id="modalQrCode" class="qr-container">
                    {{-- JS appends <img src="..." /> here --}}
                </div>
            </div>


        </div>
    </div>
</div>
