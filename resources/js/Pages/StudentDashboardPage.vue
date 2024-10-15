<template>
  <AuthenticatedLayout>
    <div class="student-dashboard">
      <!-- Pass the inherited data to the StudentDashboard component -->
      <StudentDashboard :currentDate="currentDate" :currentTime="currentTime" :student="student" />
    </div>
  </AuthenticatedLayout>
</template>

<script>
import StudentDashboard from '@/Components/StudentDashboard.vue';

export default {
  components: {
    StudentDashboard,
  },
  computed: {
    // Assume that the AuthenticatedLayout provides the 'student' object with name and class
    student() {
      return this.$attrs.student || { name: 'Unknown', class: 'Unknown' }; // Fallback if no data is provided
    },
  },
  data() {
    return {
      currentDate: new Date().toLocaleDateString(),
      currentTime: new Date().toLocaleTimeString(),
    };
  },
};
</script>
