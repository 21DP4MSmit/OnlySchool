<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav class="bg-gradient-to-r from-blue-500 to-blue-700 text-white shadow-lg py-4 px-6 relative">
                <div class="absolute inset-0 opacity-30" style="background-image: linear-gradient(60deg, rgba(255, 255, 255, 0.1) 25%, transparent 25%, transparent 75%, rgba(255, 255, 255, 0.1) 75%), linear-gradient(60deg, rgba(0, 0, 0, 0.1) 25%, transparent 25%, transparent 75%, rgba(0, 0, 0, 0.1) 75%); background-size: 15px 15px;"></div>

                <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center">
                            <a href="#" class="flex items-center cursor-pointer">
                                <ApplicationLogo class="block h-10 w-auto fill-current text-white" />
                                <h1 class="text-3xl font-bold ms-3 leading-7">OnlySchool</h1>
                            </a>

                            <div class="hidden space-x-8 sm:flex sm:ms-10">
                                <Link href="/dashboard" class="text-white hover:text-yellow-300 transition duration-300 ease-in-out text-lg cursor-pointer">
                                    Sākums
                                </Link>
                                <Link href="/dienasgramata" class="text-white hover:text-yellow-300 transition duration-300 ease-in-out text-lg cursor-pointer">
                                    Dienasgrāmata
                                </Link>
                                <Link href="/kavejumi" class="text-white hover:text-yellow-300 transition duration-300 ease-in-out text-lg cursor-pointer">
                                    Kavējumi
                                </Link>
                                <Link href="/atzimes" class="text-white hover:text-yellow-300 transition duration-300 ease-in-out text-lg cursor-pointer">
                                    Atzīmes
                                </Link>
                                <Link href="/conversations" class="text-white relative hover:text-yellow-300 transition duration-300 ease-in-out text-lg cursor-pointer">
                                    Vēstules
                                    <span v-if="page.props.unreadMessageCount > 0" class="absolute right-0 top-0 inline-flex items-center justify-center h-5 w-5 rounded-full bg-red-500 text-xs font-bold text-white">
                                        {{ page.props.unreadMessageCount }}
                                    </span>
                                </Link>
                            </div>
                        </div>

                        <div class="flex items-center space-x-6">
                            <p class="text-lg">{{ page.props.auth.user.name }}!</p>
                            <!-- Display user profile picture -->
                            <img :src="profilePictureUrl" alt="Profile" class="h-10 w-10 rounded-full border-2 border-white shadow-md" />
                            <div class="relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button
                                            type="button"
                                            class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-3 py-2 text-sm font-medium leading-4 text-white transition duration-150 ease-in-out hover:bg-blue-700 focus:outline-none"
                                        >
                                            <svg class="-me-0.5 ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </template>

                                    <template #content>
                                        <DropdownLink href="/profile">Profile</DropdownLink>
                                        <DropdownLink href="/logout" method="post" as="button">Log Out</DropdownLink>
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
import { computed } from 'vue';
import { usePage, Link } from '@inertiajs/vue3'; // Import usePage to access Inertia props
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const page = usePage();

console.log('Unread Message Count:', page.props.unreadMessageCount);
const profilePictureUrl = computed(() => {
    const profilePath = page.props.auth.user.profilePicturePath;
    if (profilePath) {
        return `/storage/${profilePath}`;
    }
    return '/images/default-profile.jpg';
});
</script>
