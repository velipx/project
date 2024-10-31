<script setup>
import { ref, computed, toRefs, watch } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import BaseButton from '@/Components/Admin/BaseButton.vue';
import TableCheckboxCell from '@/Components/Admin/TableCheckboxCell.vue';
import FormCheckRadio from '@/Components/Admin/FormCheckRadio.vue';
import Pagination from '@/Components/Admin/Pagination.vue';
import { mdiBackupRestore, mdiDelete, mdiMagnify } from '@mdi/js';
import { Menu, MenuItems, MenuButton } from '@headlessui/vue';
import FormControl from '@/Components/Admin/FormControl.vue';
import {formatDate} from "@/utils/utils.js";

// Constants
const DELETE_CONFIRMATION_ROUTE = '.remove';
const RESTORE_RESOURCE_ROUTE = '.restore';
const SHOW_DELETED_KEY = '_showDeleted';
const ACTIVE_HEADERS_KEY = '_activeHeaders';
const DEFAULT_ITEMS_PER_PAGE = 10;

// Extracting Route Parameters
const routeParams = route().params;

// Component Properties
const props = defineProps({
    headers: { type: Array, required: true },
    data: { type: Array, required: true },
    hasActions: Boolean,
    checkable: Boolean,
    pagination: Object,
    sortKey: String,
    sortOrder: String
});

const { headers, data, hasActions, checkable, pagination, sortKey, sortOrder } = toRefs(props);
const form = useForm({ search: routeParams.search });
const selectedRowIds = ref([]);
const showDeleted = ref(false);
const activeHeaders = ref(initializeActiveHeaders(headers.value));
const routeKey = route().current();
const itemsPerPage = ref(pagination.value?.per_page ?? DEFAULT_ITEMS_PER_PAGE);
const hasSelectedRows = computed(() => selectedRowIds.value.length > 0);
const allRowsSelected = computed(() => selectedRowIds.value.length === data.value.length);

// Initialize Active Headers
function initializeActiveHeaders(headers) {
    return headers.reduce((acc, header) => {
        acc[header] = true;
        return acc;
    }, {});
}

// Check if a Row is Selected
function isRowSelected(rowId) {
    return selectedRowIds.value.includes(rowId);
}

// Toggle Row Selection
function toggleRowSelection(rowId) {
    const index = selectedRowIds.value.indexOf(rowId);
    index > -1 ? selectedRowIds.value.splice(index, 1) : selectedRowIds.value.push(rowId);
}

// Toggle All Row Selections
function toggleAllRowSelection() {
    selectedRowIds.value = allRowsSelected.value ? [] : data.value.map(row => row.id);
}

// Perform Action
function performAction(path, params, action = 'visit') {
    const routeUrl = route(routeKey + path, params);
    return action === 'post' ? router.post(routeUrl) : router.visit(routeUrl);
}

// Open Delete Confirmation Modal
function openDeleteConfirmationModal() {
    if (selectedRowIds.value.length > 0) {
        performAction(DELETE_CONFIRMATION_ROUTE, { ids: selectedRowIds.value });
    }
}

// Restore Resource
function restoreResource(row) {
    performAction(RESTORE_RESOURCE_ROUTE, { id: row.id }, 'post');
}

// Handle Search
function handleSearch() {
    const searchParams = {
        search: form.search,
        sortKey: sortKey.value,
        sortOrder: sortOrder.value,
        itemsPerPage: itemsPerPage.value
    };
    router.get(route(routeKey), searchParams, { preserveScroll: true, preserveState: true });
}

// Sort Column
function sortColumn(headerKey) {
    const newOrder = sortKey.value === headerKey && sortOrder.value === 'asc' ? 'desc' : 'asc';
    const searchParams = { ...routeParams, sortKey: headerKey, sortOrder: newOrder, itemsPerPage: itemsPerPage.value };
    router.get(route(routeKey), searchParams, { preserveScroll: true });
}

// Load and Save Filters
function loadFilters() {
    showDeleted.value = JSON.parse(localStorage.getItem(`${routeKey}${SHOW_DELETED_KEY}`)) ?? false;
    Object.assign(activeHeaders.value, JSON.parse(localStorage.getItem(`${routeKey}${ACTIVE_HEADERS_KEY}`)) ?? {});
}

function saveFilters() {
    localStorage.setItem(`${routeKey}${SHOW_DELETED_KEY}`, JSON.stringify(showDeleted.value));
    localStorage.setItem(`${routeKey}${ACTIVE_HEADERS_KEY}`, JSON.stringify(activeHeaders.value));
}

loadFilters();
watch(showDeleted, saveFilters);
watch([itemsPerPage, () => form.search], handleSearch);
watch(activeHeaders, saveFilters, { deep: true });

const filteredData = computed(() => showDeleted.value ? data.value : data.value.filter(row => !row.deleted_at));

</script>

