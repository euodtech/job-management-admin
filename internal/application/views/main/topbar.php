<!-- Topbar -->
<header id="topbar" class="fixed top-0 right-0 left-0 z-50 flex items-center justify-between bg-primary px-4 h-16 lg:ml-64 transition-all duration-300">
    <!-- Left: Hamburger -->
    <div class="flex items-center">
        <button type="button"
                class="inline-flex items-center justify-center w-10 h-10 rounded-lg text-white hover:bg-white/10 transition-colors"
                onclick="toggleSidebar()"
                aria-label="Toggle sidebar">
            <i class="fa fa-bars text-lg"></i>
        </button>
    </div>

    <!-- Right: Sign Out -->
    <div>
        <a href="<?php echo base_url('auth/logout'); ?>"
           class="swt flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-bold text-white hover:bg-white/10 transition-colors">
            <i class="fa-solid fa-arrow-right-from-bracket"></i>
            <span class="whitespace-nowrap">Sign Out</span>
        </a>
    </div>
</header>
