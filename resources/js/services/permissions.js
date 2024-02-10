import { ref } from "vue";

export default function usePermissions(){

    const permissions = ref({});

    //const getPermissionsToSelect = async () => {
    //    axios.get('/api/get_permissions_to_select')
    //    .then(response => {
    //        categories.value = response.data.data;
    //    });
    //};

    const getPermissions = async () => {
        axios.get('/api/permissions')
        .then(response => {
            console.log('getPermissions', response);
            permissions.value = response.data.data;
        });
    }

    return {permissions, getPermissions}
}