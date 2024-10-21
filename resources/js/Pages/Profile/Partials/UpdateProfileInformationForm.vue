<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    email: user.email,
    phoneNumber: user.phoneNumber,
    profile_picture: null, // Initialize the file input
});

// Handle file upload
const handleFileUpload = (event) => {
    const file = event.target.files[0];
    form.setData('profile_picture', file); // Set the file in form data
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                Profila Informācija
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Atjauniniet sava profila informāciju            </p>
        </header>

        <form
            @submit.prevent="form.patch(route('profile.update'))"
            class="mt-6 space-y-6"
        >

            <div>
                <InputLabel for="name" value="Vārds" />
                <div 
                    id="name" 
                    class="mt-1 block w-full disabled border border-gray-300 rounded-md p-2 bg-gray-100" 
                    v-text="user.name"
                    role="presentation"
                ></div>
                <InputError class="mt-2" :message="form.errors.name" />
            </div>
            <div>
                <InputLabel for="class" value="Klase" />
                <div 
                    id="name" 
                    class="mt-1 block w-full disabled border border-gray-300 rounded-md p-2 bg-gray-100" 
                    v-text="user.class_id"
                    role="presentation"
                ></div>
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="E-pasts" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>
            <div>
                <InputLabel for="phoneNumber" value="Tālruņa numurs" />
                <TextInput
                    id="phoneNumber"
                    class="mt-1 block w-full"
                    v-model="form.phoneNumber"
                    required
                    autocomplete="tel"
                />
                <InputError class="mt-2" :message="form.errors.phoneNumber" />
            </div>

            <div>
                <InputLabel for="profile_picture" value="Profila bilde" />
                <input 
                    type="file" 
                    id="profile_picture" 
                    class="mt-1 block w-full" 
                    @change="handleFileUpload" 
                    accept="image/*"
                />
                <InputError class="mt-2" :message="form.errors.profile_picture" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-gray-800">
                    Your email address is unverified.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Click here to re-send the verification email.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600"
                >
                    A new verification link has been sent to your email address.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Saglabāt</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Saglabāts!</p>
                    <p v-else-if="form.processing" class="text-sm text-gray-600">Sūta...</p>
                </Transition>
            </div>
        </form>
    </section>
</template>
