import { defineStore } from 'pinia'
import { ref, onBeforeMount } from 'vue'

export const useDarkModeStore = defineStore('darkMode', () => {
    const isEnabled = ref(false)

    function set(payload = null) {
        isEnabled.value = payload !== null ? payload : !isEnabled.value

        if (typeof document !== 'undefined') {
            document.body.classList[isEnabled.value ? 'add' : 'remove']('dark-scrollbars')
            document.documentElement.classList[isEnabled.value ? 'add' : 'remove'](
                'dark',
                'dark-scrollbars-compat'
            )
        }

        // Save the setting in localStorage
        if (typeof localStorage !== 'undefined') {
            localStorage.setItem('darkMode', isEnabled.value ? '1' : '0')
        }
    }

    onBeforeMount(() => {
        // Check localStorage for the dark mode setting
        if (typeof localStorage !== 'undefined') {
            const darkModeSetting = localStorage.getItem('darkMode')
            if (darkModeSetting !== null) {
                isEnabled.value = darkModeSetting === '1'
                if (isEnabled.value) {
                    document.body.classList.add('dark-scrollbars')
                    document.documentElement.classList.add('dark', 'dark-scrollbars-compat')
                } else {
                    document.body.classList.remove('dark-scrollbars')
                    document.documentElement.classList.remove('dark', 'dark-scrollbars-compat')
                }
            }
        }
    })

    return {
        isEnabled,
        set
    }
})
