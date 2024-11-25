<script setup>
import {
    mdiAccount,
    mdiTablet,
    mdiCellphone,
    mdiMonitor,
    mdiFormTextboxPassword, mdiAsterisk
} from '@mdi/js';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import SectionMain from '@/Components/Admin/SectionMain.vue';
import CardBox from '@/Components/Admin/CardBox.vue';
import SectionTitleLineWithButton from "@/Components/Admin/SectionTitleLineWithButton.vue";
import BaseDivider from "@/Components/Admin/BaseDivider.vue";
import BaseButton from "@/Components/Admin/BaseButton.vue";
import FormControl from "@/Components/Admin/FormControl.vue";
import FormField from "@/Components/Admin/FormField.vue";
import {useForm} from "@inertiajs/vue3";
import BaseIcon from "@/Components/Admin/BaseIcon.vue";
import {clearFormFieldError, formatDate} from "@/utils/utils.js";

const props = defineProps({
    user: Object,
    passwordUpdates: Array,
});

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const deviceIcon = (device) => {
    switch (device) {
        case 'desktop':
            return mdiMonitor;
        case 'tablet':
            return mdiTablet;
        case 'mobile':
            return mdiCellphone;
        default:
            return mdiMonitor;
    }
};

const changePassword = async () => {
    form.post(route('admin.users.update-password', props.user), {
        preserveState: true,

        onSuccess: () => {
            form.reset();
        }
    });
}
</script>

<template>
    <AdminLayout>
        <SectionMain>
            <SectionTitleLineWithButton :icon="mdiAccount" title="Profile" main />

            <div class="grid grid-cols-1 gap-6 xl:grid-cols-2 mt-5">
                <CardBox is-form @submit.prevent="changePassword">
                    <FormField
                        label="Current password"
                        help="Required. Your current password"
                        :error="form.errors.current_password"
                        class="opacity-30 cursor-not-allowed"
                    >
                        <div>
                            <FormControl
                                v-model="form.current_password"
                                :icon="mdiAsterisk"
                                name="password_current"
                                type="password"
                                disabled
                                autocomplete="current-password"
                                @input="clearFormFieldError(form, 'current_password')"
                            />
                        </div>
                    </FormField>

                    <BaseDivider />

                    <FormField
                        label="New password"
                        help="Required. New password"
                        :error="form.errors.password"
                    >
                        <div>
                            <FormControl
                                v-model="form.password"
                                :icon="mdiFormTextboxPassword"
                                name="password"
                                type="password"
                                required
                                autocomplete="new-password"
                                @input="clearFormFieldError(form, 'password')"
                            />
                        </div>
                    </FormField>

                    <FormField
                        label="Confirm password"
                        help="Required. New password one more time"
                    >
                        <div>
                            <FormControl
                                v-model="form.password_confirmation"
                                :icon="mdiFormTextboxPassword"
                                name="password_confirmation"
                                type="password"
                                required
                                autocomplete="new-password"
                            />
                        </div>
                    </FormField>

                    <template #footer>
                        <BaseButton type="submit" color="info" label="Submit" :processing="form.processing" />
                    </template>
                </CardBox>

                <div>
                    <CardBox>
                        <h1 class="text-lg text-gray-400 dark:text-slate-200 -mt-2 pb-0 font-medium">
                            Password changes
                        </h1>
                        <div>
                            <BaseDivider class="mt-4" />
                            <div v-if="passwordUpdates.length === 0">
                                <p class="text-slate-400">There are no password changes</p>
                            </div>
                            <div v-else>
                                <div v-for="(update, index) in passwordUpdates" :key="update.id">
                                    <div class="flex items-center justify-between rounded-lg shadow-2xs">
                                        <div class="flex items-center space-x-3">
                                            <BaseIcon class="text-slate-400" w="10" :path="deviceIcon(update.device)" size="42" />
                                            <div>
                                                <p class="text-slate-200 font-medium">{{ update.platform }} - {{ update.browser }}</p>
                                                <p class="text-sm">
                                                    {{ update.ip_address }},
                                                    <span class="text-slate-200">
                                                        {{ formatDate(update.created_at) }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <BaseDivider v-if="index < passwordUpdates.length - 1" />
                                </div>
                            </div>
                        </div>
                    </CardBox>
                </div>
            </div>
        </SectionMain>
    </AdminLayout>
</template>
