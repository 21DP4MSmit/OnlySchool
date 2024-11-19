<template>
  <AuthenticatedLayout>
    <div class="container mx-auto px-4 py-8">
      <!-- Header with current date -->
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-4xl font-extrabold text-gray-900">Studentu lapa</h1>
        <span class="text-lg font-semibold text-gray-600 bg-gray-200 py-2 px-4 rounded-lg shadow-sm">
          {{ currentDate }} (Šodien)
        </span>
      </div>

      <!-- Today's Timetable -->
      <div class="bg-white p-6 rounded-lg shadow-lg mb-8">
        <h2 class="text-3xl font-semibold text-gray-800 mb-4">Šodienas stundu saraksts:</h2>
        <div v-if="todayTimetable.length" class="overflow-hidden shadow-lg rounded-lg">
          <table class="min-w-full bg-white rounded-lg border-collapse">
            <thead class="bg-gradient-to-r from-blue-500 to-blue-700 text-white uppercase text-sm font-semibold">
              <tr>
                <th class="w-1/12 py-4 px-6 text-left">#</th>
                <th class="w-1/12 py-4 px-6 text-left">Laiks</th>
                <th class="w-1/4 py-4 px-6 text-left">Priekšmets & Klase</th>
                <th class="w-1/5 py-4 px-6 text-left">Tēma</th>
                <th class="w-1/4 py-4 px-6 text-left">Mājasdarbs</th>
              </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
              <tr
                v-for="(lesson, index) in todayTimetable"
                :key="lesson.ListID"
                class="border-b border-gray-200 hover:bg-gray-50 transition duration-150 ease-in-out"
              >
                <td class="py-4 px-6">{{ index + 1 }}</td>
                <td class="py-4 px-6">{{ getTimeForLesson(index) }}</td>
                <td class="py-4 px-6">
                  <span class="font-bold text-gray-900">{{ lesson.subject.Name }}</span><br>
                  <span class="text-gray-500">Klase: {{ lesson.classroom.Classroom }}</span>
                </td>
                <td class="py-4 px-6 truncate max-w-xs">{{ lesson.topic || 'Tēma nav pieejama' }}</td>
                <td class="py-4 px-6 truncate max-w-xs">
                  <span v-html="convertTextWithLinks(lesson.homework || 'Mājasdarbs nav piešķirts')"></span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-else class="text-center text-gray-500 italic mt-6">
          Nav priekšmetu šodien.
        </div>
      </div>

      <!-- Recent Grades -->
      <div class="bg-white p-6 rounded-lg shadow-lg mb-8">
        <h3 class="text-3xl font-semibold text-gray-800 mb-4">Recent Marks:</h3>
        <ul>
        <li v-for="marks in recentMarks" :key="mark.id" class="mb-2">
          {{ marks.subject }} - {{ marks.marks }} ({{ marks.date }})
      </li>
      </ul>

      </div>

      <!-- Monthly Absences -->
      <div class="bg-white p-6 rounded-lg shadow-lg">
        <h3 class="text-3xl font-semibold text-gray-800 mb-4">Monthly Absences:</h3>
        <ul>
          <li v-for="absence in monthlyAbsences" :key="absence.id" class="mb-2">
            {{ absence.date }} - {{ absence.reason }}
          </li>
        </ul>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script>
import { startOfWeek, addDays, addWeeks, format } from 'date-fns';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import axios from 'axios';
import Echo from "laravel-echo";

export default {
  components: { AuthenticatedLayout },
  data() {
    return {
      currentDate: '',
      todayTimetable: [],
      recentMarks: [],
      monthlyAbsences: [],
      lessonTimes: [
        "07:45 - 08:25", "08:30 - 09:10", "09:15 - 09:55", "10:10 - 10:50",
        "10:55 - 11:35", "12:05 - 12:45", "12:50 - 13:30", "13:35 - 14:15",
        "14:20 - 15:00", "15:05 - 15:45", "15:50 - 16:30", "16:35 - 17:15",
        "17:20 - 18:00"
      ],
    };
  },
  methods: {
    updateCurrentDate() {
      const today = new Date();
      this.currentDate = today.toLocaleDateString('lv-LV', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
      });
    },
    fetchData() {
      // Fetch today's timetable
      axios
        .get('/api/timetable/today')
        .then(response => {
          this.todayTimetable = response.data;
        })
        .catch(() => {
          this.todayTimetable = [];
        });

      // Fetch recent marks
      axios
        .get('/api/marks/recent')
        .then(response => {
          this.recentMarks = response.data;
        })
        .catch(() => {
          this.recentMarks = [];
        });

      // Fetch monthly absences
      axios
        .get('/api/absences/monthly')
        .then(response => {
          this.monthlyAbsences = response.data;
        })
        .catch(() => {
          this.monthlyAbsences = [];
        });
    },
    getTimeForLesson(index) {
      return this.lessonTimes[index] || '...';
    },
    convertTextWithLinks(text) {
      return text.replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank" class="text-blue-500 hover:underline">$1</a>');
    },
  },
  mounted() {
    this.updateCurrentDate();
    this.fetchData();
  },
};
</script>