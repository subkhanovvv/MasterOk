<div class="modal fade" id="barcodeModal" tabindex="-1" role="dialog" aria-labelledby="barcodeModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="barcodeModalLabel">Barcode Options</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="copyCount">Number of Copies:</label>
                    <input type="number" class="form-control" id="copyCount" min="1" max="100"
                        value="1">
                </div>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Single copy will print larger. Multiple copies will arrange 3 per
                    row.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="proceedAction">Proceed</button>
            </div>
        </div>
    </div>
</div>
