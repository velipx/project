<script setup>
import { ref, nextTick, computed } from 'vue';
import { Popover } from '@headlessui/vue';
import RoleBadge from '@/Components/Admin/RoleBadge.vue';
import axios from 'axios';
import { router } from "@inertiajs/vue3";
import {formatDate} from "@/utils/utils.js";

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
    popover: {
        type: Boolean,
        default: true,
    }
});

const isPopoverOpen = ref(false);
const userData = ref(null);
const showSkeleton = ref(false);
const avatarRef = ref(null);
const popoverStyle = ref({});
const arrowStyle = ref({});
const popoverWidth = ref(null);
const popoverHeight = ref(null);
let popoverTimer = null;

/**
 * Fetch the user data from the server
 */
const fetchUserData = async () => {
    try {
        await new Promise(resolve => setTimeout(resolve, 500)); // Simulate API delay
        const response = await axios.get(`/api/user/${props.username}`);
        userData.value = response.data;
    } catch (error) {
        console.error(error);
    } finally {
        showSkeleton.value = false;
        await nextTick();
    }
};

/**
 * Show a loading skeleton in the popover
 */
const showSkeletonLoader = async () => {
    showSkeleton.value = true;
    await fetchUserData();
    await nextTick();
    calculatePopoverPosition();
};

/**
 * Start the popover timer and show the popover after a delay
 */
const startPopoverTimer = async () => {
    popoverTimer = setTimeout(async () => {
        isPopoverOpen.value = true;
        await nextTick();
        if (!popoverWidth.value || !popoverHeight.value) {
            calculatePopoverPosition();
        } else {
            popoverStyle.value = {
                ...popoverStyle.value,
                width: `${popoverWidth.value}px`,
            };
        }
        await showSkeletonLoader(); // Load user data and show skeleton
    }, 500); // Show popover after 500 ms delay
};

/**
 * Cancel the popover timer and hide the popover
 */
const cancelPopoverTimer = () => {
    clearTimeout(popoverTimer); // Clear the timer
    isPopoverOpen.value = false; // Hide the popover
};

/**
 * Calculate the position of the popover based on the avatar element
 */
const calculatePopoverPosition = () => {
    const avatarElement = avatarRef.value.getBoundingClientRect();
    const popoverElement = document.querySelector('.popover-content');
    if (popoverElement) {
        popoverWidth.value = popoverElement.getBoundingClientRect().width;
        popoverHeight.value = popoverElement.getBoundingClientRect().height;
    } else {
        popoverWidth.value = 230; // default width
        popoverHeight.value = 228.5; // default height
    }

    let leftPosition = avatarElement.left + (avatarElement.width / 2) - (popoverWidth.value / 2);
    if (leftPosition < 10) leftPosition = 10;
    if (leftPosition + popoverWidth.value > window.innerWidth) {
        leftPosition = window.innerWidth - popoverWidth.value - 20;
    }

    let topPosition = avatarElement.top - popoverHeight.value - 5; // above avatar
    if (topPosition < 0) {
        topPosition = avatarElement.bottom + 10; // below avatar
        arrowStyle.value = {
            left: `${(avatarElement.left + (avatarElement.width / 2)) - leftPosition - 5}px`,
            bottom: '100%',
            transform: 'rotate(45deg)',
        };
    } else {
        arrowStyle.value = {
            left: `${(avatarElement.left + (avatarElement.width / 2)) - leftPosition - 5}px`,
            top: `${popoverHeight.value}px`,
            transform: 'rotate(45deg)',
        };
    }

    popoverStyle.value = {
        left: `${leftPosition}px`,
        top: `${topPosition}px`,
        maxWidth: '90vw',
    };
};

const avatarUrl = computed(() => props.avatar ? props.avatar : `https://api.dicebear.com/7.x/avataaars/svg?seed=${props.username}`);
</script>
<template>
    <div v-if="props.popover" @mouseenter="startPopoverTimer" @mouseleave="cancelPopoverTimer" ref="avatarRef" class="relative inline-block cursor-pointer" @click="router.visit(route('admin.users.show', props.username))">
        <img
            :src="avatarUrl"
            :alt="username"
            :class="`w-${props.size} h-${props.size}`"
            class="rounded-full block max-w-full bg-gray-100 dark:bg-slate-800 border-2"
        />
        <Popover v-if="isPopoverOpen" class="fixed z-10 min-w-[230px] transition-transform duration-300 popover-content" :style="popoverStyle">
            <div class="items-center p-4 bg-slate-800 text-white rounded-lg shadow-md relative border-2 border-slate-700 max-h-[228px]">
                <div class="shrink-0">
                    <img
                        :src="avatarUrl"
                        :alt="username"
                        class="rounded-full w-16 h-16 mx-auto mt-0 border-2 border-slate-700"
                    />
                </div>
                <div class="mt-2 text-center">
                    <div v-if="showSkeleton || !userData?.name" class="animate-pulse">
                        <div class="h-4 bg-gray-600 rounded-xs mb-2 w-20 mx-auto"></div>
                        <div class="h-4 bg-gray-600 rounded-xs mb-2 w-48 mx-auto"></div>
                        <div class="h-4 bg-gray-600 rounded-xs mb-2 w-32 mx-auto"></div>
                        <div class="h-4 bg-gray-600 rounded-xs mb-2 w-24 mx-auto"></div>
                        <div class="h-5 bg-gray-600 rounded-xs mb-2 w-20 mx-auto"></div>
                    </div>
                    <div v-else>
                        <h4 class="font-semibold">{{ userData?.name || 'N/A' }}</h4>
                        <p class="text-slate-300">{{ userData?.email || 'N/A' }}</p>
                        <p>Registered: {{ formatDate(userData?.created_at) || 'N/A' }}</p>
                        <p>Role: {{ userData?.roles?.map(role => role.name).join(', ') || 'N/A' }}</p>
                        <RoleBadge :role="userData?.roles?.map(role => role.name).join(', ')" small />
                    </div>
                </div>
                <div class="absolute w-3 h-3 bg-gray-800" :style="arrowStyle"></div>
            </div>
        </Popover>
    </div>
    <div v-else ref="avatarRef" class="relative inline-block">
        <img
            :src="avatarUrl"
            :alt="username"
            :class="`w-${props.size} h-${props.size}`"
            class="rounded-full block max-w-full bg-gray-100 dark:bg-slate-800 border-2"
        />
    </div>
</template>
<style scoped>
.popover-enter-active {
    transition: transform 0.3s ease, opacity 0.3s ease;
}
.popover-leave-active {
    transition: transform 0.3s ease, opacity 0.3s ease;
}
/* Stil za fade transition */
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.5s ease;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>
