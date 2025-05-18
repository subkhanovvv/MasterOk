<script>
    function printTransactionCheque(id) {
        const iframe = document.createElement('iframe');
        iframe.style.position = 'absolute';
        iframe.style.width = '0';
        iframe.style.height = '0';
        iframe.style.border = 'none';
        iframe.src = `/history/print/${id}`;
        document.body.appendChild(iframe);

        iframe.onload = function() {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();

            setTimeout(() => {
                document.body.removeChild(iframe);
            }, 500);
        };
    }
    const transactionDetailsModal = document.getElementById('transactionDetailsModal');
  transactionDetailsModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;

    // Clear previous items
    const itemsList = transactionDetailsModal.querySelector('#modalItemsList');
    itemsList.innerHTML = '';

    // Set transaction main info
    transactionDetailsModal.querySelector('#modalId').textContent = button.dataset.id || '-';
    transactionDetailsModal.querySelector('#modalCreatedAt').textContent = button.dataset.created_at ? new Date(button.dataset.created_at).toLocaleDateString() : '-';
    transactionDetailsModal.querySelector('#modalCreatedAtFull').textContent = button.dataset.created_at ? new Date(button.dataset.created_at).toLocaleString() : '-';
    transactionDetailsModal.querySelector('#modalUpdatedAt').textContent = button.dataset.updated_at ? new Date(button.dataset.updated_at).toLocaleString() : '-';
    transactionDetailsModal.querySelector('#modalType').textContent = button.dataset.type || '-';
    transactionDetailsModal.querySelector('#modalClientName').textContent = button.dataset.client_name || '-';
    transactionDetailsModal.querySelector('#modalClientPhone').textContent = button.dataset.client_phone || '-';
    transactionDetailsModal.querySelector('#modalStatus').textContent = button.dataset.status || '-';
    transactionDetailsModal.querySelector('#modalLoanDirection').textContent = button.dataset.loan_direction || '-';
    transactionDetailsModal.querySelector('#modalTotalPrice').textContent = button.dataset.total_price ? `${button.dataset.total_price} uzs` : '-';
    transactionDetailsModal.querySelector('#modalPaymentType').textContent = button.dataset.payment_type || '-';
    transactionDetailsModal.querySelector('#modalPaidAmount').textContent = button.dataset.paid_amount ? `${button.dataset.paid_amount} uzs` : '-';
    transactionDetailsModal.querySelector('#modalLoanDueTo').textContent = button.dataset.loan_due_to ? `${button.dataset.loan_due_to} uzs` : '-';
    transactionDetailsModal.querySelector('#modalReturnReason').textContent = button.dataset.return_reason || '-';
    transactionDetailsModal.querySelector('#modalNote').textContent = button.dataset.note || '-';
    transactionDetailsModal.querySelector('#modalSupplier').textContent = button.dataset.supplier || '-';

    // QR Code image
    const qrCodeContainer = transactionDetailsModal.querySelector('#modalQrCode');
    if (button.dataset.qr_code) {
      qrCodeContainer.innerHTML = `<img src="${button.dataset.qr_code}" alt="QR Code" style="max-width: 150px; border: 1px solid #ddd; border-radius: 6px;">`;
    } else {
      qrCodeContainer.textContent = 'Нет QR кода';
    }

    // Load product activity items (JSON string expected)
    const itemsJson = button.dataset.items || '[]';
    const items = JSON.parse(itemsJson);

    if (items.length === 0) {
      itemsList.innerHTML = '<p class="text-muted fst-italic">Товары отсутствуют</p>';
    } else {
      items.forEach(item => {
        const itemDiv = document.createElement('div');
        itemDiv.classList.add('d-flex', 'justify-content-between', 'align-items-center', 'border', 'rounded', 'p-2', 'shadow-sm');
        itemDiv.style.backgroundColor = '#f9f9f9';

        itemDiv.innerHTML = `
          <div>
            <strong>${item.product_name || 'Без названия'}</strong><br>
            <small class="text-muted">${item.unit} x ${item.qty}</small>
          </div>
          <div class="text-end fw-semibold">${item.price ? item.price + ' uzs' : '-'}</div>
        `;

        itemsList.appendChild(itemDiv);
      });
    }
  });
</script>
