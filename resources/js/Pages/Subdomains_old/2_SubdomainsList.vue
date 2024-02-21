<script setup>
    import { reactive, ref, onMounted } from 'vue';
    import SubdomainsGrid from './SubdomainsGrid.vue';
    import axios from 'axios';

    const searchQuery = ref('');

    const state = reactive({
        Records: [],
        Columns: ['id', 'subdomain', 'url', 'name']
        /*
        Columns: {
                   id: { label: '#', is_visible: true, is_sortable: true, is_filteravle: true },
            subdomain: { label: 'subdomain', is_visible: true, is_sortable: true, is_filteravle: true },
                  url: { label: 'url', is_visible: true, is_sortable: true, is_filteravle: true },
                 name: { label: 'name', is_visible: true, is_sortable: true, is_filteravle: true },
        },
        */
    });

    const getSubdomains = async () => {
        axios.get(route('getSubdomains2'))
        .then((response) => {
            console.log(response.data.subdomains);
            state.Records = response.data.subdomains;
        })
        .catch((error) => {
            console.log(error);
        });
    };

    onMounted(() => {
        getSubdomains();
    });
</script>

<template>
    <form id="search">
        Search <input type="text" 
                      v-model="searchQuery" />
    </form>

    <SubdomainsGrid :data="state.Records" 
                    :columns="state.Columns" 
                    :filter-key="searchQuery"
    ></SubdomainsGrid>
</template>