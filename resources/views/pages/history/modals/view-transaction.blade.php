<style>
    /* #transactionDetailsModal .modal-content {
        font-family: 'Courier New', Courier, monospace;
        border: 1px dashed #333;
        padding: 10px;
    }

    #transactionDetailsModal hr {
        margin: 0.5rem 0;
    } */
</style><!-- Transaction Details Modal -->
<div class="modal fade" id="transactionDetailsModal" tabindex="-1" aria-labelledby="transactionDetailsLabel"
    aria-hidden="true">
    <div class="modal-dialog  "> <!-- Smaller size like receipt -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Детали транзакции</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body" id="transactionDetailsContent">
                <!-- Filled via JavaScript -->
                <div class="text-center">
                    <h6 class="mb-1">Магазин XYZ</h6>
                    <p class="mb-0"><small>Дата: <span id="td-date"></span></small></p>
                    <p class="mb-0"><small>Тип: <span id="td-type"></span></small></p>
                </div>
                <hr>
                <div id="td-items"></div>
                <hr>
                <p class="mb-0"><strong>Общая сумма:</strong> <span id="td-total"></span> uzs</p>
                <p class="mb-0"><strong>Оплата:</strong> <span id="td-payment-type"></span></p>
                <p class="mb-0"><strong>Статус:</strong> <span id="td-status"></span></p>
            </div>
        </div>
    </div>
</div>
