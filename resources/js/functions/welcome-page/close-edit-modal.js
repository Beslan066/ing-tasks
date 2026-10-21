 export  function closeEditModal() {
    console.log('close-editmodal.js')
            document.getElementById('editTaskModal').classList.add('hidden');
            document.getElementById('editTaskForm').reset();
            currentEditTaskId = null;
            taskEditSelectedFiles = [];
            document.getElementById('editUploadNewFilesInput').value = '';
            document.getElementById('editUploadFilesList').classList.add('hidden');
            document.body.classList.remove('overflow-y-hidden')
        }
