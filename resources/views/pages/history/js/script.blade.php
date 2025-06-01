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

    transactionDetailsModal.addEventListener('show.bs.modal', function(event) {
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

        const translate = (map, key) => map[key] || key || '-';

        const setText = (id, value) => {
            transactionDetailsModal.querySelector('#' + id).textContent = value || '-';
        };

        setText('modalId', button.dataset.id);
        setText('modalCreatedAtFull', button.dataset.created_at ? new Date(button.dataset.created_at)
            .toLocaleString() : '-');
        setText('modalUpdatedAt', button.dataset.updated_at ? new Date(button.dataset.updated_at)
            .toLocaleString() : '-');
        setText('modalType', translate(typeMap, button.dataset.type));
        setText('modalStatus', translate(statusMap, button.dataset.status));
        setText('modalClientName', button.dataset.client_name);
        setText('modalClientPhone', button.dataset.client_phone);
        setText('modalSupplier', button.dataset.supplier);
        setText('modalTotalPrice', button.dataset.total_price ? button.dataset.total_price + ' uzs' : '-');
        setText('modalPaymentType', translate(paymentTypeMap, button.dataset.payment_type));
        setText('modalLoanDirection', button.dataset.loan_direction);
        // setText('modalLoanDueTo', button.dataset.loan_due_to ? button.dataset.loan_due_to + ' uzs' : '-');
        setText('modalReturnReason', button.dataset.return_reason);
        setText('modalNote', button.dataset.note);
        setText('modalBrand',button.dataset.brand);

        // QR Code rendering
        const qrCodeContainer = transactionDetailsModal.querySelector('#modalQrCode');
        if (button.dataset.qr_code) {
            qrCodeContainer.innerHTML =
                `<img src="${button.dataset.qr_code}" alt="QR Code" class="img-fluid" style="display:block; margin: 0 auto; max-width:130px; border-radius:6px;">`;
        } else {
            qrCodeContainer.innerHTML = '<div style="text-align:center; color:#888;">Нет QR кода</div>';
        }

        // Render items
        const items = JSON.parse(button.dataset.items || '[]');
        if (items.length === 0) {
            itemsList.innerHTML =
                `<li style="text-align:center; color:#888; font-style:italic;">Товары отсутствуют</li>`;
        } else {
            items.forEach(item => {
                const li = document.createElement('li');
                li.classList.add('item-line');
                li.innerHTML = `
        <span><strong>${item.product_name || '-'}</strong> × ${item.qty} ${item.unit}</span>
        <span>${item.price ? item.price + ' uzs' : '-'}</span>
      `;
                itemsList.appendChild(li);
            });
        }
    });
</script>
