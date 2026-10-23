<template>
  <!-- Rahmen erst ab Tablet; auf dem Handy stehen die Karten direkt auf der Seite -->
  <div class="relative md:bg-neutral-primary-soft md:shadow-xs md:rounded-lg md:border md:border-default-medium md:dark:border-gray-600">
        <!-- Filter Slots (optional) -->
    <div v-if="$slots.filters" class="pt-4 md:m-4 md:pt-0">
      <slot name="filters"></slot>
    </div>

    <!-- Search Input -->
    <div class="py-4 md:p-4">
        <label :for="searchId" class="sr-only">Suchen</label>
        <div class="relative">
            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                <svg class="w-4 h-4 text-body" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg>
            </div>
                  <input
        :id="searchId"
        v-model="globalFilter"
        type="text" 
        placeholder="Suchen..." 
        class="block w-full max-w-96 ps-9 pe-3 py-2 text-heading text-sm border border-default-medium border-gray-300 rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 px-3 py-2.5 shadow-xs placeholder:text-body"
      />
        </div>
    </div>

    <!-- Sortieren auf dem Handy: dort fehlen die Spaltenköpfe -->
    <div v-if="sortableColumns.length" class="pb-4 md:hidden">
      <label :for="sortId" class="sr-only">Sortieren</label>
      <select
        :id="sortId"
        :value="sortValue"
        class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
        @change="setSortValue($event.target.value)"
      >
        <option value="">Sortierung: Standard</option>
        <template v-for="column in sortableColumns" :key="column.id">
          <option :value="column.id + ':asc'">{{ column.columnDef.header }} ↑</option>
          <option :value="column.id + ':desc'">{{ column.columnDef.header }} ↓</option>
        </template>
      </select>
    </div>

    <!-- Table (ab Tablet) -->
    <div class="hidden overflow-x-auto md:block">
      <table class="w-full text-left rtl:text-right text-body">
        <thead class="text-body bg-neutral-secondary-medium border-b border-t border-default-medium">
          <tr>
            <th 
              v-for="header in table.getHeaderGroups()[0]?.headers"
              :key="header.id"
              scope="col"
              :tabindex="header.column.getCanSort() ? 0 : undefined"
              :aria-sort="ariaSort(header)"
              @click="toggleSort(header)"
              @keydown.enter.prevent="toggleSort(header)"
              @keydown.space.prevent="toggleSort(header)"
              :style="{ 'max-width': header.getSize() + 'px' }"
              :class="header.column.getCanSort() ? 'cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600' : ''"
              class="px-4 py-3 font-medium select-none"
            >
            <div class="flex items-center gap-2">
                {{ header.column.columnDef.header }}
                <span v-if="header.column.getCanSort()">
                  {{ header.column.getIsSorted() === 'asc' ? '↑' : header.column.getIsSorted() === 'desc' ? '↓' : '↕' }}
                </span>
              </div>
            </th>
          </tr>
        </thead>
        <tbody id="tableBody" :aria-busy="isLoading">
          <!-- Erstes Laden: Platzhalterzeilen statt leerer Tabelle -->
          <template v-if="isLoading && !tableData.length">
            <tr v-for="n in 5" :key="'skeleton-' + n" class="border-b border-default">
              <td v-for="column in columns" :key="column.accessorKey" class="px-4 py-3 border border-gray-300 dark:border-gray-600">
                <div class="h-3 animate-pulse rounded bg-gray-200 dark:bg-gray-700"></div>
              </td>
            </tr>
          </template>
          <tr v-else-if="loadError">
            <td :colspan="columns.length" class="px-4 py-6 text-center">
              Die Daten konnten nicht geladen werden.
              <button type="button" class="ms-2 font-medium text-orientalpink hover:underline" @click="loadData">Erneut laden</button>
            </td>
          </tr>
          <tr v-else-if="!table.getRowModel().rows.length">
            <td :colspan="columns.length" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">Keine Einträge gefunden.</td>
          </tr>
          <template v-else>
          <tr
            v-for="row in table.getRowModel().rows"
            :key="row.id"
            class="bg-neutral-primary border-b border-default hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
          >
            <td
              v-for="cell in row.getVisibleCells()"
              :key="cell.id"
              :style="{ 'max-width': cell.column.getSize() + 'px' }"
              class="px-4 py-2 border border-gray-300 dark:border-gray-600"
            >
              <DataTableActions
                v-if="cell.column.columnDef.meta?.actions"
                :actions="cell.getValue() ?? []"
                :busy-action="busyAction"
                @run="runAction(row.original, $event)"
              />
              <DataTableCell v-else :cell="cell" />
            </td>
          </tr>
          </template>
        </tbody>
      </table>
    </div>

    <!-- Karten (Handy). Nach der Tabelle, damit auf dem Desktop zuerst die sichtbaren Buttons im DOM stehen -->
    <div class="space-y-3 md:hidden" :aria-busy="isLoading" data-testid="cards">
      <template v-if="isLoading && !tableData.length">
        <div v-for="n in 3" :key="'card-skeleton-' + n" class="space-y-2 rounded-lg border border-gray-200 p-4 dark:border-gray-700">
          <div class="h-3 w-1/3 animate-pulse rounded bg-gray-200 dark:bg-gray-700"></div>
          <div class="h-4 w-2/3 animate-pulse rounded bg-gray-200 dark:bg-gray-700"></div>
        </div>
      </template>
      <p v-else-if="loadError" class="py-6 text-center">
        Die Daten konnten nicht geladen werden.
        <button type="button" class="ms-2 font-medium text-orientalpink hover:underline" @click="loadData">Erneut laden</button>
      </p>
      <p v-else-if="!table.getRowModel().rows.length" class="py-6 text-center text-gray-500 dark:text-gray-400">Keine Einträge gefunden.</p>
      <template v-else>
        <article
          v-for="row in table.getRowModel().rows"
          :key="'card-' + row.id"
          class="rounded-lg border border-gray-200 bg-white p-4 shadow-xs dark:border-gray-700 dark:bg-gray-800"
        >
          <div v-if="mobileLayout.kicker || mobileLayout.badge" class="flex items-start justify-between gap-2">
            <span v-if="mobileLayout.kicker" class="text-xs font-medium text-gray-500 dark:text-gray-400" data-card="kicker">
              <DataTableCell :cell="cellOf(row, mobileLayout.kicker)" />
            </span>
            <span v-if="mobileLayout.badge && row.original[mobileLayout.badge]" :class="['ms-auto shrink-0 rounded px-1.5 py-0.5 text-xs font-medium', badgeClass(row.original[mobileLayout.badge])]" data-card="badge">
              {{ row.original[mobileLayout.badge] }}
            </span>
          </div>
          <div class="mt-1 font-semibold text-gray-900 dark:text-white" data-card="title">
            <DataTableCell :cell="cellOf(row, mobileLayout.title)" />
          </div>
          <template v-for="columnId in mobileLayout.lines" :key="columnId">
            <div v-if="hasValue(row, columnId)" class="line-clamp-2 text-sm text-gray-600 dark:text-gray-300" data-card="line">
              <DataTableCell :cell="cellOf(row, columnId)" />
            </div>
          </template>
          <ul v-if="mobileLayout.checks && row.original[mobileLayout.checks]?.length" class="mt-2 flex flex-wrap gap-x-3 gap-y-1 text-xs" data-card="checks">
            <li
              v-for="check in row.original[mobileLayout.checks]"
              :key="check.label"
              :class="check.done ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400'"
            >
              <span aria-hidden="true">{{ check.done ? '✓' : '✗' }}</span>
              <span class="sr-only">{{ check.done ? 'erledigt:' : 'offen:' }}</span>
              {{ check.label }}
            </li>
          </ul>
          <details v-if="mobileLayout.details.some((columnId) => hasValue(row, columnId))" class="mt-2 text-sm">
            <summary class="cursor-pointer font-medium text-orientalpink">Mehr</summary>
            <dl class="mt-2 space-y-2">
              <template v-for="columnId in mobileLayout.details" :key="columnId">
                <div v-if="hasValue(row, columnId)">
                  <dt class="text-xs text-gray-500 dark:text-gray-400">{{ table.getColumn(columnId)?.columnDef.header }}</dt>
                  <dd class="break-words text-gray-900 dark:text-white"><DataTableCell :cell="cellOf(row, columnId)" /></dd>
                </div>
              </template>
            </dl>
          </details>
          <DataTableActions
            v-if="actionsColumnId && row.original[actionsColumnId]?.length"
            class="mt-3"
            block
            :actions="row.original[actionsColumnId]"
            :busy-action="busyAction"
            @run="runAction(row.original, $event)"
          />
        </article>
      </template>
    </div>

    <div aria-live="polite" class="pointer-events-none fixed bottom-20 end-5 z-60 md:bottom-5">
      <Transition enter-active-class="transition-opacity duration-200" enter-from-class="opacity-0" leave-active-class="transition-opacity duration-300" leave-to-class="opacity-0">
        <div
          v-if="toast"
          :class="[
            'pointer-events-auto rounded-lg border px-4 py-3 text-sm shadow-lg',
            toast.error ? 'border-red-300 bg-red-50 text-red-800 dark:border-red-800 dark:bg-gray-800 dark:text-red-400' : 'border-green-300 bg-green-50 text-green-800 dark:border-green-800 dark:bg-gray-800 dark:text-green-400',
          ]"
        >
          {{ toast.message }}
        </div>
      </Transition>
    </div>

    <!-- Page Size Selector (ab Tablet) -->
    <div class="mb-4 mt-4 hidden items-center gap-2 px-4 md:flex">
      <label :for="pageSizeId" class="text-sm text-gray-600 dark:text-gray-400">Zeilen pro Seite:</label>
      <select
          :id="pageSizeId"
          :value="table.getState().pagination.pageSize"
          @change="handlePageSizeChange"
          class="px-3 py-2 border border-gray-300 rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option
            :key="pageSize"
            :value="pageSize"
            v-for="pageSize in pageSizes"
          >
            {{ pageSize }}
          </option>
        </select>
    </div>
    

    <!-- Pagination Controls: auf dem Handy nur Zurück, Seite x / y, Nächste -->
    <div class="my-4 flex gap-2 items-center justify-center md:flex-wrap">
       <button 
        @click="goToPage(0)"
        :disabled="table.getState().pagination.pageIndex === 0"
        class="hidden md:block px-3 py-2 bg-gladegreen hover:bg-gladegreen text-white rounded-lg font-medium text-sm disabled:opacity-50 disabled:cursor-not-allowed"
      >
        « Erste
      </button>
      <button 
        @click="previousPage()"
        :disabled="!table.getCanPreviousPage()"
        aria-label="Vorherige Seite"
        class="px-3 py-2 bg-gladegreen hover:bg-gladegreen text-white rounded-lg font-medium text-sm disabled:opacity-50 disabled:cursor-not-allowed"
      >
        ← <span class="hidden md:inline">Zurück</span>
      </button>
      
      <div id="pageNumbers" class="hidden md:flex gap-1">
        <button 
          v-for="pageNum in visiblePages"
          :key="pageNum"
          @click="goToPage(pageNum)"
          :class="{
            'bg-blue-600 text-white': pageNum === table.getState().pagination.pageIndex,
            'bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white hover:bg-gray-300': pageNum !== table.getState().pagination.pageIndex
          }"
          class="px-3 py-2 rounded-lg text-sm font-medium"
        >
          {{ pageNum + 1 }}
        </button>
      </div>
      <span class="px-2 text-sm text-gray-700 md:hidden dark:text-gray-300">Seite {{ pagination.pageIndex + 1 }} / {{ Math.max(table.getPageCount(), 1) }}</span>
      
      <button 
        @click="nextPage()"
        :disabled="!table.getCanNextPage()"
        aria-label="Nächste Seite"
        class="px-3 py-2 bg-gladegreen hover:bg-gladegreen text-white rounded-lg font-medium text-sm disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <span class="hidden md:inline">Nächste</span> →
      </button>
      <button 
        @click="goToPage(table.getPageCount() - 1)"
        :disabled="table.getState().pagination.pageIndex === table.getPageCount() - 1"
        class="hidden md:block px-3 py-2 bg-gladegreen hover:bg-gladegreen text-white rounded-lg font-medium text-sm disabled:opacity-50 disabled:cursor-not-allowed"
      >
        Letzte »
      </button>
      
      <span class="hidden md:inline text-sm text-gray-600 dark:text-gray-400 ml-4">
        {{ paginationInfo }}
      </span>
    </div>
  </div>
