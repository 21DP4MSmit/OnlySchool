<template>
  <div>
    <!-- Header Section -->
    <div class="header">
      <h1>OnlySchool</h1>
      <p class="date">{{ currentDate }}</p>
      <p class="time">{{ currentTime }}</p>
      <a class="logout" href="#">Iziet</a>
    </div>

    <!-- Additional Bar Section -->
    <div class="header-bar"></div>

    <!-- Main Content Section -->
    <div class="content">
      <!-- Timetable -->
      <div class="left-section">
        <h2>Šodienas stundu saraksts:</h2>
        <div class="timetable">
          <p>(Tabula)</p>
        </div>
      </div>

      <!-- Student Information -->
      <div class="right-section">
        <div class="student-info">
          <img class="profile-pic" src="https://example.com/student-picture.jpg" alt="Student Picture" />
          <h2>Sveiks, {{ student.name }}!</h2>
          <p>{{ student.class }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    currentDate: String,
    currentTime: String,
    student: {
      type: Object,
      required: true, // The student object is required to ensure the component works correctly
    },
  },
};
</script>
