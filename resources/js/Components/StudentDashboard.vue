<template>
  <div class="student-dashboard">
    <!-- Additional Bar Section -->
    <div class="header-bar"></div>

    <!-- Main Content Section -->
    <div class="content">
      <!-- Timetable -->
      <div class="left-section">
        <h2>Šodienas stundu saraksts:</h2>
        <div class="timetable">
          <ul>
            <li v-for="lesson in todayTimetable" :key="lesson.id">{{ lesson.subject }} - {{ lesson.time }}</li>
          </ul>
        </div>
      </div>

      <!-- Student Information -->
      <div class="right-section">

        <!-- Logbook Entries -->
        <div class="logbook">
          <h3>Pēdējās atzīmes:</h3>
          <div class="logbook-content">
            <ul>
              <li v-for="grade in recentGrades" :key="grade.id">{{ grade.subject }} - {{ grade.grade }} ({{ grade.date }})</li>
            </ul>
          </div>
        </div>
        <!-- Monthly Absences -->
        <div class="absences">
          <h3>Mēneša kavējumi:</h3>
          <div class="absences-content">
            <ul>
              <li v-for="absence in monthlyAbsences" :key="absence.id">{{ absence.date }} - {{ absence.reason }}</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const todayTimetable = ref([]);
const recentGrades = ref([]);
const monthlyAbsences = ref([]);

onMounted(() => {
  // Fetch today's timetable
  axios.get('/api/timetable/today').then(response => {
    todayTimetable.value = response.data;
  });

  // Fetch the 4-5 most recent grades
  axios.get('/api/grades/recent').then(response => {
    recentGrades.value = response.data;
  });

  // Fetch absences for the current month
  axios.get('/api/absences/monthly').then(response => {
    monthlyAbsences.value = response.data;
  });
});
</script>

<style scoped>
.student-dashboard {
  background-color: #f0f0f0;
  padding: 20px;
  font-family: Arial, sans-serif;
}

.logout {
  color: white;
  text-decoration: none;
}

.content {
  display: flex;
}

.left-section {
  flex: 3;
  border: 1px solid black;
  height: 500px;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 24px;
  padding: 20px;
  background-color: white;
  margin-right: 20px;
}

.right-section {
  flex: 2;
  display: flex;
  flex-direction: column;
  align-items: flex-end;
}

.student-info {
  text-align: right;
  margin-bottom: 20px;
}

.profile-pic {
  width: 150px;
  height: 150px;
  border-radius: 50%;
  margin-bottom: 10px;
}

.logbook, .absences, .tests {
  border: 1px solid black;
  padding: 20px;
  background-color: white;
  margin-bottom: 20px;
  width: 100%;
}

h2, h3 {
  margin-bottom: 10px;
}
</style>
