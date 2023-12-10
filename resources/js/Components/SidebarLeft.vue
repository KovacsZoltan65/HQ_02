<script setup>
    import Menu from '../Components/menu/Menu.vue';
    import user2 from '@/../../public/dist/img/user2-160x160.jpg';
</script>

<template>
    <div class="sidebar">

        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img :src="user2" 
                     class="img-circle elevation-2" 
                     alt="User Image">
            </div>
            <div class="info">
                <a href="#" 
                   class="d-block">Alexander Pierce</a>
            </div>
        </div>

        <!-- Sidebar Search -->
        <div class="form-inline">
            <div class="input-group" 
                 data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" 
                       type="search" 
                       placeholder="Search" 
                       aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- SIDEBAR MENU -->
        <Menu />

    </div>
</template>