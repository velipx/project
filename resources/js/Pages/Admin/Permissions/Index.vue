<script setup>
import { router } from '@inertiajs/vue3';
import { mdiAccountKey, mdiPlus, mdiEye, mdiTrashCan } from '@mdi/js';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import SectionMain from '@/Components/Admin/SectionMain.vue';
import SectionTitleLineWithButton from '@/Components/Admin/SectionTitleLineWithButton.vue';
import CardBox from '@/Components/Admin/CardBox.vue';
import BaseButton from "@/Components/Admin/BaseButton.vue";
import BaseButtons from '@/Components/Admin/BaseButtons.vue';
import UserAvatar from '@/Components/Admin/UserAvatar.vue';
import Table from '@/Components/Admin/Table.vue';
import SubNavigation from "@/Components/Admin/SubNavigation.vue";
import roleRoutes from '@/routes/roles.js';

const props = defineProps({
    permissions: Object,
    sortKey: String,
    sortOrder: String,
});

const openCreatePermissionsModal = () => {
    router.visit(route('admin.permissions.create'));
};

const openDeleteConfirmationModal = (permission) => {
    router.visit(route('admin.permissions.remove', {
        ids: [permission.id]
    }));
};
</script>

<template>
    <AdminLayout>
        <SectionMain>
            <SectionTitleLineWithButton :icon="mdiAccountKey" title="Roles & Permissions" main />

            <SubNavigation
                :routes="roleRoutes"
                buttonLabel="Create New"
                :buttonIcon="mdiPlus"
                :onButtonClick="openCreatePermissionsModal"
            />

            <CardBox class="my-5" has-table>
                <Table
                    :headers="['id', 'name', 'guard_name', 'created_at']"
                    :data="permissions.data"
                    :hasActions="true"
                    :checkable="true"
                    :pagination="permissions"
                    :sortKey="sortKey"
                    :sortOrder="sortOrder"
                >
                    <template #actions="{ row }">
                        <BaseButtons type="justify-start lg:justify-end" no-wrap>
                            <BaseButton color="danger" :icon="mdiTrashCan" small @click="() => openDeleteConfirmationModal(row)" />
                        </BaseButtons>
                    </template>
                </Table>
            </CardBox>
        </SectionMain>
    </AdminLayout>
</template>
