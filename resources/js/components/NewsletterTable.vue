<template>
  <div>
    <data-table
      :columns="newsletterColumns"
      :api-endpoint="apiEndpoint"
      :filters="{}"
      :searchable-columns="['name', 'firstname', 'email']"
      :page-size="25"
    />
  </div>
</template>

<script setup>
import { createColumnHelper } from '@tanstack/vue-table'
import DataTable from './DataTable.vue'

const apiEndpoint = '/newsletter/datatable'

const columnHelper = createColumnHelper()



const newsletterColumns = [
  columnHelper.accessor('name', {
    header: 'Name',
    enableSorting: true,
    size: 200,
  }),
  columnHelper.accessor('firstname', {
    header: 'Vorname',
    enableSorting: true,
    size: 150,
  }),
  columnHelper.accessor('email', {
    header: 'E-Mail',
    enableSorting: true,
    size: 220,
  }),
  columnHelper.accessor('bookings', {
    header: 'Buchungen',
    enableSorting: false,
    size: 100,
  }),
  columnHelper.accessor('members', {
    header: 'Genossenschaft',
    enableSorting: false,
    size: 120,
  }),
]
</script>