<script setup>
import Chat from '@/Components/Chat.vue';
import axios from 'axios';
import { ref } from 'vue';

const chats = ref([]);

const newChat = post => {
    axios.post(route('chats', post.id))
    .then(response => {
        const chat = response.data.data;
        const chatIsRender = chats.value.find(cht => cht.id == chat.id);
        
        if(chatIsRender) {
            return;
        }

        chats.value.push(chat);
    });
}

const removeChat = chat => {
    const chatIndex = chats.value.indexOf(chat);
    chats.value.splice(chatIndex, 1);
}

defineExpose({ newChat });
</script>
<template>
    <div class="fixed bottom-0 right-0 z-10 flex items-end gap-2 max-w-full p-2">
        <Chat 
            v-for="chat in chats" 
            :chat="chat"
            @close="removeChat(chat)"
        />
    </div>
</template>