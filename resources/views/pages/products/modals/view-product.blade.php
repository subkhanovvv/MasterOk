<div class="modal fade" id="viewProductModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h4 class="modal-title fw-bold">Product Details</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <!-- Product Image Section -->
                    <div class="col-md-5 border-end pe-4">
                        <div class="text-center mb-4">
                            <img id="product_photo" src="" 
                                class="img-fluid rounded shadow-sm border" 
                                alt="Product Image"
                                style="max-height: 250px; width: auto; object-fit: contain;">
                        </div>
                        
                        <div class="d-flex justify-content-center mb-3">
                            <img id="product_barcode" src="" 
                                alt="Barcode" 
                                class="img-fluid" 
                                style="max-height: 60px; width: auto;">
                        </div>
                        
                        <div class="text-center">
                            <h3 id="product_name" class="fw-bold mb-0"></h3>
                            <div class="d-flex justify-content-center align-items-center gap-2 mt-2">
                                <span id="product_brand" class="badge bg-primary bg-opacity-10 text-primary"></span>
                                <span id="product_category" class="badge bg-secondary bg-opacity-10 text-secondary"></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product Details Section -->
                    <div class="col-md-7 ps-4">
                        <div class="mb-4">
                            <h5 class="fw-bold text-uppercase text-muted mb-3">Pricing</h5>
                            <div class="row g-3">
                                <div class="col-4">
                                    <div class="p-3 bg-light rounded text-center">
                                        <div class="text-muted small">UZS Price</div>
                                        <div id="product_uzs_price" class="h5 fw-bold text-success"></div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-3 bg-light rounded text-center">
                                        <div class="text-muted small">USD Price</div>
                                        <div id="product_usd_price" class="h5 fw-bold text-primary"></div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-3 bg-light rounded text-center">
                                        <div class="text-muted small">Sale Price</div>
                                        <div id="product_sale_price" class="h5 fw-bold text-danger"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h5 class="fw-bold text-uppercase text-muted mb-3">Details</h5>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="form-label text-muted small mb-1">Stock Quantity</label>
                                    <div id="product_quantity" class="fw-bold">-</div>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label text-muted small mb-1">Product Code</label>
                                    <div id="product_code" class="fw-bold">-</div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted small mb-1">Description</label>
                                    <div id="product_description" class="fw-light">No description available</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Additional fields can be added here -->
                        <div class="alert alert-light mt-4">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-info-circle-fill text-primary me-2"></i>
                                <small class="text-muted">Last updated: <span id="product_updated_at">-</span></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Edit Product</button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden field -->
<input type="hidden" id="product_id">