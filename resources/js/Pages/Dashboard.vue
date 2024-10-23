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
      currentDate: new Date().toLocaleDateString(),
      currentTime: new Date().toLocaleTimeString(),
      student: {
        id: 1, // Replace with actual student data
        name: 'John Doe', // Replace with actual student data
      },
    };
  },
};
</script>
