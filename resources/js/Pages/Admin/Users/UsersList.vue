<script setup>
    import { reactive, onMounted, ref } from 'vue';
    import axios from 'axios';
    import MainLayout from '@/Layouts/MainLayout.vue';
    import { Head, Link } from '@inertiajs/vue3';

    import VPagination from '@hennge/vue3-pagination';
    import '@hennge/vue3-pagination/dist/vue3-pagination.css';

    const props = defineProps({
        can: {
            type: Object, default: () => ({}),
        },
        languageOptions: {
            type: Object, default: () => ({}),
        }
    });

    const errors = ref({})

    const state = reactive({
        // Felhasználók
        Users: [],
        // Aktuális felhasználó
        User: newUser(),
        // Szerkesztendő felhasználó
        editingUser: newUser(),
        // Törlendő felhasználó
        deletingUser: newUser(),
        changePassword: {
            password: '',
            confirm_password: '',
        },
        showSettingsModal: false,
        showEditModal: false,
        showDeleteModal: false,
        showChangePasswordModal: false,

        // Szerkesztés folyamatban
        isEdit: false,
        // Táblázat oszlopai
        columns: {
               id: { label: '#', is_visible: true, is_sortable: true, is_filterable: true },
             name: { label: 'name', is_visible: true, is_sortable: true, is_filterable: true },
            email: { label: 'email', is_visible: true, is_sortable: true, is_filterable: true },
        },

        pagination: {
            current_page: 1,
            total_number_of_pages: 0,
            per_page: 10,
            range: 5,
        },
        filter: {
            tags: [],
            search: null,
            column: null,
            direction: null,
        },

        searchQuery: '',
    });

    // =====================
    // ÚJ REKORD
    // =====================

    // Új rekord előkészítése
    function newUser_init(){
        cancelEdit();

        openEditModal();
    };

    // Új rekord beállítása
    function newUser(){
        return {
            id: null, name: null,
            email: null, password: null,
            role: null, avatar: null, language: 'hu'
        };
    };

    // Új rekord mentése
    function createUser(user){
        errors.value = '';
        axios.post(route('users_store', state.User))
        .then(res => {
            state.Users.push(res.data.user);
            closeEditModal();
        })
        .catch(error => {
            if( error.response.status == 422 ){
                errors.value = error.response.data.errors;
                console.log(errors);
            }
        });
    }

    // =====================
    // SZERKESZTÉS
    // =====================

    // Szerkesztés kezdete
    function editUser(user){
        state.editingUser = JSON.parse( JSON.stringify(user) );
        state.User = state.editingUser;
        state.isEdit = true;

        openEditModal();
    };

    // Szerkesztett adatok mentése
    function updateUser(){
        errors.value = '';

        console.log('updateUser');
        console.log(state.editingUser);

        axios.put(route('users_update', {user: state.editingUser.id}), {
            name: state.editingUser.name,
            email: state.editingUser.email,
        })
        .then(res => {
            closeEditModal();
        })
        .catch(e => {
            if( e.response.status == 422 ){
                console.log('e', e.response.data.errors);
                errors.value = e.response.data.errors;
            }
        });
    }

    // Szerkesztés megszakítása
    function cancelEdit(){
        state.editingUser = newUser();
        state.User = newUser();
        state.isEdit = false;
        errors.value = {};
    }

    // =====================
    // JELSZÓ MÓDOSÍTÁS
    // =====================
    function changePassword_init(user){
        state.editingUser = JSON.parse( JSON.stringify(user) );
        openChangePasswordModal();
    };

    function changePassword(){
        errors.value = '';

        axios.post(route('change_password', {user: state.editingUser.id}), {
            password: state.changePassword.password,
            change_password: state.changePassword.confirm_password,
        })
        .then(res => {
            closeChangePasswordModal();
        })
        .catch(error => {
            //
        });
    }

    function cancelChangePassword(){
        closeChangePasswordModal();
    }

    // =====================
    // TÖRLÉS
    // =====================
    function deleteUser_init(user){
        state.editingUser = null;
        state.deletingUser = user;

        openDeleteModal();
    }

    function deleteUser(user){
        axios.delete(route('users_delete', {user: state.deletingUser.id}))
        .then(response => {
            state.Users = state.Users.filter(u => u.id != user.id);
            state.deleteingUser = null;

            closeDeleteModal();
        })
        .catch(error => {
            console.log(error);
        });
    }

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
        $('#userFormModal').modal('show');
    };
    function closeEditModal() {
        cancelEdit();

        $('#userFormModal').modal('hide');
    };
    // törlés
    function openDeleteModal() {
        $('#userDeleteModal').modal('show');
    };
    function closeDeleteModal() {
        $('#userDeleteModal').modal('hide');
    };
    // jelszó módosítás
    function openChangePasswordModal(){
        $('#changePasswordModal').modal('show');
    };
    function closeChangePasswordModal(){
        state.editingUser = newUser();

        $('#changePasswordModal').modal('hide');
    };

    // =====================
    // ADATLEKÉRÉS
    // =====================
    const getUsers = (page = state.pagination.current_page) => {
        axios.get(route('getUsers', {
            filters: state.filter,
            config: {
                per_page: state.pagination.per_page,
            },
            page
        }))
        .then((response) => {
            //console.log(response);
            state.Users = response.data.users.data;

            state.pagination.total_number_of_pages = response.data.users.last_page;
            state.pagination.current_page = response.data.users.current_page;
        });
    };

    onMounted( async () => {
        getUsers();
    });

