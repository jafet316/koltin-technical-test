<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import DownloadFileIcon from './DownloadFileIcon.vue';

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

const getAttachmentSize = () => {
    const bytes = props.message.attachment_size;

    if (bytes < 1024) {
        return bytes + " bytes";
    } 
    else if (bytes < 1048576) {
        const kilobytes = (bytes / 1024).toFixed(2);
        return kilobytes + " KB";
    } 
    else {
        const megabytes = (bytes / 1048576).toFixed(2);
        return megabytes + " MB";
    }
}
</script>

<template>
    <div class="mb-1 relative">
        <!-- Sender -->
        <div 
            v-if="!sameSender"
            class="text-xs font-semibold"
            :class="{ 'text-right': ownerMessage }"
        >
            <span>
                {{ message.user.name }}
            </span>
        </div>

        <!-- Text message -->
        <div 
            class="flex w-full"
            :class="{ 'justify-end': ownerMessage }"
        >
            <div 
                class="text-xs border rounded-lg py-1 px-2 max-w-[80%]"
                :class="{ 'bg-teal-100': !ownerMessage, 'bg-cyan-100': ownerMessage }"
            >
                <!-- Attachment -->
                <div v-if="message.attachment">
                    <a 
                        :href="route('messages.downloadAttachment', message.id)"
                        class="block text-xs rounded-md p-1 w-full text-left"
                        :class="{ 'right-[-4px] bg-cyan-200': ownerMessage, 'left-[-4px] bg-teal-200': !ownerMessage }"
                    >
                        <div class="grid grid-cols-[1rem_1fr] gap-1">
                            <DownloadFileIcon />
                            <div class="text-gray-500 ml-1">
                                <div class="mb-1">
                                    {{ message.attachment_name }}
                                </div>
                                <div>
                                    {{ getAttachmentSize() }} · <span class="uppercase">{{ message.attachment_extension }}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div v-if="message.message" class="mt-1">
                    {{ message.message }}
                </div>
                <div class="text-gray-500 text-right" style="font-size: 0.6rem">
                    {{ message.time }}
                </div>
            </div>
        </div>
    </div>
</template>