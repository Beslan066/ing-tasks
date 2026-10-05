
(function () {
    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.content : '';
    }

    async function handleEditTaskSubmit(e) {
        e.preventDefault();

        const form = e.currentTarget;
        const taskId = document.getElementById('editTaskId').value;
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn ? submitBtn.innerHTML : '';

        if (submitBtn) {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Сохранение...';
            submitBtn.disabled = true;
        }

        try {
            const formData = new FormData(form);
            formData.set('_method', 'POST');
            const state = window.taskEditState || { selected: [] };
            const selectedFileIds = state.selected.map(f => Number(f.id));
            console.log('Отправляемые ID файлов на сервер:', selectedFileIds);
            formData.append('selected_files', JSON.stringify(selectedFileIds));

            const newFilesInput = document.getElementById('editUploadNewFilesInput');
            if (newFilesInput && newFilesInput.files.length > 0 && !newFilesInput.name) {
                for (let i = 0; i < newFilesInput.files.length; i++) {
                    formData.append('new_files[]', newFilesInput.files[i]);
                }
                console.log('Новых файлов для загрузки:', newFilesInput.files.length);
            }

            const response = await fetch(`/tasks/${taskId}/update`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                body: formData
            });

            const raw = await response.text();
            let data;
            try {
                data = JSON.parse(raw);
            } catch (_) {
                console.error('Сервер вернул не JSON:', raw.slice(0, 500));
                throw new Error(`Некорректный ответ сервера (HTTP ${response.status})`);
            }
            console.log('Ответ сервера:', data);

            if (response.ok && data.success) {
                showNotification('Задача успешно обновлена!', 'success');
                closeEditModal();
                location.reload();
            } else {
                const firstError = data.errors ? Object.values(data.errors)[0][0] : null;
                showNotification(firstError || data.message || 'Ошибка при обновлении задачи', 'error');
            }
        } catch (error) {
            console.error('Ошибка:', error);
            showNotification('Ошибка при обновлении задачи: ' + error.message, 'error');
        } finally {
            if (submitBtn) {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        }
    }

    function bindEditTaskForm() {
        const form = document.getElementById('editTaskForm');
        if (!form || form.dataset.bound === '1') return;
        form.dataset.bound = '1';
        form.addEventListener('submit', handleEditTaskSubmit);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindEditTaskForm);
    } else {
        bindEditTaskForm();
    }
})();
