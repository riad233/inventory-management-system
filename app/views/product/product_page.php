<link href="css/product.css" rel="stylesheet">

<div class="product-page">
  <div class="product-hero">
    <h1>Product Masterfile</h1>
    <p>Capture every detail for inventory, finance, and compliance in a single product record.</p>
    <div class="hero-meta">
      <div class="hero-chip"><i class="fas fa-layer-group"></i> Inventory Control</div>
      <div class="hero-chip"><i class="fas fa-shield-alt"></i> Compliance Ready</div>
      <div class="hero-chip"><i class="fas fa-tags"></i> Pricing & Margin</div>
    </div>
  </div>

  <form class="product-form" method="post" action="">
    <div class="section-card">
      <div class="section-title">
        <span><i class="fas fa-id-card"></i></span>
        Product Identification
      </div>
      <div class="form-grid">
        <div>
          <label class="form-label">Product ID</label>
          <input type="text" name="product_id" class="form-control" placeholder="PRD-000248">
        </div>
        <div>
          <label class="form-label">Product Name</label>
          <input type="text" name="product_name" class="form-control" placeholder="Ultra-Quiet Air Compressor" required>
        </div>
        <div>
          <label class="form-label">SKU (Stock Keeping Unit)</label>
          <input type="text" name="sku" class="form-control" placeholder="AC-QUIET-240">
        </div>
        <div>
          <label class="form-label">Barcode / UPC / EAN</label>
          <input type="text" name="barcode" class="form-control" placeholder="0123456789012">
        </div>
        <div>
          <label class="form-label">Brand / Manufacturer</label>
          <input type="text" name="brand" class="form-control" placeholder="Atlas Industries">
        </div>
      </div>
    </div>

    <div class="section-card">
      <div class="section-title">
        <span><i class="fas fa-sitemap"></i></span>
        Categorization
      </div>
      <div class="form-grid">
        <div>
          <label class="form-label">Category</label>
          <input type="text" name="category" class="form-control" placeholder="Equipment">
        </div>
        <div>
          <label class="form-label">Sub-category</label>
          <input type="text" name="subcategory" class="form-control" placeholder="Compressors">
        </div>
        <div>
          <label class="form-label">Tags / Keywords</label>
          <input type="text" name="tags" class="form-control" placeholder="portable, low-noise, 24L">
        </div>
        <div>
          <label class="form-label">Status (Active/Inactive)</label>
          <select name="status" class="form-select">
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
          </select>
        </div>
      </div>
    </div>

    <div class="section-card">
      <div class="section-title">
        <span><i class="fas fa-warehouse"></i></span>
        Inventory & Warehouse
      </div>
      <div class="form-grid">
        <div>
          <label class="form-label">Unit of Measure (UoM)</label>
          <input type="text" name="uom" class="form-control" placeholder="Piece / Unit">
        </div>
        <div>
          <label class="form-label">Opening Stock / Initial Quantity</label>
          <input type="number" name="opening_stock" class="form-control" placeholder="120" min="0">
        </div>
        <div>
          <label class="form-label">Current Quantity on Hand</label>
          <input type="number" name="current_stock" class="form-control" placeholder="98" min="0">
        </div>
        <div>
          <label class="form-label">Reorder Level (Safety Stock)</label>
          <input type="number" name="reorder_level" class="form-control" placeholder="25" min="0">
        </div>
        <div>
          <label class="form-label">Reorder Quantity</label>
          <input type="number" name="reorder_qty" class="form-control" placeholder="50" min="0">
        </div>
        <div>
          <label class="form-label">Warehouse / Bin Location</label>
          <input type="text" name="warehouse" class="form-control" placeholder="WH-A / BIN-12">
        </div>
        <div>
          <label class="form-label">Lead Time</label>
          <input type="text" name="lead_time" class="form-control" placeholder="7 business days">
          <div class="helper-text">Time between ordering and receiving.</div>
        </div>
      </div>
    </div>

    <div class="section-card">
      <div class="section-title">
        <span><i class="fas fa-coins"></i></span>
        Pricing & Finance
      </div>
      <div class="form-grid">
        <div>
          <label class="form-label">Purchase Price (Cost)</label>
          <input type="number" name="purchase_price" class="form-control" placeholder="680" step="0.01" min="0">
        </div>
        <div>
          <label class="form-label">Selling Price (MSRP)</label>
          <input type="number" name="selling_price" class="form-control" placeholder="899" step="0.01" min="0">
        </div>
        <div>
          <label class="form-label">Wholesale Price</label>
          <input type="number" name="wholesale_price" class="form-control" placeholder="820" step="0.01" min="0">
        </div>
        <div>
          <label class="form-label">Tax Class / Rate</label>
          <input type="text" name="tax_rate" class="form-control" placeholder="VAT 7.5%">
        </div>
        <div>
          <label class="form-label">Discount Amount/Percentage</label>
          <input type="text" name="discount" class="form-control" placeholder="5% or 25.00">
        </div>
        <div>
          <label class="form-label">Currency</label>
          <select name="currency" class="form-select">
            <option value="USD">USD</option>
            <option value="EUR">EUR</option>
            <option value="GBP">GBP</option>
            <option value="INR">INR</option>
            <option value="NGN">NGN</option>
          </select>
        </div>
      </div>
    </div>

    <div class="section-card">
      <div class="section-title">
        <span><i class="fas fa-dolly"></i></span>
        Physical Attributes & Logistics
      </div>
      <div class="form-grid">
        <div>
          <label class="form-label">Weight</label>
          <input type="text" name="weight" class="form-control" placeholder="12.5 kg">
        </div>
        <div>
          <label class="form-label">Length</label>
          <input type="text" name="length" class="form-control" placeholder="65 cm">
        </div>
        <div>
          <label class="form-label">Width</label>
          <input type="text" name="width" class="form-control" placeholder="32 cm">
        </div>
        <div>
          <label class="form-label">Height</label>
          <input type="text" name="height" class="form-control" placeholder="55 cm">
        </div>
        <div>
          <label class="form-label">Color</label>
          <input type="text" name="color" class="form-control" placeholder="Matte Black">
        </div>
        <div>
          <label class="form-label">Size</label>
          <input type="text" name="size" class="form-control" placeholder="Medium">
        </div>
        <div>
          <label class="form-label">Material</label>
          <input type="text" name="material" class="form-control" placeholder="Powder-coated steel">
        </div>
      </div>
    </div>

    <div class="section-card">
      <div class="section-title">
        <span><i class="fas fa-shield-check"></i></span>
        Traceability & Compliance
      </div>
      <div class="form-grid">
        <div>
          <label class="form-label">Supplier / Vendor ID</label>
          <input type="text" name="vendor_id" class="form-control" placeholder="VND-1098">
        </div>
        <div>
          <label class="form-label">Batch / Lot Number</label>
          <input type="text" name="batch_number" class="form-control" placeholder="LOT-2026-0413">
        </div>
        <div>
          <label class="form-label">Serial Number</label>
          <input type="text" name="serial_number" class="form-control" placeholder="SN-88910012">
        </div>
        <div>
          <label class="form-label">Manufacturing Date</label>
          <input type="date" name="manufacturing_date" class="form-control">
        </div>
        <div>
          <label class="form-label">Expiry Date</label>
          <input type="date" name="expiry_date" class="form-control">
        </div>
        <div>
          <label class="form-label">Warranty Period</label>
          <input type="text" name="warranty_period" class="form-control" placeholder="18 months">
        </div>
      </div>
    </div>

    <div class="section-card">
      <div class="section-title">
        <span><i class="fas fa-photo-video"></i></span>
        Media & Documentation
      </div>
      <div class="media-grid">
        <div>
          <label class="form-label">Product Description</label>
          <textarea name="description" class="form-control" rows="8" placeholder="Describe the product features, usage, and value proposition."></textarea>
        </div>
        <div class="form-grid">
          <div>
            <label class="form-label">Product Image URL / File</label>
            <input type="url" name="image_url" class="form-control" placeholder="https://">
            <div class="helper-text">Add a URL or upload a file below.</div>
            <input type="file" name="image_file" class="form-control mt-2" accept="image/*">
          </div>
          <div>
            <label class="form-label">Technical Specifications (PDF/Text)</label>
            <input type="file" name="spec_file" class="form-control" accept="application/pdf">
            <textarea name="spec_text" class="form-control mt-2" rows="4" placeholder="Paste key specifications or standards."></textarea>
          </div>
        </div>
      </div>
    </div>

    <div class="action-row">
      <button type="reset" class="btn btn-outline-secondary">Clear Form</button>
      <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> Save Product
      </button>
    </div>
  </form>
</div>
