<!-- Modal -->
<div class="modal fade" id="transactionDetailsModal" tabindex="-1" aria-labelledby="transactionDetailsLabel" aria-hidden="true">
  <div class="modal-dialog ">
    <div class="modal-content border-0" style="border-radius: 8px; font-family: 'Courier New', monospace;">
      <!-- Header -->
      <div class="modal-header border-bottom-0 pb-0 pt-3 px-4">
        <div class="w-100 text-center">
          <h5 class="modal-title fs-6 fw-bold mb-1">ТРАНЗАКЦИЯ</h5>
          <div class="d-flex justify-content-between align-items-center small">
            <span id="modalCreatedAt" class="text-muted"></span>
            <span class="badge rounded-pill px-2 py-1" id="modalStatus"></span>
          </div>
          <div class="text-muted small">№ <span id="modalId" class="fw-bold"></span></div>
        </div>
      </div>
      
      <!-- Body -->
      <div class="modal-body px-4 py-2">
        <!-- Amount Section -->
        <div class="text-center border-bottom pb-2 mb-2">
          <div class="fs-5 fw-bold" id="modalTotalPrice"></div>
          <div class="small text-muted text-capitalize" id="modalType"></div>
        </div>
        
        <!-- Client Info - Only shows if data exists -->
        <div id="clientInfoSection" class="border-bottom pb-2 mb-2 d-none">
          <div class="d-flex justify-content-between small">
            <span class="text-muted">Клиент:</span>
            <span class="fw-medium text-end" id="modalClientName"></span>
          </div>
          <div class="d-flex justify-content-between small">
            <span class="text-muted">Телефон:</span>
            <span class="text-end" id="modalClientPhone"></span>
          </div>
        </div>
        
        <!-- Payment Info -->
        <div class="border-bottom pb-2 mb-2">
          <div class="d-flex justify-content-between small">
            <span class="text-muted">Оплата:</span>
            <span class="text-capitalize" id="modalPaymentType"></span>
          </div>
          <div class="d-flex justify-content-between small">
            <span class="text-muted">Оплачено:</span>
            <span class="fw-medium" id="modalPaidAmount"></span>
          </div>
          <div class="d-flex justify-content-between small">
            <span class="text-muted">Остаток:</span>
            <span class="fw-medium text-danger" id="modalLoanDueTo"></span>
          </div>
        </div>
        
        <!-- Additional Info - Only shows if data exists -->
        <div id="additionalInfoSection" class="border-bottom pb-2 mb-2 d-none">
          <div class="d-flex justify-content-between small" id="loanDirectionRow">
            <span class="text-muted">Направление:</span>
            <span class="text-capitalize" id="modalLoanDirection"></span>
          </div>
          <div class="d-flex justify-content-between small" id="supplierRow">
            <span class="text-muted">Поставщик:</span>
            <span id="modalSupplier"></span>
          </div>
        </div>
        
        <!-- Notes Section - Only shows if data exists -->
        <div id="notesSection" class="border-bottom pb-2 mb-2 d-none">
          <div class="small" id="modalNote"></div>
          <div class="small fst-italic" id="modalReturnReason"></div>
        </div>
        
        <!-- Items List -->
        <h6 class="text-center small fw-bold my-2">ТОВАРЫ</h6>
        <div id="modalItemsList" class="mb-2" style="max-height: 200px; overflow-y: auto;">
          <!-- Items will be appended here -->
        </div>
        
        <!-- Footer -->
        <div class="text-center small text-muted mt-3">
          <div id="qrCodeSection">
            <div id="modalQrCode"></div>
          </div>
          <div class="mt-1">Создано: <span id="modalCreatedAtFull"></span></div>
          <div>Обновлено: <span id="modalUpdatedAt"></span></div>
        </div>
      </div>
    </div>
  </div>
</div>
