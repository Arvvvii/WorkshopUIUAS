import os
import re

filepath = r'd:\laragon\www\BLINKCO\detail-produk.html'

with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# Add ID to breadcrumb category
content = content.replace(
    '<a href="#" class="hover:text-primary transition-colors">Accessories</a> &rsaquo;',
    '<a href="#" class="hover:text-primary transition-colors" id="bc-cat">Category</a> &rsaquo;'
)

# Add ID to breadcrumb name
content = content.replace(
    '<span class="text-textMain">Official Lightstick Ver.2</span>',
    '<span class="text-textMain" id="bc-name">Product Name</span>'
)

# Add ID to main image
content = content.replace(
    '<img src="https://placehold.co/800x800/f3f4f6/f72585?text=Lightstick+Ver.2" alt="Product Image" class="w-full h-full object-contain">',
    '<img id="main-img" src="https://placehold.co/800x800/f3f4f6/f72585?text=Loading" alt="Product Image" class="w-full h-full object-contain transition-opacity duration-300">'
)

# Replace thumbnails with dynamic container
thumb_html = '''                    <!-- Thumbnails -->
                    <div id="thumb-container" class="grid grid-cols-4 gap-4 mt-4">
                        <!-- Dynamic thumbs -->
                    </div>'''
content = re.sub(r'<!-- Thumbnails -->.*?</div>\s*</div>', thumb_html + '\n                </div>', content, flags=re.DOTALL)

# Add IDs to product info
content = content.replace(
    '<h1 class="font-poppins font-bold text-3xl md:text-4xl lg:text-5xl mb-4 text-textMain leading-tight">Official Lightstick Ver.2</h1>',
    '<h1 id="prod-name" class="font-poppins font-bold text-3xl md:text-4xl lg:text-5xl mb-4 text-textMain leading-tight">Loading...</h1>'
)

content = content.replace(
    '<p class="font-poppins font-bold text-3xl text-primary">Rp 450,000</p>',
    '<p id="prod-price" class="font-poppins font-bold text-3xl text-primary">Rp 0</p>'
)

content = re.sub(
    r'<p class="text-textMuted leading-relaxed mb-8 font-medium">\s*The official.*?<\/p>',
    '<p id="prod-desc" class="text-textMuted leading-relaxed mb-8 font-medium">Loading description...</p>',
    content, flags=re.DOTALL
)

# Rewrite scripts
new_script = '''    <script>
        let currentQty = 1;
        let activeProduct = null;

        function updateQty(change) {
            currentQty += change;
            if (currentQty < 1) currentQty = 1;
            document.getElementById('qtyInput').value = currentQty;
        }

        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const productId = urlParams.get('id');

            if (!productId) {
                showErrorToast('Error', 'Product ID not found.');
                return;
            }

            fetch('api/get_products.php')
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const product = data.data.find(p => String(p.id) === productId);
                        if (product) {
                            activeProduct = product;
                            renderProduct(product);
                        } else {
                            showErrorToast('Error', 'Product not found.');
                        }
                    }
                })
                .catch(err => {
                    showErrorToast('Error', 'Failed to load product.');
                });
        });

        function renderProduct(product) {
            document.title = product.name + ' | BLINKCO';
            document.getElementById('bc-cat').textContent = product.category_name || 'Uncategorized';
            document.getElementById('bc-name').textContent = product.name;
            document.getElementById('prod-name').textContent = product.name;
            document.getElementById('prod-desc').textContent = product.description || 'No description available.';
            
            const priceStr = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(product.price).replace(/,00$/, '');
            document.getElementById('prod-price').textContent = priceStr;
            
            const mainImg = product.image_url || product.image || 'https://placehold.co/800x800/f3f4f6/f72585?text=Image';
            document.getElementById('main-img').src = mainImg;

            // Optional: Mock thumbnails if product has gallery
            const thumbContainer = document.getElementById('thumb-container');
            const images = [mainImg];
            if (product.image_url_2) images.push(product.image_url_2);
            if (product.image_url_3) images.push(product.image_url_3);
            if (product.image_url_4) images.push(product.image_url_4);

            thumbContainer.innerHTML = '';
            images.forEach((imgSrc, index) => {
                const borderClass = index === 0 ? 'border-primary' : 'border-gray-200 hover:border-primary';
                thumbContainer.innerHTML += `
                    <div class="aspect-square bg-white rounded-2xl border ${borderClass} overflow-hidden cursor-pointer transition-colors" onclick="changeMainImg(this, '${imgSrc}')">
                        <img src="${imgSrc}" class="w-full h-full object-cover">
                    </div>
                `;
            });
        }

        function changeMainImg(element, src) {
            document.getElementById('main-img').src = src;
            const thumbs = document.getElementById('thumb-container').children;
            for(let thumb of thumbs) {
                thumb.className = thumb.className.replace('border-primary', 'border-gray-200 hover:border-primary');
            }
            element.className = element.className.replace('border-gray-200 hover:border-primary', 'border-primary');
        }

        function checkLogin() {
            if (!localStorage.getItem('currentUser')) {
                showInfoToast('Login Diperlukan', 'Silakan Log In terlebih dahulu untuk mulai berbelanja!');
                setTimeout(() => window.location.href = 'login.html', 1500);
                return false;
            }
            return true;
        }

        function masukKeranjang() {
            if (!checkLogin() || !activeProduct) return;
            window.tambahKeKeranjangDB(
                activeProduct.id,
                activeProduct.name,
                activeProduct.price,
                activeProduct.image_url || activeProduct.image
            );
            // We already call showCleanToast in script.js inside tambahKeKeranjangDB
        }

        function beliLangsung() {
            if (!checkLogin() || !activeProduct) return;
            
            let cart = JSON.parse(localStorage.getItem('blinkco_cart')) || [];
            if (!Array.isArray(cart)) cart = [];

            const exist = cart.findIndex(i => String(i.id) === String(activeProduct.id));
            if (exist > -1) {
                cart[exist].qty += currentQty;
            } else {
                cart.push({
                    id: String(activeProduct.id),
                    name: activeProduct.name,
                    price: Number(activeProduct.price),
                    qty: currentQty,
                    image: activeProduct.image_url || activeProduct.image || ''
                });
            }
            localStorage.setItem('blinkco_cart', JSON.stringify(cart));
            
            window.location.href = 'pembayaran.html';
        }
    </script>'''

content = re.sub(r'<script>\s*// Quantity Logic.*?<\/script>', new_script, content, flags=re.DOTALL)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(content)
print("detail-produk.html updated.")
