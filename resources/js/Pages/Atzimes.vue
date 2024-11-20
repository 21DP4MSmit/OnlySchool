<template>
    <AuthenticatedLayout>
      <div class="container mx-auto px-4 py-8">
        <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight leading-tight mb-8">Jūsu atzīmes</h1>
  
        <!-- Month and Year Selector with Enhanced Styling -->
        <div class="mb-6 flex items-center space-x-6">
          <div class="flex items-center space-x-4">
            <label for="month" class="text-lg font-semibold text-gray-700">Izvēlieties mēnesi:</label>
            <select
              id="month"
              v-model="selectedMonth"
              class="p-3 border rounded-lg bg-white shadow focus:outline-none focus:ring-2 focus:ring-blue-500 w-40"
            >
              <option v-for="(month, index) in months" :key="index" :value="index + 1">
                {{ month }}
              </option>
            </select>
          </div>
  
          <div class="flex items-center space-x-4">
            <label for="year" class="text-lg font-semibold text-gray-700">Izvēlieties gadu:</label>
            <select
              id="year"
              v-model="selectedYear"
              class="p-3 border rounded-lg bg-white shadow focus:outline-none focus:ring-2 focus:ring-blue-500 w-32"
            >
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
                <th class="w-1/3 py-4 px-6 text-center">Atzīmes šomēnes</th>
                <th class="w-1/6 py-4 px-6 text-center">Vidējā atzīme</th>
              </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
              <tr
                v-for="(grade, index) in filteredGrades"
                :key="grade.subject.Name"
                class="border-b border-gray-200 hover:bg-gray-50 transition duration-150 ease-in-out"
              >
                <td class="py-4 px-6 font-bold text-gray-900">{{ grade.subject.Name }}</td>
                <td class="py-4 px-6 flex flex-wrap justify-center space-x-1">
                  <!-- Display grades -->
                  <span
                    v-for="(mark, idx) in grade.marks"
                    :key="idx"
                    class="inline-block px-2 py-1 bg-blue-500 text-white rounded-full mb-1"
                  >
                    {{ mark }}
                  </span>
                </td>
                <td class="py-4 px-6 text-center">{{ grade.average }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </AuthenticatedLayout>
  </template>
  
  <script setup>
  import { ref, computed } from 'vue';
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
  
  const props = defineProps({
    marks: Array,
  });
  
  const selectedMonth = ref(new Date().getMonth() + 1); // Default to current month
  const selectedYear = ref(new Date().getFullYear()); // Default to current year
  
  const months = [
    'Janvāris',
    'Februāris',
    'Marts',
    'Aprīlis',
    'Maijs',
    'Jūnijs',
    'Jūlijs',
    'Augusts',
    'Septembris',
    'Oktobris',
    'Novembris',
    'Decembris',
  ];
  
  const years = [2023, 2024, 2025]; // Adjust this as needed
  
  const groupedGrades = computed(() => {
    const grouped = {};
  
    props.marks.forEach((mark) => {
      const subjectName = mark.subject?.Name || 'Unknown';
  
      if (!grouped[subjectName]) {
        grouped[subjectName] = { subject: mark.subject, marks: [] };
      }
  
      grouped[subjectName].marks.push({ mark: mark.mark, date: mark.date });
    });
  
    return Object.values(grouped);
  });
  
  const filteredGrades = computed(() => {
    return groupedGrades.value
      .map((grade) => {
        const filteredMarks = grade.marks.filter((m) => {
          const date = new Date(m.date);
          return (
            date.getMonth() + 1 === selectedMonth.value && date.getFullYear() === selectedYear.value
          );
        });
  
        const marksArray = filteredMarks.map((m) => Number(m.mark)).filter((n) => !isNaN(n));
        const sum = marksArray.reduce((a, b) => a + b, 0);
        const average = marksArray.length ? (sum / marksArray.length).toFixed(2) : 'N/A';
  
        return {
          subject: grade.subject,
          marks: filteredMarks.map((m) => m.mark),
          average: average,
        };
      })
      .filter((grade) => grade.marks.length > 0);
  });
  </script>
  