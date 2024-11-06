<template>
    <AuthenticatedLayout>
        <div class="max-w-5xl mx-auto py-10">
            <button 
                @click="goBack"
                class="mb-4 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition duration-300"
            >
                Atpakaļ
            </button>

            <button 
                @click="deleteConversation"
                class="ml-4 mb-4 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition duration-300"
            >
                Dzēst saraksti
            </button>

            <button 
                @click="showParticipants"
                class="ml-4 mb-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-300"
            >
                Skatīt dalībnieku
            </button>

            <h1 class="text-3xl font-bold mb-6">
                {{ isGroupChat ? conversation.subject || 'Conversation' : getOtherParticipantName }}
                <span v-if="isGroupChat" class="group-icon">👥</span>
            </h1>

            <div ref="chatContainer" class="border border-gray-300 rounded-lg p-4 max-h-96 overflow-y-auto space-y-6">
                <div v-for="(message, index) in conversation.messages" :key="message.id" class="flex space-x-4 items-start">
                    <!-- Avatar -->
                    <img 
                        :src="getProfilePicture(message.user.profilePicturePath)" 
                        alt="User Avatar" 
                        class="w-10 h-10 rounded-full object-cover"
                    />

                    <div class="flex-1">
                        <!-- User Name and Timestamp -->
                        <div class="flex justify-between items-center">
                            <p class="font-semibold text-gray-800">{{ message.user.name }}</p>
                            <span class="text-sm text-gray-500">{{ formatTimestamp(message.created_at) }}</span>
                        </div>

                        <!-- Message Text -->
                        <p class="bg-gray-100 p-3 rounded-lg mt-1 text-gray-900 break-all max-w-full">
                            {{ message.text }}
                        </p>

                        <!-- Attachments -->
                        <div v-if="message.attachments" class="mt-2 space-y-2">
                            <div v-for="(attachment, i) in parseAttachments(message.attachments)" :key="i" class="flex items-center space-x-2">
                                <!-- Image Attachments -->
                                <div v-if="isImage(attachment)" class="cursor-pointer">
                                    <img 
                                        :src="`/storage/${attachment}`" 
                                        class="w-16 h-16 object-cover" 
                                        @click="openImageModal(i, message.attachments)"
                                    />
                                </div>
                                <!-- Non-image Attachments -->
                                <div v-else class="flex items-center space-x-2 border rounded p-3 bg-gray-50 hover:bg-gray-100">
                                    <img :src="fileIcon" class="w-8 h-8 object-cover" />
                                    <div class="flex-grow">
                                        <a 
                                            :href="`/storage/${attachment}`" 
                                            target="_blank" 
                                            class="text-blue-600 hover:underline break-all"
                                        >
                                            {{ truncateFileName(attachment.split('/').pop()) }}
                                        </a>
                                        <p class="text-sm text-gray-500">Document</p>
                                    </div>
                                    <a :href="`/storage/${attachment}`" download class="text-blue-500 hover:text-blue-700">
                                        <img :src="downloadIcon" class="w-5 h-5" />
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form @submit.prevent="sendMessage" class="mt-6 flex items-center space-x-2">
                <textarea 
                    v-model="text" 
                    placeholder="Type a message..." 
                    class="flex-grow border rounded-lg px-4 py-2 max-h-32 overflow-y-auto resize-y"
                    @keypress.enter.prevent="sendMessage"
                ></textarea>
                <label for="file-upload" class="cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-gray-600 hover:text-gray-800 transition duration-200">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 12V8a4 4 0 00-8 0v6a4 4 0 008 0V10m-4 2v6" />
                    </svg>
                </label>
                <input id="file-upload" type="file" @change="handleFileUpload" multiple class="hidden" ref="fileInput" />

                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-lg">
                    Send
                </button>
            </form>

            <!-- File previews under the input box -->
            <div v-if="attachments.length" class="flex space-x-4 my-4">
                <div v-for="(file, index) in attachments" :key="index" class="relative flex flex-col items-center group">
                    <span 
                        class="absolute -top-3 -right-3 bg-transparent text-gray-500 hover:text-gray-800 border border-white p-1 rounded-full cursor-pointer flex items-center justify-center w-6 h-6 transition duration-300"
                        @click="removeAttachment(index)"
                    >
                        ✖
                    </span>

                    <!-- Preview for Image Attachments -->
                    <img 
                        :src="file.preview" 
                        v-if="isImage(file)" 
                        class="w-16 h-16 object-cover" 
                    />

                    <div v-else class="relative">
                        <img :src="fileIcon" class="w-16 h-16 object-cover" />
                        <span 
                            class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-700 text-white text-sm py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition duration-300"
                            style="white-space: nowrap;"
                        >
                            {{ file.name }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Participants Modal -->
            <div v-if="participantsVisible" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 w-full max-w-lg">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold">Participants</h2>
                        <button @click="closeParticipants" class="text-gray-500 hover:text-gray-700 text-2xl">✖</button>
                    </div>
                    <ul class="space-y-2">
                        <li v-for="participant in conversation.participants" :key="participant.id" class="flex items-center space-x-4">
                            <img 
                                :src="getProfilePicture(participant.profilePicturePath)" 
                                alt="Participant Avatar" 
                                class="w-10 h-10 rounded-full object-cover"
                            />
                            <p class="text-gray-800">{{ participant.name }}</p>
                            
                            <!-- Show Remove button if this is a group chat, there are more than two participants, and the participant is not the current user -->
                            <button 
                                v-if="isGroupChat && conversation.participants.length > 2 && participant.id !== $page.props.auth.user.id" 
                                @click="removeParticipant(participant.id)" 
                                class="text-red-500 text-sm hover:text-red-700 ml-auto"
                            >
                                Remove
                            </button>
                            <span v-if="isGroupChat">[Debug: Group Chat Detected]</span>
                        </li>
                    </ul>
                </div>
            </div>


            <!-- Expanded Image Modal -->
            <div v-if="expandedImages.length" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75">
                <div class="absolute top-4 left-4 text-white text-lg">
                    {{ expandedImages[expandedImageIndex].split('/').pop() }}
                </div>

                <!-- Close and download buttons-->
                <div class="absolute top-4 right-4 flex space-x-4">
                    <a :href="`/storage/${expandedImages[expandedImageIndex]}`" download>
                        <div class="w-12 h-12 border border-white p-1 rounded-lg flex items-center justify-center">
                            <img :src="downloadIcon" alt="Download" class="w-full h-full filter invert" />
                        </div>
                    </a>
                    <button @click="closeImageModal" class="text-white text-2xl">
                        <div class="w-12 h-12 border border-white p-1 rounded-lg flex items-center justify-center bg-white">
                            <img :src="closeIcon" alt="Close" class="w-full h-full" />
                        </div>
                    </button>
                </div>

                <!-- Fullscreen image -->
                <div class="relative">
                    <img :src="`/storage/${expandedImages[expandedImageIndex]}`" class="max-w-screen-lg max-h-screen object-contain" />

                    <!-- Previous and Next arrows -->
                    <button @click="prevImage" v-if="expandedImageIndex > 0" class="absolute left-0 text-white text-3xl">
                        &#9664;
                    </button>
                    <button @click="nextImage" v-if="expandedImageIndex < expandedImages.length - 1" class="absolute right-0 text-white text-3xl">
                        &#9654;
                    </button>
                </div>

                <!-- Thumbnail preview at the bottom -->
                <div class="absolute bottom-4 flex space-x-2">
                    <div v-for="(img, index) in expandedImages" :key="index" @click="expandedImageIndex = index" class="cursor-pointer">
                        <img :src="`/storage/${img}`" class="w-16 h-16 object-cover border border-gray-300" />
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
            const parsedAttachments = Array.isArray(attachments) ? attachments : this.parseAttachments(attachments);
            this.expandedImages = parsedAttachments.filter(this.isImage);
            this.expandedImageIndex = index;
        },
        closeImageModal() {
            this.expandedImages = [];
            this.expandedImageIndex = 0;
        },
        isImage(file) {
            const imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            const extension = file.name
                ? file.name.split('.').pop().toLowerCase()
                : file.split('.').pop().toLowerCase();
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
            return otherParticipants[0]?.name || 'Unknown Participant'; // Safely handle the case where there is no other participant
        }
    },
    mounted() {
        this.listenForMessages();
        this.scrollToBottom();
    }
};
</script>
