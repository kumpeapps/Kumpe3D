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
    buildProducts();
};


function buildProducts() {
    const productsColumn = document.getElementById("productsColumn");
    const productsGrid = document.getElementById("productsGrid");
    removeAllChildNodes(productsColumn);
    removeAllChildNodes(productsGrid);
    products.forEach(build);
    function build(element, _, _) {
        const divColumn = document.createElement("div");
        divColumn.setAttribute("class", "col-6 col-xl-4 col-lg-6 col-md-6 col-sm-6 m-md-b15 m-sm-b0 m-b30");
        const divGrid = document.createElement("div");
        divGrid.setAttribute("class", "col-6 col-xl-3 col-lg-3 col-md-4 col-sm-6 m-md-b15 m-b30 grid-5");

        const card = document.createElement("div");
        card.setAttribute("class", "shop-card");
        const media = document.createElement("div");
        media.setAttribute("class", "dz-media");
        const img = document.createElement("img");
        img.setAttribute("src", element.default_photo);
        const content = document.createElement("class");
        content.setAttribute("class", "dz-content");
        const title = document.createElement("h5");
        title.setAttribute("class", "title");
        const titleLink = document.createElement("a");
        titleLink.setAttribute("href", "product?sku=" + element.sku);
        titleLink.innerHTML = element.title;
        title.appendChild(titleLink);
        const priceLabel = document.createElement("h6");
        priceLabel.setAttribute("class", "price");
        const productTags = document.createElement("div");
        productTags.setAttribute("class", "product-tag");
        const onSaleTag = document.createElement("span");
        onSaleTag.setAttribute("class", "badge badge-warning");
        onSaleTag.innerHTML = "On Sale";
        const newTag = document.createElement("span");
        newTag.setAttribute("class", "ribbon ribbon-top-left");
        newTag.innerHTML = "NEW";
        if (element.is_on_sale) {
            priceLabel.innerHTML = "<del>$" + element.original_price + "</del> $" + element.price;
            productTags.appendChild(onSaleTag);
        } else {
            priceLabel.innerHTML = "$" + element.price;
        }
        if (element.is_new) {
            productTags.appendChild(newTag);
        }
        content.appendChild(title);
        content.appendChild(priceLabel);
        media.appendChild(img);
        card.appendChild(media);
        card.appendChild(content);
        card.appendChild(productTags);
        divColumn.appendChild(card);
        divGrid.appendChild(card);
        productsColumn.appendChild(divColumn);
        productsGrid.appendChild(divGrid);
    }
};