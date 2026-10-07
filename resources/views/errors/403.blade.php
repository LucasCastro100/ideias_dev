<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acesso Negado</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-950 text-gray-100 font-sans antialiased min-h-screen flex items-center justify-center p-4">
    <div class="text-center max-w-md">
        <div class="text-8xl font-bold mb-4" style="font-family: 'Fredoka', sans-serif; background: linear-gradient(135deg, #ef4444, #f472b6); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
            403
        </div>
        <h1 class="text-2xl font-semibold mb-2">Acesso Negado</h1>
        <p class="text-gray-400 mb-8">Você não tem permissão para acessar esta página.</p>
        <a href="{{ auth()->check() ? route('dashboard') : url('/') }}" class="inline-block px-6 py-3 bg-gray-800 hover:bg-gray-700 text-gray-200 rounded-lg transition font-medium">
            {{ auth()->check() ? 'Ir para o Dashboard' : 'Voltar para o Início' }}
        </a>
    </div>
</body>
</html>
