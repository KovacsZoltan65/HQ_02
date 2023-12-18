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
    });

    const errors = ref({})

    const state = reactive({
        Users: [],
        User: newUser(),
        editingUser: null,
        deletingUser: null,
        showSettingsModal: false,
        showEditModal: false,
        showDeleteModal: false,
        isEdit: false,

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

    // Új rekord előkészítése
    function newUser_init(){
        state.User = newUser();
        state.editingUser = null;
        state.isEdit = false;

        openEditModal();
    };

    // Új rekord beállítása
    function newUser(){
        return {
            id: null,
            name: null,
            email: null,
            password: null,
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

    // Szerkesztés kezdete
    function editUser(user){
        state.editUser = user;
        state.User = state.editUser;
        state.isEdit = true;

        openEditModal();
    };

    // Szerkesztett adatok mentése
    function updateUser(){
        errors.value = '';
        axios.put(route('users_update', {user: state.editUser.id}))
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

    function openSettingsModal() {
        $('#settingsModal').modal('show');
    };
    function closeSettingsModal() {
        $('#settingsModal').modal('hide');
    };

    function openEditModal() {
        $('#userFormModal').modal('show');
    };
    function closeEditModal() {
        state.isEdit = false;
        errors.value = {};

        $('#userFormModal').modal('hide');
    };

    function openDeleteModal() {
        $('#userDeleteModal').modal('show');
    };
    function closeDeleteModal() {
        $('#userDeleteModal').modal('hide');
    };

    const getUsers = (page = state.pagination.current_page) => {
        axios.get(route('getUsers', {
            filters: state.filter,
            config: {
                per_page: state.pagination.per_page,
            },
            page
        }))
        .then((response) => {
            console.log(response);
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
    <Head title="USERS" />

    <MainLayout>
        
        <!-- CONTENT HEADER -->
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">USERS</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <Link :href="route('dashboard')">HOME</Link>
                        </li>
                        <li class="breadcrumb-item active">USERS</li>
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
                            ADD NEW USER
                        </button>

                        <!-- BULK DELETE -->
                        <div>
                            <button type="button" @click="" 
                                    class="ml-2 mb-2 btn btn-danger">
                                <i class="fa fa-trash mr-1"></i>
                                Delete Selected
                            </button>
                            <span class="ml-2">Selected 10 users</span>
                        </div>

                        <!-- SEARCH -->
                        <div>
                            <input type="text"
                                   v-model="state.searchQuery" 
                                   class="form-control" 
                                   placeholder="Search..." />
                        </div>

                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            
                            <div class="card-header">
                                <h5 class="card-title">USERS</h5>
                            </div>

                            <div class="card-body">

                                <p class="card-text">
                                    Some quick example text to build on the card title and make up the bulk of the card's
                                    content.
                                </p>

                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>NAME</th>
                                            <th>EMAIL</th>
                                            <th>OPTIONS</th>
                                        </tr>
                                    </thead>
                                    <tbody v-for="user in state.Users">
                                        <tr>
                                            <td>{{ user.id }}</td>
                                            <td>{{ user.name }}</td>
                                            <td>{{ user.email }}</td>
                                            <td>
                                                <button class="btn btn-primary" type="button"  
                                                        @click="editUser(user)">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <button class="ml-2 btn btn-danger" type="button">
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
            <div class="modal-dialog" role="document">
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

                        <!-- NAME -->
                        <div class="form-group">
                            <label>NAME</label>
                            
                            <input name="name" type="text" 
                                   class="form-control" 
                                   :class="errors?.name ? 'is-invalid' : 'is-valid'"
                                   aria-describedby="nameHelp" 
                                   placeholder="Enter full name"
                                   v-model="state.User.name"/>
                            <div class="invalid-feedback" 
                                 v-if="errors?.name">{{ errors.name[0] }}</div>

                        </div>

                        <!-- EMAIL -->
                        <div class="form-group">
                            <label>EMAIL</label>
                            
                            <input name="email" type="email" 
                                   class="form-control"
                                   :class="errors?.email ? 'is-invalid' : 'is-valid'"
                                   aria-describedby="emailHelp" 
                                   placeholder="Enter email"
                                   v-model="state.User.email"/>
                            <span class="invalid-feedback" 
                                  v-if="errors?.email">{{ errors.email[0] }}</span>

                        </div>

                        <!-- PASSWORD -->
                        <div class="form-group">
                            <label>PASSWORD</label>
                            
                            <input name="password" type="password" 
                                   class="form-control" 
                                   :class="errors?.password ? 'is-invalid' : 'is-valid'"
                                   aria-describedby="passwordHelp" 
                                   v-model="state.User.password"/>
                            <span class="invalid-feedback" 
                                  v-if="errors?.password">{{ errors.password[0] }}</span>

                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" 
                                class="btn btn-secondary"
                                @click="closeEditModal()">CANCEL</button>
                        <button type="submit" 
                                @click="state.isEdit? updateUser() : createUser()"  
                                class="btn btn-primary"
                        >{{ state.isEdit ? 'UPDATE' : 'CREATE' }}</button>
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