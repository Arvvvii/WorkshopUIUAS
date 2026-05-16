document.addEventListener('DOMContentLoaded', () => {
    // 1. Auth Check - Navbar Profile Name & Logout
    const userStr = localStorage.getItem('currentUser');
    if (userStr) {
        try {
            const user = JSON.parse(userStr);
            const loginBtns = document.querySelectorAll('a[href="login.html"]');
            loginBtns.forEach(btn => {
                if (btn.textContent.trim().includes('Log In') || btn.textContent.trim().includes('Login')) {
                    btn.textContent = `Hi, ${user.name}`;
                    btn.href = 'history-transaksi.html';
                    btn.classList.add('flex', 'items-center');

                    const logoutBtn = document.createElement('button');
                    logoutBtn.textContent = 'Logout';
                    logoutBtn.className = 'ml-3 text-xs font-bold text-red-500 hover:text-red-700 transition-colors uppercase tracking-wider bg-red-50 px-3 py-1.5 rounded-lg';
                    logoutBtn.onclick = function() {
                        localStorage.removeItem('currentUser');
                        localStorage.removeItem('blinkco_user');
                        localStorage.removeItem('blinkco_cart');
                        window.location.reload();
                    };
                    btn.parentNode.insertBefore(logoutBtn, btn.nextSibling);
                }
            });
        } catch(e) {}
    }

    // 2. Init cart as flat array (canonical structure)
    const existingCart = localStorage.getItem('blinkco_cart');
    if (!existingCart) {
        localStorage.setItem('blinkco_cart', JSON.stringify([]));
    } else {
        try {
            const parsed = JSON.parse(existingCart);
            // Migrate old {items:[], total:0} structure to flat array
            if (!Array.isArray(parsed) && parsed.items) {
                localStorage.setItem('blinkco_cart', JSON.stringify(parsed.items || []));
            }
        } catch(e) {
            localStorage.setItem('blinkco_cart', JSON.stringify([]));
        }
    }

    updateCartBadge();

    window.addEventListener('storage', (e) => {
        if (e.key === 'blinkco_cart') updateCartBadge();
    });

    // 3. Mobile menu toggle - prevent overlay blocking clicks
    const mobileToggle = document.querySelector('button.md\\:hidden');
    const mobileMenu = document.getElementById('mobile-menu');
    if (mobileToggle && mobileMenu) {
        mobileToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            mobileMenu.classList.toggle('hidden');
        });
        document.addEventListener('click', (e) => {
            if (!mobileMenu.contains(e.target) && !mobileToggle.contains(e.target)) {
                mobileMenu.classList.add('hidden');
            }
        });
    }

    // 4. Cart icon click guard
    document.querySelectorAll('a[aria-label="Cart"], a[href="keranjang.html"]').forEach(icon => {
        icon.addEventListener('click', function(e) {
            if (!localStorage.getItem('currentUser')) {
                e.preventDefault();
                e.stopPropagation();
                alert('Silakan Log In untuk melihat keranjang');
                window.location.href = 'login.html';
            }
        });
    });
});

// ─── ADD TO CART (dynamic product cards on index.html & katalog.html) ───────

window.tambahKeKeranjangDB = function(id, name, price, image) {
    if (!localStorage.getItem('currentUser')) {
        alert('Silakan Log In terlebih dahulu!');
        window.location.href = 'login.html';
        return;
    }
    let cart = JSON.parse(localStorage.getItem('blinkco_cart')) || [];
    if (!Array.isArray(cart)) cart = [];

    const exist = cart.findIndex(i => String(i.id) === String(id));
    if (exist > -1) {
        cart[exist].qty += 1;
    } else {
        cart.push({ id: String(id), name, price: Number(price), qty: 1, image: image || '' });
    }
    localStorage.setItem('blinkco_cart', JSON.stringify(cart));
    updateCartBadge();
    showCleanToast(name, 1);
};

