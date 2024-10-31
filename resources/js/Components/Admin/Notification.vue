<template>
    <div :class="[typeClass]">
        {{ title }}
    </div>
</template>

<script>
export default {
    props: {
        type: {
            type: String,
            required: true
        },
        title: {
            type: String,
            required: true
        }
    },
    computed: {
        typeClass() {
            switch (this.type) {
                case 'success':
                    return 'toast-success';
                case 'error':
                    return 'toast-error';
                default:
                    return 'toast-default';
            }
        }
    }
}
</script>

<style scoped>
.toast-default {
    background-color: #f0f0f0; /* Primer */
}

.toast-success {
    background-color: #4CAF50; /* Zeleni */
}

.toast-error {
    background-color: #F44336; /* Crveni */
}
</style>
