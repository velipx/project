<script setup>
import {mdiAccount, mdiCamera, mdiCloudLock} from '@mdi/js';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import SectionMain from '@/Components/Admin/SectionMain.vue';
import CardBox from '@/Components/Admin/CardBox.vue';
import BaseButton from "@/Components/Admin/BaseButton.vue";
import SectionTitleLineWithButton from "@/Components/Admin/SectionTitleLineWithButton.vue";
import BaseDivider from "@/Components/Admin/BaseDivider.vue";
import { computed, ref } from "vue";
import { formatDate } from "@/utils/utils.js";
import UserAvatar from "@/Components/Admin/UserAvatar.vue";
import {router, usePage} from "@inertiajs/vue3";

const props = defineProps({
    user: Object,
    friendship: Object,
});

const ipAddress = computed(() => props.user.session ? props.user.session.ip_address : 'N/A');
const lastActivity = computed(() => props.user.session ? formatDate(props.user.session.last_activity * 1000) : 'N/A');
const showUploadModal = ref(false);

const toggleUploadModal = () => {
    showUploadModal.value = !showUploadModal.value;
};

const onAvatarUploadSuccess = (newAvatarUrl) => {
    props.user.avatar_url = newAvatarUrl;
    toggleUploadModal();
};

// Add friend, accept friend, and reject friend functions
const addFriend = () => {
    router.post(route('admin.friendship.request', props.user.id), {}, {
        onSuccess: (page) => console.log(page.props.flash.success),
    });
};

const acceptFriend = (friendshipId) => {
    router.post(route('admin.friendship.accept', friendshipId), {}, {
        onSuccess: (page) => console.log(page.props.flash.success),
    });
};

const rejectFriend = (friendshipId) => {
    router.post(route('admin.friendship.reject', friendshipId), {}, {
        onSuccess: (page) => console.log(page.props.flash.success),
    });
};

const loginAsUser = () => {
    if (confirm('Are you sure you want to login as this user?')) {
        router.post(route('admin.users.loginAs', props.user.id), {}, {
            onSuccess: (page) => console.log(page.props.flash.success),
        });
    }
};
</script>

<template>
    <AdminLayout>
        <SectionMain>
            <SectionTitleLineWithButton :icon="mdiAccount" title="Profile" main />

            <div class="grid grid-cols-1 gap-6 xl:grid-cols-3 mt-5">
                <CardBox class="lg:col-span-2 xl:col-span-2 text-white py-4 flex">
                    <div class="flex flex-col lg:flex-row items-center lg:items-start">
                        <div class="relative flex">
                            <UserAvatar :username="user.username" :avatar="user.avatar_url" :size="32" class="rounded-full" />
                            <BaseButton :icon="mdiCamera" color="info" class="absolute bottom-0 right-0 rounded-full p-3" iconSize="24" :href="route('admin.users.upload-avatar', user)" modal />
                        </div>

                        <div class="ml-4 lg:ml-6">
                            <h3 class="text-2xl font-base flex items-center">{{ props.user.name }}</h3>
                            <p class="text-sm text-gray-400">{{ props.user.name }} | {{ props.user.name }}</p>
                            <div class="flex items-center mt-2 space-x-4 text-gray-400 text-sm">
                                <span><strong>Test</strong> Likes</span>
                                <span><strong>Test</strong> Posts</span>
                                <span><strong>Test</strong> Media</span>
                                <span><strong>Test</strong> Links</span>
                                <span><strong>Test</strong> Files</span>
                            </div>
                            <div class="mt-4 space-x-4">
                                <!-- Conditionally Render Buttons -->
                                <BaseButton
                                    v-if="!friendship"
                                    label="Add friend"
                                    color="lightDark"
                                    small
                                    @click="addFriend"
                                />
                                <BaseButton
                                    v-if="friendship && friendship.status === 'pending' && friendship.sender_id === usePage().props.auth.user.id"
                                    label="Pending"
                                    color="lightDark"
                                    small
                                    disabled
                                />
                                <BaseButton
                                    v-if="friendship && friendship.status === 'pending' && friendship.receiver_id === usePage().props.auth.user.id"
                                    label="Accept friend"
                                    color="success"
                                    small
                                    @click="acceptFriend(friendship.id)"
                                />
                                <BaseButton
                                    v-if="friendship && friendship.status === 'pending' && friendship.receiver_id === usePage().props.auth.user.id"
                                    label="Reject friend"
                                    color="danger"
                                    small
                                    @click="rejectFriend(friendship.id)"
                                />
                                <BaseButton
                                    v-if="friendship && friendship.status === 'accepted'"
                                    label="Friends"
                                    color="lightDark"
                                    small
                                    disabled
                                />
                                <BaseButton
                                    label="Login as this user"
                                    color="warning"
                                    small
                                    @click="loginAsUser"
                                />
                            </div>
                        </div>
                    </div>
                </CardBox>

                <CardBox class="lg:col-span-1 bg-linear-to-r text-white p-6 text-center">
                    <div class="flex justify-center items-center h-full">
                        <p class="font-semibold">Some info</p>
                    </div>
                </CardBox>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                <section class="flex flex-col">
                    <SectionTitleLineWithButton :icon="mdiCloudLock" title="Security options" :breadcrumbs="false" />
                    <CardBox class="flex flex-col justify-between h-full">
                        <div class="flex items-center justify-between">
                            <p>Last activity <b>{{ lastActivity }}</b> from IP <b>{{ ipAddress }}</b></p>
                            <BaseButton label="Manage sessions" :href="route('admin.users.sessions', user)" color="lightDark" small />
                        </div>
                        <BaseDivider />
                        <div class="flex items-center justify-between">
                            <p>Password changed <b>{{ formatDate(props.user.lastPasswordChange) }}</b></p>
                            <BaseButton label="Change password" :href="route('admin.users.change-password', user)" color="lightDark" small />
                        </div>
                    </CardBox>
                </section>
                <section class="flex flex-col">
                    <SectionTitleLineWithButton :icon="mdiCloudLock" title="Security options" :breadcrumbs="false" />
                    <CardBox class="h-full">
                        test
                        <BaseDivider />
                    </CardBox>
                </section>
            </div>

            <div class="grid grid-cols-1 gap-6 mt-6">
                <CardBox class="text-white">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-bold">Billing Overview</h3>
                        <button class="p-1 rounded-full bg-slate-800">
                            asd
                        </button>
                    </div>
                </CardBox>
            </div>
        </SectionMain>
    </AdminLayout>
</template>
