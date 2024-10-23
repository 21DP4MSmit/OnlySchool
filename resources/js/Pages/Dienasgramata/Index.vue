<template>
  <AuthenticatedLayout>
    <div class="container mx-auto px-4 py-8">
      <!-- Page Header -->
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight leading-tight">Dienasgrāmata</h1>
        <span class="text-lg font-semibold text-gray-600 bg-gray-200 py-2 px-4 rounded-lg shadow-sm">
          {{ currentDate }} (Šodien)
        </span>
      </div>

      <!-- Week Navigation -->
      <div class="flex justify-between items-center mb-8">
        <button @click="prevWeek" class="text-4xl text-blue-600 hover:text-blue-700 transition transform hover:scale-105 font-bold">
          &lt;
        </button>
        <span class="text-lg font-semibold bg-blue-100 py-2 px-6 rounded-lg shadow-sm">
          {{ formattedWeekRange }}
        </span>
        <button @click="nextWeek" class="text-4xl text-blue-600 hover:text-blue-700 transition transform hover:scale-105 font-bold">
          &gt;
        </button>
      </div>

      <!-- Subject Lists by Day -->
      <div v-for="(subjectLists, day) in weekdays" :key="day" class="mb-12">
        <h2 class="text-3xl font-semibold text-gray-800 mb-6">{{ day }}</h2>

        <div v-if="subjectLists.length" class="overflow-hidden shadow-lg rounded-lg">
          <table class="min-w-full bg-white rounded-lg border-collapse">
            <thead class="bg-gradient-to-r from-blue-500 to-blue-700 text-white uppercase text-sm font-semibold">
              <tr>
                <th class="w-1/12 py-4 px-6 text-left">#</th>
                <th class="w-1/12 py-4 px-6 text-left">Laiks</th>
                <th class="w-1/4 py-4 px-6 text-left">Priekšmets & Klase</th>
                <th class="w-1/5 py-4 px-6 text-left">Tēma</th>
                <th class="w-1/4 py-4 px-6 text-left">Mājasdarbs</th>
                <th class="w-1/5 py-4 px-6 text-left">Kavējumi</th>
                <th class="w-1/6 py-4 px-6 text-left">Atzīmes</th>
              </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
              <tr
                v-for="(subjectList, index) in subjectLists"
                :key="subjectList.ListID"
                class="border-b border-gray-200 hover:bg-gray-50 transition duration-150 ease-in-out"
              >
                <td class="py-4 px-6">{{ index + 1 }}</td>
                <td class="py-4 px-6">{{ getTimeForLesson(index) }}</td>
                <td class="py-4 px-6">
                  <span class="font-bold text-blue-600">{{ subjectList.subject.Name }}</span><br>
                  <span class="text-gray-500">Klase: {{ subjectList.classroom.Classroom }}</span>
                </td>
                <td class="py-4 px-6 truncate max-w-xs">{{ subjectList.topic || 'Tēma nav pieejama' }}</td>
                <td class="py-4 px-6 truncate max-w-xs">
                  <span v-html="convertTextWithLinks(subjectList.homework || 'Mājasdarbs nav piešķirts')"></span>
                </td>
                <td class="py-4 px-6">
                  <template v-for="absence in subjectList.absences" :key="absence.AbsenceID">
                    <span v-if="absence.Absence === 1" class="inline-block w-4 h-4 bg-green-500 rounded-full"></span>
                    <span v-if="absence.Absence === 2" class="inline-block w-4 h-4 bg-red-500 rounded-full"></span>
                  </template>
                  <span v-if="!subjectList.absences.length" class="inline-block w-4 h-4 bg-gray-400 rounded-full"></span>
                </td>
                <td class="py-4 px-6">
                  <span v-if="subjectList.marks.length && subjectList.marks[0].mark" class="font-semibold">
                    {{ subjectList.marks[0].mark }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-else class="text-center text-gray-500 italic mt-6">
          Nav priekšmetu šajā dienā.
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { startOfWeek, addDays, addWeeks, format } from 'date-fns';

export default {
props: {
  subjectLists: Array,
},
data() {
  return {
    currentDate: '',
    currentWeekStart: startOfWeek(new Date(), { weekStartsOn: 1 }), // Start from Monday
    lessonTimes: [
      "07:45 - 08:25", "08:30 - 09:10", "09:15 - 09:55", "10:10 - 10:50",
      "10:55 - 11:35", "12:05 - 12:45", "12:50 - 13:30", "13:35 - 14:15",
      "14:20 - 15:00", "15:05 - 15:45", "15:50 - 16:30", "16:35 - 17:15",
      "17:20 - 18:00"
    ]
  };
},
computed: {
  weekdays() {
    return {
      "Pirmdiena": this.groupedSubjectLists["Monday"] || [],
      "Otrdiena": this.groupedSubjectLists["Tuesday"] || [],
      "Trešdiena": this.groupedSubjectLists["Wednesday"] || [],
      "Ceturtdiena": this.groupedSubjectLists["Thursday"] || [],
      "Piektdiena": this.groupedSubjectLists["Friday"] || [],
    };
  },
  groupedSubjectLists() {
    return this.subjectLists.reduce((groups, subjectList) => {
      const lessonDate = new Date(subjectList.Date);

      // Ensure no time component and no timezone shifts in comparisons
      const normalizedLessonDate = new Date(lessonDate.toISOString().split('T')[0]);
      const normalizedWeekStart = new Date(this.currentWeekStart.toISOString().split('T')[0]);
      const normalizedWeekEnd = new Date(normalizedWeekStart);

      // **Changed here to include Friday**
      normalizedWeekEnd.setDate(normalizedWeekEnd.getDate() + 5); // Monday-Friday range

      console.log(`Checking lesson: ${subjectList.subject.Name} Date: ${normalizedLessonDate}`);
      console.log(`Week range: ${normalizedWeekStart} - ${normalizedWeekEnd}`);
      
      // Compare normalized dates to filter lessons
      if (normalizedLessonDate >= normalizedWeekStart && normalizedLessonDate <= normalizedWeekEnd) {
        const day = normalizedLessonDate.toLocaleDateString('en-US', { weekday: 'long' });
        if (!groups[day]) {
          groups[day] = [];
        }
        groups[day].push(subjectList);
      }
      return groups;
    }, {});
  },
  formattedWeekRange() {
    const start = format(this.currentWeekStart, 'dd.MM.yyyy');
    const end = format(addDays(this.currentWeekStart, 5), 'dd.MM.yyyy'); // **Changed here to include Friday**
    return `${start} - ${end}`;
  },
},
mounted() {
  this.updateCurrentDate();
  this.loadLessonsForWeek();
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
  getTimeForLesson(index) {
    return this.lessonTimes[index] || 'Laiks nav pieejams';
  },
  loadLessonsForWeek() {
    const weekStartDate = this.currentWeekStart.toISOString().split('T')[0];

    this.$inertia.get('/dienasgramata', {
      weekStart: weekStartDate,
    }, {
      preserveState: true,
    });
  },
  prevWeek() {
    this.currentWeekStart = addWeeks(this.currentWeekStart, -1);
    this.loadLessonsForWeek();
  },
  nextWeek() {
    this.currentWeekStart = addWeeks(this.currentWeekStart, 1);
    this.loadLessonsForWeek();
  },
  convertTextWithLinks(text) {
    const urlRegex = /(https?:\/\/[^\s]+)/g;
    return text.replace(urlRegex, (url) => {
      return `<a href="${url}" class="text-blue-500 hover:underline" target="_blank">${url}</a>`;
    });
  }
},
};
</script>
