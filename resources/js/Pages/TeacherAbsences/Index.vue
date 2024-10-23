<template>
    <AuthenticatedLayout>
        <div class="container mx-auto px-4 py-8">
            <!-- Page Header -->
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight leading-tight">Teacher Absences</h1>
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

            <!-- Subject Lists by Day -->
            <div v-for="(subjectLists, day) in weekdays" :key="day" class="mb-12">
                <h2 class="text-3xl font-semibold text-gray-800 mb-6">{{ day }}</h2>

                <div v-if="subjectLists.length" class="overflow-hidden shadow-lg rounded-lg">
                    <table class="min-w-full bg-white rounded-lg border-collapse">
                        <thead class="bg-gradient-to-r from-blue-500 to-blue-700 text-white uppercase text-sm font-semibold">
                            <tr>
                                <th class="w-1/12 py-4 px-6 text-left">#</th>
                                <th class="w-1/12 py-4 px-6 text-left">Laiks</th>
                                <th class="w-1/4 py-4 px-6 text-left">Priekšmets & Klase</th>
                                <th class="w-1/5 py-4 px-6 text-left">Tēma</th>
                                <th class="w-1/4 py-4 px-6 text-left">Mājasdarbs</th>
                                <th class="w-1/5 py-4 px-6 text-left">Kavējumi</th>
                                <th class="w-1/6 py-4 px-6 text-left">Atzīmes</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm">
                            <tr v-for="(subjectList, index) in subjectLists"
                                :key="subjectList.ListID"
                                class="border-b border-gray-200 hover:bg-gray-50 transition duration-150 ease-in-out cursor-pointer"
                                @click="openModal(subjectList)">
                                <td class="py-4 px-6">{{ index + 1 }}</td>
                                <td class="py-4 px-6">{{ getTimeForLesson(index) }}</td>
                                <td class="py-4 px-6">
                                    <span class="font-bold text-blue-600">{{ subjectList.subject.Name }}</span><br>
                                    <span class="text-gray-500">Klase: {{ subjectList.classroom.Classroom }}</span>
                                </td>
                                <td class="py-4 px-6 truncate max-w-xs">{{ subjectList.topic || 'Tēma nav pieejama' }}</td>
                                <td class="py-4 px-6 truncate max-w-xs">
                                    <span v-html="convertTextWithLinks(subjectList.homework || 'Mājasdarbs nav piešķirts')"></span>
                                </td>
                                <td class="py-4 px-6">
                                    <template v-for="absence in subjectList.absences" :key="absence.AbsenceID">
                                        <span v-if="absence.Absence === 1" class="inline-block w-4 h-4 bg-green-500 rounded-full"></span>
                                        <span v-if="absence.Absence === 2" class="inline-block w-4 h-4 bg-red-500 rounded-full"></span>
                                    </template>
                                    <span v-if="!subjectList.absences.length" class="inline-block w-4 h-4 bg-gray-400 rounded-full"></span>
                                </td>
                                <td class="py-4 px-6">
                                    <span v-if="subjectList.marks.length && subjectList.marks[0].mark" class="font-semibold">
                                        {{ subjectList.marks[0].mark }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-else class="text-center text-gray-500 italic mt-6">
                    Nav priekšmetu šajā dienā.
                </div>
            </div>

            <!-- Modal for User Absences -->
            <div v-if="showModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                <div class="bg-white rounded-lg shadow-lg w-11/12 md:w-1/2 lg:w-1/3 p-6 relative">
                    <button @click="closeModal" class="absolute top-2 right-2 text-gray-600 hover:text-gray-800">
                        &times;
                    </button>
                    <h3 class="text-xl font-bold mb-4">Mark Absences for {{ selectedSubject.subject.Name }}</h3>

                    <div class="mb-4">
                        <label class="block text-gray-700">Class:</label>
                        <input type="text" :value="selectedSubject.classroom.Classroom" class="w-full border border-gray-300 rounded-md p-2" readonly />
                    </div>

                    <!-- Absence Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2">Name</th>
                                    <th class="px-4 py-2">Date</th>
                                    <th class="px-4 py-2">Absence</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="user in users" :key="user.id">
                                    <td class="border px-4 py-2">{{ user.name }}</td>
                                    <td class="border px-4 py-2">{{ currentDate }}</td>
                                    <td class="border px-4 py-2">
                                        <select v-model="selectedAbsence[user.id]" class="w-full border border-gray-300 rounded-md p-2">
                                            <option value="">-- Select --</option>
                                            <option value="1">Attaisnots</option>
                                            <option value="2">Kavējums</option>
                                        </select>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="text-center mt-4">
                        <button @click="saveAbsences" class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-md shadow-sm mr-2">
                            Save Absences
                        </button>
                        <button @click="closeModal" class="bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-md shadow-sm">
                            Cancel
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
        },
        data() {
            return {
                currentDate: '',
                currentWeekStart: startOfWeek(new Date(), { weekStartsOn: 1 }), // Start from Monday
                lessonTimes: [
                    "07:45 - 08:25", "08:30 - 09:10", "09:15 - 09:55", "10:10 - 10:50",
                    "10:55 - 11:35", "12:05 - 12:45", "12:50 - 13:30", "13:35 - 14:15",
                    "14:20 - 15:00", "15:05 - 15:45", "15:50 - 16:30", "16:35 - 17:15",
                    "17:20 - 18:00"
                ],
                showModal: false,
                selectedSubject: null,
                users: [],
                selectedAbsence: {}
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

                    const normalizedLessonDate = new Date(lessonDate.toISOString().split('T')[0]);
                    const normalizedWeekStart = new Date(this.currentWeekStart.toISOString().split('T')[0]);
                    const normalizedWeekEnd = new Date(normalizedWeekStart);
                    normalizedWeekEnd.setDate(normalizedWeekEnd.getDate() + 4); // Monday-Friday range

                    if (normalizedLessonDate >= normalizedWeekStart && normalizedLessonDate <= normalizedWeekEnd) {
                        const day = normalizedLessonDate.toLocaleDateString('en-US', { weekday: 'long' });
                        if (!groups[day]) {
                            groups[day] = [];
                        }
                        groups[day].push(subjectList);
                    }
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
   getTimeForLesson(index) {
       return this.lessonTimes[index] || 'Laiks nav pieejams';
   },
   openModal(subject) {
        this.selectedSubject = subject;
        this.showModal = true;
    // Use subject.classroom.ClassID instead of subject.classroom.Classroom
        this.fetchUsers(subject.classroom.ClassID);  // Fetch users by class_id (not classroom)
    },
   closeModal() {
       this.showModal = false;
       this.selectedSubject = null;
   },
   async fetchUsers(classroomId) {
       try {
           const response = await axios.get(`/users-by-class/${classroomId}`);
           this.users = response.data;  // Populate the users array with the fetched data
       } catch (error) {
           console.error('Error fetching users:', error);
       }
   },
   saveAbsences() {
    // Prepare the absence data to be sent
    const absencesData = {
        subject_id: this.selectedSubject.SubjectID,
        class_id: this.selectedSubject.classroom.ClassroomID,
        absences: this.selectedAbsence
    };

    console.log('Data to be sent:', absencesData); // Log the data for debugging

    // Send the data to the server
    axios.post('/save-absences', absencesData)
        .then(response => {
            console.log('Absences saved successfully', response);
            this.closeModal();
        })
        .catch(error => {
            console.error('Error saving absences:', error);
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
   // Here's the function we're using
   convertTextWithLinks(text) {
       const urlRegex = /(https?:\/\/[^\s]+)/g;
       return text.replace(urlRegex, (url) => `<a href="${url}" class="text-blue-500 hover:underline" target="_blank">${url}</a>`);
   }
},
    };
</script>
