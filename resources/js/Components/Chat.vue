<script setup>
import TimesIcon from '@/Components/TimesIcon.vue';
import MinimizeIcon from '@/Components/MinimizeIcon.vue';
import MaximizeIcon from '@/Components/MaximizeIcon.vue';
import TextInput from '@/Components/TextInput.vue';
import { onMounted, ref } from 'vue';
import ChatButton from './ChatButton.vue';

defineEmits(['close']);

const props = defineProps({
    chat: {
        type: Object
    }
});

const collapsed = ref(false);
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
            v-show="!collapsed" 
            class="font-semibold text-sm text-right px-4"
        >
            Hola: {{ chat.user.name }}
        </div>

        <!-- Messages -->
        <div 
            v-show="!collapsed"
            class="h-[300px] py-1 px-4 overflow-y-auto"
        >
            <div>
                <div class="text-xs font-semibold">
                    Jafet:
                </div>
                <div class="text-xs border rounded-lg p-1 bg-teal-100 w-[80%]">
                    Lorem ipsum es el texto que se usa habitualmente en diseño gráfico en demostraciones de tipografías o de borradores de diseño para probar el diseño visual antes de insertar el texto final
                </div>
            </div>
            <div>
                <div class="text-xs font-semibold text-right">
                    Jafet:
                </div>
                <div class="text-xs border rounded-lg p-1 bg-cyan-100 w-[80%] ml-auto">
                    Lorem ipsum es el texto que se usa habitualmente en diseño gráfico en demostraciones de tipografías o de borradores de diseño para probar el diseño visual antes de insertar el texto final
                </div>
            </div>
            <div>
                <div class="text-xs font-semibold">
                    Jafet:
                </div>
                <div class="text-xs border rounded-lg p-1 bg-teal-100 w-[80%]">
                    Lorem ipsum es el texto que se usa habitualmente en diseño gráfico en demostraciones de tipografías o de borradores de diseño para probar el diseño visual antes de insertar el texto final
                </div>
            </div>
            <div>
                <div class="text-xs font-semibold text-right">
                    Jafet:
                </div>
                <div class="text-xs border rounded-lg p-1 bg-cyan-100 w-[80%] ml-auto">
                    Lorem ipsum es el texto que se usa habitualmente en diseño gráfico en demostraciones de tipografías o de borradores de diseño para probar el diseño visual antes de insertar el texto final
                </div>
            </div>
        </div>
        
        <!-- Input -->
        <div
            v-show="!collapsed"
            class="p-1 relative"
        >
            <TextInput type="text" class="w-full" placeholder="Hola..."></TextInput>
        </div>
    </div>
</template>