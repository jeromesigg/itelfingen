<template>
  <div>
    <data-table
      :columns="contactsColumns"
      :api-endpoint="apiEndpoint"
      :filters="{}"
      :searchable-columns="['name', 'email']"
      :page-size="10"
    />
  </div>
</template>

<script setup>
import { createColumnHelper } from '@tanstack/vue-table'
import DataTable from './DataTable.vue'

const apiEndpoint = '/contacts/datatable'

const columnHelper = createColumnHelper()

const contactsColumns = [
  columnHelper.accessor('created_at', {
    header: 'Erstellt am',
    enableSorting: true,
    size: 50,
  }),
  columnHelper.accessor('name', {
    header: 'Name',
    enableSorting: true,
    size: 100,
  }),
  columnHelper.accessor('email', {
    header: 'E-Mail',
    enableSorting: true,
    size: 120,
  }),
  columnHelper.accessor('subject', {
    header: 'Betreff',
    enableSorting: false,
    size: 100,
  }),
  columnHelper.accessor('content', {
    header: 'Text',
    enableSorting: false,
    size: 250,
  }),
  columnHelper.accessor('done', {
    header: 'Bearbeitet',
    enableSorting: false,
    size: 40,
  }),
  columnHelper.accessor('user', {
    header: 'von',
    enableSorting: false,
    size: 80,
  }),
  columnHelper.accessor('actions', {
    header: 'Aktionen',
    enableSorting: false,
    size: 60,
  }),
]
</script>