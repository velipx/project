<script setup>
import { computed } from 'vue';
import Marker from '@/Components/Admin/Marker.vue';
import BaseIcon from "@/Components/Admin/BaseIcon.vue";
import { mdiAccountMultiple } from "@mdi/js";

const props = defineProps({
    icon: String,
    iconColor: {
        type: String,
        default: 'text-[#2EF2FF]',
    },
    textColor: {
        type: String,
        default: 'text-white',
    },
    href: String,
    label: String,
    containerClassName: String,
    onClick: Function,
    markerFill: String,
    size: {
        type: String,
        default: 'md',
        validator: value => ['xs', 'sm', 'md', 'lg'].includes(value)
    }
});

// Combined size-related classes into a single object structure.
const sizeStyles = {
    xs: {
        button: 'min-h-[30px] text-xs p-[1px]',
        svg: 'w-[1.5rem] h-[1.5rem] mr-2',
        text: 'text-xs',
        glow: 'glow-before-xs glow-after-xs'
    },
    sm: {
        button: 'min-h-[40px] text-sm p-0.5',
        svg: 'w-[2rem] h-[2rem] mr-3',
        text: 'text-sm',
        glow: 'glow-before glow-after'
    },
    md: {
        button: 'min-h-[60px] text-base p-0.5',
        svg: 'w-[2.5rem] h-[2.5rem] mr-4',
        text: 'text-base',
        glow: 'glow-before glow-after'
    },
    lg: {
        button: 'min-h-[80px] text-lg p-0.5',
        svg: 'w-[3rem] h-[3rem] mr-5',
        text: 'text-lg',
        glow: 'glow-before glow-after'
    }
};

// Computed properties for dynamic classes.
const buttonClass = computed(() => [sizeStyles[props.size].button, 'relative g5 rounded-xl shadow-600 hover:shadow-500 transition group', props.containerClassName]);
const svgClass = computed(() => [sizeStyles[props.size].svg, 'z-4']);
const textClass = computed(() => [sizeStyles[props.size].text, props.textColor]);
const glowClass = computed(() => sizeStyles[props.size].glow);
</script>

<template>
    <component
        :is="href ? 'a' : 'button'"
        :href="href"
        :class="buttonClass"
        @click="onClick"
    >
        <span :class="[sizeStyles[size].button, 'relative flex items-center rounded-xl g4 px-4 inner-before group-hover:before:opacity-100 overflow-hidden']">
            <span class="absolute -left-[1px]">
                <Marker :markerFill="markerFill" />
            </span>
            <span class="flex items-center" v-if="icon">
                <svg :class="svgClass" viewBox="0 0 43 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.05" fill-rule="evenodd" clip-rule="evenodd"
                          d="M31.5 1.99805H11.5C9.37497 1.99805 7.42196 2.73453 5.88228 3.96617L15.1712 13.2551C16.8951 11.8444 19.0987 10.998 21.5 10.998C23.9013 10.998 26.1048 11.8444 27.8287 13.2551L37.1177 3.96613C35.578 2.73451 33.625 1.99805 31.5 1.99805ZM26.4056 14.6782C25.0509 13.6251 23.3487 12.998 21.5 12.998C19.6513 12.998 17.949 13.6251 16.5943 14.6782L21.5 19.5838L26.4056 14.6782ZM13.757 14.6693L4.46807 5.3804C3.23646 6.92006 2.5 8.87305 2.5 10.998V30.998C2.5 33.1231 3.23647 35.076 4.46809 36.6157L13.757 27.3268C12.3464 25.6029 11.5 23.3993 11.5 20.998C11.5 18.5968 12.3464 16.3932 13.757 14.6693ZM15.1801 25.9037C14.1271 24.549 13.5 22.8468 13.5 20.998C13.5 19.1494 14.1271 17.4471 15.1801 16.0924L20.0857 20.9981L15.1801 25.9037ZM15.1713 28.741L5.8823 38.0299C7.42197 39.2616 9.37498 39.998 11.5 39.998H31.5C33.625 39.998 35.578 39.2616 37.1176 38.03L27.8287 28.741C26.1048 30.1517 23.9013 30.998 21.5 30.998C19.0987 30.998 16.8951 30.1517 15.1713 28.741ZM26.4056 27.3179C25.0509 28.371 23.3487 28.998 21.5 28.998C19.6513 28.998 17.949 28.371 16.5943 27.3179L21.5 22.4123L26.4056 27.3179ZM27.8198 25.9037L22.9142 20.9981L27.8198 16.0924C28.8729 17.4471 29.5 19.1493 29.5 20.998C29.5 22.8468 28.8729 24.549 27.8198 25.9037ZM29.2429 27.3268C30.6536 25.6029 31.5 23.3993 31.5 20.998C31.5 18.5968 30.6536 16.3932 29.2429 14.6693L38.5319 5.38034C39.7635 6.92002 40.5 8.87303 40.5 10.998V30.998C40.5 33.1231 39.7635 35.0761 38.5319 36.6158L29.2429 27.3268ZM11.5 -0.00195312C5.42487 -0.00195312 0.5 4.92292 0.5 10.998V30.998C0.5 37.0732 5.42487 41.998 11.5 41.998H31.5C37.5751 41.998 42.5 37.0732 42.5 30.998V10.998C42.5 4.92292 37.5751 -0.00195312 31.5 -0.00195312H11.5Z"
                          fill="#2EF2FF"/>
                    <foreignObject x="0" y="0" width="100%" height="100%">
                        <div class="flex justify-center items-center h-full">
                            <BaseIcon :path="icon" size="30" :class="iconColor" />
                        </div>
                    </foreignObject>
                </svg>
            </span>
            <span class="relative z-2 font-poppins font-bold uppercase leading-[24px]" :class="textClass">
                {{ label }}
            </span>
        </span>
        <span :class="glowClass" />
    </component>
</template>
