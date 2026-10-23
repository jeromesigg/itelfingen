<template>
  <div>
    <data-table
      :columns="contactsColumns"
    :mobile="mobileCards"
      :api-endpoint="apiEndpoint"
      :filters="{status: statusFilter}"
      :searchable-columns="['name', 'email']"
      :page-size="10">
      <template #filters>
        <FilterButtons v-model="statusFilter" :options="['Alle', ...contactStatuses]" label="Status" />
      </template>
    </data-table>
  </div>
</template>

<script setup>       
  import { ref } from 'vue'
  const contactStatuses = ['Offen', 'Bearbeitet']
  const statusFilter = ref('Alle')
  import { createColumnHelper } from '@tanstack/vue-table'
  import DataTable from './DataTable.vue'
  import FilterButtons from './FilterButtons.vue'

  const apiEndpoint = '/contacts/datatable'

  const columnHelper = createColumnHelper()

  // Karten auf dem Handy (siehe DataTable.vue, Prop mobile)

  const mobileCards = { kicker: 'created_at', title: 'name', lines: ['subject', 'content'], details: ['email', 'done', 'user'] }


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
      meta: { actions: true },
      header: 'Aktionen',
      enableSorting: false,
      size: 60,
    }),
  ]
</script>