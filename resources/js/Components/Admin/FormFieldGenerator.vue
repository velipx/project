<script setup>
import FormField from '@/Components/Admin/FormField.vue';
import FormControl from '@/Components/Admin/FormControl.vue';

const props = defineProps({
    label: String,
    modelValue: String,
    icon: String,
    type: {
        type: String,
        default: 'text'
    },
    placeholder: String,
    errorKey: String,
    form: Object,
    clearFormFieldError: Function,
    required: {
        type: Boolean,
        default: true
    }
});

const onInput = () => {
    props.clearFormFieldError(props.form, props.errorKey);
};
</script>

<template>
    <FormField :label="label" :error="form.errors[errorKey]">
        <div>
            <FormControl
                v-model="form[modelValue]"
                :icon="icon"
                :type="type"
                :placeholder="placeholder"
                :required="required"
                @input="onInput"
            />
        </div>
    </FormField>
</template>
