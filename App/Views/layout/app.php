<!DOCTYPE html>
<html lang="en" class="scroll-smooth" x-data x-bind:class="$store.theme.dark ? 'dark' : ''">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Store') ?> — Scooter Dynamics</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        accent:       { DEFAULT: 'var(--color-accent)', hover: 'var(--color-accent-hover)' },
                        surface:      'var(--color-surface)',
                        muted:        'var(--color-muted)',
                        'brand-bg':   'var(--color-bg)',
                        'brand-text': 'var(--color-text)',
                        border:       'var(--color-border)',
                    },
                    fontFamily: {
                        sans: ['Inter', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'sans-serif'],
                    },
                    borderRadius: {
                        btn:   'var(--radius-button)',
                        input: 'var(--radius-input)',
                        card:  'var(--radius-card)',
                    },
                    screens: {
                        xs: '480px',
                    },
                },
            },
        }
    </script>
    <style>
        :root {
            --color-accent: #ff2d37;
            --color-accent-hover: #e0232c;
            --color-bg: #f8f9fb;
            --color-text: #1a1a2e;
            --color-surface: #ffffff;
            --color-muted: #5f6578;
            --color-border: #dfe2e8;
            --radius-button: 8px;
            --radius-input: 8px;
            --radius-card: 10px;
            --shadow-sm: 0 1px 2px rgba(0,0,0,.05);
            --shadow-md: 0 2px 8px rgba(0,0,0,.08);
            --shadow-lg: 0 4px 16px rgba(0,0,0,.1);
            --transition: .2s ease;
        }
        .dark {
            --color-accent: #ff4d55;
            --color-accent-hover: #ff6b72;
            --color-bg: #161616;
            --color-text: #ececec;
            --color-surface: #1e1e1e;
            --color-muted: #909090;
            --color-border: #2e2e2e;
            --shadow-sm: 0 1px 2px rgba(0,0,0,.2);
            --shadow-md: 0 2px 8px rgba(0,0,0,.3);
            --shadow-lg: 0 4px 16px rgba(0,0,0,.35);
        }
        body { font-family: 'Inter', sans-serif; background: var(--color-bg); color: var(--color-text); }
        * { transition: background-color .2s ease, border-color .2s ease, color .2s ease; }
        input:focus, select:focus, textarea:focus { outline: none; }
        .product-card:hover .product-img { transform: scale(1.05); }
        .product-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-lg); }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen flex flex-col antialiased">
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                dark: localStorage.getItem('theme') === 'dark',
                toggle() {
                    this.dark = !this.dark;
                    localStorage.setItem('theme', this.dark ? 'dark' : 'light');
                }
            });
            Alpine.store('cart', {
                items: JSON.parse(localStorage.getItem('cart') || '[]'),
                add(product) {
                    const existing = this.items.find(i => i.id === product.id);
                    if (existing) { existing.qty++; }
                    else { this.items.push({ ...product, qty: 1 }); }
                    this.persist();
                },
                remove(id) {
                    this.items = this.items.filter(i => i.id !== id);
                    this.persist();
                },
                update(id, qty) {
                    const item = this.items.find(i => i.id === id);
                    if (item) { item.qty = Math.max(1, qty); }
                    this.persist();
                },
                clear() { this.items = []; this.persist(); },
                get count() { return this.items.reduce((s, i) => s + i.qty, 0); },
                get total() { return this.items.reduce((s, i) => s + (i.sale_price || i.price) * i.qty, 0); },
                persist() { localStorage.setItem('cart', JSON.stringify(this.items)); }
            });
        });
    </script>

    <?php include __DIR__ . '/header.php'; ?>

    <main class="flex-1">
        <?= $content ?>
    </main>

    <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
