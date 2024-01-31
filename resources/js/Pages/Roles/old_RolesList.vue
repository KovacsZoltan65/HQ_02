<script setup>
    import { reactive, onMounted, ref } from 'vue';
    import axios from 'axios';
    import { Head, Link } from '@inertiajs/vue3';

    import MainLayout from '@/Layouts/MainLayout.vue';
    import VPagination from '@hennge/vue3-pagination';
    import '@hennge/vue3-pagination/dist/vue3-pagination.css';

    import { trans } from 'laravel-vue-i18n';

    import { useToastr } from '@/toastr';
    const toastr = useToastr();

    const props = defineProps({
        can: {
            type: Object,
            default: () => ({})
        }
    });

    const errors = ref({});
    const selectedRecords = ref([]);
    const selectAll = ref(false);

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

    // =====================
    // ÚJ REKORD
    // =====================
    function newRecord() {
        return {
            id: 0,
            name: 'role_01',
            guard_name: 'web',
        };
    };
    // Új rekord előkészítése
    const newRecord_init = () => {
        cancelEdit();
        state.isEdit = false;

        openEditModal();
    };
    // Új rekord mentése
    const createRecord = () => {
        console.log('createRecord', state.Record);
        errors.value = '';

        axios.post(route('roles'), state.Record)
        .then(resource => {
            console.log(resource);
            closeEditModal();
        })
        .catch(error => {
            console.log(error);
        });
    };

    // =====================
    // SZERKESZTÉS
    // =====================
    const editRecord = (record) => {
        state.editingRecord = record;
        state.isEdit = true;

        openEditModal();
    };
    // Szerkesztett adatok mentése
    const updateRecord = () => {
        errors.value = '';

        axios.put(route('roles_update', {role: state.editingRecord.id}), {
            id: state.editingRecord.id,
            name: state.editingRecord.name,
            guard_name: state.editingRecord.guard_name,
        })
        .then(response => {
            cancelEdit();
            closeEditModal();

            toastr.success(trans('roles_updated'));
            //toastr.success('ASDASDAS');
        })
        .catch(error => {
            console.log('error', error);
            //errors.value = error.response.data.errors;
        });
    };
    // Szerkesztés megszakítása
    const cancelEdit = () => {
        state.editRecord = newRecord();
        state.isEdit = false;
    };

    // =====================
    // REKORD(OK) TÖRLÉSE
    // =====================
    // Törlés megerősítése
    const confirmDelete = (record) => {
        state.deletingRecord = record;
        openDeleteModal();
    };
    // Rekord törlése
    const deleteRecord = () => {
        axios.delete(`/roles/${state.deletingRecord.id}`)
        .then(response => {
            closeDeleteModal();
            state.Records = state.Records.filter(record => record.id !== state.deletingRecord.id);

            toastr.success(trans('roles_deleted'));
        })
        .catch(error => console.log(error));
    };
    // Kijelölt rekordok törlése
    const bulkDelete = () => {
        //console.log('bulkDelete', selectedRecords.value);
        axios.delete(route('roles_bulkDelete', {data: {}}))
        .then(resource => {
            state.Records = state.Records.filter(s => !selectedRecords.value.includes(s.id) );
            selectedRecords.value = [];
            selectAll.value = false;
            toastr.success(trans('roles_bulk_deleted'));
        })
        .catch(error => {
            //
        });
    };
    // Törlés megszakítása
    const cancelDelete = () => {
        state.deletingRecord = newRecord();
        closeDeleteModal();
    };

    // =====================
    // ADATLEKÉRÉS
    // =====================
    const getRecords = async (page = state.pagination.current_page) => {
        axios.get(route('getRoles', {
            filters: state.filter,
            config: {
                per_page: state.pagination.per_page,
            }, page
        })).then(response => {
            state.Records = response.data.roles.data;
            //console.log(state.Records);
            selectedRecords.value = [];
            selectAll.value = false;
        }).catch(error => {
            console.log('getRecords error', error);
        });
    };

    // =====================
    // KIJELÖLÉS
    // =====================
    // Összes rekord kijelölése
    const selectAllRecord = () => {
        if( selectAll.value ){
            selectedRecords.value = state.Records.map(record => record.id);
        }else{
            selectedRecords.value = [];
        }
    };
    // Rekord jelölés váltása
    const toggleSelection = (id) => {

        const index = selectedRecords.value.indexOf(id);

        if( index === -1 ) {
            selectedRecords.value.push(id);
        } else {
            selectedRecords.value.splice(index, 1);
        }
        //console.log('state.Records.length', state.Records.length);
        //console.log('selectedRecords.value.length', selectedRecords.value.length);
        //console.log( (state.Records.length === selectedRecords.value.length) );
        //selectAll.value = (state.Records.length === selectedRecords.value.length);
    };

    onMounted(() => {
        getRecords();
    });

    // =====================
    // MODAL KEZELÉS
    // =====================
    // beállítások
    function openSettingsModal() {
        $('#settingsModal').modal('show');
    };
    function closeSettingsModal() {
        $('#settingsModal').modal('hide');
    };
    // szerkesztés
    function openEditModal() {
        $('#editModal').modal('show');
    };
    function closeEditModal() {
        cancelEdit();

        $('#editModal').modal('hide');
    };
    // törlés
    function openDeleteModal() {
        $('#deleteModal').modal('show');
    };
    function closeDeleteModal() {
        $('#deleteModal').modal('hide');
    };
