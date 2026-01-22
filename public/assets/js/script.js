/**
 * YouCode Job Dating - Main Controller
 * Handles Navbar (Sidebar) generation and Theme management.
 */

// 1. Navigation Configuration
const NAV_ITEMS = [
    { href: '/admin/dashboard', icon: 'dashboard', label: 'Dashboard' },
    { href: '/admin/announcements', icon: 'campaign', label: 'Announcements' },
    { href: '/admin/students', icon: 'group', label: 'Candidates' },
    { href: '/admin/companies', icon: 'corporate_fare', label: 'Companies' },
    { href: '/admin/announcements/archived', icon: 'archive', label: 'Archived' }

];

// 2. Helper: Detect Active Page
function getActivePageName() {
    const path = window.location.pathname;
    const fileName = path.split("/").pop().split('#')[0].split('?')[0];
    
    if (!fileName || fileName === '' || fileName === 'index' || fileName === '/') {
        return 'index.html';
    }
    return fileName.endsWith('.html') ? fileName : fileName + '.html';
}

// 3. Theme Management
function initTheme() {
    const savedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const isDark = savedTheme === 'dark' || (!savedTheme && prefersDark);
    
    if (isDark) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
}

function toggleTheme() {
    const isDark = document.documentElement.classList.toggle('dark');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    updateThemeUI();
}

function updateThemeUI() {
    const isDark = document.documentElement.classList.contains('dark');
    const icon = document.getElementById('theme-icon');
    const text = document.getElementById('theme-text');
    if (icon) icon.innerText = isDark ? 'light_mode' : 'dark_mode';
    if (text) text.innerText = isDark ? 'Light Mode' : 'Dark Mode';
}

// 4. Sidebar Component (The Navbar)
function renderSidebar() {
    const container = document.getElementById('sidebar-container');
    if (!container) return;

    const currentPage = getActivePageName();

    // Generate Nav Links
    const navLinksHtml = NAV_ITEMS.map(item => {
        const isActive = currentPage === item.href;
        return `
            <a href="${item.href}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all font-semibold text-sm ${
                isActive 
                    ? 'bg-primary/10 text-primary border-r-4 border-primary' 
                    : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white'
            }">
                <span class="material-symbols-outlined ${isActive ? 'fill' : ''}">${item.icon}</span>
                <span>${item.label}</span>
            </a>
        `;
    }).join('');

    // Sidebar Template
    container.innerHTML = `
    <aside class="w-full bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 h-screen flex flex-col transition-colors overflow-hidden">
        <div class="p-8 flex flex-col h-full">
            <!-- Logo Section -->
            <div class="flex items-center gap-3 mb-10 shrink-0">
                <div class="bg-primary size-10 rounded-xl flex items-center justify-center text-white shadow-lg shadow-primary/20 shrink-0">
                    <span class="material-symbols-outlined fill">handshake</span>
                </div>
                <div class="min-w-0">
                    <h1 class="text-base font-black leading-tight text-slate-900 dark:text-white truncate">YouCode</h1>
                    <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest truncate">Job Dating</p>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 space-y-1 overflow-y-auto no-scrollbar pr-2 -mr-2">
                ${navLinksHtml}
            </nav>

            <!-- Bottom Actions -->
            <div class="pt-6 border-t border-slate-100 dark:border-slate-800 space-y-2 mt-auto shrink-0">
            <form method="post" action="/logout" class="w-full">
    <input type="hidden" name="csrf_token" value="{{ csrf_token }}">

    <button type="submit"
        class="flex items-center gap-3 px-4 py-3 rounded-xl w-full
               text-red-500 hover:bg-red-50 transition-all font-semibold text-sm">
        <span class="material-symbols-outlined">logout</span>
        <span>Logout</span>
    </button>
</form>
    
            <!-- Theme Toggle -->
                <button id="theme-toggle" class="flex items-center gap-3 px-4 py-3 rounded-xl w-full text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all font-semibold text-sm group">
                    <span class="material-symbols-outlined group-hover:scale-110 transition-transform" id="theme-icon">dark_mode</span>
                    <span id="theme-text">Dark Mode</span>
                </button>
                
                <!-- Profile Snippet -->
                <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-2xl mt-4 border border-slate-100 dark:border-slate-800 overflow-hidden">
                    <div class="size-9 rounded-full bg-slate-200 dark:bg-slate-700 bg-cover bg-center shrink-0" style="background-image: url('https://picsum.photos/seed/admin/100/100')"></div>
                    <div class="flex flex-col min-w-0">
                        <span class="text-xs font-bold truncate text-slate-900 dark:text-white">Admin User</span>
                        <span class="text-[9px] text-slate-400 font-bold uppercase truncate">Super Admin</span>
                    </div>
                </div>
            </div>
        </div>
    </aside>
    `;

    // After rendering, ensure the correct theme icons are shown
    updateThemeUI();
}

// 5. App Initialization
function bootstrap() {
    initTheme();
    renderSidebar();
}
// 
// Handle multiple ready states to ensure injection
if (document.readyState === 'complete') {
    bootstrap();
} else {
    window.addEventListener('load', bootstrap);
    document.addEventListener('DOMContentLoaded', bootstrap);
}

// 6. Global Event Listeners
document.addEventListener('click', (e) => {
    // Theme toggle listener
    if (e.target.closest('#theme-toggle')) {
        e.preventDefault();
        toggleTheme();
    }
});