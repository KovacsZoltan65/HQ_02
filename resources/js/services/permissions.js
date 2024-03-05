import { ref } from "vue";
import axios from "axios";

export default function usePermissions(){

    const permissions = ref([]);
    /**
     * Jogosultságok táblázathoz
     */
    const permissionsToTable = ref([]);
    /**
     * Jogosultságok kiválasztásahoz
     */
    const permissionsToSelect = ref([]);

    /**
     * Összes jogosultság
     */
    const permission = ref({});

    const getPermissionsToTable = async (conf) => {
        await axios.get(
            route('getPermissionsToTable', conf)
        ).then(
            response => permissionsToTable.value = response.data.data
        ).catch(
            error => console.error('Error getPermissionsToTable:', error)
        );
    };

    /**
     * Lekérem az összes jogosultságot
     */
    const getPermissions = async () => {
        await axios.get(
            route('getAllPermissions')
        ).then(
            permissions.value = response.data.data
        ).catch(
            error => console.error('Error getPermissions:', error)
        );
    };
    
    /**
     * Jogosultság lekérése azonosító alapján
     */
    const getPermissionById = async (id) => {
        await axios.get(
            route('getPermissionById'), id
        ).then(
            permission.value = response.data.data
        ).catch(
            error => console.error('Error getPermissionById:', error)
        );
    };

    /**
     * Retrieves permissions for selection from the server and updates the permissions to select with the retrieved data
     */
    const getPermissionsToSelect = async () => {
        await axios.get(
            route('getPermissionsToSelect')
        ).then(
            response => permissionsToSelect.value = response.data.data
        ).catch(
            error => console.error('Error getPermissionsToSelect:', error)
        );
    };

    /**
     * Aszinkron módon létrehoz egy új engedélyrekordot.
     * @param {object} record - A létrehozandó engedélyrekord.
     */
    const permissionCreate = async (record) => {
        await axios.post(
            route('permissions'), record
        ).then(
            res => { response.value = res.data.data; }
        ).catch(error => console.error('permissionCreate error', error));
    };

    /**
     * Update permission record with the given ID
     * @param {Object} record - The data to update
     * @param {number} id - The ID of the permission record
     */
    const permissionUpdate = async (id, record) => {
        await axios.put(
            route('permissions_update', id), record
        ).then(
            res => { response.value = res.data.data; }
        ).catch(error => console.error('permissionUpdate error', error));
    };

    /**
     * Deletes a permission by ID
     * @param {string} id - The ID of the permission to delete
     * @returns {Promise} - The result of the permission deletion
     */
    const permissionDelete = async (id) => {
        await axios.delete(
            `/permissions/${id}`
        ).then(
            res => { response.value = res.data.data; }
        ).catch(error => console.error('permissionDelete error', error));
    };

    const permissionBulkDelete = async (ids) => {
        await axios.delete(
            '/permissions_bulkDelete', { params: { ids } }
        ).then(
            res => { response.value = res.data.data; }
        ).catch(error => console.error('permissionBulkDelete error', error));
    };

    /**
     * Restore permission for a specific user
     * @param {number} id - The ID of the user
     */
    const permissionRestore = async (id) => {
        await axios.post(
            route('permission_restore'), {data: {id: id}}
        ).then(
            res => { response.value = res.data.data; }
        ).catch(error => console.error('permissionRestore error', error));
    };

    return {
        permissions, permissionsToSelect, permissionsToTable, permission, 
        getPermissions, getPermissionsToTable, getPermissionsToSelect, getPermissionById,
        permissionCreate, permissionUpdate,
        permissionDelete, permissionBulkDelete, permissionRestore
    };
}