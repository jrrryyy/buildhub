
document.getElementById('product-name').addEventListener('blur', function() {
    const productName = this.value;
    if (productName.trim() === '') return;

    fetch('get_product.php?product_name=' + encodeURIComponent(productName))
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('price').value = data.price;
                document.getElementById('quantity').value = data.quantity;
            } else {
                document.getElementById('price').value = '';
                document.getElementById('quantity').value = '';
            }
        });
});
