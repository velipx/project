<script setup>
import { mdiClose, mdiBrightness5, mdiBrightness3 } from '@mdi/js';
import AsideMenuList from '@/Components/Admin/AsideMenuList.vue';
import BaseIcon from '@/Components/Admin/BaseIcon.vue';
import { useDarkModeStore } from '@/darkMode';

defineProps({
    menu: {
        type: Array,
        required: true
    }
});

const darkModeStore = useDarkModeStore();

const toggleDarkMode = (isDark) => {
    darkModeStore.set(isDark);
};

const emit = defineEmits(['menu-click', 'aside-lg-close-click']);

const buttonClass = (isActive) => [
    'relative z-1 group flex justify-center items-center h-10 basis-1/2 font-semibold transition-colors text-s6',
    isActive ? 'text-[#fff]' : 'text-white',
];

const iconClass = 'inline-block w-6 h-6 transition-colors mr-3';

const menuClick = (event, item) => {
    emit('menu-click', event, item);
};

const asideLgCloseClick = (event) => {
    emit('aside-lg-close-click', event);
};
</script>

<template>
    <aside
        id="aside"
        class="w-72 fixed flex z-40 top-0 h-screen transition-position overflow-hidden"
    >
        <div class="aside flex-1 flex flex-col overflow-hidden bg-slate-900 dark:bg-s2 p-4">
            <div class="aside-brand flex flex-row h-20 items-center justify-between">
                <div class="text-center flex-1 lg:text-left lg:pl-6 xl:text-center xl:pl-0">
                    <img
                        class="h-12 ml-4"
                        src="../../../logo.png"
                        alt="Logo"
                    />
                </div>
                <button class="hidden lg:inline-block xl:hidden p-3" @click.prevent="asideLgCloseClick">
                    <BaseIcon :path="mdiClose"/>
                </button>
            </div>
            <div
                class="flex-1 overflow-y-auto overflow-x-hidden aside-scrollbars dark:aside-scrollbars-[slate]"
            >
                <AsideMenuList :menu="menu" @menu-click="menuClick"/>
            </div>

            <ul class="mt-4">
                <li class="relative flex w-full p-1 bg-s8 rounded-xl">
                    <!-- Sliding Indicator -->
                    <div
                        class="absolute top-1 bottom-1 left-1 w-[calc(50%-0.25rem)] rounded-[0.625rem] transition-transform bg-s4"
                        :class="darkModeStore.isEnabled ? 'translate-x-full' : 'translate-x-0'"
                    ></div>
                    <!-- Light Mode Button -->
                    <button
                        @click="toggleDarkMode(false)"
                        :class="buttonClass(!darkModeStore.isEnabled)"
                    >
                        <BaseIcon
                            :path="mdiBrightness5"
                            :class="iconClass"
                        />
                        Light
                    </button>
                    <!-- Dark Mode Button -->
                    <button
                        @click="toggleDarkMode(true)"
                        :class="buttonClass(darkModeStore.isEnabled)"
                    >
                        <BaseIcon
                            :path="mdiBrightness3"
                            :class="iconClass"
                        />
                        Dark
                    </button>
                </li>
            </ul>
        </div>
    </aside>
</template>
