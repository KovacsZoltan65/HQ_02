import { ref } from "vue";
import axios from "axios";

export default function useRoles(){

    const roles = ref([]);
    /**
     * Jogosultságok táblázathoz
     */
    const rolesToTable = ref([]);
    /**
     * Jogosultságok kiválasztásahoz
     */
    const rolesToSelect = ref([]);

    /**
     * Összes jogosultság
     */
    const role = ref({});

    /**
     * Fetches roles from the server and updates roles.value
     */
    const getRolesToTable = async (conf) => {
        try {
            /**
             * Make a GET request to the 'getRoles' route
             */
            let response = await axios.get(route('getRolesToTable', conf));
            //console.log('getRolesToTable', response);
            /**
             * Update roles.value with the response data
             */
            rolesToTable.value = response.data.data;
        } catch (error) {
            // Handle error
            console.error('getRolesToTable error', error);
        }
    };

    /**
     * Lekérem az összes jogosultságot
     */
    const getRoles = () => {
        try{
            let response = axios.get( route('getAllRoles') );
            roles.value = response.data.data;
        }catch(error){
            console.error('Error getRoles:', error);
        }
    };
    
    /**
     * Jogosultság lekérése azonosító alapján
     */
    const getRoleById = (id) => {
        try{
            let response = axios.get(route('getRoleById'), id);
            role.value = response.data.data;
        }catch(error){
            console.error('Error getRole:', error);
        }
    };

    /**
     * Retrieves roles for selection from the server and updates the roles to select with the retrieved data
     */
    const getRolesToSelect = async () => {
        try {
            // Send request to server to retrieve roles
            let response = await axios.get( route('getRolesToSelect') );

            // Update roles to select with retrieved data
            rolesToSelect.value = response.data.data;
        } catch (error) {
            console.error('Error retrieving roles:', error);
        }
    };

    /**
     * Asynchronously creates a new role record.
     * @param {object} record - The role record to be created.
     */
    const roleCreate = async (record) => {
        try {
            // Destructure the 'data' property from the response
            const { data } = await axios.post(route('roles'), record);
            // Return the 'data' property directly
            return data;
        } catch (error) {
            // Log any errors that occur during the creation process
            console.error('roleCreate error', error);
        }
    };

    /**
     * Update role record with the given ID
     * @param {Object} record - The data to update
     * @param {number} id - The ID of the role record
     */
    const roleUpdate = async (id, record) => {

        try {
            // Make a PUT request to update the role record
            const result = await axios.put(route('roles_update', id), record);
            
            return result;
        } catch (error) {
            console.error('roleUpdate error', error);
        }
    };

    /**
     * Deletes a role by ID
     * @param {string} id - The ID of the role to delete
     * @returns {Promise} - The result of the role deletion
     */
    const roleDelete = async (id) => {
        try {
            // Send a delete request to the server to delete the role
            return await axios.delete(`/roles/${id}`);
        } catch (error) {
            // Log any errors that occur during the role deletion process
            console.error('roleDelete error', error);
        }
    };

    const roleBulkDelete = async (ids) => {
        try {
            const response = await axios.delete('roles_bulkDelete', { params: { ids } });
            return response.data;
        } catch (error) {
            console.error('roleBulkDelete error', error);
        }
    };

    /**
     * Restore role for a specific user
     * @param {number} id - The ID of the user
     */
    const roleRestore = async (id) => {
        try {
            // Send a POST request to restore the role
            const response = await axios.post(route('role_restore'), {data: {id: id}});
            return response;
        } catch (error) {
            // Log any errors that occur during the role restore process
            console.error('roleRestore error', error);
        }
    };

    return {
        roles, rolesToSelect, rolesToTable, role, 
        getRoles, getRolesToTable, getRolesToSelect, getRoleById,
        roleCreate, roleUpdate,
        roleDelete, roleBulkDelete, roleRestore
    };
}