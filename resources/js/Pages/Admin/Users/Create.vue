<script setup>
import { mdiTextBoxOutline, mdiEmail } from '@mdi/js';
import {useForm, usePage} from '@inertiajs/vue3';
import { clearFormFieldError } from "@/utils/utils.js";
import BaseButtons from "@/Components/Admin/BaseButtons.vue";
import BaseButton from "@/Components/Admin/BaseButton.vue";
import FormFieldGenerator from '@/Components/Admin/FormFieldGenerator.vue';
import { ref } from "vue";

const MODAL_TITLE = "Create New User";
const CREATE_ROUTE = route('admin.users.store');

const form = useForm({
    username: null,
    name: null,
    email: null,
    password: null,
    password_confirmation: null,
    _token: usePage().props.csrf_token,
});

const userModalRef = ref(null);

const handleFormSubmit = () => {
    form.post(CREATE_ROUTE, {
        onSuccess: () => {
            form.reset();
            userModalRef.value.close();
        }
    });
};
</script>

<template>
    <Modal
        ref="userModalRef"
        #default="{ close }"
        slideover
    >
        <div class="flex items-center justify-between mb-3">
            <h1 class="text-2xl dark:text-white">
                {{ MODAL_TITLE }}
            </h1>
        </div>
        <form @submit.prevent="handleFormSubmit" class="flex-1 overflow-y-auto p-4 space-y-3">
            <FormFieldGenerator
                label="Name"
                modelValue="name"
                :icon="mdiTextBoxOutline"
                type="text"
                placeholder="Enter user name"
                errorKey="name"
                :form="form"
                :clearFormFieldError="clearFormFieldError"
            />
            <FormFieldGenerator
                label="Username"
                modelValue="username"
                :icon="mdiTextBoxOutline"
                type="text"
                placeholder="Enter user username"
                errorKey="username"
                :form="form"
                :clearFormFieldError="clearFormFieldError"
            />
            <FormFieldGenerator
                label="Email"
                modelValue="email"
                :icon="mdiEmail"
                type="email"
                placeholder="Enter user email"
                errorKey="email"
                :form="form"
                :clearFormFieldError="clearFormFieldError"
            />
            <FormFieldGenerator
                label="Password"
                modelValue="password"
                :icon="null"
                type="password"
                placeholder="Enter password"
                errorKey="password"
                :form="form"
                :clearFormFieldError="clearFormFieldError"
            />
            <FormFieldGenerator
                label="Confirm Password"
                modelValue="password_confirmation"
                :icon="null"
                type="password"
                placeholder="Confirm password"
                errorKey="password_confirmation"
                :form="form"
                :clearFormFieldError="clearFormFieldError"
            />
            <BaseButtons>
                <BaseButton
                    label="Create"
                    color="info"
                    :processing="form.processing"
                    type="submit"
                />
                <BaseButton
                    label="Cancel"
                    color="info"
                    outline
                    @click="close"
                />
            </BaseButtons>
        </form>
    </Modal>
</template>
