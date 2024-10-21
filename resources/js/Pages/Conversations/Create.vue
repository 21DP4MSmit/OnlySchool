<template>
    <AuthenticatedLayout>
        <div class="max-w-5xl mx-auto py-10">
            <h1 class="text-3xl font-bold mb-6">Create New Conversation</h1>

            <form @submit.prevent="submitForm" class="space-y-4">
                <!-- Subject (Optional) -->
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                    <input
                        v-model="form.subject"
                        type="text"
                        id="subject"
                        placeholder="Enter subject"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                    />
                </div>

                <!-- Select Users -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Select Users</label>
                    <div class="space-y-2">
                        <div v-for="user in users" :key="user.id" class="flex items-center justify-between p-2 border border-gray-300 rounded-lg">
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
                    </div>
                </div>

                <!-- Initial Message -->
                <div>
                    <label for="initialMessage" class="block text-sm font-medium text-gray-700">Initial Message</label>
                    <textarea
                        v-model="form.initialMessage"
                        id="initialMessage"
                        placeholder="Type your message..."
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                    ></textarea>
                </div>

                <!-- Submit Button -->
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
        users: Array, // List of users passed from the backend
    },
    data() {
        return {
            form: {
                subject: '',
                recipients: [],
                initialMessage: '',
            },
        };
    },
    methods: {
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

        submitForm() {
            this.$inertia.post(route('conversations.store'), this.form);
        }
    }
};
</script>
