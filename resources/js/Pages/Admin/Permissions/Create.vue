<script setup>
import { mdiTextBoxOutline } from '@mdi/js';
import Modal from '@/Components/Admin/Modal.vue';
import FormField from '@/Components/Admin/FormField.vue';
import FormControl from '@/Components/Admin/FormControl.vue';
import { useForm } from '@inertiajs/vue3';
import {clearFormFieldError} from "@/utils/utils.js";

const modalTitle = "Create New Permission";
const createRoute = route('admin.permissions.store');
const form = useForm({ name: null });

const handleFormSubmit = () => {
    form.post(createRoute, {
        onSuccess: () => {
            form.reset();
        },
        onError: ({ errors }) => {
            console.error("Error creating permission:", errors);
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
        size="small"
        :isProcessing="form.processing"
    >
        <form @submit.prevent="handleFormSubmit" class="space-y-6">
            <FormField label="Permission Name" :error="form.errors.name">
                <div>
                    <FormControl
                        v-model="form.name"
                        :icon="mdiTextBoxOutline"
                        placeholder="Enter permission name"
                        required
                        @output="clearFormFieldError('name')"
                    />
                </div>
            </FormField>
        </form>
    </Modal>
</template>
