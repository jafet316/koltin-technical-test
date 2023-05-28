<script setup>
import Chat from '@/Components/Chat.vue';
import axios from 'axios';
import { ref } from 'vue';

const chats = ref([]);

const chatIsRender = chat => {

   return chats.value.find(cht => cht.id == chat.id) !== undefined;
}

const findOrNewChat = post => {
    axios.post(route('chats.findOrNew', post.id))
    .then(response => {
        const chat = response.data.data;
        
        // Verify if chat is render
        if(chatIsRender(chat)) {
            return;
        }

        chats.value.push(chat);
    });
}

const addChat = (chat = null, post = null) => {
    // If there is not chat try to find o create a new one
    if(!chat && post) {
        findOrNewChat(post);

        return;
    }
    
    // Verify if chat is render
    if(chatIsRender(chat)) {
        return;
    }

    chats.value.push(chat);
}

const removeChat = chat => {
    const chatIndex = chats.value.indexOf(chat);
    chats.value.splice(chatIndex, 1);
}

defineExpose({ addChat });
</script>
<template>
    <div class="fixed bottom-0 right-0 z-10 flex items-end gap-2 max-w-full py-2 px-4">
        <Chat 
            v-for="chat in chats" 
            :chat="chat"
            @close="removeChat(chat)"
        />
    </div>
</template>