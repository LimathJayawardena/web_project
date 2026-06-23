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
                <h4 style="font-size: 0.9rem;">${item.name}</h4>
                <small>Rs. ${item.price.toFixed(2)} x ${item.quantity}</small>
            </div>
            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 5px;">
                <strong style="font-size: 0.9rem;">Rs. ${itemTotal.toFixed(2)}</strong>
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
    const emailInput = document.getElementById('email');
    
    if (cart.length === 0) {
        if(event) event.preventDefault();
        showToast("Your cart is empty! Add items before checking out.");
        return false;
    }

    if (emailInput && emailInput.value) {
        const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
        
        if (!emailPattern.test(emailInput.value)) {
            if(event) event.preventDefault();
            showToast("Please enter a valid email address!");
            emailInput.focus(); 
            return false;
        }
        
        if (!emailInput.value.endsWith("@students.nsbm.ac.lk")) {
            if(event) event.preventDefault();
            showToast("You must use an @students.nsbm.ac.lk email!");
            emailInput.focus();
            return false;
        }
    }

    if (orderDetailsInput && orderTotalInput) {
        orderDetailsInput.value = JSON.stringify(cart);
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