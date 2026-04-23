// resources/js/tables/EventsTable.js
import { 
  createColumnHelper, 
  getCoreRowModel, 
  getFilteredRowModel,
  getPaginationRowModel,
  createTable,
} from '@tanstack/table-core'

const columnHelper = createColumnHelper()

/**
 * Initialize Events Table with TanStack Table
 * Simplified Vanilla JS version
 */
export function initEventsTable() {
  // ===== 1. COLUMN DEFINITIONS =====
  const columns = [
    columnHelper.accessor('start_date', {
      header: 'Datum',
      cell: info => info.getValue().display,
      enableGlobalFilter: true,
    }),
    columnHelper.accessor('number', {
      header: 'Nr.',
      cell: info => info.getValue(),
      enableGlobalFilter: true,
    }),
    columnHelper.accessor('name', {
      header: 'Name',
      cell: info => info.getValue(),
      enableGlobalFilter: true,
    }),
    columnHelper.accessor('email', {
      header: 'E-Mail',
      cell: info => info.getValue(),
      enableGlobalFilter: true,
    }),
    columnHelper.accessor('total_amount', {
      header: 'Total',
      cell: info => info.getValue(),
      enableGlobalFilter: false,
    }),
    columnHelper.accessor('comment', {
      header: 'Bemerkung',
      cell: info => info.getValue(),
      enableGlobalFilter: true,
    }),
    columnHelper.accessor('comment_intern', {
      header: 'Bemerkung Intern',
      cell: info => info.getValue(),
      enableGlobalFilter: true,
    }),
    columnHelper.accessor('status', {
      header: 'Status',
      cell: info => info.getValue(),
      enableGlobalFilter: false,
    }),
  ]

    let allTableData = []
    let tableState = {
      pagination: { pageIndex: 0, pageSize: 10 },
      globalFilter: '',
      columnFilters: [],
    }

    // ===== 3. CREATE TANSTACK TABLE WITH GLOBAL FILTER =====
    let table = createTable({
      data: allTableData,
      columns,
      state: tableState,
      onStateChange: (updater) => {
        tableState = typeof updater === 'function' 
          ? updater(tableState) 
          : updater
        renderTable()
      },
      globalFilterFn: (row, columnId, filterValue) => {
        // Custom fuzzy search (matches partial strings)
        const cellValue = String(row.getValue(columnId) || '')
        return cellValue.toLowerCase().includes(filterValue.toLowerCase())
      },
      getCoreRowModel: getCoreRowModel(),
      getFilteredRowModel: getFilteredRowModel(),
      getPaginationRowModel: getPaginationRowModel(),
    })

   // ===== 4. LOAD ALL DATA FROM BACKEND (Initial Load) =====
    async function loadAllData() {
      try {
        const dateFilter = document.getElementById('date_btn_value')?.value || 'Alle'
        const statusFilter = document.getElementById('status_btn_value')?.value || 'Alle'

        const params = new URLSearchParams({
          date: dateFilter,
          status: statusFilter,
        })

        const response = await fetch(
          `/events/datatable?${params}`,
          {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
          }
        )

        if (!response.ok) {
          throw new Error(`HTTP ${response.status}`)
        }

        const { data } = await response.json()
        allTableData = data

        // Recreate table with new data
        table = createTable({
          data: allTableData,
          columns,
          state: tableState,
          onStateChange: (updater) => {
            tableState = typeof updater === 'function' 
              ? updater(tableState) 
              : updater
            renderTable()
          },
          globalFilterFn: (row, columnId, filterValue) => {
            const cellValue = String(row.getValue(columnId) || '')
            return cellValue.toLowerCase().includes(filterValue.toLowerCase())
          },
          getCoreRowModel: getCoreRowModel(),
          getFilteredRowModel: getFilteredRowModel(),
          getPaginationRowModel: getPaginationRowModel(),
        })
        
        // Reset to first page
        tableState.pagination.pageIndex = 0
        renderTable()
      } catch (error) {
        console.error('Error loading events data:', error)
      }
    }

  // ===== 5. RENDER TABLE =====
  function renderTable() {
    const tbody = document.getElementById('eventTableBody')
    if (!tbody) {
      console.error('tbody#eventTableBody not found!')
      return
    }

    tbody.innerHTML = ''

    // Get the paginated rows (after filtering)
 // Get filtered rows from TanStack
    const filteredRows = table.getFilteredRowModel().rows
    
    // Manual pagination: get only the rows for current page
    const { pageIndex, pageSize } = tableState.pagination
    const startIndex = pageIndex * pageSize
    const endIndex = startIndex + pageSize
    const paginatedRows = filteredRows.slice(startIndex, endIndex)

    console.log('Rendering table with rows:', paginatedRows)
    
    paginatedRows.forEach((row, index) => {
      const tr = document.createElement('tr')
      const isOdd = index % 2 === 0
      
      tr.className = 'border-b border-default hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors'
      if (isOdd) {
        tr.className += ' bg-neutral-primary'
      } else {
        tr.className += ' bg-neutral-secondary-soft'
      }

      // Render each column
      columns.forEach(col => {
        const td = document.createElement('td')
        td.className = 'px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm'
        
        // Get the accessor key
        const accessorKey = col.accessorKey
        const rowData = row.original
        let cellValue = rowData[accessorKey]
        
        // Handle composite objects like start_date
        if (typeof cellValue === 'object' && cellValue !== null && cellValue.display) {
          cellValue = cellValue.display
        } else if (typeof cellValue === 'object' && cellValue !== null) {
          cellValue = JSON.stringify(cellValue)
        }
        
        // Set content
        td.innerHTML = String(cellValue || '')
        tr.appendChild(td)
      })

      tbody.appendChild(tr)
    })

    updatePaginationInfo()
  }
  
   // ===== 7. PAGINATION CONTROLS =====
  const prevBtn = document.getElementById('prevBtn')
  const nextBtn = document.getElementById('nextBtn')
  const firstPageBtn = document.getElementById('firstPageBtn')
  const lastPageBtn = document.getElementById('lastPageBtn')
  const pageNumbersContainer = document.getElementById('pageNumbers')

  function renderPaginationNumbers() {
    pageNumbersContainer.innerHTML = ''
    
    const currentPage = tableState.pagination.pageIndex
    const pageCount = table.getPageCount()
    
    let startPage = Math.max(0, currentPage - 2)
    let endPage = Math.min(pageCount - 1, currentPage + 2)
    
    if (startPage > 0) {
      const dots = document.createElement('span')
      dots.textContent = '...'
      dots.className = 'px-2 text-gray-600'
      pageNumbersContainer.appendChild(dots)
    }
    
    for (let i = startPage; i <= endPage; i++) {
      const pageBtn = document.createElement('button')
      pageBtn.textContent = i + 1
      pageBtn.className = `px-3 py-2 rounded-lg text-sm font-medium ${
        i === currentPage
          ? 'bg-blue-600 text-white'
          : 'bg-gray-200 text-gray-800 hover:bg-gray-300'
      }`
      
      pageBtn.addEventListener('click', () => {
        tableState.pagination.pageIndex = i
        renderTable()
      })
      
      pageNumbersContainer.appendChild(pageBtn)
    }
    
    if (endPage < pageCount - 1) {
      const dots = document.createElement('span')
      dots.textContent = '...'
      dots.className = 'px-2 text-gray-600'
      pageNumbersContainer.appendChild(dots)
    }
  }

  function updatePaginationButtons() {
    const currentPage = tableState.pagination.pageIndex
    const pageCount = table.getPageCount()
    
    if (prevBtn) prevBtn.disabled = currentPage === 0
    if (nextBtn) nextBtn.disabled = currentPage >= pageCount - 1
    if (firstPageBtn) firstPageBtn.disabled = currentPage === 0
    if (lastPageBtn) lastPageBtn.disabled = currentPage >= pageCount - 1
    
    renderPaginationNumbers()
  }

  if (prevBtn) {
    prevBtn.addEventListener('click', () => {
      if (tableState.pagination.pageIndex > 0) {
        tableState.pagination.pageIndex--
        renderTable()
      }
    })
  }
  
  if (nextBtn) {
    nextBtn.addEventListener('click', () => {
      const pageCount = table.getPageCount()
      if (tableState.pagination.pageIndex < pageCount - 1) {
        tableState.pagination.pageIndex++
        renderTable()
      }
    })
  }
  
  if (firstPageBtn) {
    firstPageBtn.addEventListener('click', () => {
      tableState.pagination.pageIndex = 0
      renderTable()
    })
  }
  
  if (lastPageBtn) {
    lastPageBtn.addEventListener('click', () => {
      tableState.pagination.pageIndex = table.getPageCount() - 1
      renderTable()
    })
  }

  // ===== 6. UPDATE PAGINATION INFO =====
  function updatePaginationInfo() {
    const pageInfo = document.getElementById('pageInfo')
    if (!pageInfo) return

    const { pageIndex, pageSize } = tableState.pagination
    const totalFiltered = table.getFilteredRowModel().rows.length
    const start = totalFiltered === 0 ? 0 : pageIndex * pageSize + 1
    const end = Math.min((pageIndex + 1) * pageSize, totalFiltered)
    
    pageInfo.textContent = `${start}-${end} of ${totalFiltered}`
    updatePaginationButtons()
  }

  // ===== 8. GLOBAL SEARCH INPUT =====
  const searchInput = document.getElementById('searchInput')
  if (searchInput) {
    searchInput.addEventListener('input', (e) => {
      // Update global filter in tableState
      tableState.globalFilter = e.target.value
      
      // Reset to first page when filtering
      tableState.pagination.pageIndex = 0
      
      // Trigger the state update callback
      // This will cause TanStack to recalculate filtered rows
      table.setOptions(prev => ({
        ...prev,
        state: tableState
      }))
      
      renderTable()
    })
  }

  // ===== 9. DATE FILTER BUTTONS =====
  const dateFilterContainer = document.getElementById('date_btn')
  if (dateFilterContainer) {
    dateFilterContainer.addEventListener('click', (e) => {
      const btn = e.target.closest('button')
      if (btn) {
        dateFilterContainer.querySelectorAll('button').forEach(b => {
          b.classList.remove('active')
        })
        btn.classList.add('active')
        document.getElementById('date_btn_value').value = btn.textContent.trim()
        
        // Reload data from backend with new date filter
        tableState.pagination.pageIndex = 0
        loadAllData()
      }
    })
  }

  // ===== 10. STATUS FILTER BUTTONS =====
  const statusFilterContainer = document.getElementById('status_btn')
  if (statusFilterContainer) {
    statusFilterContainer.addEventListener('click', (e) => {
      const btn = e.target.closest('button')
      if (btn) {
        statusFilterContainer.querySelectorAll('button').forEach(b => {
          b.classList.remove('active')
        })
        btn.classList.add('active')
        document.getElementById('status_btn_value').value = btn.textContent.trim()
        
        // Reload data from backend with new status filter
        tableState.pagination.pageIndex = 0
        loadAllData()
      }
    })
  }
  loadAllData()
}