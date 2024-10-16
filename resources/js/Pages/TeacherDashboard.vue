<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import TeacherLessonsTable from '@/Components/TeacherLessonsTable.vue';
import TeacherUnreadMessages from '@/Components/TeacherUnreadMessages.vue';
</script>

<template>
    <Head title="Teacher Dashboard" />

    <AuthenticatedLayout>
        <div class="h-screen flex items-center justify-center"> <!-- Centered vertically and horizontally -->
            <div class="flex w-full max-w-7xl h-4/5"> <!-- Adjusted height to 4/5 of the screen -->
                <div class="w-3/4 h-full p-4 overflow-auto"> <!-- Left component -->
                    <TeacherLessonsTable />
                </div>

                <div class="w-1/4 h-full p-4 overflow-auto"> <!-- Right component -->
                    <TeacherUnreadMessages />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
