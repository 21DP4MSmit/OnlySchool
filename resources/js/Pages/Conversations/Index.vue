<template>
    <AuthenticatedLayout>
        <div class="max-w-5xl mx-auto py-10">
            <h1 class="text-3xl font-bold mb-6">Saziņa</h1>

            <!-- Button to create a new conversation -->
            <button 
                @click="createNewConversation"
                class="mb-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-300"
            >
                Jauna sarakste
            </button>

            <!-- Conversations list -->
            <ul class="space-y-4">
                <li 
                    v-for="conversation in sortedConversations" 
                    :key="conversation.id" 
                    class="bg-white shadow-md rounded-lg p-4 flex justify-between items-center" 
                    :class="{'bg-gray-100': hasUnreadMessages(conversation)}"
                >
                    <div>
                        <Link :href="route('conversations.show', conversation.id)" class="text-xl font-semibold text-gray-900 hover:text-blue-500 transition duration-300">
                            {{ conversation.subject || getParticipants(conversation) }}
                        </Link>
                        
                        <p v-if="!isGroupChat(conversation)" class="text-gray-600 text-sm">
                            {{ getParticipants(conversation) }}
                        </p>
                        
                        <!-- Display the latest message timestamp -->
                        <p class="text-gray-500 text-xs">
                            Pēdēja ziņa: {{ formatTimestamp(getLatestMessage(conversation).created_at) }}
                        </p>
                    </div>
                    <div v-if="hasUnreadMessages(conversation)" class="text-red-500 font-bold">Unread Messages</div>
                </li>
            </ul>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import { Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { format } from 'date-fns';

export default {
    props: {
        conversations: Array,
    },
    components: {
        Link,
    },
    computed: {
        sortedConversations() {
            return [...this.conversations].sort((a, b) => {
                const latestMessageA = this.getLatestMessage(a);
                const latestMessageB = this.getLatestMessage(b);
                return new Date(latestMessageB.created_at) - new Date(latestMessageA.created_at);
            });
        }
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
            return conversation.messages.some(message => 
                !message.is_read && message.user_id !== this.$page.props.auth.user.id
            );
        },
        getLatestMessage(conversation) {
            return conversation.messages.reduce((latest, current) => 
                new Date(current.created_at) > new Date(latest.created_at) ? current : latest
            );
        },
        formatTimestamp(timestamp) {
            return format(new Date(timestamp), 'PPpp');
        }
    },
};
</script>
