import { ref } from "vue";
import axios from "axios";

export default function useSubdomains() {

    const subdomains = ref([]);

    const subdomainsToTable = ref([]);

    const subdomainsToSelect = ref([]);

    const subdomain = ref({});
    const password = ref({});

    const getSubdomains = () => 
        subdomains.value = axios.get(route('getAllSubdomains'))
        .then(response => {response.data.data})
        .catch(error => {console.error('getSubdomains', error);});


    const getSubdomainsToTable = async (conf) => {
        try {
            /**
             * Make a GET request to the 'getPermissions' route
             */
            let response = await axios.get(route('getSubdomainsToTable', conf));
            //console.log('getSubdomainsToTable', response);
            /**
             * Update permissions.value with the response data
             */
            subdomainsToTable.value = response.data.data;
        } catch (error) {
            // Handle error
            console.error('getSubdomainsToTable error', error);
        }
    };

    /**
     * Retrieves subdomains for selection from the server and updates the subdomains to select with the retrieved data
     */
    const getSubdomainsToSelect = async () => {

        subdomainsToSelect.value = await axios.get(route('getSubdomainsToSelect'))
        .then(response => response.data.data)
        .catch(error => {
            // Handle error
            console.error('getSubdomainsToSelect', error);
        });
    };

    const getSubdomainById = async (id) => {
        subdomain.value = await axios.get(route('getSubdomainById'), id)
        .then(response => response.data.data)
        .catch(error => {
            console.error('getSubdomainById', error);
        });
    };

    const subdomainCreate = (record) => {
        axios.post(route('permissions'), record)
        .then(response => response.data)
        .catch(error => console.error('subdomainCreate', error));
    };


    const subdomainUpdate = (id, record) => {
        axios.put(route('permissions_update', id), record)
        .then(response => response.data)
        .error(error => console.error('subdomainUpdate', error));
    };

    const subdomainDelete = (id) => {
        axios.delete(route('permissions_delete', id))
        .then(({ data }) => data)
        .catch(error => console.error('subdomainDelete', error));
    };


    const subdomainBulkDelete = (ids) => {
        axios.delete(route('permissions_bulkDelete'), {data: {ids} })
        .then(response => response.data)
        .catch(error => console.error('subdomainBulkDelete', error));
    };

    const subdomainRestore = (id) => {
        axios.post(route('permission_restore'), {id})
        .then(response => response.data)
        .catch(error => console.error('subdomainRestore', error))
    };

    const genPassword = async (length) => {
        password.value = await axios.post(route('genPassword'), {
            minLength: 5,
            maxLength: 10
        })
        .then(resonse => response.data)
        .catch(error => console.error('genPassword', error));
    };

    return {
        subdomains, subdomainsToSelect, subdomainsToTable, subdomain, password,
        getSubdomains, getSubdomainsToTable, getSubdomainsToSelect, getSubdomainById,
        subdomainCreate, subdomainUpdate, genPassword,
        subdomainDelete, subdomainBulkDelete, subdomainRestore
    };
}