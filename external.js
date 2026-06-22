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
    
    showToast(`${itemName} added to cart!`);
    updateCartUI();
}

function removeFromCart(index) {
    cart.splice(index, 1);
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

    cart.forEach((item, index) => {
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
            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 5px;">
                <strong>Rs. ${itemTotal.toFixed(2)}</strong>
                <button class="remove-btn" onclick="removeFromCart(${index})">Remove</button>
            </div>
        `;
        cartItemsContainer.appendChild(itemElement);
    });

    cartTotalElement.innerText = total.toFixed(2);
    cartCountElement.innerText = itemCount;
}

function prepareCheckout(event) {
    const orderDetailsInput = document.getElementById('order_details');
    const orderTotalInput = document.getElementById('order_total_input');
    
    if (cart.length === 0) {
        if(event) event.preventDefault();
        showToast("Your cart is empty! Add items before checking out.");
        return false;
    }

    if (orderDetailsInput && orderTotalInput) {
        const detailsString = cart.map(item => `${item.name} (x${item.quantity})`).join(', ');
        
        orderDetailsInput.value = detailsString;
        orderTotalInput.value = total;
    }
}

function showToast(message) {
    const container = document.getElementById('toast-container');
    if (!container) return; 
    
    const toast = document.createElement('div');
    toast.classList.add('toast');
    toast.innerText = message;
    
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('fade-out');
        setTimeout(() => {
            toast.remove();
        }, 300); 
    }, 3000);
}