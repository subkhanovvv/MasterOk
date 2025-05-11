<script>
    function openBarcodeModal(action, productId = '') {
        currentBarcodeAction = action;
        currentProductId = productId;

        const modal = document.getElementById('barcodeModal');
        const proceedBtn = document.getElementById('proceedAction');

        if (action === 'print') {
            proceedBtn.textContent = 'Print';
            proceedBtn.className = 'btn btn-primary';
        } else if (action === 'download') {
            proceedBtn.textContent = 'Download PDF';
            proceedBtn.className = 'btn btn-success';
        } else if (action === 'print-all') {
            proceedBtn.textContent = 'Print All';
            proceedBtn.className = 'btn btn-primary';
        }

        const bootstrapModal = new bootstrap.Modal(modal);
        bootstrapModal.show();

        proceedBtn.onclick = function() {
            handleBarcodeAction();
            bootstrapModal.hide();
        };

        document.getElementById('copyCount').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                handleBarcodeAction();
                bootstrapModal.hide();
            }
        });
    }

    function handleBarcodeAction() {
        const copies = document.getElementById('copyCount').value || 1;

        if (currentBarcodeAction === 'print') {
            const iframe = document.createElement('iframe');
            iframe.style.position = 'absolute';
            iframe.style.width = '0';
            iframe.style.height = '0';
            iframe.style.border = 'none';
            iframe.src = `/barcode/print/${currentProductId}?copies=${copies}`;
            document.body.appendChild(iframe);

            iframe.onload = function() {
                iframe.contentWindow.print();
                setTimeout(() => document.body.removeChild(iframe), 300);
            };
        } else if (currentBarcodeAction === 'download') {
            window.location.href = `/barcode/download/${currentProductId}?copies=${copies}`;
        } else
        if (currentBarcodeAction === 'print-all') {
            const iframe = document.createElement('iframe');
            iframe.style.position = 'absolute';
            iframe.style.width = '0';
            iframe.style.height = '0';
            iframe.style.border = 'none';
            iframe.src = `/barcode/print-all?copies=${copies}`;
            document.body.appendChild(iframe);

            iframe.onload = function() {
                iframe.contentWindow.print();
                setTimeout(() => document.body.removeChild(iframe), 300);
            };
        }
    }
</script>
