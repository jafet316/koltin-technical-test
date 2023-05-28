<script setup>
import { computed } from 'vue';

const props = defineProps({
    message: {
        type: Object
    },
    sameSender: {
        type: Boolean,
        default: false
    }
});

const ownerMessage = computed(() => props.message.user.id == props.message.chat.user.id);
</script>

<template>
    <div class="mb-1">
        <div 
            v-if="!sameSender"
            class="text-xs font-semibold"
            :class="{ 'text-right': ownerMessage }"
        >
            <span v-if="ownerMessage">
                {{ message.chat.user.name }}
            </span>
            <span v-else>
                {{ message.chat.post.user.name }}
            </span>
        </div>
        <div 
            v-if="message.message"
            class="text-xs border rounded-lg p-1 bg-teal-100 w-[80%]"
            :class="{ 'bg-teal-100': !ownerMessage, 'bg-cyan-100 ml-auto': ownerMessage }"
        >
            {{ message.message }}
        </div>
    </div>
</template>