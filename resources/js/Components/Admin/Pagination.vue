<script setup>
import { ref, computed, watch, nextTick } from 'vue';
import { router } from '@inertiajs/vue3';
import BaseLevel from "@/Components/Admin/BaseLevel.vue";
import BaseButtons from "@/Components/Admin/BaseButtons.vue";
import BaseButton from "@/Components/Admin/BaseButton.vue";

const props = defineProps({
    pagination: {
        type: Object,
        required: true,
    },
});

const DEFAULT_CURRENT_PAGE = 1;
const DEFAULT_TOTAL_PAGES = 1;
const currentPage = ref(props.pagination.current_page || DEFAULT_CURRENT_PAGE);
const totalPages = computed(() => props.pagination.last_page || DEFAULT_TOTAL_PAGES);
const isPopupOpen = ref(false);
const inputPage = ref('');
const queryParams = ref(route().params);  // Get current query params

watch(() => props.pagination.current_page, (newPage) => {
    currentPage.value = newPage || DEFAULT_CURRENT_PAGE;
});

const openPageInputPopup = () => {
    isPopupOpen.value = true;
    inputPage.value = '';
    nextTick(() => {
        const inputField = document.getElementById("inputPageField");
        if (inputField) {
            inputField.focus();
        }
    });
};

const closePageInputPopup = () => {
    isPopupOpen.value = false;
};

const goToPage = () => {
    const page = parseInt(inputPage.value);
    if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page;
        router.get(route(route().current()), { ...queryParams.value, page }, { preserveScroll: true });
    }
    closePageInputPopup();
};

const handleKeydown = (event) => {
    if (event.key === 'Escape') closePageInputPopup();
    if (event.key === 'Enter') goToPage();
};

const isValidPage = (page) => page > 0 && page <= totalPages.value;

const changePage = (page) => {
    if (page !== currentPage.value && isValidPage(page)) {
        currentPage.value = page;
        router.get(route(route().current()), {...queryParams.value, page}, { preserveScroll: true });
    }
};

const generatePageRange = (current, total, delta = 2) => {
    let range = [];
    for (let i = Math.max(2, current - delta); i <= Math.min(total - 1, current + delta); i++) {
        range.push(i);
    }
    if (current - delta > 2) range.unshift('...');
    if (current + delta < total - 1) range.push('...');
    range.unshift(1);
    if (total > 1) range.push(total);

    return range;
};

const pagesToShow = computed(() => generatePageRange(currentPage.value, totalPages.value));

</script>

<template>
    <div class="p-3 lg:px-6 border-t border-gray-100 dark:border-slate-800">
        <BaseLevel>
            <BaseButtons>
                <BaseButton
                    :disabled="currentPage <= 1"
                    :label="'Previous'"
                    color="whiteDark"
                    @click="changePage(currentPage - 1)"
                    small
                />
                <BaseButton
                    v-for="page in pagesToShow"
                    :key="page ? page : '...'"
                    :active="page ? page === currentPage : false"
                    :label="page ? page : '...'"
                    color="whiteDark"
                    small
                    @click="page === '...' ? openPageInputPopup() : changePage(Number(page))"
                />
                <BaseButton
                    :disabled="currentPage >= totalPages"
                    :label="'Next'"
                    color="whiteDark"
                    @click="changePage(currentPage + 1)"
                    small
                />
            </BaseButtons>
            <small>Page {{ currentPage }} of {{ totalPages }}</small>
        </BaseLevel>

        <!-- Popup za unos stranice -->
        <div
            v-if="isPopupOpen"
            class="fixed inset-0 flex items-center justify-center bg-slate-800 bg-opacity-75 z-40"
            @click.self="closePageInputPopup"
        >
            <div
                class="bg-gray-900 text-white p-6 rounded-lg shadow-lg w-64"
                @keydown="handleKeydown"
                tabindex="0"
            >
                <h3 class="text-lg font-semibold mb-3">Enter Page Number</h3>
                <input
                    id="inputPageField"
                    v-model="inputPage"
                    type="number"
                    class="w-full p-2 bg-gray-700 text-white rounded border border-gray-600 focus:outline-none placeholder-slate-300 focus:border-blue-500"
                    :min="1"
                    :max="totalPages"
                    placeholder="Page number"
                />
                <div class="mt-4 flex justify-end space-x-2">
                    <button class="px-4 py-2 bg-gray-700 text-white rounded" @click="closePageInputPopup">Cancel
                    </button>
                    <button class="px-4 py-2 bg-blue-600 text-white rounded" @click="goToPage">Go</button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Dark stilovi za pop-up */
.bg-opacity-75 {
    background-color: rgba(31, 41, 55, 0.75);
}
</style>
