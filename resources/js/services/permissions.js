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

    /**
     * Fetches permissions from the server and updates permissions.value
     */
    const getPermissionsToTable = async (conf) => {
        try {
            /**
             * Make a GET request to the 'getPermissions' route
             */
            let response = await axios.get(route('getPermissionsToTable', conf));
            //console.log('getPermissionsToTable', response);
            /**
             * Update permissions.value with the response data
             */
            permissionsToTable.value = response.data.data;
        } catch (error) {
            // Handle error
            console.error('getPermissionsToTable error', error);
        }
    };

    /**
     * Lekérem az összes jogosultságot
     */
    const getPermissions = () => {
        try{
            let response = axios.get( route('getAllPermissions') );
            permissions.value = response.data.data;
        }catch(error){
            console.error('Error getPermissions:', error);
        }
    };
    
    /**
     * Jogosultság lekérése azonosító alapján
     */
    const getPermissionById = (id) => {
        try{
            let response = axios.get(route('getPermissionById'), id);
            permission.value = response.data.data;
        }catch(error){
            console.error('Error getPermission:', error);
        }
    };

    /**
     * Retrieves permissions for selection from the server and updates the permissions to select with the retrieved data
     */
    const getPermissionsToSelect = async () => {
        try {
            // Send request to server to retrieve permissions
            let response = await axios.get( route('getPermissionsToSelect') );

            // Update permissions to select with retrieved data
            permissionsToSelect.value = response.data.data;
        } catch (error) {
            console.error('Error retrieving permissions:', error);
        }
    };

    /**
     * Asynchronously creates a new permission record.
     * @param {object} record - The permission record to be created.
     */
    const permissionCreate = async (record) => {
        try {
            // Destructure the 'data' property from the response
            const { data } = await axios.post(route('permissions'), record);
            // Return the 'data' property directly
            return data;
        } catch (error) {
            // Log any errors that occur during the creation process
            console.error('permissionCreate error', error);
        }
    };

    /**
     * Update permission record with the given ID
     * @param {Object} record - The data to update
     * @param {number} id - The ID of the permission record
     */
    const permissionUpdate = async (id, record) => {

        try {
            // Make a PUT request to update the permission record
            const result = await axios.put(route('permissions_update', id), record);
            
            return result;
        } catch (error) {
            console.error('permissionUpdate error', error);
        }
    };

    /**
     * Deletes a permission by ID
     * @param {string} id - The ID of the permission to delete
     * @returns {Promise} - The result of the permission deletion
     */
    const permissionDelete = async (id) => {
        try {
            // Send a delete request to the server to delete the permission
            return await axios.delete(`/permissions/${id}`);
        } catch (error) {
            // Log any errors that occur during the permission deletion process
            console.error('permissionDelete error', error);
        }
    };

    const permissionBulkDelete = async (ids) => {
        try {
            const response = await axios.delete('permissions_bulkDelete', { params: { ids } });
            return response.data;
        } catch (error) {
            console.error('permissionBulkDelete error', error);
        }
    };

    /**
     * Restore permission for a specific user
     * @param {number} id - The ID of the user
     */
    const permissionRestore = async (id) => {
        try {
            // Send a POST request to restore the permission
            const response = await axios.post(route('permission_restore'), {data: {id: id}});
            return response;
        } catch (error) {
            // Log any errors that occur during the permission restore process
            console.error('permissionRestore error', error);
        }
    };

    return {
        permissions, permissionsToSelect, permissionsToTable, permission, 
        getPermissions, getPermissionsToTable, getPermissionsToSelect, getPermissionById,
        permissionCreate, permissionUpdate,
        permissionDelete, permissionBulkDelete, permissionRestore
    };
}