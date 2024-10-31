<script setup>
import { mdiAccountKey, mdiKeyboardBackspace, mdiPlus } from '@mdi/js';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import SectionMain from '@/Components/Admin/SectionMain.vue';
import SectionTitleLineWithButton from '@/Components/Admin/SectionTitleLineWithButton.vue';
import CardBox from '@/Components/Admin/CardBox.vue';
import SubNavigation from "@/Components/Admin/SubNavigation.vue";
import roleRoutes from '@/routes/roles.js';
import FormCheckRadio from '@/Components/Admin/FormCheckRadio.vue';
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import BaseButton from "@/Components/Admin/BaseButton.vue";
import BaseButtons from "@/Components/Admin/BaseButtons.vue";
import UserProfile from "@/Components/Admin/UserProfile.vue";
import { formatNumber } from '@/utils/utils.js';

const props = defineProps({
    role: Object,
    permissions: Array,
});

// Function to group permissions by category based on the latter part of the name
const groupPermissions = (permissions) => {
    const groups = permissions.reduce((acc, permission) => {
        const parts = permission.name.split(' ');
        const category = parts[parts.length - 1]; // Take the last part of the name
        if (!acc[category]) {
            acc[category] = [];
        }
        acc[category].push(permission);
        return acc;
    }, {});

    // Sort permissions within each group by creation date
    Object.keys(groups).forEach(category => {
        groups[category].sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
    });

    return groups;
};

// Grouped permissions
const groupedPermissions = groupPermissions(props.permissions);

const form = useForm({
    role_id: props.role.id,
    permissions: props.role.permissions.map(permission => permission.id),
});

// Save original permissions for change detection
const originalPermissions = ref([...form.permissions]);

// Toggle permissions and update the form
const togglePermission = (permissionId) => {
    if (form.permissions.includes(permissionId)) {
        form.permissions = form.permissions.filter(id => id !== permissionId);
    } else {
        form.permissions.push(permissionId);
    }
};

// Computed property to check if the form is changed
const isChanged = computed(() => {
    return JSON.stringify(originalPermissions.value) !== JSON.stringify(form.permissions);
});

// Submit the form
const submitForm = () => {
    form.post(route('admin.roles.update-permissions', props.role.id), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            // Refresh originalPermissions after successful save
            originalPermissions.value = [...form.permissions];
        },
    });
};

// Displayed users
const displayedUsers = computed(() => {
    return props.role.users.slice(0, 10); // First 10 users
});

const extraUsers = computed(() => {
    return props.role.users.length > 10 ? props.role.users.length - 10 : 0; // Extra users
});
</script>

<template>
    <AdminLayout>
        <SectionMain>
            <SectionTitleLineWithButton :icon="mdiAccountKey" title="Roles & Permissions" main />

            <SubNavigation
                :routes="roleRoutes"
                buttonLabel="Create New"
                :buttonIcon="mdiPlus"
            />

            <div class="grid grid-cols-1 gap-6 mb-6 xl:grid-cols-4 mt-5">
                <CardBox class="lg:mb-0 lg:col-span-2 xl:col-span-3">
                    <div class="flex flex-col h-full lg:mb-0 lg:col-span-2 xl:col-span-3">
                        <h3 class="text-lg font-bold mb-4">Manage Permissions</h3>

                        <div class="flex flex-wrap">
                            <div v-for="(permissions, category) in groupedPermissions" :key="category"
                                 class="w-full sm:w-1/2 md:w-1/3 mb-6 px-2">
                                <h4 class="text-md font-semibold mb-2 capitalize">
                                    {{ category.charAt(0).toUpperCase() + category.slice(1) }} Permissions
                                </h4>

                                <div class="space-y-4">
                                    <div v-for="permission in permissions" :key="permission.id"
                                         class="flex items-center space-x-3">
                                        <FormCheckRadio
                                            type="switch"
                                            :name="'permission-' + permission.id"
                                            :inputValue="permission.id"
                                            :label="permission.name"
                                            :model-value="form.permissions"
                                            @change="togglePermission(permission.id)"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- BaseButtons at the bottom -->
                        <BaseButtons class="mt-auto">
                            <BaseButton
                                class="border-0"
                                color="inherit"
                                route-name="admin.roles"
                                :icon="mdiKeyboardBackspace"
                                label="Back"
                                outline
                            />
                            <BaseButton
                                :disabled="!isChanged"
                                :processing="form.processing"
                                color="info"
                                @click="submitForm"
                                label="Save"
                            />
                        </BaseButtons>
                    </div>
                </CardBox>


                <CardBox class="flex flex-col lg:mb-0 lg:col-span-2 xl:col-span-1">
                    <div>
                        <!-- Role name -->
                        <h3 class="text-lg font-bold mb-4">{{ props.role.name }}</h3>

                        <!-- Users with this role -->
                        <h4 class="text-md font-semibold mb-2">Users:</h4>
                        <ul class="list-none list-inside mb-4 space-y-2">
                            <li
                                v-for="(user, index) in displayedUsers"
                                :key="user.id"
                                class="flex items-center space-x-2"
                            >
                                <UserProfile :user="user" />
                            </li>
                            <li v-if="props.role.total_users_count > 10" class="flex items-center space-x-2">
                                <span class="text-slate-400">+{{ formatNumber(props.role.total_users_count - 10) }} more</span>
                            </li>
                        </ul>

                        <!-- Number of permissions -->
                        <h4 class="text-md font-semibold mb-2">Number of permissions:</h4>
                        <p class="mb-4">{{ props.role.permissions.length }}</p>
                    </div>
                </CardBox>
            </div>
        </SectionMain>
    </AdminLayout>
</template>
