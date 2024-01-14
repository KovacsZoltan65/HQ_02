<script setup>
    import { Head, useForm } from '@inertiajs/vue3';
    import RegisterLayout from '@/Layouts/RegisterLayout.vue';

    const props = defineProps({
        email: {
            type: String,
            required: true
        },
        token: {
            type: String,
            required: true
        },
    });
    const form = useForm({
        token: props.token,
        email: props.email,
        password: '',
        password_confirmation: ''
    });

    const submit = () => {
        form.post(route('password.store'), {
            onFinish: () => form.reset('password', 'password_confirmation'),
        });
    };
</script>

<template>
    <RegisterLayout>
        <Head :title="$t('reset_password')" />

        <div class="register-box">
            <div class="card card-outline card-primary">

                <div class="card-header text-center">
                    <Link :href="route('dashboard')" class="h1">
                        <b>Admin</b>LTE
                    </Link>
                </div>

                <div class="card-body">
                    <p class="login-box-msg">{{ $t('reset_password_title') }}</p>

                    <form @submit.prevent="submit">
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
                    </form>

                </div>

            </div>
        </div>

    </RegisterLayout>
</template>