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
            subdomain: { label: 'subdomain', is_visible: true, is_sortable: true, is_filterable: true },
                  url: { label: 'url', is_visible: true, is_sortable: true, is_filterable: true },
                 name: { label: 'name', is_visible: true, is_sortable: true, is_filterable: true },
        },

        subdomain_states: [
            {
                id: 1,
                name: 'Aktív'
            },
            {
                id: 2,
                name: 'Felfüggesztve'
            },
            {
                id: 3,
                name: 'Leállítva(HQ)'
            },
            {
                id: 4,
                name: 'Leállítva(példány)'
            }
        ],
        access_control_systems: [
            {
                id: 0,
                name: 'Nincs'
            },{
                id: 1,
                name: 'WinAccess'
            },
            {
                id: 2,
                name: 'Enviromux'
            },
            {
                id: 3,
                name: 'Siport'
            },
            {
                id: 4,
                name: 'WinAccess WC'
            },
            {
                id: 5,
                name: 'GenerallyACS'
            }
        ],
    });


    // =====================
    // JELSZÓ GENERÁLÁS
    // =====================
    const genPassword = () => {
        axios.post(route('generatePassword'), {
            minLength: 5,
            maxLength: 10
        })
        .then(response => {
            //console.log(response.data);
            state.editingRecord.db_password = response.data;
        })
        .catch(error => {
            console.log(error);
        });
    };

    // =====================
    // ÚJ REKORD
    // =====================
    function newRecord() {
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
            last_export: ''
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

        axios.post(route('subdomains'), state.Record)
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

        axios.put(route('subdomains_update', {subdomain: state.editingRecord.id}), {
            id: state.editingRecord.id,
            subdomain: state.editingRecord.subdomain,
            url: state.editingRecord.url,
            name: state.editingRecord.name,
            db_host: state.editingRecord.db_host,
            db_port: state.editingRecord.db_port,
            db_name: state.editingRecord.db_name,
            db_user: state.editingRecord.db_user,
            db_password: state.editingRecord.db_password,
            notification: state.editingRecord.notification,
            state_id: state.editingRecord.state_id,
            is_mirror: state.editingRecord.is_mirror,
            sso: state.editingRecord.sso,
            access_control_system: state.editingRecord.access_control_system,
            last_export: state.editingRecord.last_export
        })
        .then(response => {
            cancelEdit();
            closeEditModal();

            toastr.success(trans('subdomains_updated'));
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
        axios.delete(`/subdomains/${state.deletingRecord.id}`)
        .then(response => {
            closeDeleteModal();
            state.Records = state.Records.filter(record => record.id !== state.deletingRecord.id);

            toastr.success(trans('subdomains_deleted'));
        })
        .catch(error => console.log(error));
    };
    // Kijelölt rekordok törlése
    const bulkDelete = () => {
        //console.log('bulkDelete', selectedRecords.value);
        axios.delete(route(''), {})
        .then(resource => {
            state.Records = state.Records.filter(s => !selectedRecords.value.includes(s.id) );
            selectedRecords.value = [];
            selectAll.value = false;
            toastr.success(trans('subdomains_bulk_deleted'));
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
        axios.get(route('getSubdomains', {
            filters: state.filter,
            config: {
                per_page: state.pagination.per_page,
            }, page
        })).then(response => {
            state.Records = response.data.subdomains.data;
            selectedRecords.value = [];
            selectAll.value = false;

            state.pagination.total_number_of_pages = response.data.subdomains.last;
            state.pagination.current_page = response.data.subdomains.current_page;
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
    <Head :title="$t('subdomains')"/>

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
                                <h5 class="card-title">{{ $t('subdomains') }}</h5>
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

    </MainLayout>
</template>