</template>

<script setup>

import { ref, computed, onMounted, watch, useId } from 'vue'
import {
  getCoreRowModel,
  getFilteredRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  useVueTable,
} from '@tanstack/vue-table'
import DataTableActions from './DataTableActions.vue'
import DataTableCell from './DataTableCell.vue'

const pageSizes = [10, 25, 50, 100]

// ===== PROPS =====
const props = defineProps({
  columns: { type: Array, required: true,},
  apiEndpoint: { type: String, required: true,},
  filters: { type: Object, default: () => ({}), },
  searchableColumns: {type: Array, default: () => [], },
  pageSize: {type: Number, default: 10,},
  // Karten auf dem Handy: Spalten-IDs für { kicker, title, lines, details } und Schlüssel der Zeile für
  // { badge, checks } (Text bzw. [{ label, done }]). Nicht genannte Spalten fehlen auf der Karte.
  // Ohne Angabe: erste Spalte als Titel, die nächsten zwei als Zeilen, der Rest unter "Mehr".
  // badgeClasses: Farbe pro Badge-Text, z. B. { 'Neu': 'bg-orange-100 …' }; sonst grau.
  mobile: { type: Object, default: null },
})

// ===== STATE =====
const tableData = ref([])
const isLoading = ref(false)
const loadError = ref(false)
const busyAction = ref(null)
const toast = ref(null)
const pagination = ref({ pageIndex: 0, pageSize: props.pageSize })
const globalFilter = ref('')
const debouncedFilter = ref('')
const sorting = ref([])

