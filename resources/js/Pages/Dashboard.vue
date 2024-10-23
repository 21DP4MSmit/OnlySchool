<template>
    <AuthenticatedLayout>
      <div class="student-dashboard-page">
        <StudentDashboard :currentDate="currentDate" :currentTime="currentTime" :student="student" />
      </div>
    </AuthenticatedLayout>
  </template>
  
  <script>
  import StudentDashboard from '@/Components/StudentDashboard.vue';
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
  
  export default {
    components: {
      StudentDashboard,
      AuthenticatedLayout,
    },
  };
  </script>
  
  