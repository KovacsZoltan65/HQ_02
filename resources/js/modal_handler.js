// =====================
    // MODAL KEZELÉS
    // =====================
    // beállítások
    function openSettingsModal() {
        $('#settingsModal').modal('show');
    };
    function closeSettingsModal() {
        $('#settingsModal').modal('hide');
    };
    // szerkesztés
    function openEditModal() {
        $('#editModal').modal('show');
    };
    function closeEditModal() {
        cancelEdit();

        $('#editModal').modal('hide');
    };
    // törlés
    function openDeleteModal() {
        $('#deleteModal').modal('show');
    };
    function closeDeleteModal() {
        $('#deleteModal').modal('hide');
    };