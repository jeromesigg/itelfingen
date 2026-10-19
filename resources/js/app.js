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
import 'flowbite';
import './libs/custom.js';

// ===== VUE SETUP =====
import { createApp } from 'vue'
import App from './components/App.vue'
import NewsletterTable from './components/NewsletterTable.vue'
import ApplicationTable from './components/ApplicationTable.vue'
import ContactTable from './components/ContactTable.vue'
import FAQTable from './components/FAQTable.vue'

// Component Registry
const components = {
  'events-table': App,
  'newsletter-table': NewsletterTable,
  'applications-table': ApplicationTable,
  'contacts-table': ContactTable,
  'faqs-table': FAQTable,
  // Später: 'users-table': UsersTable, etc.
}

// Auto-mount alle Vue Components
document.querySelectorAll('[data-vue-component]').forEach(el => {
  const componentName = el.dataset.vueComponent
  if (components[componentName]) {
    createApp(components[componentName]).mount(el)
  }
})