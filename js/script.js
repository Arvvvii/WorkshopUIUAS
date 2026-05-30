document.addEventListener('DOMContentLoaded', () => {
    // 1. Auth Check - Navbar Profile Name & Logout
    const userStr = localStorage.getItem('currentUser');
    if (userStr) {
        try {
            const user = JSON.parse(userStr);
            const loginBtns = document.querySelectorAll('a[href="login.html"]');
            loginBtns.forEach(btn => {
                if (btn.textContent.trim() === 'Log In' || btn.textContent.trim() === 'Login') {
                    btn.textContent = `Hi, ${user.name}`;
                    btn.href = 'profile.html';
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

    // 3. Mobile menu toggle — uses id="mobile-menu-toggle" & id="mobile-menu"
    const mobileToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    if (mobileToggle && mobileMenu) {
        const iconHamburger = `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>`;
        const iconClose = `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>`;

        mobileToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = mobileMenu.classList.toggle('hidden') === false;
            mobileToggle.innerHTML = isOpen ? iconClose : iconHamburger;
        });

        document.addEventListener('click', (e) => {
            if (!mobileMenu.classList.contains('hidden') &&
                !mobileMenu.contains(e.target) &&
                !mobileToggle.contains(e.target)) {
                mobileMenu.classList.add('hidden');
                mobileToggle.innerHTML = iconHamburger;
            }
        });
    }

    // 4. Cart icon click guard
    document.querySelectorAll('a[aria-label="Cart"], a[href="keranjang.html"]').forEach(icon => {
        icon.addEventListener('click', function(e) {
            if (!localStorage.getItem('currentUser')) {
                e.preventDefault();
                e.stopPropagation();
                showInfoToast('Login Diperlukan', 'Silakan login terlebih dahulu');
                setTimeout(() => window.location.href = 'login.html', 1600);
            }
        });
    });

    // 5. Auto-highlight active nav link
    setActiveNavLink();
});

// ─── ACTIVE NAV LINK ──────────────────────────────────────────────────────────

function setActiveNavLink() {
    const currentPath = window.location.pathname.split('/').pop() || 'index.html';

    // Desktop nav links (class="nav-link")
    document.querySelectorAll('nav .nav-link').forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            link.classList.add('text-primary');
        }
    });

    // Mobile nav links (class="mobile-nav-link")
    document.querySelectorAll('#mobile-menu .mobile-nav-link').forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            link.classList.add('text-primary');
        }
    });
}

// ─── ADD TO CART (dynamic product cards — index.html & katalog.html) ─────────

window.tambahKeKeranjangDB = function(id, name, price, image) {
    if (!localStorage.getItem('currentUser')) {
        showInfoToast('Login Diperlukan', 'Login diperlukan untuk menambahkan produk ke keranjang');
        setTimeout(() => window.location.href = 'login.html', 1600);
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

// ─── CART BADGE ───────────────────────────────────────────────────────────────

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

// ─── WISHLIST TOGGLE ─────────────────────────────────────────────────────────

window.toggleWishlist = function(btn) {
    const svg = btn.querySelector('svg');
    if (svg.getAttribute('fill') === 'none') {
        svg.setAttribute('fill', '#F72585');
        svg.setAttribute('stroke', '#F72585');
    } else {
        svg.setAttribute('fill', 'none');
        svg.setAttribute('stroke', 'currentColor');
    }
};

// ─── TOAST NOTIFICATION SYSTEM ───────────────────────────────────────────────

function _getToastContainer() {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'fixed bottom-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none';
        document.body.appendChild(container);
    }
    return container;
}

function _createToast(iconHtml, title, subtitle) {
    const container = _getToastContainer();
    const toast = document.createElement('div');
    toast.className = [
        'bg-white border border-gray-100 rounded-xl p-4',
        'flex items-center gap-4',
        'shadow-[0_8px_30px_rgb(0,0,0,0.08)]',
        'transform transition-all duration-300 translate-y-12 opacity-0',
        'pointer-events-auto min-w-[300px] max-w-sm'
    ].join(' ');

    toast.innerHTML = `
        ${iconHtml}
        <div class="flex-1 min-w-0">
            <h4 class="font-poppins font-semibold text-sm text-gray-900 leading-tight">${title}</h4>
            ${subtitle ? `<p class="text-xs text-gray-500 font-medium mt-0.5 truncate">${subtitle}</p>` : ''}
        </div>
        <button onclick="this.parentElement.remove()" class="text-gray-400 hover:text-gray-600 transition-colors p-1 flex-shrink-0" aria-label="Tutup notifikasi">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>`;

    container.appendChild(toast);
    // Animate in
    requestAnimationFrame(() => {
        requestAnimationFrame(() => toast.classList.remove('translate-y-12', 'opacity-0'));
    });
    // Auto-dismiss after 3.5s
    setTimeout(() => {
        if (document.body.contains(toast)) {
            toast.classList.add('translate-y-12', 'opacity-0');
            setTimeout(() => { if (document.body.contains(toast)) toast.remove(); }, 300);
        }
    }, 3500);
    return toast;
}

// ── Success (green) — "Added to Cart" ──
function showCleanToast(productName, qty) {
    _createToast(
        `<div class="w-10 h-10 bg-green-50 rounded-full flex items-center justify-center flex-shrink-0 text-green-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
        </div>`,
        'Ditambahkan ke Keranjang',
        `${qty}x ${productName}`
    );
}

// ── Generic Success (green) ──
window.showSuccessToast = function(title, subtitle) {
    _createToast(
        `<div class="w-10 h-10 bg-green-50 rounded-full flex items-center justify-center flex-shrink-0 text-green-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
        </div>`,
        title,
        subtitle || ''
    );
};

// ── Error (red) ──
window.showErrorToast = function(title, subtitle) {
    _createToast(
        `<div class="w-10 h-10 bg-red-50 rounded-full flex items-center justify-center flex-shrink-0 text-red-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"></path></svg>
        </div>`,
        title,
        subtitle || ''
    );
};

// ── Info (blue) ──
window.showInfoToast = function(title, subtitle) {
    _createToast(
        `<div class="w-10 h-10 bg-blue-50 rounded-full flex items-center justify-center flex-shrink-0 text-blue-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>`,
        title,
        subtitle || ''
    );
};
