<script setup>
import { ref, computed, onMounted } from 'vue';
import { mdiMinus, mdiPlus } from '@mdi/js';
import { getButtonColor } from '@/colors.js';
import { Link } from '@inertiajs/vue3'
import BaseIcon from '@/Components/Admin/BaseIcon.vue';
import AsideMenuList from '@/Components/Admin/AsideMenuList.vue';
import { useDarkModeStore } from "@/darkMode.js";

const props = defineProps({
    item: {
        type: Object,
        required: true
    },
    isDropdownList: Boolean
});

const emit = defineEmits(['menu-click']);

const hasColor = computed(() => props.item && props.item.color);

const asideMenuItemActiveStyle = computed(() =>
    hasColor.value ? '' : 'aside-menu-item-active font-bold'
);

const isDropdownActive = ref(false);

const componentClass = computed(() => [
    props.isDropdownList ? 'py-3 px-6 text-sm' : 'py-3',
    hasColor.value
        ? getButtonColor(props.item.color, false, true)
        : `aside-menu-item dark:text-slate-300 dark:hover:text-white`
]);

const hasDropdown = computed(() => !!props.item.menu);

const menuClick = (event) => {
    emit('menu-click', event, props.item);

    if (hasDropdown.value) {
        isDropdownActive.value = !isDropdownActive.value;
    }
};

const itemHref = computed(() => (props.item.route ? route(props.item.route) : props.item.href));

const darkModeStore = useDarkModeStore();

// Update for activeInactiveStyle
const activeInactiveStyle = computed(() => {
    const currentRoute = route().current(); // Uzimamo trenutnu rutu
    const isActive = (props.item.route && currentRoute === props.item.route) ||
        (props.item.route === 'admin.roles' && currentRoute.startsWith('admin.roles')) ||
        (props.item.route === 'admin.permissions' && currentRoute.startsWith('admin.permissions'));
    return isActive ? 'aside-menu-item-active font-bold' : '';
});

// Automatically expand menu if any subitem is active
onMounted(() => {
    const currentRoute = route().current();
    if (currentRoute.startsWith('admin.roles') || currentRoute.startsWith('admin.permissions')) {
        isDropdownActive.value = true; // Proširi meni ako je neka od podmenija aktivna
    }
});
</script>

<template>
    <li>
        <component
            :is="item.route ? Link : 'a'"
            :href="itemHref"
            :target="item.target ?? null"
            class="flex cursor-pointer"
            :class="componentClass"
            @click="menuClick"
        >
            <BaseIcon
                v-if="item.icon"
                :path="item.icon"
                :class="activeInactiveStyle"
                class="flex-none"
                w="w-16"
                :size="18"
            />
            <span
                class="grow text-ellipsis line-clamp-1"
                :class="[
                    { 'pr-12': !hasDropdown }, activeInactiveStyle
                ]"
            >{{ item.label }}</span>
            <BaseIcon
                v-if="hasDropdown"
                :path="isDropdownActive ? mdiMinus : mdiPlus"
                class="flex-none"
                :class="activeInactiveStyle"
                w="w-12"
            />
        </component>
        <AsideMenuList
            v-if="hasDropdown"
            :menu="item.menu"
            :class="['aside-menu-dropdown', isDropdownActive ? 'block dark:bg-slate-800/50' : 'hidden']"
            is-dropdown-list
        />
    </li>
</template>
