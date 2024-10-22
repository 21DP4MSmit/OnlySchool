<template>
    <AuthenticatedLayout>
      <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-4xl font-extrabold text-gray-800">Dienasgrāmata</h1>
          <!-- Top-right corner date display -->
          <span class="text-lg font-semibold text-gray-600">
            {{ currentDate }} (Šodien)
          </span>
        </div>
  
        <!-- Week navigation buttons -->
        <div class="flex justify-between items-center mb-6">
          <button @click="prevWeek" class="text-blue-600 hover:underline text-4xl font-extrabold">&lt;</button>
          <span class="text-lg font-semibold">{{ formattedWeekRange }}</span>
          <button @click="nextWeek" class="text-blue-600 hover:underline text-4xl font-extrabold">&gt;</button>
        </div>
  
        <div v-for="(subjectLists, day) in weekdays" :key="day" class="mb-10">
          <h2 class="text-2xl font-semibold text-gray-700 mb-4">{{ day }}</h2>
  
          <div v-if="subjectLists.length" class="overflow-hidden shadow-lg rounded-lg">
            <table class="min-w-full table-fixed bg-white rounded-lg border-collapse">
              <thead class="bg-gradient-to-r from-blue-500 to-blue-700 text-white uppercase text-sm leading-normal">
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
              <tbody class="text-gray-700 text-sm font-light">
                <tr v-for="(subjectList, index) in subjectLists" :key="subjectList.ListID" class="border-b border-gray-200 hover:bg-gray-100 odd:bg-gray-50">
                  <td class="py-4 px-6 text-left">{{ index + 1 }}</td>
                  <td class="py-4 px-6 text-left">{{ getTimeForLesson(index) }}</td>
                  <td class="py-4 px-6 text-left break-words whitespace-normal">
                    <span class="font-bold text-blue-600">{{ subjectList.subject.Name }}</span><br>
                    <span class="text-gray-500">Klase: {{ subjectList.classroom.Classroom }}</span>
                  </td>
                  <td class="py-4 px-6 text-left break-words whitespace-normal truncate max-w-xs">{{ subjectList.topic || 'Tēma nav pieejama' }}</td>
                  <td class="py-4 px-6 text-left break-words whitespace-normal truncate max-w-xs">
                    <span v-html="convertTextWithLinks(subjectList.homework || 'Mājasdarbs nav piešķirts')"></span>
                  </td>
                  <td class="py-4 px-6 text-left">
                    <span v-if="subjectList.absences.length">
                      <template v-for="absence in subjectList.absences" :key="absence.AbsenceID">
                        <span v-if="absence.Absence === 1" class="inline-block w-4 h-4 bg-green-500 rounded-full"></span>
                        <span v-if="absence.Absence === 2" class="inline-block w-4 h-4 bg-red-500 rounded-full"></span>
                      </template>
                    </span>
                    <span v-else>
                      <span class="inline-block w-4 h-4 bg-gray-400 rounded-full"></span>
                    </span>
                  </td>
                  <td class="py-4 px-6 text-left">
                    <span v-if="subjectList.marks.length && subjectList.marks[0].mark">
                      <span class="font-semibold">{{ subjectList.marks[0].mark }}</span>
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
  
          <div v-else class="text-center mt-6 text-gray-500 italic">
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
        normalizedWeekEnd.setDate(normalizedWeekEnd.getDate() + 4); // Monday-Friday range

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
        const end = format(addDays(this.currentWeekStart, 4), 'dd.MM.yyyy'); // Add 4 days to get Friday
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
      updateCurrentDate() {
        const today = new Date();
        this.currentDate = today.toLocaleDateString('lv-LV', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
        });
    },
    
    // Fetch lessons for the currently selected week
    loadLessonsForWeek() {
        const weekStartDate = this.currentWeekStart.toISOString().split('T')[0];
        
        // Send an API request to fetch lessons for the current week range
        this.$inertia.get('/dienasgramata', {
            weekStart: weekStartDate, // Send the current week's start date to the backend
        }, {
            preserveState: true, // Ensure state is preserved when changing weeks
        });
    },
    
    prevWeek() {
        this.currentWeekStart = addWeeks(this.currentWeekStart, -1); // Move to the previous week
        this.loadLessonsForWeek(); // Fetch lessons for the previous week
    },
    
    nextWeek() {
        this.currentWeekStart = addWeeks(this.currentWeekStart, 1); // Move to the next week
        this.loadLessonsForWeek(); // Fetch lessons for the next week
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
  