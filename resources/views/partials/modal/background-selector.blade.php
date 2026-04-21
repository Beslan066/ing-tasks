<div id="backgroundSelectorModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4">
        <div class="flex justify-between items-center p-6 border-b">
            <h3 class="text-xl font-semibold">Выбор фона</h3>
            <button onclick="closeBackgroundSelector()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="p-6">
            @php
                $backgroundEnabled = auth()->check() && auth()->user()->background_enabled;
                $backgroundImage = auth()->check() ? auth()->user()->background_image : null;
            @endphp

                <!-- Опция без фона -->
            <div class="mb-6">
                <label class="flex items-center cursor-pointer p-3 border rounded-lg hover:bg-gray-50 transition-colors">
                    <input type="radio" name="background" value=""
                           onchange="selectBackground('', false)"
                        {{ !$backgroundEnabled ? 'checked' : '' }}>
                    <div class="ml-3">
                        <span class="font-medium">Без фона</span>
                        <p class="text-sm text-gray-500">Sidebar будет с белым фоном и тенью</p>
                    </div>
                </label>
            </div>

            <!-- Сетка с фонами -->
            <div class="mb-2">
                <p class="font-medium mb-3">Доступные фоны:</p>
                <div class="grid grid-cols-3 gap-4" id="backgroundsGrid">
                    @php
                        $fones = [];
                        if (is_dir(public_path('images/fones'))) {
                            $files = glob(public_path('images/fones/*.{jpg,jpeg,png,gif,webp}'), GLOB_BRACE);
                            foreach($files as $file) {
                                $fones[] = asset('images/fones/' . basename($file));
                            }
                        }
                    @endphp

                    @forelse($fones as $index => $fone)
                        <div class="relative group cursor-pointer background-option"
                             data-image="{{ $fone }}"
                             onclick="selectBackground('{{ $fone }}', true, this)">
                            <div class="aspect-video rounded-lg overflow-hidden border-2
                                {{ $backgroundEnabled && $backgroundImage == $fone ? 'border-primary' : 'border-transparent' }}
                                hover:border-primary transition-all">
                                <img src="{{ $fone }}" alt="Фон {{ $index + 1 }}"
                                     class="w-full h-full object-cover">
                            </div>
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all rounded-lg"></div>
                            <p class="text-xs text-center mt-1 text-gray-600">Фон {{ $index + 1 }}</p>
                        </div>
                    @empty
                        <div class="col-span-3 text-center py-8 text-gray-500">
                            <i class="fas fa-images text-4xl mb-2"></i>
                            <p>Нет доступных фонов</p>
                            <p class="text-sm mt-1">Добавьте изображения в папку <code class="bg-gray-100 px-2 py-1 rounded">images/fones/</code></p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="flex justify-end p-6 border-t">
            <button onclick="closeBackgroundSelector()"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">
                Закрыть
            </button>
        </div>
    </div>
</div>

<script>
    function openBackgroundSelector() {
        const modal = document.getElementById('backgroundSelectorModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeBackgroundSelector() {
        const modal = document.getElementById('backgroundSelectorModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    function selectBackground(imagePath, enabled, element = null) {
        console.log('Selecting background:', imagePath, enabled); // Для отладки

        // Проверяем что функция updateBackground существует
        if (typeof window.updateBackground === 'function') {
            window.updateBackground(enabled ? imagePath : null, enabled);
        } else {
            console.error('updateBackground function not found');
            // Если функции нет, попробуем отправить запрос напрямую
            fetch('{{ route("user.updateBackground") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    background_image: enabled ? imagePath : null,
                    background_enabled: enabled
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload(); // Перезагружаем страницу для применения изменений
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Обновляем состояние радиокнопок
        const radios = document.querySelectorAll('input[name="background"]');
        radios.forEach(radio => {
            if (enabled && radio.value === imagePath) {
                radio.checked = true;
            } else if (!enabled && radio.value === '') {
                radio.checked = true;
            }
        });

        // Обновляем стили выбранных элементов
        const allOptions = document.querySelectorAll('.background-option');
        allOptions.forEach(option => {
            const borderDiv = option.querySelector('.aspect-video');
            if (borderDiv) {
                borderDiv.classList.remove('border-primary');
                borderDiv.classList.add('border-transparent');
            }
        });

        if (enabled && element) {
            const borderDiv = element.querySelector('.aspect-video');
            if (borderDiv) {
                borderDiv.classList.remove('border-transparent');
                borderDiv.classList.add('border-primary');
            }
        }

        // Закрываем модальное окно
        setTimeout(() => {
            closeBackgroundSelector();
        }, 300);
    }

    // Закрытие по клику вне модального окна
    document.getElementById('backgroundSelectorModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeBackgroundSelector();
        }
    });

    // Закрытие по ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('backgroundSelectorModal');
            if (modal && !modal.classList.contains('hidden')) {
                closeBackgroundSelector();
            }
        }
    });
</script>
