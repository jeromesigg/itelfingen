import { h } from 'vue'

// Inhalt einer Zelle, gemeinsam für die Tabelle (Desktop) und die Karten (Handy) in DataTable.vue.
// Funktionale Komponente ohne eigene Instanz: bei hunderten Zellen pro Sortierung spürbar schneller.

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

/**
 * HTML nur für Spalten mit meta.html (vom Server escaped), sonst als Text: Werte aus öffentlichen Formularen.
 * Einzige Stelle, an der die Tabelle HTML rendert (geprüft in tests/Feature/Security/XssTest.php).
 */
function DataTableCell({ cell }) {
  const value = formatCellValue(cell.getValue())
  return cell.column.columnDef.meta?.html ? h('span', { innerHTML: value }) : value
}

DataTableCell.props = { cell: { type: Object, required: true } }

export default DataTableCell
