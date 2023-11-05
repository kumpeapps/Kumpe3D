let products;


function getProducts(sku = "%", category = "%", tag = "%") {
    products = GET(apiUrl + "/product?sku=" + sku + "&category_filter=" + category + "&tag_filter=" + tag).response;
};

function load() {
    getProducts();
}

function refresh() {
    load();
    updateShoppingCartModal();
    loadingOverlay().cancel(spinHandle);
};