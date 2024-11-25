<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Popover, PopoverButton, PopoverPanel } from '@headlessui/vue';
import BaseIcon from '@/Components/Admin/BaseIcon.vue';
import { mdiBell } from '@mdi/js';
import { usePage } from '@inertiajs/vue3';
import {formatDate} from "@/utils/utils.js";
import BaseButton from "@/Components/Admin/BaseButton.vue";
import BaseButtons from "@/Components/Admin/BaseButtons.vue";
import UserAvatar from "@/Components/Admin/UserAvatar.vue";

const notifications = ref([]);
const isLoading = ref(false);
const notificationCount = ref(0);
let notificationChannel = null;

const { user } = usePage().props.auth;

const fetchNotifications = async () => {
    notifications.value = [];
    isLoading.value = true;
    try {
        const response = await axios.get(route('notifications.index'));
        notifications.value = response.data.notifications;
        notificationCount.value = response.data.unreadCount;
    } catch (error) {
        console.error("Error fetching notifications", error);
    } finally {
        isLoading.value = false;
    }
};

onMounted(async () => {
    await fetchNotifications();

    notificationChannel = Echo.private(`notifications.${user.id}`)
        .notification(() => {
            notificationCount.value += 1;
        });
});

onUnmounted(() => {
    if (notificationChannel) {
        Echo.leave(`notifications.${user.id}`);
    }
});

const handleBellClick = async () => {
    await fetchNotifications();
};

const handleFriendship = async (friendshipId, action) => {
    try {
        const response = await axios.post(route(`admin.friendship.${action}`, {friendship: friendshipId}));
        const updatedNotification = response.data.notification;
        notifications.value = notifications.value.map(notification =>
            notification.data.friendship_id === friendshipId ? updatedNotification : notification
        );
        notificationCount.value -= 1;
    } catch (error) {
        console.error(`Error during friendship ${action}`, error);
    }
};
</script>
+
<template>
    <div>
        <Popover class="relative">
            <PopoverButton
                class="group inline-flex items-center rounded-md bg-s2 px-3 py-2 text-base font-medium hover:text-white focus:outline-hidden focus-visible:ring-2 focus-visible:ring-white/75"
                @click="handleBellClick">
                <BaseIcon :path="mdiBell" size="24" class="text-white relative">
                    <span v-if="notificationCount > 0"
                          class="absolute -top-1 -right-1 inline-flex items-center justify-center rounded-full bg-red-500 text-xs font-bold text-white h-4 w-4">
                        {{ notificationCount }}
                    </span>
                </BaseIcon>
            </PopoverButton>
            <transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="translate-y-1 opacity-0"
                enter-to-class="translate-y-0 opacity-100"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="translate-y-0 opacity-100"
                leave-to-class="translate-y-1 opacity-0">

                <PopoverPanel class="absolute right-0 mt-1.5 w-[500px] max-w-sm transform px-4 sm:px-0 lg:max-w-md">
                    <div
                        class="overflow-hidden rounded-b-lg shadow-lg ring-1 ring-black/5 dark:border-2 dark:border-s2">
                        <div class="relative grid gap-8 bg-white dark:bg-s1 p-2 lg:grid-cols-1">
                            <div v-if="isLoading" class="text-black dark:text-white">
                                <div role="status" class="max-w-md p-2 space-y-4 divide-y divide-gray-200 rounded-xs animate-pulse dark:divide-gray-700/20 dark:border-gray-700">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="h-2.5 bg-gray-300 rounded-full dark:bg-gray-600 w-24 mb-2.5"></div>
                                            <div class="w-32 h-2 bg-gray-200 rounded-full dark:bg-gray-700"></div>
                                        </div>
                                        <div class="h-2.5 bg-gray-300 rounded-full dark:bg-gray-700 w-12"></div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="divide-y divide-gray-200 dark:divide-gray-700/20 px-2">
                                <div v-for="notification in notifications" :key="notification.id" class="py-2 flex items-center">
                                    <UserAvatar :user="notification.data.sender_id" :size="10" class="mr-3" :username="notification.data.sender_name"/>
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-200 mb-1">
                                            <p class="dark:text-slate-200">{{ notification.data.message }}</p>
                                            <p class="text-xs dark:text-slate-600">{{ formatDate(notification.created_at) }}</p>
                                        </div>
                                        <div v-if="notification.data && notification.data.type === 'friendship_request' && !notification.read_at" class="flex space-x-2 my-2">
                                            <BaseButtons>
                                                <BaseButton
                                                    @click="handleFriendship(notification.data.friendship_id, 'accept')"
                                                    label="Accept"
                                                    color="success"
                                                    small
                                                />
                                                <BaseButton
                                                    @click="handleFriendship(notification.data.friendship_id, 'reject')"
                                                    label="Decline"
                                                    color="whiteDark"
                                                    outline
                                                    small
                                                />
                                            </BaseButtons>
                                        </div>
                                        <div v-else-if="notification.data && notification.data.type === 'friendship_request' && notification.read_at" class="text-green-500 text-sm mt-2">
                                            Friendship Accepted
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <span v-if="!notification.read_at" class="inline-block w-3 h-3 bg-red-500 rounded-full"></span>
                                    </div>
                                </div>
                                <div v-if="notifications.length === 0" class="text-black dark:text-white text-center py-2">
                                    No new notifications
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-s2 p-2">
                            <a href="#" class="flow-root rounded-md px-2 py-2 text-center transition duration-150 ease-in-out hover:bg-gray-100 dark:hover:bg-s1/50 focus:outline-hidden focus-visible:ring-3 focus-visible:ring-orange-500/50">
                                <span class="flex items-center justify-center">
                                    <span class="text-sm font-medium text-gray-900 dark:text-slate-400 dark:hover:text-slate-200">View all notifications</span>
                                </span>
                            </a>
                        </div>
                    </div>
                </PopoverPanel>
            </transition>
        </Popover>
    </div>
</template>
