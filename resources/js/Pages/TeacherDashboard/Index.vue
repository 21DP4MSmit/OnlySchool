<template>
    <AuthenticatedLayout>
        <div class="container mx-auto px-4 py-8">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight leading-tight">Today's Classes</h1>
                <span class="text-lg font-semibold text-gray-600 bg-gray-200 py-2 px-4 rounded-lg shadow-sm">
                    {{ new Date().toLocaleDateString('lv-LV') }} (Šodien)
                </span>
            </div>

            <div>
                <h2 class="text-2xl mb-4">Classes for Today</h2>
                <div v-if="todayClasses.length" class="overflow-hidden shadow-lg rounded-lg">
                    <table class="min-w-full bg-white rounded-lg border-collapse">
                        <thead class="bg-gradient-to-r from-blue-500 to-blue-700 text-white uppercase text-sm font-semibold">
                            <tr>
                                <th class="py-4 px-6 text-left">#</th>
                                <th class="py-4 px-6 text-left">Time</th>
                                <th class="py-4 px-6 text-left">Subject & Class</th>
                                <th class="py-4 px-6 text-left">Room</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm">
                            <tr v-for="(classItem, index) in todayClasses" :key="index" class="border-b border-gray-200 hover:bg-gray-50 transition duration-150 ease-in-out">
                                <td class="py-4 px-6">{{ index + 1 }}</td>
                                <td class="py-4 px-6">{{ getTimeForClass(index) }}</td>
                                <td class="py-4 px-6">{{ classItem.subject?.Name || 'N/A' }} (Class: {{ classItem.classroom?.Classroom || 'N/A' }})</td>
                                <td class="py-4 px-6">{{ classItem.classroom?.Room || 'No Room' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else class="text-center text-gray-500 italic mt-6">
                    No classes for today.
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
    import { ref } from 'vue';
    import { usePage } from '@inertiajs/inertia-vue3';

    const { props } = usePage();
    const todayClasses = ref(props.todayClasses || []);

    const lessonTimes = [
        "07:45 - 08:25", "08:30 - 09:10", "09:15 - 09:55",
        "10:10 - 10:50", "10:55 - 11:35", "12:05 - 12:45",
        "12:50 - 13:30", "13:35 - 14:15", "14:20 - 15:00",
        "15:05 - 15:45", "15:50 - 16:30", "16:35 - 17:15",
        "17:20 - 18:00"
    ];

    const getTimeForClass = (index) => {
        return lessonTimes[index] || 'Time not available';
    };
</script>
