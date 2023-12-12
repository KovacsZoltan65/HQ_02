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
    };
    const createUser = (values, {resetForm, setErrors}) => {
        axios.post()
        .then((response) => {
            Users.value.data.unshift(response.data);
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
        formValues.value = {
            id: user.id,
            name: user.name,
            email: user.email
        };
    };
    const updateUser = (values, {setErrors}) => {
        axios.put()
        .then((response) => {
            const index = users.value.data.findIndex(user => user.id === response.data.id);
            Users.value.data[index] = response.data;
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
        <div class="content-header">

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
                                                    <button class="btn btn-primary">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                    <button class="ml-2 btn btn-danger">
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

        </div>
    </MainLayout>

</template>