<!-- Page for testing pls ignore -->
<!-- Page for testing pls ignore -->
<!-- Page for testing pls ignore -->
<!-- Page for testing pls ignore -->
<!-- Page for testing pls ignore -->
<!-- Page for testing pls ignore -->
<!-- Page for testing pls ignore -->
<!-- Page for testing pls ignore -->
<!-- Page for testing pls ignore -->
<!-- Page for testing pls ignore -->
<!-- Page for testing pls ignore -->
<!-- Page for testing pls ignore -->
<!-- Page for testing pls ignore -->
<template>
  <AuthenticatedLayout>
    <div class="container mx-auto px-4 py-8">
      <!-- Header with current date -->
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-4xl font-extrabold text-gray-900">Student Dashboard</h1>
        <span class="text-lg font-semibold text-gray-600 bg-gray-200 py-2 px-4 rounded-lg shadow-sm">
          {{ currentDate }}
        </span>
      </div>

      <!-- Navigation for previous/next week -->
      <div class="flex justify-between items-center mb-8">
        <button @click="prevWeek" class="text-4xl text-blue-600 font-bold">&lt;</button>
        <span class="text-lg font-semibold bg-blue-100 py-2 px-6 rounded-lg">
          {{ formattedWeekRange }}
        </span>
        <button @click="nextWeek" class="text-4xl text-blue-600 font-bold">&gt;</button>
      </div>

      <!-- StudentDashboard Component -->
      <StudentDashboard :todayTimetable="todayTimetable" :recentGrades="recentGrades" :monthlyAbsences="monthlyAbsences" />
    </div>
  </AuthenticatedLayout>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { startOfWeek, addDays, addWeeks, format } from 'date-fns';
import StudentDashboard from '@/Components/StudentDashboard.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import axios from 'axios';

export default {
  components: {
    StudentDashboard,
    AuthenticatedLayout,
  },
  data() {
    return {
      currentDate: new Date().toLocaleDateString(),
      currentWeekStart: startOfWeek(new Date(), { weekStartsOn: 1 }),
      todayTimetable: [],
      todayGrades: [],
      todayAbsences: [],
    };
  },
  computed: {
    formattedWeekRange() {
      const start = format(this.currentWeekStart, 'dd.MM.yyyy');
      const end = format(addDays(this.currentWeekStart, 4), 'dd.MM.yyyy');
      return `${start} - ${end}`;
    },
  },
  methods: {
    fetchData() {
      axios.get('/api/timetable/today').then(response => (this.todayTimetable = response.data));
      axios.get('/api/grades/recent').then(response => (this.recentGrades = response.data));
      axios.get('/api/absences/monthly').then(response => (this.monthlyAbsences = response.data));
    },
  },
  mounted() {
    this.fetchData();
  },
};
</script>
<!-- Page for testing pls ignore -->
<!-- Page for testing pls ignore -->
<!-- Page for testing pls ignore -->
<!-- Page for testing pls ignore -->
<!-- Page for testing pls ignore -->
<!-- Page for testing pls ignore -->
<!-- Page for testing pls ignore -->
<!-- Page for testing pls ignore -->
<!-- Page for testing pls ignore -->
<!-- Page for testing pls ignore -->
<!-- Page for testing pls ignore -->
<!-- Page for testing pls ignore -->
<!-- Page for testing pls ignore -->
<!-- Page for testing pls ignore -->