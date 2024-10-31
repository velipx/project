<script setup>
import { mdiLogout, mdiClose } from '@mdi/js'
import { computed } from 'vue'
import AsideMenuList from '@/Components/Admin/AsideMenuList.vue'
import AsideMenuItem from '@/Components/Admin/AsideMenuItem.vue'
import BaseIcon from '@/Components/Admin/BaseIcon.vue'

defineProps({
    menu: {
        type: Array,
        required: true
    }
})

const emit = defineEmits(['menu-click', 'aside-lg-close-click'])

const logoutItem = computed(() => ({
    label: 'Logout',
    icon: mdiLogout,
    color: 'info',
    isLogout: true
}))

const menuClick = (event, item) => {
    emit('menu-click', event, item)
}

const asideLgCloseClick = (event) => {
    emit('aside-lg-close-click', event)
}
</script>

<template>
    <aside
        id="aside"
        class="lg:py-2 lg:pl-2 w-72 fixed flex z-40 top-0 h-screen transition-position overflow-hidden"
    >
        <div class="aside lg:rounded-2xl flex-1 flex flex-col overflow-hidden dark:bg-slate-900">
            <div class="aside-brand flex flex-row h-20 items-center justify-between dark:bg-slate-900">
                <div class="text-center flex-1 lg:text-left lg:pl-6 xl:text-center xl:pl-0">
                    <img
                        class="h-12 ml-4"
                        src="../../../logo.png"
                        alt="Logo"
                    />
                </div>
                <button class="hidden lg:inline-block xl:hidden p-3" @click.prevent="asideLgCloseClick">
                    <BaseIcon :path="mdiClose" />
                </button>
            </div>
            <div
                class="flex-1 overflow-y-auto overflow-x-hidden aside-scrollbars dark:aside-scrollbars-[slate]"
            >
                <AsideMenuList :menu="menu" @menu-click="menuClick" />
            </div>

            <ul>
                <AsideMenuItem :item="logoutItem" @menu-click="menuClick" />
            </ul>
        </div>
    </aside>
</template>
