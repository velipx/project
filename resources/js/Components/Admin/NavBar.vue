<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { containerMaxW } from '@/config.js';
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue';
import BaseIcon from '@/Components/Admin/BaseIcon.vue';
import { mdiChevronDown, mdiChevronUp } from '@mdi/js';
import { usePage } from '@inertiajs/vue3';
import UserProfile from "@/Components/Admin/UserProfile.vue";
import Notifications from "@/Components/Admin/Notifications.vue";

const props = defineProps({
    menu: {
        type: Array,
        required: true
    }
});

const emit = defineEmits(['menu-click']);

const { user } = usePage().props.auth;
</script>

<template>
    <nav class="fixed top-0 inset-x-0 h-14 z-30 bg-s2 dark:bg-s2 w-full transition-position lg:w-auto">
        <div class="flex justify-between lg:items-stretch pr-6" :class="containerMaxW">
            <div class="flex flex-1 items-stretch h-14">
                <slot/>
            </div>
            <div class="flex items-center space-x-4">
                <!-- Menu for User Profile -->
                <Menu as="div" class="relative inline-block text-left">
                    <MenuButton
                        class="flex justify-center rounded-full focus:outline-hidden focus-visible:ring-2 focus-visible:ring-white/75">
                        <UserProfile :user="user"/>
                        <BaseIcon :path="menu[0].expanded ? mdiChevronUp : mdiChevronDown" size="20"
                                  class="ml-2 text-violet-400"/>
                    </MenuButton>
                    <transition
                        enter-active-class="transition duration-100 ease-out"
                        enter-from-class="transform scale-95 opacity-0"
                        enter-to-class="transform scale-100 opacity-100"
                        leave-active-class="transition duration-75 ease-in"
                        leave-from-class="transform scale-100 opacity-100"
                        leave-to-class="transform scale-95 opacity-0">
                        <MenuItems
                            class="absolute right-0 mt-2 w-56 origin-top-right divide-y divide-gray-100 dark:divide-gray-600 rounded-md bg-white dark:bg-s1 shadow-lg ring-1 ring-black/5 focus:outline-hidden">
                            <div v-for="item in menu[0].menu" :key="item.label" class="px-1 py-1">
                                <MenuItem v-slot="{ active }">
                                    <a
                                        :href="item.href ? item.href : '#'"
                                        @click="item.isLogout ? emit('menu-click', { isLogout: true }) : null"
                                        :class="[
                            active ? 'bg-violet-500 text-white' : 'text-gray-900 dark:text-gray-200',
                            'group flex w-full items-center rounded-md px-2 py-2 text-sm'
                          ]">
                                        <BaseIcon :path="item.icon" size="20" class="mr-2 h-5 w-5 text-violet-400"/>
                                        {{ item.label }}
                                    </a>
                                </MenuItem>
                            </div>
                        </MenuItems>
                    </transition>
                </Menu>
                <!-- Notification Bell -->
                <Notifications />
            </div>
        </div>
    </nav>
</template>
