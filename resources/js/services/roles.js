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

    const response = ref([]);

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
     * Kiválasztandó szerepköröket kér le a kiszolgálóról, 
     * és frissíti a szerepkörök kiválasztásához a letöltött adatokkal
     */
    const getRolesToSelect = async () => {
        try {
            // Kérelem küldése a szervernek a szerepkörök lekéréséhez
            let response = await axios.get( route('getRolesToSelect') );

            // Frissítse a szerepköröket a lekért adatokkal való kiválasztáshoz
            rolesToSelect.value = response.data.data;
        } catch (error) {
            console.error('Error retrieving roles:', error);
        }
    };

    /**
     * Aszinkron módon létrehoz egy új szerepkörrekordot.
     * @param {object} record - A létrehozandó szereprekord.
     */
    const roleCreate = async (record) => {
        try {
            const { response } = await axios.post(route('roles'), record);
            return response;
        } catch (error) {
            console.error('roleCreate error', error);
        }
    };

    /**
     * Szerepkörrekord frissítése a megadott azonosítóval
     * @param {Object} record - A frissítendő adatok
     * @param {number} id - A szereprekord azonosítója
     */
    const roleUpdate = async (id, record) => {

        try {
            let result = await axios.put(route('roles_update', id), record);
            
            response.value =  result;
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
            let response = await axios.delete(`/roles/${id}`);
        } catch (error) {
            // Log any errors that occur during the role deletion process
            console.error('roleDelete error', error);
        }
    };

    /**
     * Bulk delete roles
     *
     * @param {array} ids - A törölni kívánt szerepkörök azonosítói
     *
     * @returns {Promise} - The result of the role bulk deletion
     */
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
        roles, rolesToSelect, rolesToTable, role, response,
        getRoles, getRolesToTable, getRolesToSelect, getRoleById,
        roleCreate, roleUpdate,
        roleDelete, roleBulkDelete, roleRestore
    };
}