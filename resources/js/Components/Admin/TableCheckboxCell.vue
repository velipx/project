<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    type: {
        type: String,
        default: 'td'
    },
    checked: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['update:checked']);

const isChecked = ref(props.checked);

watch(isChecked, (newVal) => {
    emit('update:checked', newVal);
});

watch(() => props.checked, (newVal) => {
    isChecked.value = newVal;
});

</script>

<template>
    <component :is="type" class="lg:w-1">
        <label class="checkbox">
            <input v-model="isChecked" type="checkbox" />
            <span class="check" />
        </label>
    </component>
</template>
