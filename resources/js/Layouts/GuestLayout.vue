<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="row g-0 flex-fill">
        <div class="col-12 col-lg-6 col-xl-4 border-top-wide border-primary d-flex flex-column justify-content-center">
            <div class="container container-tight my-5 px-lg-5">
                <div class="text-center mb-2">
                    <a href="." class="navbar-brand navbar-brand-autodark" style="font-size: 32px;">
                        Музыка Барокко
                    </a>
                </div>
                <slot />
            </div>
        </div>
        <div class="col-12 col-lg-6 col-xl-8 d-none d-lg-block">
            <!-- Photo -->
            <div class="bg-cover h-100 min-vh-100"
                style="background-image: url(https://cmbv.fr/sites/default/files/cmbv/styles/16x9_1920/public/ged/the-concert-oil-on-canvas-nicolas-tournier.jpg?itok=lOFsXcc3)">
            </div>
        </div>
    </div>
</template>
