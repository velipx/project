<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    username: {
        type: String,
        required: true,
    },
    avatar: {
        type: String,
        required: false,
    },
    size: {
        type: Number,
        default: 6
    },
    border: {
        type: Boolean,
        default: false,
    },
    rounded: {
        type: Boolean,
        default: true,
    }
});

const BASE_BORDER_CLASSES = 'block max-w-full bg-gray-100 dark:bg-slate-800 border-2';

const avatarSource = computed(() => props.avatar || `https://api.dicebear.com/7.x/avataaars/svg?seed=${props.username}`);

const styleClasses = computed(() => ({
    [`w-${props.size}`]: true,
    [`h-${props.size}`]: true,
    'rounded-full': props.rounded,
    [BASE_BORDER_CLASSES]: props.border,
}));

</script>
<template>
    <div>
        <img :src="avatarSource"
             :alt="props.username"
             :class="styleClasses"/>
    </div>
</template>
