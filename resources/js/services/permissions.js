import { ref } from "vue";
import axios from "axios";

export default function usePermissions(){

    /**
     * Jogosultságok táblázathoz
     */
    const permissions = ref([]);
    /**
     * Jogosultságok kiválasztásahoz
     */
    const permissionsToSelect = ref([]);

    /**
     * Fetches permissions from the server and updates permissions.value
     */
    const getPermissions = async ( conf ) => {
//console.log('conf', conf);
        /**
         * Make a GET request to the 'getPermissions' route
         */
        let response = await axios.get( route('getPermissions', conf) );
//console.log('response.data', response.data.data);
        /**
         * Update permissions.value with the response data
         */
        permissions.value = response.data.data;
    };

    /**
     * Retrieves permissions for selection from the server and updates the permissions to select with the retrieved data
     */
    const getPermissionsToSelect = async () => {

        // Send request to server to retrieve permissions
        let response = await axios.get( route('getPermissionsToSelect') );

        // Update permissions to select with retrieved data
        permissionsToSelect.value = response.data.data;
    };

    const permissionCreate = async (record) => {
        axios.post( route('permissions') )
        .then(resource => {
            console.log('permissionCreate resource', resource);
        })
        .catch(error => {
            console.log('permissionCreate error', error);
        });
    };

    const permissionUpdate = async (record) => {
        axios.put()
        .then(response => {
            console.log('permissionUpdate response', response);
        })
        .catch(error => {
            console.log('permissionUpdate error', error);
        });
    };

    const permissionDelete = async (id) => {
        axios.delete( `/permissions/${id}` )
        .then(response => {
            console.log('permissionDelete response', response);
        })
        .catch(error => {
            console.log('permissionDelete error', error);
        });
    };

    const permissionBulkDelete = async (ids) => {
        axios.delete( route('permissions_bulkDelete', {
            data: {ids: ids}
        }) )
        .then(response => {
            console.log('permissionBulkDelete response', response);
        })
        .catch(error => {
            console.log('permissionBulkDelete error', error);
        })
    };

    const permissionRestore = async (id) => {
        axios.post(route('permission_restore'), {data: {id: id}})
        .then(response => {
            console.log('permissionRestore response', response);
        })
        .catch(error => {
            console.log('permissionRestore error', error);
        });
    };

    return {
        permissions, permissionsToSelect, 
        getPermissions, getPermissionsToSelect,
        permissionCreate, permissionUpdate,
        permissionDelete, permissionBulkDelete, permissionRestore
    };
}