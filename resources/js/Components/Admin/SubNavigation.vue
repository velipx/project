<script setup>
import { defineProps } from 'vue';
import BaseButton from "@/Components/Admin/BaseButton.vue";
import CardBox from "@/Components/Admin/CardBox.vue";

const props = defineProps({
    routes: {
        type: Array,
        required: true,
    },
    buttonLabel: {
        type: String,
        default: '',
    },
    buttonIcon: {
        type: String,
        default: '',
    },
    onButtonClick: {
        type: Function,
        default: () => {},
    },
});

const currentRoute = route().current();

const getButtonClass = (nav) => {
    return currentRoute.startsWith(nav.route) ? 'bg-blue-500' : undefined;
};
</script>

<template>
    <CardBox>
        <div class="flex flex-col sm:flex-row justify-between -m-3">
            <div class="flex space-x-2 overflow-x-auto">
                <BaseButton
                    v-for="(nav, index) in routes"
                    :key="index"
                    :routeName="nav.route"
                    color="info"
                    :label="nav.label"
                    outline
                    class="text-white dark:text-white focus:ring-0 border-0"
                    :class="getButtonClass(nav)"
                />
            </div>
            <BaseButton
                v-if="buttonLabel"
                :label="buttonLabel"
                :icon="buttonIcon"
                class="flex justify-end mt-2 sm:mt-0"
                @click="onButtonClick"
                color="whiteDark"
            />
        </div>
    </CardBox>
</template>
