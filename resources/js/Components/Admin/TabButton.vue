<script setup>
import { TabList, Tab } from '@headlessui/vue'
import BaseButton from '@/Components/Admin/BaseButton.vue'
import {defineComponent} from "vue";

// Funkcija za generisanje klasa
const generateButtonClass = (selected) => {
    return selected ? 'bg-blue-500 text-white dark:text-white focus:ring-0' : ''
}

defineProps({
    label: {
        type: String,
        required: true
    },
});
</script>

<template>
    <Tab v-slot="{ selected }" as="div">
        <BaseButton
            color="info"
            :label="label"
            outline
            :class="generateButtonClass(selected)"
        />
    </Tab>
</template>
