<script setup>
import { router } from '@inertiajs/vue3';
import {mdiAccount, mdiPlus, mdiEye, mdiTrashCan, mdiAccountMultiple} from '@mdi/js';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import SectionMain from '@/Components/Admin/SectionMain.vue';
import SectionTitleLineWithButton from '@/Components/Admin/SectionTitleLineWithButton.vue';
import CardBox from '@/Components/Admin/CardBox.vue';
import BaseButton from "@/Components/Admin/BaseButton.vue";
import BaseButtons from '@/Components/Admin/BaseButtons.vue';
import UserAvatar from '@/Components/Admin/UserAvatar.vue';
import Table from '@/Components/Admin/Table.vue';
import SubNavigation from "@/Components/Admin/SubNavigation.vue";
import UserProfile from "@/Components/Admin/UserProfile.vue";

const props = defineProps({
    users: Object,
    sortKey: String,
    sortOrder: String,
});

const navigateTo = (routeName, params = {}) => {
    router.visit(route(routeName, params));
};

const openCreateUserModal = () => navigateTo('admin.users.create');
const viewUser = (user) => navigateTo('admin.users.show', user);
const openDeleteConfirmationModal = (user) => navigateTo('admin.users.remove', { ids: [user.id] });

</script>
<template>
    <AdminLayout>
        <SectionMain>
            <SectionTitleLineWithButton :icon="mdiAccountMultiple" title="Users" main />
            <SubNavigation
                buttonLabel="Create New"
                :buttonIcon="mdiPlus"
                :onButtonClick="openCreateUserModal"
                :routes="[]"/>
            <CardBox class="my-5" has-table>
                <Table
                    :headers="['id', 'name', 'username', 'email', 'created_at']"
                    :data="users.data"
                    :hasActions="true"
                    :checkable="true"
                    :pagination="users"
                    :sortKey="sortKey"
                    :sortOrder="sortOrder"
                >
                    <template #cell-name="{ row }">
                        <UserProfile :user="row" />
                    </template>
                    <template #actions="{ row }">
                        <BaseButtons type="justify-start lg:justify-end" no-wrap>
                            <BaseButton color="info" :icon="mdiEye" small @click="() => viewUser(row)" />
                            <BaseButton color="danger" :icon="mdiTrashCan" small
                                        @click="() => openDeleteConfirmationModal(row)" />
                        </BaseButtons>
                    </template>
                </Table>
            </CardBox>
        </SectionMain>
    </AdminLayout>
</template>
