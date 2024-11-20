<template>
    <AuthenticatedLayout>
        <div class="container mx-auto px-4 py-8">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight leading-tight">Atzīmēšana</h1>
                <span class="text-lg font-semibold text-gray-600 bg-gray-200 py-2 px-4 rounded-lg shadow-sm">
                    {{ currentDate }} (Šodien)
                </span>
            </div>

            <!-- Week Navigation -->
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

            <!-- Absences Table by Day -->
            <div v-for="(classLists, day) in weekdays" :key="day" class="mb-12">
                <h2 class="text-3xl font-semibold text-gray-800 mb-6">{{ day }}</h2>
                <div v-if="classLists.length" class="overflow-hidden shadow-lg rounded-lg">
                    <table class="min-w-full bg-white rounded-lg border-collapse">
                        <thead class="bg-gradient-to-r from-blue-500 to-blue-700 text-white uppercase text-sm font-semibold">
                            <tr>
                                <th class="w-1/12 py-4 px-6 text-left">Stunda</th>
                                <th class="w-1/12 py-4 px-6 text-left">Time</th>
                                <th class="w-1/4 py-4 px-6 text-left">Stunda un klase</th>
                                <th class="w-1/5 py-4 px-6 text-left">Klase</th>
                                <th class="w-1/6 py-4 px-6 text-left">Tēma</th>
                                <th class="w-1/6 py-4 px-6 text-left">Mājasdarbs</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm">
                            <tr v-for="(classItem, index) in classLists" :key="index" class="border-b border-gray-200 hover:bg-gray-50 transition duration-150 ease-in-out cursor-pointer" @click="openModal(classItem)">
                                <td class="py-4 px-6">{{ index + 1 }}</td>
                                <td class="py-4 px-6">{{ getTimeForClass(index) }}</td>
                                <td class="py-4 px-6">
                                    <span class="font-bold text-black">{{ classItem.subject?.Name || 'N/A' }}</span><br>
                                    <span class="text-gray-500">Klases istaba: {{ classItem.classroom?.Classroom || 'N/A' }}</span>
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

            <!-- Modal for Absence Details -->
            <div v-if="showModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                <div class="bg-white rounded-lg shadow-lg w-11/12 md:w-3/4 lg:w-1/2 p-6 relative">
                    <button @click="closeModal" class="absolute top-2 right-2 text-gray-600 hover:text-gray-800">
                        &times;
                    </button>
                    <h3 class="text-xl font-bold mb-4">Mark Absences for {{ selectedSubject.subject?.Name }}</h3>

                    <div class="mb-4">
                        <label class="block text-gray-700">Klase</label>
                        <input type="text" :value="selectedSubject.classroom?.Classroom || 'N/A'" class="w-full border border-gray-300 rounded-md p-2" readonly />
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2">Vārds</th>
                                    <th class="px-4 py-2">Datums</th>
                                    <th class="px-4 py-2">Kavējums</th>
                                    <th class="px-4 py-2">Atzīmes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="user in users" :key="user.id">
                                    <td class="border px-4 py-2">{{ user.name }}</td>
                                    <td class="border px-4 py-2">{{ currentDate }}</td>
                                    <td class="border px-4 py-2">
                                        <select v-model="selectedAbsence[user.id]" class="w-full border border-gray-300 rounded-md p-2">
                                            <option value="">          </option>
                                            <option value="1">Attaisnots</option>
                                            <option value="2">Kavējums</option>
                                        </select>
                                    </td>
                                    <td class="border px-4 py-2">
                                        <input type="text" v-model="selectedAtzime[user.id]" class="w-full border border-gray-300 rounded-md p-2" placeholder="Ievadi atzīmi" />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="text-center mt-4">
                        <button @click="saveAbsences" class="bg-green-600 hover:bg-green-700 text-white py-2 px-6 rounded-md shadow-sm mr-2">
                            Saglabāt
                        </button>
                        <button @click="closeModal" class="bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-md shadow-sm">
                            Atcelt
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { startOfWeek, addDays, addWeeks, format } from 'date-fns';
    import axios from 'axios';

    export default {
        props: {
            subjectLists: Array,
            absenceData: {
                type: Array,
                default: () => [] // Set default to an empty array
            },
            markData: {
                type: Array,
                default: () => [] // Set default to an empty array
            },
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
                ],
                showModal: false,
                selectedSubject: null,
                users: [],
                selectedAbsence: {},
                selectedAtzime: {}
            };
        },
        computed: {
            weekdays() {
                return {
                    "Pirmdiena": this.groupedSubjectLists["Monday"] || [],
                    "Otrdiena": this.groupedSubjectLists["Tuesday"] || [],
                    "Trešdiena": this.groupedSubjectLists["Wednesday"] || [],
                    "Ceturtdiena": this.groupedSubjectLists["Thursday"] || [],
                    "Piektdiena": this.groupedSubjectLists["Friday"] || [],
                };
            },
            groupedSubjectLists() {
                return this.subjectLists.reduce((groups, subjectList) => {
                    const lessonDate = new Date(subjectList.Date);
                    const day = lessonDate.toLocaleDateString('en-US', { weekday: 'long' });
                    if (!groups[day]) {
                        groups[day] = [];
                    }
                    groups[day].push(subjectList);
                    return groups;
                }, {});
            },
            formattedWeekRange() {
                const start = format(this.currentWeekStart, 'dd.MM.yyyy');
                const end = format(addDays(this.currentWeekStart, 4), 'dd.MM.yyyy');
                return `${start} - ${end}`;
            },
        },
        mounted() {
            this.updateCurrentDate();
            this.loadLessonsForWeek();
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
            openModal(subject) {
                this.selectedSubject = subject;
                const classId = subject.klase?.ClassID; 
                console.log('ClassID passed to fetchUsers:', classId);
                this.fetchUsers(classId)
                    .then(() => {
                        this.populateExistingData(); 
                        this.showModal = true;
                    })
                    .catch(error => {
                        console.error('Error fetching users:', error);
                    });
            },
            async fetchUsers(classId) {
                try {
                    console.log('Fetching users for ClassID:', classId);
                    const response = await axios.get(`/user-by-class/${classId}`);
                    this.users = response.data;
                    console.log('Fetched users:', this.users);
                } catch (error) {
                    console.error('Error fetching users:', error);
                }
            },
            populateExistingData() {
                // Populate absence data
                if (Array.isArray(this.absenceData)) {
                    this.absenceData.forEach(absence => {
                        this.selectedAbsence[absence.UserID] = absence.Absence;
                    });
                }

                // Populate mark data
                if (Array.isArray(this.markData)) {
                    this.markData.forEach(mark => {
                        this.selectedAtzime[mark.UserID] = mark.mark;
                    });
                }
            },
            closeModal() {
                this.showModal = false;
                this.selectedSubject = null;
                this.users = [];
            },
            saveAbsences() {
                const dateParts = this.currentDate.split('.');
                const formattedDate = new Date(`${dateParts[2]}-${dateParts[1]}-${dateParts[0]}`).toISOString().split('T')[0];

                const atzimesData = {};
                for (const [userId, grade] of Object.entries(this.selectedAtzime)) {
                    const numericGrade = Number(grade);

                    // Allow grades from 0 to 10
                    if (numericGrade >= 0 && numericGrade <= 10) {
                        atzimesData[userId] = numericGrade.toString();
                    } else {
                        alert("Kļūda, ievadi skaitli starp 0 un 10!");
                        return; // Exit the function if invalid input is detected
                    }
                }

                const absencesData = {
                    subject_id: this.selectedSubject.SubjectID,
                    class_id: this.selectedSubject.klase?.ClassID,
                    date: formattedDate,
                    absences: this.selectedAbsence,
                    atzimes: atzimesData
                };

                axios.post('/save-absences', absencesData)
                    .then(response => {
                        this.closeModal();
                    })
                    .catch(error => {
                        console.error('Error saving absences and grades:', error);
                    });
            },
            loadLessonsForWeek() {
                const weekStartDate = this.currentWeekStart.toISOString().split('T')[0];
                this.$inertia.get('/TeacherAbsences', {
                    weekStart: weekStartDate,
                }, {
                    preserveState: true,
                });
            },
            prevWeek() {
                this.currentWeekStart = addWeeks(this.currentWeekStart, -1);
                this.loadLessonsForWeek();
            },
            nextWeek() {
                this.currentWeekStart = addWeeks(this.currentWeekStart, 1);
                this.loadLessonsForWeek();
            },
            convertTextWithLinks(text) {
                const urlRegex = /(https?:\/\/[^\s]+)/g;
                return text.replace(urlRegex, (url) => `<a href="${url}" class="text-blue-500 hover:underline" target="_blank">${url}</a>`);
            }
        }
    };
</script>
