<script setup>
    import { reactive, onMounted, ref } from 'vue';
    import axios from 'axios';
    import { Head, Link } from '@inertiajs/vue3';
    import MainLayout from '@/Layouts/MainLayout.vue';
    import VPagination from '@hennge/vue3-pagination';
    import '@hennge/vue3-pagination/dist/vue3-pagination.css';
    
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
        searchQuesy: '',

        columns: {
                   id: { label: '#', is_visible: true, is_sortable: true, is_filterable: true },
            subdomain: { label: 'subdomain', is_visible: true, is_sortable: true, is_filterable: true },
                  url: { label: 'url', is_visible: true, is_sortable: true, is_filterable: true },
                 name: { label: 'name', is_visible: true, is_sortable: true, is_filterable: true },
        },

    });

    // =====================
    // ÚJ REKORD
    // =====================
    function newRecord(){
        return {
            id: 0,
            subdomain: '', 
            url: '', 
            name: '',
            db_host: '', 
            db_port: '', 
            db_name: '', 
            db_user: '', 
            db_password: '',
            notification: '', 
            state_id: '', 
            is_mirror: '', 
            sso: '',
            access_control_system: '', 
            last_export: ''
        };
    };

    const newRecord_init = () => {
        //console.log('newRecord_init');
        cancelEdit();
        state.isEdit = false;
        openEditModal();
    };

    const createRecord = (record) => {
        errors.value = '';
        closeEditModal();
    };

    // =====================
    // SZERKESZTÉS
    // =====================
    const editRecord = (record) => {
        console.log('editRecord', record);
        state.editingRecord = record;
        state.isEdit = true;

        openEditModal();
    };
    // Szerkesztett adatok mentése
    const updateRecord = () => {
        console.log('updateRecord', state.editingRecord);
    }
    // Szerkesztés megszakítása
    const cancelEdit = () => {
        console.log('cancelEdit');
        state.editingRecord = newRecord();
    };

    // =====================
    // REKORD(OK) TÖRLÉSE
    // =====================
    // Törlés előkészítése
    const deleteRecord_init = (record) => {
        console.log('deleteRecord_init', record);
        state.deletingRecord = record;
    }
    // Rekord törlése
    const deleteRecord = () => {};
    // Rekordok csoportos törlése
    const bulkDelete = () => {
        console.log('bulkDelete');
    };

    // =====================
    // KIJELÖLÉS
    // =====================
    const toggleSelection = (record) => {
        console.log('toggleSelection', record);
    };
    //
    const selectAllRecord = () => {
        console.log('selectAllRecord');
    };


    // =====================
    // ADATLEKÉRÉS
    // =====================
    const getRecords = (page = state.pagination.current_page) => {
        axios.get(route('getSubdomains', {
            filters: state.filter,
            config: {
                per_page: state.pagination.per_page,
            },
            page
        }))
        .then((response) => {
            state.Records = response.data.subdomains.data;
            selectedRecords.value = [];
            selectAll
        })
        .catch((error) => {});
    };
    //
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
        console.log('openEditModal');
        $('#editModal').modal('show');
    };
    function closeEditModal() {
        cancelEdit();

        console.log('closeEditModal');
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
    <Head :title="$t('subdomains')" />

    <MainLayout>
        <!-- CONTENT HEADER -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">

                    <!-- PAGE TITLE -->
                    <div class="col-sm-6">
                        <h1 class="m-0">{{ $t('subdomains') }}</h1>
                    </div>

                    <!-- BREADCRUMB -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <Link :href="route('dashboard')">{{ $t('home') }}</Link>
                            </li>
                            <li class="breadcrumb-item active">{{ $t('subdomains') }}</li>
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
                        <!-- ADD NEW RECORD -->
                        <button type="button" 
                                class="mb-2 btn btn-primary" 
                                @click="newRecord_init()">
                            <i class="fa fa-plus-circle mr-1"></i>
                            {{ $t('subdomains_new') }}
                        </button>

                        <!-- BULK DELETE -->
                        <div v-if="selectedRecords.length > 0">
                            <button type="button" 
                                    @click="bulkDelete" 
                                    class="ml-2 mb-2 btn btn-danger">
                                <i class="fa fa-trash mr-1"></i>
                                {{ $t('delete_selected') }}
                            </button>
                            <span class="ml-2">Selected {{ selectedRecords.length }} subdomains</span>
                        </div>

                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        
                        <div class="card">

                            <!-- CARD HEADER -->
                            <div class="card-header">
                                <h5 class="card-title">{{ $t('subdomains') }}</h5>
                                <div class="card-tools">
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

                            <!-- CARD BODY -->
                            <div class="card-body">
                                <p class="card-text">
                                    {{ $t('subdomains_card_text') }}
                                </p>

                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="px-6 py-3">
                                                <input type="checkbox" 
                                                       v-model="selectAll" 
                                                       @change="selectAllRecord" />
                                            </th>
                                            
                                            <th scope="col" 
                                                class="px-6 py-3" 
                                                v-show="state.columns.id.is_visible">
                                                {{ $t(state.columns.id.label) }}
                                            </th>

                                            <th scope="col" 
                                                class="px-6 py-3" 
                                                v-show="state.columns.subdomain.is_visible">
                                                {{ $t(state.columns.subdomain.label) }}
                                            </th>

                                            <th scope="col" 
                                                class="px-6 py-3" 
                                                v-show="state.columns.url.is_visible">
                                                {{ $t(state.columns.url.label) }}
                                            </th>

                                            <th scope="col" 
                                                class="px-6 py-3" 
                                                v-show="state.columns.name.is_visible">
                                                {{ $t(state.columns.name.label) }}
                                            </th>

                                            <th>{{ $t('actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody v-for="record in state.Records">
                                        <tr>
                                            <td>
                                                <input type="checkbox" 
                                                       :checked="selectAll"
                                                       @change="toggleSelection(record)"/>
                                            </td>
                                            <td>{{ record.id }}</td>
                                            <td>{{ record.subdomain }}</td>
                                            <td>{{ record.url }}</td>
                                            <td>{{ record.name }}</td>
                                            <td>
                                                <!-- EDIT -->
                                                <button class="btn btn-primary" 
                                                        type="button" 
                                                        @click="editRecord(record)">
                                                    <i class="fa fa-edit"></i>
                                                </button>

                                                <!-- DELETE -->
                                                <button class="btn btn-danger ml-2">
                                                     <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- CARD FOOTER -->
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
                        <div class="row">
                            <!-- NAME -->
                            <div class="col form-group">
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

                            <!-- SUBDOMAIN -->
                            <div class="col form-group">
                                <label>{{ $t('subdomain') }}</label>
                                
                                <input name="subdomain" type="text" 
                                    class="form-control" 
                                    :class="errors?.subdomain ? 'is-invalid' : 'is-valid'"
                                    aria-describedby="subdomainHelp" 
                                    :placeholder="$t('subdomain_placeholder')"
                                    v-model="state.editingRecord.subdomain"/>
                                <div class="invalid-feedback" 
                                    v-if="errors?.subdomain">{{ errors.subdomain[0] }}</div>

                            </div>
                        </div>
                        <div class="row">
                            <!-- URL -->
                            <div class="col form-group">
                                <label>{{ $t('url') }}</label>
                                
                                <input name="url" type="text" 
                                    class="form-control" 
                                    :class="errors?.url ? 'is-invalid' : 'is-valid'"
                                    aria-describedby="urlHelp" 
                                    :placeholder="$t('url_placeholder')"
                                    v-model="state.editingRecord.url"/>
                                <div class="invalid-feedback" 
                                    v-if="errors?.url">{{ errors.url[0] }}</div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" 
                                class="btn btn-secondary"
                                @click="closeEditModal()">{{ $t('cancel') }}</button>
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
                    <div class="modal-header"></div>
                    <div class="modal-body"></div>
                    <div class="modal-footer"></div>
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