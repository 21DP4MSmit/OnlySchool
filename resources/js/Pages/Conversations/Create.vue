<template>
    <AuthenticatedLayout>
        <div class="max-w-5xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Create New Conversation</h1>

            <form @submit.prevent="submitForm" class="space-y-6 bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <!-- Subject input with dynamic styling -->
                <div class="space-y-1">
                    <label for="subject" class="block text-sm font-medium" :class="{
                        'text-purple-600': form.recipients.length > 1,
                        'text-emerald-600': form.recipients.length === 1,
                        'text-gray-700': form.recipients.length === 0
                    }">Tituls</label>
                    <input
                        v-model="form.subject"
                        :disabled="isOneOnOne"
                        type="text"
                        id="subject"
                        placeholder="Ievadīt titulu..."
                        class="w-full rounded-lg border-gray-300 shadow-sm transition duration-200"
                        :class="{
                            'focus:ring-purple-500 focus:border-purple-500': form.recipients.length > 1,
                            'focus:ring-emerald-500 focus:border-emerald-500': form.recipients.length === 1,
                            'focus:ring-blue-500 focus:border-blue-500': form.recipients.length === 0
                        }"
                    />
                </div>

                <!-- User selection section -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <label class="block text-sm font-medium text-gray-700">Izvēlēties dalībniekus</label>
                        <span class="text-sm" :class="{
                            'text-purple-600': form.recipients.length > 1,
                            'text-emerald-600': form.recipients.length === 1,
                            'text-gray-500': form.recipients.length === 0
                        }">
                            {{ form.recipients.length }} selected
                        </span>
                    </div>

                    <!-- Search bar for users -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input 
                            v-model="searchQuery"
                            type="text"
                            placeholder="Meklēt dalībniekus..."
                            class="w-full pl-10 rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                        />
                    </div>

                    <!-- User list with load more functionality -->
                    <div ref="userListContainer" class="border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                        <div class="max-h-96 overflow-y-auto divide-y divide-gray-200">
                            <div v-for="user in paginatedUsers" :key="user.id" 
                                 class="flex items-center justify-between p-4 hover:bg-gray-50 transition duration-150">
                                <span class="text-gray-900 font-medium">{{ user.name }}</span>
                                <button 
                                    type="button" 
                                    @click="toggleRecipient(user.id)"
                                    :class="{
                                        'bg-green-500 hover:bg-green-600': isSelected(user.id),
                                        'bg-gray-500 hover:bg-gray-600': !isSelected(user.id),
                                    }"
                                    class="px-4 py-2 rounded-lg text-white font-medium text-sm transition duration-150 transform hover:scale-105"
                                >
                                    {{ isSelected(user.id) ? 'Izvēlēts' : 'Izvēlēt' }}
                                </button>
                            </div>
                            <div ref="loadMoreUsersTrigger" v-if="hasMoreUsers" 
                                 class="text-center py-4 text-gray-500 bg-gray-50">
                                <div class="animate-pulse">Loading more users...</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Initial Message -->
                <div>
                    <label for="initialMessage" class="block text-sm font-medium text-gray-700 mb-1">Sākuma ziņa</label>
                    <textarea
                        v-model="form.initialMessage"
                        id="initialMessage"
                        placeholder="Rakstīt ziņu..."
                        rows="4"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                    ></textarea>
                </div>

                <!-- Submit button -->
                <div>
                    <button
                        type="submit"
                        class="w-full sm:w-auto px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transform transition duration-150 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        Izveidot saraksti
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>


<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

export default {
    props: {
        users: Array,
    },
    data() {
        return {
            form: {
                subject: '',
                recipients: [],
                initialMessage: '',
            },
            searchQuery: '',
            usersPerPage: 10,
            currentUserPage: 1,
            hasMoreUsers: true,
        };
    },
    computed: {
        isOneOnOne() {
            return this.form.recipients.length === 1;
        },
        filteredUsers() {
            return this.users.filter(user => 
                user.name.toLowerCase().includes(this.searchQuery.toLowerCase())
            );
        },
        paginatedUsers() {
            return this.filteredUsers.slice(0, this.currentUserPage * this.usersPerPage);
        }
    },
    watch: {
        isOneOnOne(newVal) {
            if (newVal && this.form.recipients.length === 1) {
                this.form.subject = this.getParticipantName(this.form.recipients[0]);
            } else {
                this.form.subject = '';
            }
        }
    },
    methods: {
        setupUserIntersectionObserver() {
            const options = {
                root: this.$refs.userListContainer,
                rootMargin: '0px',
                threshold: 1.0,
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && this.hasMoreUsers) {
                        this.loadMoreUsers();
                    }
                });
            }, options);

            if (this.$refs.loadMoreUsersTrigger) {
                observer.observe(this.$refs.loadMoreUsersTrigger);
            }
        },
        loadMoreUsers() {
            if (this.currentUserPage * this.usersPerPage >= this.filteredUsers.length) {
                this.hasMoreUsers = false;
            } else {
                this.currentUserPage++;
            }
        },
        toggleRecipient(userId) {
            const index = this.form.recipients.indexOf(userId);
            if (index !== -1) {
                this.form.recipients.splice(index, 1);
            } else {
                this.form.recipients.push(userId);
            }
        },
        isSelected(userId) {
            return this.form.recipients.includes(userId);
        },
        getParticipantName(userId) {
            const user = this.users.find(user => user.id === userId);
            return user ? user.name : '';
        },
        submitForm() {
            this.$inertia.post(route('conversations.store'), this.form);
        }
    },
    mounted() {
        this.setupUserIntersectionObserver();
    }
};
</script>