let debounceTimer = null
watch(globalFilter, (val) => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => {
    debouncedFilter.value = val
  }, 300)
})


// Gefilterte Daten als computed
const filteredData = computed(() => {
  if (!debouncedFilter.value) return tableData.value
  
  const search = debouncedFilter.value.toLowerCase()
  const cols = props.searchableColumns

  return tableData.value.filter(row => {
    const fields = cols.length ? cols.map(k => row[k]) : Object.values(row)
    return fields.some(val => String(val || '').toLowerCase().includes(search))
  })
})

watch(debouncedFilter, () => {
  pagination.value.pageIndex = 0
})

// ===== TABLE SETUP =====
// Daten als Ref und Zustand als Getter pro Eigenschaft (Muster aus der TanStack-Doku für Vue).
// Mit "get data()" und "get state()" verschachtelt useVueTable die Optionen bei jedem Sortieren
// tiefer; jeder Klick dauerte ein Vielfaches des vorherigen, bis die Seite hing (tests/js/DataTableStress.test.js).
const table = useVueTable({
  data: filteredData,
  columns: props.columns,
  state: {
    get pagination() { return pagination.value },
    get sorting() { return sorting.value },
  },
  onPaginationChange: (updater) => {
    pagination.value = typeof updater === 'function'
      ? updater(pagination.value) : updater
  },
  // onGlobalFilterChange: (updater) => {
  //   globalFilter.value = typeof updater === 'function'
  //     ? updater(globalFilter.value) : updater
  // },
  onSortingChange: (updater) => {
  const newValue = typeof updater === 'function'
    ? updater(sorting.value) : updater
  sorting.value = newValue
  },
  getCoreRowModel: getCoreRowModel(),
  getFilteredRowModel: getFilteredRowModel(),
  getPaginationRowModel: getPaginationRowModel(),
  getSortedRowModel: getSortedRowModel(),
  // Datumsspalten kommen als { display, sort }; sonst alphanumerisch
  defaultColumn: { sortingFn: 'sortValue' },
  sortingFns: {
    sortValue: (rowA, rowB, columnId) => {
      const value = (row) => {
        const raw = row.getValue(columnId)
        return raw !== null && typeof raw === 'object' && 'sort' in raw ? raw.sort : (raw ?? '')
      }
      const a = value(rowA)
      const b = value(rowB)
      if (typeof a === 'number' && typeof b === 'number') {
        return a - b
      }
      return String(a).localeCompare(String(b), 'de-CH', { numeric: true })
    },
  },
  // globalFilterFn: (row, columnId, filterValue) => {
  //   if (!props.searchableColumns.length) return true
  //   const cellValue = String(row.getValue(columnId) || '').toLowerCase()
  //   return cellValue.includes(filterValue.toLowerCase())
  // },
})

