<!-- Модальное окно подтверждения удаления отдела -->
<div id="deleteDepartmentModal"
     role="alertdialog" aria-modal="true"
     aria-labelledby="deleteDepartmentTitle" aria-describedby="deleteDepartmentDesc"
     onclick="if (event.target === this) closeDeleteDepartmentModal()"
     class="fixed inset-0 bg-slate-900/40 flex items-center justify-center hidden z-50 backdrop-blur-sm p-4">

    <div class="modal-content bg-white rounded-[24px] w-full max-w-md max-h-[90vh] overflow-y-auto shadow-2xl shadow-slate-900/10
                scrollbar-none [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden max-[500px]:w-[98%]">

        <div class="px-8 pt-8 pb-6 text-center max-[500px]:px-5 max-[500px]:pt-6">
            <div class="mx-auto w-14 h-14 rounded-full bg-rose-50 border border-rose-100 flex items-center justify-center mb-4">
                <i class="fas fa-trash-alt text-lg text-rose-500"></i>
            </div>

            <h3 id="deleteDepartmentTitle" class="text-[22px] font-semibold text-slate-800 tracking-tight max-[500px]:text-xl">Удалить отдел?</h3>
            <p id="deleteDepartmentDesc" class="text-[13px] text-slate-500 mt-2">
                Отдел <span id="deleteDepartmentName" class="font-semibold text-slate-700"></span> будет удалён без возможности восстановления.
            </p>

            <div class="mt-5 text-left px-4 py-3 bg-rose-50/60 border border-rose-100 rounded-xl">
                <p class="text-[13px] text-rose-800 font-medium mb-1.5">Вместе с отделом удалятся:</p>
                <ul class="text-[13px] text-rose-700 space-y-1">
                    <li class="flex items-center gap-2"><i class="fas fa-circle text-[5px] opacity-70"></i>задачи</li>
                    <li class="flex items-center gap-2"><i class="fas fa-circle text-[5px] opacity-70"></i>письма</li>
                    <li class="flex items-center gap-2"><i class="fas fa-circle text-[5px] opacity-70"></i>файлы</li>
                </ul>
            </div>
        </div>

        <!-- Кнопки действий -->
        <div class="flex justify-end items-center gap-3 px-8 py-5 border-t border-slate-100 max-[500px]:px-5 max-[500px]:justify-center max-[500px]:flex-col-reverse">
            <x-ui.button type="button" variant="secondary" onclick="closeDeleteDepartmentModal()">
                Отмена
            </x-ui.button>

            <x-ui.button
                type="button"
                id="confirmDeleteDepartmentBtn"
                variant="danger"
                icon="fas fa-trash-alt"
                onclick="confirmDeleteDepartment()"
            >
                <span id="confirmDeleteDepartmentText">Удалить</span>
            </x-ui.button>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        (function () {
            let pendingDepartmentId = null;

            function onEsc(e) {
                if (e.key === 'Escape') window.closeDeleteDepartmentModal();
            }

            function setLoading(isLoading) {
                const btn = document.getElementById('confirmDeleteDepartmentBtn');
                btn.querySelector('i').className =
                    (isLoading ? 'fas fa-spinner fa-spin' : 'fas fa-trash-alt') + ' text-[11px] mr-1.5';
                document.getElementById('confirmDeleteDepartmentText').textContent =
                    isLoading ? 'Удаление...' : 'Удалить';
                btn.disabled = isLoading;
            }

            window.deleteDepartment = function (departmentId, departmentName = '') {
                pendingDepartmentId = departmentId;
                document.getElementById('deleteDepartmentName').textContent = departmentName ? `«${departmentName}»` : '';
                setLoading(false);
                document.getElementById('deleteDepartmentModal').classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
                document.addEventListener('keydown', onEsc);
                document.getElementById('confirmDeleteDepartmentBtn').focus();
            };

            window.closeDeleteDepartmentModal = function () {
                document.getElementById('deleteDepartmentModal').classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                document.removeEventListener('keydown', onEsc);
                pendingDepartmentId = null;
            };

            window.confirmDeleteDepartment = async function () {
                if (!pendingDepartmentId) return;
                setLoading(true);

                try {
                    const response = await fetch(`/departments/${pendingDepartmentId}/delete`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                    });
                    const data = await response.json();

                    if (data.success) {
                        location.reload();
                        return;
                    }

                    setLoading(false);
                    showNotification('Ошибка при удалении: ' + (data.message || 'неизвестная ошибка'), 'error');
                } catch (error) {
                    console.error('Error:', error);
                    setLoading(false);
                    showNotification('Произошла ошибка при удалении', 'error');
                }
            };
        })();
    </script>
@endpush
