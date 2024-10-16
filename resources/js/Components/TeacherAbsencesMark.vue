<template>
    <div class="bg-white shadow-sm rounded-lg p-6">
        <h2 class="text-lg font-semibold mb-4">Kavējumu atzīmēšana</h2>
        <table class="min-w-full border border-gray-300">
            <thead>
                <tr>
                    <th class="border-b border-gray-300 px-4 py-2">Vārds</th>
                    <th class="border-b border-gray-300 px-4 py-2">Priekšmets</th>
                    <th class="border-b border-gray-300 px-4 py-2">Kavējums</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="user in users" :key="user.id">
                    <td class="border-b border-gray-300 px-4 py-2">{{ user.name }}</td>
                    <td class="border-b border-gray-300 px-4 py-2">
                        <select v-model="selectedSubject[user.id]" class="border-gray-300">
                            <option v-for="subject in subjects" :key="subject.id" :value="subject.id">
                                {{ subject.name }}
                            </option>
                        </select>
                    </td>
                    <td class="border-b border-gray-300 px-4 py-2">
                        <select v-model="selectedAbsence[user.id]" class="border-gray-300">
                            <option value="">-- Select --</option>
                            <option value="Attaisnots">Attaisnots</option>
                            <option value="Kavējums">Kavējums</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
    import { ref, onMounted } from 'vue';

    const users = ref([]);
    const subjects = ref([]);
    const selectedSubject = ref({});
    const selectedAbsence = ref({});

    onMounted(async () => {
        try {
            // Fetch users
            const userResponse = await fetch('/api/users');
            if (!userResponse.ok) throw new Error('Network response was not ok');
            users.value = await userResponse.json(); // Parse the JSON
            console.log(users.value); // Log the response to see if it's as expected

            // Fetch subjects
            const subjectResponse = await fetch('/api/subjects');
            if (!subjectResponse.ok) throw new Error('Network response was not ok');
            subjects.value = await subjectResponse.json(); // Parse the JSON
        } catch (error) {
            console.error("Error fetching data:", error);
        }
    });
</script>

<style scoped>
    /* Add any custom styles here if necessary */
</style>
