<script setup>
    import { reactive, onMounted, ref, watch } from 'vue';
    //import axios from 'axios';
    import { Head, Link } from '@inertiajs/vue3';
    import MainLayout from '@/Layouts/MainLayout.vue';
    import VPagination from '@hennge/vue3-pagination';
    import '@hennge/vue3-pagination/dist/vue3-pagination.css';
    import { trans } from 'laravel-vue-i18n';
    import Swal from 'sweetalert2';
    import 'sweetalert2/dist/sweetalert2.min.css';

    const local_storage_column_key = 'ln_subdomains_grid_columns';

    const props = defineProps({
        can: {
            type: Object,
            default: () => ({})
        }
    });

    const errors = ref({});
    const selectedRecords = ref([]);
    const selectAll = ref(false);

    /**
     * Creates and returns a new record object with default values.
     *
     * @return {Object} The new record object
     */
    function newRecord () {
        return {
            id: 0,
            subdomain: 'subdomain_01', 
            url: 'http://subdomain_01.com', 
            name: 'subdomain_01',
            db_host: 'localhost', 
            db_port: 4406, 
            db_name: 'ej2_subdomain_p', 
            db_user: 'ej2_subdomain_p', 
            db_password: 'password',
            notification: 1, 
            state_id: 1, 
            is_mirror: 0, 
            sso: 0,
            access_control_system: 0, 
            last_export: ''};
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
            subdomain: { label: 'subdomain', is_visible: true, is_sortable: true, is_filterable: true },
                  url: { label: 'url', is_visible: true, is_sortable: true, is_filterable: true },
                 name: { label: 'name', is_visible: true, is_sortable: true, is_filterable: true },
        },

        subdomain_states: [
            {id: 1,name: 'Aktív'},
            {id: 2,name: 'Felfüggesztve'},
            {id: 3,name: 'Leállítva(HQ)'},
            {id: 4,name: 'Leállítva(példány)'}
        ],
        access_control_systems: [
            {id: 0,name: 'Nincs'},
            {id: 1,name: 'WinAccess'},
            {id: 2,name: 'Enviromux'},
            {id: 3,name: 'Siport'},
            {id: 4,name: 'WinAccess WC'},
            {id: 5,name: 'GenerallyACS'}
        ],
    });

    watch(state.columns, (new_value, old_value) => {
        localStorage.setItem(local_storage_column_key, JSON.stringify(new_value));
    });

    // ===================================
    // NEW RECORD
    // ===================================
    const newRecord_init = () => {
        // szerkesztés megszakítása
        cancelEdit();
        state.isEdit = false;

        openEditModal();
    };

    /**
     * Function to create a record.
     *
     * @return {void} No return value
     */
    const createRecord = () => {
        errors.value = '';

        subdomainCreate(state.Record);
    };


    // ===================================
    // EDIT RECORD
    // ===================================
    
    /**
     * Edit a record.
     *
     * @param {object} record - the record to be edited
     *
     * @return {void}
     */
    const editRecord = (record) => {
        // set the record to be edited
        state.editingRecord = record;

        // open the edit modal
        openEditModal();
    };

    /**
     * A function that updates a record.
     *
     * @param {void} -
     * @return {void} -
     */
    const updateRecord = () => {
        subdomainCreate(state.editingRecord);
    };

    /**
     * Cancel the edit.
     *
     * @return {void}
     */
    const cancelEdit = () => {
        // reset the editable object
        state.editingRecord = newRecord();
        // false = new
        state.isEdit = false;
    };

    // ===================================
    // DELETE RECORD
    // ===================================
    
    /**
     * Open the delete modal with the given record.
     *
     * @param {object} record - the record to be deleted
     *
     * @return {void}
     */
    const confirmDelete = (record) => {
        state.deletingRecord = record;
        openDeleteModal();
    };

    /**
     * Delete a record.
     *
     * @param {object} record - the record to be deleted
     *
     * @return {void}
     */
    const deleteRecord = (record) => {
        response = subdomainDelete(record.id);
    };

    /**
     * Több rekord törlése
     *
     * @return {void}
     */
    const bulkDelete = () => {
        response = subdomainBulkDelete(selectedRecords.value);
        //console.log('bulkDelete response', response);
    };

    const cancelDelete = () => {
        state.deletingRecord = newRecord();
        closeDeleteModal();
    };

    // ===================================
    // KIJELÖLÉS
    // ===================================
    
    /**
     * Select all records.
     *
     * @return {void}
     */
    const selectAllRecord = () => {
        // if select all is checked
        if( selectAll.value ) {
            // összes rekord kiválasztása
            selectedRecords.value = state.Records.map(record => record.id);
        } else {
            // összes kiválasztás megszüntetése
            selectedRecords.value = [];
        }
    }; 

    /**
     * Select or unselect record.
     *
     * @param {int} id - Record ID
     * @return {void}
     */
    const toggleSelection = (id) => {
        const index = selectedRecords.value.indexOf(id);

        // Ha a rekord nincs kiválasztva,,
        if( index === -1 ) {
            // add it to selected records,
            selectedRecords.value.push(id);
        } else {
            // else remove it from selected records
            selectedRecords.value.splice(index, 1);
        }
    };

    // =====================
    // MODAL KEZELÉS
    // =====================
    
    /**
     * Megnyitja a beállítások modalt
     *
     * @return {void}
     */
    function openSettingsModal() {
        $('#settingsModal').modal('show');
    };

    /**
     * Bezárja a beállítások modalt
     *
     * @return {void}
     */
    function closeSettingsModal() {
        $('#settingsModal').modal('hide');
    };

    
    /**
     * Megnyitja a szerkesztés modalt
     *
     * @return {void}
     */
    function openEditModal() {
        $('#editModal').modal('show');
    };
    
    /**
     * Bezárja a szerkesztés modalt
     *
     * @return {void}
     */
    function closeEditModal() {
        cancelEdit();

        $('#editModal').modal('hide');
    };

    /**
     * Megnyitja a törlés modalt
     *
     * @return {void}
     */
    function openDeleteModal() {
        $('#deleteModal').modal('show');
    };
    
    /**
     * Bezárja a törlés modalt
     *
     * @return {void}
     */
    function closeDeleteModal() {
        $('#deleteModal').modal('hide');
    };

    // ===================================
    // Services
    // ===================================
    
    import useRoles from '@/services/subdomains.js';
    
    const {
        subdomains, subdomainsToSelect, subdomainsToTable, subdomain, 
        getSubdomains, getSubdomainsToTable, getSubdomainsToSelect, getSubdomainById, password,
        subdomainCreate, subdomainUpdate, genPassword,
        subdomainDelete, subdomainBulkDelete, subdomainRestore

    } = useRoles();

    /**
     * Retrieve subdomains with specified filters and pagination configuration
     *
     * @param {Object} filters - The filters to apply
     * @param {Object} config - The pagination configuration
     * @param {number} page - The page number to retrieve
     */
     const getRecords = async (page = state.pagination.current_page) => {
        await getSubdomainsToTable({
            filters: state.filter, 
            config: {
                per_page: state.pagination.per_page
            },
            page
        });
    };
    
   /*
    const getRecords = (page = state.pagination.current_page) => {
        getSubdomainsToTable({
            filters: state.filter, 
            config: {
                per_page: state.pagination.per_page
            },
            page
        });
    };
    */

    onMounted(() => {

        let columns = localStorage.getItem(local_storage_column_key);
        if( columns ) {
            columns = JSON.parse(columns);
            for( const column_name in columns ) {
                state.columns[column_name].is_visible = columns[column_name];
            }
        }

        getRecords();
    });

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

                            <!-- SETTINGS -->
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
                                            <th>chk</th>

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
                                        <tr v-for="Record in subdomainsToTable.data" 
                                            :key="Record.id">

                                            <!-- checkbox a sor elején -->
                                            <td>
                                                <!-- CHECKBOX -->
                                                <input type="checkbox" 
                                                       :checked="selectAll" 
                                                       @change="toggleSelection(Record.id)"/>
                                            </td>

                                            <!-- FIELDS -->
                                            <td v-for="(key, value) in state.columns" 
                                                :key="key" 
                                                v-show="key.is_visible">
                                                {{ Record[value] }}
                                            </td>

                                            <!-- ACTIONS -->
                                            <td>
                                                <div class="bd-example">
                                                    
                                                    <!-- SZERKESZTÉS -->
                                                    <button type="button" 
                                                            class="btn btn-primary" 
                                                            @click="editRecord(Record)">
                                                        <i class="fa fa-edit"></i>
                                                    </button>

                                                    <!-- TÖRLÉS -->
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
                                <v-pagination v-model="subdomainsToTable.current_page" 
                                              :pages="subdomainsToTable.last_page"
                                              :range-size="state.pagination.range"
                                              active-color="#DCEDFF"
                                              @update:modelValue="getRecords"/>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </MainLayout>

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
                                    aria-describedby="url_help" 
                                    :placeholder="$t('url_placeholder')"
                                    v-model="state.editingRecord.url"/>
                                <div class="invalid-feedback" 
                                    v-if="errors?.url">{{ errors.url[0] }}</div>

                            </div>
                        </div>

                        <div class="row">
                            <!-- DB_HOST -->
                            <div class="col form-group">
                                <label>{{ $t('db_host') }}</label>
                                <input name="db_host" id="db_host" 
                                       class="form-control" :class="errors?.db_host? 'is-invalid' : 'is-valid'" 
                                       aria-describedby="db_host_help"  
                                       :placeholder="$t('db_host_placeholder')"
                                       v-model="state.editingRecord.db_host" />
                                <div class="invalid-feedback" 
                                     v-if="errors?.db_host">{{ errors.db_host[0] }}</div>
                            </div>

                            <!-- DB_PORT -->
                            <div class="col form-group">
                                <label>{{ $t('db_port') }}</label>
                                <input name="db_port" id="db_port" 
                                       class="form-control" :class="errors?.db_port? 'is-invalid' : 'is-valid'"
                                       aria-describedby="db_port_help"   
                                       :placeholder="$t('db_port_placeholder')"
                                       v-model="state.editingRecord.db_port" />
                                <div class="invalid-feedback" 
                                     v-if="errors?.db_port">{{ errors.db_port[0] }}</div>
                            </div>

                            <!-- DB_NAME -->
                            <div class="col form-group">
                                <label>{{ $t('db_name') }}</label>
                                <input name="db_name" id="db_name" 
                                       class="form-control" :class="errors?.db_name? 'is-invalid' : 'is-valid'"
                                       aria-describedby="db_name_help"    
                                       :placeholder="$t('db_name_placeholder')"
                                       v-model="state.editingRecord.db_name" />
                                <div class="invalid-feedback" 
                                     v-if="errors?.db_name">{{ errors.db_name[0] }}</div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- DB_USER -->
                            <div class="col form-group">
                                <label>{{ $t('db_user') }}</label>
                                <input name="db_user" id="db_user" 
                                       class="form-control" :class="errors?.db_user? 'is-invalid' : 'is-valid'"
                                       aria-describedby="db_user_help"     
                                       :placeholder="$t('db_user_placeholder')"
                                       v-model="state.editingRecord.db_user" />
                                <div class="invalid-feedback" 
                                     v-if="errors?.db_user">{{ errors.db_user[0] }}</div>
                            </div>

                            <!-- DB_PASSWORD -->
                            <!--
                            <div class="col form-group">
                                <label>{{ $t('db_password') }}</label>
                                <input name="db_password" id="db_password" 
                                       class="form-control" :class="errors?.db_password? 'is-invalid' : 'is-valid'" 
                                       aria-describedby="db_password_help"    
                                       :placeholder="$t('db_password_placeholder')" 
                                       v-model="state.editingRecord.db_password" />
                                <div class="invalid-feedback" 
                                     v-if="errors?.db_password">{{ errors.db_password[0] }}</div>
                            </div>
                            -->
                            <div class="col form-group">
                                <label>{{ $t('db_password') }}</label>
                                <div class="input-group mb-3">
                                    <input type="text" 
                                        class="form-control" 
                                        placeholder="$t('db_password_placeholder')" 
                                        aria-label="Recipient's username" 
                                        aria-describedby="password_generate" 
                                        v-model="state.editingRecord.db_password">
                                    <button class="btn btn-outline-secondary" 
                                            type="button" 
                                            id="password_generate"
                                            @click="genPassword()">
                                        <i class="fa fa-fingerprint"></i>
                                    </button>
                                    <div class="invalid-feedback" 
                                         v-if="errors?.db_password">{{ errors.db_password[0] }}</div>
                                </div>
                            </div>

                        </div>

                        <hr/>

                        <div class="row">

                            <!-- NOTIFICATION -->
                            <div class="col form-check">
                                <input id="notification" name="notification"
                                       class="form-check-input" 
                                       type="checkbox" 
                                       value="" 
                                       v-model="state.editingRecord.notification"/>
                                <label class="form-check-label" for="notification">
                                    {{ $t('notification') }}
                                </label>
                            </div>

                            <!-- IS_MIRROR -->
                            <div class="col form-check">
                                <input id="is_mirror" name="is_mirror"
                                       class="form-check-input" 
                                       type="checkbox" 
                                       value=""
                                       v-model="state.editingRecord.is_mirror">
                                <label class="form-check-label" 
                                       for="is_mirror">
                                       {{ $t('is_mirror') }}
                                </label>
                            </div>

                            <!-- SSO -->
                            <div class="col form-check">
                                <input id="sso" name="sso" 
                                       class="form-check-input" 
                                       type="checkbox" 
                                       value=""
                                       v-model="state.editingRecord.sso">
                                <label class="form-check-label" 
                                       for="sso">
                                       {{ $t('sso') }}
                                </label>
                            </div>
                        </div>

                        <hr/>

                        <div class="row">

                            <!-- STATE_ID -->
                            <div class="col form-group">
                                <label>{{ $t('state_id') }}</label>
                                <select id="state_id" name="state_id"
                                        class="form-control">
                                    <option v-for="s in state.subdomain_states" 
                                            :key="s.id" 
                                            :value="s.id"
                                            :selected="(s.id == state.editingRecord.state_id)">
                                        {{ s.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- ACCESS CONTROLL_SYSTEM -->
                            <div class="col form-group">
                                <label>{{ $t('access_control_system') }}</label>
                                <select id="access_control_system" name="access_control_system"
                                        class="form-control">
                                    <option v-for="acs in state.access_control_systems"
                                            :key="acs.id" 
                                            :value="acs.id"
                                            :selected="(acs.id == state.editingRecord.access_control_system)">
                                        {{ acs.name }}
                                    </option>
                                </select>
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

</template>