<script>
    // Store the current action and product ID
    let currentBarcodeAction = '';
    let currentProductId = '';

    // Function to open the modal
    function openBarcodeModal(action, productId) {
        currentBarcodeAction = action;
        currentProductId = productId;

        // Get modal elements
        const modal = document.getElementById('barcodeModal');
        const proceedBtn = document.getElementById('proceedAction');

        // Update button text and style based on action
        if (action === 'print') {
            proceedBtn.textContent = 'Print';
            proceedBtn.className = 'btn btn-primary';
        } else {
            proceedBtn.textContent = 'Download PDF';
            proceedBtn.className = 'btn btn-success';
        }

        // Show the modal (using Bootstrap's JS)
        const bootstrapModal = new bootstrap.Modal(modal);
        bootstrapModal.show();

        // Set up the proceed action
        proceedBtn.onclick = function() {
            handleBarcodeAction();
            bootstrapModal.hide();
        };

        // Allow pressing Enter key to submit
        document.getElementById('copyCount').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                handleBarcodeAction();
                bootstrapModal.hide();
            }
        });
    }

    // Function to handle the print/download action
    // Function to handle the print/download action
    function handleBarcodeAction() {
        const copies = document.getElementById('copyCount').value || 1;

        if (currentBarcodeAction === 'print') {
            // Create an iframe element
            const iframe = document.createElement('iframe');
            iframe.style.position = 'absolute';
            iframe.style.width = '0';
            iframe.style.height = '0';
            iframe.style.border = 'none';

            // Set the source to the print URL
            iframe.src = `/barcode/print/${currentProductId}?copies=${copies}`;

            // Append the iframe to the body
            document.body.appendChild(iframe);

            // Wait for the iframe to load, then trigger print dialog
            iframe.onload = function() {
                iframe.contentWindow.print(); // Open print dialog on the iframe content
                setTimeout(function() {
                    document.body.removeChild(iframe); // Clean up by removing the iframe after print
                }, 300);
            };
        } else {
            // Trigger download
            window.location.href = `/barcode/download/${currentProductId}?copies=${copies}`;
        }
    }


    // Close window after printing (for print view)
    if (window.location.pathname.includes('/barcode/print')) {
        window.onafterprint = function() {
            setTimeout(function() {
                window.close();
            }, 300);
        };
    }
</script>
