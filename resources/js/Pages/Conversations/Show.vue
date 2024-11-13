<template>
    <AuthenticatedLayout>
        <div class="max-w-5xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex space-x-4 mb-6">
                <button 
                    @click="goBack"
                    class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Atpakaļ
                </button>

                <button 
                    @click="deleteConversation"
                    class="inline-flex items-center px-4 py-2 bg-red-50 text-red-700 hover:bg-red-100 rounded-lg transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Dzēst saraksti
                </button>

                <button 
                    @click="showParticipants"
                    class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Skatīt dalībnieku
                </button>
            </div>

            <!-- Enhanced chat interface -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 border-b border-gray-200" :class="{
                    'bg-purple-50': isGroupChat,
                    'bg-emerald-50': !isGroupChat
                }">
                    <h1 class="text-2xl font-semibold text-gray-900 flex items-center space-x-3">
                        <span :class="{
                            'text-purple-600': isGroupChat,
                            'text-emerald-600': !isGroupChat
                        }">
                            <!-- Group Chat Icon -->
                            <svg v-if="isGroupChat" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <!-- Individual Chat Icon -->
                            <svg v-else class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </span>
                        <span>{{ isGroupChat ? conversation.subject || 'Group Chat' : getOtherParticipantName }}</span>
                    </h1>
                </div>

                <!-- Chat messages container -->
                <div ref="chatContainer" class="p-4 h-[32rem] overflow-y-auto scroll-smooth space-y-6 bg-gray-50">
                    <div 
                        v-for="(message, index) in conversation.messages" 
                        :key="message.id" 
                        class="flex space-x-4 animate-fade-in"
                    >
                        <div class="flex-shrink-0">
                            <img 
                                :src="getProfilePicture(message.user.profilePicturePath)" 
                                alt="User Avatar" 
                                class="w-10 h-10 rounded-full object-cover ring-2 ring-white shadow-sm"
                            />
                        </div>

                        <div class="flex-1 space-y-1">
                            <div class="flex items-center space-x-2">
                                <span class="font-medium" :class="{
                                    'text-purple-700': isGroupChat && message.user.id !== $page.props.auth.user.id,
                                    'text-emerald-700': !isGroupChat && message.user.id !== $page.props.auth.user.id,
                                    'text-gray-700': message.user.id === $page.props.auth.user.id
                                }">{{ message.user.name }}</span>
                                <span class="text-sm text-gray-500">{{ formatTimestamp(message.created_at) }}</span>
                            </div>

                            <div class="inline-block max-w-[85%] rounded-2xl px-4 py-2 shadow-sm" :class="{
                                'bg-blue-600 text-white': message.user.id === $page.props.auth.user.id,
                                'bg-white border border-gray-200': message.user.id !== $page.props.auth.user.id
                            }">
                                <p class="whitespace-pre-wrap">{{ message.text }}</p>
                            </div>

                            <div v-if="message.attachments" class="flex flex-wrap gap-4 mt-2">
                                <div 
                                    v-for="(attachment, i) in parseAttachments(message.attachments)" 
                                    :key="i"
                                    class="group"
                                >
                                    <!-- Image Attachments -->
                                    <div 
                                        v-if="isImage(attachment)" 
                                        class="relative cursor-pointer overflow-hidden rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200"
                                        @click="openImageModal(i, message.attachments)"
                                    >
                                        <img 
                                            :src="attachment" 
                                            class="w-32 h-32 object-cover rounded-lg transform transition-transform duration-200 group-hover:scale-105"
                                        />
                                    </div>

                                    <!-- Non-image Attachments -->
                                    <div 
                                        v-else 
                                        class="flex items-center space-x-3 p-3 bg-gray-50 border rounded-lg hover:bg-gray-100 transition-colors duration-200"
                                    >
                                        <img :src="fileIcon" class="w-8 h-8 object-cover" />
                                        <div class="flex-1 min-w-0">
                                            <a 
                                                :href="attachment" 
                                                target="_blank" 
                                                class="text-blue-600 hover:text-blue-800 font-medium truncate block"
                                            >
                                                {{ truncateFileName(attachment.split('/').pop()) }}
                                            </a>
                                            <p class="text-sm text-gray-500">Document</p>
                                        </div>
                                        <a 
                                            :href="attachment" 
                                            download 
                                            class="p-2 text-gray-500 hover:text-gray-700 rounded-full hover:bg-gray-200 transition-colors duration-200"
                                        >
                                            <img :src="downloadIcon" class="w-5 h-5" />
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 p-4 bg-white">
                    <form @submit.prevent="sendMessage" class="space-y-4">
                        <div class="flex items-end space-x-4">
                            <div class="flex-1">
                                <textarea 
                                    v-model="text" 
                                    placeholder="Rakstīt ziņu..." 
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 resize-none transition-shadow duration-200 min-h-[80px]"
                                    @keypress.enter.prevent="sendMessage"
                                ></textarea>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <label 
                                    for="file-upload" 
                                    class="p-2 text-gray-500 hover:text-gray-700 rounded-full hover:bg-gray-100 cursor-pointer transition-colors duration-200"
                                >
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                    </svg>
                                </label>
                                <input id="file-upload" type="file" @change="handleFileUpload" multiple class="hidden" ref="fileInput" />

                                <button 
                                    type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                                >
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                    </svg>
                                    Sūtīt
                                </button>
                            </div>
                        </div>

                        <!-- File Preview Section -->
                        <div v-if="attachments.length" class="flex flex-wrap gap-4">
                            <div 
                                v-for="(file, index) in attachments" 
                                :key="index" 
                                class="relative group"
                            >
                                <button
                                    @click="removeAttachment(index)"
                                    class="absolute -top-2 -right-2 z-10 w-6 h-6 bg-white rounded-full shadow-md flex items-center justify-center text-gray-500 hover:text-gray-700 transition-colors duration-200"
                                >
                                    ✕
                                </button>

                                <div class="w-20 h-20 rounded-lg overflow-hidden border border-gray-200 shadow-sm">
                                    <img 
                                        v-if="isImage(file)" 
                                        :src="file.preview" 
                                        class="w-full h-full object-cover"
                                    />
                                    <div v-else class="w-full h-full bg-gray-50 flex items-center justify-center p-2">
                                        <img :src="fileIcon" class="w-full h-full object-contain" />
                                    </div>
                                </div>
                                
                                <div 
                                    class="absolute inset-x-0 -top-8 opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                                >
                                    <div class="bg-gray-900 text-white text-xs py-1 px-2 rounded text-center">
                                        {{ truncateFileName(file.name) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Participants Modal -->
            <div 
                v-if="participantsVisible" 
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            >
                <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-lg">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-semibold text-gray-900">Dalībnieki</h2>
                        <button 
                            @click="closeParticipants" 
                            class="p-2 text-gray-500 hover:text-gray-700 rounded-full hover:bg-gray-100 transition-colors duration-200"
                        >
                            ✕
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div 
                            v-for="participant in conversation.participants" 
                            :key="participant.id"
                            class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors duration-200"
                        >
                            <div class="flex items-center space-x-4">
                                <img 
                                    :src="getProfilePicture(participant.profilePicturePath)" 
                                    alt="Participant Avatar" 
                                    class="w-10 h-10 rounded-full object-cover ring-2 ring-white shadow-sm"
                                />
                                <span class="font-medium text-gray-900">{{ participant.name }}</span>
                            </div>
                            
                            <button 
                                v-if="isGroupChat && conversation.participants.length > 2 && participant.id !== $page.props.auth.user.id"
                                @click="removeParticipant(participant.id)"
                                class="text-red-600 hover:text-red-700 text-sm font-medium transition-colors duration-200"
                            >
                                Remove
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Image Modal -->
            <div 
                v-if="expandedImages.length" 
                class="fixed inset-0 z-50 bg-black bg-opacity-90 flex items-center justify-center"
            >
                <div class="absolute top-4 left-4 right-4 flex justify-between items-center text-white">
                    <span class="text-lg">
                        {{ expandedImages[expandedImageIndex].split('/').pop() }}
                    </span>
                    <div class="flex items-center space-x-4">
                        <a 
                            :href="`/storage/${expandedImages[expandedImageIndex]}`"
                            download
                            class="p-2 rounded-lg border border-white/30 hover:bg-white/10 transition-colors duration-200"
                        >
                            <img :src="downloadIcon" class="w-6 h-6 filter invert" />
                        </a>
                        <button 
                            @click="closeImageModal"
                            class="p-2 rounded-lg border border-white/30 hover:bg-white/10 transition-colors duration-200"
                        >
                            <img :src="closeIcon" class="w-6 h-6 filter invert" />
                        </button
                    </button>
                    </div>
                </div>

                <div class="relative max-w-7xl mx-auto px-4">
                    <button 
                        v-if="expandedImageIndex > 0"
                        @click="prevImage" 
                        class="absolute left-4 top-1/2 -translate-y-1/2 p-2 rounded-full bg-black/50 text-white hover:bg-black/70 transition-colors duration-200"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>

                    <img 
                        :src="expandedImages[expandedImageIndex]" 
                        class="max-w-full max-h-[80vh] mx-auto object-contain rounded-lg"
                    />

                    <button 
                        v-if="expandedImageIndex < expandedImages.length - 1"
                        @click="nextImage" 
                        class="absolute right-4 top-1/2 -translate-y-1/2 p-2 rounded-full bg-black/50 text-white hover:bg-black/70 transition-colors duration-200"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>

                <div class="absolute bottom-4 left-1/2 -translate-x-1/2">
                    <div class="flex items-center space-x-2 bg-black/50 p-2 rounded-lg">
                        <div 
                            v-for="(img, index) in expandedImages" 
                            :key="index"
                            @click="expandedImageIndex = index"
                            class="relative cursor-pointer group"
                        >
                            <img 
                                :src="img" 
                                class="w-16 h-16 object-cover rounded-md transition-transform duration-200 group-hover:scale-105"
                                :class="{'ring-2 ring-blue-500': expandedImageIndex === index}"
                            />
                            <div 
                                class="absolute inset-0 bg-black/30 rounded-md"
                                :class="{'bg-transparent': expandedImageIndex === index}"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { format } from 'date-fns';
import fileIcon from '@/assets/file-icon.png'; 
import downloadIcon from '@/assets/download-icon.png'; 
import closeIcon from '@/assets/close-icon.png';
import Echo from "laravel-echo";
import Pusher from "pusher-js";

export default {
    props: {
        conversation: Object,
    },
    data() {
        return {
            text: '',
            attachments: [],
            expandedImages: [],
            expandedImageIndex: 0,
            fileIcon: fileIcon,  
            downloadIcon: downloadIcon,  
            closeIcon: closeIcon,
            participantsVisible: false,
        };
    },
    methods: {
        goBack() {
            this.$inertia.visit(route('conversations.index'));
        },
        deleteConversation() {
            if (confirm("Are you sure you want to delete this conversation?")) {
                this.$inertia.delete(route('conversations.destroy', this.conversation.id));
            }
        },
        getProfilePicture(profilePicturePath) {
            return profilePicturePath ? `/storage/${profilePicturePath}` : 'https://via.placeholder.com/40';
        },
        handleFileUpload(event) {
            const files = event.target.files;
            for (let file of files) {
                this.attachments.push({
                    file,
                    preview: URL.createObjectURL(file),
                    name: file.name,
                });
            }
            this.$refs.fileInput.value = ''; 
        },
        removeAttachment(index) {
            this.attachments.splice(index, 1);
        },
        listenForMessages() {
            if (!window.Echo) {
                console.error('Echo is not defined. Check the Echo setup.');
                return;
            }

            window.Echo.channel(`conversation.${this.conversation.id}`)
                .listen("MessageSent", (event) => {
                    this.conversation.messages.push(event.message);
                    this.scrollToBottom();
                });
        },
        sendMessage() {
            const formData = new FormData();
            formData.append('text', this.text);

            this.attachments.forEach((attachment) => {
                formData.append('attachments[]', attachment.file);
            });

            this.$inertia.post(route('messages.store', this.conversation.id), formData, {
                onSuccess: () => {
                    this.text = '';
                    this.attachments = [];
                    this.scrollToBottom();
                },
            });
        },
        parseAttachments(attachments) {
            if (typeof attachments === 'string') {
                try {
                    attachments = JSON.parse(attachments);
                } catch (e) {
                    console.error("Failed to parse attachments:", e);
                    attachments = [];
                }
            }
            return attachments.map(attachment => {
                return attachment.startsWith('/storage') ? attachment : `/storage/${attachment}`;
            });
        },
        removeParticipant(userId) {
            this.$inertia.post(route('conversations.remove-participant', this.conversation.id), { user_id: userId }, {
                onSuccess: () => {
                    this.conversation.participants = this.conversation.participants.filter(p => p.id !== userId);
                    this.participantsVisible = false;
                },
                onError: () => {
                    console.error("Failed to remove participant.");
                },
            });
        },

        scrollToBottom() {
            const chatContainer = this.$refs.chatContainer;
            if (chatContainer) {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
        },
        showParticipants() {
            this.participantsVisible = true;
        },
        closeParticipants() {
            this.participantsVisible = false;
        },
        openImageModal(index, attachments) {
            const parsedAttachments = this.parseAttachments(attachments);
            this.expandedImages = parsedAttachments.filter(this.isImage);
            this.expandedImageIndex = index;
        },
        closeImageModal() {
            this.expandedImages = [];
            this.expandedImageIndex = 0;
        },
        isImage(file) {
            const imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            const extension = typeof file === 'string'
                ? file.split('.').pop().toLowerCase()
                : file.name.split('.').pop().toLowerCase();
            return imageExtensions.includes(extension);
        },

        formatTimestamp(timestamp) {
            return format(new Date(timestamp), 'PPpp');
        },
        truncateFileName(fileName) {
            return fileName.length > 30 ? `${fileName.substring(0, 27)}...` : fileName;
        }
    },
    computed: {
        isGroupChat() {
            return this.conversation.initialParticipantCount > 2;
        },
        getOtherParticipantName() {
            const otherParticipants = this.conversation.participants.filter(p => p.id !== this.$page.props.auth.user.id);
            return otherParticipants[0]?.name || 'Unknown Participant';
        }
    },
    mounted() {
        this.listenForMessages();
        this.scrollToBottom();
    }
};
</script>

<style>
.animate-fade-in {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
