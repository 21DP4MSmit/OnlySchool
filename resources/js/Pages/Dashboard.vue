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
    data() {
      return {
        currentDate: new Date().toLocaleDateString('lv-LV', {
          weekday: 'long',
          year: 'numeric',
          month: 'numeric',
          day: 'numeric',
        }),
        currentTime: new Date().toLocaleTimeString('lv-LV', {
          hour: '2-digit',
          minute: '2-digit',
          second: '2-digit',
        }),
        student: {
          name: 'Džonis',
          class: '5.B',
        },
      };
    },
    mounted() {
      setInterval(() => {
        const now = new Date();
        this.currentDate = now.toLocaleDateString('lv-LV', {
          weekday: 'long',
          year: 'numeric',
          month: 'numeric',
          day: 'numeric',
        });
        this.currentTime = now.toLocaleTimeString('lv-LV', {
          hour: '2-digit',
          minute: '2-digit',
          second: '2-digit',
        });
      }, 1000);
    },
  };
  </script>
  
  <style scoped>
  .student-dashboard-page {
    background-color: #f0f0f0;
    padding: 20px;
  }
  </style>
  