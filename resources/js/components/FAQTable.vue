<template>
  <div>
    <data-table
      :columns="faqsColumns"
      :api-endpoint="apiEndpoint"
      :filters="{}"
      :searchable-columns="['name', 'description', 'chapter']"
      :page-size="25"
    />
  </div>
</template>

<script setup>
import { createColumnHelper } from '@tanstack/vue-table'
import DataTable from './DataTable.vue'

const apiEndpoint = '/faqs/datatable'

const columnHelper = createColumnHelper()



const faqsColumns = [
  columnHelper.accessor('image', {
    meta: { html: true },
    header: 'Bild',
    enableSorting: false,
    size: 100,
  }),
  columnHelper.accessor('name', {
    meta: { html: true },
    header: 'Name',
    enableSorting: true,
    size: 100,
  }),
  columnHelper.accessor('description', {
    header: 'Beschreibung',
    enableSorting: true,
    size: 200,
  }),
  columnHelper.accessor('chapter', {
    header: 'Kapitel',
    enableSorting: true,
    size: 100,
  }),
  columnHelper.accessor('archive_status', {
    header: 'Archiv-Status',
    enableSorting: true,
    size: 100,
  }),
]
</script>