// ===== COMPUTED =====
const actionsColumnId = computed(() => props.columns.find((column) => column.meta?.actions)?.accessorKey ?? null)

const mobileLayout = computed(() => {
  const ids = props.columns.filter((column) => !column.meta?.actions).map((column) => column.accessorKey)
  if (!props.mobile) {
    return { title: ids[0], lines: ids.slice(1, 3), details: ids.slice(3) }
  }
  return { lines: [], details: [], ...props.mobile, title: props.mobile.title ?? ids[0] }
})

const sortableColumns = computed(() => table.getAllLeafColumns().filter((column) => column.getCanSort()))

// Wert des Sortier-Auswahlfelds: "spalte:asc", "spalte:desc" oder leer
const sortValue = computed(() => {
  const [sort] = sorting.value
  return sort ? `${sort.id}:${sort.desc ? 'desc' : 'asc'}` : ''
})

const filteredRowCount = computed(() => filteredData.value.length)

const visiblePages = computed(() => {
  const pageCount = table.getPageCount()
  const currentPage = pagination.value.pageIndex
  let startPage = Math.max(0, currentPage - 2)
  let endPage = Math.min(pageCount - 1, currentPage + 2)
  return Array.from({ length: endPage - startPage + 1 }, (_, i) => startPage + i)
})

