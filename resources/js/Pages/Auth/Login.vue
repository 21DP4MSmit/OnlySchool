<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in" />

    <div class="flex min-h-screen bg-gray-100">
        <!-- Blue Section with Login Form & Welcome Text -->
        <div class="flex flex-col items-center justify-center w-full lg:w-1/2 px-6 py-12 bg-blue-600">
            <div class="text-center p-6 text-white mb-8">
                <h2 class="text-3xl font-bold mb-2">Welcome to School System</h2>
                <p class="text-lg">Access your student portal, assignments, and more.</p>
            </div>

            <form @submit.prevent="submit" class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
                <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
                    {{ status }}
                </div>

                <div>
                    <InputLabel for="email" value="Email" />
                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="mt-6">
                    <InputLabel for="password" value="Password" />
                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="mt-6 block">
                    <label class="flex items-center">
                        <Checkbox name="remember" v-model:checked="form.remember" />
                        <span class="ml-2 text-sm text-gray-700">Remember me</span>
                    </label>
                </div>

                <div class="mt-6 flex items-center justify-between">
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="rounded-md text-sm text-gray-600 underline hover:text-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        Forgot your password?
                    </Link>

                    <PrimaryButton
                        class="ml-4 bg-blue-600 text-white hover:bg-blue-700 transition duration-300 ease-in-out"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Log in
                    </PrimaryButton>
                    <PrimaryButton
                        class="ms-4"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        <Link :href="route('register')">Register</Link>
                    </PrimaryButton>
                </div>
            </form>
        </div>

        <!-- News Section on White Background -->
        <div class="hidden lg:flex flex-col items-start justify-start w-1/2 bg-white p-10">
            <h3 class="text-2xl font-bold text-gray-800 mb-6">School News & Updates</h3>
            <div class="space-y-4">
                <div class="p-4 bg-gray-100 rounded-md shadow-sm">
                    <h4 class="text-lg font-semibold text-blue-600">New Semester Starts October 20th</h4>
                    <p class="text-sm text-gray-700">Ensure you have all your materials ready. Check the student portal for the updated schedule.</p>
                </div>
                <div class="p-4 bg-gray-100 rounded-md shadow-sm">
                    <h4 class="text-lg font-semibold text-blue-600">Parent-Teacher Meetings</h4>
                    <p class="text-sm text-gray-700">Meetings will be held virtually on November 10th. Check your email for your time slot.</p>
                </div>
                <div class="p-4 bg-gray-100 rounded-md shadow-sm">
                    <h4 class="text-lg font-semibold text-blue-600">Join the Coding Club!</h4>
                    <p class="text-sm text-gray-700">Learn programming and more. Meetings every Friday at 4 PM in Room 204.</p>
                </div>
            </div>
        </div>
    </div>
</template>

