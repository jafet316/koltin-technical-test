<script setup>
defineProps({
    width: {
        type: Number,
        default: 20
    },
    height: {
        type: Number,
        default: 20
    },
});
</script>

<template>
    <svg class="inline" :width="width" :height="height" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M16 8L8 16M8 8L16 16" stroke="#000000" stroke-width="2" stroke-linecap="round"/>
    </svg>
</template> 