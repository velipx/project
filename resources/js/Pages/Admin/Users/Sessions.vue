<script setup>
import {mdiAccount, mdiDesktopClassic, mdiTablet, mdiCellphone, mdiMonitor, mdiChevronLeft} from '@mdi/js';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import SectionMain from '@/Components/Admin/SectionMain.vue';
import CardBox from '@/Components/Admin/CardBox.vue';
import SectionTitleLineWithButton from "@/Components/Admin/SectionTitleLineWithButton.vue";
import { computed } from "vue";
import BaseDivider from "@/Components/Admin/BaseDivider.vue";
import BaseIcon from "@/Components/Admin/BaseIcon.vue";
import { router, usePoll } from '@inertiajs/vue3';
import BaseButton from "@/Components/Admin/BaseButton.vue";
import {formatDate, navigateBack} from "@/utils/utils.js";

usePoll(5000)

const props = defineProps({
    sessions: Array,
    user: Object,
});

const deviceIcon = (device) => {
    switch (device) {
        case 'desktop':
            return mdiMonitor;
        case 'tablet':
            return mdiTablet;
        case 'mobile':
            return mdiCellphone;
        default:
            return mdiMonitor;
    }
};

const currentSession = computed(() => props.sessions.find(session => session.is_current_device));

const logoutSession = (sessionId) => {
    router.post(route('admin.users.sessions.logout', { user: props.user }), { sessionId });
};

const logoutAllOtherSessions = () => {
    router.post(route('admin.users.sessions.logout-all-other', { user: props.user }));
};
</script>

<template>
    <AdminLayout>
        <SectionMain>
            <SectionTitleLineWithButton :icon="mdiAccount" title="Profile" main />

            <!-- Session details excluding the current one -->
            <div class="grid grid-cols-1 gap-6 xl:grid-cols-3 mt-5">
                <!-- Adjusting the flex properties for CardBox -->
                <CardBox class="lg:col-span-2 xl:col-span-2 flex flex-col justify-between">
                    <div>
                        <div v-if="sessions.length">
                            <div v-for="(session, index) in sessions" :key="session.id">
                                <div class="flex items-center justify-between rounded-lg shadow-sm">
                                    <div class="flex items-center space-x-3">
                                        <BaseIcon class="text-slate-400" w="10" :path="deviceIcon(session.device)" size="42" />
                                        <div>
                                            <p class="text-slate-200 font-medium">{{ session.platform }} - {{ session.browser }}</p>
                                            <p class="text-sm">
                                                {{ session.ip_address }},
                                                <!-- Display 'This device' message for current device -->
                                                <span :class="session.is_current_device ? 'text-green-500' : 'text-slate-200'">
                                                    {{ session.is_current_device ? 'This device' : formatDate(session.last_activity * 1000) }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                    <!-- Add logout button for each session -->
                                    <button
                                        v-if="!session.is_current_device"
                                        @click="logoutSession(session.id)"
                                        class="text-red-500">
                                        Logout
                                    </button>
                                </div>
                                <!-- Add <BaseDivider /> if this is not the last session -->
                                <BaseDivider v-if="index < sessions.length - 1" />
                            </div>
                        </div>
                        <p v-else>There are no other active sessions.</p>
                    </div>

                    <!-- Move button div inside the CardBox and add spacing as necessary -->
                    <BaseDivider />
                    <div class="mt-6 flex justify-between">
                        <BaseButton @click="navigateBack" color="lightDark" label="Back" :icon="mdiChevronLeft" />
                        <BaseButton v-if="sessions.length" @click="logoutAllOtherSessions" color="danger" label="Logout from all other sessions" />
                    </div>
                </CardBox>

                <CardBox class="lg:col-span-1" :has-background="false">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-500 text-white p-6 text-center rounded-xl">
                        <h3 class="text-xl font-bold mb-4">Current session</h3>
                        <div v-if="currentSession">
                            <p><strong>IP Address:</strong> {{ currentSession.ip_address }}</p>
                            <p><strong>Device:</strong> {{ currentSession.device }}</p>
                            <p><strong>Platform:</strong> {{ currentSession.platform }}</p>
                            <p><strong>Browser:</strong> {{ currentSession.browser }}</p>
                            <p><strong>Last Activity:</strong> {{ 'This device' }}</p>
                        </div>
                        <p v-else>Current session is not available.</p>
                    </div>
                </CardBox>
            </div>
        </SectionMain>
    </AdminLayout>
</template>
