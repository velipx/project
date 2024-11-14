<script setup>
import { ref, computed, onMounted } from 'vue';
import { mdiMinus, mdiPlus } from '@mdi/js';
import { getButtonColor } from '@/colors.js';
import { Link } from '@inertiajs/vue3';
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

const isDropdownActive = ref(false);

const isCurrentRouteMatch = (routePattern) => {
    const currentRoute = route().current();
    return currentRoute && currentRoute.startsWith(routePattern);
};

const isActiveRoute = computed(() => {
    if (props.item.route === 'admin.roles' || props.item.route === 'admin.permissions') {
        return isCurrentRouteMatch('admin.roles') || isCurrentRouteMatch('admin.permissions');
    }
    return (props.item.route && isCurrentRouteMatch(props.item.route));
});

const dropdownItemActiveStyle = computed(() => {
    return props.isDropdownList && isActiveRoute.value ? 'text-white' : '';
});

const activeInactiveStyle = computed(() => {
    if (!props.isDropdownList) {
        if ((props.item.label === 'Roles & Permissions' && isDropdownActive.value) || isActiveRoute.value) {
            return 'aside-menu-item-active dark:text-white bg-s3 rounded-md shadow-[inset_0px_0.0625rem_0_rgba(255,255,255,0.05),0_0.25rem_0.5rem_0_rgba(0,0,0,0.1)]';
        }
    }
    return '';
});

const componentClass = computed(() => [
    props.isDropdownList ? 'px-6 text-sm' : '',
    hasColor.value
        ? getButtonColor(props.item.color, false, true)
        : `aside-menu-item dark:text-slate-300 dark:hover:text-white py-2.5 transition-colors font-semibold text-sm items-center`,
    activeInactiveStyle.value,
    dropdownItemActiveStyle.value
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

onMounted(() => {
    if (isCurrentRouteMatch('admin.roles') || isCurrentRouteMatch('admin.permissions')) {
        isDropdownActive.value = true;
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
                :class="`text-[${props.item.iconColor}]`"
                :path="item.icon"
                :style="`color: ${props.item.iconColor}`"
                class="flex-none"
                w="w-16"
                :size="18"
            />
            <span
                class="grow text-ellipsis line-clamp-1"
                :class="{ 'pr-12': !hasDropdown }"
            >{{ item.label }}</span>
            <BaseIcon
                v-if="hasDropdown"
                :path="isDropdownActive ? mdiMinus : mdiPlus"
                class="flex-none"
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

<style scoped>
.text-white {
    color: white;
    font-weight: bold; /* Ako želite dodatne stilove za aktivne stavke unutar dropdown menija */
}
</style>
