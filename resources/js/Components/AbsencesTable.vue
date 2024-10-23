<template>
    <div>
        <!-- Table for displaying absences -->
        <div v-if="subjectLists.length" class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 shadow-sm rounded-lg">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border p-4 text-left text-sm font-medium text-gray-600">Subject</th>
                        <th class="border p-4 text-left text-sm font-medium text-gray-600">Date</th>
                        <th class="border p-4 text-left text-sm font-medium text-gray-600">Absences</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(subjectList, index) in subjectLists" :key="index" class="hover:bg-gray-50">
                        <td class="border p-4">{{ subjectList.subject.Name }}</td>
                        <td class="border p-4">{{ formatDate(subjectList.Date) }}</td>
                        <td class="border p-4">
                            <span v-for="(absence, i) in subjectList.absences" :key="i">
                                {{ absence.Absence === 1 ? "Justified" : "Unjustified" }}
                            </span>
                            <span v-if="!subjectList.absences.length" class="text-gray-400">No absences</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-else>
            <p class="text-gray-500 italic">No subjects or absences available.</p>
        </div>
    </div>
</template>

<script>
export default {
  props: {
    subjectLists: Array, // This is the array of subjects and absences passed from the parent
  },
  methods: {
    formatDate(date) {
      const formattedDate = new Date(date);
      return formattedDate.toLocaleDateString();
    },
  },
};
</script>

<style scoped>
    /* Add any styles you need */
</style>
