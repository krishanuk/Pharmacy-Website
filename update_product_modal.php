<div class="modal-header">
    <h5 class="modal-title">Update Product - <?= htmlspecialchars($product['ProductName']) ?></h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <form method="POST" action="update_product.php" enctype="multipart/form-data">
        <input type="hidden" name="product_id" value="<?= $product['ProductID'] ?>">

        <div class="form-group">
            <label for="product_name">Product Name:</label>
            <input type="text" class="form-control" id="product_name" name="product_name" value="<?= htmlspecialchars($product['ProductName']) ?>" required>
        </div>

        <div class="form-group">
            <label for="product_price">Price:</label>
            <input type="number" class="form-control" id="product_price" name="product_price" step="0.01" value="<?= $product['ProductPrice'] ?>" required>
        </div>

        <div class="form-group">
            <label for="stock_quantity">Stock Quantity:</label>
            <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" value="<?= $batch['Quantity'] ?>" required>
        </div>

        <div class="form-group">
            <label for="batch_number">Batch Number:</label>
            <input type="text" class="form-control" id="batch_number" name="batch_number" value="<?= $batch['BatchNumber'] ?>" required>
        </div>

        <div class="form-group">
            <label for="expiry_date">Expiry Date:</label>
            <input type="date" class="form-control" id="expiry_date" name="expiry_date" value="<?= $batch['ExpiryDate'] ?>" required>
        </div>

        <div class="form-group">
            <label for="image">Product Image:</label>
            <input type="file" class="form-control-file" id="image" name="image">
            <small>Leave empty if you don't want to change the image.</small>
        </div>

        <button type="submit" class="btn btn-primary">Update Product</button>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>
