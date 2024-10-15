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
          <p>(Tabula)</p>
        </div>
      </div>

      <!-- Student Information -->
      <div class="right-section">
        <!-- Student Info -->
        <div class="student-info">
          <img class="profile-pic" src="https://facts.net/wp-content/uploads/2023/10/22-astounding-facts-about-johnny-sins-1696433310.jpg" alt="Student Picture" />
          <h2>Sveiks, {{ student.name }}!</h2>
          <p>{{ student.class }}</p>
        </div>

        <!-- Logbook Entries -->
        <div class="logbook">
          <h3>Ieraksti dienasgrāmatā:</h3>
          <div class="logbook-content">
            (Uzrādas izmaiņas dienasgrāmatā: stundu izmaiņas, skolotāju maiņas, jaunas atzīmes, kavējumu atzīmējumi, piezīmes)
          </div>
        </div>

        <!-- Monthly Absences -->
        <div class="absences">
          <h3>Mēneša kavējumi:</h3>
          <div class="absences-content">
            (Tabula ar stundām, kurās un cik ir kavējumi)
          </div>
        </div>

        <!-- Upcoming Tests -->
        <div class="tests">
          <h3>Nākamie kontroldarbi:</h3>
          <div class="tests-content">
            (Skolotājam nākamo 7 dienu periodā stundā atzīmējot kontroldarbu, tas rādas šeit)
          </div>
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
      required: true,
    },
  },
};
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