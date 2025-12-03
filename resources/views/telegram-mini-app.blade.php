<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BaroqueMusic.ru</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-slate-950 text-slate-50 flex items-center justify-center">
    <div class="w-full max-w-md px-6 py-10 text-center">
        <h1 class="text-3xl font-semibold tracking-tight mb-2">BaroqueMusic.ru</h1>
        <p class="text-sm text-slate-400 mb-8">Добро пожаловать!<br />Выберите, что вы хотите добавить.</p>

        <div class="space-y-3">
            <a href="{{ url('/telegram-mini-app/news') }}"
                class="block w-full rounded-xl bg-emerald-500 hover:bg-emerald-400 active:bg-emerald-600 text-slate-950 font-medium py-3 transition-colors text-sm shadow-sm">
                Новость
            </a>
            <a href="{{ url('/telegram-mini-app/event') }}"
                class="block w-full rounded-xl bg-slate-800 hover:bg-slate-700 active:bg-slate-900 text-slate-50 font-medium py-3 transition-colors text-sm border border-slate-700">
                Событие
            </a>
        </div>
    </div>
</body>

</html>