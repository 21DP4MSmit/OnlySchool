<template>
    <div class="day-table bg-white shadow-md rounded-lg p-4 h-48">
        <!-- Fixed height for tables -->
        <h2 class="text-lg font-semibold mb-2 text-center">{{ day }}</h2>
        <table class="min-w-full border border-gray-300">
            <thead>
                <tr>
                    <th class="border-b border-gray-300 px-4 py-2">Stunda</th>
                    <th class="border-b border-gray-300 px-4 py-2">Priekšmets</th>
                </tr>
            </thead>
            <tbody>
                <!-- Initially, we can just leave this blank -->
                <tr>
                    <td class="border-b border-gray-300 px-4 py-2" colspan="2">Nav informācijas</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
    import { defineProps } from 'vue';

    const props = defineProps({
        day: {
            type: String,
            required: true
        }
    });
</script>

<style scoped>
    .day-table {
        width: 100%; /* Make tables responsive */
    }
</style>
