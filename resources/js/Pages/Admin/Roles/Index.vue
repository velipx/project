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
import { formatNumber } from '@/utils/utils.js';

const props = defineProps({
    roles: Object,
    sortKey: String,
    sortOrder: String,
});

const navigateTo = (routeName, params = {}) => {
    router.visit(route(routeName, params));
};

const openCreateRoleModal = () => navigateTo('admin.roles.create');
const viewRole = (role) => navigateTo('admin.roles.show', role);
const openDeleteConfirmationModal = (role) => navigateTo('admin.roles.remove', { ids: [role.id] });

</script>
<template>
    <AdminLayout>
        <SectionMain>
            <SectionTitleLineWithButton :icon="mdiAccountKey" title="Roles & Permissions" main />
            <SubNavigation
                :routes="roleRoutes"
                buttonLabel="Create New"
                :buttonIcon="mdiPlus"
                :onButtonClick="openCreateRoleModal"
            />
            <CardBox class="my-5" has-table>
                <Table
                    :headers="['id', 'name', 'guard_name', 'users', 'created_at']"
                    :data="roles.data"
                    :hasActions="true"
                    :checkable="true"
                    :pagination="roles"
                    :sortKey="sortKey"
                    :sortOrder="sortOrder"
                >
                    <template #cell-users="{ row }">
                        <div class="flex space-x-1">
                            <div class="-space-x-2 flex">
                                <UserAvatar
                                    v-for="(user, index) in row.users.slice(0, 5)"
                                    :key="user.id"
                                    :username="user.username"
                                    :avatar="user.avatar_url"
                                    class=" -space-x-2"
                                    border
                                />
                            </div>

                            <div v-if="row.total_users_count > 5"
                                 class="flex items-center justify-center text-xs text-slate-200 font-semibold">
                                +{{ formatNumber(row.total_users_count - 5) }}
                            </div>
                        </div>
                    </template>
                    <template #actions="{ row }">
                        <BaseButtons type="justify-start lg:justify-end" no-wrap>
                            <BaseButton color="info" :icon="mdiEye" small @click="() => viewRole(row)" />
                            <BaseButton color="danger" :icon="mdiTrashCan" small
                                        @click="() => openDeleteConfirmationModal(row)" />
                        </BaseButtons>
                    </template>
                </Table>
            </CardBox>
        </SectionMain>
    </AdminLayout>
</template>
