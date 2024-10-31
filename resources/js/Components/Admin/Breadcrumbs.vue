<!-- /components/Breadcrumb.vue -->
<template>
    <nav v-if="breadcrumbs" class="p-2" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-1">
            <li v-for="(page, index) in breadcrumbs" :key="index" class="flex items-center">
                <span v-if="page === '/'" class="text-slate-400 flex">
                     <BaseIcon :path="mdiChevronRight" class="items-center" size="20" />
                </span>
                <Link
                    v-else-if="page.url"
                    :href="page.url"
                    :class="['text-slate-400 hover:text-white text-base', { 'font-semibold text-slate-100': page.current }]"
                >
                    {{ page.title }}
                </Link>
                <span v-else class="text-gray-500">{{ page.title }}</span>
                <!-- Append separator except for the last item -->
            </li>
        </ol>
    </nav>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

import {mdiChevronRight, mdiHome} from "@mdi/js";
import BaseIcon from "@/Components/Admin/BaseIcon.vue";

const insertBetween = (items, insertion) => {
    return items.flatMap(
        (value, index, array) =>
            array.length - 1 !== index ? [value, insertion] : value,
    );
};

const breadcrumbs = computed(() => insertBetween(usePage().props.breadcrumbs || [], '/'));
</script>
