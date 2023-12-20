<script setup>
    import { ref } from 'vue';
    import { Link } from '@inertiajs/vue3';

    import SidebarSearch from '../Components/SidebarSearch.vue';
    import Messages from '../Components/Messages.vue';
    import Notifications from '../Components/Notifications.vue';
    import FullScreen from '../Components/FullScreen.vue';
    import SidebarRightButton from '../Components/SidebarRightButton.vue';
    import SidebarLeft from '../Components/SidebarLeft.vue';
    import QuickMenu from '../Components/QuickMenu.vue';
    import NavLink from '../Components/NavLink.vue';
    import Language from '../Components/Language.vue';

    import AdminLTELogo from '../../../public/dist/img/AdminLTELogo.png';
    
</script>

<template>
    <div id="MainLayout" class="w-full h-screen">
        <div class="wrapper">

            <!-- HEADER -->
            <nav class="main-header navbar navbar-expand navbar-white navbar-light">

                <!-- QUICK MENU -->
                <QuickMenu />

                <ul class="navbar-nav ml-auto">

                    <!-- SEARCH -->
                    <SidebarSearch />

                    <!-- MESSAGES -->
                    <Messages />

                    <!-- NOTIFICATIONS -->
                    <Notifications />

                    <!-- LANGUAGE -->
                    <Language />

                    <!-- FULL SCREEN -->
                    <FullScreen/>

                    <!-- Right SIDE BAR -->
                    <SidebarRightButton />

                </ul>

            </nav>

            <aside class="main-sidebar sidebar-dark-primary elevation-4">
                <Link class="brand-link"
                         :href="route('dashboard')" 
                         :active="route().current('/dashboard')">
                    <img :src="AdminLTELogo" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
                    <span class="brand-text font-weight-light">AdminLTE 3</span>
                </Link>

                <!-- LEFT SIDEBAR -->
                <SidebarLeft/>

            </aside>

            <!-- Content -->
            <div class="content-wrapper">
                <slot />

            </div>

            <!-- Left Sidebar -->
            <aside class="control-sidebar control-sidebar-dark">

                <div class="p-3">
                    <h5>Title</h5>
                    <p>Sidebar content</p>
                </div>
            </aside>


            <footer class="main-footer">

                <div class="float-right d-none d-sm-inline">
                    Anything you want
                </div>

                <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
            </footer>
        </div>
        
    </div>
</template>
