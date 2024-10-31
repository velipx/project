<template>
    <div>
        <h2>Disable Two-Factor Authentication</h2>
        <button @click="disable2fa">Disable 2FA</button>
    </div>
</template>

<script setup>
import { Inertia } from '@inertiajs/vue3';

const disable2fa = async () => {
    try {
        await axios.post(route('disable-2fa'));
        Inertia.visit(route('dashboard'));
    } catch (error) {
        console.error(error);
    }
};
</script>
