<template>
    <div>
        <div class="min-h-screen bg-gray-50">
            <nav class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white shadow-xl relative">
                <!-- Gradient background pattern -->
                <div
                    class="absolute inset-0 opacity-20"
                    style="background-image: radial-gradient(circle at 1px 1px, rgba(255, 255, 255, 0.15) 1px, transparent 0); background-size: 20px 20px;"
                ></div>

                <!-- Main nav content -->
                <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-20">
                        <!-- Left side -->
                        <div class="flex items-center">
                            <!-- Logo and title -->
                            <a :href="urls.home" class="flex items-center cursor-pointer group">
                                <ApplicationLogo class="block h-12 w-auto fill-current text-white transition-transform duration-300 group-hover:scale-110" />
                                <h1 class="text-2xl font-bold ms-4 leading-7 tracking-wide">
                                    OnlySchool
                                    <span class="block text-xs text-blue-200 font-normal">School Management System</span>
                                </h1>
                            </a>

                            <!-- Navigation Links -->
                            <div class="hidden sm:flex sm:ms-12 space-x-8">
                                <Link
                                    v-for="(link, name) in visibleNavigationLinks"
                                    :key="name"
                                    :href="link.url"
                                    class="relative group px-3 py-2"
                                >
                                    <span class="relative z-10 text-white group-hover:text-yellow-300 transition duration-300 ease-in-out text-base font-medium cursor-pointer">
                                        {{ link.text }}
                                        <span
                                            v-if="name === 'vestules' && page.props.unreadMessageCount > 0"
                                            class="absolute -right-4 -top-2 inline-flex items-center justify-center h-5 w-5 rounded-full bg-red-500 text-xs font-bold"
                                        >
                                            {{ page.props.unreadMessageCount }}
                                        </span>
                                    </span>
                                    <span class="absolute bottom-0 left-0 w-full h-0.5 bg-yellow-300 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></span>
                                </Link>
                            </div>
                        </div>

                        <!-- Right side -->
                        <div class="flex items-center space-x-4">
                            <!-- User info with hover effect -->
                            <div class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-blue-700/50 transition duration-300">
                                <div class="flex flex-col items-end">
                                    <p class="text-sm font-medium truncate max-w-xs">{{ page.props.auth.user.name }}</p>
                                    <span class="text-xs text-blue-200">{{ isTeacher ? 'Teacher' : 'Student' }}</span>
                                </div>
                                <img
                                    :src="profilePictureUrl"
                                    alt="Profile"
                                    class="h-10 w-10 rounded-full border-2 border-white/80 shadow-md hover:scale-110 transition-transform duration-300"
                                />
                                <!-- Dropdown -->
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button
                                            type="button"
                                            class="inline-flex items-center rounded-md border border-transparent bg-blue-600/50 px-3 py-2 text-sm font-medium leading-4 text-white transition duration-300 ease-in-out hover:bg-blue-500/50 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 focus:ring-offset-blue-600"
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
                                        <DropdownLink href="/profile" class="hover:bg-gray-100">Profile</DropdownLink>
                                        <DropdownLink href="/logout" method="post" as="button" class="hover:bg-gray-100">Log Out</DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Mobile menu button -->
                        <div class="flex sm:hidden">
                            <button
                                @click="mobileMenuOpen = !mobileMenuOpen"
                                type="button"
                                class="inline-flex items-center justify-center rounded-lg p-2 text-white hover:text-yellow-300 hover:bg-blue-600/50 focus:outline-none focus:ring-2 focus:ring-blue-400 transition duration-300 ease-in-out"
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
                    <div class="px-2 pt-2 pb-3 space-y-1 bg-blue-700/30 backdrop-blur-sm">
                        <Link
                            v-for="(link, name) in visibleNavigationLinks"
                            :key="name"
                            :href="link.url"
                            class="block px-4 py-3 rounded-lg text-base font-medium text-white hover:text-yellow-300 hover:bg-blue-600/50 transition duration-300"
                        >
                            {{ link.text }}
                            <span
                                v-if="name === 'vestules' && page.props.unreadMessageCount > 0"
                                class="ms-2 inline-flex items-center justify-center h-5 w-5 rounded-full bg-red-500 text-xs font-bold"
                            >
                                {{ page.props.unreadMessageCount }}
                            </span>
                        </Link>
                    </div>
                    <div class="pt-4 pb-3 border-t border-blue-700/30 bg-blue-700/30 backdrop-blur-sm">
                        <div class="flex items-center px-5">
                            <div class="flex-shrink-0">
                                <img
                                    :src="profilePictureUrl"
                                    alt="Profile"
                                    class="h-10 w-10 rounded-full border-2 border-white/80 shadow-md"
                                />
                            </div>
                            <div class="ml-3">
                                <div class="text-base font-medium text-white truncate max-w-xs">
                                    {{ page.props.auth.user.name }}
                                </div>
                                <div class="text-sm text-blue-200">
                                    {{ isTeacher ? 'Teacher' : 'Student' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Header -->
            <header class="bg-white shadow-md" v-if="$slots.header">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Main content -->
            <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
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

const isTeacher = computed(() => page.props.auth.user.role === 'admin');

// Base URLs
const urls = computed(() => ({
    home: isTeacher.value ? '/TeacherClasses' : '/dashboard',
    dienasgramata: '/dienasgramata',
    kavejumi: '/kavejumi',
    atzimes: '/atzimes',
    teacherOverview: '/TeacherAbsences'
}));

// All possible navigation links
const navigationLinks = computed(() => ({
    // Student-specific links
    home: { 
        url: urls.value.home, 
        text: 'Sākums',
        studentOnly: true
    },
    dienasgramata: { 
        url: urls.value.dienasgramata, 
        text: 'Dienasgrāmata',
        studentOnly: true
    },
    kavejumi: { 
        url: urls.value.kavejumi, 
        text: 'Kavējumi', 
        studentOnly: true 
    },
    atzimes: { 
        url: urls.value.atzimes, 
        text: 'Atzīmes', 
        studentOnly: true 
    },
    
    // Teacher-specific links
    classes: { 
        url: urls.value.home, 
        text: 'Klases', 
        teacherOnly: true 
    },
    overview: { 
        url: urls.value.teacherOverview, 
        text: 'Pārskats', 
        teacherOnly: true 
    },
    
    // Common links
    vestules: { 
        url: '/conversations', 
        text: 'Vēstules' 
    }
}));

// Filter navigation links based on user role
const visibleNavigationLinks = computed(() => {
    return Object.entries(navigationLinks.value).reduce((acc, [key, link]) => {
        if (
            (!link.teacherOnly || isTeacher.value) && 
            (!link.studentOnly || !isTeacher.value)
        ) {
            acc[key] = link;
        }
        return acc;
    }, {});
});

const profilePictureUrl = computed(() => {
    const profilePath = page.props.auth.user.profilePicturePath;
    return profilePath ? `/storage/${profilePath}` : '/images/default-profile.jpg';
});
</script>