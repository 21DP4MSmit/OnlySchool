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
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
      <!-- Today's Timetable -->
      <div class="bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-3xl font-semibold text-gray-800 mb-4">Today's Timetable:</h2>
        <ul>
          <li v-for="lesson in todayTimetable" :key="lesson.id" class="mb-2">
            <span class="font-bold">{{ lesson.subject }}</span> - {{ lesson.time }}
          </li>
        </ul>
      </div>
  
      <!-- Recent Grades and Monthly Absences -->
      <div>
        <div class="bg-white p-6 rounded-lg shadow-lg mb-8">
          <h3 class="text-3xl font-semibold text-gray-800 mb-4">Recent Grades:</h3>
          <ul>
            <li v-for="grade in recentGrades" :key="grade.id" class="mb-2">
              {{ grade.subject }} - {{ grade.grade }} ({{ grade.date }})
            </li>
          </ul>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-lg">
          <h3 class="text-3xl font-semibold text-gray-800 mb-4">Monthly Absences:</h3>
          <ul>
            <li v-for="absence in monthlyAbsences" :key="absence.id" class="mb-2">
              {{ absence.date }} - {{ absence.reason }}
            </li>
          </ul>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      todayTimetable: Array,
      recentGrades: Array,
      monthlyAbsences: Array,
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
<!-- Page for testing pls ignore -->