</script>

<template>
    <Head :title="$t('users')" />

    <MainLayout>
        
        <!-- CONTENT HEADER -->
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ $t('users') }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <Link :href="route('dashboard')">{{ $t('home') }}</Link>
                        </li>
                        <li class="breadcrumb-item active">{{ $t('users') }}</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- CONTENT -->
        <div class="content">
            <div class="container-fluid">

                <div class="d-flex justify-content-between">
                    <div class="d-flex">
                        
                        <!-- ADD NEW USER -->
                        <button type="button" 
                                class="mb-2 btn btn-primary" 
                                @click="newUser_init()">
                            <i class="fa fa-plus-circle mr-1"></i>
                            {{ $t('users_new') }}
                        </button>

                        <!-- BULK DELETE -->
                        <div>
                            <button type="button" @click="" 
                                    class="ml-2 mb-2 btn btn-danger">
                                <i class="fa fa-trash mr-1"></i>
                                {{ $t('delete_selected') }}
                            </button>
                            <span class="ml-2">Selected 10 users</span>
                        </div>

                        <!-- SEARCH -->
                        <div>
                            <input type="text"
                                   v-model="state.searchQuery" 
                                   class="form-control" 
                                   :placeholder="$t('search')" />
                        </div>

                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            
                            <div class="card-header">
                                <h5 class="card-title">{{ $t('users') }}</h5>
                            </div>

                            <div class="card-body">

                                <p class="card-text">
                                    Some quick example text to build on the card title and make up the bulk of the card's
                                    content.
                                </p>

                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>{{ $t('id')}}</th>
                                            <th>{{ $t('name')}}</th>
                                            <th>{{ $t('email')}}</th>
                                            <th>{{ $t('language')}}</th>
                                            <th>{{ $t('actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody v-for="user in state.Users">
                                        <tr>
                                            <td>{{ user.id }}</td>
                                            <td>{{ user.name }}</td>
                                            <td>{{ user.email }}</td>
                                            <td>{{ user.language }}</td>
                                            <td>
                                                <!-- EDIT -->
                                                <button class="btn btn-primary" 
                                                        type="button"  
                                                        @click="editUser(user)">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <!-- CHANGE PASSWORD -->
                                                <button class="ml-2 btn btn-warning" 
                                                        type="button" 
                                                        @click="changePassword_init(user)">
                                                    <i class="fa fa-key"></i>
                                                </button>
                                                <!-- DELETE -->
                                                <button class="ml-2 btn btn-danger" 
                                                        type="button">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                                
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
                                              @update:modelValue="getUsers" />
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- EDIT MODAL -->
        <div class="modal fade" id="userFormModal" 
             data-backdrop="static" 
             tabindex="-1" role="dialog" 
             aria-labelledby="staticBackdropLabel" 
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">
                            <span v-if="state.isEdit">Edit User</span>
                            <span v-else>Create User</span>
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
                                    v-model="state.editingUser.name"/>
                                <div class="invalid-feedback" 
                                    v-if="errors?.name">{{ errors.name[0] }}</div>

                            </div>

                            <!-- EMAIL -->
                            <div class="col form-group">
                                <label>{{ $t('email') }}</label>
                                
                                <input name="email" type="email" 
                                    class="form-control"
                                    :class="errors?.email ? 'is-invalid' : 'is-valid'"
                                    aria-describedby="emailHelp" 
                                    :placeholder="$t('email_placeholder')"
                                    v-model="state.editingUser.email"/>
                                <span class="invalid-feedback" 
                                    v-if="errors?.email">{{ errors.email[0] }}</span>

                            </div>
                        </div>
                        <div class="row">
                            <!-- PASSWORD -->
                            <div class="col form-group">
                                <label>{{ $t('password') }}</label>
                                
                                <input name="password" type="password" 
                                    class="form-control"
                                    :class="errors?.password ? 'is-invalid' : 'is-valid'"
                                    aria-describedby="passwordHelp" 
                                    :placeholder="$t('password_placeholder')"
                                    v-model="state.editingUser.password"/>
                                <span class="invalid-feedback" 
                                    v-if="errors?.password">{{ errors.password[0] }}</span>

                            </div>

                            <!-- LANGUAGE -->
                            <div class="col form-group">
                                <label>{{ $t('language') }}</label>
                                <select class="form-control" 
                                        aria-label="Default select example">
                                    <option v-for="(lang, key) in languageOptions" 
                                            :key="key"
                                            :value="lang.value"
                                            :selected="(state.editingUser.language == lang.value)"
                                    >{{ lang.name }}</option>
                                </select>
                            </div>
                            
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" 
                                class="btn btn-secondary"
                                @click="closeEditModal()">{{ $t('cancel') }}</button>
                        <button type="submit" 
                                @click="state.isEdit? updateUser() : createUser()"  
                                class="btn btn-primary"
                        >{{ state.isEdit ? $t('update') : $t('create') }}</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- CHANGE PASSWORD MODAL -->
        <div class="modal fade" id="changePasswordModal" 
             data-backdrop="static" 
             tabindex="-1" role="dialog" 
             aria-labelledby="staticBackdropLabel" 
             aria-hidden="true">
             <div class="modal-dialog" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">
                            <span>CHANGE PASSWORD</span>
                        </h5>
                        <button type="button" class="close" 
                                data-dismiss="modal" 
                                aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    
                    <div class="modal-body">

                        <!-- PASSWORD -->
                        <div class="form-group">
                            <label>PASSWORD</label>
                            
                            <input name="password" type="password" 
                                class="form-control" 
                                :class="errors?.password ? 'is-invalid' : 'is-valid'"
                                aria-describedby="passwordHelp" 
                                v-model="state.changePassword.password"/>
                            <span class="invalid-feedback" 
                                v-if="errors?.password">{{ errors.password[0] }}</span>

                        </div>

                        <!-- CONFIRM PASSWORD -->
                        <div class="form-group">
                            <label>CONFIRM PASSWORD</label>
                            
                            <input name="confirm_password" type="password" 
                                class="form-control" 
                                :class="errors?.confirm_password ? 'is-invalid' : 'is-valid'"
                                aria-describedby="passwordHelp" 
                                v-model="state.changePassword.confirm_password"/>
                            <span class="invalid-feedback" 
                                v-if="errors?.confirm_password">{{ errors.confirm_password[0] }}</span>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" 
                                class="btn btn-secondary" 
                                @click="cancelChangePassword()">CANCEL</button>
                        <button type="submit" 
                                class="btn btn-primary"
                                @click="changePassword()">CHANGE PASSWORD</button>
                    </div>
                    
                </div>
             </div>
        </div>

        <!-- DELETE MODAL -->
        <div class="modal fade" id="userDeleteModal" 
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