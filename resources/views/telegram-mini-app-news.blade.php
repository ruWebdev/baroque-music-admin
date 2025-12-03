<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новая новость — BaroqueMusic.ru</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-slate-950 text-slate-50 flex items-center justify-center">
    <div class="w-full max-w-lg px-6 py-8">
        <h1 class="text-2xl font-semibold mb-1 text-center">Новая новость</h1>
        <p class="text-xs text-slate-400 mb-6 text-center">Заполните поля и отправьте на модерацию</p>

        @if (session('status'))
        <div class="mb-4 rounded-xl bg-emerald-500/10 border border-emerald-500/40 px-4 py-3 text-xs text-emerald-200">
            {{ session('status') }}
        </div>
        @endif

        @if ($errors->any())
        <div class="mb-4 rounded-xl bg-red-500/10 border border-red-500/40 px-4 py-3 text-xs text-red-200">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ url('/telegram-mini-app/news') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div class="space-y-1">
                <label class="block text-xs font-medium text-slate-200">Заголовок</label>
                <input type="text" name="title" value="{{ old('title') }}"
                    class="w-full rounded-xl bg-slate-900/70 border border-slate-700 px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-medium text-slate-200">Текст новости</label>
                <textarea name="body" rows="5"
                    class="w-full rounded-xl bg-slate-900/70 border border-slate-700 px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">{{ old('body') }}</textarea>
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-medium text-slate-200">Изображение</label>
                <input type="file" name="image"
                    class="w-full text-xs text-slate-300 file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-500 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-slate-950 hover:file:bg-emerald-400">
                <p class="text-[10px] text-slate-500 mt-1">Необязательно. JPG/PNG/WebP, до 5 МБ.</p>
            </div>

            <div class="pt-2 flex gap-3">
                <a href="{{ url('/telegram-mini-app') }}"
                    class="flex-1 rounded-xl border border-slate-700 px-3 py-2 text-center text-xs font-medium text-slate-200 hover:bg-slate-800/70 transition-colors">
                    Назад
                </a>
                <button type="submit"
                    class="flex-1 rounded-xl bg-emerald-500 hover:bg-emerald-400 active:bg-emerald-600 text-slate-950 font-medium py-2 text-xs transition-colors">
                    Отправить новость
                </button>
            </div>
        </form>
    </div>
</body>

</html>