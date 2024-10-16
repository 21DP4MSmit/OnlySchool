<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head } from '@inertiajs/vue3';
    import TeacherAbsencesMark from '@/Components/TeacherAbsencesMark.vue';
</script>

<template>
    <Head title="Teacher Absences" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <TeacherAbsencesMark />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
