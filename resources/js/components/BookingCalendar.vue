<template>
  <div class="hk-agenda">
    <div class="mb-4 flex items-center justify-between gap-4">
      <button type="button" :class="navClass" :disabled="!canGoBack" @click="move(-1)">
        <span aria-hidden="true">‹</span> Frühere Monate
      </button>
      <button type="button" :class="navClass" @click="move(1)">
        Spätere Monate <span aria-hidden="true">›</span>
      </button>
    </div>
    <div :class="gridClass">
      <div v-for="(month, index) in visibleMonths" :key="month.key" :class="index > 1 ? 'hidden sm:block' : ''">
        <h4 class="hk-agenda__title">{{ month.title }}</h4>
        <table class="hk-agenda__month w-full sm:w-auto">
          <thead>
            <tr>
              <th v-for="label in WEEKDAYS" :key="label" scope="col" class="hk-agenda__label font-normal">{{ label }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(week, w) in month.weeks" :key="w" class="hk-agenda__week">
              <template v-for="(iso, k) in week" :key="iso ?? `${w}-${k}`">
                <td v-if="!iso" class="hk-agenda__day--inactive"></td>
                <td v-else :class="dayClass(iso, k)" :data-date="iso">
                  <button
                    v-if="mode === 'guest'"
                    type="button"
                    class="h-full w-full cursor-[inherit] focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-ebonyclay"
                    :aria-label="dayLabel(iso)"
                    :aria-disabled="iso < minDate || undefined"
                    :aria-pressed="iso in selection"
                    @click="select(iso)"
                  >{{ dayNumber(iso) }}</button>
                  <a
                    v-else-if="days[iso]?.id"
                    :href="editUrl.replace('__ID__', days[iso].id)"
                    class="flex h-full w-full items-center justify-center"
                  >{{ dayNumber(iso) }}</a>
                  <template v-else>{{ dayNumber(iso) }}</template>
                </td>
              </template>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import {
  MIN_LEAD_DAYS,
  MONTH_NAMES,
  addDays,
  baseState,
  clampEnd,
  fromISO,
  isStartable,
  monthWeeks,
  selectionStates,
  todayISO,
} from '../libs/booking-calendar.js'

const WEEKDAYS = ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So']

const props = defineProps({
  // Belegte Tage aus App\Services\BookingDays
  days: { type: Object, default: () => ({}) },
  // guest: Zeitraum wählen; admin: Klick auf eine Buchung öffnet sie
  mode: { type: String, default: 'guest' },
  months: { type: Number, default: 6 },
  // Monate pro Blättern ab Breakpoint sm bzw. auf dem Handy
  step: { type: Number, default: 3 },
  mobileStep: { type: Number, default: 2 },
  gridClass: { type: String, default: 'grid grid-cols-1 gap-6 sm:grid-cols-3 sm:gap-4' },
  // Link zur Buchung im Admin-Kalender, __ID__ wird ersetzt
  editUrl: { type: String, default: '' },
})

const start = defineModel('start', { type: String, default: null })
const end = defineModel('end', { type: String, default: null })
const emit = defineEmits(['message'])

const navClass = 'inline-flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-900 hover:bg-gray-100 focus:outline-hidden focus:ring-4 focus:ring-gray-100 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:hover:bg-gray-700'

const today = todayISO()
const minDate = addDays(today, MIN_LEAD_DAYS)
const firstMonth = start.value ?? today
const cursor = ref({ year: Number(firstMonth.slice(0, 4)), month: Number(firstMonth.slice(5, 7)) - 1 })

const visibleMonths = computed(() => Array.from({ length: props.months }, (_, i) => {
  const date = new Date(Date.UTC(cursor.value.year, cursor.value.month + i, 1))
  const year = date.getUTCFullYear()
  const month = date.getUTCMonth()
  return { key: `${year}-${month}`, title: `${MONTH_NAMES[month]} ${year}`, weeks: monthWeeks(year, month) }
}))

// Gäste blättern nicht in die Vergangenheit
const canGoBack = computed(() => props.mode !== 'guest'
  || cursor.value.year * 12 + cursor.value.month > Number(today.slice(0, 4)) * 12 + Number(today.slice(5, 7)) - 1)

const selection = computed(() => (props.mode === 'guest' ? selectionStates(props.days, start.value, end.value) : {}))

function move(direction) {
  const months = window.matchMedia('(min-width: 480px)').matches ? props.step : props.mobileStep
  const date = new Date(Date.UTC(cursor.value.year, cursor.value.month + direction * months, 1))
  cursor.value = { year: date.getUTCFullYear(), month: date.getUTCMonth() }
}

function dayClass(iso, weekday) {
  const state = selection.value[iso] ?? baseState(props.days, iso)
  const classes = ['hk-agenda__day--' + state]
  if (state === 'FF' && weekday === 5) {
    classes.push('hk-agenda__day--sat')
  }
  if (state === 'FF' && weekday === 6) {
    classes.push('hk-agenda__day--sun')
  }
  if (props.mode === 'guest' && iso < minDate) {
    classes.push('hk-agenda__day--past')
  }
  return classes
}

function dayNumber(iso) {
  return Number(iso.slice(8, 10))
}

function dayLabel(iso) {
  const text = fromISO(iso).toLocaleDateString('de-CH', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric', timeZone: 'UTC' })
  return isStartable(props.days, iso) ? text : text + ', belegt'
}

function select(iso) {
  if (iso < minDate) {
    emit('message', `Buchungen sind frühestens ${MIN_LEAD_DAYS} Tage im Voraus möglich.`)
    return
  }
  // Neue Auswahl: erster Klick oder Klick vor den Anreisetag
  if (!start.value || end.value || iso <= start.value) {
    if (!isStartable(props.days, iso)) {
      emit('message', 'An diesem Datum kann nicht reserviert werden.')
      return
    }
    start.value = iso
    end.value = null
    emit('message', null)
    return
  }
  const clamped = clampEnd(props.days, start.value, iso)
  if (clamped === start.value) {
    emit('message', 'Am Tag nach der Anreise ist das Haus bereits belegt.')
    return
  }
  end.value = clamped
  emit('message', clamped < iso ? 'Der Zeitraum endet vor der nächsten Buchung.' : null)
}
</script>
