<template>
    <AuthenticatedLayout>
      <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-4xl font-extrabold text-gray-800">Dienasgramata</h1>
          <!-- Top-right corner date display -->
          <span class="text-lg font-semibold text-gray-600">
            {{ currentDate }} (Šodien)
          </span>
        </div>
  
        <div v-for="(subjectLists, day) in weekdays" :key="day" class="mb-10"> <!-- Increased spacing -->
          <h2 class="text-2xl font-semibold text-gray-700 mb-4">{{ day }}</h2>
  
          <div v-if="subjectLists.length" class="overflow-hidden shadow-lg rounded-lg">
            <table class="min-w-full table-fixed bg-white rounded-lg border-collapse">
              <thead class="bg-gradient-to-r from-blue-500 to-blue-700 text-white uppercase text-sm leading-normal">
                <tr>
                  <th class="w-1/4 py-4 px-6 text-left">Priekšmets & Klase</th>
                  <th class="w-1/5 py-4 px-6 text-left">Tēma</th>
                  <th class="w-1/4 py-4 px-6 text-left">Mājasdarbs</th>
                  <th class="w-1/5 py-4 px-6 text-left">Kavējumi</th>
                  <th class="w-1/6 py-4 px-6 text-left">Atzīmes</th>
                </tr>
              </thead>
              <tbody class="text-gray-700 text-sm font-light">
                <tr v-for="subjectList in subjectLists" :key="subjectList.ListID" class="border-b border-gray-200 hover:bg-gray-100">
                  <td class="py-4 px-6 text-left">
                    <span class="font-bold text-blue-600">{{ subjectList.subject.Name }}</span><br>
                    <span class="text-gray-500">Klase: {{ subjectList.classroom.Classroom }}</span>
                  </td>
                  <td class="py-4 px-6 text-left break-words">{{ subjectList.topic || 'Tēma nav pieejama' }}</td>
                  <td class="py-4 px-6 text-left break-words">{{ subjectList.homework || 'Mājasdarbs nav piešķirts' }}</td>
                  <td class="py-4 px-6 text-left">
                    <span v-if="subjectList.absences.length">
                      <template v-for="absence in subjectList.absences" :key="absence.AbsenceID">
                        <span v-if="absence.Absence === 1" class="inline-block w-4 h-4 bg-green-500 rounded-full"></span>
                        <span v-if="absence.Absence === 2" class="inline-block w-4 h-4 bg-red-500 rounded-full"></span>
                      </template>
                    </span>
                    <span v-else class="text-gray-500">Kavējumi nav reģistrēti</span>
                  </td>
                  <td class="py-4 px-6 text-left">
                    <span v-if="subjectList.marks.length" class="font-semibold">{{ subjectList.marks[0].mark }}</span>
                    <span v-else class="text-gray-500">Atzīmes nav pieejamas</span>
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
  
  export default {
    props: {
      subjectLists: Array,
    },
    data() {
      return {
        currentDate: '',
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
          const day = new Date(subjectList.Date).toLocaleDateString('en-US', { weekday: 'long' });
          if (!groups[day]) {
            groups[day] = [];
          }
          groups[day].push(subjectList);
          return groups;
        }, {});
      },
    },
    mounted() {
      this.updateCurrentDate();
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
    },
  };
  </script>
