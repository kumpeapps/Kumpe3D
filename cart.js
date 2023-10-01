function buildShoppingCartModalList() {
    const ul = document.getElementById('shoppingCartModal');
    const subtotalLabel = document.getElementById('subtotalLabel');
    removeAllChildNodes(ul);
    subtotalLabel.innerHTML = '$' + cartLS.total();
    cart = cartLS.list();
    cart.forEach(renderShoppingCartModalList);

    function renderShoppingCartModalList(element, index, arr) {
        const img_url = cart[index]["image_url"];
        const title = cart[index]["name"];
        const qty = cart[index]["quantity"];
        const original_price = cart[index]["original_price"];
        let price = '$' + (cart[index]["price"] * qty);
        if (cart[index]["price"] != original_price) {
            price = price + ' <del>$' + (original_price * qty) + '</del>';
        }
        const sku = cart[0]["id"];
        let li = document.createElement('li');
        let div1 = document.createElement('div');
        div1.setAttribute('class','cart-widget');
        let div11 = document.createElement('div');
        div11.setAttribute('class', 'dz-media me-4');
        div1.appendChild(div11);
        let img111 = document.createElement('img');
        img111.setAttribute('src', img_url);
        img111.setAttribute('width', '80');
        img111.setAttribute('height', '80');
        div1.appendChild(img111);
        let div12 = document.createElement('div');
        div12.setAttribute('class', 'cart-content');
        div1.appendChild(div12);
        let title121 = document.createElement('h6');
        title121.setAttribute('class', 'title');
        title121.innerHTML = title;
        div12.appendChild(title121);
        let div122 = document.createElement('div');
        div122.setAttribute('class', 'd-flex align-itmes-center');
        div12.appendChild(div122);
        let div1221 = document.createElement('div');
        div1221.setAttribute('class', 'btn-quantity light quantity-sm me-3');
        div122.appendChild(div1221);
        let input12211 = document.createElement('input');
        input12211.setAttribute('type', 'text');
        input12211.setAttribute('value', qty);
        input12211.setAttribute('name', 'disable-demo-vertical2');
        input12211.setAttribute('readonly', true);
        div1221.appendChild(input12211);
        let text1222 = document.createElement('h6');
        text1222.setAttribute('class', 'dz-price text-primary mb-0');
        text1222.innerHTML = price;
        div122.appendChild(text1222);
        // let button13 = document.createElement('button');
        // button13.setAttribute('class', 'dz-close');
        // let ticlose = document.createElement('i');
        // ticlose.setAttribute('class', 'ti-close');
        // button13.appendChild(ticlose);
        // button13.setAttribute('onclick', "deleteItem('" + sku + "')");
        // div1.appendChild(button13);
        li.appendChild(div1);

        ul.appendChild(li);
    }
};

function removeAllChildNodes(parent) {
    while (parent.firstChild) {
        parent.removeChild(parent.firstChild);
    }
};

function deleteItem(sku) {
    cartLS.remove(cartLS.get(sku));
    buildShoppingCartModalList();
};