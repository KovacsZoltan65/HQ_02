import { ref } from "vue";
import axios from "axios";

export default function useSubdomains() {

    const subdomains = ref([]);

    const subdomainsToTable = ref([]);

    const subdomainsToSelect = ref([]);

    const subdomain = ref({});
    const password = ref({});

    const getSubdomains = async () => {
        await axios.get(
            route('getAllSubdomains')
        ).then(
            res => { subdomains.value = res.data.data; }
        ).catch(
            error => console.error('Error getSubdomains:', error)
        );
    };

    /**
     * Async function to retrieve subdomains and update the table.
     *
     * @param {Object} conf - Configuration object for the request
     * @return {Promise} A Promise that resolves with the subdomains data
     */
    const getSubdomainsToTable = async (conf) => {
        await axios.get(
            route('getSubdomainsToTable', conf)
        ).then (
            res => { subdomainsToTable.value = res.data.data; }
        ).catch (
            error => console.error('Error getSubdomainsToTable:', error)
        );
    };


    /**
     * Get the subdomains to populate the select input for creating/editing subdomains
     *
     * @return {Promise} A Promise that resolves with the subdomains data
     */
    const getSubdomainsToSelect = async () => {
        await axios.get(
            route('getSubdomainsToSelect')
        ).then(res => { subdomainsToSelect.value = res.data.data; }
        ).catch(
            error => console.error('getSubdomainsToSelect', error)
        );
    };


    /**
     * Get a subdomain by its ID
     *
     * @param {Number} id - The ID of the subdomain
     *
     * @return {Promise} A promise that resolves with the subdomain data
     */
    const getSubdomainById = async (id) => {
        await axios.get(
            route('getSubdomainById'), id
        ).then(
            res => { subdomain.value = res.data.data; }
        ).catch(error => console.error('Error getSubdomainById:', error));
    };


    /**
     * Create a new subdomain
     *
     * @param {Object} record - The record to create
     *
     * @return {Promise} A promise that resolves with the server's response
     */
    const subdomainCreate = async (record) => {
        await axios.post(
            route('subdomains'), record
        ).then(
            res => { response.value = res.data.data; }
        ).catch(error => console.error('Error subdomainCreate:', error));
    };

    const subdomainUpdate = async (id, record) => {
        await axios.put(
            route('subdomains_update', id), record
        ).then(
            res => { response.value = res.data.data; }
        ).catch(error => console.error('Error subdomainUpdate:', error));
    };

    const subdomainDelete = async (id) => {
        await axios.delete(
            route('subdomains_delete', id)
        ).then(
            res => { response.value = res.data.data; }
        ).catch(error => console.error('Error subdomainDelete:', error));
    };


    const subdomainBulkDelete = async (ids) => {
        await axios.delete(
            route('subdomains_bulkDelete'), { data: { ids } }
        ).then(
            res => { response.value = res.data.data; }
        ).catch(error => console.error('Error subdomainBulkDelete:', error));
    };

    const subdomainRestore = async (id) => {
        await axios.post(
            route('subdomains_restore'), {id}
        ).then(
            res => { response.value = res.data.data; }
        ).catch(error => console.error('Error subdomainRestore:', error));
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