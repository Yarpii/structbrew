<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StructBrew - Setup Wizard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        accent: '#ff2d37',
                        'accent-hover': '#e0232c',
                    },
                    fontFamily: {
                        sans: ['Inter', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'sans-serif'],
                    },
                },
            },
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <!-- Header -->
    <header class="bg-white border-b border-gray-200 py-4">
        <div class="max-w-3xl mx-auto px-6 flex items-center gap-3">
            <div class="w-9 h-9 bg-accent rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-lg font-bold text-gray-900">StructBrew</h1>
                <p class="text-xs text-gray-500">Setup Wizard</p>
            </div>
        </div>
    </header>

    <!-- Progress Steps -->
    <?php if (isset($step) && $step <= 5): ?>
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-3xl mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <?php
                $steps = [
                    1 => 'Requirements',
                    2 => 'Database',
                    3 => 'Application',
                    4 => 'Admin Account',
                    5 => 'Complete',
                ];
                $i = 0;
                foreach ($steps as $num => $label):
                    $i++;
                    $isActive = $num === $step;
                    $isDone = $num < $step;
                ?>
                    <?php if ($i > 1): ?>
                        <div class="flex-1 h-0.5 mx-2 <?= $isDone ? 'bg-accent' : 'bg-gray-200' ?>"></div>
                    <?php endif; ?>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold
                            <?php if ($isDone): ?>
                                bg-accent text-white
                            <?php elseif ($isActive): ?>
                                bg-accent text-white ring-4 ring-red-100
                            <?php else: ?>
                                bg-gray-200 text-gray-500
                            <?php endif; ?>
                        ">
                            <?php if ($isDone): ?>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            <?php else: ?>
                                <?= $num ?>
                            <?php endif; ?>
                        </div>
                        <span class="text-sm font-medium hidden sm:inline <?= $isActive ? 'text-gray-900' : 'text-gray-500' ?>"><?= $label ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Content -->
    <main class="flex-1 py-8">
        <div class="max-w-3xl mx-auto px-6">
            <?= $content ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="py-4 text-center text-xs text-gray-400 border-t border-gray-100">
        StructBrew Setup Wizard
    </footer>
</body>
</html>
