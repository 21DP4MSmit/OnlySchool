<template>
    <AuthenticatedLayout>
        <div class="max-w-5xl mx-auto py-10">
            <h1 class="text-3xl font-bold mb-6">Conversations</h1>

            <!-- Button to create a new conversation -->
            <button 
                @click="createNewConversation"
                class="mb-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-300"
            >
                New Conversation
            </button>

            <!-- Conversations list -->
            <ul class="space-y-4">
                <li v-for="conversation in conversations" :key="conversation.id" class="bg-white shadow-md rounded-lg p-4 flex justify-between items-center" :class="{'bg-gray-100': hasUnreadMessages(conversation)}">
                    <div>
                        <Link :href="route('conversations.show', conversation.id)" class="text-xl font-semibold text-gray-900 hover:text-blue-500 transition duration-300">
                            <!-- Display conversation subject or participants -->
                            {{ conversation.subject || getParticipants(conversation) }}
                        </Link>
                        <!-- Display participant names for individual conversations (excluding the current user) -->
                        <p v-if="!isGroupChat(conversation)" class="text-gray-600 text-sm">
                            Messaging: {{ getParticipants(conversation) }}
                        </p>
                    </div>
                    <!-- Show unread message badge if the conversation has unread messages -->
                    <div v-if="hasUnreadMessages(conversation)" class="text-red-500 font-bold">Unread Messages</div>
                </li>
            </ul>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import { Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

export default {
    props: {
        conversations: Array,
    },
    components: {
        Link,
    },
    methods: {
        createNewConversation() {
            this.$inertia.get(route('conversations.create'));
        },
        isGroupChat(conversation) {
            return conversation.participants && conversation.participants.length > 2;
        },
        getParticipants(conversation) {
            if (conversation.participants && conversation.participants.length > 1) {
                const otherParticipants = conversation.participants.filter(p => p.id !== this.$page.props.auth.user.id);
                return otherParticipants.map(p => p.name).join(', ');
            } else {
                return 'No other participants';
            }
        },
        hasUnreadMessages(conversation) {
            // Check if there are unread messages
            return conversation.messages.some(message => !message.is_read && message.user_id !== this.$page.props.auth.user.id);
        }
    },
};
</script>
