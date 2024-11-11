<template>
    <div>
      <label for="profile_picture" class="block text-sm font-medium text-gray-700">Profila attēls</label>
      
      <div class="mt-2 flex items-center">
        <img
          v-if="currentPicture"
          :src="currentPicture"
          alt="Profile Picture"
          class="h-16 w-16 rounded-full object-cover mr-4"
        />
        
        <input
          ref="fileInput"
          type="file"
          id="profile_picture"
          class="hidden"
          @change="uploadPicture"
          accept="image/*"
        />
        
        <PrimaryButton
          :disabled="form.processing"
          @click="chooseFile"
          class="ml-4"
        >
          Izvēlies attēlu
        </PrimaryButton>
      </div>
  
      <div v-if="status" class="mt-2 text-sm text-green-600">{{ status }}</div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import { useForm } from '@inertiajs/vue3';
  import PrimaryButton from '@/Components/PrimaryButton.vue';
  
  const form = useForm({
    profile_picture: null, // Holds the file object
  });
  
  const currentPicture = ref(null); // For previewing the profile picture
  const status = ref(null);
  
  const uploadPicture = (event) => {
    const file = event.target.files[0];
    form.profile_picture = file; // Directly set the file to form property
  
    // Preview the image
    const reader = new FileReader();
    reader.onload = (e) => {
      currentPicture.value = e.target.result;
    };
    reader.readAsDataURL(file);
  
    form.post(route('profile.picture.update'), {
      forceFormData: true, // Make sure form data is sent as FormData (for file upload)
      onSuccess: () => {
        status.value = 'Profila attēls ir veiksmīgi atjaunināts!';
      },
      onError: (errors) => {
        console.log(errors);
      }
    });
  };
  
  const chooseFile = () => {
    document.getElementById('profile_picture').click();
  };
  </script>
  