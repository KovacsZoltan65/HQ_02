<script setup>
    import { reactive, onMounted, onUnmounted } from 'vue';
    import { Link } from '@inertiajs/vue3';
    
    import { Form, Field, useResetForm } from 'vee-validate';
    import * as yup from 'yup';

    import MainLayout from '../../../Layouts/MainLayout.vue';
    import { useToastr } from '../../../toastr.js';
import axios from 'axios';

    const toastr = useToastr();
    const formValues = ref();
    const editing = ref(false);
    const searchQuery = ref(null);
    const selectedUsers = ref([]);

    const state = reactive({
        Users: [],
        
        // Oldaltörés
        pagination: {
            current_page: 1,
            total_number_of_pages: 0,
            per_page: 10,
            range: 5,
        },
        // Szűrés és keresés
        filters: {
            tags: [],
            search: null,
            column: null,
            direction: null,
        },
    });

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

    const toggleSelection = (user) => {
        const index = selectedUsers.value.indexOf(user.id);
        if( index === -1 ){
            //
        } else {
            //
        }
    };

    const createUser = async (values, {resetForm, setErrors}) => {
        axios.post('', values)
        .then((response) => {
            users.value.data.unshift(response.data);
            resetForm();
            toastr.success('USER CREATED SUCCESSFULLY');
        })
        .catch((error) => {
            if(error.response.data.errors){
                setErrors(error.response.data.errors);
            }
        });
    };

    const addUser = () => {};

    const editUser = async (user) => {
        //
    };

    // Adatok lekérése
    const getUsers = async (page = 1) => {
        axios.get(`/getUsers?page=${page}`, {
            params: {
                query: searchQuery.value
            },
            config: {
                per_page: 0
            }
        })
        .then((res) => {
            state.Users = res.data.users;
        });
    };

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
                                            <tr v-for="user in state.Users">
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