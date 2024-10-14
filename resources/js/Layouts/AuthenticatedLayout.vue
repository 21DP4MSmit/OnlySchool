<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav class="border-b border-gray-100 bg-blue-600 text-white">
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between items-center">
                        <div class="flex">
                            <!-- Logo and School Name -->
                            <div class="flex shrink-0 items-center">
                                <ApplicationLogo class="block h-9 w-auto fill-current text-white" />
                                <h1 class="text-2xl font-bold ms-3">OnlySchool</h1>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <NavLink :href="route('dashboard')" class="text-white">
                                    Dienasgrāmata
                                </NavLink>
                                <NavLink href="#" class="text-white">
                                    Kavējumi
                                </NavLink>
                                <NavLink href="#" class="text-white">
                                    Atzīmes
                                </NavLink>
                                <NavLink href="#" class="text-white">
                                    Jaunumi
                                </NavLink>
                                <NavLink href="#" class="text-white relative">
                                    Vēstules 
                                    <span class="absolute right-0 top-0 inline-flex items-center justify-center h-5 w-5 rounded-full bg-red-500 text-xs font-bold">+99</span>
                                </NavLink>
                            </div>
                        </div>

                        <!-- User Greeting and Profile Section -->
                        <div class="flex items-center space-x-4">
                            <p class="text-lg">Sveiks, {{ $page.props.auth.user.name }}!</p>
                            <!-- Profile Picture -->
                            <img src="/path-to-profile-picture.jpg" alt="Profile" class="h-10 w-10 rounded-full" />
                            <!-- Settings Dropdown -->
                            <div class="relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-3 py-2 text-sm font-medium leading-4 text-white transition duration-150 ease-in-out hover:bg-blue-700 focus:outline-none"
                                            >
                                                <svg class="-me-0.5 ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button">Log Out</DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
