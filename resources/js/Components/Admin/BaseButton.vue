<script setup>
import { computed } from 'vue'
import { getButtonColor } from '@/colors.js'
import BaseIcon from '@/Components/Admin/BaseIcon.vue'
import { Link } from '@inertiajs/vue3'
import { mdiLoading } from '@mdi/js';
import { ModalLink } from '@inertiaui/modal-vue';

const props = defineProps({
  label: {
    type: [String, Number],
    default: null
  },
  labelColor: {
    type: [String, Number],
    default: 'text-white'
  },
  icon: {
    type: String,
    default: null
  },
  iconSize: {
    type: [String, Number],
    default: null
  },
  href: {
    type: String,
    default: null
  },
  target: {
    type: String,
    default: null
  },
  routeName: {
    type: [String, Object],
    default: null
  },
  processing: {
    type: Boolean,
    default: false
  },
  type: {
    type: String,
    default: null
  },
  modal: {
    type: Boolean,
    default: false
  },
  color: {
    type: String,
    default: 'white'
  },
  as: {
    type: String,
    default: null
  },
  small: Boolean,
  outline: Boolean,
  active: Boolean,
  disabled: Boolean,
  roundedFull: Boolean
})

const is = computed(() => {
  if (props.modal) {
    return ModalLink;
  }

  if (props.as) {
    return props.as
  }

  if (props.routeName) {
    return Link
  }

  if (props.href) {
    return Link
  }

  return 'button'
})

const computedType = computed(() => {
  if (is.value === 'button') {
    return props.type ?? 'button'
  }

  return null
})

const labelClass = computed(() => (props.small && props.icon ? 'px-1 ' + props.labelColor : 'px-2 ' + props.labelColor))

const componentClass = computed(() => {
  const base = [
    'inline-flex',
    'justify-center',
    'items-center',
    'whitespace-nowrap',
    'focus:outline-hidden',
    'transition-colors',
    'focus:ring-3',
    'duration-150',
    'border',
    (props.disabled || props.processing) ? 'cursor-not-allowed' : 'cursor-pointer',
    props.roundedFull ? 'rounded-full' : 'rounded-xs',
    getButtonColor(props.color, props.outline, !props.disabled, props.active)
  ]

  if (!props.label && props.icon) {
    base.push('p-1')
  } else if (props.small) {
    base.push('text-sm', props.roundedFull ? 'px-3 py-1' : 'p-1')
  } else {
    base.push('py-2', props.roundedFull ? 'px-6' : 'px-3')
  }

  if (props.disabled || props.processing) {
    base.push(props.outline ? 'opacity-50' : 'opacity-70')
  }

  return base
})
</script>

<template>
  <component
    :is="is"
    :class="componentClass"
    :href="routeName ? route(routeName) : href"
    :type="computedType"
    :target="target"
    :disabled="disabled || processing"
    v-bind="is === ModalLink ? { navigate: true } : {}"
  >
    <BaseIcon v-if="processing" :path="mdiLoading" size="1.2rem" class="animate-spin mr-0" />
    <BaseIcon v-if="icon" :path="icon" :size="iconSize" :class="props.labelColor" />
    <span v-if="label" :class="labelClass">{{ label }}</span>
  </component>
</template>
