import { ref } from "vue";
import axios from "axios";

export default function useRoles(){

    /**
     * Szerepkörök
     */
    const roles = ref({});
    /**
     * Szerepkörök
     */
    const rolesToSelect = ref({});

    /**
     * Szerepkörök lekérése a szerverről
     */
    const getRoles = async () => {
        // Készítsen GET-kérést a „getRoles” végponthoz
        let response = await axios.get( route('getRoles') );
        // Frissítse a szerepek értékét a válaszból származó adatokkal
        roles.value = response.data.data;
    };


    /**
     * Lekéri a kiválasztandó szerepköröket a kiszolgálóról, és frissíti a rolesToSelect értéket
     */
    const getRolesToSelect = async () => {
        // Kérjen GET-kérést a kiszolgálóhoz a kiválasztandó szerepkörök lekéréséhez
        let response = await axios.get( route('getRolesToSelect') );
        // Frissítse a rolesToSelect értéket a szerver válaszából származó adatokkal
        rolesToSelect.value = response.data.data;
    };

    /**
     * Új szerepkört hoz létre a megadott adatokkal.
     * @param {Object} data - Az új szerepkör adatai.
     * @returns {Promise} - Ígéret, amely a szerep létrejöttével megszűnik.
     */
    const roleCreate = async (record) => {
        await axios.post( route('roles'), record )
        .then(response => {
            console.log('roleCreate response', response);
        })
        .catch(error => {
            console.log('roleCreate error', error);
        });
    };

    /**
     * Frissítse a szerepkört a megadott azonosítóval a megadott adatok segítségével
     * @param {string} id - A frissítendő szerep azonosítója
     * @param {object} data - A szerep frissítéséhez szükséges adatok
     * @returns {Promise} - Ígéret, amely a szerep frissítése után megszűnik
     */
    const roleUpdate = async (id, data) => {
        // A szerepkör frissítésének megvalósítása a megadott azonosítóval a megadott adatok felhasználásával
        await axios.put( route('roles_update', {role: id}, {
            id: data.id,
            name: data.name,
            guard_name: data.guard_name
        }) )
        .then(response => {
            console.log('roleUpdate response', response);
        })
        .catch(error => {
            console.log('roleUpdate error', error);
        });
    }

    /**
     * Töröl egy szerepet az azonosítója alapján
     * @param {string} id - A törlendő szerep azonosítója
     */
    const roleDelete = async (id) => {
        await axios.delete(`/roles/${id}`)
        .then(response => {
            console.log('roleDelete response', response);
        })
        .catch(error => {
            console.log('roleDelete error', error);
        });
    }

    const rolesBulkDelete = async (ids) => {
        await axios.delete('roles_bulkDelete')
        .then(response => {
            console.log('rolesBulkDelete response', response);
        })
        .catch(error => {
            console.log('rolesBulkDelete error', error);
        });
    }

    /**
     * Aszinkron módon visszaállítja a szerepet a megadott azonosítóval.
     * 
     * @param {string} id - A visszaállítandó szerep azonosítója
     */
    const roleRestore = async (id) => {
        await axios.post(route('role_restore'), {data: {id: id}})
        .then(response => {
            console.log('roleRestore response', response);
        })
        .catch(error => {
            console.log('roleRestore error', error);
        });
    };

    /**
     * Visszatérés
     */
    return {
        roles, rolesToSelect, 
        getRoles, getRolesToSelect,
        roleCreate, roleUpdate,
        roleDelete, rolesBulkDelete, roleRestore
    };
};