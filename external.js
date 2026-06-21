window.addEventListener('load', () => {
    const loader = document.getElementById('loader');
    if (loader) {
        setTimeout(() => {
            loader.classList.add('loader-hidden');
        }, 600);
    }
});

let cart = [];
let total = 0;

function toggleCart() {
    const sidebar = document.getElementById('checkout-sidebar');
    if(sidebar) {
        sidebar.classList.toggle('hidden');
    }
}

function addToCart(itemName, itemPrice) {
    const existingItem = cart.find(item => item.name === itemName);
    
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({ name: itemName, price: itemPrice, quantity: 1 });
    }
    
    updateCartUI();
}

function updateCartUI() {
    const cartItemsContainer = document.getElementById('cart-items');
    const cartTotalElement = document.getElementById('cart-total');
    const cartCountElement = document.getElementById('cart-count');
    
    if (!cartItemsContainer || !cartTotalElement || !cartCountElement) return;

    cartItemsContainer.innerHTML = '';
    total = 0;
    let itemCount = 0;

    cart.forEach(item => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;
        itemCount += item.quantity;

        const itemElement = document.createElement('div');
        itemElement.className = 'cart-item';
        itemElement.innerHTML = `
            <div>
                <h4>${item.name}</h4>
                <small>Rs. ${item.price.toFixed(2)} x ${item.quantity}</small>
            </div>
            <div>
                <strong>Rs. ${itemTotal.toFixed(2)}</strong>
            </div>
        `;
        cartItemsContainer.appendChild(itemElement);
    });

    cartTotalElement.innerText = total.toFixed(2);
    cartCountElement.innerText = itemCount;
}

function prepareCheckout() {
    const orderDetailsInput = document.getElementById('order_details');
    const orderTotalInput = document.getElementById('order_total_input');
    
    if (orderDetailsInput && orderTotalInput) {
        orderDetailsInput.value = JSON.stringify(cart);
        orderTotalInput.value = total;
    }
}