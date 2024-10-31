<script setup>
import UserAvatar from "@/Components/Admin/UserAvatar.vue";
import {computed} from "vue";

const props = defineProps({
    user: {
        type: Object,
        required: true,
    }
});

const hasAdminRole = computed(() => {
    return props.user.roles && props.user.roles.some(role => role.name === 'admin');
});
</script>

<template>
    <div class="flex items-center space-x-2">
        <UserAvatar :username="user.username" :avatar="user.avatar_url" border />
        <span
            class="truncate max-w-xs"
            :title="user.name"
            :class="{ 'text-red-400': hasAdminRole }"
        >
            {{ user.name }}
        </span>
    </div>
</template>
