<template>
    <div @mouseenter="showPopover" @mouseleave="hidePopover" ref="avatarRef" class="relative inline-block">
        <slot></slot> <!-- Dinamički sadržaj se ubacuje ovde -->
        <Popover v-if="isPopoverOpen" class="fixed z-10 transition-transform duration-300 popover-content" :style="popoverStyle">
            <div class="flex items-center p-4 bg-gray-800 text-white rounded shadow-md relative">
                <div class="flex-shrink-0">
                    <img v-if="avatar" :src="avatar" :alt="username" class="rounded-full w-12 h-12" />
                    <div v-else class="flex items-center justify-center w-12 h-12 rounded-full bg-gray-600 text-white font-bold">
                        {{ username.charAt(0).toUpperCase() }}
                    </div>
                </div>
                <div class="ml-3">
                    <div v-if="showSkeleton || !dataLoaded" class="animate-pulse">
                        <div v-for="n in skeletonCount" :key="n" class="h-4 bg-gray-600 rounded mb-2" :style="{ width: `${skeletonWidth}px` }"></div>
                    </div>
                    <div v-else>
                        <slot name="content" :userData="userData"></slot> <!-- Dinamički sadržaj za popover -->
                    </div>
                </div>
                <div class="absolute w-3 h-3 bg-gray-800" :style="arrowStyle"></div>
            </div>
        </Popover>
    </div>
</template>

<script setup>
import { ref, nextTick } from 'vue';
import { Popover } from '@headlessui/vue';
import axios from 'axios';

const props = defineProps({
    username: {
        type: String,
        required: true,
    },
    avatar: {
        type: String,
        default: null,
    },
    skeletonCount: {
        type: Number,
        default: 4,
    },
});

const isPopoverOpen = ref(false);
const userData = ref({});
const showSkeleton = ref(false);
const dataLoaded = ref(false);
const avatarRef = ref(null);
const popoverStyle = ref({});
const arrowStyle = ref({});
const skeletonWidth = 150; // Postavi osnovnu širinu skeletona

const showPopover = async () => {
    isPopoverOpen.value = true;
    await nextTick(); // Čekaj da se DOM ažurira
    showSkeleton.value = true; // Prikaži skeleton
    await loadUser(); // Učitaj podatke
    await nextTick(); // Sačekaj da se DOM ažurira
    calculatePopoverPosition(); // Izračunaj poziciju
};

const hidePopover = () => {
    isPopoverOpen.value = false;
};

const loadUser = async () => {
    try {
        const response = await axios.get(`/api/user/${props.username}`);
        userData.value = response.data;
        dataLoaded.value = true;
    } catch (error) {
        console.error(error);
    } finally {
        showSkeleton.value = false; // Ukloni skeleton
    }
};

const calculatePopoverPosition = () => {
    // Implementacija pozicije popovera kao u prethodnom kodu
};
</script>

<style scoped>
.popover-enter-active {
    transition: transform 0.3s ease, opacity 0.3s ease;
}
.popover-leave-active {
    transition: transform 0.3s ease, opacity 0.3s ease;
}
</style>
