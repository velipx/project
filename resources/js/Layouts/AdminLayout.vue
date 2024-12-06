<script setup>
import { mdiForwardburger, mdiBackburger, mdiMenu } from '@mdi/js'
import { onMounted, onUnmounted, ref, watch } from 'vue'
import menuAside from '@/routes/menuAside.js'
import menuNavBar from '@/routes/menuNavBar.js'
import { useDarkModeStore } from '@/darkMode.js'
import BaseIcon from '@/Components/Admin/BaseIcon.vue'
import FormControl from '@/Components/Admin/FormControl.vue'
import NavBar from '@/Components/Admin/NavBar.vue'
import NavBarItemPlain from '@/Components/Admin/NavBarItemPlain.vue'
import AsideMenu from '@/Components/Admin/AsideMenu.vue'
import FooterBar from '@/Components/Admin/FooterBar.vue'
import WalletBalance from '@/Components/Admin/WalletBalance.vue'
import { router, usePage } from '@inertiajs/vue3'
import { TYPE, useToast } from "vue-toastification"

const ASIDE_PADDING_CLASS = 'xl:pl-72';
const ASIDE_MOBILE_CLASS = 'ml-72 lg:ml-0';
const TOAST_DURATION = 5000;

const darkModeStore = useDarkModeStore()
const isAsideMobileExpanded = ref(false)
const isAsideLgActive = ref(false)

const handleMenuClick = ({ isToggleLightDark, isLogout }) => {
    if (isToggleLightDark) {
        darkModeStore.set()
    }
    if (isLogout) {
        router.post(route('logout'))
    }
}

const toast = useToast();
const page = usePage();

watch(
    () => page.props.flash,
    (flash) => {
        if (!flash) return;

        let toastId = null;

        if (flash.message) {
            toastId = toast({ content: flash.message, type: TYPE.DEFAULT });
        }
        if (flash.success) {
            toastId = toast.success(flash.success);
        }
        if (flash.error) {
            toastId = toast.error(flash.error);
        }

        if (toastId !== null) {
            setTimeout(() => toast.dismiss(toastId), TOAST_DURATION);
        }
    },
    { deep: true }
);

router.on('navigate', () => {
    isAsideMobileExpanded.value = false
    isAsideLgActive.value = false
})

const userId = usePage().props.auth.user.id;

let notificationChannel;

onMounted(() => {
    notificationChannel = Echo.private(`notifications.${userId}`)
        .notification((notification) => {
            toast.success(notification.message, {timeout: TOAST_DURATION});
        });
});

onUnmounted(() => {
    if (notificationChannel) {
        Echo.leave(`notifications.${userId}`);
    }
});
</script>
<template>
    <div :class="{ 'overflow-hidden lg:overflow-visible': isAsideMobileExpanded }">
        <div
            :class="[ASIDE_PADDING_CLASS, { [ASIDE_MOBILE_CLASS]: isAsideMobileExpanded }]"
            class="pt-14 min-h-screen w-screen transition-position lg:w-auto bg-s2 rounded-lg dark:text-slate-100"
        >
            <NavBar
                :menu="menuNavBar"
                :class="[ASIDE_PADDING_CLASS, { [ASIDE_MOBILE_CLASS]: isAsideMobileExpanded }]"
                @menu-click="handleMenuClick"
            >
                <NavBarItemPlain display="flex lg:hidden"
                                 @click.prevent="isAsideMobileExpanded = !isAsideMobileExpanded">
                    <BaseIcon :path="isAsideMobileExpanded ? mdiBackburger : mdiForwardburger" size="24"/>
                </NavBarItemPlain>
                <NavBarItemPlain display="hidden lg:flex xl:hidden" @click.prevent="isAsideLgActive = true">
                    <BaseIcon :path="mdiMenu" size="24"/>
                </NavBarItemPlain>
                <NavBarItemPlain use-margin>
                    <WalletBalance />
                </NavBarItemPlain>
            </NavBar>
            <AsideMenu
                :is-aside-mobile-expanded="isAsideMobileExpanded"
                :is-aside-lg-active="isAsideLgActive"
                :menu="menuAside"
                @menu-click="handleMenuClick"
                @aside-lg-close-click="isAsideLgActive = false"
            />
            <main class="relative bg-gray-50 dark:bg-s1 rounded-[1.25rem] min-h-screen xl:mr-5" :key="page.url">
                <slot/>
            </main>

            <FooterBar/>
        </div>
    </div>
</template>

<style scoped>
.page-enter-active,
.page-leave-active {
    @apply transition-opacity;
}

.page-enter,
.page-leave-active {
    @apply opacity-50;
}
</style>
