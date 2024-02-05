<script setup>
    import { reactive, onMounted, ref, watch } from 'vue';
    import axios from 'axios';
    import { Head, Link } from '@inertiajs/vue3';
    import MainLayout from '@/Layouts/MainLayout.vue';
    import VPagination from '@hennge/vue3-pagination';
    import '@hennge/vue3-pagination/dist/vue3-pagination.css';
    import { trans } from 'laravel-vue-i18n';
    import Swal from 'sweetalert2';
    import 'sweetalert2/dist/sweetalert2.min.css';

    const local_storage_column_key = 'ln_roles_grid_columns';

    const props = defineProps({
        can: {
            type: Object,
            default: () => ({})
        }
    });

    const errors = ref({});
    const selectedRecords = ref([]);
    const selectAll = ref(false);

    // Általános alert
    const alerta = Swal.mixin({
        buttonsStyling: true
    });

    // Törlés alert
    const delete_alert = Swal.mixin({
        buttonsStyling: true,
        title: trans('delete_confirmation'),
        icon: 'question',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
    });

    const newRecord = () => {
        return {
            id: 0,
            name: 'role_01',
            guard_name: 'web',
        };
    };

    const state = reactive({
        Records: [],
        Record: newRecord(),

        editingRecord: newRecord(),
        deletingRecord: newRecord(),

        showSettingsModal: false,
        showEditModal: false,
        showDeleteModal: false,

        filter:{
            tags: [],
            search: null,
            column: null,
            direction: null,
        },
        pagination: {
            current_page: 1,
            total_number_of_pages: 0,
            per_page: 10,
            range: 5,
        },
        searchQuery: '',

        columns: {
            id: { label: '#', is_visible: true, is_sortable: true, is_filterable: true },
            name: { label: 'name', is_visible: true, is_sortable: true, is_filterable: true },
            guard_name: { label: 'guard_name', is_visible: true, is_sortable: true, is_filterable: true },
        },
    });

    watch(state.columns, (new_value, old_value) => {
        localStorage.setItem(local_storage_column_key, JSON.stringify(new_value));
    });

    // =====================
    // ÚJ REKORD
    // =====================
    const newRecord_init = () => {
        cancelEdit();
        state.isEdit = false;

        openEditModal();
    };
    
    const createRecord = () => {
        errors.value = '';

        axios.post(route('permissions'), state.Record)
        .then(resource => {
            //console.log('createRecord', resource);
            state.Records.push(resource.data.permission);

            alerta.fire(trans('permissions_created'), '', 'info');
        })
        .catch(error => {
            console.log('createRecord error', error); 
        });
    };

    // =====================
    // REKORD SZERKESZTÉSE
    // =====================
    const editRecord = (record) => {
        state.editingRecord = record;
        //console.log('state.editingRecord', state.editingRecord);
        state.isEdit = true;

        openEditModal();
    };
    const updateRecord = () => {
        errors.value = '';

        axios.put(route('permissions_update', {permission: state.editingRecord.id}), {
            id: state.editingRecord.id,
            name: state.editingRecord.name,
            guard_name: state.editingRecord.guard_name,
        })
        .then(response => {
            for(let i = 0; i < state.Records.length; i++){
                if( state.Records[i].id == state.editingRecord.id ){
                    state.Records[i] = response.data;
                }
            }

            cancelEdit();

            closeEditModal();

            alerta.fire(trans('permissions_updated'), '', 'info');
        })
        .catch(error => { console.log('updateRecord error', error); });
    };

    const cancelEdit = () => {
        state.editingRecord = newRecord();
        state.isEdit = false;
    };

    // =====================
    // REKORD TÖRLÉSE
    // =====================
    // törlés előkészítése
    const confirmDelete = (record) => {
        /*
        state.deletingRecord = record;
        
        openDeleteModal();
        */
        delete_alert.fire({
            text: trans('permissions_delete_confirmation', {name: record.name}),
            confirmButtonText: trans('yes'),
            showDenyButton: false,
            denyButtonText: trans('deny'),
            showCancelButton: true,
            cancelButtonText: trans('cancel'),
        }).then(result => {
            //
            if( result.isConfirmed ){
                state.deletingRecord = record;
                deleteRecord();
            }else if( result.isDenied ){
                state.deletingRecord = newRecord();
                alerta.fire(trans('deletion_aborted'), '', 'info');
            }
        });
    };

    // rekord törlése
    const deleteRecord = () => {
        axios.delete(`/permissions/${state.deletingRecord.id}`)
        .then(response => {
            state.Records = state.Records.filter(item => item.id !== state.deletingRecord.id);
        })
        .catch(error => {
            console.log('deleteRecord error', error);
        });
    };

    const bulkDelete = () => {
        axios.delete(
            route('permissions_bulkDelete', 
                {data: {
                    ids: selectedRecords.value
                }}
            )
        ).then(response => {
            state.Records = state.Records.filter(s => !selectedRecords.value.includes(s.id));
            selectedRecords.value = [];
            selectAll.value = false;

        })
        .catch(error => {
            console.log('bulkDelete error', error);
        });
    };

    const cancelDelete = () => {
        state.deletingRecord = newRecord();
        //closeDeleteModal();
    };

    // =====================
    // KIJELÖLÉS
    // =====================

    const selectAllRecord = () => {
        if( selectAll.value ){
            selectedRecords.value = state.Records.map(record => record.id);
        }else{
            selectedRecords.value = [];
        }
    };

    const toggleSelection = (id) => {
        const index = selectedRecords.value.indexOf(id);

        if( index === -1 ){
            selectedRecords.value.push(id);
        }else{
            selectedRecords.value.splice(index, 1);
        }
    };
    
    
    // =====================
    // ADATLEKÉRÉS
    // =====================
    const getRecords = async (page = state.pagination.current_page) => {
        axios.get(route('getPermissions', {
            filters: state.filter, 
            config: {
                per_page: state.pagination.per_page
            }, page
        }))
        .then(response => {
            state.Records = response.data.permissions.data;
            selectedRecords.value = [];
            selectAll.value = false;

            state.pagination.current_page = response.data.permissions.current_page;
            state.pagination.total_number_of_pages = response.data.permissions.last_page;
        })
        .catch(error => {
            console.log('getRecords error', error);
        });
    };

    onMounted(async () => {
        
        let columns = localStorage.getItem(local_storage_column_key);
        if( columns ){
            columns = JSON.parse(columns);
            for(const column_name in columns){
                state.columns[column_name] = columns[column_name];
            }
        }

        getRecords();
    });

    const settings_init = () => { openSettingsModal(); };

    // =====================
    // MODAL KEZELÉS
    // =====================
    const openSettingsModal = () => { $('#settingsModal').modal('show'); };
    const closeSettingsModal = () => { $('#settingsModal').modal('hide'); };

    const openEditModal = () => { $('#editModal').modal('show'); };
    const closeEditModal = () => {
        cancelEdit();
        $('#editModal').modal('hide');
    };

    //const openDeleteModal = () => { $('#deleteModal').modal('show'); };
    //const closeDeleteModal = () => { $('#deleteModal').modal('hide'); };

