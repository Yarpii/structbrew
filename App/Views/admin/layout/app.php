<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Admin') ?> — StructBrew Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        admin: {
                            bg: '#f1f5f9',
                            sidebar: '#0f172a',
                            'sidebar-hover': '#1e293b',
                            'sidebar-active': '#334155',
                            accent: '#3b82f6',
                            'accent-hover': '#2563eb',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full bg-admin-bg" x-data="{ sidebarOpen: true, mobileMenu: false }">
    <div class="flex h-full">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-30 flex flex-col bg-admin-sidebar text-white transition-all duration-300"
               :class="sidebarOpen ? 'w-64' : 'w-20'">
            <!-- Logo -->
            <div class="flex items-center h-16 px-4 border-b border-white/10">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-admin-accent rounded-lg flex items-center justify-center font-bold text-sm">SB</div>
                    <span x-show="sidebarOpen" x-cloak class="font-semibold text-lg">StructBrew</span>
                </div>
            </div>

            <!-- Store Scope Selector -->
            <?php if (!empty($storeViews)): ?>
            <div class="px-3 py-3 border-b border-white/10" x-show="sidebarOpen" x-cloak>
                <select name="store_scope" onchange="window.location.href='?store_view='+this.value"
                        class="w-full bg-admin-sidebar-hover text-white text-sm rounded-lg px-3 py-2 border border-white/20 focus:outline-none focus:border-admin-accent">
                    <option value="0">All Store Views</option>
                    <?php foreach ($storeViews as $sv): ?>
                    <option value="<?= (int) $sv['id'] ?>" <?= ($currentStoreView ?? 0) == $sv['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($sv['name']) ?> (<?= htmlspecialchars($sv['locale'] ?? '') ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
                <?php
                $menuItems = [
                    ['label' => 'Dashboard', 'url' => '/admin', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'section' => ''],
                    ['label' => 'CATALOG', 'section' => 'header'],
                    ['label' => 'Products', 'url' => '/admin/products', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'section' => 'catalog'],
                    ['label' => 'Categories', 'url' => '/admin/categories', 'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', 'section' => 'catalog'],
                    ['label' => 'Attributes', 'url' => '/admin/attributes', 'icon' => 'M7 7h10M7 12h10M7 17h10M4 7h.01M4 12h.01M4 17h.01', 'section' => 'catalog'],
                    ['label' => 'Brands', 'url' => '/admin/brands', 'icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z', 'section' => 'catalog'],
                    ['label' => 'Vehicles', 'url' => '/admin/vehicles', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'section' => 'catalog'],
                    ['label' => 'SALES', 'section' => 'header'],
                    ['label' => 'Orders', 'url' => '/admin/orders', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01', 'section' => 'sales'],
                    ['label' => 'Customers', 'url' => '/admin/customers', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'section' => 'sales'],
                    ['label' => 'MARKETING', 'section' => 'header'],
                    ['label' => 'Price Rules', 'url' => '/admin/marketing/price-rules', 'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z', 'section' => 'marketing'],
                    ['label' => 'Coupons', 'url' => '/admin/marketing/coupons', 'icon' => 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z', 'section' => 'marketing'],
                    ['label' => 'Ads', 'url' => '/admin/marketing/ads', 'icon' => 'M11 5h2m-1-1v2m-7 5h14m-7 0v8m-5 0h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z', 'section' => 'marketing'],
                    ['label' => 'CONTENT', 'section' => 'header'],
                    ['label' => 'CMS Pages', 'url' => '/admin/content/pages', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'section' => 'content'],
                    ['label' => 'Translations', 'url' => '/admin/content/translations', 'icon' => 'M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129', 'section' => 'content'],
                    ['label' => 'STORES', 'section' => 'header'],
                    ['label' => 'Websites', 'url' => '/admin/stores/websites', 'icon' => 'M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 009-9', 'section' => 'stores'],
                    ['label' => 'Store Views', 'url' => '/admin/stores/views', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'section' => 'stores'],
                    ['label' => 'Domains', 'url' => '/admin/stores/domains', 'icon' => 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1', 'section' => 'stores'],
                    ['label' => 'SYSTEM', 'section' => 'header'],
                    ['label' => 'Configuration', 'url' => '/admin/config', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z', 'section' => 'system'],
                    ['label' => 'Admin Users', 'url' => '/admin/system/users', 'icon' => 'M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'section' => 'system'],
                    ['label' => 'Activity Log', 'url' => '/admin/system/activity', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'section' => 'system'],
                    ['label' => 'SUPPORT', 'section' => 'header'],
                    ['label' => 'Tickets', 'url' => '/admin/tickets', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'section' => 'support'],
                    ['label' => 'Departments', 'url' => '/admin/tickets/departments', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'section' => 'support'],
                    ['label' => 'Mailboxes', 'url' => '/admin/tickets/mailboxes', 'icon' => 'M3 8l7.89 4.26a2 2 0 002.22 0L21 8m-18 8h18a2 2 0 002-2V8a2 2 0 00-2-2H3a2 2 0 00-2 2v6a2 2 0 002 2z', 'section' => 'support'],
                    ['label' => 'SLA Policies', 'url' => '/admin/tickets/sla', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'section' => 'support'],
                    ['label' => 'Canned Responses', 'url' => '/admin/tickets/canned', 'icon' => 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z', 'section' => 'support'],
                ];
                $currentPath = $_SERVER['REQUEST_URI'] ?? '/admin';
                $currentPath = strtok($currentPath, '?');
                foreach ($menuItems as $item):
                    if ($item['section'] === 'header'):
                ?>
                    <div class="pt-4 pb-1 px-3" x-show="sidebarOpen" x-cloak>
                        <span class="text-xs font-semibold text-slate-400 tracking-wider"><?= htmlspecialchars((string) $item['label']) ?></span>
                    </div>
                <?php else:
                    $isActive = ($currentPath === $item['url']) || ($item['url'] !== '/admin' && str_starts_with($currentPath, $item['url']));
                ?>
                    <a href="<?= htmlspecialchars($item['url']) ?>"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors <?= $isActive ? 'bg-admin-accent text-white' : 'text-slate-300 hover:bg-admin-sidebar-hover hover:text-white' ?>">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="<?= htmlspecialchars($item['icon']) ?>"/>
                        </svg>
                        <span x-show="sidebarOpen" x-cloak><?= htmlspecialchars($item['label']) ?></span>
                    </a>
                <?php endif; endforeach; ?>
            </nav>

            <!-- Sidebar Toggle -->
            <div class="p-3 border-t border-white/10">
                <button @click="sidebarOpen = !sidebarOpen"
                        class="flex items-center gap-3 w-full px-3 py-2 text-sm text-slate-400 hover:text-white rounded-lg hover:bg-admin-sidebar-hover transition-colors">
                    <svg class="w-5 h-5 flex-shrink-0 transition-transform" :class="!sidebarOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                    </svg>
                    <span x-show="sidebarOpen" x-cloak>Collapse</span>
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 transition-all duration-300" :class="sidebarOpen ? 'ml-64' : 'ml-20'">
            <!-- Top Bar -->
            <header class="sticky top-0 z-20 bg-white border-b border-gray-200 h-16 flex items-center justify-between px-6">
                <div class="flex items-center gap-4">
                    <h1 class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></h1>
                </div>
                <div class="flex items-center gap-4">
                    <a href="/" target="_blank" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        View Store
                    </a>
                    <div class="h-6 w-px bg-gray-200"></div>
                    <div class="flex items-center gap-2" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 text-sm text-gray-700 hover:text-gray-900">
                            <div class="w-8 h-8 bg-admin-accent rounded-full flex items-center justify-center text-white text-xs font-semibold">
                                <?= htmlspecialchars(strtoupper(substr((string) ($adminUser['first_name'] ?? 'A'), 0, 1) . substr((string) ($adminUser['last_name'] ?? 'D'), 0, 1))) ?>
                            </div>
                            <span><?= htmlspecialchars(($adminUser['first_name'] ?? 'Admin') . ' ' . ($adminUser['last_name'] ?? '')) ?></span>
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak
                             class="absolute right-6 top-14 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1">
                            <a href="/admin/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Profile</a>
                            <form method="POST" action="/admin/logout">
                                <input type="hidden" name="_csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
            <div class="mx-6 mt-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-center gap-2" x-data="{ show: true }" x-show="show">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span class="text-sm"><?= htmlspecialchars($flash) ?></span>
                <button @click="show = false" class="ml-auto text-green-500 hover:text-green-700"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg></button>
            </div>
            <?php endif; ?>
            <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
            <div class="mx-6 mt-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg flex items-center gap-2" x-data="{ show: true }" x-show="show">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                <span class="text-sm"><?= htmlspecialchars($flash) ?></span>
                <button @click="show = false" class="ml-auto text-red-500 hover:text-red-700"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg></button>
            </div>
            <?php endif; ?>

            <!-- Page Content -->
            <div class="p-6">
                <?= $content ?>
            </div>
        </div>
    </div>
</body>
</html>
