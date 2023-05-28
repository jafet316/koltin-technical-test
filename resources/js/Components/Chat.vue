<script setup>
import TimesIcon from '@/Components/TimesIcon.vue';
import MinimizeIcon from '@/Components/MinimizeIcon.vue';
import MaximizeIcon from '@/Components/MaximizeIcon.vue';
import AttachmentIcon from '@/Components/AttachmentIcon.vue';
import ChatMessage from '@/Components/ChatMessage.vue';
import TextInput from '@/Components/TextInput.vue';
import { computed, onMounted, ref } from 'vue';
import ChatButton from './ChatButton.vue';
import axios from 'axios';

defineEmits(['close']);

const props = defineProps({
    chat: {
        type: Object
    }
});

const collapsed = ref(false);
const input = ref('');
const inputRef = ref();
const attachmentInputRef = ref();
const messagesBoxRef = ref();
const messages = ref([]);
const sendingMessage = ref(false);
const attachment = ref({});
const paginateMetaData = ref({});
const lastPageLoaded = ref(0);

const thereIsAttachment = computed(() => attachment.value instanceof File);

const handleNewAttachment = () => {
    removeAttachment();

    if(attachmentInputRef.value.files.length) {
        attachment.value = attachmentInputRef.value.files[0];
    }

    inputRef.value.focus();
}

const removeAttachment = () => {
    attachment.value = {};
}

const sendMessage = e => {
    // Prevent input tab if ther is a message sending
    if(sendingMessage.value) {
        return;
    }

    if(e.key == 'Enter') {
        const message = input.value;

        // Clear input
        input.value = '';

        // Data to send
        const data = new FormData();
        data.append('message', message);

        if(thereIsAttachment.value) {
            data.append('attachment', attachment.value);
        }
       
        sendingMessage.value = true;
        axios.post(route('messages', props.chat.id), data, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })
        .then(response => {
            // TODO: Optimize new message push
            //messages.value.push(response.data.data);
            removeAttachment();
        })
        .catch(() => {
            input.value = message;
        })
        .finally(() => {
            sendingMessage.value = false;
            inputRef.value.focus();
        });
    }
}

const getMessages = (scrollHeight = null) => {
    // if already loaded the maximum of pages esc from function
    if(paginateMetaData.value.last_page && paginateMetaData.value.last_page == lastPageLoaded.value) {
        return
    }

    lastPageLoaded.value++;

    return axios.get(route('messages', props.chat.id),  { params: { page: lastPageLoaded.value } })
    .then(response => {
        messages.value = [...response.data.data.reverse(), ...messages.value];
        paginateMetaData.value = response.data.meta;

        if(scrollHeight) {
            // Force scroll to the last calculated position before load new data
            messagesBoxRef.value.scroll(0, -(scrollHeight / (response.data.meta.per_page - 1) * response.data.data.length));
        }
    })
    .catch(() => {
        lastPageLoaded.value--;
    });
}

const handleScroll = e => {
    // Load more data on top off scroll
    if((e.srcElement.offsetHeight - e.srcElement.scrollTop) >= e.srcElement.scrollHeight) {
        getMessages(e.srcElement.scrollHeight);
    }
}

Echo.private(`chats.${props.chat.id}`)
    .listen('NewChatMessageEvent', (e) => {
        messages.value.push(e.message);
        messages.value = [...new Set(messages.value)]
    });

onMounted(() => {
    getMessages();
    inputRef.value.focus();
});
</script>
<template>
    <div 
        class="border bg-white w-[350px] rounded-md shadow-md" 
        :class="{ 'w-[250px]': collapsed }"
    >
        <div class="grid grid-cols-[1fr_80px] items-center gap-1 px-1 py-2 border-b">
            <div 
                class="truncate"
            >
                {{ chat.post.title }}
            </div>
            <div class="text-right">
                <ChatButton 
                    v-show="!collapsed"
                    @click="collapsed = true"
                >
                    <MinimizeIcon />
                </ChatButton>
                <ChatButton 
                    v-show="collapsed"
                    @click="collapsed = false"
                >
                    <MaximizeIcon />
                </ChatButton>
                <ChatButton @click="$emit('close')">
                    <TimesIcon></TimesIcon>
                </ChatButton>
            </div>
        </div>

        <!-- Messages -->
        <div 
            v-show="!collapsed"
            ref="messagesBoxRef"
            class="h-[300px] py-1 px-4 overflow-y-auto flex flex-col-reverse"
            @scroll="handleScroll"
        >
            <div>
                <ChatMessage
                    v-for="(message, i) in messages"
                    :message="message"
                    :same-sender="message.user.id == (messages[i-1] ? messages[i-1].user.id : 0)"
                />
            </div>
        </div>
        
        <!-- Input -->
        <div
            v-show="!collapsed"
            class="relative p-1"
        >
            <div 
                v-show="thereIsAttachment"
                class="absolute top-3 left-4 flex justify-between items-center gap-1 max-w-[80%] text-gray-700 text-sm bg-gray-100 rounded-lg px-2"
            >
                <div class="truncate">{{ attachment.name }}</div>
                <button 
                    class="p-1 rounded-full leading-none hover:bg-gray-50"
                    @click="removeAttachment"
                >
                    <TimesIcon></TimesIcon>
                </button>
            </div>
            <div class="flex justify-between gap-1">
                <div>
                    <label 
                        v-show="!thereIsAttachment"
                        :for="`attachment-chat${chat.id}`"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm cursor-pointer hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                    >
                        <AttachmentIcon />
                    </label>
                    <input
                        :id="`attachment-chat${chat.id}`"
                        type="file"
                        class="hidden"
                        ref="attachmentInputRef"
                        @change="handleNewAttachment"
                    >
                </div>
                <TextInput 
                    v-model="input"
                    ref="inputRef"
                    type="text" 
                    class="w-full text-sm"
                    :class="{ 'pt-10': thereIsAttachment }" 
                    placeholder="Hola..."
                    @keydown="sendMessage"
                />
            </div>
        </div>
    </div>
</template>