</script>

<template>
    <Head :title="$t('permissions')"/>
    <MainLayout>
        <!-- CONTENT HEADER -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">

                    <!-- PAGE TITLE -->
                    <div class="col-sm-6">
                        <h1 class="m-0">{{ $t('permissions') }}</h1>
                    </div>

                    <!-- BREADCRUMB -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <Link :href="route('dashboard')">{{ $t('home') }}</Link>
                            </li>
                            <li class="breadcrumb-item active">{{ $t('permissions') }}</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>

        <!-- CONTENT -->
        <div class="content">
            <div class="container-fluid">

                <div class="d-flex justify-content-between">
                    <div class="d-flex">
                        <div class="bd-example">

                            <!-- ADD NEW RECORD -->
                            <button type="button"
                                    class="btn btn-primary"
                                    :title="$t('permissions_new')"
                                    @click="newRecord_init()">
                                <i class="fa fa-plus-circle mr-1"></i>
                                {{ $t('permissions_new') }}
                            </button>

                            <!-- REFRESH -->
                            <button type="button"
                                    class="btn btn-success"
                                    :title="$t('refresh')"
                                    @click="getRecords()">
                                <i class="fas fa-sync"></i>
                            </button>

                            <!-- SETTUNGS -->
                            <button typw="button" 
                                    class="btn btn-primary" @click="settings_init()"
                            >{{ $t('settings') }}</button>

                            <!-- BULK DELETE -->
                            <div v-if="selectedRecords.length > 0">
                                <button type="button"
                                        class="btn btn-danger"
                                        @click="bulkDelete()">
                                    <i class="fa fa-trash mr-1"></i>
                                    {{ $t('delete_selected') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">

                            <div class="card-header">
                                <h5 class="card-title">{{ $t('permissions') }}</h5>

                                <div class="card-tools">
                                    <!-- KERESÉS -->
                                    <div class="input-group input-group-sm">
                                        <input type="text" 
                                               class="form-control" 
                                               v-model="state.searchQuery"
                                               :placeholder="$t('search')" />
                                        <div class="input-group-append">
                                            <div class="btn btn-primary">
                                                <i class="fas fa-search"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <!-- TÁBLA -->
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <!-- checkbox a fejlécben -->
                                            <th>
                                                <input type="checkbox" 
                                                       v-model="selectAll"
                                                       @change="selectAllRecord()"/>
                                            </th>
                                            
                                            <!-- HEAD -->
                                            <th v-for="(key, value) in state.columns" 
                                                :key="key" 
                                                v-show="key.is_visible">
                                                {{ $t(value) }}
                                            </th>

                                            <!-- ACTIONS -->
                                            <th>{{ $t('actions') }}</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="Record in state.Records" 
                                            :key="Record.id">
                                            <!-- checkbox a sor elején -->
                                            <td>
                                                <input type="checkbox" 
                                                       :checked="selectAll"
                                                       @change="toggleSelection(Record.id)"/>
                                            </td>

                                            <!-- FIELDS -->
                                            <td v-for="(key, value) in state.columns" 
                                                :key="key" 
                                                v-show="key.is_visible">{{ Record[value] }}</td>
                                            
                                            <!-- ACTIONS -->
                                            <td>
                                                <div class="bd-example">
                                                    <button type="button" 
                                                            class="btn btn-primary"
                                                            @click="editRecord(Record)">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                    <button type="button" 
                                                            class="btn btn-danger"
                                                            @click="confirmDelete(Record)">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="card-footer">
                                <v-pagination v-model="state.pagination.current_page" 
                                              :pages="state.pagination.total_number_of_pages"
                                              :range-size="state.pagination.range"
                                              active-color="#DCEDFF"
                                              @update:modelValue="getRecords"/>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- edit modal -->
        <div class="modal fade" id="editModal" 
             data-backdrop="static" 
             tabindex="-1" permission="dialog" 
             aria-labelledby="staticBackdropLabel" 
             aria-hidden="true"
             >
            <div class="modal-dialog modal-dialog-scrollable modal-lg" 
                 permission="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">
                            <span v-if="state.isEdit">{{ $t('subdomains_edit') }}</span>
                            <span v-else>{{ $t('subdomains_new') }}</span>
                        </h5>
                        <button type="button" class="close" 
                                data-dismiss="modal" 
                                aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div>
                            <!-- NAME -->
                            <div class="form-group">
                                <label>{{ $t('name') }}</label>
                                
                                <input name="name" type="text" 
                                    class="form-control" 
                                    :class="errors?.name ? 'is-invalid' : 'is-valid'"
                                    aria-describedby="nameHelp" 
                                    :placeholder="$t('name_placeholder')"
                                    v-model="state.editingRecord.name"/>
                                <div class="invalid-feedback" 
                                    v-if="errors?.name">{{ errors.name[0] }}</div>

                            </div>

                            <!-- GUARD_NAME -->
                            <div class="form-group">
                                <label>{{ $t('guard_name') }}</label>
                                
                                <input name="guard_name" type="text" 
                                    class="form-control" 
                                    :class="errors?.guard_name ? 'is-invalid' : 'is-valid'"
                                    aria-describedby="subdomainHelp" 
                                    :placeholder="$t('subdomain_placeholder')"
                                    v-model="state.editingRecord.guard_name"/>
                                <div class="invalid-feedback" 
                                    v-if="errors?.guard_name">{{ errors.guard_name[0] }}</div>

                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        
                        <!-- CANCEL -->
                        <button type="button" 
                                class="btn btn-secondary"
                                @click="closeEditModal()">
                            {{ $t('cancel') }}
                        </button>
                        
                        <!-- SAVE -->
                        <button type="submit" 
                                @click="state.isEdit? updateRecord() : createRecord()"  
                                class="btn btn-primary"
                        >{{ state.isEdit ? $t('update') : $t('create') }}</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- delete modal -->
        <div class="modal fade" id="deleteModal" 
             data-backdrop="static" 
             tabindex="-1" permission="dialog" 
             aria-labelledby="staticBackdropLabel" 
             aria-hidden="true"
             :show="state.showDeleteModal">
            <div class="modal-dialog" permission="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">
                            <span>{{ $t('roles_delete') }}</span>
                        </h5>
                        <button type="button" class="close" 
                                data-dismiss="modal" 
                                aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <h5>{{ $t('roles_delete_confirmation') }}</h5>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" 
                                class="btn btn-secondary"
                                @click="cancelDelete()">{{ $t('cancel') }}</button>
                        <button type="submit" 
                                @click="deleteRecord()"  
                                class="btn btn-primary"
                        >{{ state.isEdit ? $t('update') : $t('delete') }}</button>
                    </div>

                </div>
            </div>
        </div>

        <!-- settings modal -->
        <div class="modal fade" id="settingsModal" 
             data-backdrop="static" 
             tabindex="-1" permission="dialog" 
             aria-labelledby="staticBackdropLabel" 
             aria-hidden="true"
             :show="state.showSettingsModal">
            <div class="modal-dialog" permission="document">
                <div class="modal-content">
                    
                    <div class="modal-header">{{ $t('settings') }}</div>

                    <div class="modal-body">
                        <div v-for="(config, column) in state.columns" 
                             :key="column" 
                             class="form-check">
                            <input v-model="config.is_visible" 
                                   class="form-check-input" 
                                   type="checkbox">
                            <label class="form-check-label">{{ $t(config.label) }}</label>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" 
                                class="btn btn-primary" 
                                @click="closeSettingsModal()"
                        >{{ $t('back') }}</button>
                    </div>
                </div>
            </div>
        </div>

    </MainLayout>
</template>