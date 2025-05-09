<div class="modal fade" id="viewProductModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h4 class="modal-title fw-bold">Product Details</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <!-- Left Column - Image and Basic Info -->
                    <div class="col-md-5 border-end pe-4">
                        <div class="text-center mb-4">
                            <img id="product_photo" src="" class="img-fluid rounded shadow-sm border"
                                alt="Product Image" style="max-height: 250px; width: auto; object-fit: contain;">
                        </div>

                        <div class="d-flex justify-content-center mb-3">
                            <img id="product_barcode" src="" alt="Barcode" class="img-fluid"
                                style="max-height: 60px; width: auto;">
                        </div>

                        <div class="text-center">
                            <h4 id="product_name" class="fw-bold mb-0"></h4>
                        </div>
                    </div>

                    <!-- Right Column - Details -->
                    <div class="col-md-7 ps-4">
                        <div class="mb-4">
                            <h5 class="fw-bold text-uppercase text-muted mb-3">Pricing</h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="p-3 bg-light rounded text-center">
                                        <div class="text-muted small">UZS Price</div>
                                        <div id="product_uzs_price" class="h5 fw-bold text-success"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 bg-light rounded text-center">
                                        <div class="text-muted small">USD Price</div>
                                        <div id="product_usd_price" class="h5 fw-bold text-primary"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 bg-light rounded text-center">
                                        <div class="text-muted small">Sale Price</div>
                                        <div id="product_sale_price" class="h5 fw-bold text-danger"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5 class="fw-bold text-uppercase text-muted mb-3">Inventory</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small mb-1">Warehouse Qty</label>
                                    <div class="fw-bold"><span id="product_qty"></span> <span id="product_unit"></span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small mb-1">Stock Unit</label>
                                    <div class="fw-bold"><span id="product_stock_unit"></span> <span
                                            id="product_units_per_stock"></span></div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5 class="fw-bold text-uppercase text-muted mb-3">Details</h5>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label text-muted small mb-1">Brand</label>
                                    <div id="product_brand" class="fw-bold"></div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label text-muted small mb-1">Category</label>
                                    <div id="product_category" class="fw-bold"></div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label text-muted small mb-1">Status</label> <br>
                                    <div id="product_status" class="badge badge-primary"></div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label text-muted small mb-1">Description</label>
                                    <div id="product_description" class="fw-bold">No description available</div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label text-muted small mb-1">Created</label>
                                    <div id="product_created_at" class="fw-bold small"></div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label text-muted small mb-1">Updated</label>
                                    <div id="product_updated_at" class="fw-bold small"></div>
                                </div>

                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="product_id">
