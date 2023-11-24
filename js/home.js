load();

function load() {
    refresh();
}

function refresh() {
    updateShoppingCartModal();
    buildCategoriesSwiper();
    loadingOverlay().cancel(spinHandle);
};

function buildCategoriesSwiper() {
    const categoriesSwiper = document.getElementById("categoriesSwiper");
    const categories = GET(apiUrl + "/products/categories?ignore_category=%").response;
    removeAllChildNodes(categoriesSwiper);
    categories.forEach(build);
    function build(element, _, _) {
        const slide = document.createElement("div");
        slide.setAttribute("class", "swiper-slide");
        const product_box = document.createElement("div");
        product_box.setAttribute("class", "product-box style-2 wow fadeInUp");
        product_box.setAttribute("data-wow-delay", "0.4s");
        product_box.setAttribute("style", "background-image: url('" + element['photo'] + "');");
        const product_content = document.createElement("div");
        product_content.setAttribute("class", "product-content");
        const main_content = document.createElement("div");
        main_content.setAttribute("class", "main-content");
        const product_name = document.createElement("h2");
        product_name.setAttribute("class", "product-name");
        product_name.innerHTML = element["name"];
        const cat_link = document.createElement("a");
        console.debug("Category: " + element['category']);
        cat_link.setAttribute("href", "shop?category=" + element['category']);
        cat_link.setAttribute("class", "btn btn-outline-secondary");
        cat_link.innerHTML = "Shop Now";
        main_content.appendChild(product_name);
        product_content.appendChild(main_content);
        product_content.appendChild(cat_link);
        product_box.appendChild(product_content);
        slide.appendChild(product_box);
        categoriesSwiper.appendChild(slide);
        
        categoriesSwiper.appendChild(categoryOption);
    }
};