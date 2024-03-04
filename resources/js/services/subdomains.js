import { ref } from "vue";
import axios from "axios";

export default function useSubdomains() {

    const subdomains = ref([]);

    const subdomainsToTable = ref([]);

    const subdomainsToSelect = ref([]);

    const subdomain = ref({});
    const password = ref({});

    const getSubdomains = async () => {
        try {
            let response = await axios.get( route('getAllSubdomains') );
            subdomains.value = response.data.data;
        } catch (error) {
            console.error('Error getSubdomains:', error);
        }
    };

    const getSubdomainsToTable = async (conf) => {
        try {
            let response = await axios.get(route('getSubdomainsToTable', conf));
            
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

        try {
            let response = await axios.get(route('getSubdomainsToSelect'));
            subdomainsToSelect.value = response.data.data;    
        } catch(error) {
            console.error('getSubdomainsToSelect', error);
        }
    };

    const getSubdomainById = async (id) => {
        try {
            let response = await axios.get( route('getSubdomainById'), id );
            subdomain.value = response.data.data;
        } catch(error) {
            console.error('getSubdomainById', error);
        }
    };

    const subdomainCreate = (record) => {
        try {
            let response = axios.post(route('permissions'), record);
            return response;
        } catch(error) {
            console.error('subdomainCreate', error);
        }
    };

    const subdomainUpdate = (id, record) => {
        try {
            let response = axios.put(route('subdomains_update', id), record);
            return response;
        } catch(error) {
            console.error('subdomainUpdate', error);
        }
    };

    const subdomainDelete = (id) => {
        try {
            let response = axios.delete(route('subdomains_delete', id));
            return response;
        } catch(error) {
            console.error('subdomainDelete', error);
        }
    };


    const subdomainBulkDelete = (ids) => {

        try {
            let response = axios.delete(route('permissions_bulkDelete'), {data: {ids} });
            return response;
        } catch(error) {
            console.error('subdomainBulkDelete', error);
        }
    };

    const subdomainRestore = (id) => {
        try {
            let response = axios.post(route('subdomains_restore'), {id});
            return response;
        } catch(error) {
            console.error('subdomainRestore', error);
        }
    };

    const genPassword = async (length) => {
        try {
            password.value = await axios.post(route('genPassword'), {
                minLength: 5, maxLength: 10
            });
        } catch(error) {
            console.error('genPassword', error);
        }
    };

    return {
        subdomains, subdomainsToSelect, subdomainsToTable, subdomain, password,
        getSubdomains, getSubdomainsToTable, getSubdomainsToSelect, getSubdomainById,
        subdomainCreate, subdomainUpdate, genPassword,
        subdomainDelete, subdomainBulkDelete, subdomainRestore
    };
}