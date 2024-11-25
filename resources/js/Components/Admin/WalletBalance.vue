<script setup>
import { Listbox, ListboxButton, ListboxOptions, ListboxOption } from '@headlessui/vue';
import BaseIcon from "@/Components/Admin/BaseIcon.vue";
import {mdiChevronDown, mdiChevronUp, mdiCurrencyBtc, mdiEthereum} from "@mdi/js";
import BaseButton from "@/Components/Admin/BaseButton.vue";
import { usePage } from "@inertiajs/vue3";
import { ref } from "vue";
import GlowButton from "@/Components/Admin/GlowButton.vue";

const ICON_PATHS = {
    mdiCurrencyBtc,
    mdiEthereum,
};

const wallets = usePage().props.auth.user.wallets;
const selectedWallet = ref(wallets[0]);

const SELECTED_CLASS = 'bg-s5 text-white';
const UNSELECTED_CLASS = 'text-white/70';
const HOVER_CLASS = 'hover:bg-s5/40 transition flex group hover:text-white';
const BASE_ICON_CLASS_COMMON = 'flex justify-center items-center rounded-full';
</script>

<template>
    <div class="relative">
        <Listbox v-model="selectedWallet">
            <ListboxButton class="flex hover:text-white bg-s1 rounded-2xl items-center" v-slot="{ open }">
                <div class="p-2 flex group">
                    <BaseIcon :path="ICON_PATHS[selectedWallet.currency.meta.icon]" class="mr-2 flex justify-center items-center rounded-full" size="16" :style="{ backgroundColor: selectedWallet.currency.meta.color }" />
                    <p class="font-semibold">{{ selectedWallet.balanceUSD }}</p>
                    <BaseIcon :path="open ? mdiChevronUp : mdiChevronDown" class="group-hover:bg-s5 transition rounded-md ml-2" size="20" />
                </div>
                <GlowButton size="xs" class="mr-1 rounded-xl" :icon="mdiCurrencyBtc" text-color="text-[#2EF2FF]" label="Deposit" />
            </ListboxButton>
            <transition name="bounce">
                <ListboxOptions class="absolute left-0 w-full bg-s2 border-4 border-s1 rounded-xl mt-1">
                    <div class="m-4 bg-s8 rounded-xl">
                        <ListboxOption
                            v-for="wallet in wallets"
                            :key="wallet.id"
                            :value="wallet"
                            v-slot="{ active, selected }"
                            as="template"
                        >
                            <li class="first:rounded-t-xl last:rounded-b-xl px-3 py-2" :class="[HOVER_CLASS, selected ? SELECTED_CLASS : '']">
                                <div class="mr-2 flex justify-center items-center">
                                    <BaseIcon :path="ICON_PATHS[wallet.currency.meta.icon]" size="20" w="w-8" h="h-8" :class="BASE_ICON_CLASS_COMMON" :style="{ backgroundColor: wallet.currency.meta.color }" />
                                </div>
                                <div class="flex flex-col justify-center">
                                    <div class="block space-x-1 font-semibold">
                                        <span :class="{ 'text-white': selected, [UNSELECTED_CLASS]: !selected }">{{ wallet.name }}</span>
                                        <span class="text-xs text-white/50">{{ wallet.currency.code }}</span>
                                    </div>
                                    <span class="text-green-500 text-xs font-semibold">{{ wallet.balanceUSD }}</span>
                                </div>
                            </li>
                        </ListboxOption>
                    </div>
                </ListboxOptions>
            </transition>
        </Listbox>
    </div>
</template>

<style>
.bounce-enter-active {
    animation: test 0.2s;
}
.bounce-leave-active {
    animation: test 0.2s reverse;
}

@keyframes test {
    0% {
        opacity: 0;
        transform: scale(0.75, 0.5625);
        transition: opacity 333ms cubic-bezier(0.4, 0, 0.2, 1),
        transform 222ms cubic-bezier(0.4, 0, 0.2, 1) 111ms;
        transform-origin: 103.5px 0px 0px;
        visibility: hidden;
    }
    100% {
        opacity: 1;
        transform: none;
        transition: opacity 333ms cubic-bezier(0.4, 0, 0.2, 1),
        transform 222ms cubic-bezier(0.4, 0, 0.2, 1);
        transform-origin: 103.5px 0px 0px;
    }
}
</style>
