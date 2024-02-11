import { ref } from "vue";
import axios from "axios";

export default function usePermissions(){

    const permissions = ref({});
    const permissionsToSelect = ref({});

    const getPermissions = async () => {
        //console.log('getPermissions');
        let response = await axios.get( route('getPermissions') );
        //console.log(response.data.permissions);
        permissions.value = response.data.data;
    };

    const getPermissionsToSelect = async () => {
        //console.log('getPermissionsToSelect');

        let response = await axios.get( route('getPermissionsToSelect') );
        //console.log(response.data.data);
        permissionsToSelect.value = response.data.data;
    };

    return {
        permissions, permissionsToSelect, 
        getPermissions, getPermissionsToSelect
    };
}