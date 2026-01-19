@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Компактный заголовок -->
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Новое письмо</h1>
                        <p class="text-sm text-gray-600 mt-1">Отдел: {{ $department->name }}</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('departments.emails.index', $department) }}"
                           class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Отмена
                        </a>
                        <button type="submit"
                                form="emailForm"
                                name="save_draft"
                                value="1"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Черновик
                        </button>
                        <button type="submit"
                                form="emailForm"
                                name="action"
                                value="send"
                                class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-green-500">
                            Отправить
                        </button>
                    </div>
                </div>
            </div>

            <!-- Компактная форма -->
            <div class="bg-white rounded-xl shadow">
                <form id="emailForm"
                      action="{{ route('departments.emails.store', $department) }}"
                      method="POST"
                      enctype="multipart/form-data"
                      class="divide-y divide-gray-200">
                    @csrf

                    <!-- Верхняя часть - получатели и тема -->
                    <div class="p-4">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                            <!-- Получатели -->
                            <div class="lg:col-span-2">
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">
                                            Кому <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <input type="text"
                                                   name="to_emails"
                                                   required
                                                   class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-1 focus:ring-primary"
                                                   placeholder="email1@example.com, email2@example.com">
                                            <button type="button"
                                                    onclick="showContactModal('to')"
                                                    class="absolute right-2 top-2 text-gray-400 hover:text-primary">
                                                <i class="fas fa-address-book text-sm"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Копия и скрытая копия в одной строке -->
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">
                                                Копия (CC)
                                            </label>
                                            <input type="text"
                                                   name="cc_emails"
                                                   class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-1 focus:ring-primary"
                                                   placeholder="cc@example.com">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">
                                                Скрытая (BCC)
                                            </label>
                                            <input type="text"
                                                   name="bcc_emails"
                                                   class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-1 focus:ring-primary"
                                                   placeholder="bcc@example.com">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Правая колонка - тема и настройки -->
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">
                                        Тема <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                           name="subject"
                                           required
                                           class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-1 focus:ring-primary"
                                           placeholder="Тема письма"
                                           value="{{ old('subject') }}">
                                </div>

                                <!-- Быстрые настройки -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <label class="flex items-center">
                                            <input type="checkbox"
                                                   name="is_important"
                                                   value="1"
                                                   class="h-4 w-4 text-yellow-500 focus:ring-yellow-400 border-gray-300 rounded">
                                            <span class="ml-2 text-xs text-gray-700">Важное</span>
                                        </label>

                                        @if($templates->count() > 0)
                                            <div class="relative">
                                                <button type="button"
                                                        onclick="toggleTemplateMenu()"
                                                        class="text-xs text-gray-600 hover:text-primary flex items-center">
                                                    <i class="fas fa-file-alt mr-1"></i>
                                                    Шаблон
                                                </button>
                                                <div id="templateMenu"
                                                     class="hidden absolute z-10 mt-1 w-48 bg-white rounded-lg shadow-lg border border-gray-200">
                                                    <div class="py-1">
                                                        @foreach($templates as $template)
                                                            <button type="button"
                                                                    onclick="applyTemplate({{ $template->id }})"
                                                                    class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                                {{ $template->name }}
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Панель инструментов редактора -->
                    <div class="p-3 bg-gray-50 border-t">
                        <div class="flex flex-wrap items-center gap-1">
                            <button type="button" onclick="formatText('bold')"
                                    class="p-1.5 text-gray-600 hover:text-primary hover:bg-gray-100 rounded"
                                    title="Жирный">
                                <i class="fas fa-bold text-xs"></i>
                            </button>
                            <button type="button" onclick="formatText('italic')"
                                    class="p-1.5 text-gray-600 hover:text-primary hover:bg-gray-100 rounded"
                                    title="Курсив">
                                <i class="fas fa-italic text-xs"></i>
                            </button>
                            <button type="button" onclick="formatText('underline')"
                                    class="p-1.5 text-gray-600 hover:text-primary hover:bg-gray-100 rounded"
                                    title="Подчеркивание">
                                <i class="fas fa-underline text-xs"></i>
                            </button>
                            <button type="button" onclick="formatText('link')"
                                    class="p-1.5 text-gray-600 hover:text-primary hover:bg-gray-100 rounded"
                                    title="Ссылка">
                                <i class="fas fa-link text-xs"></i>
                            </button>
                            <div class="w-px h-4 bg-gray-300 mx-1"></div>
                            <button type="button" onclick="formatText('ul')"
                                    class="p-1.5 text-gray-600 hover:text-primary hover:bg-gray-100 rounded"
                                    title="Список">
                                <i class="fas fa-list-ul text-xs"></i>
                            </button>
                            <button type="button" onclick="formatText('ol')"
                                    class="p-1.5 text-gray-600 hover:text-primary hover:bg-gray-100 rounded"
                                    title="Нумерованный список">
                                <i class="fas fa-list-ol text-xs"></i>
                            </button>
                            <div class="w-px h-4 bg-gray-300 mx-1"></div>
                            <button type="button" onclick="insertEmoji()"
                                    class="p-1.5 text-gray-600 hover:text-primary hover:bg-gray-100 rounded"
                                    title="Эмодзи">
                                <i class="far fa-smile text-xs"></i>
                            </button>
                            <button type="button" onclick="insertSignature()"
                                    class="p-1.5 text-gray-600 hover:text-primary hover:bg-gray-100 rounded"
                                    title="Подпись">
                                <i class="fas fa-signature text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Тело письма -->
                    <div class="p-4">
                    <textarea name="body"
                              id="emailBody"
                              rows="12"
                              required
                              class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-1 focus:ring-primary resize-none"
                              placeholder="Начните писать ваше письмо здесь...">{{ old('body') }}</textarea>
                    </div>

                    <!-- Нижняя панель - вложения и метки -->
                    <div class="p-4 bg-gray-50 border-t">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <!-- Левая колонка - вложения -->
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label class="text-xs font-medium text-gray-700">
                                        <i class="fas fa-paperclip mr-1"></i> Вложения
                                    </label>
                                    <span class="text-xs text-gray-500" id="fileCount">0 файлов</span>
                                </div>

                                <!-- Компактная зона загрузки -->
                                <div class="relative">
                                    <input type="file"
                                           id="attachments"
                                           name="files[]"
                                           multiple
                                           class="hidden"
                                           onchange="handleFiles(this.files)">
                                    <label for="attachments"
                                           class="flex items-center justify-center w-full p-2 border-2 border-dashed border-gray-300 rounded-lg hover:border-primary cursor-pointer transition-colors">
                                        <div class="text-center">
                                            <i class="fas fa-cloud-upload-alt text-gray-400 mb-1"></i>
                                            <p class="text-xs text-gray-600">Нажмите или перетащите файлы</p>
                                            <p class="text-xs text-gray-400 mt-1">До 10 файлов, 50MB каждый</p>
                                        </div>
                                    </label>
                                </div>

                                <!-- Список файлов -->
                                <div id="fileList" class="mt-2 space-y-1 max-h-32 overflow-y-auto">
                                    <!-- Файлы появятся здесь -->
                                </div>
                            </div>

                            <!-- Правая колонка - метки и шаблоны -->
                            <div>
                                <div class="mb-3">
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        <i class="fas fa-tag mr-1"></i> Метки
                                    </label>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($tags as $tag)
                                            <label class="inline-flex items-center">
                                                <input type="checkbox"
                                                       name="tags[]"
                                                       value="{{ $tag->id }}"
                                                       class="h-3 w-3 text-primary focus:ring-primary border-gray-300 rounded">
                                                <span class="ml-1 text-xs text-gray-700">{{ $tag->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Шаблон -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">
                                        Сохранить как шаблон
                                    </label>
                                    <div class="flex space-x-2">
                                        <input type="text"
                                               name="template_name"
                                               placeholder="Название шаблона"
                                               class="flex-1 text-xs border border-gray-300 rounded px-2 py-1.5">
                                        <label class="inline-flex items-center px-2 py-1.5 border border-gray-300 rounded text-xs text-gray-700 bg-white">
                                            <input type="checkbox"
                                                   name="save_template"
                                                   value="1"
                                                   class="mr-1.5 h-3 w-3">
                                            Сохранить
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Информационная панель -->
            <div class="mt-4 flex items-center justify-between text-xs text-gray-500">
                <div>
                    Письмо будет отправлено от:
                    <span class="font-medium">{{ auth()->user()->name }} &lt;{{ auth()->user()->email }}&gt;</span>
                </div>
                <div class="flex items-center space-x-4">
                    <button type="button"
                            onclick="previewEmail()"
                            class="text-primary hover:text-secondary">
                        <i class="fas fa-eye mr-1"></i> Предпросмотр
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно контактов (упрощенное) -->
    <dialog id="contactModal" class="rounded-lg shadow-xl p-0 max-w-md">
        <div class="p-4">
            <div class="flex justify-between items-center mb-3">
                <h3 class="text-sm font-semibold text-gray-900">Выбор контактов</h3>
                <button onclick="closeContactModal()"
                        class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <input type="text"
                   id="contactSearch"
                   placeholder="Поиск..."
                   class="w-full text-sm border border-gray-300 rounded px-3 py-1.5 mb-3">

            <div id="contactList" class="max-h-60 overflow-y-auto space-y-1">
                <!-- Контакты загружаются через JS -->
            </div>

            <div class="mt-3 flex justify-end space-x-2">
                <button type="button"
                        onclick="closeContactModal()"
                        class="px-3 py-1.5 text-xs border border-gray-300 rounded text-gray-700">
                    Отмена
                </button>
                <button type="button"
                        onclick="applySelectedContacts()"
                        class="px-3 py-1.5 text-xs bg-primary text-white rounded">
                    Выбрать
                </button>
            </div>
        </div>
    </dialog>

    <!-- Предпросмотр письма -->
    <dialog id="previewModal" class="rounded-lg shadow-xl p-0 max-w-3xl">
        <div class="p-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-sm font-semibold text-gray-900">Предпросмотр письма</h3>
                <button onclick="closePreview()"
                        class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="previewContent" class="prose prose-sm max-w-none bg-gray-50 p-4 rounded-lg">
                <!-- Содержимое будет вставлено через JS -->
            </div>
        </div>
    </dialog>

    <!-- Модальное окно контактов -->
    <dialog id="contactModal" class="rounded-lg shadow-xl p-0 max-w-md">
        <div class="p-4">
            <div class="flex justify-between items-center mb-3">
                <h3 class="text-sm font-semibold text-gray-900">Выбор контактов</h3>
                <button onclick="closeContactModal()"
                        class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <input type="text"
                   id="contactSearch"
                   placeholder="Поиск..."
                   class="w-full text-sm border border-gray-300 rounded px-3 py-1.5 mb-3">

            <div id="contactList" class="max-h-60 overflow-y-auto space-y-1">
                <!-- Контакты будут загружены через JS -->
                <div class="text-center py-4 text-gray-500">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p class="text-xs mt-1">Загрузка контактов...</p>
                </div>
            </div>

            <div class="mt-3 flex justify-end space-x-2">
                <button type="button"
                        onclick="closeContactModal()"
                        class="px-3 py-1.5 text-xs border border-gray-300 rounded text-gray-700">
                    Отмена
                </button>
                <button type="button"
                        onclick="applySelectedContacts()"
                        class="px-3 py-1.5 text-xs bg-primary text-white rounded">
                    Выбрать
                </button>
            </div>
        </div>
    </dialog>
@endsection

@push('scripts')
    <script>

        // Работа с контактами
        let currentRecipientField = null;

        function showContactModal(field) {
            currentRecipientField = field;

            // Загрузка контактов
            loadContacts();

            // Показываем модальное окно
            document.getElementById('contactModal').showModal();
        }

        function closeContactModal() {
            document.getElementById('contactModal').close();
        }

        async function loadContacts() {
            try {
                // Получаем контакты из отдела
                const response = await fetch(`/api/departments/{{ $department->id }}/contacts`);
                const contacts = await response.json();

                const contactList = document.getElementById('contactList');

                if (!contacts || contacts.length === 0) {
                    contactList.innerHTML = `
                <div class="text-center py-4 text-gray-500">
                    <i class="fas fa-users"></i>
                    <p class="text-xs mt-1">Контакты не найдены</p>
                </div>
            `;
                    return;
                }

                let html = '';
                contacts.forEach(contact => {
                    html += `
                <div class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer contact-item"
                     onclick="toggleContactSelection(this, '${contact.id}', '${contact.email}')">
                    <input type="checkbox"
                           id="contact_${contact.id}"
                           class="h-3 w-3 mr-2">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-700">${contact.name || contact.email}</p>
                        ${contact.email ? `<p class="text-xs text-gray-500">${contact.email}</p>` : ''}
                    </div>
                </div>
            `;
                });

                contactList.innerHTML = html;

                // Поиск контактов
                document.getElementById('contactSearch').addEventListener('input', function(e) {
                    const searchTerm = e.target.value.toLowerCase();
                    const contactItems = document.querySelectorAll('.contact-item');

                    contactItems.forEach(item => {
                        const text = item.textContent.toLowerCase();
                        item.style.display = text.includes(searchTerm) ? 'flex' : 'none';
                    });
                });

            } catch (error) {
                console.error('Ошибка загрузки контактов:', error);
                document.getElementById('contactList').innerHTML = `
            <div class="text-center py-4 text-red-500">
                <i class="fas fa-exclamation-triangle"></i>
                <p class="text-xs mt-1">Ошибка загрузки</p>
            </div>
        `;
            }
        }

        // Если API еще не готов, используем заглушку
        async function loadContactsFallback() {
            const contactList = document.getElementById('contactList');

            // Заглушка с пользователями отдела
            const users = [
                    @foreach($department->users as $user)
                {
                    id: '{{ $user->id }}',
                    name: '{{ $user->name }}',
                    email: '{{ $user->email }}'
                },
                @endforeach
            ];

            if (users.length === 0) {
                contactList.innerHTML = `
            <div class="text-center py-4 text-gray-500">
                <i class="fas fa-users"></i>
                <p class="text-xs mt-1">В отделе нет пользователей</p>
            </div>
        `;
                return;
            }

            let html = '';
            users.forEach(user => {
                html += `
            <div class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer contact-item"
                 onclick="toggleContactSelection(this, '${user.id}', '${user.email}')">
                <input type="checkbox"
                       id="contact_${user.id}"
                       class="h-3 w-3 mr-2">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-700">${user.name}</p>
                    <p class="text-xs text-gray-500">${user.email}</p>
                </div>
            </div>
        `;
            });

            contactList.innerHTML = html;

            // Поиск контактов
            document.getElementById('contactSearch').addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const contactItems = document.querySelectorAll('.contact-item');

                contactItems.forEach(item => {
                    const text = item.textContent.toLowerCase();
                    item.style.display = text.includes(searchTerm) ? 'flex' : 'none';
                });
            });
        }

        // Используем заглушку пока нет API
        function loadContacts() {
            loadContactsFallback();
        }

        // Выбор контактов
        let selectedContacts = [];

        function toggleContactSelection(element, contactId, email) {
            const checkbox = element.querySelector('input[type="checkbox"]');
            checkbox.checked = !checkbox.checked;

            if (checkbox.checked) {
                element.classList.add('bg-blue-50', 'border', 'border-primary');
                if (!selectedContacts.find(c => c.id === contactId)) {
                    selectedContacts.push({ id: contactId, email: email });
                }
            } else {
                element.classList.remove('bg-blue-50', 'border', 'border-primary');
                selectedContacts = selectedContacts.filter(c => c.id !== contactId);
            }
        }

        function applySelectedContacts() {
            if (!currentRecipientField || selectedContacts.length === 0) {
                closeContactModal();
                return;
            }

            const emails = selectedContacts.map(c => c.email).join(', ');
            const inputField = document.querySelector(`[name="${currentRecipientField}_emails"]`) ||
                document.querySelector(`[name="to_emails"]`);

            if (inputField) {
                if (inputField.value) {
                    inputField.value += ', ' + emails;
                } else {
                    inputField.value = emails;
                }
            }

            // Сброс
            selectedContacts = [];
            document.querySelectorAll('.contact-item').forEach(item => {
                item.classList.remove('bg-blue-50', 'border', 'border-primary');
                item.querySelector('input[type="checkbox"]').checked = false;
            });

            closeContactModal();
        }

        // Если нужно, можно добавить простую версию без API
        function showSimpleContactModal() {
            currentRecipientField = 'to';

            // Простой список email
            const simpleContacts = [
                { email: 'manager@example.com', name: 'Менеджер проекта' },
                { email: 'support@example.com', name: 'Техподдержка' },
                { email: 'sales@example.com', name: 'Отдел продаж' },
            ];

            const contactList = document.getElementById('contactList');
            let html = '';
            simpleContacts.forEach(contact => {
                html += `
            <div class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer"
                 onclick="selectSimpleContact('${contact.email}')">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-700">${contact.name}</p>
                    <p class="text-xs text-gray-500">${contact.email}</p>
                </div>
            </div>
        `;
            });

            contactList.innerHTML = html;
            document.getElementById('contactModal').showModal();
        }

        function selectSimpleContact(email) {
            const inputField = document.querySelector('[name="to_emails"]');
            if (inputField) {
                if (inputField.value) {
                    inputField.value += ', ' + email;
                } else {
                    inputField.value = email;
                }
            }
            closeContactModal();
        }

        // Упрощенный JS для компактной формы
        let attachments = [];

        // Обработка файлов
        function handleFiles(fileList) {
            const fileListElement = document.getElementById('fileList');

            for (let file of fileList) {
                if (attachments.length >= 10) {
                    alert('Максимум 10 файлов');
                    break;
                }

                attachments.push(file);
                addFileToList(file);
            }

            updateFileCount();
        }

        function addFileToList(file) {
            const fileList = document.getElementById('fileList');
            const fileId = 'file_' + Date.now();

            const fileItem = document.createElement('div');
            fileItem.className = 'flex items-center justify-between p-2 bg-white border rounded text-xs';
            fileItem.id = fileId;
            fileItem.innerHTML = `
        <div class="flex items-center space-x-2">
            <i class="fas fa-file text-gray-400"></i>
            <span class="truncate max-w-xs">${file.name}</span>
            <span class="text-gray-500">(${formatFileSize(file.size)})</span>
        </div>
        <button type="button"
                onclick="removeFile('${fileId}', '${file.name}')"
                class="text-gray-400 hover:text-red-500">
            <i class="fas fa-times text-xs"></i>
        </button>
    `;

            fileList.appendChild(fileItem);
        }

        function removeFile(fileId, fileName) {
            const fileItem = document.getElementById(fileId);
            if (fileItem) {
                fileItem.remove();
                attachments = attachments.filter(file => file.name !== fileName);
                updateFileCount();
            }
        }

        function updateFileCount() {
            const count = attachments.length;
            document.getElementById('fileCount').textContent = `${count} файл${count === 1 ? '' : count > 1 && count < 5 ? 'а' : 'ов'}`;
        }

        function formatFileSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
        }

        // Работа с шаблонами
        function toggleTemplateMenu() {
            const menu = document.getElementById('templateMenu');
            menu.classList.toggle('hidden');
        }

        async function applyTemplate(templateId) {
            try {
                const response = await fetch(`/email-templates/${templateId}/preview`);
                const template = await response.json();

                document.querySelector('input[name="subject"]').value = template.subject;
                document.getElementById('emailBody').value = template.body;

                document.getElementById('templateMenu').classList.add('hidden');
            } catch (error) {
                console.error('Error:', error);
            }
        }

        // Форматирование текста (упрощенное)
        function formatText(type) {
            const textarea = document.getElementById('emailBody');
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const selected = textarea.value.substring(start, end);

            let formatted = selected;
            switch(type) {
                case 'bold': formatted = `**${selected}**`; break;
                case 'italic': formatted = `*${selected}*`; break;
                case 'underline': formatted = `__${selected}__`; break;
                case 'link':
                    const url = prompt('URL:', 'https://');
                    if (url) formatted = `[${selected || url}](${url})`;
                    break;
                case 'ul': formatted = `\n- ${selected}\n- `; break;
                case 'ol': formatted = `\n1. ${selected}\n2. `; break;
            }

            textarea.value = textarea.value.substring(0, start) + formatted + textarea.value.substring(end);
            textarea.focus();
        }

        function insertEmoji() {
            const emoji = '😊';
            insertAtCursor(emoji);
        }

        function insertSignature() {
            const signature = `\n\n--\n${document.querySelector('span.font-medium').textContent}`;
            insertAtCursor(signature);
        }

        function insertAtCursor(text) {
            const textarea = document.getElementById('emailBody');
            const start = textarea.selectionStart;
            textarea.value = textarea.value.substring(0, start) + text + textarea.value.substring(start);
            textarea.focus();
        }

        // Предпросмотр
        function previewEmail() {
            const subject = document.querySelector('input[name="subject"]').value;
            const body = document.getElementById('emailBody').value;
            const from = document.querySelector('span.font-medium').textContent;

            const preview = document.getElementById('previewContent');
            preview.innerHTML = `
        <div class="bg-white p-4 rounded border">
            <div class="border-b pb-2 mb-3">
                <strong>Тема:</strong> ${subject || '(без темы)'}
            </div>
            <div class="border-b pb-2 mb-3">
                <strong>От:</strong> ${from}
            </div>
            <div class="whitespace-pre-wrap">${body || '(текст письма)'}</div>
            ${attachments.length > 0 ? `
                <div class="mt-4 pt-3 border-t">
                    <strong>Вложения:</strong>
                    <ul class="mt-1">
                        ${attachments.map(f => `<li>• ${f.name} (${formatFileSize(f.size)})</li>`).join('')}
                    </ul>
                </div>
            ` : ''}
        </div>
    `;

            document.getElementById('previewModal').showModal();
        }

        function closePreview() {
            document.getElementById('previewModal').close();
        }

        // Валидация формы
        document.getElementById('emailForm').addEventListener('submit', function(e) {
            const required = ['to_emails', 'subject', 'body'];
            let valid = true;

            required.forEach(field => {
                const element = field === 'body'
                    ? document.getElementById('emailBody')
                    : document.querySelector(`[name="${field}"]`);

                if (!element.value.trim()) {
                    valid = false;
                    element.classList.add('border-red-300');
                    setTimeout(() => element.classList.remove('border-red-300'), 2000);
                }
            });

            if (!valid) {
                e.preventDefault();
                alert('Пожалуйста, заполните все обязательные поля');
            }
        });
    </script>
@endpush
