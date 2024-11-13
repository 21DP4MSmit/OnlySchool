<template>
    <AuthenticatedLayout>
      <div class="container mx-auto px-4 py-8">
        <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight leading-tight mb-8">Jūsu kavējumi</h1>
  
        <div class="overflow-hidden shadow-lg rounded-lg">
          <table class="min-w-full bg-white rounded-lg border-collapse">
            <thead class="bg-gradient-to-r from-blue-500 to-blue-700 text-white uppercase text-sm font-semibold">
              <tr>
                <th class="w-1/2 py-4 px-6 text-left">Priekšmets</th>
                <th class="w-1/3 py-4 px-6 text-center">Kavējumi šomēnes</th>
                <th class="w-1/6 py-4 px-6 text-center">Neapmeklēts</th>
                <th class="w-1/6 py-4 px-6 text-center">Apmeklēts</th>
              </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
              <tr
                v-for="(absence, index) in groupedAbsences"
                :key="absence.subject.Name"
                class="border-b border-gray-200 hover:bg-gray-50 transition duration-150 ease-in-out"
              >
                <td class="py-4 px-6 font-bold text-gray-900">{{ absence.subject.Name }}</td>
                <td class="py-4 px-6 flex justify-center space-x-1">
                  <!-- Green circles for attendance -->
                  <span v-for="i in absence.attendance" :key="'attend-' + i" class="inline-block w-4 h-4 bg-green-500 rounded-full"></span>
                  <!-- Red circles for absence -->
                  <span v-for="i in absence.absence" :key="'absent-' + i" class="inline-block w-4 h-4 bg-red-500 rounded-full"></span>
                  <!-- Gray circles for no record -->
                  <span v-for="i in absence.noRecord" :key="'norecord-' + i" class="inline-block w-4 h-4 bg-gray-400 rounded-full"></span>
                </td>
                <td class="py-4 px-6 text-center">{{ absence.absence }}</td>
                <td class="py-4 px-6 text-center">{{ absence.attendance }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </AuthenticatedLayout>
  </template>
  
  <script>
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
  
  export default {
    props: {
      absences: Array,
    },
    components: {
      AuthenticatedLayout,
    },
    computed: {
      groupedAbsences() {
        // Group absences by subject and count attendance, absences, and no records
        const grouped = this.absences.reduce((acc, absence) => {
          const subjectName = absence.subject?.Name || 'Unknown';
          if (!acc[subjectName]) {
            acc[subjectName] = { subject: absence.subject, attendance: 0, absence: 0, noRecord: 0 };
          }
          if (absence.Absence === 1) {
            acc[subjectName].attendance++; // Green for Apmeklēts
          } else if (absence.Absence === 2) {
            acc[subjectName].absence++; // Red for Neapmeklēts
          } else {
            acc[subjectName].noRecord++; // Gray for no record
          }
          return acc;
        }, {});
        return Object.values(grouped);
      },
    },
  };
  </script>
  