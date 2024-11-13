<template>
    <AuthenticatedLayout>
        <div class="max-w-5xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Saziņa</h1>
                <button 
                    @click="createNewConversation"
                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transform transition duration-150 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    Jauna sarakste
                </button>
            </div>

            <div class="relative mb-6">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </div>
                <input 
                    v-model="searchQuery"
                    type="text"
                    placeholder="Meklēt..."
                    class="w-full pl-10 rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                />
            </div>

            <div ref="conversationContainer" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <ul class="divide-y divide-gray-200">
                    <li 
                        v-for="conversation in paginatedConversations" 
                        :key="conversation.id" 
                        class="hover:bg-gray-50/80 transition-all duration-200"
                        :class="{
                            'bg-blue-50/70': hasUnreadMessages(conversation),
                            'border-l-4 border-purple-400': isGroupChat(conversation),
                            'border-l-4 border-emerald-400': !isGroupChat(conversation)
                        }"
                    >
                        <div class="p-6">
                            <div class="flex justify-between items-start">
                                <div class="flex-1 min-w-0">
                                    <Link 
                                        :href="route('conversations.show', conversation.id)" 
                                        class="group flex items-center space-x-2 text-lg font-semibold text-gray-900 hover:text-blue-600 transition duration-150"
                                    >
                                        <!-- Group Chat Icon -->
                                        <span v-if="isGroupChat(conversation)" class="flex items-center justify-center w-8 h-8 rounded-lg bg-purple-100 text-purple-600 group-hover:bg-purple-200 transition-colors duration-200">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </span>
                                        <!-- Individual Chat Icon -->
                                        <span v-else class="flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 group-hover:bg-emerald-200 transition-colors duration-200">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </span>
                                        <span>{{ isGroupChat(conversation) ? (conversation.subject || 'Group Chat') : getOtherParticipantName(conversation) }}</span>
                                    </Link>
                                    
                                    <p v-if="isGroupChat(conversation)" class="mt-1 text-sm text-gray-500 pl-10">
                                        {{ getParticipants(conversation) }}
                                    </p>
                                    
                                    <p class="mt-2 text-sm text-gray-400 pl-10">
                                        Pēdēja ziņa: {{ formatTimestamp(getLatestMessage(conversation).created_at) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
                <div ref="loadMoreTrigger" v-if="hasMoreConversations" 
                     class="text-center py-6 bg-gray-50 text-gray-500">
                    <div class="animate-pulse">Lādē vēl saziņas...</div>
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
