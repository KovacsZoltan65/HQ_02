<script setup>
    import { reactive, onMounted, onUnmounted, ref } from 'vue';
    
    import '../../../../node_modules/admin-lte/plugins/icheck-bootstrap/icheck-bootstrap.min.css';

    import { Head, Link, useForm } from '@inertiajs/vue3';
    import LoginLayout from '@/Layouts/LoginLayout.vue';

</script>
<template>
    <LoginLayout>

        <Head :title="$t('forgot_password')" />

        <div class="login-box">
            <div class="card card-outline card-primary">
                <div class="card-header text-center">
                    <a href="../../index2.html" class="h1"><b>Admin</b>LTE</a>
                </div>
                <div class="card-body">
                    <p class="login-box-msg">You forgot your password? Here you can easily retrieve a new password.</p>
                    <form action="recover-password.html" method="post">
                        <div class="input-group mb-3">
                        <input type="email" class="form-control" placeholder="Email">
                        <div class="input-group-append">
                            <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                        </div>
                        <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block">Request new password</button>
                        </div>
                        <!-- /.col -->
                        </div>
                    </form>
                    <p class="mt-3 mb-1">
                        <Link :href="route('login')">Login</Link>
                    </p>
                </div>
                <!-- /.login-card-body -->
            </div>
        </div>

    </LoginLayout>

</template>

