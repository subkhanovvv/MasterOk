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
</script>
<script>
  const transactionDetailsModal = document.getElementById('transactionDetailsModal');
  transactionDetailsModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;

    // Clear previous items
    const itemsList = transactionDetailsModal.querySelector('#modalItemsList');
    itemsList.innerHTML = '';

    // Translation maps
    const typeMap = {
      consume: 'Расход',
      loan: 'Заем',
      return: 'Возврат',
      intake: 'Приемка',
      intake_loan: 'Приемка займа',
      intake_return: 'Приемка возврата'
    };

    const statusMap = {
      complete: 'Завершен',
      incomplete: 'Не завершен'
    };

    const paymentTypeMap = {
      cash: 'Наличные',
      card: 'Карта',
      bank_transfer: 'Банковский перевод'
    };

    // Helper to get translated text or fallback to raw value
    const translate = (map, key) => map[key] || key || '-';

    // Fill modal main info helper
    const setText = (id, value) => {
      transactionDetailsModal.querySelector('#' + id).textContent = value || '-';
    };

    setText('modalId', button.dataset.id);
    setText('modalCreatedAtFull', button.dataset.created_at ? new Date(button.dataset.created_at).toLocaleString() : '-');
    setText('modalUpdatedAt', button.dataset.updated_at ? new Date(button.dataset.updated_at).toLocaleString() : '-');
    setText('modalType', translate(typeMap, button.dataset.type));
    setText('modalClientName', button.dataset.client_name);
    setText('modalClientPhone', button.dataset.client_phone);
    setText('modalStatus', translate(statusMap, button.dataset.status));
    setText('modalLoanDirection', button.dataset.loan_direction);
    setText('modalTotalPrice', button.dataset.total_price ? button.dataset.total_price + ' uzs' : '-');
    setText('modalPaymentType', translate(paymentTypeMap, button.dataset.payment_type));
    setText('modalPaidAmount', button.dataset.paid_amount ? button.dataset.paid_amount + ' uzs' : '-');
    setText('modalLoanDueTo', button.dataset.loan_due_to ? button.dataset.loan_due_to + ' uzs' : '-');
    setText('modalReturnReason', button.dataset.return_reason);
    setText('modalNote', button.dataset.note);
    setText('modalSupplier', button.dataset.supplier);

    // QR code image
    const qrCodeContainer = transactionDetailsModal.querySelector('#modalQrCode');
    if (button.dataset.qr_code) {
      qrCodeContainer.innerHTML = `<img src="${button.dataset.qr_code}" alt="QR Code" style="max-width: 130px; border-radius: 5px;">`;
    } else {
      qrCodeContainer.textContent = 'Нет QR кода';
    }

    // Load items JSON
    const items = JSON.parse(button.dataset.items || '[]');
    if (items.length === 0) {
      itemsList.innerHTML = `<li style="text-align:center; color:#888; font-style:italic;">Товары отсутствуют</li>`;
    } else {
      items.forEach(item => {
        const li = document.createElement('li');
        li.style.borderBottom = '1px dashed #ddd';
        li.style.padding = '4px 0';
        li.innerHTML = `
          <div><strong>${item.product_name || '-'}</strong></div>
          <div>Кол-во: ${item.qty} ${item.unit} | Цена: ${item.price ? item.price + ' uzs' : '-'}</div>
        `;
        itemsList.appendChild(li);
      });
    }
  });
</script>

