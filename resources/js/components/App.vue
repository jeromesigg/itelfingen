<template>
  <data-table
    :columns="eventColumns"
    :mobile="mobileCards"
    :api-endpoint="apiEndpoint"
    :filters="{ date: dateFilter, status: statusFilter }"
    :searchable-columns="['number', 'firstname', 'name', 'email', 'comment']"
  >
    <template #filters>
      <div class="flex flex-col gap-3">
        <FilterButtons v-model="dateFilter" :options="['Ab Heute', 'Alle']" label="Zeitraum" active-class="bg-gladegreen focus:ring-gladegreen/50" />
        <FilterButtons v-model="statusFilter" :options="['Alle', ...contractStatuses]" label="Vertragsstatus" />
      </div>
    </template>
  </data-table>
</template>

<script setup>
import { ref } from 'vue'
import DataTable from './DataTable.vue'
import FilterButtons from './FilterButtons.vue'
import { createColumnHelper } from '@tanstack/vue-table'

defineProps({
  // Namen der Vertragsstatus (ContractStatus), aus data-props
  contractStatuses: { type: Array, default: () => [] },
})

const dateFilter = ref('Ab Heute')
const statusFilter = ref('Alle')
const apiEndpoint = '/events/datatable'
const columnHelper = createColumnHelper()

// Karten auf dem Handy (siehe DataTable.vue, Prop mobile)

const mobileCards = {
  kicker: 'start_date',
  badge: 'state',
  badgeClasses: {
    'Neu': 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
    'Bestätigt': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
  },
  title: 'name',
  lines: ['number'],
  checks: 'checks',
  details: ['email', 'total_amount', 'comment', 'comment_intern'],
}


const eventColumns = [
  columnHelper.accessor('start_date', {
    header: 'Datum',
    enableSorting: true,
    size: 70,
  }),
  columnHelper.accessor('number', {
    meta: { html: true },
    header: 'Nr.',
    enableSorting: true,
    size: 20,
  }),
  columnHelper.accessor('name', {
    meta: { html: true },
    header: 'Name',
    enableSorting: true,
    size: 100,
  }),
  columnHelper.accessor('email', {
    header: 'E-Mail',
    enableSorting: true,
    size: 75,
  }),
  columnHelper.accessor('total_amount', {
    header: 'Total',
    enableSorting: false,
    size: 50,
  }),
  columnHelper.accessor('comment', {
    header: 'Bemerkung',
    enableSorting: false,
    size: 200,
  }),
  columnHelper.accessor('comment_intern', {
    header: 'Bemerkung Intern',
    enableSorting: false,
    size: 200,
  }),
  columnHelper.accessor('status', {
    meta: { html: true },
    header: 'Status',
    enableSorting: true,
    size: 200,
  }),
]
</script>
