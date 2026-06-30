<template>
  <div>
    <data-table
      :columns="applicationsColumns"
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

const apiEndpoint = '/applications/datatable'

const columnHelper = createColumnHelper()



const applicationsColumns = [
  columnHelper.accessor('created_at', {
    header: 'Erstellt am',
    enableSorting: true,
    size: 100,
  }),
  columnHelper.accessor('name', {
    header: 'Name',
    enableSorting: true,
    size: 100,
  }),
  columnHelper.accessor('firstname', {
    header: 'Vorname',
    enableSorting: true,
    size: 100,
  }),
  columnHelper.accessor('email', {
    header: 'E-Mail',
    enableSorting: true,
    size: 150,
  }),
  columnHelper.accessor('street', {
    header: 'Strasse',
    enableSorting: false,
    size: 100,
  }),
  columnHelper.accessor('city', {
    header: 'PLZ / Ort',
    enableSorting: false,
    size: 120,
  }),
  columnHelper.accessor('group', {
    header: 'Organisation',
    enableSorting: false,
    size: 120,
  }),
  columnHelper.accessor('why', {
    header: 'Wieso',
    enableSorting: false,
    size: 250,
  }),
  columnHelper.accessor('comment', {
    header: 'Bemerkung',
    enableSorting: false,
    size: 120,
  }),
  columnHelper.accessor('invoice_send', {
    header: 'Gesendet',
    enableSorting: true,
    size: 60,
  }),
  columnHelper.accessor('refuse', {
    header: 'Abgelehnt',
    enableSorting: true,
    size: 60,
  }),
  columnHelper.accessor('actions', {
    header: 'Aktionen',
    enableSorting: false,
    size: 60,
  }),
]
</script>