<script setup>
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';


const props = defineProps({
    chat: {
        type: Object
    }
});

const messages = ref([]);

const ownerChat = computed(() => props.chat.user.id == usePage().props.auth.user.id);

onMounted(() => {
    axios.get(route('messages', props.chat.id), { params: { single: true } })
    .then(response => {
        messages.value = response.data.data;
    });
});
</script>

<template>
    <div class="grid grid-cols-[50px_1fr] gap-2 px-2 py-1 hover:bg-gray-100 text-sm select-none	">
        <!-- Chat img -->
        <div class="w-full">
            <img :src="chat.post.image" :alt="chat.post.title">
        </div>

        <!-- Chat info -->
        <div>
            <div v-if="!ownerChat">
                <span class="text-gray-700 font-semibold">{{ chat.user.name }}</span> 
                on 
                <span class="text-gray-700 font-semibold">{{ chat.post.title }}</span>
            </div>
            <div v-else>
                <span class="text-gray-700 font-semibold">{{ chat.post.title }}</span>
            </div>
            <div class=" min-h-[20px] mt-1 px-2">
                <div 
                    v-if="messages.length"
                    class="text-xs"
                >
                    <div>
                        <span v-if="messages[0].user.id == $page.props.auth.user.id">
                            You
                        </span>
                        <span v-else>
                            {{ messages[0].user.name }}
                        </span>
                    </div>
                    <div class="text-gray-500 truncate">
                        {{ messages[0].message }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>