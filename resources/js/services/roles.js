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
        await axios.get(
            route('getRolesToTable', conf)
        ).then(res => { 
            rolesToTable.value = res.data.data; 
        }).catch(error => console.error('getRolesToTable error', error));
    };

    /**
     * Lekérem az összes jogosultságot (gyorsabb verzió)
     */
    const getRoles = async () => {
        await axios.get(
            route('getAllRoles')
        ).then(
            res => { roles.value = res.data.data; }
        ).catch( error => console.error('Error getRoles:', error) );
    };
    
    /**
     * Jogosultság lekérése azonosító alapján
     */
    const getRoleById = async (id) => {
        await axios.get(
            route('getRoleById'), id
        ).then(
            res => { role.value = res.data.data; }
        ).catch( error => console.error('Error getRoleById:', error) );
    };

    /**
     * Kiválasztandó szerepköröket kér le a kiszolgálóról, 
     * és frissíti a szerepkörök kiválasztásához a letöltött adatokkal
     */
    const getRolesToSelect = async () => {
        await axios.get(
            route('getRolesToSelect')
        ).then(
            res => {rolesToSelect.value = res.data.data;}
        ).catch(
            error => console.error('Error getRolesToSelect:', error)
        );
    };

    /**
     * Aszinkron módon létrehoz egy új szerepkörrekordot.
     * @param {object} record - A létrehozandó szereprekord.
     */
    const roleCreate = async (record) => {
        await axios.post(
            route('roles'), record
        ).then(
            res => { response.value = res.data.data; }
        ).catch(
            error => console.error('roleCreate error', error)
        );
    };

    /**
     * Szerepkörrekord frissítése a megadott azonosítóval
     * @param {Object} record - A frissítendő adatok
     * @param {number} id - A szereprekord azonosítója
     */
    const roleUpdate = async (id, record) => {
        await axios.put(
            route('roles_update', id), record
        ).then(
            res => { response.value = res.data.data; }
        ).catch(
            error => console.error('roleUpdate error', error)
        );
    };

    /**
     * Deletes a role by ID
     * @param {string} id - The ID of the role to delete
     * @returns {Promise} - The result of the role deletion
     */
    const roleDelete = async (id) => {
        await axios.delete(
            route('roles_delete', id)
        ).then(
            res => { response.value = res.data.data; }
        ).catch(
            error => console.error('roleDelete error', error)
        );
    };

    /**
     * Bulk delete roles.
     *
     * @param {array} ids - The array of role IDs to be deleted
     * @return {Promise} A Promise that resolves with the response data or rejects with an error
     */
    const roleBulkDelete = async (ids) => {
        await axios.delete('roles_bulkDelete', { params: { ids } }
        ).then(
            res => { response.value = res.data.data; }
        ).catch(
            error => console.error('roleBulkDelete error', error)
        );
    }

    /**
     * Szerepkör visszaállítása
     * @param {number} id - Visszaállítandó szerepkör azonosítója
     */
    const roleRestore = async (id) => {
        await axios.post(
            //
        ).then(
            res => { response.value = res.data.data; }
        ).catch(error => console.error('Error roleRestore:', error));
    };

    return {
        roles, rolesToSelect, rolesToTable, role, response,
        getRoles, getRolesToTable, getRolesToSelect, getRoleById,
        roleCreate, roleUpdate,
        roleDelete, roleBulkDelete, roleRestore
    };
}