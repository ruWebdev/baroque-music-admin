<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новое событие — BaroqueMusic.ru</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-slate-950 text-slate-50 flex items-center justify-center">
    <div class="w-full max-w-lg px-6 py-8">
        <h1 class="text-2xl font-semibold mb-1 text-center">Новое событие</h1>
        <p class="text-xs text-slate-400 mb-6 text-center">Заполните шаги мастера и отправьте событие на модерацию</p>

        @if (session('status'))
        <div class="mb-4 rounded-xl bg-emerald-500/10 border border-emerald-500/40 px-4 py-3 text-xs text-emerald-200">
            {{ session('status') }}
        </div>
        <script>
            setTimeout(function() {
                window.location.href = "{{ url('/telegram-mini-app') }}";
            }, 5000);
        </script>
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

        <form action="{{ url('/telegram-mini-app/event') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2 text-[10px] text-slate-400">
                    <span class="inline-flex w-6 h-6 items-center justify-center rounded-full bg-emerald-500 text-slate-950 font-semibold" data-step-dot="1">1</span>
                    <span class="inline-flex w-6 h-6 items-center justify-center rounded-full bg-slate-700 text-slate-300" data-step-dot="2">2</span>
                    <span class="inline-flex w-6 h-6 items-center justify-center rounded-full bg-slate-700 text-slate-300" data-step-dot="3">3</span>
                    <span class="inline-flex w-6 h-6 items-center justify-center rounded-full bg-slate-700 text-slate-300" data-step-dot="4">4</span>
                    <span class="inline-flex w-6 h-6 items-center justify-center rounded-full bg-slate-700 text-slate-300" data-step-dot="5">5</span>
                </div>
                <div id="step-label" class="text-[10px] text-slate-400">Шаг 1 из 5</div>
            </div>

            <div class="space-y-4" data-step="1">
                <h2 class="text-sm font-semibold text-slate-100 mb-1">Основная информация</h2>
                <p class="text-[11px] text-slate-400 mb-2">Укажите базовые параметры события.</p>

                <div class="space-y-1">
                    <label class="block text-xs font-medium text-slate-200">Тип события</label>
                    <select name="event_type"
                        class="w-full rounded-xl bg-slate-900/70 border border-slate-700 px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        <option value="1" {{ old('event_type') == 1 ? 'selected' : '' }}>Концерт / Театр / Представление</option>
                        <option value="2" {{ old('event_type') == 2 ? 'selected' : '' }}>Мастер-класс / Лекция / Экскурсия</option>
                        <option value="3" {{ old('event_type') == 3 ? 'selected' : '' }}>Другое</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-medium text-slate-200">Название события</label>
                    <input type="text" name="title" value="{{ old('title') }}"
                        class="w-full rounded-xl bg-slate-900/70 border border-slate-700 px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-medium text-slate-200">Город</label>
                    <input type="text" name="city" value="{{ old('city') }}"
                        class="w-full rounded-xl bg-slate-900/70 border border-slate-700 px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-medium text-slate-200">Место проведения</label>
                    <input type="text" name="place" value="{{ old('place') }}"
                        class="w-full rounded-xl bg-slate-900/70 border border-slate-700 px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="block text-xs font-medium text-slate-200">Дата начала</label>
                        <input type="date" name="event_date" value="{{ old('event_date') }}"
                            class="w-full rounded-xl bg-slate-900/70 border border-slate-700 px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-medium text-slate-200">Время начала</label>
                        <input type="time" name="event_time" value="{{ old('event_time') }}"
                            class="w-full rounded-xl bg-slate-900/70 border border-slate-700 px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    </div>
                </div>

                <div class="pt-2 flex gap-3">
                    <a href="{{ url('/telegram-mini-app') }}"
                        class="flex-1 rounded-xl border border-slate-700 px-3 py-2 text-center text-xs font-medium text-slate-200 hover:bg-slate-800/70 transition-colors">
                        Назад
                    </a>
                    <button type="button" data-next-step="2"
                        class="flex-1 rounded-xl bg-emerald-500 hover:bg-emerald-400 active:bg-emerald-600 text-slate-950 font-medium py-2 text-xs transition-colors">
                        Далее
                    </button>
                </div>
            </div>

            <div class="space-y-4 hidden" data-step="2">
                <h2 class="text-sm font-semibold text-slate-100 mb-1">Описание и билеты</h2>
                <p class="text-[11px] text-slate-400 mb-2">Расскажите подробнее о событии и укажите стоимость билетов.</p>

                <div class="space-y-1">
                    <label class="block text-xs font-medium text-slate-200">Подробное описание</label>
                    <textarea name="description" rows="4"
                        class="w-full rounded-xl bg-slate-900/70 border border-slate-700 px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="block text-xs font-medium text-slate-200">Стоимость билетов ОТ</label>
                        <input type="text" name="ticket_price_from" value="{{ old('ticket_price_from') }}"
                            class="w-full rounded-xl bg-slate-900/70 border border-slate-700 px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-medium text-slate-200">Стоимость билетов ДО</label>
                        <input type="text" name="ticket_price_to" value="{{ old('ticket_price_to') }}"
                            class="w-full rounded-xl bg-slate-900/70 border border-slate-700 px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-medium text-slate-200">Возрастное ограничение</label>
                    <select name="age_restrictions"
                        class="w-full rounded-xl bg-slate-900/70 border border-slate-700 px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        <option value="0+" {{ old('age_restrictions', '0+') == '0+' ? 'selected' : '' }}>0+</option>
                        <option value="6+" {{ old('age_restrictions') == '6+' ? 'selected' : '' }}>6+</option>
                        <option value="12+" {{ old('age_restrictions') == '12+' ? 'selected' : '' }}>12+</option>
                        <option value="16+" {{ old('age_restrictions') == '16+' ? 'selected' : '' }}>16+</option>
                        <option value="18+" {{ old('age_restrictions') == '18+' ? 'selected' : '' }}>18+</option>
                    </select>
                </div>

                <div class="pt-2 flex gap-3">
                    <button type="button" data-prev-step="1"
                        class="flex-1 rounded-xl border border-slate-700 px-3 py-2 text-center text-xs font-medium text-slate-200 hover:bg-slate-800/70 transition-colors">
                        Назад
                    </button>
                    <button type="button" data-next-step="3"
                        class="flex-1 rounded-xl bg-emerald-500 hover:bg-emerald-400 active:bg-emerald-600 text-slate-950 font-medium py-2 text-xs transition-colors">
                        Далее
                    </button>
                </div>
            </div>

            <div class="space-y-4 hidden" data-step="3">
                <h2 class="text-sm font-semibold text-slate-100 mb-1">Исполнители и программа</h2>
                <p class="text-[11px] text-slate-400 mb-2">Найдите исполнителей и композиторов по базе или введите данные вручную.</p>

                <div class="space-y-2">
                    <label class="block text-xs font-medium text-slate-200">Исполнители</label>
                    <input type="text" id="performers-search"
                        class="w-full rounded-xl bg-slate-900/70 border border-slate-700 px-3 py-2 text-xs outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                        placeholder="Начните вводить фамилию исполнителя или название коллектива">
                    <div id="performers-suggestions" class="space-y-1 text-[11px]"></div>
                    <textarea name="performers" id="performers-textarea" rows="3"
                        class="w-full rounded-xl bg-slate-900/70 border border-slate-700 px-3 py-2 text-xs outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" placeholder="Например: ансамбль, солисты и т.п.">{{ old('performers') }}</textarea>
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-medium text-slate-200">Программа</label>
                    <input type="text" id="program-composer-search"
                        class="w-full rounded-xl bg-slate-900/70 border border-slate-700 px-3 py-2 text-xs outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                        placeholder="Поиск композитора">
                    <div id="program-composer-suggestions" class="space-y-1 text-[11px]"></div>
                    <input type="text" id="program-title-input"
                        class="w-full rounded-xl bg-slate-900/70 border border-slate-700 px-3 py-2 text-xs outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                        placeholder="Название произведения">
                    <button type="button" id="program-add-btn"
                        class="w-full rounded-xl bg-slate-800 hover:bg-slate-700 text-[11px] text-slate-100 font-medium py-2 transition-colors">
                        Добавить в программу
                    </button>
                    <textarea name="program" id="program-textarea" rows="4"
                        class="w-full rounded-xl bg-slate-900/70 border border-slate-700 px-3 py-2 text-xs outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" placeholder="Кратко опишите программу концерта или события или используйте добавление из поиска.">{{ old('program') }}</textarea>
                </div>

                <div class="pt-2 flex gap-3">
                    <button type="button" data-prev-step="2"
                        class="flex-1 rounded-xl border border-slate-700 px-3 py-2 text-center text-xs font-medium text-slate-200 hover:bg-slate-800/70 transition-colors">
                        Назад
                    </button>
                    <button type="button" data-next-step="4"
                        class="flex-1 rounded-xl bg-emerald-500 hover:bg-emerald-400 active:bg-emerald-600 text-slate-950 font-medium py-2 text-xs transition-colors">
                        Далее
                    </button>
                </div>
            </div>

            <div class="space-y-4 hidden" data-step="4">
                <h2 class="text-sm font-semibold text-slate-100 mb-1">Изображение</h2>
                <p class="text-[11px] text-slate-400 mb-2">Добавьте одно изображение афиши или события. Его можно будет заменить.</p>

                <div class="space-y-1">
                    <label class="block text-xs font-medium text-slate-200">Изображение</label>
                    <input type="file" name="image" id="image-input" accept="image/*"
                        class="w-full text-xs text-slate-300 file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-500 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-slate-950 hover:file:bg-emerald-400">
                    <p class="text-[10px] text-slate-500 mt-1">JPG/PNG/WebP, до 5 МБ. При отсутствии изображения будет использована стандартная картинка.</p>
                </div>

                <div id="image-preview-wrapper" class="mt-3 hidden">
                    <p class="text-[11px] text-slate-400 mb-2">Предпросмотр изображения</p>
                    <img id="image-preview" src="#" alt="Предпросмотр" class="w-full h-56 object-cover rounded-xl border border-slate-700 bg-slate-900/60">
                    <button type="button" id="image-remove-btn" class="mt-2 text-[11px] text-red-300 underline">Удалить изображение</button>
                </div>

                <div class="pt-2 flex gap-3">
                    <button type="button" data-prev-step="3"
                        class="flex-1 rounded-xl border border-slate-700 px-3 py-2 text-center text-xs font-medium text-slate-200 hover:bg-slate-800/70 transition-colors">
                        Назад
                    </button>
                    <button type="button" data-next-step="5"
                        class="flex-1 rounded-xl bg-emerald-500 hover:bg-emerald-400 active:bg-emerald-600 text-slate-950 font-medium py-2 text-xs transition-colors">
                        Далее
                    </button>
                </div>
            </div>

            <div class="space-y-4 hidden" data-step="5">
                <h2 class="text-sm font-semibold text-slate-100 mb-1">Подтверждение</h2>
                <p class="text-[11px] text-slate-400 mb-3">Проверьте данные перед отправкой. При необходимости вернитесь на предыдущие шаги.</p>

                <div class="space-y-2 text-[11px] text-slate-300">
                    <div>
                        <div class="font-semibold text-slate-200">Основная информация</div>
                        <div id="summary-main" class="mt-1 whitespace-pre-line"></div>
                    </div>
                    <div>
                        <div class="font-semibold text-slate-200">Описание и билеты</div>
                        <div id="summary-details" class="mt-1 whitespace-pre-line"></div>
                    </div>
                    <div>
                        <div class="font-semibold text-slate-200">Исполнители и программа</div>
                        <div id="summary-performers" class="mt-1 whitespace-pre-line"></div>
                    </div>
                </div>

                <div class="pt-3 flex gap-3">
                    <button type="submit"
                        class="rounded-xl bg-emerald-500 hover:bg-emerald-400 active:bg-emerald-600 text-slate-950 font-medium py-2 text-xs transition-colors flex-1">
                        Отправить событие
                    </button>
                </div>

                <div class="pt-3 flex gap-3">
                    <a href="{{ url('/telegram-mini-app') }}"
                        class="rounded-xl border border-slate-700 px-3 py-2 text-center text-xs font-medium text-slate-200 hover:bg-slate-800/70 transition-colors flex-1">
                        Отменить
                    </a>
                    <button type="button" data-prev-step="4"
                        class="rounded-xl border border-slate-700 px-3 py-2 text-center text-xs font-medium text-slate-200 hover:bg-slate-800/70 transition-colors flex-1">
                        Назад
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var currentStep = 1;
            var steps = Array.prototype.slice.call(document.querySelectorAll('[data-step]'));
            var dots = Array.prototype.slice.call(document.querySelectorAll('[data-step-dot]'));
            var stepLabel = document.getElementById('step-label');

            function updateStepLabel() {
                if (stepLabel) {
                    stepLabel.textContent = 'Шаг ' + currentStep + ' из 5';
                }
            }

            function showStep(step) {
                currentStep = step;
                steps.forEach(function(el) {
                    var s = parseInt(el.getAttribute('data-step'));
                    if (s === step) {
                        el.classList.remove('hidden');
                    } else {
                        el.classList.add('hidden');
                    }
                });
                dots.forEach(function(dot) {
                    var s = parseInt(dot.getAttribute('data-step-dot'));
                    if (s === step) {
                        dot.classList.remove('bg-slate-700');
                        dot.classList.add('bg-emerald-500', 'text-slate-950');
                    } else {
                        dot.classList.add('bg-slate-700');
                        dot.classList.remove('bg-emerald-500', 'text-slate-950');
                    }
                });
                updateStepLabel();
                if (step === 5) {
                    updateSummary();
                }
            }

            document.querySelectorAll('[data-next-step]').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    var target = parseInt(btn.getAttribute('data-next-step'));
                    if (!isNaN(target)) {
                        showStep(target);
                    }
                });
            });

            document.querySelectorAll('[data-prev-step]').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    var target = parseInt(btn.getAttribute('data-prev-step'));
                    if (!isNaN(target)) {
                        showStep(target);
                    }
                });
            });

            function updateSummary() {
                var main = [];
                var details = [];
                var perf = [];

                var typeEl = document.querySelector('select[name="event_type"]');
                var titleEl = document.querySelector('input[name="title"]');
                var cityEl = document.querySelector('input[name="city"]');
                var placeEl = document.querySelector('input[name="place"]');
                var dateEl = document.querySelector('input[name="event_date"]');
                var timeEl = document.querySelector('input[name="event_time"]');

                if (typeEl && typeEl.options[typeEl.selectedIndex]) {
                    main.push('Тип: ' + typeEl.options[typeEl.selectedIndex].textContent.trim());
                }
                if (titleEl && titleEl.value) {
                    main.push('Название: ' + titleEl.value);
                }
                if (cityEl && cityEl.value) {
                    main.push('Город: ' + cityEl.value);
                }
                if (placeEl && placeEl.value) {
                    main.push('Место проведения: ' + placeEl.value);
                }
                if (dateEl && dateEl.value) {
                    main.push('Дата: ' + dateEl.value);
                }
                if (timeEl && timeEl.value) {
                    main.push('Время: ' + timeEl.value);
                }

                var descEl = document.querySelector('textarea[name="description"]');
                var priceFromEl = document.querySelector('input[name="ticket_price_from"]');
                var priceToEl = document.querySelector('input[name="ticket_price_to"]');
                var ageEl = document.querySelector('select[name="age_restrictions"]');

                if (descEl && descEl.value) {
                    details.push('Описание: ' + descEl.value);
                }
                if (priceFromEl && priceFromEl.value) {
                    var line = 'Стоимость билетов: от ' + priceFromEl.value;
                    if (priceToEl && priceToEl.value) {
                        line += ' до ' + priceToEl.value;
                    }
                    details.push(line);
                }
                if (ageEl && ageEl.value) {
                    details.push('Возрастное ограничение: ' + ageEl.value);
                }

                var perfText = document.getElementById('performers-textarea');
                var progText = document.getElementById('program-textarea');

                if (perfText && perfText.value) {
                    perf.push('Исполнители:\n' + perfText.value);
                }
                if (progText && progText.value) {
                    perf.push('Программа:\n' + progText.value);
                }

                var mainBox = document.getElementById('summary-main');
                var detailsBox = document.getElementById('summary-details');
                var perfBox = document.getElementById('summary-performers');

                if (mainBox) {
                    mainBox.textContent = main.join('\n');
                }
                if (detailsBox) {
                    detailsBox.textContent = details.join('\n');
                }
                if (perfBox) {
                    perfBox.textContent = perf.join('\n\n');
                }
            }

            // Поиск исполнителей (артисты и коллективы)
            var performersSearchInput = document.getElementById('performers-search');
            var performersSuggestions = document.getElementById('performers-suggestions');
            var performersTextarea = document.getElementById('performers-textarea');
            var performersTimer = null;

            function clearPerformersSuggestions() {
                if (performersSuggestions) {
                    performersSuggestions.innerHTML = '';
                }
            }

            function renderPerformerSuggestions(items) {
                if (!performersSuggestions) {
                    return;
                }
                performersSuggestions.innerHTML = '';
                items.forEach(function(item) {
                    var btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'block w-full text-left px-2 py-1 rounded-lg bg-slate-900/60 hover:bg-slate-800 text-[11px] text-slate-100';
                    btn.textContent = item.label;
                    btn.addEventListener('click', function() {
                        if (!performersTextarea) {
                            return;
                        }
                        var current = performersTextarea.value.trim();
                        performersTextarea.value = current ? (current + '\n' + item.label) : item.label;
                    });
                    performersSuggestions.appendChild(btn);
                });
            }

            if (performersSearchInput) {
                performersSearchInput.addEventListener('input', function() {
                    var q = performersSearchInput.value.trim();
                    if (performersTimer) {
                        clearTimeout(performersTimer);
                    }
                    if (q.length < 2) {
                        clearPerformersSuggestions();
                        return;
                    }
                    performersTimer = setTimeout(function() {
                        Promise.all([
                            fetch('/telegram-mini-app/search-artists?q=' + encodeURIComponent(q)),
                            fetch('/telegram-mini-app/search-bands?q=' + encodeURIComponent(q))
                        ]).then(function(responses) {
                            return Promise.all(responses.map(function(r) {
                                if (!r.ok) {
                                    return [];
                                }
                                return r.json();
                            }));
                        }).then(function(results) {
                            var artists = results[0] || [];
                            var bands = results[1] || [];
                            var items = [];
                            artists.forEach(function(a) {
                                items.push({
                                    label: (a.last_name || '') + ', ' + (a.first_name || ''),
                                    type: 'artist'
                                });
                            });
                            bands.forEach(function(b) {
                                items.push({
                                    label: b.title,
                                    type: 'band'
                                });
                            });
                            renderPerformerSuggestions(items);
                        }).catch(function() {
                            clearPerformersSuggestions();
                        });
                    }, 300);
                });
            }

            // Поиск композиторов и добавление в программу
            var programComposerSearchInput = document.getElementById('program-composer-search');
            var programComposerSuggestions = document.getElementById('program-composer-suggestions');
            var programTitleInput = document.getElementById('program-title-input');
            var programTextarea = document.getElementById('program-textarea');
            var programAddBtn = document.getElementById('program-add-btn');
            var composerTimer = null;

            function clearComposerSuggestions() {
                if (programComposerSuggestions) {
                    programComposerSuggestions.innerHTML = '';
                }
            }

            function renderComposerSuggestions(items) {
                if (!programComposerSuggestions) {
                    return;
                }
                programComposerSuggestions.innerHTML = '';
                items.forEach(function(item) {
                    var btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'block w-full text-left px-2 py-1 rounded-lg bg-slate-900/60 hover:bg-slate-800 text-[11px] text-slate-100';
                    btn.textContent = item.label;
                    btn.addEventListener('click', function() {
                        if (programComposerSearchInput) {
                            programComposerSearchInput.value = item.label;
                        }
                    });
                    programComposerSuggestions.appendChild(btn);
                });
            }

            if (programComposerSearchInput) {
                programComposerSearchInput.addEventListener('input', function() {
                    var q = programComposerSearchInput.value.trim();
                    if (composerTimer) {
                        clearTimeout(composerTimer);
                    }
                    if (q.length < 2) {
                        clearComposerSuggestions();
                        return;
                    }
                    composerTimer = setTimeout(function() {
                        fetch('/telegram-mini-app/search-composers?q=' + encodeURIComponent(q))
                            .then(function(r) {
                                if (!r.ok) {
                                    return [];
                                }
                                return r.json();
                            })
                            .then(function(items) {
                                items = items || [];
                                var mapped = items.map(function(c) {
                                    return {
                                        label: (c.last_name || '') + ', ' + (c.first_name || '')
                                    };
                                });
                                renderComposerSuggestions(mapped);
                            })
                            .catch(function() {
                                clearComposerSuggestions();
                            });
                    }, 300);
                });
            }

            if (programAddBtn) {
                programAddBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (!programTextarea) {
                        return;
                    }
                    var composer = programComposerSearchInput && programComposerSearchInput.value ? programComposerSearchInput.value.trim() : '';
                    var title = programTitleInput && programTitleInput.value ? programTitleInput.value.trim() : '';
                    if (!composer && !title) {
                        return;
                    }
                    var line = '';
                    if (composer && title) {
                        line = composer + ' — ' + title;
                    } else if (composer) {
                        line = composer;
                    } else {
                        line = title;
                    }
                    var current = programTextarea.value.trim();
                    programTextarea.value = current ? (current + '\n' + line) : line;
                    if (programTitleInput) {
                        programTitleInput.value = '';
                    }
                });
            }

            // Предпросмотр изображения
            var imageInput = document.getElementById('image-input');
            var imagePreviewWrapper = document.getElementById('image-preview-wrapper');
            var imagePreview = document.getElementById('image-preview');
            var imageRemoveBtn = document.getElementById('image-remove-btn');

            if (imageInput && imagePreviewWrapper && imagePreview) {
                imageInput.addEventListener('change', function() {
                    var file = imageInput.files && imageInput.files[0];
                    if (!file) {
                        imagePreviewWrapper.classList.add('hidden');
                        imagePreview.src = '#';
                        return;
                    }
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreviewWrapper.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                });
            }

            if (imageRemoveBtn && imageInput && imagePreviewWrapper && imagePreview) {
                imageRemoveBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    imageInput.value = '';
                    imagePreviewWrapper.classList.add('hidden');
                    imagePreview.src = '#';
                });
            }

            showStep(1);
        });
    </script>
</body>

</html>