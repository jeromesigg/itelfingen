<template>
  <!-- Aktionen kommen als Daten ({ label, url, variant, confirm }), DataTable.vue führt sie per fetch aus -->
  <div class="flex flex-wrap gap-2">
    <button
      v-for="action in actions"
      :key="action.url"
      type="button"
      :disabled="busyAction === action.url"
      :class="[
        'rounded-lg px-3 py-1.5 text-sm font-medium text-white focus:outline-hidden focus:ring-4 disabled:cursor-wait disabled:opacity-60',
        action.variant === 'danger' ? 'bg-red-700 hover:bg-red-800 focus:ring-red-300' : 'bg-grannysmith hover:opacity-90 focus:ring-grannysmith/50',
        { 'flex-1 py-2.5': block },
      ]"
      @click="emit('run', action)"
    >
      {{ action.label }}
    </button>
  </div>
</template>

<script setup>
defineProps({
  actions: { type: Array, default: () => [] },
  busyAction: { type: String, default: null },
  // Auf dem Handy: Buttons über die ganze Breite der Karte
  block: { type: Boolean, default: false },
})

const emit = defineEmits(['run'])
</script>
