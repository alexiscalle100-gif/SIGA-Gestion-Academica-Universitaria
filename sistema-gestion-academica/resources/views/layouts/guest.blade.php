<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Acceso') — SIGA UPDS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 flex flex-col items-center justify-center p-6">
    <div class="w-full max-w-md">
        <div class="flex items-center justify-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-[#0F2744] text-white flex items-center justify-center font-bold text-lg shadow-sm">
                S
            </div>
            <div class="text-left">
                <div class="text-lg font-bold text-[#0F2744] leading-tight">SIGA Académico</div>
                <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Universidad Privada Domingo Savio</div>
            </div>
        </div>

        @yield('content')
    </div>
</body>
</html>
