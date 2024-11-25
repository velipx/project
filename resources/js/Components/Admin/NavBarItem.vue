<script setup>
import { mdiBell } from '@mdi/js';
import { ref, computed } from 'vue';
import BaseIcon from '@/Components/Admin/BaseIcon.vue';
import UserAvatarCurrentUser from '@/Components/Admin/UserAvatarCurrentUser.vue';
import { usePage } from '@inertiajs/vue3';
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue';
import { Popover, PopoverButton, PopoverPanel } from '@headlessui/vue';
import menuNavBar from "@/routes/menuNavBar.js";

const props = defineProps({
    item: {
        type: Object,
        required: true
    },
    notifications: {
        type: Array,
        default: () => []
    },
    loadingNotifications: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['menu-click']);
const isDropdownActive = ref(false);
const userName = computed(() => usePage().props.auth.user.name);

const toggleDropdown = () => {
    isDropdownActive.value = !isDropdownActive.value;
};

const menuClick = (event) => {
    emit('menu-click', event, props.item);
};

const isNotificationBell = computed(() => props.item.isNotificationBell);
const isCurrentUser = computed(() => props.item.isCurrentUser);
const userProfileMenu = menuNavBar.find(item => item.isCurrentUser).menu;
</script>

<template>
    <div class="flex items-center space-x-4">
        <!-- User Profile Dropdown -->
        <Menu v-if="isCurrentUser" as="div" class="relative inline-block text-left">
            <div>
                <MenuButton
                    class="inline-flex justify-center rounded-md bg-black/20 px-4 py-2 text-sm font-medium text-white hover:bg-black/30 focus:outline-hidden focus-visible:ring-2 focus-visible:ring-white/75"
                >
                    <UserAvatarCurrentUser class="w-6 h-6 mr-3 inline-flex" />
                    <span class="ml-2">{{ userName }}</span>
                </MenuButton>
            </div>

            <transition
                enter-active-class="transition duration-100 ease-out"
                enter-from-class="transform scale-95 opacity-0"
                enter-to-class="transform scale-100 opacity-100"
                leave-active-class="transition duration-75 ease-in"
                leave-from-class="transform scale-100 opacity-100"
                leave-to-class="transform scale-95 opacity-0"
            >
                <MenuItems
                    class="absolute right-0 mt-2 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-hidden"
                >
                    <div class="px-1 py-1">
                        <MenuItem v-for="option in userProfileMenu" :key="option.label" v-slot="{ active }">
                            <a
                                v-if="option.href"
                                :href="option.href"
                                :class="['group flex items-center rounded-md px-2 py-2 text-sm', active ? 'bg-violet-500 text-white' : 'text-gray-900']"
                            >
                                <BaseIcon :path="option.icon" class="w-5 h-5 mr-2" />
                                {{ option.label }}
                            </a>
                            <span v-else-if="option.isDivider" class="py-1">
                                <hr class="border-t border-gray-200" />
                            </span>
                        </MenuItem>
                    </div>
                </MenuItems>
            </transition>
        </Menu>

        <!-- Notification Bell -->
        <div v-if="isNotificationBell">
            <Popover v-slot="{ open }" class="relative">
                <PopoverButton
                    :class="open ? 'text-white' : 'text-white/90'"
                    class="group inline-flex items-center rounded-md px-3 py-2 text-base font-medium text-white focus:outline-hidden focus-visible:ring-2 focus-visible:ring-white/75"
                >
                    <BaseIcon :path="mdiBell" />
                    <ChevronDownIcon
                        :class="open ? 'text-orange-300' : 'text-orange-300/70'"
                        class="ml-2 h-5 w-5 transition duration-150 ease-in-out group-hover:text-orange-300/80"
                        aria-hidden="true"
                    />
                </PopoverButton>

                <transition
                    enter-active-class="transition duration-200 ease-out"
                    enter-from-class="translate-y-1 opacity-0"
                    enter-to-class="translate-y-0 opacity-100"
                    leave-active-class="transition duration-150 ease-in"
                    leave-from-class="translate-y-0 opacity-100"
                    leave-to-class="translate-y-1 opacity-0"
                >
                    <PopoverPanel
                        class="absolute right-0 z-10 mt-3 w-80 max-w-sm -translate-x-1/2 transform px-4 sm:px-0 lg:max-w-3xl"
                    >
                        <div class="overflow-hidden rounded-lg shadow-lg ring-1 ring-black/5">
                            <div v-if="loadingNotifications" class="p-4 text-gray-500">Loading...</div>
                            <ul v-else class="divide-y divide-gray-200 bg-white">
                                <li
                                    v-for="notification in notifications"
                                    :key="notification.id"
                                    class="p-4 text-sm text-gray-700 hover:bg-gray-100"
                                >
                                    {{ notification.data.message }}
                                </li>
                            </ul>
                            <div class="bg-gray-50 p-4 text-center">
                                <button class="text-sm text-blue-500 hover:underline" @click="toggleDropdown">
                                    Mark all as read
                                </button>
                            </div>
                        </div>
                    </PopoverPanel>
                </transition>
            </Popover>
        </div>
    </div>
</template>
