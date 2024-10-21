<template>
    <AuthenticatedLayout>
        <div class="max-w-5xl mx-auto py-10">
            <button 
                @click="goBack"
                class="mb-4 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition duration-300"
            >
                Back to Conversations
            </button>

            <h1 class="text-3xl font-bold mb-6">{{ conversation.subject || 'Conversation' }}</h1>

            <div ref="chatContainer" class="border border-gray-300 rounded-lg p-4 max-h-96 overflow-y-auto space-y-6">
                <div v-for="(message, index) in conversation.messages" :key="message.id" class="space-y-2">
                    <p v-if="shouldShowTimestamp(index)" class="text-gray-500 text-sm text-center">
                        {{ formatTimestamp(message.created_at) }}
                    </p>
                    <div class="flex items-start">
                        <p><strong>{{ message.user.name }}:</strong> {{ message.text }}</p>
                    </div>
                </div>
            </div>

            <form @submit.prevent="sendMessage" class="mt-6 flex">
                <input 
                    v-model="text" 
                    placeholder="Type a message..." 
                    class="flex-grow border rounded-l-lg px-4 py-2"
                />
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-r-lg">
                    Send
                </button>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { format, differenceInMinutes } from 'date-fns';

export default {
    props: {
        conversation: Object,
    },
    data() {
        return {
            text: '',
        };
    },
    methods: {
        goBack() {
            this.$inertia.get(route('conversations.index'));
        },
        sendMessage() {
            this.$inertia.post(route('messages.store', this.conversation.id), {
                text: this.text,
            }, {
                onSuccess: () => {
                    this.text = '';
                    this.scrollToBottom();
                }
            });
        },
        scrollToBottom() {
            const chatContainer = this.$refs.chatContainer;
            if (chatContainer) {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
        },
        formatTimestamp(timestamp) {
            return format(new Date(timestamp), 'PPpp');
        },
        shouldShowTimestamp(index) {
            if (index === 0) return true;

            const currentMessageTime = new Date(this.conversation.messages[index].created_at);
            const previousMessageTime = new Date(this.conversation.messages[index - 1].created_at);

            return differenceInMinutes(currentMessageTime, previousMessageTime) >= 30;
        },
    },
    mounted() {
        this.scrollToBottom();
    }
};
</script>
