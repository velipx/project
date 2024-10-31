<script setup>
import {computed, ref, onMounted, watch} from 'vue'
import { Deferred } from "@inertiajs/vue3";
import { useMainStore } from '@/main'
import {
    mdiAccountMultiple,
    mdiCartOutline,
    mdiChartTimelineVariant,
    mdiGithub,
} from '@mdi/js'
import * as chartConfig from '@/Components/Admin/Charts/chart.config.js'
import SectionMain from '@/Components/Admin/SectionMain.vue'
import CardBoxWidget from '@/Components/Admin/CardBoxWidget.vue'
import BaseButton from '@/Components/Admin/BaseButton.vue'
import CardBoxTransaction from '@/Components/Admin/CardBoxTransaction.vue'
import CardBoxClient from '@/Components/Admin/CardBoxClient.vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import SectionTitleLineWithButton from '@/Components/Admin/SectionTitleLineWithButton.vue'
import { Link } from '@inertiajs/vue3'

const chartData = ref(null)

const fillChartData = () => {
    chartData.value = chartConfig.sampleChartData()
}

onMounted(() => {
    fillChartData()
})

const mainStore = useMainStore()

const clientBarItems = computed(() => mainStore.clients.slice(0, 4))

const transactionBarItems = computed(() => mainStore.history)

const props = defineProps({
    permissions: Object,
})
const showLoading = ref(false);

watch(() => props.permissions, (newValue) => {
    if (!newValue) {
        showLoading.value = true;
        setTimeout(() => {
            showLoading.value = false; // Resetujemo loading nakon 2 sekunde
        }, 2000);
    }
}, { immediate: true });
</script>

<template>
    <AdminLayout>
        <SectionMain>
            <SectionTitleLineWithButton :icon="mdiChartTimelineVariant" title="Overview" main>
                <BaseButton
                    href="https://github.com/justboil/admin-one-vue-tailwind"
                    target="_blank"
                    :icon="mdiGithub"
                    label="Star on GitHub"
                    color="contrast"
                    rounded-full
                    small
                />
            </SectionTitleLineWithButton>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 mb-6">
                <CardBoxWidget
                    trend="12%"
                    trend-type="up"
                    color="text-emerald-500"
                    :icon="mdiAccountMultiple"
                    :number="512"
                    label="Clients"
                />
                <CardBoxWidget
                    trend="12%"
                    trend-type="down"
                    color="text-blue-500"
                    :icon="mdiCartOutline"
                    :number="7770"
                    prefix="$"
                    label="Sales"
                />
                <CardBoxWidget
                    trend="Overflow"
                    trend-type="alert"
                    color="text-red-500"
                    :icon="mdiChartTimelineVariant"
                    :number="256"
                    suffix="%"
                    label="Performance"
                />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="flex flex-col justify-between">
                    <CardBoxTransaction
                        v-for="(transaction, index) in transactionBarItems"
                        :key="index"
                        :amount="transaction.amount"
                        :date="transaction.date"
                        :business="transaction.business"
                        :type="transaction.type"
                        :name="transaction.name"
                        :account="transaction.account"
                    />
                </div>
                <div class="flex flex-col justify-between">
                    <CardBoxClient
                        v-for="client in clientBarItems"
                        :key="client.id"
                        :name="client.name"
                        :login="client.login"
                        :date="client.created"
                        :progress="client.progress"
                    />
                </div>
            </div>

            <Link href="/admin/test">
                Test
            </Link>

            <Deferred data="permissions">
                <template #fallback>
                    <transition name="fade">
                        <div v-if="showLoading" class="skeleton-loader">
                            <div class="skeleton-title"></div>
                            <div class="skeleton-line"></div>
                            <div class="skeleton-line short"></div>
                        </div>
                    </transition>
                </template>

                <template #default>
                    <div v-for="permission in permissions">
                        {{ permission.name }}
                    </div>
                </template>
            </Deferred>
        </SectionMain>
    </AdminLayout>
</template>

<style scoped>
/* Skeleton Styles */
.skeleton-loader {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.skeleton-title {
    height: 20px;
    width: 80%;
    background-color: #e0e0e0;
    border-radius: 4px;
    animation: pulse 1.5s infinite ease-in-out;
}

.skeleton-line {
    height: 14px;
    width: 100%;
    background-color: #e0e0e0;
    border-radius: 4px;
    animation: pulse 1.5s infinite ease-in-out;
}

.skeleton-line.short {
    width: 60%;
}

/* Pulse Animation */
@keyframes pulse {
    0% {
        background-color: #e0e0e0;
    }
    50% {
        background-color: #d6d6d6;
    }
    100% {
        background-color: #e0e0e0;
    }
}

/* Fade Animation */
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.5s ease-in-out;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>