const paginationInfo = computed(() => {
  const { pageIndex, pageSize } = pagination.value
  const total = filteredRowCount.value
  const start = total === 0 ? 0 : pageIndex * pageSize + 1
  const end = Math.min((pageIndex + 1) * pageSize, total)
  return `${start}-${end} von ${total}`
})


// ===== METHODS =====
async function loadData() {
  try {
    isLoading.value = true
    // Build query params
    const params = new URLSearchParams({ 
      ...props.filters,
      pageIndex: pagination.value.pageIndex,
      pageSize: pagination.value.pageSize,})

    // Add sorting params
    if (sorting.value.length > 0) {
      sorting.value.forEach((sort, index) => {
        params.append(`sort[${index}][id]`, sort.id)
        params.append(`sort[${index}][desc]`, sort.desc ? '1' : '0')
      })
    }

    const response = await fetch(`${props.apiEndpoint}?${params}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
      }
    )
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}`)
    }
    const { data } = await response.json()
    tableData.value = data
    loadError.value = false
  } catch (error) {
    console.error('Error loading table data:', error)
    loadError.value = true
  } finally {
    isLoading.value = false
  }
}

let toastTimer = null
function showToast(message, error = false) {
  toast.value = { message, error }
  clearTimeout(toastTimer)
  toastTimer = setTimeout(() => { toast.value = null }, 4000)
}

/**
 * Aktion einer Zeile ausführen (POST, Antwort { message, row }) und nur diese Zeile ersetzen.
 */
async function runAction(rowData, action) {
  if (action.confirm) {
    const { default: Swal } = await import('sweetalert2')
    const result = await Swal.fire({
      titleText: action.confirm,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: action.label,
      cancelButtonText: 'Abbrechen',
      confirmButtonColor: action.variant === 'danger' ? '#b91c1c' : undefined,
      theme: 'auto',
    })
    if (!result.isConfirmed) {
      return
    }
  }
  busyAction.value = action.url
  try {
    const response = await fetch(action.url, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
      },
    })
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}`)
    }
    const { message, row } = await response.json()
    if (row) {
      // Neues Array statt splice: TanStack erkennt geänderte Daten an der Referenz
      tableData.value = tableData.value.map((entry) => (entry.id === rowData.id ? row : entry))
    }
    showToast(message)
  } catch (error) {
    console.error('Error running action:', error)
    showToast('Die Aktion ist fehlgeschlagen. Bitte Seite neu laden und nochmals versuchen.', true)
  } finally {
    busyAction.value = null
  }
}

// ===== WATCHERS =====
// Navigation – table.X() direkt, kein manuelles State-Update
function previousPage()      { table.previousPage() }
function nextPage()          { table.nextPage() }
function goToPage(i)         { table.setPageIndex(i) }
function handlePageSizeChange(e) { table.setPageSize(Number(e.target.value)) }
const searchId = useId()
const pageSizeId = useId()
const sortId = useId()

// Sortierrichtung für Screenreader
function ariaSort(header) {
  if (!header.column.getCanSort()) return undefined
  const sorted = header.column.getIsSorted()
  if (sorted === 'asc') return 'ascending'
  if (sorted === 'desc') return 'descending'
  return 'none'
}

function setSortValue(value) {
  const [id, direction] = value.split(':')
  sorting.value = id ? [{ id, desc: direction === 'desc' }] : []
}

function badgeClass(text) {
  return props.mobile?.badgeClasses?.[text] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
}

function cellOf(row, columnId) {
  return row.getAllCells().find((cell) => cell.column.id === columnId)
}

function hasValue(row, columnId) {
  const value = row.original[columnId]
  return value !== null && value !== undefined && value !== '' && !(typeof value === 'object' && !value.display)
}

function toggleSort(header) {
  if (!header.column.getCanSort()) return
  
  const columnId = header.column.id
  const currentSort = sorting.value.find(s => s.id === columnId)
  
  if (!currentSort) {
    // First click: sort ascending
    sorting.value = [{ id: columnId, desc: false }]
  } else if (!currentSort.desc) {
    // Second click: sort descending
    sorting.value = [{ id: columnId, desc: true }]
  } else {
    // Third click: remove sort
    sorting.value = []
  }
  
}

// ===== WATCHERS =====
watch(() => props.filters, () => {
    pagination.value.pageIndex = 0
    loadData()
}, { deep: true })

watch(sorting, () => {
  pagination.value.pageIndex = 0
}, { deep: true })

// ===== LIFECYCLE =====
onMounted(() => {
  loadData()
})
</script>