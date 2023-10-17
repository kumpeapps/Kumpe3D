const changeQty = document.querySelector("#qty");
const priceLabel = document.querySelector("#priceLabel");
const totalPriceLabel = document.querySelector("#totalPriceLabel");
const skuLabel = document.querySelector("#skuLabel");
const addToCartButton = document.querySelector("#addToCartButton");
const titleCrumb = document.querySelector("#titleCrumb");
const titleLabel = document.querySelector("#titleLabel");
const descriptionLabel = document.querySelector("#descriptionLabel");
if (env == 'dev') {
    Swal.fire(
        'Pre-Prod Server!',
        'You are viewing the Pre-Production/Dev server. Orders submitted via this site will not be filled or charged. Only PayPal sandbox accounts/credit cards will work.',
        'warning'
    );
};
const querySKU = urlParams.get('sku')
let product = GET(apiUrl + "/product?sku=" + querySKU).response
const productImages = GET(apiUrl + "/product-images?sku=" + querySKU).response
titleCrumb.innerHTML = product.title
titleLabel.innerHTML = product.title
descriptionLabel.innerHTML = product.description
document.getElementById("categoryLabel").innerHTML = product.categories
document.getElementById("tagsLabel").innerHTML = product.tags
refresh();
buildImageGallery();

function getColorValue() {
    const ele = document.getElementsByName('radioColor');
    for (i = 0; i < ele.length; i++) {
        if (ele[i].checked)
            return ele[i].value;
    }
    return "000";
};

function changedColor() {
    const color_id = getColorValue();
    const base_sku = product.sku_parts.base_sku;
    const qty = document.getElementById('qty').value;
    let sku = base_sku + '-' + color_id;
    skuLabel.innerHTML = sku;
};

changeQty.addEventListener("change", function () {
    changedQty();
});

changeQty.addEventListener("keyup", function () {
    changedQty();
});

addToCartButton.addEventListener("click", function () {
    addToCart();
});

function changedQty() {
    const qty = document.getElementById('qty').value;
    const isOnSaleBadge = document.getElementById('isOnSaleBadge');
    const productPrice = GET(apiUrl + "/product-price?sku=" + querySKU + "&quantity=" + qty).response
    if (Boolean(productPrice.isOnSale)) {
        isOnSaleBadge.removeAttribute("hidden")
    }
    let totalPrice = productPrice.price
    // Update total price on qty change (and give wholesale price if >=10)
    if (productPrice.price !== productPrice.originalPrice) {
        totalPrice = productPrice.price * qty;
        const originalTotal = productPrice.originalPrice * qty;
        priceLabel.innerHTML = '$' + productPrice.price + ' <del>$' + productPrice.originalPrice + '</del>';
        const newTotalPriceLabel = '$' + totalPrice + ' <del>$' + originalTotal + '</del>';
        totalPriceLabel.innerHTML = newTotalPriceLabel;
    } else {
        totalPrice = productPrice.price * qty;
        priceLabel.textContent = '$' + productPrice.price;
        const newTotalPriceLabel = '$' + totalPrice;
        totalPriceLabel.textContent = newTotalPriceLabel;
    }
    changedColor();
};

function isColorSet() {
    const ele = document.getElementsByName('radioColor');
    for (i = 0; i < ele.length; i++) {
        if (ele[i].checked)
            return true;
    }
    return false;
};

function refresh() {
    changedQty();
    updateShoppingCartModal();
};

function buildImages(images) {
    const div1 = document.createElement("div");
    div1.setAttribute("class", "swiper product-gallery-swiper2");
    const wrapper = document.createElement("div");
    wrapper.setAttribute("class", "swiper-wrapper");
    wrapper.setAttribute("id", "lightgallery");
    images.forEach(renderImages);
    function renderImages(element, _, _) {
        const image = document.createElement("img");
        image.setAttribute("src", element['file_path']);
        const feather = document.createElement("i");
        feather.setAttribute("class", "feather icon-maximize dz-maximize top-left");
        const link = document.createElement("a");
        link.setAttribute("class", "mfp-link lg-item");
        link.setAttribute("href", element['file_path']);
        link.setAttribute("data-src", element['file_path']);
        link.appendChild(feather);
        const media = document.createElement("div");
        media.setAttribute("class", "dz-media DZoomImage");
        media.appendChild(link);
        const slide = document.createElement("div");
        slide.setAttribute("class", "swiper-slide");
        slide.appendChild(media);
        wrapper.appendChild(slide);
    }
    div1.appendChild(wrapper);
    return div1
};

function buildThumbnails(images) {
    const div1 = document.createElement("div");
    div1.setAttribute("class", "swiper product-gallery-swiper thumb-swiper-lg");
    const wrapper = document.createElement("div");
    wrapper.setAttribute("class", "swiper-wrapper");
    images.forEach(renderThumbnails);
    function renderThumbnails(element, _, _) {
        const image = document.createElement("img");
        image.setAttribute("src", element['file_path']);
        const slider = document.createElement("div");
        slider.setAttribute("class", "swiper-slide");
        slider.appendChild(image);
        wrapper.appendChild(slider);
    }
    div1.appendChild(wrapper);
    return div1
};

function buildImageGallery(images) {
    const productImageGallery = document.getElementById("productImageGallery");
    removeAllChildNodes(productImageGallery);
    productImageGallery.appendChild(buildImages(images));
    productImageGallery.appendChild(buildThumbnails(images));
};