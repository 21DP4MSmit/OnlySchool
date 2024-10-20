<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Sekmes from '@/Components/Sekmes.vue';

</script>

<template>
    <Head title="home" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div id="app">
                        <Sekmes />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
