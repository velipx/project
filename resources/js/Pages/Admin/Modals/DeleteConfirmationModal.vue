<script setup>
import Modal from '@/Components/Admin/Modal.vue';
import {useForm} from '@inertiajs/vue3';

const props = defineProps({
    resources: Array,
    deleteRoute: String,
});

const mapResourceIds = resources => resources.map(resource => resource.id);

const form = useForm({
    ids: mapResourceIds(props.resources)
});

const logError = ({errors}) => {
    console.error("Error deleting resources:", errors);
};

const deleteResources = () => {
    form.delete(props.deleteRoute, {
        onSuccess: redirect,
        onError: logError,
    });
};

const modalConfig = {
    isProcessing: form.processing,
    title: "Confirm Deletion",
    buttonLabel: "Delete",
    hasCancel: true,
    confirmEvent: deleteResources,
    size: "small",
    button: "danger",
};
</script>

<template>
    <Modal
        :isProcessing="modalConfig.isProcessing"
        :title="modalConfig.title"
        :button-label="modalConfig.buttonLabel"
        :has-cancel="modalConfig.hasCancel"
        @confirm="modalConfig.confirmEvent"
        :size="modalConfig.size"
        :button="modalConfig.button"
        @cancel="modalConfig.cancelEvent"
    >
        <div class="dark:text-white">
            <p>Are you sure you want to delete these items?</p>
            <div class="flex-1 overflow-y-auto space-y-3 max-h-96 dark:text-white">
                <ul class="list-decimal list-inside mt-5">
                    <li class="dark:text-red-400" v-for="resource in props.resources" :key="resource.id">{{
                            resource.name
                        }}
                    </li>
                </ul>
            </div>
        </div>
    </Modal>
</template>
