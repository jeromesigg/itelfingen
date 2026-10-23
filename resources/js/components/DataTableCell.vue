<template>
  <!-- HTML nur für Spalten mit meta.html (vom Server escaped), sonst als Text: Werte aus öffentlichen Formularen -->
  <span v-if="cell.column.columnDef.meta?.html" v-html="formatCellValue(cell.getValue())"></span>
  <template v-else>{{ formatCellValue(cell.getValue()) }}</template>
</template>

<script setup>
// Inhalt einer Zelle, gemeinsam für die Tabelle (Desktop) und die Karten (Handy) in DataTable.vue
defineProps({
  cell: { type: Object, required: true },
})

function formatCellValue(value) {
  // Werte mit Sortierschlüssel wie { display, sort }
  if (typeof value === 'object' && value !== null && value.display) {
    return value.display
  }

  if (typeof value === 'object' && value !== null) {
    return JSON.stringify(value)
  }

  return String(value || '')
}
</script>
