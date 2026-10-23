<template>
  <!-- Auf dem Handy eine Zeile zum seitlichen Scrollen statt mehrerer umbrechender Reihen -->
  <div role="group" :aria-label="label" class="-mx-4 flex gap-2 overflow-x-auto px-4 pb-1 [scrollbar-width:none] md:mx-0 md:flex-wrap md:overflow-visible md:px-0 md:pb-0">
    <button
      v-for="option in options"
      :key="option"
      type="button"
      :aria-pressed="option === model"
      :class="[
        'shrink-0 whitespace-nowrap rounded-lg border px-4 py-2 text-sm font-medium transition-colors focus:outline-hidden focus:ring-4',
        option === model
          ? `${activeClass} border-transparent text-white`
          : 'border-gray-300 bg-white text-gray-900 hover:bg-gray-100 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:hover:bg-gray-700',
      ]"
      @click="model = option"
    >
      {{ option }}
    </button>
  </div>
</template>

<script setup>
const model = defineModel({ type: String, required: true })

defineProps({
  options: { type: Array, required: true },
  // Beschriftung der Gruppe für Screenreader
  label: { type: String, required: true },
  activeClass: { type: String, default: 'bg-grannysmith focus:ring-grannysmith/50' },
})
</script>
