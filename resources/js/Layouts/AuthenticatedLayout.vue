<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav class="bg-gradient-to-r from-blue-500 to-blue-700 text-white shadow-lg relative">
                <!-- Gradient background for pattern -->
                <div
                    class="absolute inset-0 opacity-30"
                    style="background-image: linear-gradient(60deg, rgba(255, 255, 255, 0.1) 25%, transparent 25%, transparent 75%, rgba(255, 255, 255, 0.1) 75%), linear-gradient(60deg, rgba(0, 0, 0, 0.1) 25%, transparent 25%, transparent 75%, rgba(0, 0, 0, 0.1) 75%); background-size: 15px 15px;"
                ></div>

                <!-- Main nav content -->
                <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        <!-- Left side -->
                        <div class="flex items-center">
                            <!-- Logo and title -->
                            <a :href="urls.home" class="flex items-center cursor-pointer">
                                <ApplicationLogo class="block h-10 w-auto fill-current text-white" />
                                <h1 class="text-2xl font-bold ms-3 leading-7">OnlySchool</h1>
                            </a>

                            <!-- Navigation Links -->
                            <div class="hidden sm:flex sm:ms-10 space-x-4">
                                <Link
                                    :href="urls.home"
                                    class="text-white hover:text-yellow-300 transition duration-300 ease-in-out text-base cursor-pointer"
                                >
                                    Sākums
                                </Link>
                                <Link
                                    :href="urls.dienasgramata"
                                    class="text-white hover:text-yellow-300 transition duration-300 ease-in-out text-base cursor-pointer"
                                >
                                    Dienasgrāmata
                                </Link>
                                <Link
                                    :href="urls.kavejumi"
                                    class="text-white hover:text-yellow-300 transition duration-300 ease-in-out text-base cursor-pointer"
                                >
                                    Kavējumi
                                </Link>
                                <Link
                                    :href="urls.atzimes"
                                    class="text-white hover:text-yellow-300 transition duration-300 ease-in-out text-base cursor-pointer"
                                >
                                    Atzīmes
                                </Link>
                                <Link
                                    href="/conversations"
                                    class="text-white relative hover:text-yellow-300 transition duration-300 ease-in-out text-base cursor-pointer"
                                >
                                    Vēstules
                                    <span
                                        v-if="page.props.unreadMessageCount > 0"
                                        class="absolute right-0 top-0 inline-flex items-center justify-center h-5 w-5 rounded-full bg-red-500 text-xs font-bold text-white"
                                    >
                                        {{ page.props.unreadMessageCount }}
                                    </span>
                                </Link>
                            </div>
                        </div>

                        <!-- Right side -->
                        <div class="flex items-center space-x-3">
                            <!-- User info -->
                            <p class="text-base truncate max-w-xs">{{ page.props.auth.user.name }}</p>
                            <img
                                :src="profilePictureUrl"
                                alt="Profile"
                                class="h-10 w-10 rounded-full border-2 border-white shadow-md"
                            />
                            <!-- Dropdown for profile and logout -->
                            <div class="relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button
                                            type="button"
                                            class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-2 py-1 text-sm font-medium leading-4 text-white transition duration-150 ease-in-out hover:bg-blue-700 focus:outline-none"
                                        >
                                            <svg
                                                class="h-4 w-4"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </button>
                                    </template>

                                    <template #content>
                                        <DropdownLink href="/profile">Profile</DropdownLink>
                                        <DropdownLink href="/logout" method="post" as="button"
                                            >Log Out</DropdownLink
                                        >
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Mobile menu button -->
                        <div class="flex sm:hidden">
                            <button
                                @click="mobileMenuOpen = !mobileMenuOpen"
                                type="button"
                                class="inline-flex items-center justify-center rounded-md p-2 text-white hover:text-yellow-300 hover:bg-blue-600 focus:outline-none focus:bg-blue-600 focus:text-yellow-300 transition duration-150 ease-in-out"
                            >
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path
                                        v-if="!mobileMenuOpen"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        v-else
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Mobile Menu -->
                <div v-if="mobileMenuOpen" class="sm:hidden">
                    <div class="px-2 pt-2 pb-3 space-y-1">
                        <Link
                            :href="urls.home"
                            class="block px-3 py-2 rounded-md text-base font-medium text-white hover:text-yellow-300 hover:bg-blue-600"
                        >
                            Sākums
                        </Link>
                        <Link
                            :href="urls.dienasgramata"
                            class="block px-3 py-2 rounded-md text-base font-medium text-white hover:text-yellow-300 hover:bg-blue-600"
                        >
                            Dienasgrāmata
                        </Link>
                        <Link
                            :href="urls.kavejumi"
                            class="block px-3 py-2 rounded-md text-base font-medium text-white hover:text-yellow-300 hover:bg-blue-600"
                        >
                            Kavējumi
                        </Link>
                        <Link
                            :href="urls.atzimes"
                            class="block px-3 py-2 rounded-md text-base font-medium text-white hover:text-yellow-300 hover:bg-blue-600"
                        >
                            Atzīmes
                        </Link>
                        <Link
                            href="/conversations"
                            class="block px-3 py-2 rounded-md text-base font-medium text-white hover:text-yellow-300 hover:bg-blue-600 relative"
                        >
                            Vēstules
                            <span
                                v-if="page.props.unreadMessageCount > 0"
                                class="absolute right-0 top-0 inline-flex items-center justify-center h-5 w-5 rounded-full bg-red-500 text-xs font-bold text-white"
                            >
                                {{ page.props.unreadMessageCount }}
                            </span>
                        </Link>
                    </div>
                    <div class="pt-4 pb-3 border-t border-blue-700">
                        <div class="flex items-center px-5">
                            <div class="flex-shrink-0">
                                <img
                                    :src="profilePictureUrl"
                                    alt="Profile"
                                    class="h-10 w-10 rounded-full border-2 border-white shadow-md"
                                />
                            </div>
                            <div class="ml-3">
                                <div class="text-base font-medium text-white truncate max-w-xs">
                                    {{ page.props.auth.user.name }}
                                </div>
                            </div>
                            <div class="ml-auto">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button
                                            type="button"
                                            class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-2 py-1 text-sm font-medium leading-4 text-white transition duration-150 ease-in-out hover:bg-blue-700 focus:outline-none"
                                        >
                                            <svg
                                                class="h-4 w-4"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </button>
                                    </template>

                                    <template #content>
                                        <DropdownLink href="/profile">Profile</DropdownLink>
                                        <DropdownLink href="/logout" method="post" as="button"
                                            >Log Out</DropdownLink
                                        >
                                    </template>
                                </Dropdown>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <header class="bg-white shadow" v-if="$slots.header">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <main>
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const page = usePage();
const mobileMenuOpen = ref(false);

// Ensure that 'urls' is properly unwrapped in the template
const urls = computed(() => {
    const isTeacher = page.props.auth.user.role === 'admin';
    return {
        home: isTeacher ? '/TeacherClasses' : '/dashboard',
        dienasgramata: isTeacher ? '/TeacherClasses' : '/dienasgramata',
        kavejumi: isTeacher ? '/TeacherAbsences' : '/kavejumi',
        atzimes: isTeacher ? '/TeacherAbsences' : '/atzimes',
    };
});

// Use 'toRefs' to make properties reactive in the template
const { home, dienasgramata, kavejumi, atzimes } = urls.value;

const profilePictureUrl = computed(() => {
    const profilePath = page.props.auth.user.profilePicturePath;
    return profilePath ? `/storage/${profilePath}` : '/images/default-profile.jpg';
});
</script>
