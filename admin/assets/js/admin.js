document.addEventListener('DOMContentLoaded', function () {
    const deleteModal = document.getElementById('deleteConfirmModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;

            const recordId = button.getAttribute('data-record-id');
            const recordName = button.getAttribute('data-record-name') || 'this record';
            const action = button.getAttribute('data-action-name') || 'delete';

            const idInput = deleteModal.querySelector('input[name="delete_id"]');
            const modalRecord = deleteModal.querySelector('[data-delete-record]');
            const actionInput = deleteModal.querySelector('input[name="action"]');

            if (idInput) idInput.value = recordId;
            if (modalRecord) modalRecord.textContent = recordName;
            if (actionInput) actionInput.value = action;
        });
    }

    const editModal = document.getElementById('editRecordModal');
    if (editModal) {
        editModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;

            editModal.querySelectorAll('[data-fill]').forEach(function (field) {
                const key = field.getAttribute('data-fill');
                const value = button.getAttribute('data-' + key) || '';
                field.value = value;
            });
        });
    }

    document.querySelectorAll('[data-auto-submit]').forEach(function (field) {
        field.addEventListener('change', function () {
            field.closest('form')?.submit();
        });
    });
});
