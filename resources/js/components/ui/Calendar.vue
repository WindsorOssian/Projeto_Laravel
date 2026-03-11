<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  modelValue: String
})

const emit = defineEmits(['update:modelValue'])

const today = new Date()
const current = ref(props.modelValue ? new Date(props.modelValue) : new Date())

const year = computed(() => current.value.getFullYear())
const month = computed(() => current.value.getMonth())

const daysInMonth = computed(() =>
  new Date(year.value, month.value + 1, 0).getDate()
)

const firstDayOfWeek = computed(() =>
  new Date(year.value, month.value, 1).getDay()
)

function selectDay(day) {
  const date = new Date(year.value, month.value, day)
  emit('update:modelValue', date.toISOString().slice(0, 10))
}

function prevMonth() {
  current.value = new Date(year.value, month.value - 1, 1)
}

function nextMonth() {
  current.value = new Date(year.value, month.value + 1, 1)
}
</script>

<template>
  <div class="p-4 w-72">
    <div class="flex justify-between mb-2">
      <button @click="prevMonth">‹</button>
      <span>{{ month + 1 }}/{{ year }}</span>
      <button @click="nextMonth">›</button>
    </div>

    <div class="grid grid-cols-7 gap-1 text-center">
      <div v-for="n in firstDayOfWeek" :key="'empty'+n"></div>

      <button
        v-for="day in daysInMonth"
        :key="day"
        @click="selectDay(day)"
        class="hover:bg-gray-200 rounded p-1"
      >
        {{ day }}
      </button>
    </div>
  </div>
</template>