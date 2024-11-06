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

            <!-- Search Bar -->
            <div class="mb-4">
                <input 
                    v-model="searchQuery"
                    type="text"
                    placeholder="Search conversations..."
                    class="w-full border border-gray-300 rounded-lg p-2 focus:border-blue-500"
                />
            </div>

            <!-- Conversations list -->
            <div ref="conversationContainer" class="space-y-4 max-h-96 overflow-y-auto border border-gray-300 rounded-lg p-4">
                <ul>
                    <li 
                        v-for="conversation in paginatedConversations" 
                        :key="conversation.id" 
                        class="bg-white shadow-md rounded-lg p-4 flex justify-between items-center relative" 
                        :class="{'bg-gray-100': hasUnreadMessages(conversation)}"
                    >
                        <div class="flex-1">
                            <Link :href="route('conversations.show', conversation.id)" class="text-xl font-semibold text-gray-900 hover:text-blue-500 transition duration-300">
                                <!-- Display the other participant's name if one-on-one, otherwise show the subject -->
                                {{ isGroupChat(conversation) ? (conversation.subject || 'Conversation') : getOtherParticipantName(conversation) }}
                            </Link>
                            
                            <!-- Remove participants display for one-on-one -->
                            <p v-if="isGroupChat(conversation)" class="text-gray-600 text-sm">
                                {{ getParticipants(conversation) }}
                            </p>
                            
                            <!-- Display the latest message timestamp -->
                            <p class="text-gray-500 text-xs">
                                Pēdēja ziņa: {{ formatTimestamp(getLatestMessage(conversation).created_at) }}
                            </p>
                        </div>

                        <!-- Delete Button -->
                        <button @click="deleteConversation(conversation.id)" class="text-red-500 hover:text-red-700">
                            Delete
                        </button>
                    </li>
                </ul>

                <!-- Infinite scroll loading trigger -->
                <div ref="loadMoreTrigger" v-if="hasMoreConversations" class="text-center py-4 text-gray-500">
                    Loading more conversations...
                </div>
            </div>
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
    data() {
        return {
            searchQuery: '',
            conversationsPerPage: 10,
            currentPage: 1,
            hasMoreConversations: true,
        };
    },
    computed: {
        filteredConversations() {
            return this.sortedConversations.filter((conversation) => {
                const subjectMatch = conversation.subject?.toLowerCase().includes(this.searchQuery.toLowerCase());
                const participantMatch = this.getParticipants(conversation).toLowerCase().includes(this.searchQuery.toLowerCase());
                return subjectMatch || participantMatch;
            });
        },
        paginatedConversations() {
            return this.filteredConversations.slice(0, this.currentPage * this.conversationsPerPage);
        },
        sortedConversations() {
            return [...this.conversations].sort((a, b) => {
                const latestMessageA = this.getLatestMessage(a);
                const latestMessageB = this.getLatestMessage(b);
                return new Date(latestMessageB.created_at) - new Date(latestMessageA.created_at);
            });
        },
    },
    methods: {
        setupIntersectionObserver() {
            const options = {
                root: this.$refs.conversationContainer,
                rootMargin: '0px',
                threshold: 1.0,
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && this.hasMoreConversations) {
                        this.loadMoreConversations();
                    }
                });
            }, options);

            if (this.$refs.loadMoreTrigger) {
                observer.observe(this.$refs.loadMoreTrigger);
            }
        },
        loadMoreConversations() {
            if (this.currentPage * this.conversationsPerPage >= this.filteredConversations.length) {
                this.hasMoreConversations = false;
            } else {
                this.currentPage++;
            }
        },
        createNewConversation() {
            this.$inertia.get(route('conversations.create'));
        },
        deleteConversation(conversationId) {
            if (confirm("Are you sure you want to delete this conversation?")) {
                this.$inertia.delete(route('conversations.destroy', conversationId), {
                    onSuccess: () => {

                    },
                    onError: (error) => {
                        console.error(error);
                    }
                });
            }
        },
        isGroupChat(conversation) {
            return conversation.participants && conversation.participants.length > 2;
        },
        getParticipants(conversation) {
            if (this.isGroupChat(conversation)) {
                const otherParticipants = conversation.participants.filter(p => p.id !== this.$page.props.auth.user.id);
                return otherParticipants.map(p => p.name).join(', ');
            } else {
                return this.getOtherParticipantName(conversation);
            }
        },
        getOtherParticipantName(conversation) {
            const otherParticipants = conversation.participants.filter(p => p.id !== this.$page.props.auth.user.id);
            return otherParticipants[0]?.name || 'Unknown Participant'; // Handle case where there's no other participant
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
    mounted() {
        this.setupIntersectionObserver();
    }
};
</script>
