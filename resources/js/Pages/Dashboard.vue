<template>
  <AuthenticatedLayout>
    <div class="container mx-auto px-4 py-8">
      <!-- Header with current date -->
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-4xl font-extrabold text-gray-900">Sākumlapa</h1>
        <span class="text-lg font-semibold text-gray-600 bg-gray-200 py-2 px-4 rounded-lg shadow-sm">
          {{ currentDate }} (Šodien)
        </span>
      </div>

      <!-- Today's Timetable -->
      <div class="bg-white p-6 rounded-lg shadow-lg mb-8">
        <h2 class="text-3xl font-semibold text-gray-800 mb-4">Šodienas stundu saraksts:</h2>
        <div v-if="todayTimetable.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="(lesson, index) in todayTimetable"
            :key="lesson.ListID"
            class="bg-gradient-to-r from-blue-500 to-blue-700 text-white p-4 rounded-lg shadow-md transform hover:scale-105 transition duration-200"
          >
            <div class="flex items-center justify-between mb-2">
              <div class="text-sm font-semibold">
                {{ getTimeForLesson(index) }}
              </div>
              <div class="text-sm font-semibold">
                {{ lesson.subject.Name }}
              </div>
            </div>
            <div class="text-xs">
              <span class="font-semibold">Tēma:</span> {{ lesson.topic || 'Tēma nav pieejama' }}
            </div>
            <div class="text-xs mt-2">
              <span class="font-semibold">Mājasdarbs:</span>
              <span v-html="convertTextWithLinks(lesson.homework || 'Mājasdarbs nav piešķirts')"></span>
            </div>
          </div>
        </div>
        <div v-else class="text-center text-gray-500 italic mt-6">
          Nav priekšmetu šodien.
        </div>
      </div>

      <!-- Recent Grades -->
      <div class="bg-white p-6 rounded-lg shadow-lg mb-8">
        <h3 class="text-3xl font-semibold text-gray-800 mb-4">Jaunākās atzīmes:</h3>
        <ul>
          <li
            v-for="grade in recentGrades"
            :key="grade.MarkID"
            class="mb-2 flex items-center justify-between p-4 bg-gray-100 rounded-lg shadow-sm"
          >
            <div>
              <div class="font-semibold text-gray-800">{{ grade.subject.Name }}</div>
              <div class="text-sm text-gray-600">{{ formatDate(grade.date) }}</div>
            </div>
            <div class="text-xl font-bold text-blue-600">{{ grade.mark }}</div>
          </li>
        </ul>
      </div>

      <!-- Absences and Scholarship -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Kavējumi -->
        <div class="bg-white p-6 rounded-lg shadow-lg">
          <h3 class="text-3xl font-semibold text-gray-800 mb-4">Kavējumi:</h3>
          <ul>
            <li
              v-for="absence in recentAbsences"
              :key="absence.AbsenceID"
              class="mb-2 flex items-center justify-between p-4 bg-gray-100 rounded-lg shadow-sm"
            >
              <div>
                <div class="font-semibold text-gray-800">{{ absence.subject.Name }}</div>
                <div class="text-sm text-gray-600">{{ formatDate(absence.date) }}</div>
              </div>
              <div
                :class="{
                  'text-red-600': absence.Absence === 2,
                  'text-green-600': absence.Absence === 1,
                }"
                class="text-xl font-bold"
              >
                {{ absence.Absence === 2 ? 'Neapmeklēts' : 'Apmeklēts' }}
              </div>
            </li>
          </ul>
        </div>
        <!-- Prognozējamā stipendija -->
        <div class="bg-white p-6 rounded-lg shadow-lg flex flex-col justify-center items-center">
          <h3 class="text-3xl font-semibold text-gray-800 mb-4">Prognozējamā stipendija:</h3>
          <p class="text-5xl font-bold text-blue-600 mb-4">
            {{ scholarshipAmount }} €
          </p>
          <p class="text-gray-600">Pamatojoties uz vidējo atzīmi: {{ averageGrade.toFixed(2) }}</p>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
  todayTimetable: Array,
  recentGrades: Array,
  recentAbsences: Array,
  averageGrade: Number,
});

const currentDate = ref('');
const scholarshipAmount = ref(0);

const lessonTimes = [
  '07:45 - 08:25', '08:30 - 09:10', '09:15 - 09:55', '10:10 - 10:50',
  '10:55 - 11:35', '12:05 - 12:45', '12:50 - 13:30', '13:35 - 14:15',
  '14:20 - 15:00', '15:05 - 15:45', '15:50 - 16:30', '16:35 - 17:15',
  '17:20 - 18:00',
];

function updateCurrentDate() {
  const today = new Date();
  currentDate.value = today.toLocaleDateString('lv-LV', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
  });
}

function getTimeForLesson(index) {
  return lessonTimes[index] || '...';
}

function formatDate(dateStr) {
  const date = new Date(dateStr);
  return date.toLocaleDateString('lv-LV', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
  });
}

function convertTextWithLinks(text) {
  const urlRegex = /(https?:\/\/[^\s]+)/g;
  return text.replace(urlRegex, (url) => {
    return `<a href="${url}" class="underline" target="_blank">${url}</a>`;
  });
}

function calculateScholarship() {
  const average = props.averageGrade || 0;

  if (average >= 10) {
    scholarshipAmount.value = 100;
  } else if (average >= 8 && average < 10) {
    scholarshipAmount.value = 81;
  } else if (average >= 7 && average < 8) {
    scholarshipAmount.value = 71;
  } else if (average >= 6 && average < 7) {
    scholarshipAmount.value = 61;
  } else if (average >= 5 && average < 6) {
    scholarshipAmount.value = 41;
  } else {
    scholarshipAmount.value = 0;
  }
}

updateCurrentDate();
calculateScholarship();
</script>

<style scoped>
/* Additional styling for better design */
</style>
