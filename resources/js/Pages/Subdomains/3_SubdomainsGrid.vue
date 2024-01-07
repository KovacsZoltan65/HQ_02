<script setup>
    import { ref, computed } from 'vue';

    const selectedRecords = ref([]);
    const selectAll = ref(false);
    const props = defineProps({
        data: Object,
        columns: Object,
        filterKey: String,
    });
    const emit = defineEmits(
        'editRecord', 
        'confirmDelete', 
        'toggleSelection', 
        'selectAllRecord'
    );
    //const sortKey = ref('id');
    //const sortOrders = ref(
        //props.columns.reduce((o, key) => ((o[key] = 1), o), {})
    //);
    //const filteredData = computed(() => {});

    //const sortBy = (key) => {
        //sortKey.value = key;
        //sortOrders.value[key] *= -1;
    //};

    //const capitalize = (str) => {
        //return str.charAt(0).toUpperCase() + str.slice(1);
    //};

    const toggleSelection = (record) => {

        console.log('SubdomainsGrid toggleSelection');

        const index = selectedRecords.value.indexOf(record.id);
        //console.log('index', index);
        if( index === -1 ) {
            selectedRecords.value.push(record.id);
        } else {
            selectAll.value = false;
            selectedRecords.value.splice(index, 1);
        }

        console.log('selectAll.value', selectAll.value);

        emit('toggleSelection', record);
    };
    // Összes rekord kijelölése
    const selectAllRecord = () => {
        
        console.log('SubdomainsGrid selectAllRecord');

        if( selectAll.value ){
            selectedRecords.value = props.data.map((record) => record.id);
        }else{
            selectedRecords.value = [];
        }

        console.log('selectedRecords.value', selectedRecords.value);

        emit('SelectAllRecord', selectAll.value);
    };
</script>

<template>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>
                    <!-- checkbox a fejlécben -->
                    <input type="checkbox" 
                           v-model="selectAll"
                           @change="selectAllRecord()"/>
                </th>
                <th v-for="(value, key) in columns">
                    {{ $t(key) }}
                </th>
                <th>{{ $t('actions') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="dat in data">
                <td>
                    <!-- checkbox a sor elején -->
                    <input type="checkbox" 
                           :checked="selectAll" 
                           @change="toggleSelection(dat)"/>
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