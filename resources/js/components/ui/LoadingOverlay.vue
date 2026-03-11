<template>
    <div v-if="show" class="fixed inset-0 flex items-center justify-center bg-black/40 z-[9999]">
  
  <div class="bg-white rounded-xl px-6 py-4 shadow-lg flex flex-col items-center gap-3">
    
    <div class="animate-spin rounded-full h-12 w-12 border-4 border-gray-300 border-t-gray-800"></div>

    <span class="text-sm text-gray-700">
      Carregando...
    </span>

  </div>

</div>
</template>

<script setup>
defineProps({
    show: Boolean
})
</script>
