<template>
    <AuthenticatedLayout>
        <div class="max-w-5xl mx-auto py-10">
            <h1 class="text-3xl font-bold mb-6">Create New Conversation</h1>

            <form @submit.prevent="submitForm" class="space-y-4">
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                    <input
                        v-model="form.subject"
                        :disabled="isOneOnOne"
                        type="text"
                        id="subject"
                        placeholder="Enter subject"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                    />
                </div>
                
                <div class="mb-4">
                    <input 
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search users..."
                        class="w-full border border-gray-300 rounded-lg p-2 focus:border-blue-500"
                    />
                </div>
                
                <div ref="userListContainer" class="border border-gray-300 rounded-lg max-h-96 overflow-y-auto space-y-2">
                    <div v-for="user in paginatedUsers" :key="user.id" class="flex items-center justify-between p-2 border border-gray-300 rounded-lg">
                        <span>{{ user.name }}</span>
                        <button 
                            type="button" 
                            @click="toggleRecipient(user.id)"
                            :class="{
                                'bg-green-500': isSelected(user.id),
                                'bg-gray-500': !isSelected(user.id),
                            }"
                            class="text-white font-bold py-1 px-3 rounded transition duration-300"
                        >
                            {{ isSelected(user.id) ? 'Selected' : 'Select' }}
                        </button>
                    </div>
                    <div ref="loadMoreUsersTrigger" v-if="hasMoreUsers" class="text-center py-4 text-gray-500">
                        Loading more users...
                    </div>
                </div>

                <div>
                    <label for="initialMessage" class="block text-sm font-medium text-gray-700">Initial Message</label>
                    <textarea
                        v-model="form.initialMessage"
                        id="initialMessage"
                        placeholder="Type your message..."
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                    ></textarea>
                </div>

                <div>
                    <button
                        type="submit"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300"
                    >
                        Start Conversation
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
