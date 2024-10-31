<script setup>
import { mdiCog, mdiChevronLeft } from '@mdi/js'
import { useSlots, computed, ref } from 'vue'
import BaseIcon from '@/Components/Admin/BaseIcon.vue'
import BaseButton from '@/Components/Admin/BaseButton.vue'
import IconRounded from '@/Components/Admin/IconRounded.vue'
import Breadcrumbs from "@/Components/Admin/Breadcrumbs.vue";
import {navigateBack} from "@/utils/utils.js";

const props = defineProps({
    icon: {
        type: String,
        default: null
    },
    title: {
        type: String,
        required: true
    },
    breadcrumbs: {
        type: Boolean,
        default: true
    },
    main: Boolean
})

const hasSlot = computed(() => useSlots().default)
const isHovered = ref(false);

const handleHover = () => {
    isHovered.value = true;
}

const handleMouseLeave = () => {
    isHovered.value = false;
}
</script>

<template>
    <section :class="{ 'pt-6': !main }" class="mb-6 flex items-center justify-between">
        <div class="flex items-center justify-start relative">
            <div
                v-if="icon && main"
                @mouseenter="handleHover"
                @mouseleave="handleMouseLeave"
                @click="navigateBack"
                class="cursor-pointer"
            >
                <IconRounded
                    :icon="isHovered ? mdiChevronLeft : icon"
                    color="light"
                    class="mr-3"
                    bg
                >
                    <template #default>
                        <svg
                            v-if="isHovered"
                            key="hovered"
                            class="fade-icon"
                            :width="24"
                            :height="24"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                        >
                            <path :d="mdiChevronLeft" />
                        </svg>
                        <svg
                            v-else
                            key="not-hovered"
                            class="fade-icon"
                            :width="24"
                            :height="24"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                        >
                            <path :d="icon" />
                        </svg>
                    </template>
                </IconRounded>
            </div>
            <BaseIcon v-else-if="icon" :path="icon" class="mr-2" size="20" />
            <h1 :class="main ? 'text-3xl' : 'text-2xl'" class="leading-tight">
                {{ title }}
            </h1>
        </div>
        <slot v-if="hasSlot" />
        <Breadcrumbs v-else-if="props.breadcrumbs" />
    </section>
</template>

<style scoped>
.fade-icon {
    transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
}

.fade-icon-enter-from, .fade-icon-leave-to {
    opacity: 0;
    visibility: hidden;
}
</style>
