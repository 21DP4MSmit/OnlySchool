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
});

</script>

<template>
    <div class="space-y-6">
      <div class="border-b border-gray-200 pb-6">
        <h2 class="text-xl font-semibold text-gray-900">
          Profila Informācija
        </h2>
        <p class="mt-1 text-sm text-gray-500">
          Atjauniniet sava profila informāciju
        </p>
      </div>
  
      <form @submit.prevent="form.patch(route('profile.update'))" class="space-y-6">
        <!-- Form fields with improved styling -->
        <div class="grid gap-6 md:grid-cols-2">
          <div>
            <InputLabel for="name" value="Vārds" class="text-gray-700" />
            <div 
              id="name" 
              class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 p-2.5 text-gray-900 shadow-sm" 
              v-text="user.name"
              role="presentation"
            ></div>
          </div>
  
          <div>
            <InputLabel for="class" value="Klase" class="text-gray-700" />
            <div 
              id="class" 
              class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 p-2.5 text-gray-900 shadow-sm" 
              v-text="user.class_id"
              role="presentation"
            ></div>
          </div>
        </div>
  
        <!-- Email and Phone fields -->
        <div class="grid gap-6 md:grid-cols-2">
          <div>
            <InputLabel for="email" value="E-pasts" class="text-gray-700" />
            <TextInput
              id="email"
              type="email"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              v-model="form.email"
              required
            />
            <InputError class="mt-2" :message="form.errors.email" />
          </div>
  
          <div>
            <InputLabel for="phoneNumber" value="Tālruņa numurs" class="text-gray-700" />
            <TextInput
              id="phoneNumber"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              v-model="form.phoneNumber"
              required
            />
            <InputError class="mt-2" :message="form.errors.phoneNumber" />
          </div>
        </div>
  
        <!-- Save Button and Status -->
        <div class="flex items-center justify-between pt-4">
          <PrimaryButton 
            :disabled="form.processing"
            class="w-32 justify-center"
          >
            Saglabāt
          </PrimaryButton>
  
          <Transition
            enter-active-class="transition ease-in-out duration-300"
            enter-from-class="opacity-0"
            leave-active-class="transition ease-in-out duration-300"
            leave-to-class="opacity-0"
          >
            <p 
              v-if="form.recentlySuccessful" 
              class="text-sm text-green-600"
            >
              Saglabāts!
            </p>
          </Transition>
        </div>
      </form>
    </div>
  </template>
