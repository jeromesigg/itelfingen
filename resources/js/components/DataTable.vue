<template>
  <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-lg border border-default-medium dark:border-gray-600">
        <!-- Filter Slots (optional) -->
    <div v-if="$slots.filters" class="m-4">
      <slot name="filters"></slot>
    </div>

    <!-- Search Input -->
    <div class="p-4">
        <label for="input-group-1" class="sr-only">Search</label>
        <div class="relative">
            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                <svg class="w-4 h-4 text-body" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg>
            </div>
                  <input 
        v-model="tableState.globalFilter"
        type="text" 
        placeholder="Suchen..." 
        class="block w-full max-w-96 ps-9 pe-3 py-2 text-heading text-sm border border-default-medium border-gray-300 rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 px-3 py-2.5 shadow-xs placeholder:text-body"
      />
        </div>
    </div>



    <!-- Table -->
      <table class="w-full text-sm text-left rtl:text-right text-body">
        <thead class="text-sm text-body bg-neutral-secondary-medium border-b border-t border-default-medium">
          <tr>
            <th 
              v-for="header in table.getHeaderGroups()[0]?.headers"
              :key="header.id"
              class="px-4 py-3 font-bold"
            >
              {{ header.column.columnDef.header }}
            </th>
          </tr>
        </thead>
        <tbody id="tableBody">
          <tr 
            v-for="(row, index) in table.getRowModel().rows"
            :key="row.id"
            :class="{
              'bg-neutral-primary': index % 2 === 0,
              'bg-neutral-primary': index % 2 !== 0,
              'border-b border-default hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors': true
            }"
          >
            <td 
              v-for="cell in row.getVisibleCells()"
              :key="cell.id"
              class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm"
              v-html="formatCellValue(cell.getValue())"
            ></td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Page Size Selector -->
    <div class="mb-4 flex items-center gap-2">
      <label class="text-sm text-gray-600 dark:text-gray-400">Rows pro Seite:</label>
      <select 
        v-model.number="tableState.pagination.pageSize"
        class="px-3 py-2 border border-gray-300 rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="50">50</option>
        <option value="100">100</option>
      </select>
    </div>

    <!-- Pagination Controls -->
    <div class="mt-4 flex gap-2 items-center justify-center flex-wrap">
       <button 
        @click="goToPage(0)"
        :disabled="table.getState().pagination.pageIndex === 0"
        class="px-3 py-2 bg-gladegreen hover:bg-gladegreen text-white rounded-lg font-medium text-sm disabled:opacity-50 disabled:cursor-not-allowed"
      >
        « Erste
      </button>
      <button 
        @click="previousPage()"
        :disabled="!table.getCanPreviousPage()"
        class="px-3 py-2 bg-gladegreen hover:bg-gladegreen text-white rounded-lg font-medium text-sm disabled:opacity-50 disabled:cursor-not-allowed"
      >
        ← Zurück
      </button>
      
      <div id="pageNumbers" class="flex gap-1">
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
      
      <button 
        @click="nextPage()"
        :disabled="!table.getCanNextPage()"
        class="px-3 py-2 bg-gladegreen hover:bg-gladegreen text-white rounded-lg font-medium text-sm disabled:opacity-50 disabled:cursor-not-allowed"
      >
        Nächste →
      </button>
      <button 
        @click="goToPage(table.getPageCount() - 1)"
        :disabled="table.getState().pagination.pageIndex === table.getPageCount() - 1"
        class="px-3 py-2 bg-gladegreen hover:bg-gladegreen text-white rounded-lg font-medium text-sm disabled:opacity-50 disabled:cursor-not-allowed"
      >
        Letzte »
      </button>
      
      <span class="text-sm text-gray-600 dark:text-gray-400 ml-4">
        {{ paginationInfo }}
      </span>
    </div>
</template>

<script setup>

import { ref, reactive, computed, onMounted, watch } from 'vue'
import {
  createColumnHelper,
  getCoreRowModel,
  getFilteredRowModel,
  getPaginationRowModel,
  useVueTable,
} from '@tanstack/vue-table'

