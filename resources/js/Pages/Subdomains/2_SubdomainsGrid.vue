<script setup>
    import { ref, computed } from 'vue';

    const props = defineProps({
        data: Array,
        columns: Array,
        filterKey: String
    });
    const sortKey = ref('');
    const sortOrders = ref(
        props.columns.reduce((o, key) => ((o[key] = 1), o), {})
    );

    const filteredData = computed(() => {
        let { data, filterKey } = props
        if (filterKey) {
            filterKey = filterKey.toLowerCase();
            data = data.filter((row) => {
                return Object.keys(row).some((key) => {
                    return String(row[key]).toLowerCase().indexOf(filterKey) > -1
                });
            });
        }
        const key = sortKey.value;
        if (key) {
            const order = sortOrders.value[key]
            data = data.slice().sort((a, b) => {
                a = a[key]
                b = b[key]
                return (a === b ? 0 : a > b ? 1 : -1) * order
            });
        }
        return data;
    });

    function sortBy(key) {
        sortKey.value = key;
        sortOrders.value[key] *= -1;
    };

    function capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    };

</script>

<template>
    <table class="table table-bordered" 
           v-if="filteredData.length">
        <thead>
            <tr>
                <th v-for="key in columns" 
                    @click="sortBy(key)"  
                    :class="{ active: sortKey == key }">
                    {{ key }}
                    <span class="arrow" 
                          :class="sortOrders[key] > 0 ? 'asc' : 'dsc'"
                    ></span>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="entry in filteredData">
                <td v-for="key in columns">
                    {{ entry[key] }}
                </td>
            </tr>
        </tbody>
    </table>
</template>

<style>
    th {
        background-color: #42b983;
        color: rgba(255, 255, 255, 0.66);
        cursor: pointer;
        user-select: none;
    }
    .arrow {
        display: inline-block;
        vertical-align: middle;
        width: 0;
        height: 0;
        margin-left: 5px;
        opacity: 0.66;
    }
    .arrow.asc {
        border-left: 4px solid transparent;
        border-right: 4px solid transparent;
        border-bottom: 4px solid #fff;
    }
    .arrow.dsc {
        border-left: 4px solid transparent;
        border-right: 4px solid transparent;
        border-top: 4px solid #fff;
    }
</style>