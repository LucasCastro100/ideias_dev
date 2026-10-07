<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Página não encontrada</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-950 text-gray-100 font-sans antialiased min-h-screen flex items-center justify-center p-4">
    <div class="text-center max-w-md">
        <div class="text-8xl font-bold mb-4" style="font-family: 'Fredoka', sans-serif; background: linear-gradient(135deg, #f59e0b, #f97316); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
            404
        </div>
        <h1 class="text-2xl font-semibold mb-2">Página não encontrada</h1>
        <p class="text-gray-400 mb-8">A página que você procura não existe ou foi removida.</p>
        <a href="{{ auth()->check() ? route('dashboard') : url('/') }}" class="inline-block px-6 py-3 bg-gray-800 hover:bg-gray-700 text-gray-200 rounded-lg transition font-medium">
            {{ auth()->check() ? 'Ir para o Dashboard' : 'Voltar para o Início' }}
        </a>
    </div>
</body>
</html>
