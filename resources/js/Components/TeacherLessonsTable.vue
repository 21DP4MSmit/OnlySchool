<template>
    <div class="bg-white shadow-sm rounded-lg h-full flex flex-col">
        <table class="min-w-full border border-gray-300">
            <thead>
                <tr>
                    <th class="border-b border-gray-300 px-4 py-2 text-3xl">Today's Classes</th>
                </tr>
                <tr>
                    <th class="border-b border-gray-300 px-4 py-2">#</th>
                    <th class="border-b border-gray-300 px-4 py-2">Time</th>
                    <th class="border-b border-gray-300 px-4 py-2">Subject & Class</th>
                    <th class="border-b border-gray-300 px-4 py-2">Room</th>
                    <th class="border-b border-gray-300 px-4 py-2">Teacher</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(classItem, index) in classes" :key="index" class="border-b border-gray-200 hover:bg-gray-50 transition duration-150 ease-in-out">
                    <td class="py-4 px-6">{{ index + 1 }}</td>
                    <td class="py-4 px-6">{{ classItem.time || 'Time not available' }}</td>
                    <td class="py-4 px-6">
                        <span class="font-bold text-blue-600">{{ classItem.subject?.Name || 'N/A' }}</span><br>
                        <span class="text-gray-500">Class: {{ classItem.classroom?.Classroom || 'N/A' }}</span>
                    </td>
                    <td class="py-4 px-6">{{ classItem.classroom?.Room || 'No Room' }}</td>
                    <td class="py-4 px-6">{{ classItem.teacher?.name || 'No Teacher' }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
    import { defineProps } from 'vue';

    // Define props for receiving class data
    const props = defineProps({
        classes: Array
    });
</script>
