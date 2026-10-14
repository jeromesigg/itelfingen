<template>
  <div>
    <data-table
      :columns="contactsColumns"
      :api-endpoint="apiEndpoint"
      :filters="{status: statusFilter}"
      :searchable-columns="['name', 'email']"
      :page-size="10">
      <template #filters>
        <!-- Status Filter Buttons -->
        <div id="status_btn" class="grid grid-cols-1 md:grid-cols-7 gap-2 mt-2">
          <div>
            <button @click="statusFilter = 'Alle'" class="focus:outline-hidden text-white bg-grannysmith hover:bg-grannysmith hover:text-white focus:ring-4 focus:ring-grannysmith font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-grannysmith dark:hover:bg-grannysmith dark:focus:ring-grannysmith active">Alle</button>
          </div>
          <div v-for="status in contactStatuses" :key="status">
            <button @click="statusFilter = status" class="focus:outline-hidden text-white bg-grannysmith hover:bg-grannysmith hover:text-white focus:ring-4 focus:ring-grannysmith font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-grannysmith dark:hover:bg-grannysmith dark:focus:ring-grannysmith">{{ status }}</button>
          </div>
        </div>
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
      meta: { html: true },
      header: 'Aktionen',
      enableSorting: false,
      size: 60,
    }),
  ]
</script>