const INITIAL_PAGE_INDEX = 0


const goToPageNumber = ref(INITIAL_PAGE_INDEX + 1)

// ===== PROPS =====
const props = defineProps({
  columns: {
    type: Array,
    required: true,
  },
  apiEndpoint: {
    type: String,
    required: true,
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
  searchableColumns: {
    type: Array,
    default: () => [],
  },
  pageSize: {
    type: Number,
    default: 10,
  },
})

// ===== STATE =====
const tableData = ref([])
const isLoading = ref(false)

const tableState = reactive({
  pagination: { pageIndex: 0, pageSize: props.pageSize },
  globalFilter: '',
})

// ===== TABLE SETUP =====
const table = useVueTable({
  get data() {
    return tableData.value
  },
  columns: props.columns,
  state: tableState,
  onStateChange: (updater) => {
    // Wichtig: State update triggern
    const newState = typeof updater === 'function' ? updater(tableState) : updater
    Object.assign(tableState, newState)
  },
  getCoreRowModel: getCoreRowModel(),
  getFilteredRowModel: getFilteredRowModel(),
  getPaginationRowModel: getPaginationRowModel(),
  globalFilterFn: (row, columnId, filterValue) => {
    if (!props.searchableColumns.length) return true
    
    const cellValue = String(row.getValue(columnId) || '').toLowerCase()
    return cellValue.includes(filterValue.toLowerCase())
  },
})

// ===== COMPUTED =====
const visiblePages = computed(() => {
  const pageCount = table.getPageCount()
  const currentPage = table.getState().pagination.pageIndex
  
  let startPage = Math.max(0, currentPage - 2)
  let endPage = Math.min(pageCount - 1, currentPage + 2)
  
  return Array.from({ length: endPage - startPage + 1 }, (_, i) => startPage + i)
})

const paginationInfo = computed(() => {
  const { pageIndex, pageSize } = table.getState().pagination
  const totalFiltered = table.getFilteredRowModel().rows.length
  const start = totalFiltered === 0 ? 0 : pageIndex * pageSize + 1
  const end = Math.min((pageIndex + 1) * pageSize, totalFiltered)
  
  return `${start}-${end} of ${totalFiltered}`
})

// ===== METHODS =====
async function loadData() {
  try {
    isLoading.value = true
    
    // Build query params
    const params = new URLSearchParams({
      ...props.filters,
    })

    const response = await fetch(
      `${props.apiEndpoint}?${params}`,
      {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
      }
    )

    if (!response.ok) {
      throw new Error(`HTTP ${response.status}`)
    }

    const { data } = await response.json()
    tableData.value = data
    
    // Reset to first page
    tableState.pagination.pageIndex = 0
  } catch (error) {
    console.error('Error loading table data:', error)
  } finally {
    isLoading.value = false
  }
}

function formatCellValue(value) {
  // Handle composite objects like start_date
  if (typeof value === 'object' && value !== null && value.display) {
    return value.display
  }
  
  if (typeof value === 'object' && value !== null) {
    return JSON.stringify(value)
  }
  
  return String(value || '')
}

// ===== WATCHERS =====
watch(() => props.filters, () => {
  tableState.pagination.pageIndex = 0
  loadData()
}, { deep: true })

// Wenn pageSize sich ändert
watch(() => tableState.pagination.pageSize, (newPageSize) => {
  // Reset to first page wenn pageSize ändert
  tableState.pagination.pageIndex = 0
  loadData()
}, { deep: true })

function previousPage() {
  
  tableState.pagination.pageIndex--;
  console.log('Navigating to page:', tableState.pagination.pageIndex)
 loadData()
}

function nextPage() {
  // console.log('Navigating to page:', tableState.pagination.pageIndex)
  // tableState.nextPage()
  
  tableState.pagination.pageIndex += 1;
  console.log('Navigating to page:', tableState.pagination.pageIndex)
 loadData()
}

function goToPage(pageIndex) {
  console.log('Navigating to page:', pageIndex)
  tableState.setPageIndex(pageIndex)
}

// ===== LIFECYCLE =====
onMounted(() => {
  loadData()
})
</script>