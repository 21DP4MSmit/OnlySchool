<template>
  <div class="space-y-4">
    <div class="flex flex-col items-center">
      <img
        v-if="currentPicture"
        :src="currentPicture"
        alt="Profile Picture"
        class="h-32 w-32 rounded-full object-cover ring-4 ring-white shadow-lg"
      />
      <div v-else class="h-32 w-32 rounded-full bg-gray-200 flex items-center justify-center">
        <span class="text-4xl text-gray-400">👤</span>
      </div>
      
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
        class="mt-4"
      >
        <span v-if="currentPicture">Mainīt attēlu</span>
        <span v-else>Pievienot attēlu</span>
      </PrimaryButton>
    </div>
    
    <div 
      v-if="status" 
      class="mt-2 rounded-md bg-green-50 p-3 text-sm text-green-600 text-center"
    >
      {{ status }}
    </div>
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
  