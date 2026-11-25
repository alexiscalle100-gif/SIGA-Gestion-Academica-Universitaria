<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>403 — Acceso Restringido</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-100 flex items-center justify-center p-6">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-lg border border-slate-200 p-8 text-center">
        <div class="w-14 h-14 mx-auto rounded-full bg-amber-100 text-amber-700 flex items-center justify-center text-2xl font-bold">403</div>
        <h1 class="mt-4 text-xl font-bold text-[#0F2744]">Acceso Restringido</h1>
        <p class="mt-2 text-sm text-slate-600">{{ $exception->getMessage() ?: 'Tu rol actual no cuenta con permisos para acceder a este recurso.' }}</p>
        <a href="{{ route('dashboard') }}" class="mt-6 inline-block px-5 py-2.5 rounded-lg bg-[#0F2744] text-white text-sm font-semibold hover:bg-[#1E3A5F]">
            Volver al Dashboard
        </a>
    </div>
</body>
</html>
