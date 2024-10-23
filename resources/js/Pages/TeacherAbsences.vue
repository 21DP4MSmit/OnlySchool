<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head } from '@inertiajs/vue3';

</script>

<template>
    <Head title="Teacher Absences" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div id="app">
                        <TeacherAbsencesMark :subjectLists="subjectLists" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
