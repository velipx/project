<script setup>
import { mdiTextBoxOutline, mdiEmail } from '@mdi/js';
import Modal from '@/Components/Admin/Modal.vue';
import FormField from '@/Components/Admin/FormField.vue';
import FormControl from '@/Components/Admin/FormControl.vue';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'inertia-modal';
import {clearFormFieldError} from "@/utils/utils.js";

const modalTitle = "Create New User";
const createRoute = route('admin.users.store');
const { redirect } = useModal();
const form = useForm({ username: null, name: null, email: null, password: null, password_confirmation: null });

const handleFormSubmit = () => {
    form.post(createRoute, {
        onSuccess: () => {
            form.reset();
            redirect();
        },
        onError: ({ errors }) => {
            console.error("Error creating user:", errors);
        }
    });
};
</script>

<template>
    <Modal
        :title="modalTitle"
        button-label="Create"
        has-cancel
        @confirm="handleFormSubmit"
        @cancel="redirect"
        :isProcessing="form.processing"
    >
        <form @submit.prevent="handleFormSubmit" class="space-y-6">
            <FormField label="Name" :error="form.errors.name">
                <div>
                    <FormControl
                        v-model="form.name"
                        :icon="mdiTextBoxOutline"
                        placeholder="Enter user name"
                        required
                        @input="clearFormFieldError(form, 'name')"
                    />
                </div>
            </FormField>
            <FormField label="Username" :error="form.errors.username">
                <div>
                    <FormControl
                        v-model="form.username"
                        :icon="mdiTextBoxOutline"
                        placeholder="Enter user username"
                        required
                        @input="clearFormFieldError(form, 'username')"
                    />
                </div>
            </FormField>
            <FormField label="Email" :error="form.errors.email">
                <div>
                    <FormControl
                        v-model="form.email"
                        :icon="mdiEmail"
                        placeholder="Enter user email"
                        type="email"
                        required
                        @input="clearFormFieldError(form, 'email')"
                    />
                </div>
            </FormField>
            <FormField label="Password" :error="form.errors.password">
                <div>
                    <FormControl
                        v-model="form.password"
                        type="password"
                        placeholder="Enter password"
                        required
                        @input="clearFormFieldError(form, 'password')"
                    />
                </div>
            </FormField>
            <FormField label="Confirm Password" >
                <div>
                    <FormControl
                        v-model="form.password_confirmation"
                        type="password"
                        placeholder="Confirm password"
                        required
                    />
                </div>
            </FormField>
        </form>
    </Modal>
</template>
