<script setup>
    import { Head, Link, useForm } from '@inertiajs/vue3';
    import RegisterLayout from '@/Layouts/RegisterLayout.vue';
    import InputError from '@/Components/InputError.vue';

    import '../../../../node_modules/admin-lte/plugins/icheck-bootstrap/icheck-bootstrap.min.css';

    const form = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        terms: false,
    });

    const submit = () => {
        form.post(route('register'), {
            onFinish: () => form.reset('password', 'password_confirmation'),
        });
    };
</script>

<template>
    <RegisterLayout>
        <Head :title="$t('register')" />

        <div class="register-box">
            <div class="card card-outline card-primary">
                
                <div class="card-header text-center">
                    <!--<a href="../../index2.html" class="h1"><b>Admin</b>LTE</a>-->
                    <Link :href="route('dashboard')" class="h1">
                        <b>Admin</b>LTE
                    </Link>
                </div>

                <div class="card-body">
                    <p class="login-box-msg">{{ $t('register_title') }}</p>

                    <form @submit.prevent="submit">
                    
                        <!-- NAME -->
                        <div class="input-group mb-3">
                            <input v-model="form.name"
                                   id="name" name="name"
                                   type="text" class="form-control" 
                                   :placeholder="$t('full_name')"
                                   autocomplete="name"
                                   required autofocus>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user"></span>
                                </div>
                            </div>
                        </div>
                        <InputError class="mt-2"
                                    :message="form.errors.name" />

                        <!-- EMAIL -->
                        <div class="input-group mb-3">
                            <input id="email" name="email"
                                   v-model="form.email"
                                   type="email" class="form-control" 
                                   :placeholder="$t('email')"
                                   required autocomplete="username">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                                </div>
                            </div>
                        </div>
                        <InputError class="mt-2"
                                    :message="form.errors.email" />
                        
                        <!-- PASSWORD -->
                        <div class="input-group mb-3">
                            <input id="password" name="password"
                                   v-model="form.password"
                                   type="password" class="form-control" 
                                   :placeholder="$t('password')"
                                   required autocomplete="new-password">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>
                        <InputError class="mt-2"
                                    :message="form.errors.password" />
                        
                        <!-- CONFIRM -->
                        <div class="input-group mb-3">
                            <input id="password_confirmation" name="password_confirmation"
                                   v-model="form.password_confirmation"
                                   type="password" class="form-control" 
                                   :placeholder="$t('retype_password')">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>
                        <InputError class="mt-2"
                                    :message="form.errors.password_confirmation" />
                        
                        <div class="row">
                            <div class="col-8">
                                <div class="icheck-primary">
                                    <input type="checkbox" value="agree"
                                           id="agreeTerms" name="terms">
                                    <label for="agreeTerms">
                                        I agree to the <a href="#">terms</a>
                                    </label>
                                </div>
                            </div>
                            <!-- /.col -->
                            <div class="col-4">
                                <button type="submit" 
                                        class="btn btn-primary btn-block"
                                >Register</button>
                            </div>
                            <!-- /.col -->
                        </div>
                    </form>

                    <!--<div class="social-auth-links text-center">
                        <a href="#" class="btn btn-block btn-primary">
                            <i class="fab fa-facebook mr-2"></i>
                            Sign up using Facebook
                        </a>
                        <a href="#" class="btn btn-block btn-danger">
                            <i class="fab fa-google-plus mr-2"></i>
                            Sign up using Google+
                        </a>
                    </div>-->

                    <Link :href="route('login')">I already have a membership</Link>
                </div>
                <!-- /.form-box -->
            </div><!-- /.card -->
        </div>

    </RegisterLayout>
</template>