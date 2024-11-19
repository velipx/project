<script setup>
import NavBarItem from '@/Components/Admin/NavBarItem.vue';

const props = defineProps({
    menu: {
        type: Array,
        default: () => [],
    },
    notifications: {
        type: Array,
        default: () => [],
    },
    loadingNotifications: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['menu-click']);

const menuClick = (event, item) => {
    emit('menu-click', event, item);
};
</script>

<template>
    <ul>
        <NavBarItem
            v-for="(item, index) in menu"
            :key="index"
            :item="item"
            :notifications="notifications"
            :loading-notifications="loadingNotifications"
            @menu-click="menuClick"
        />
    </ul>
</template>