</script>

<template>
    <Head :title="$t('roles')"/>

    <MainLayout>
        <!-- CONTENT HEADER -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">

                    <!-- PAGE TITLE -->
                    <div class="col-sm-6">
                        <h1 class="m-0">{{ $t('roles') }}</h1>
                    </div>

                    <!-- BREADCRUMB -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <Link :href="route('dashboard')">{{ $t('home') }}</Link>
                            </li>
                            <li class="breadcrumb-item active">{{ $t('roles') }}</li>
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
                                    :title="$t('subdomains_new')"
                                    @click="newRecord_init()">
                                <i class="fa fa-plus-circle mr-1"></i>
                                {{ $t('subdomains_new') }}
                            </button>

                            <!-- REFRESH -->
                            <button type="button"
                                    class="btn btn-success"
                                    :title="$t('refresh')"
                                    @click="getRecords()">
                                <i class="fas fa-sync"></i>
                            </button>

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
                    <div class="col-lg-12">

                        <div class="card">
                            
                            <div class="card-header">
                                <h5 class="card-title">{{ $t('roles') }}</h5>
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
                                <!-- TÁBLÁZAT -->
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>
                                                <!-- checkbox a fejlécben -->
                                                <input type="checkbox" 
                                                       v-model="selectAll"
                                                       @change="selectAllRecord()"/>
                                            </th>

                                            <th v-for="(key, value) in state.columns">
                                                {{ $t(value) }}
                                            </th>

                                            <th>{{ $t('actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="Record in state.Records">
                                            <td>
                                                <!-- checkbox a sor elején -->
                                                <input type="checkbox" 
                                                       :checked="selectAll"
                                                       @change="toggleSelection(Record.id)"/>
                                            </td>
                                            
                                            <td v-for="(key, value) in state.columns">
                                                {{ Record[value] }}
                                            </td>
                                            
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
                            <!-- /táblázat -->

                            <!-- footer -->
                            <div class="card-footer">
                                <v-pagination v-model="state.pagination.current_page" 
                                              :pages="state.pagination.total_number_of_pages"
                                              :range-size="state.pagination.range"
                                              active-color="#DCEDFF"
                                              @update:modelValue="getRecords"/>
                            </div>
                            <!-- /footer -->
                        </div>
                        <!-- /card -->
                    </div>
                </div>

            </div>

        </div>

        <!-- EDIT MODAL -->
        <div class="modal fade" id="editModal" 
             data-backdrop="static" 
             tabindex="-1" role="dialog" 
             aria-labelledby="staticBackdropLabel" 
             aria-hidden="true"
             :show="state.showEditModal">
            <div class="modal-dialog modal-dialog-scrollable modal-lg" 
                 role="document">
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

        <!-- DELETE MODAL -->
        <div class="modal fade" id="deleteModal" 
             data-backdrop="static" 
             tabindex="-1" role="dialog" 
             aria-labelledby="staticBackdropLabel" 
             aria-hidden="true"
             :show="state.showDeleteModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">
                            <span>{{ $t('subdomains_delete') }}</span>
                        </h5>
                        <button type="button" class="close" 
                                data-dismiss="modal" 
                                aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <h5>{{ $t('subdomains_delete_confirmation') }}</h5>
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

        <!-- SETTINGS MODAL -->
        <div class="modal fade" id="settingsModal" 
             data-backdrop="static" 
             tabindex="-1" role="dialog" 
             aria-labelledby="staticBackdropLabel" 
             aria-hidden="true"
             :show="state.showSettingsModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header"></div>
                    <div class="modal-body"></div>
                    <div class="modal-footer"></div>
                </div>
            </div>
        </div>

    </MainLayout>
</template>