<script setup>
    //import { reactive, onMounted, onUnmounted, ref } from 'vue';
    
    import '../../../../node_modules/admin-lte/plugins/icheck-bootstrap/icheck-bootstrap.min.css';

    import { Head, Link, useForm } from '@inertiajs/vue3';
    import LoginLayout from '@/Layouts/LoginLayout.vue';

    defineProps({
        status: String
    });
    const form = useForm({
        email: ''
    });

    const submit = () => {
        form.post(route('password.email'));
    };
</script>
<template>
    <LoginLayout>

        <Head :title="$t('forgot_password')" />

        <div class="login-box">
            
            <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                {{ status }}
            </div>

            <div class="card card-outline card-primary">
                <div class="card-header text-center">
                    <a href="../../index2.html" 
                       class="h1"><b>Admin</b>LTE</a>
                </div>
                <div class="card-body">
                    <p class="login-box-msg">{{ $t('forgot_password_title') }}</p>
                    <form @submit.prevent="submit">

                        <!-- EMAIL -->
                        <div class="input-group mb-3">
                            <input type="email" 
                                class="form-control" 
                                :placeholder="$t('email')">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-envelope"></span>
                                </div>
                            </div>
                        </div>

                        <!-- ÚJ JELSZÓ KÉRÉSE -->
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" 
                                        class="btn btn-primary btn-block"
                                >{{ $t('request_new_password') }}</button>
                            </div>
                        </div>

                    </form>

                    <!-- LOGIN OLDALRA -->
                    <p class="mt-3 mb-1">
                        <Link :href="route('login')">{{ $t('login') }}</Link>
                    </p>
                </div>
                
            </div>
        </div>

    </LoginLayout>

</template>

