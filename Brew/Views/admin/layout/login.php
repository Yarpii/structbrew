<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — StructBrew</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="h-full bg-slate-100 flex items-center justify-center">
    <div class="w-full max-w-md p-8">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-8">
                <div class="w-14 h-14 bg-blue-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <span class="text-white font-bold text-xl">SB</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">StructBrew Admin</h1>
                <p class="text-gray-500 mt-1">Sign in to your admin panel</p>
            </div>

            <?php if ($error ?? false): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="/admin/login" class="space-y-5">
                <input type="hidden" name="_token" value="<?= \Brew\Core\Session::csrfToken() ?>">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                    <input type="email" name="email" required autofocus
                           value="<?= htmlspecialchars($email ?? '') ?>"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                           placeholder="admin@structbrew.com">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                           placeholder="Enter your password">
                </div>

                <button type="submit"
                        class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-sm transition-colors">
                    Sign In
                </button>
            </form>
        </div>
    </div>
</body>
</html>
