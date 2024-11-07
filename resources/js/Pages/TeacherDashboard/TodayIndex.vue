<template>
    <AuthenticatedLayout>
        <div class="container mx-auto px-4 py-8">
            <h1 class="text-4xl font-extrabold">Today's Classes</h1>
            <div v-if="todayClasses.length" class="overflow-hidden shadow-lg rounded-lg">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Time</th>
                            <th>Subject & Class</th>
                            <th>Room</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(classItem, index) in todayClasses" :key="index">
                            <td>{{ index + 1 }}</td>
                            <td>{{ getTimeForClass(index) }}</td>
                            <td>{{ classItem.subject?.Name || 'N/A' }}</td>
                            <td>{{ classItem.classroom?.Classroom || 'No Room' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-else> No classes scheduled for today. </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
    import { computed } from 'vue';
    import { usePage } from '@inertiajs/inertia-vue3';

    // Access the props and check if the data exists within props.value
    const props = usePage().props;
    console.log("Props received:", props); // For debugging

    // Safely access teacherClasses in case it’s nested within another object or key
    const teacherClasses = props.teacherClasses ? props.teacherClasses.value : [];

    console.log("Teacher Classes received:", teacherClasses); // For debugging

    // Filter to show only today’s classes
    const todayDate = new Date().toISOString().split('T')[0];
    const todayClasses = computed(() => {
        return teacherClasses.filter(classItem => classItem.Date === todayDate);
    });

    const lessonTimes = [
        "07:45 - 08:25", "08:30 - 09:10", "09:15 - 09:55",
        "10:10 - 10:50", "10:55 - 11:35", "12:05 - 12:45",
    ];

    const getTimeForClass = (index) => lessonTimes[index] || 'Time not available';
</script>
