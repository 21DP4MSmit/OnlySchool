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
        <!-- Blue Gradient Section with Login Form & Welcome Text -->
        <div class="flex flex-col items-center justify-center w-full lg:w-1/2 px-6 py-12 bg-gradient-to-br from-blue-600 via-blue-500 to-blue-400">
            <div class="text-center p-6 text-white mb-8">
                <h2 class="text-4xl font-extrabold mb-2">Pieslēgties</h2>
                <p class="text-lg font-medium">temp text idk if its needed here</p>
            </div>

            <form @submit.prevent="submit" class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-sm">
                <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
                    {{ status }}
                </div>

                <div>
                    <InputLabel for="email" value="Email" />
                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full border border-gray-300 rounded-lg p-2 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
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
                        class="mt-1 block w-full border border-gray-300 rounded-lg p-2 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="mt-6 flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ml-2 text-sm text-gray-700">Remember me</span>
                </div>

                <div class="mt-8 flex items-center justify-between">
                    <PrimaryButton
                        class="bg-blue-600 text-white py-1 px-4 rounded-lg hover:bg-blue-700 transition duration-300 ease-in-out"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Log in
                    </PrimaryButton>

                    <Link :href="route('register')" class="ml-4 bg-gray-100 text-blue-600 py-1 px-4 rounded-lg border border-blue-600 hover:bg-blue-50 transition duration-300 ease-in-out">
                        Register
                    </Link>
                </div>

                <div class="mt-4 text-center">
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-sm text-blue-600 underline hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        Forgot your password?
                    </Link>
                </div>
            </form>
        </div>

        <!-- News Section on White Background with Silver Accents -->
        <div class="hidden lg:flex flex-col items-cente justify-start w-1/2 bg-white p-10">
            <h3 class="text-2xl font-bold text-gray-800 mb-6">Izglītībai & Atpūtai</h3>

            <!-- Larger Panel for the New News Item -->
            <a href="https://isic.lv/" target="_blank" class="mb-6 block w-full max-w-md p-6 bg-blue-50 border border-blue-300 rounded-lg shadow-lg hover:shadow-xl transition transform hover:scale-105 ">
                <img src="https://www.cnet.com/personal-finance/assets/uploads/2022/09/greenlight-debit-card.webp?auto=webp" alt="New Semester" class="w-full h-48 object-cover rounded-t-md mb-4">
                <div>
                    <h4 class="text-xl font-semibold text-blue-600">Izveido savu studenta ISIC karti</h4>
                    <p class="text-sm text-gray-700">
                        ISIC ir vienīgā starptautiski atzītā pilna un nepilna laika studenta karte.
                    </p>
                </div>
            </a>

            <!-- Three Existing Panels Below the Larger One -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <a href="https://jaunmokupils.lv/pils/pils-pakalpojumi/skolenu-un-turistu-grupam/" target="_blank" class="block p-4 bg-gray-50 border border-gray-200 rounded-md shadow-sm hover:shadow-md transition transform hover:scale-105">
                    <img src="https://jaunmokupils.lv/wp-content/uploads/2021/09/JaunmokuPils_No-augs-as_5_Web_Facebook.jpg" alt="Parent-Teacher Meetings" class="w-full h-32 object-cover rounded-t-md">
                    <div class="p-4">
                        <h4 class="text-lg font-semibold text-blue-600">Atpūties no skolas ar visu klasi</h4>
                        <p class="text-sm text-gray-700">
                            Iegādājies biļetes visai klasei un izbaudi ekskursiju ar klasesbiedriem.
                        </p>
                    </div>
                </a>

                <a href="https://chatgpt.com/" target="_blank" class="block p-4 bg-gray-50 border border-gray-200 rounded-md shadow-sm hover:shadow-md transition transform hover:scale-105">
                    <img src="https://miro.medium.com/v2/resize:fit:1400/1*GySLsSxAcKFmbmw43eO6Yg.png" alt="Join the Coding Club!" class="w-full h-32 object-cover rounded-t-md">
                    <div class="p-4">
                        <h4 class="text-lg font-semibold text-blue-600">Grūtības skolā?</h4>
                        <p class="text-sm text-gray-700">
                            Uzdod jautājumus gudrijama draugam, ChatGPT.
                        </p>
                    </div>
                </a>

                <a href="https://example.com/" target="_blank" class="block p-4 bg-gray-50 border border-gray-200 rounded-md shadow-sm hover:shadow-md transition transform hover:scale-105">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRz-vYaXn2T9tE1Zw9ab4gTaADctxFVJNGquw&s" alt="Placeholder Image" class="w-full h-32 object-cover rounded-t-md">
                    <div class="p-4">
                        <h4 class="text-lg font-semibold text-blue-600">temp text</h4>
                        <p class="text-sm text-gray-700">
                            temp texttemp texttemp texttemp text
                        </p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</template>








