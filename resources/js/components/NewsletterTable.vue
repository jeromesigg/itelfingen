<template>
  <div>
    <data-table
      :columns="newsletterColumns"
    :mobile="mobileCards"
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



// Karten auf dem Handy (siehe DataTable.vue, Prop mobile)



const mobileCards = { title: 'name', lines: ['firstname', 'email'], details: ['bookings', 'members'] }




const newsletterColumns = [
  columnHelper.accessor('name', {
    meta: { html: true },
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