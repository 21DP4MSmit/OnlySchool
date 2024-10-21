<template>
    <div>
      <label for="profile_picture" class="block text-sm font-medium text-gray-700">Profile Picture</label>
      
      <div class="mt-2 flex items-center">
        <img v-if="currentPicture" :src="currentPicture" alt="Profile Picture" class="h-16 w-16 rounded-full object-cover" />
        
        <input
          ref="fileInput"
          type="file"
          id="profile_picture"
          class="hidden"
          @change="uploadPicture"
          accept="image/*"
        />
        
        <button
          type="button"
          @click="chooseFile"
          class="ml-5 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
        >
          Choose Picture
        </button>
      </div>
  
      <div v-if="status" class="mt-2 text-sm text-green-600">{{ status }}</div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import { useForm } from '@inertiajs/vue3';
  
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
        status.value = 'Profile picture updated successfully!';
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
  