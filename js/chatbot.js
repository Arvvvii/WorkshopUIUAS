document.addEventListener('DOMContentLoaded', () => {
    // 1. Inject HTML into body
    const chatbotHTML = `
    <!-- Floating Bubble -->
    <button id="chatbot-toggle" class="fixed bottom-6 right-6 w-14 h-14 bg-primary text-white rounded-full shadow-xl flex items-center justify-center hover:bg-[#e01970] transition-transform hover:scale-110 z-50 focus:outline-none">
        <svg id="chatbot-icon-chat" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
        <svg id="chatbot-icon-close" class="w-7 h-7 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
    </button>

    <!-- Chatbot Window -->
    <div id="chatbot-window" class="fixed bottom-24 right-6 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col transition-all duration-300 transform scale-0 origin-bottom-right opacity-0 z-50 border border-gray-100" style="height: 500px; max-height: calc(100vh - 120px);">
        
        <!-- Header -->
        <div class="bg-primary px-5 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center text-white font-bold text-xs">B</div>
                <div>
                    <h3 class="font-poppins font-bold text-white leading-tight">BLINKBot</h3>
                    <p class="text-white/80 text-xs font-medium">Selalu online</p>
                </div>
            </div>
        </div>

        <!-- Chat Body -->
        <div id="chat-body-id" class="flex-1 p-5 overflow-y-auto bg-bgAlt flex flex-col gap-4 font-montserrat text-sm">
            <!-- Default Welcome Message -->
            <div class="flex items-start gap-2 max-w-[85%]">
                <div class="w-6 h-6 bg-primary/10 rounded-full flex items-center justify-center text-primary text-[10px] font-bold flex-shrink-0 mt-1">B</div>
                <div class="bg-white px-4 py-3 rounded-2xl rounded-tl-none shadow-sm border border-gray-100 text-textMain leading-relaxed">
                    Halo BLINK! 👋 Saya bisa bantu kamu cek <b>stok barang</b>, info <b>berita terbaru</b>, atau <b>lacak pesanan</b>. Ada yang bisa saya bantu?
                </div>
            </div>
        </div>

        <!-- Chat Input -->
        <form id="chatbot-form" class="p-4 bg-white border-t border-gray-100 flex gap-2 items-center">
            <input type="text" id="chatbot-input" required autocomplete="off" placeholder="Ketik pesan..." class="flex-1 bg-bgAlt border border-gray-200 px-4 py-2.5 rounded-full focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all text-sm font-medium">
            <button type="submit" class="w-10 h-10 bg-primary text-white rounded-full flex items-center justify-center hover:bg-[#e01970] transition-colors flex-shrink-0 focus:outline-none">
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
            </button>
        </form>
    </div>
    `;

    document.body.insertAdjacentHTML('beforeend', chatbotHTML);

    // 2. Elements & Variables
    const toggleBtn = document.getElementById('chatbot-toggle');
    const chatWindow = document.getElementById('chatbot-window');
    const iconChat = document.getElementById('chatbot-icon-chat');
    const iconClose = document.getElementById('chatbot-icon-close');
    const chatForm = document.getElementById('chatbot-form');
    const chatInput = document.getElementById('chatbot-input');
    const chatBody = document.getElementById('chat-body-id');

    let isChatOpen = false;

    // 3. Toggle Logic
    toggleBtn.addEventListener('click', () => {
        isChatOpen = !isChatOpen;
        if (isChatOpen) {
            chatWindow.classList.remove('scale-0', 'opacity-0');
            chatWindow.classList.add('scale-100', 'opacity-100');
            iconChat.classList.add('hidden');
            iconClose.classList.remove('hidden');
            chatInput.focus();
        } else {
            chatWindow.classList.remove('scale-100', 'opacity-100');
            chatWindow.classList.add('scale-0', 'opacity-0');
            iconClose.classList.add('hidden');
            iconChat.classList.remove('hidden');
        }
    });

    // Helper: Add Message to DOM
    function appendMessage(text, sender) {
        const isBot = sender === 'bot';
        let msgHtml = '';

        if (isBot) {
            msgHtml = `
            <div class="flex items-start gap-2 max-w-[85%]">
                <div class="w-6 h-6 bg-primary/10 rounded-full flex items-center justify-center text-primary text-[10px] font-bold flex-shrink-0 mt-1">B</div>
                <div class="bg-white px-4 py-3 rounded-2xl rounded-tl-none shadow-sm border border-gray-100 text-textMain leading-relaxed">
                    ${text}
                </div>
            </div>`;
        } else {
            msgHtml = `
            <div class="flex items-end justify-end gap-2 w-full">
                <div class="bg-primary text-white px-4 py-3 rounded-2xl rounded-tr-none shadow-sm leading-relaxed max-w-[85%] break-words">
                    ${text.replace(/</g, "&lt;").replace(/>/g, "&gt;")}
                </div>
            </div>`;
        }

        chatBody.insertAdjacentHTML('beforeend', msgHtml);
        
        // Auto-Scroll ke Pesan Terbaru
        chatBody.scrollTop = chatBody.scrollHeight;
    }

    // 4. Form Submit Logic
    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const userMsg = chatInput.value.trim();
        if (!userMsg) return;

        // Display user message
        appendMessage(userMsg, 'user');
        chatInput.value = '';

        // Display loading indicator
        const loadingId = 'loading-' + Date.now();
        chatBody.insertAdjacentHTML('beforeend', `
            <div id="${loadingId}" class="flex items-center gap-2 max-w-[85%]">
                <div class="w-6 h-6 bg-primary/10 rounded-full flex items-center justify-center text-primary text-[10px] font-bold flex-shrink-0">B</div>
                <div class="bg-white px-4 py-3 rounded-2xl rounded-tl-none shadow-sm border border-gray-100 text-textMuted text-xs flex gap-1 items-center">
                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce"></span>
                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></span>
                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></span>
                </div>
            </div>
        `);
        // Auto-Scroll setelah menambahkan loading
        chatBody.scrollTop = chatBody.scrollHeight;

        try {
            // Determine api path dynamically
            const basePath = window.location.pathname.includes('/admin/') ? '../api/chatbot.php' : 'api/chatbot.php';
            
            const response = await fetch(basePath, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: userMsg })
            });

            const data = await response.json();
            
            // Remove loading
            const loader = document.getElementById(loadingId);
            if(loader) loader.remove();
            
            if (data.success) {
                appendMessage(data.response, 'bot');
            } else {
                appendMessage("Maaf, terjadi kesalahan saat menyambung ke server.", 'bot');
            }
        } catch (error) {
            // Remove loading
            const loader = document.getElementById(loadingId);
            if(loader) loader.remove();
            
            appendMessage("Maaf, koneksi terputus. Pastikan server berjalan.", 'bot');
        }
    });
});
