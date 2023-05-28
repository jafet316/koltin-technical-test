<script setup>
import { usePage } from '@inertiajs/vue3';
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

const ownerMessage = computed(() => props.message.user.id == usePage().props.auth.user.id);
</script>

<template>
    <div class="mb-1">
        <div 
            v-if="!sameSender"
            class="text-xs font-semibold"
            :class="{ 'text-right': ownerMessage }"
        >
            <span>
                {{ message.user.name }}
            </span>
        </div>
        <div 
            v-if="message.message"
            class="text-xs border rounded-lg p-1 w-[80%]"
            :class="{ 'bg-teal-100': !ownerMessage, 'bg-cyan-100 ml-auto': ownerMessage }"
        >
            {{ message.message }}
        </div>
    </div>
</template>