let products;
refresh();

function getProducts(sku = "%", category = "%", tag = "%") {
    topCount = document.getElementById("resultsCountTop");
    bottomCount = document.getElementById("resultsCountBottom");
    products = GET(apiUrl + "/product?sku=" + sku + "&category_filter=" + category + "&tag_filter=" + tag + "&search=%").response;
    topCount.innerHTML = products.length;
    bottomCount.innerHTML = products.length;
};

function load() {
    getProducts();
}

function refresh() {
    load();
    updateShoppingCartModal();
    loadingOverlay().cancel(spinHandle);
};