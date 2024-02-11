import { ref } from "vue";
import axios from "axios";

export default function useRoles(){

    const roles = ref({});
    const rolesToSelect = ref({});

    const getRoles = async () => {
        //let response = await axios.get('/api/get_roles');
        let response = await axios.get( route('getRoles') );
        roles.value = response.data.data;
        //console.log(roles.value);
    };

    const getRolesToSelect = async () => {
        let response = await axios.get( route('getRolesToSelect') );
        rolesToSelect.value = response.data.data;
    };

    return {
        roles, rolesToSelect, 
        getRoles, getRolesToSelect
    };
};