<template>
    <div>
        <div v-if="!is2FAEnabled">
            <button @click="enable2FA">Enable 2FA</button>
        </div>
        <div v-if="is2FAEnabled && !is2FAConfirmed">
            <img :src="qrCode" alt="QR Code" />
            <input type="text" v-model="code" placeholder="Enter code" />
            <button @click="confirm2FA">Confirm 2FA</button>
        </div>
        <div v-if="is2FAEnabled && is2FAConfirmed">
            <p>2FA is enabled</p>
            <button @click="disable2FA">Disable 2FA</button>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const is2FAEnabled = ref(false);
const is2FAConfirmed = ref(false);
const qrCode = ref('');
const code = ref('');

const enable2FA = async () => {
    try {
        const response = await axios.post('/two-factor-authentication/enable');
        qrCode.value = response.data.qrcode;
        is2FAEnabled.value = true;
    } catch (error) {
        console.error(error);
    }
};

const confirm2FA = async () => {
    try {
        await axios.post('/two-factor-authentication/confirm', { code: code.value });
        is2FAConfirmed.value = true;
    } catch (error) {
        console.error(error);
    }
};

const disable2FA = async () => {
    try {
        await axios.post('/two-factor-authentication/disable');
        is2FAEnabled.value = false;
        is2FAConfirmed.value = false;
    } catch (error) {
        console.error(error);
    }
};
</script>
