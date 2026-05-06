<template>
  <div v-if="showApp" id="app">
    <data-table
      :columns="eventColumns"
      :api-endpoint="apiEndpoint"
      :filters="{ date: dateFilter, status: statusFilter }"
      :searchable-columns="['number', 'firstname', 'name', 'email', 'comment']"
    >
      <template #filters>
        <!-- Date Filter Buttons -->
        <div id="date_btn" class="grid grid-cols-1 md:grid-cols-2 gap-2 w-1/5">
          <div>
            <button @click="dateFilter = 'Alle'" class="focus:outline-hidden text-white bg-gladegreen hover:bg-gladegreen hover:text-white focus:ring-4 focus:ring-gladegreen font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gladegreen dark:hover:bg-gladegreen dark:focus:ring-gladegreen">Alle</button>
          </div>
          <div>
            <button @click="dateFilter = 'Ab Heute'" class="focus:outline-hidden text-white bg-gladegreen hover:bg-gladegreen hover:text-white focus:ring-4 focus:ring-gladegreen font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gladegreen dark:hover:bg-gladegreen dark:focus:ring-gladegreen active">Ab Heute</button>
          </div>
        </div>
        
        <!-- Status Filter Buttons -->
        <div id="status_btn" class="grid grid-cols-1 md:grid-cols-7 gap-2 mt-2">
          <div>
            <button @click="statusFilter = 'Alle'" class="focus:outline-hidden text-white bg-grannysmith hover:bg-grannysmith hover:text-white focus:ring-4 focus:ring-grannysmith font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-grannysmith dark:hover:bg-grannysmith dark:focus:ring-grannysmith active">Alle</button>
          </div>
          <div v-for="status in contractStatuses" :key="status">
            <button @click="statusFilter = status" class="focus:outline-hidden text-white bg-grannysmith hover:bg-grannysmith hover:text-white focus:ring-4 focus:ring-grannysmith font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-grannysmith dark:hover:bg-grannysmith dark:focus:ring-grannysmith">{{ status }}</button>
          </div>
        </div>
      </template>
    </data-table>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import DataTable from './DataTable.vue'

const showApp = ref(false)
const dateFilter = ref('Ab Heute')
const statusFilter = ref('Alle')
const apiEndpoint = '/events/datatable'  // ← ALS VARIABLE

const eventColumns = [
  { accessorKey: 'start_date', header: 'Datum', enableSorting: true, },
  { accessorKey: 'number', header: 'Nr.', enableSorting: true,  },
  { accessorKey: 'name', header: 'Name', enableSorting: true,  },
  { accessorKey: 'email', header: 'E-Mail', enableSorting: true,  },
  { accessorKey: 'total_amount', header: 'Total', enableSorting: false, },
  { accessorKey: 'comment', header: 'Bemerkung', enableSorting: false },
  { accessorKey: 'comment_intern', header: 'Bemerkung Intern', enableSorting: false },
  { accessorKey: 'status', header: 'Status', enableSorting: true },
]

// Get contract statuses from window object (set in Blade)
const contractStatuses = ref([])

onMounted(() => {
  if (window.contractStatuses) {
    contractStatuses.value = window.contractStatuses
  }
  showApp.value = true
})

import { onMounted } from 'vue'
</script>