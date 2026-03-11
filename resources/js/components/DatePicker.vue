<script setup lang="ts">
import { ref, watch } from 'vue'

const props = defineProps<{
  modelValue: string
  placeholder?: string
  disabled?: boolean
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void
}>()

const date = ref<string>(props.modelValue || '')

watch(
  () => props.modelValue,
  (value) => {
    date.value = value || ''
  }
)

watch(date, (value) => {
  emit('update:modelValue', value)
})
</script>

<template>
  <input
    type="date"
    v-model="date"
    :placeholder="placeholder"
    :disabled="disabled"
    class="border rounded px-3 py-2 w-full"
  />
</template>