<template>
    <div>
        <div class="mb-4 flex justify-between items-center pt-5 pr-5 pl-5 space-x-5">
            <div class="flex">

                <FormControl
                    id="itemsPerPage"
                    v-model="itemsPerPage"
                    :options="[5, 10, 25, 50]"
                    type="select"
                    size="small"
                    class="w-20"
                />
                <BaseButton
                    v-if="hasSelectedRows"
                    @click="openDeleteConfirmationModal"
                    small
                    label="Delete all"
                    :icon="mdiDelete"
                    color="danger"
                    class="bg-red-500 text-white rounded ml-2 px-2"
                />
            </div>
            <div class="flex items-center justify-end">
                <FormControl
                    v-model="form.search"
                    @keyup.enter="handleSearch"
                    :model-value="form.search"
                    placeholder="Search..."
                    size="small"
                    :icon="mdiMagnify"
                />
                <Menu as="div" class="relative">
                    <MenuButton class="bg-blue-500 text-white rounded p-2 -ml-2">
                        <svg v-bind="$attrs" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                             class="w-6 h-6">
                            <path fill-rule="evenodd"
                                  d="M5 7a1 1 0 100 2h14a1 1 0 100-2H5zm-2 7a1 1 0 100 2h14a1 1 0 100-2H3zm-2 4h10a1 1 0 100-2H1a1 1 0 100 2zm0-8a1 1 0 100 2h18a1 1 0 100-2H1z"
                                  clip-rule="evenodd"/>
                        </svg>
                    </MenuButton>
                    <transition
                        enter-active-class="transition duration-100 ease-out"
                        enter-from-class="transform scale-95 opacity-0"
                        enter-to-class="transform scale-100 opacity-100"
                        leave-active-class="transition duration-75 ease-in"
                        leave-from-class="transform scale-100 opacity-100"
                        leave-to-class="transform scale-95 opacity-0"
                    >
                        <MenuItems
                            class="absolute right-0 z-10 mt-2 w-96 origin-top-right divide-y divide-gray-700 rounded-md bg-gray-900 shadow-lg ring-1 ring-black/5 focus:outline-none">
                            <div class="px-4 py-3 space-y-2">
                                <h3 class="font-semibold text-lg mb-2 text-white">Filter Options</h3>
                                <div class="grid grid-cols-2 gap-x-4 gap-y-2">
                                    <div v-for="header in headers" :key="header"
                                         class="flex items-start transform scale-90">
                                        <FormCheckRadio
                                            :label="header"
                                            :name="header"
                                            :inputValue="header"
                                            type="switch"
                                            :modelValue="activeHeaders[header]"
                                            @change="activeHeaders[header] = !activeHeaders[header]"
                                        />
                                    </div>
                                    <FormCheckRadio
                                        label="Show Deleted"
                                        name="showDeleted"
                                        inputValue="showDeleted"
                                        type="switch"
                                        :modelValue="showDeleted"
                                        @change="showDeleted = !showDeleted"
                                        class="transform scale-90"
                                    />
                                </div>
                            </div>
                        </MenuItems>
                    </transition>
                </Menu>
            </div>
        </div>
        <table class="min-w-full">
            <thead>
            <tr>
                <th v-if="checkable">
                    <TableCheckboxCell
                        v-model="allRowsSelected"
                        class="p-0 flex"
                        @change="toggleAllRowSelection"
                    />
                </th>
                <th
                    v-for="header in headers"
                    :key="header"
                    v-show="activeHeaders[header]"
                    :class="(header === 'id') ? 'w-2' : ''"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 cursor-pointer"
                    @click="sortColumn(header)"
                >
                    <span>{{ header }}</span>
                    <span v-if="sortKey === header">
                        {{ sortOrder === 'asc' ? ' ▲' : ' ▼' }}
                    </span>
                </th>
                <th v-if="hasActions" class="w-2" />
            </tr>
            </thead>
            <tbody>
            <tr v-for="row in filteredData" :key="row.id"
                @dblclick="() => router.visit(route(routeKey + '.show', row))">
                <TableCheckboxCell
                    v-if="checkable"
                    :checked="isRowSelected(row.id)"
                    :id="row.id"
                    @change="() => toggleRowSelection(row.id)"
                />
                <td
                    v-for="header in headers"
                    :key="header"
                    :data-label="header"
                    v-show="activeHeaders[header]"
                    class="px-6 py-4 whitespace-nowrap"
                    :class="{ 'opacity-50 text-slate-400 cursor-not-allowed': row.deleted_at }"
                >
                    <slot :name="'cell-' + header" :row="row">
                        <span v-if="['created_at', 'updated_at'].includes(header)">
                            {{ formatDate(row[header]) }}
                        </span>
                        <span v-else-if="['id'].includes(header)" class="text-slate-400 text-sm">
                            #{{ row[header] }}
                        </span>
                        <span v-else>
                            {{ row[header] }}
                        </span>
                    </slot>
                </td>
                <td v-if="hasActions" class="px-6 py-2 w-auto whitespace-nowrap text-sm font-medium flex justify-end">
                    <BaseButton
                        v-if="row.deleted_at"
                        color="inherit"
                        outline
                        :icon="mdiBackupRestore"
                        @click="() => restoreResource(row)"
                        class="mr-2 text-red-500 border-0 hover:underline opacity-100"
                    />
                    <slot name="actions" :row="row"/>
                </td>
            </tr>
            </tbody>
        </table>
        <Pagination :pagination="pagination"/>
    </div>
</template>
