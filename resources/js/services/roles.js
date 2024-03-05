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
        await axios.get(route('getRolesToTable', conf))
            .then(res => {
                rolesToTable.value = res.data.data;
            })
            .catch(error => console.error('getRolesToTable error', error));
    };

    /**
     * Lekérem az összes jogosultságot (gyorsabb verzió)
     */
    const getRoles = async () => {
        await axios.get( route('getAllRoles') )
        .then(res => {
            roles.value = res.data.data;
        })
        .catch(error => console.error('Error getRoles:', error));
    };
    
    /**
     * Jogosultság lekérése azonosító alapján
     */
    const getRoleById = (id) => {
        try{
            const { res } = axios.get(route('getRoleById'), id);
            role.value = res.data.data;
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
            const { res } = await axios.get( route('getRolesToSelect') );

            // Frissítse a szerepköröket a lekért adatokkal való kiválasztáshoz
            rolesToSelect.value = res.data.data;
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
            const { res } = await axios.post(route('roles'), record);
            response.value = res;
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
            const { res } = await axios.put(route('roles_update', id), record);
            
            response.value = res;
        } catch (error) {
            // Naplózza a szerepkör frissítés során fellépő hibákat
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
            // Törlési kérelem küldése a kiszolgáló felé
            const { res } = await axios.delete(`/roles/${id}`);
            response.value = res;
        } catch (error) {
            // Naplózza a szerepkör törlése során fellépő hibákat
            console.error('roleDelete error', error);
        }
    };

    const roleBulkDelete = async (ids) => axios.delete('roles_bulkDelete', { params: { ids } })
        .then(({ data }) => response.value = data)
        .catch(error => console.error('roleBulkDelete error', error));


    /**
     * Szerepkör visszaállítása
     * @param {number} id - Visszaállítandó szerepkör azonosítója
     */
    const roleRestore = async (id) => {
        try {
            // Send a POST request to restore the role
            const { res } = await axios.post(route('role_restore'), {data: {id: id}});
            response.value = res;
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