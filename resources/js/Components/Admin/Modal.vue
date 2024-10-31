<script setup>
import { computed, onMounted, onBeforeUnmount, watch } from 'vue';
import { mdiClose } from '@mdi/js';
import BaseButton from '@/Components/Admin/BaseButton.vue';
import BaseButtons from '@/Components/Admin/BaseButtons.vue';
import CardBox from '@/Components/Admin/CardBox.vue';
import OverlayLayer from '@/Components/Admin/OverlayLayer.vue';
import CardBoxComponentTitle from '@/Components/Admin/CardBoxComponentTitle.vue';
import { useModal } from 'inertia-modal';

const props = defineProps({
    title: String,
    buttonLabel: {
        type: String,
        default: 'Done'
    },
    hasCancel: Boolean,
    button: {
        type: String,
        default: 'info'
    },
    size: {
        type: String,
        default: 'medium',
        validator: value => ['xs', 'small', 'medium', 'large'].includes(value)
    },
    isDisabled: {
        type: Boolean,
        default: false
    },
    isProcessing: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['cancel', 'confirm']);
const { show, close, redirect } = useModal();

const handleConfirm = () => {
    emit('confirm');
};

const handleCancel = () => {
    redirect();
    emit('cancel');
};

// Modal size class
const modalSizeClass = computed(() => {
    switch (props.size) {
        case 'xs':
            return 'sm:w-3/5 md:w-1/2 lg:w-1/3 xl:w-1/5';
        case 'small':
            return 'sm:w-1/2 md:w-1/2 lg:w-1/3 xl:w-1/4';
        case 'large':
            return 'sm:w-3/4 md:w-2/3 lg:w-3/4 xl:w-1/2';
        default:
            return 'sm:w-full md:w-2/3 lg:w-1/2 xl:w-1/3';
    }
});

// Handle ESC key press
const handleKeydown = (event) => {
    if (event.key === 'Escape') {
        handleCancel();
    }
};

onMounted(() => {
    window.addEventListener('keydown', handleKeydown);
});

onBeforeUnmount(() => {
    window.removeEventListener('keydown', handleKeydown);
});
</script>

<template>
    <OverlayLayer v-if="show" @overlay-click="handleCancel">
        <CardBox :class="['shadow-lg max-h-modal z-50 flex flex-col', modalSizeClass]" is-modal>
            <CardBoxComponentTitle :title="props.title">
                <BaseButton v-if="props.hasCancel" :icon="mdiClose" color="whiteDark" small rounded-full @click="handleCancel" />
            </CardBoxComponentTitle>
            <div
                class="flex-1 overflow-y-auto p-4 space-y-3 max-h-[450px]"
                :class="size === 'xs' ? 'flex justify-center' : ''"
            >
                <slot />
            </div>
            <div class="mt-4" :class="size === 'xs' ? 'flex justify-center' : ''">
                <slot name="footer">
                    <BaseButtons v-if="size !== 'xs'">
                        <BaseButton
                            :label="props.buttonLabel"
                            :color="props.button"
                            :processing="props.isProcessing"
                            @click="handleConfirm"
                        />
                        <BaseButton
                            v-if="props.hasCancel"
                            label="Cancel"
                            :color="props.button"
                            outline
                            @click="handleCancel"
                        />
                    </BaseButtons>
                    <BaseButton v-else :label="props.buttonLabel" :color="props.button" :processing="props.isProcessing" @click="handleConfirm" />
                </slot>
            </div>
        </CardBox>
    </OverlayLayer>
</template>
