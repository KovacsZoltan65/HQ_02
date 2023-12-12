<script setup>
    import { ref, onMounted, onUnmounted, watch } from 'vue';
    import axios from 'axios';
    import { Link } from '@inertiajs/vue3';
    
    import { Form, Field, useResetForm } from 'vee-validate';
    import * as yup from 'yup';
    import { debounce } from 'lodash';

    import MainLayout from '../../../Layouts/MainLayout.vue';
    import { useToastr } from '../../../toastr.js';

    const toastr = useToastr();
    const searchQuery = ref(null);
    const Users = ref({'data': []});
    const selectedUsers = ref([]);
    const selectAll = ref(false);
    const editing = ref(false);
    const formValues = ref();
    const form = ref(null);
    const userIdBeingDeleted = ref(null);

    // =================
    // Adatok lekérése
    // =================
    const getUsers = (page = 1) => {
        axios.get(`/getUsers?page=${page}`, {
            params: {
                query: searchQuery.value
            }
        })
        .then((res) => {
            Users.value = res.data.users;
            selectedUsers.value = [];
            selectAll.value = false;
        });
    };

    // ====================
    // Új elem
    // ====================
    const addUser = () => {
        editing.value = false;
        $('#userFormModal').modal('show');
    };
    const createUser = (values, {resetForm, setErrors}) => {
        axios.post('/users', values)
        .then((response) => {
            Users.value.data.unshift(response.data);
            $('#userFormModal').modal('hide');
            resetForm();
            toastr.success('User created successfully');
        })
        .catch((error) => {
            if(error.response.data.errors){
                setErrors(error.response.data.errors);
            }
        });
    }

    // ========================
    // Szerkesztés
    // ========================
    const editUser = (user) => {
        editing.value = true;
        form.value.resetForm();
        $('#userFormModal').modal('show');
        formValues.value = {
            id: user.id,
            name: user.name,
            email: user.email
        };
    };
    const updateUser = (values, {setErrors}) => {
        axios.put('/users/' + formValues.value.id, values)
        .then((response) => {
            const index = Users.value.data.findIndex(user => user.id === response.data.id);
            Users.value.data[index] = response.data;
            $('#userFormModal').modal('hide');
            toastr.success('User updated successfully');
        })
        .catch((error) => {
            setErrors(error.response.data.errors);
        });
    };

    const handleSubmit = (values, actions) => {
        if(editing.value){
            updateUser(values, actions);
        }else{
            createUser(values, actions);
        }
    };

    // =================
    // Delete
    // =================
    const confirmUserDeletion = (id) => {
        userIdBeingDeleted.value = id;
        $('#deleteUserModal').modal('show');
    };
    const deleteUser = () => {
        axios.delete(`/users/${userIdBeingDeleted.value}`)
        .then((response) => {
            $('#deleteUserModal').modal('hide');
            toastr.success('User deleted successfully!');
            Users.value.data = Users.value.filter(user => user.id !== userIdBeingDeleted);
        })
        .catch((error) => {
            //
        });
    };
    const bulkDeleteUsers = () => {
        axios.delete(`/users/${userIdBeingDeleted.value}`)
        .then((response) => {
            Users.value.data = Users.value.data.filter(user => !selectedUsers.value.includes(user.id));
            selectedUsers.value = [];
            selectAll.value = false;
            toastr.success(response.data.message);
        })
        .catch((error) => {});
    };

    // =================
    // Select
    // =================
    const selectAllUsers = () => {
        if(selectAll.value){
            selectedUsers.value = Users.value.data.map(user => user.id);
        }else{
            selectedUsers.value = [];
        }
    };

    // =================
    // Validálás
    // =================
    const createUserSchema = yup.object({
        name: yup.string().required(),
        email: yup.string().required(),
        password: yup.string().required(),
    });

    const updateUserSchema = yup.object({
        name: yup.string().required(),
        email: yup.string().required(),
        password: yup.string().when((password, schema) => {
            return password ? schema.required().min(8) : schema;
        }),
    });

    // =================
    // Figyelés
    // =================
    watch(searchQuery, debounce(() => {
        getUsers();
    }, 300));

    onMounted( async () => {
        getUsers();
    });

    onUnmounted(() => {});

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
                            <Link :href="route('dashboard')">Home</Link>
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
                        <button @click="addUser" type="button" class="mb-2 btn btn-primary">
                            <i class="fa fa-plus-circle mr-1"></i>
                            Add New User
                        </button>
                        <div v-if="selectedUsers.length > 0">
                            <button @click="bulkDelete" type="button" class="ml-2 mb-2 btn btn-danger">
                                <i class="fa fa-trash mr-1"></i>
                                Delete Selected
                            </button>
                            <span class="ml-2">Selected {{ selectedUsers.length }} users</span>
                        </div>
                    </div>
                    <div>
                        <input type="text" v-model="searchQuery" class="form-control" placeholder="Search..." />
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
                                    <tbody>
                                        <tr v-for="user in Users">
                                            <td>{{ user.id }}</td>
                                            <td>{{ user.name }}</td>
                                            <td>{{ user.email }}</td>
                                            <td>
                                                <button class="btn btn-primary" 
                                                        @click.prevent="editUser(user)">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <button class="ml-2 btn btn-danger" 
                                                        @click.prevent="confirmUserDeletion(user.id)">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                
                            </div>

                            <div class="card-footer">
                                <a href="#" class="card-link">Card link</a>
                                <a href="#" class="card-link">Another link</a>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <!-- userFormModal -->
        <div class="modal fade" id="userFormModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">
                            <span v-if="editing">Edit User</span>
                            <span v-else>Add New User</span>
                        </h5>
                        <button type="button" class="close" 
                                data-dismiss="modal" 
                                aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <Form ref="form" 
                          @submit="handleSubmit"
                          :validation-schema="editing ? editUserSchema : createUserSchema"
                          v-slot="{ errors }"
                          :initial-values="formValues">
                        <div class="modal-body">

                            <!-- NAME -->
                            <div class="form-group">
                                <label for="name">NAME</label>
                                <Field id="name" name="name" type="text" 
                                       class="form-control" 
                                       :class="{ 'is-invalid': errors.name }"
                                       aria-describedby="nameHelp" 
                                       placeholder="ENTER FULL NAME"/>
                                <span class="invalid-feedback">{{ errors.name }}</span>
                            </div>

                            <!-- EMAIL -->
                            <div class="form-group">
                                <label for="email"></label>
                                <Field id="email" name="email" type="email" 
                                       class="form-control" 
                                       :class="{ 'is-invalid': errors.email }"
                                       aria-describedby="emailHelp" 
                                       placeholder="ENTER EMAIL ADDRESS"/>
                                <span class="invalid-feedback">{{ errors.email }}</span>
                            </div>

                            <!-- PASSWORD -->
                            <div class="form-group">
                                <label for="password"></label>
                                <Field id="password" name="password" type="password" 
                                       class="form-control" 
                                       :class="{ 'is-invalid': errors.password }"
                                       aria-describedby="passwordHelp"/>
                                <span class="invalid-feedback">{{ errors.password }}</span>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">CANCEL</button>
                            <button type="submit" class="btn btn-primary">SAVE</button>
                        </div>
                    </Form>
                </div>
            </div>
        </div>

        <!-- deleteUserModal -->
        <div id="deleteUserModal" class="modal fade" 
             data-backdrop="static" tabindex="-1" role="dialog" 
             aria-labelledby="staticBackdropLabel" aria-hidden="true">
             <div class="modal-dialog" role="document">
                <div class="modal-content">
                    
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">
                            <span>Delete User</span>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <h5>Are you sure you want to delete this user ?</h5>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button @click.prevent="deleteUser" type="button" class="btn btn-primary">Delete User</button>
                    </div>

                </div>
             </div>
        </div>

    </MainLayout>

</template>