<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head } from '@inertiajs/vue3';
    import DayTable from '@/Components/TeacherDayTable.vue';

    const days = ['Pirmdiena', 'Otrdiena', 'Trešdiena', 'Ceturtdiena', 'Piektdiena', 'Sestdiena', 'Svētdiena'];
</script>

<template>
    <Head title="Stundu Saraksts" />

    <AuthenticatedLayout>
        <div class="flex flex-col items-center h-screen pt-4">
            <!-- Centered and given padding-top -->
            <div class="w-full max-w-7xl h-[calc(100vh-120px)] overflow-auto p-4 bg-white rounded-lg shadow-md">
                <!-- Adjusted height to prevent overall page scroll -->
                <div class="space-y-4">
                    <DayTable v-for="(day, index) in days" :key="index" :day="day" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
    /* Hide body scrollbars */
    body {
        overflow: hidden; /* Prevent body scroll */
    }

    /* Hide the scrollbars in the content area (optional) */
    .overflow-auto::-webkit-scrollbar {
        display: none; /* Hide scrollbar for Webkit browsers */
    }

    .overflow-auto {
        -ms-overflow-style: none; /* IE and Edge */
        scrollbar-width: none; /* Firefox */
    }
</style>
