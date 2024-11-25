<script setup>
import CloseButton from './CloseButton.vue'
import { DialogContent, DialogTitle, VisuallyHidden } from 'radix-vue'

defineProps({
    modalContext: Object,
    config: Object,
})
</script>

<template>
    <!-- Full-screen scrollable container -->
    <div class="im-modal-container fixed inset-0 z-40 overflow-y-auto p-0 sm:p-4">
        <!-- Container to center the panel -->
        <div
            class="im-modal-positioner flex min-h-full justify-center"
            :class="{
                'items-start': config.position === 'top',
                'items-center -mt-20': config.position === 'center',
                'items-end': config.position === 'bottom',
            }"
        >
            <Transition
                appear
                enter-from-class="translate-y-[907px] duration-[500ms]"
                enter-to-class="translate-y-[-30px] duration-[200ms]"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 translate-x-[1000px] duration-[200ms]"
                @after-leave="modalContext.afterLeave"
            >
                <DialogContent
                    :aria-describedby="undefined"
                    :class="{
                        'im-modal-wrapper w-full transition duration-300 ease-in-out min-h-screen absolute top-0 sm:relative sm:min-h-full': true,
                        'blur-xs': !modalContext.onTopOfStack,
                        'sm:max-w-sm': config.maxWidth == 'sm',
                        'sm:max-w-md': config.maxWidth == 'md',
                        'sm:max-w-md md:max-w-lg': config.maxWidth == 'lg',
                        'sm:max-w-md md:max-w-xl': config.maxWidth == 'xl',
                        'sm:max-w-md md:max-w-xl lg:max-w-2xl': config.maxWidth == '2xl',
                        'sm:max-w-md md:max-w-xl lg:max-w-3xl': config.maxWidth == '3xl',
                        'sm:max-w-md md:max-w-xl lg:max-w-3xl xl:max-w-4xl': config.maxWidth == '4xl',
                        'sm:max-w-md md:max-w-xl lg:max-w-3xl xl:max-w-5xl': config.maxWidth == '5xl',
                        'sm:max-w-md md:max-w-xl lg:max-w-3xl xl:max-w-5xl 2xl:max-w-6xl': config.maxWidth == '6xl',
                        'sm:max-w-md md:max-w-xl lg:max-w-3xl xl:max-w-5xl 2xl:max-w-7xl': config.maxWidth == '7xl',
                    }"
                    @escape-key-down="($event) => config?.closeExplicitly && $event.preventDefault()"
                    @interact-outside="($event) => config?.closeExplicitly && $event.preventDefault()"
                >
                    <VisuallyHidden as-child>
                        <DialogTitle />
                    </VisuallyHidden>

                    <div
                        class="im-modal-content relative bg-s5 border-4 dark:border-s1 rounded-2xl min-h-screen sm:min-h-full"
                        :class="[config.paddingClasses, config.panelClasses]"
                    >
                        <div
                            v-if="config.closeButton"
                            class="absolute right-0 top-0 pr-3 pt-3"
                        >
                            <CloseButton />
                        </div>

                        <slot
                            :modal-context="modalContext"
                            :config="config"
                        />
                    </div>
                </DialogContent>
            </Transition>
        </div>
    </div>
</template>
