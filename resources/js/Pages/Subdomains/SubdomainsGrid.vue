<script setup>
    import { ref, computed } from 'vue';

    const props = defineProps({
        data: Object,
        columns: Object,
        filterKey: String,
    });
    const emit = defineEmits('editRecord', 'confirmDelete', 'toggleSelection');
    //const sortKey = ref('id');
    //const sortOrders = ref(
        //props.columns.reduce((o, key) => ((o[key] = 1), o), {})
    //);
    //const filteredData = computed(() => {});

    const sortBy = (key) => {
        //sortKey.value = key;
        //sortOrders.value[key] *= -1;
    };

    //const capitalize = (str) => {
        //return str.charAt(0).toUpperCase() + str.slice(1);
    //};
</script>

<template>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>
                    <input type="checkbox"/>
                </th>
                <th v-for="(value, key) in columns">
                    {{ key }}
                </th>
                <th>actions</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="dat in data">
                <td>
                    <input type="checkbox"/>
                </td>
                <td v-for="(value, key) in columns">{{ dat[key] }}</td>
                <td>
                    <div class="bd-example">
                        <button class="btn btn-primary" 
                                @click.prevent="$emit('editRecord', dat)">
                            <i class="fa fa-edit"></i>
                        </button>

                        <button class="btn btn-danger"
                                @click.prevent="$emit('confirmDelete', dat)">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</template>