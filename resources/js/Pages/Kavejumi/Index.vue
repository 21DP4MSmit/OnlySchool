<template>
  <AuthenticatedLayout>
    <div class="container mx-auto px-4 py-8">
      <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight leading-tight mb-8">Jūsu kavējumi</h1>

      <!-- Month and Year Selector with Enhanced Styling -->
      <div class="mb-6 flex items-center space-x-6">
        <div class="flex items-center space-x-4">
          <label for="month" class="text-lg font-semibold text-gray-700">Izvēlieties mēnesi:</label>
          <select id="month" v-model="selectedMonth" class="p-3 border rounded-lg bg-white shadow focus:outline-none focus:ring-2 focus:ring-blue-500 w-40">
            <option v-for="(month, index) in months" :key="index" :value="index + 1">
              {{ month }}
            </option>
          </select>
        </div>
        
        <div class="flex items-center space-x-4">
          <label for="year" class="text-lg font-semibold text-gray-700">Izvēlieties gadu:</label>
          <select id="year" v-model="selectedYear" class="p-3 border rounded-lg bg-white shadow focus:outline-none focus:ring-2 focus:ring-blue-500 w-32">
            <option v-for="year in years" :key="year" :value="year">
              {{ year }}
            </option>
          </select>
        </div>
      </div>

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
              v-for="(absence, index) in filteredAbsences"
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
  data() {
    return {
      selectedMonth: new Date().getMonth() + 1, // Default to current month
      selectedYear: new Date().getFullYear(), // Default to current year
      months: [
        "Janvāris", "Februāris", "Marts", "Aprīlis", "Maijs", "Jūnijs",
        "Jūlijs", "Augusts", "Septembris", "Oktobris", "Novembris", "Decembris"
      ],
      years: [2023, 2024, 2025], // Adjust this as needed
    };
  },
  computed: {
    filteredAbsences() {
      return this.groupedAbsences.filter(absence => {
        const date = new Date(absence.date); // Adjusted path here
        return (
          date.getMonth() + 1 === this.selectedMonth &&
          date.getFullYear() === this.selectedYear
        );
      });
    },
    groupedAbsences() {
      const grouped = this.absences.reduce((acc, absence) => {
        const subjectName = absence.subject?.Name || 'Unknown';
        const date = absence.date; // Adjusted path here

        if (!acc[subjectName]) {
          acc[subjectName] = { subject: absence.subject, attendance: 0, absence: 0, noRecord: 0, date };
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
