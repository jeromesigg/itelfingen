/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import.meta.glob([
    '../images/**',
    '../fonts/**',
  ]);

import './bootstrap';
import './admin/jquery-ui.min.js';
import 'bootstrap';
import 'laravel-datatables-vite';
import './libs/validate.js';
import './admin/jqBootstrapValidation.js';
import 'flowbite';
// import { DataTable } from "simple-datatables";
import './libs/main.js';
import './libs/custom.js';
import './libs/faq.js';

// Initialize Events Table when DOM is ready
import { initEventsTable } from './tables/EventsTable.js'

document.addEventListener('DOMContentLoaded', () => {
  const tbody = document.getElementById('eventTableBody')
  if (tbody) {
    initEventsTable()
  }
})

// ===== VUE SETUP =====
import { createApp } from 'vue'
import App from './components/App.vue'
import DataTable from './components/DataTable.vue'

const app = createApp(App)
app.component('DataTable', DataTable)

if (document.getElementById('app')) {
  app.mount('#app')
}