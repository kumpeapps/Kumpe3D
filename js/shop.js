let products;
load();
function getProducts(sku = "%", category = "%", tag = "%", catalog = "%") {
    topCount = document.getElementById("resultsCountTop");
    bottomCount = document.getElementById("resultsCountBottom");
    products = GET(apiUrl + "/product?sku=" + sku + "&category_filter=" + category + "&tag_filter=" + tag + "&search=%&catalog=" + catalog).response;
    topCount.innerHTML = products.length;
    bottomCount.innerHTML = products.length;
    console.debug("Catalog=" + catalog);
};

function load() {
    refresh();
}

function refresh() {
    const category = document.getElementById("categorySelect").value;
    getProducts('%', category);
    buildCategories();
    buildCatalogs();
    buildProducts();
    updateShoppingCartModal();
    loadingOverlay().cancel(spinHandle);
};


function buildProducts() {
    const selectedCategory = document.getElementById("categorySelect").value;
    const selectedCatalog = document.getElementById("catalogSelect").value;
    getProducts("%", selectedCategory, "%", selectedCatalog);
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
        function buildCard(element) {
            const card = document.createElement("div");
            card.setAttribute("class", "shop-card");
            const media = document.createElement("div");
            media.setAttribute("class", "dz-media");
            const img = document.createElement("img");
            img.setAttribute("src", element.default_photo);
            const imgLink = document.createElement("a");
            if (element.is_coming_soon) {
                
            } else {
                imgLink.setAttribute("href", "product?sku=" + element.sku);
            }
            const content = document.createElement("class");
            content.setAttribute("class", "dz-content");
            const title = document.createElement("h5");
            title.setAttribute("class", "title");
            const titleLink = document.createElement("a");
            if (element.is_coming_soon) {
                
            } else {
                titleLink.setAttribute("href", "product?sku=" + element.sku);
            }
            titleLink.innerHTML = element.title;
            title.appendChild(titleLink);
            const priceLabel = document.createElement("h6");
            priceLabel.setAttribute("class", "price");
            const onSaleTag = document.createElement("div");
            onSaleTag.setAttribute("class", "ribbon ribbon-top-left");
            const onSaleSpan = document.createElement("span");
            onSaleSpan.setAttribute("class", "badge badge-warning");
            onSaleSpan.innerHTML = "On Sale";
            const newTag = document.createElement("div");
            newTag.setAttribute("class", "ribbon ribbon-top-right");
            const newSpan = document.createElement("span");
            if (element.is_coming_soon) {
                newSpan.setAttribute("class", "badge badge-warning");
                newSpan.innerHTML = "Coming Soon";
            } else {
                newSpan.setAttribute("class", "badge badge-success");
                newSpan.innerHTML = "NEW";
            }
            newTag.appendChild(newSpan);
            onSaleTag.appendChild(onSaleSpan);
            if (element.is_on_sale) {
                priceLabel.innerHTML = "<del>$" + element.original_price + "</del> $" + element.price;
            } else {
                priceLabel.innerHTML = "$" + element.price;
            }

            if (element.is_coming_soon) {
                card.appendChild(newTag);
            } else if (element.is_new) {
                card.appendChild(newTag);
            }
            content.appendChild(title);
            content.appendChild(priceLabel);
            imgLink.appendChild(img)
            media.appendChild(imgLink);
            card.appendChild(media);
            card.appendChild(content);
            if (element.is_on_sale) {
                card.appendChild(onSaleTag);
            }
            if (element.is_new) {
                card.appendChild(newTag);
            } else if (element.is_coming_soon) {
                card.appendChild(newTag);
            }
            return card
        }
        divColumn.appendChild(buildCard(element));
        divGrid.appendChild(buildCard(element));
        productsColumn.appendChild(divColumn);
        productsGrid.appendChild(divGrid);
    }
};

function buildCategories() {
    const queryCategory = urlParams.get('category') ?? "%";
    const categorySelect = document.getElementById("categorySelect");
    const categories = GET(apiUrl + "/products/categories").response;
    removeAllChildNodes(categorySelect);
    categories.forEach(build);
    function build(element, _, _) {
        const categoryOption = document.createElement("option");
        categoryOption.setAttribute("value", element.category);
        categoryOption.innerHTML = element.name;
        if (element.category == queryCategory) {
            categoryOption.setAttribute("selected", true);
        }
        categorySelect.appendChild(categoryOption);
    }
};

function buildCatalogs() {
    const queryCatalog = urlParams.get('catalog') ?? "%";
    const catalogSelect = document.getElementById("catalogSelect");
    const categories = GET(apiUrl + "/products/catalogs").response;
    removeAllChildNodes(catalogSelect);
    categories.forEach(build);
    function build(element, _, _) {
        const categoryOption = document.createElement("option");
        categoryOption.setAttribute("value", element.catalog);
        categoryOption.innerHTML = element.name;
        if (element.catalog == queryCatalog) {
            categoryOption.setAttribute("selected", true);
        }
        catalogSelect.appendChild(categoryOption);
    }
    buildProducts();
};