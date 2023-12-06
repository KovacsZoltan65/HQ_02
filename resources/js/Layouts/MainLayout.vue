<script setup>
    import { ref } from 'vue';
    import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div id="MainLayout" class="w-full h-screen">
        <div>
            Main Layout
        </div>

        <slot />
        
    </div>
</template>
