<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import ProfilePictureUpload from './Partials/ProfilePictureUpload.vue'; 
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <div class="min-h-screen bg-gray-50 py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Page Header -->
                <div class="mb-8 text-center">
                    <h1 class="text-3xl font-bold tracking-tight text-gray-900">
                        Profila iestatījumi
                    </h1>
                    <p class="mt-2 text-sm text-gray-600">
                        Pārvaldiet savu profila informāciju un drošības iestatījumus
                    </p>
                </div>

                <!-- Main Content Grid -->
                <div class="grid gap-8 md:grid-cols-12">
                    <!-- Left Column - Profile Picture -->
                    <div class="md:col-span-4">
                        <div class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-gray-900/5">
                            <div class="p-6">
                                <ProfilePictureUpload />
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Forms -->
                    <div class="space-y-8 md:col-span-8">
                        <!-- Profile Information Form -->
                        <div class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-gray-900/5">
                            <div class="p-6">
                                <UpdateProfileInformationForm
                                    :must-verify-email="mustVerifyEmail"
                                    :status="status"
                                />
                            </div>
                        </div>

                        <!-- Password Update Form -->
                        <div class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-gray-900/5">
                            <div class="p-6">
                                <UpdatePasswordForm />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mobile Layout (Profile sections stack vertically) -->
                <div class="mt-8 space-y-8 md:hidden">
                    <div class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-gray-900/5">
                        <div class="p-6">
                            <ProfilePictureUpload />
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-gray-900/5">
                        <div class="p-6">
                            <UpdateProfileInformationForm
                                :must-verify-email="mustVerifyEmail"
                                :status="status"
                            />
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-gray-900/5">
                        <div class="p-6">
                            <UpdatePasswordForm />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>