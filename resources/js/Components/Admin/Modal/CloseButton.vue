<script setup>
import { DialogClose } from 'radix-vue'
</script>

<template>
    <DialogClose class="im-close-button group text-gray-400 hover:text-gray-500 w-[40px] h-[40px] scale-75 transition-all duration-300 items-center text-slate-400 cursor-pointer flex shrink-0 justify-center relative">
        <div class="absolute inset-0 m-auto w-[2px] h-[40%] bg-slate-500 transition-all duration-300 group-hover:top-0 group-hover:-rotate-90 -rotate-45 -top-[26%] -left-[26%]"></div>
        <div class="absolute inset-0 m-auto w-[2px] h-[40%] bg-slate-500 transition-all duration-300 group-hover:-rotate-45 rotate-45 -top-[26%] left-[26%]"></div>
        <div class="absolute inset-0 m-auto w-[2px] h-[40%] bg-slate-500 transition-all duration-300 group-hover:rotate-90 group-hover:top-0 rotate-45 top-[26%] -left-[26%]"></div>
        <div class="absolute inset-0 m-auto w-[2px] h-[40%] bg-slate-500 transition-all duration-300 group-hover:rotate-45 -rotate-45 top-[26%] left-[26%]"></div>
    </DialogClose>
</template>
