<script setup>
import { computed } from 'vue'
import { mdiChevronUp, mdiChevronDown, mdiAlertCircleOutline } from '@mdi/js'
import PillTag from '@/Components/Admin/PillTag.vue'

const props = defineProps({
  role: {
    type: String,
    required: true
  },
  small: Boolean
})

const roleStyle = computed(() => {
    if(props.role === 'admin')
    {
        return {
            style: 'danger'
        }
    }

  return {
    style: 'info'
  }
})
</script>

<template>
  <PillTag :label="role" :color="roleStyle.style" :small="small" />
</template>
