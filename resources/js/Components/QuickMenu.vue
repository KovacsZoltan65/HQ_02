<script setup>
    import { Link } from '@inertiajs/vue3';

</script>

<template>
    <ul class="navbar-nav">

        <!-- HEMBURGER -->
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" 
                href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>

        <!-- HOME -->
        <li class="nav-item d-none d-sm-inline-block">
            <Link :href="route('dashboard')" 
                  class="nav-link"
            >Home</Link>
        </li>

        <!-- CONTACT -->
        <li class="nav-item d-none d-sm-inline-block">
            <Link :href="route('dashboard')" 
                  class="nav-link"
            >Contact</Link>
        </li>
    </ul>
</template>