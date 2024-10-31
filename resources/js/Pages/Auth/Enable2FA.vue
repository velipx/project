<template>
    <div>
        <h2>Enable Two-Factor Authentication</h2>
        <div v-if="qrImage">
            <p>Scan the QR code with your authenticator app and enter the code below:</p>
            <img :src="qrImage" alt="QR Code" />
            <form @submit.prevent="verifyCode">
                <input v-model="verificationCode" type="text" placeholder="Authenticator Code" required />
                <button type="submit">Verify & Enable</button>
            </form>
        </div>
        <button @click="enable2fa">Generate QR Code</button>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'

const qrImage = ref(null)
const verificationCode = ref('')
const form = useForm({ verify_code: '' })

const enable2fa = async () => {
    try {
        const response = await form.post(route('2fa.enable'))
        qrImage.value = response.data.QR_Image
    } catch (error) {
        console.error(error)
    }
}

const verifyCode = async () => {
    form.verify_code = verificationCode.value
    try {
        await form.post(route('2fa.verify'))
    } catch (error) {
        console.error(error)
    }
}
</script>
