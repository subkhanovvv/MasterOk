<div class="modal fade" id="editTransactionModal" tabindex="-1" aria-labelledby="editTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTransactionModalLabel">Edit Product Activity</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editActivityForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <!-- Non-editable transaction type -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_payment_type" class="form-label">Payment Type</label>
                            <select class="form-select" id="edit_payment_type" name="payment_type" required>
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="bank_transfer">Bank transfer</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_type" class="form-label">Transaction Type</label>
                            <input type="text" class="form-control" id="edit_type" readonly>
                            <input type="hidden" name="type" id="edit_type_hidden">
                        </div>
                    </div>

                    <!-- Display-only fields for loan/return -->
                    <div id="edit_loan_fields" class="row mb-3" style="display: none;">
                        <div class="col-md-6">
                            <label class="form-label">Loan Amount (UZS)</label>
                            <input type="text" class="form-control" id="edit_loan_amount_display" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Loan Direction</label>
                            <input type="text" class="form-control" id="edit_loan_direction_display" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Due Date</label>
                            <input type="text" class="form-control" id="edit_loan_due_to_display" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Client Name</label>
                            <input type="text" class="form-control" id="edit_client_name_display" readonly>
                        </div>
                    </div>

                    <div id="edit_return_fields" style="display: none;">
                        <div class="col-md-12">
                            <label class="form-label">Return Reason</label>
                            <textarea class="form-control" id="edit_return_reason_display" rows="2" readonly></textarea>
                        </div>
                    </div>

                    <!-- Editable note field -->
                    <div class="mb-3">
                        <label for="edit_note" class="form-label">Note</label>
                        <textarea class="form-control" name="note" id="edit_note" rows="2"></textarea>
                    </div>

                    <input type="hidden" name="total_price" id="edit_total_price_hidden" value="0">

                    <h4 class="mb-3">🧾 Product List</h4>

                    <div id="edit_products_container" class="mb-3">
                        <!-- Dynamic rows will be added here -->
                    </div>

                    <div class="mb-3 d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" id="edit_add_product">
                            <i class="fas fa-plus"></i> Add Product
                        </button>
                        <div>
                            <strong>Total UZS:</strong> <span id="edit_total_uzs">0</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

