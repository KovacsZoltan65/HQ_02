<script setup>
    import { Link } from '@inertiajs/vue3';
</script>

<template>
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!--
            <li class="nav-item menu-open">
                <a href="#" class="nav-link active">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>
                        Starter Pages
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="#" class="nav-link active">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Active Page</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Inactive Page</p>
                        </a>
                    </li>
                </ul>
            </li>
        -->
            <li class="nav-item">
                <Link :href="route('users')" 
                      class="nav-link">
                    <i class="nav-icon fas fa-users"></i>
                    <p>
                        {{ $t('users') }}
                        <!--<span class="right badge badge-danger">New</span>-->
                    </p>
                </Link>
            </li>

            <!-- SUBDOMAINS -->
            <li class="nav-item">
                <Link :href="route('subdomains')" 
                   class="nav-link">
                    <i class="nav-icon fas fa-building"></i>
                    <p>
                        {{ $t('subdomains') }}
                    </p>
                </Link>
            </li>

            <!-- ROLES -->
            <li class="nav-item">
                <Link :href="route('roles')" 
                   class="nav-link">
                    <i class="nav-icon fas fa-dirdle-user"></i>
                    <p>
                        {{ $t('roles') }}
                    </p>
                </Link>
            </li>

            <!--<li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-th"></i>
                    <p>
                        Simple Link
                        <span class="right badge badge-danger">New</span>
                    </p>
                </a>
            </li>-->

        </ul>
    </nav>
</template>