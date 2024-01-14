<script setup>   
    import '../../../../node_modules/admin-lte/plugins/icheck-bootstrap/icheck-bootstrap.min.css';

    import { Head, Link, useForm } from '@inertiajs/vue3';
    import LoginLayout from '@/Layouts/LoginLayout.vue';
    import InputError from '@/Components/InputError.vue';

    defineProps({
        canResetPassword: Boolean,
        status: String,
    });

    const form = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const submit = () => {
        form.transform(data => ({
            ...data,
            remember: form.remember ? 'on' : '',
        })).post(route('login'), {
            onFinish: () => form.reset('password'),
        });
    };
</script>
<template>
    <LoginLayout>

        <Head :title="$t('login')" />

        <div v-if="status">
        {{ status }}
        </div>
        
        <div class="login-box">
            <!-- /.login-logo -->
            <div class="card card-outline card-primary">
                <div class="card-header text-center">
                    <a href="../../index2.html" 
                       class="h1"><b>Admin</b>LTE</a>
                </div>
                <div class="card-body">
                    <p class="login-box-msg">{{ $t('login_title') }}</p>

                    <form @submit.prevent="susbmit">
                        
                        <!-- EMAIL -->
                        <div class="input-group mb-3">
                            <input id="email" name="email"
                                   v-model="form.email"
                                   type="email" class="form-control" 
                                   :placeholder="$t('email')"
                                   autofocus required
                                   autocomplete="username">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-envelope"></span>
                                </div>
                            </div>
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <!-- PASSWORD -->
                        <div class="input-group mb-3">
                            <input id="password" name="password"
                                   v-model="form.password"
                                   type="password" class="form-control" 
                                   :placeholder="$t('password')"
                                   required autocomplete="current-password">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                            <InputError class="mt-2"
                                        :message="form.errors.password" />
                        </div>

                        <div class="row">
                            <div class="col-7">
                                <!--<div class="icheck-primary">
                                    <input type="checkbox"
                                           id="remember">
                                    <label for="remember">
                                        {{ $t('remember_me') }}
                                    </label>
                                </div>-->
                            </div>
                            <!-- /.col -->
                            <div class="col-5">
                                <button type="submit"
                                        class="btn btn-primary btn-block">
                                    {{ $t('login_button') }}
                                </button>
                            </div>
                            <!-- /.col -->
                        </div>
                    </form>

                    <!--<div class="social-auth-links text-center mt-2 mb-3">
                        <a href="#" class="btn btn-block btn-primary">
                            <i class="fab fa-facebook mr-2"></i> Sign in using Facebook
                        </a>
                        <a href="#" class="btn btn-block btn-danger">
                            <i class="fab fa-google-plus mr-2"></i> Sign in using Google+
                        </a>
                    </div>-->
                    <!-- /.social-auth-links -->

                    <p class="mb-1">
                        <Link :href="route('password.request')">
                            {{ $t('forgot_password_title') }}
                        </Link>
                    </p>
                    <p class="mb-0">
                        <Link :href="route('register')">{{ $t('register_new_membership') }}</Link>
                    </p>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>

    </LoginLayout>

</template>

