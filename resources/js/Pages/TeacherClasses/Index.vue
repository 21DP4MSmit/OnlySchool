<template>
    <AuthenticatedLayout>
        <div class="container mx-auto px-4 py-8">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight leading-tight">Dienasgrāmata</h1>
                <span class="text-lg font-semibold text-gray-600 bg-gray-200 py-2 px-4 rounded-lg shadow-sm">
                    {{ currentDate }} (Šodien)
                </span>
            </div>

            <div class="flex justify-between items-center mb-8">
                <button @click="prevWeek" class="text-4xl text-blue-600 hover:text-blue-700 transition transform hover:scale-105 font-bold">
                    &lt;
                </button>
                <span class="text-lg font-semibold bg-blue-100 py-2 px-6 rounded-lg shadow-sm">
                    {{ formattedWeekRange }}
                </span>
                <button @click="nextWeek" class="text-4xl text-blue-600 hover:text-blue-700 transition transform hover:scale-105 font-bold">
                    &gt;
                </button>
            </div>

            <div v-for="(classLists, day) in weekdays" :key="day" class="mb-12">
                <h2 class="text-3xl font-semibold text-gray-800 mb-6">{{ day }}</h2>
                <div v-if="classLists.length" class="overflow-hidden shadow-lg rounded-lg">
                    <table class="min-w-full bg-white rounded-lg border-collapse">
                        <thead class="bg-gradient-to-r from-blue-500 to-blue-700 text-white uppercase text-sm font-semibold">
                            <tr>
                                <th class="w-1/12 py-4 px-6 text-left">#</th>
                                <th class="w-1/12 py-4 px-6 text-left">Time</th>
                                <th class="w-1/4 py-4 px-6 text-left">Subject & Class</th>
                                <th class="w-1/5 py-4 px-6 text-left">Klase</th>
                                <th class="w-1/6 py-4 px-6 text-left">Tēma</th>
                                <th class="w-1/6 py-4 px-6 text-left">Mājasdarbs</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm">
                            <tr v-for="(classItem, index) in classLists" :key="index" class="border-b border-gray-200 hover:bg-gray-50 transition duration-150 ease-in-out">
                                <td class="py-4 px-6">{{ index + 1 }}</td>
                                <td class="py-4 px-6">{{ getTimeForClass(index) }}</td>
                                <td class="py-4 px-6">
                                    <span class="font-bold text-black">{{ classItem.subject?.Name || 'N/A' }}</span><br>
                                    <span class="text-gray-500">Class: {{ classItem.classroom?.Classroom || 'N/A' }}</span>
                                </td>
                                <td class="py-4 px-6">{{ classItem.klase?.Department || 'N/A' }}</td>
                                <td class="py-4 px-6">{{ classItem.topic || 'N/A' }}</td>
                                <td class="py-4 px-6">{{ classItem.homework || 'N/A' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else class="text-center text-gray-500 italic mt-6">
                    Šodien nav ieplānotu stundu.
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { startOfWeek, addDays, addWeeks, format } from 'date-fns';

    export default {
        props: {
            classes: Array,
        },
        data() {
            return {
                currentDate: '',
                currentWeekStart: startOfWeek(new Date(), { weekStartsOn: 1 }),
                lessonTimes: [
                    "07:45 - 08:25", "08:30 - 09:10", "09:15 - 09:55", "10:10 - 10:50",
                    "10:55 - 11:35", "12:05 - 12:45", "12:50 - 13:30", "13:35 - 14:15",
                    "14:20 - 15:00", "15:05 - 15:45", "15:50 - 16:30", "16:35 - 17:15",
                    "17:20 - 18:00"
                ]
            };
        },
        computed: {
            weekdays() {
                return this.groupClassesByDay();
            },
            formattedWeekRange() {
                const start = format(this.currentWeekStart, 'dd.MM.yyyy');
                const end = format(addDays(this.currentWeekStart, 4), 'dd.MM.yyyy');
                return `${start} - ${end}`;
            }
        },
        mounted() {
            this.updateCurrentDate();
            this.loadClassesForWeek();
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
            getTimeForClass(index) {
                return this.lessonTimes[index] || 'Laiks nav pieejams';
            },
            loadClassesForWeek() {
                const weekStartDate = this.currentWeekStart.toISOString().split('T')[0];
                this.$inertia.get('/TeacherClasses', {
                    weekStart: weekStartDate,
                }, {
                    preserveState: true,
                    preserveScroll: true,
                });
            },
            prevWeek() {
                this.currentWeekStart = addWeeks(this.currentWeekStart, -1);
                this.loadClassesForWeek();
            },
            nextWeek() {
                this.currentWeekStart = addWeeks(this.currentWeekStart, 1);
                this.loadClassesForWeek();
            },
            groupClassesByDay() {
                const groupedClasses = {
                    "Pirmdiena": [],
                    "Otrdiena": [],
                    "Trešdiena": [],
                    "Ceturtdiena": [],
                    "Piektdiena": []
                };

                this.classes.forEach((classItem) => {
                    const dayOfWeek = new Date(classItem.Date).getDay();
                    switch (dayOfWeek) {
                        case 1: groupedClasses["Pirmdiena"].push(classItem); break;
                        case 2: groupedClasses["Otrdiena"].push(classItem); break;
                        case 3: groupedClasses["Trešdiena"].push(classItem); break;
                        case 4: groupedClasses["Ceturtdiena"].push(classItem); break;
                        case 5: groupedClasses["Piektdiena"].push(classItem); break;
                    }
                });

                return groupedClasses;
            }
        },
    };
</script>