// ─── ADD TO CART (legacy — detail-produk.html) ───────────────────────────────

window.processAddToCart = function(redirect) {
    if (!localStorage.getItem('currentUser')) {
        alert('Silakan Log In untuk mulai berbelanja!');
        window.location.href = 'login.html';
        return;
    }

    const productName = 'Official Lightstick Ver.2';
    const price = 450000;
    const qtyInput = document.getElementById('qtyInput');
    const qty = qtyInput ? parseInt(qtyInput.value) : 1;

    let cart = JSON.parse(localStorage.getItem('blinkco_cart')) || [];
    if (!Array.isArray(cart)) cart = [];

    const exist = cart.findIndex(i => i.id === 'prod_lightstick');
    if (exist > -1) {
        cart[exist].qty += qty;
    } else {
        cart.push({ id: 'prod_lightstick', name: productName, price, qty, image: '' });
    }
    localStorage.setItem('blinkco_cart', JSON.stringify(cart));
    updateCartBadge();

    document.querySelectorAll('#cart-badge').forEach(badge => {
        badge.classList.add('transition-transform', 'duration-300', 'scale-150');
        setTimeout(() => badge.classList.remove('scale-150'), 300);
    });

    if (redirect) {
        window.location.href = 'pembayaran.html';
    }
};

// ─── BADGE ───────────────────────────────────────────────────────────────────

function updateCartBadge() {
    const userStr = localStorage.getItem('currentUser');
    const badgeElements = document.querySelectorAll('#cart-badge');

    if (!userStr) {
        badgeElements.forEach(badge => badge.classList.add('hidden'));
        return;
    }

    try {
        const cart = JSON.parse(localStorage.getItem('blinkco_cart')) || [];
        let count = 0;
        if (Array.isArray(cart)) {
            count = cart.reduce((sum, item) => sum + (item.qty || 1), 0);
        } else if (cart && cart.items) {
            count = cart.items.length;
        }
        badgeElements.forEach(badge => {
            badge.textContent = count;
            count > 0 ? badge.classList.remove('hidden') : badge.classList.add('hidden');
        });
    } catch(e) {}
}

// ─── WISHLIST TOGGLE ──────────────────────────────────────────────────────────

window.toggleWishlist = function(btn) {
    const svg = btn.querySelector('svg');
    if (svg.getAttribute('fill') === 'none') {
        svg.setAttribute('fill', '#F72585');
        svg.setAttribute('stroke', '#F72585');
        svg.classList.add('text-primary');
    } else {
        svg.setAttribute('fill', 'none');
        svg.setAttribute('stroke', 'currentColor');
        svg.classList.remove('text-primary');
    }
};

// ─── TOAST NOTIFICATION ───────────────────────────────────────────────────────

function showCleanToast(productName, qty) {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'fixed bottom-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = 'bg-white border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.08)] rounded-xl p-4 flex items-center gap-4 transform transition-all duration-300 translate-y-12 opacity-0 pointer-events-auto min-w-[300px]';
    toast.innerHTML = `
        <div class="w-10 h-10 bg-green-50 rounded-full flex items-center justify-center flex-shrink-0 text-green-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
        </div>
        <div class="flex-1">
            <h4 class="font-poppins font-semibold text-sm text-textMain leading-tight">Added to Cart</h4>
            <p class="text-xs text-textMuted font-medium mt-0.5">${qty}x ${productName}</p>
        </div>
        <button onclick="this.parentElement.remove()" class="text-gray-400 hover:text-gray-600 transition-colors p-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    `;
    container.appendChild(toast);

    setTimeout(() => toast.classList.remove('translate-y-12', 'opacity-0'), 10);
    setTimeout(() => {
        if (document.body.contains(toast)) {
            toast.classList.add('translate-y-12', 'opacity-0');
            setTimeout(() => { if (document.body.contains(toast)) toast.remove(); }, 300);
        }
    }, 3500);
}
