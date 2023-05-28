<script setup>
import TimesIcon from '@/Components/TimesIcon.vue';
import MinimizeIcon from '@/Components/MinimizeIcon.vue';
import MaximizeIcon from '@/Components/MaximizeIcon.vue';
import ChatMessage from '@/Components/ChatMessage.vue';
import TextInput from '@/Components/TextInput.vue';
import { onMounted, ref } from 'vue';
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
const messages = ref([]);
const disableInput = ref(true);

const sendMessage = e => {
   if(e.key == 'Enter' && input.value.length) {
        disableInput.value = true;
        axios.post(route('messages', props.chat.id), {
            message: input.value
        })
        .then(response => {
            input.value = '';
        })
        .finally(() => {
            disableInput.value = false;
        });
   }
}

Echo.private(`chats.${props.chat.id}`)
    .listen('NewChatMessageEvent', (e) => {
        console.log(e.message);
        messages.value.push(e.message);
    });

onMounted(() => {
    axios.get(route('messages', props.chat.id))
    .then(response => {
        messages.value = response.data.data;
        inputRef.value.focus();
    })
    .finally(() => {
        disableInput.value = false;
    });
});
</script>
<template>
    <div class="border bg-white w-[350px] rounded-md" :class="{ 'w-[250px]': collapsed }">
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
                    <MinimizeIcon></MinimizeIcon>
                </ChatButton>
                <ChatButton 
                    v-show="collapsed"
                    @click="collapsed = false"
                >
                    <MaximizeIcon></MaximizeIcon>
                </ChatButton>
                <ChatButton @click="$emit('close')">
                    <TimesIcon></TimesIcon>
                </ChatButton>
            </div>
        </div>

        <div
            v-show="!collapsed && chat.user.id != chat.post.user.id" 
            class="font-semibold text-sm text-right px-4"
        >
            Hola: {{ chat.user.name }}
        </div>

        <!-- Messages -->
        <div 
            v-show="!collapsed"
            class="h-[300px] py-1 px-4 overflow-y-auto"
        >
            <ChatMessage
                v-for="(message, i) in messages"
                :message="message"
                :same-sender="message.user.id == (messages[i-1] ? messages[i-1].user.id : 0)"
            />
        </div>
        
        <!-- Input -->
        <div
            v-show="!collapsed"
            class="p-1 relative"
        >
            <TextInput 
                v-model="input"
                ref="inputRef"
                type="text" 
                class="w-full text-sm" 
                placeholder="Hola..."
                :disabled="disableInput"
                @keydown="sendMessage"
            />
        </div>
    </div>
</template>