import os
import re

html_files = [
    'index.html', 'katalog.html', 'detail-produk.html', 'keranjang.html', 
    'pembayaran.html', 'login.html', 'register.html', 'history-transaksi.html', 
    'about.html', 'contact.html', 'arsip-artikel.html', 'detail-artikel.html'
]

navbar_content = """    <!-- Sticky Navbar -->
    <nav class="sticky top-0 w-full bg-white/90 backdrop-blur-md border-b border-gray-100 z-50">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <a href="index.html" class="font-poppins font-bold text-2xl tracking-wide">
                    BLINK<span class="text-primary">CO</span>
                </a>
                
                <!-- Desktop Links -->
                <div class="hidden md:flex space-x-8 text-sm font-medium">
                    <a href="index.html" class="nav-link hover:text-primary transition-colors">Home</a>
                    <a href="katalog.html" class="nav-link hover:text-primary transition-colors">Shop</a>
                    <a href="arsip-artikel.html" class="nav-link hover:text-primary transition-colors">News</a>
                    <a href="about.html" class="nav-link hover:text-primary transition-colors">About</a>
                    <a href="contact.html" class="nav-link hover:text-primary transition-colors">Contact</a>
                    <a href="history-transaksi.html" class="nav-link hover:text-primary transition-colors">Orders</a>
                </div>
                
                <!-- Actions -->
                <div class="flex items-center space-x-5">
                    <!-- Cart Icon -->
                    <a href="keranjang.html" class="relative hover:text-primary transition-colors" aria-label="Cart">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        <span id="cart-badge" class="absolute -top-1.5 -right-1.5 bg-primary text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center hidden">0</span>
                    </a>
                    
                    <!-- Login Button -->
                    <a href="login.html" class="hidden md:inline-flex bg-textMain text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-gray-800 transition-colors shadow-sm">
                        Log In
                    </a>
                    
                    <!-- Mobile Menu Toggle -->
                    <button id="mobile-menu-toggle" class="md:hidden hover:text-primary focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                </div>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-100 absolute w-full shadow-lg">
            <div class="px-4 pt-2 pb-6 space-y-2 flex flex-col">
                <a href="index.html" class="mobile-nav-link block px-3 py-2 rounded-md text-base font-medium hover:text-primary hover:bg-gray-50">Home</a>
                <a href="katalog.html" class="mobile-nav-link block px-3 py-2 rounded-md text-base font-medium hover:text-primary hover:bg-gray-50">Shop</a>
                <a href="arsip-artikel.html" class="mobile-nav-link block px-3 py-2 rounded-md text-base font-medium hover:text-primary hover:bg-gray-50">News</a>
                <a href="about.html" class="mobile-nav-link block px-3 py-2 rounded-md text-base font-medium hover:text-primary hover:bg-gray-50">About</a>
                <a href="contact.html" class="mobile-nav-link block px-3 py-2 rounded-md text-base font-medium hover:text-primary hover:bg-gray-50">Contact</a>
                <a href="history-transaksi.html" class="mobile-nav-link block px-3 py-2 rounded-md text-base font-medium hover:text-primary hover:bg-gray-50">Orders</a>
                <a href="login.html" class="mobile-nav-link block px-3 py-2 mt-4 text-center rounded-md text-base font-medium bg-textMain text-white hover:bg-gray-800">Log In</a>
            </div>
        </div>
    </nav>"""

for f in html_files:
    filepath = os.path.join(r'd:\laragon\www\BLINKCO', f)
    if not os.path.exists(filepath): continue
    with open(filepath, 'r', encoding='utf-8') as file:
        content = file.read()
    
    # Check if file has nav block
    if '<nav ' in content:
        # replace from <!-- Sticky Navbar --> or <nav... to </nav>
        content = re.sub(r'(?:<!-- Sticky Navbar -->\s*)?<nav.*?</nav>', navbar_content, content, flags=re.DOTALL)
        with open(filepath, 'w', encoding='utf-8') as file:
            file.write(content)
        print("Updated navbar in " + f)
