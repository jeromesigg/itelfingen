/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import.meta.glob([
    '../images/**',
    '../fonts/**',
  ]);

import 'flowbite';
import './libs/custom.js';
import { initCitySearch } from './libs/city-search.js';

document.querySelectorAll('[data-city-search]').forEach(initCitySearch);

// ===== VUE SETUP =====
import { createApp } from 'vue'
import App from './components/App.vue'
import NewsletterTable from './components/NewsletterTable.vue'
import ApplicationTable from './components/ApplicationTable.vue'
import ContactTable from './components/ContactTable.vue'
import FAQTable from './components/FAQTable.vue'
import BookingWizard from './components/BookingWizard.vue'
import BookingCalendar from './components/BookingCalendar.vue'

// Component Registry
const components = {
  'events-table': App,
  'newsletter-table': NewsletterTable,
  'applications-table': ApplicationTable,
  'contacts-table': ContactTable,
  'faqs-table': FAQTable,
  'booking-wizard': BookingWizard,
  'booking-calendar': BookingCalendar,
}

// Auto-mount alle Vue Components, Props als JSON im Attribut data-props
document.querySelectorAll('[data-vue-component]').forEach(el => {
  const componentName = el.dataset.vueComponent
  if (components[componentName]) {
    createApp(components[componentName], JSON.parse(el.dataset.props || '{}')).mount(el)
  }
})