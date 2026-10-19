<template>
  <div ref="root" class="scroll-mt-24">
    <Transition enter-active-class="transition-opacity duration-300" enter-from-class="opacity-0">
      <div v-show="step === 1" class="hk-calendar">
        <p class="text-sm font-medium uppercase tracking-wide text-gladegreen">Schritt 1 von 2</p>
        <h3 class="mb-2.5 text-3xl dark:text-white">Datum wählen</h3>

        <div v-if="message" class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-800 dark:bg-gray-800 dark:text-red-400" role="alert">
          {{ message }}
        </div>
        <div v-if="discount > 0" class="mb-4 flex items-center rounded-base border border-success-subtle bg-success-soft p-4 text-sm text-fg-success-strong shadow-xs">
          Du profitierst von {{ discount }} % Buchungsrabatt.
        </div>

        <BookingCalendar v-model:start="start" v-model:end="end" :days="days" @message="message = $event" />

        <div class="mt-2 grid gap-4 md:grid-cols-6">
          <div class="md:col-span-4">
            <div class="hk-agenda__legend">
              <h4 class="text-xl">Legende</h4>
              <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                <div v-for="entry in LEGEND" :key="entry.state" class="text-base">
                  <span :class="['hk-agenda__legend-block', 'hk-agenda__day--' + entry.state]"></span>
                  <span class="hk-agenda__legend-label align-top">{{ entry.label }}</span>
                </div>
              </div>
            </div>
          </div>
          <div class="flex flex-col items-start gap-2 md:col-span-2 md:items-end">
            <p class="font-medium text-gray-900 md:text-right dark:text-white" aria-live="polite">{{ selectionText }}</p>
            <button type="button" :class="[buttonClass, 'bg-gladegreen hover:bg-gladegreen']" @click="next">Weiter</button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- v-show statt v-if: Eingaben bleiben beim Zurückblättern erhalten -->
    <Transition enter-active-class="transition-opacity duration-300" enter-from-class="opacity-0">
    <div v-show="step === 2" class="hk-dateselect">
      <p class="text-sm font-medium uppercase tracking-wide text-gladegreen">Schritt 2 von 2</p>
      <h3 class="mb-2.5 text-3xl dark:text-white">Deine Angaben</h3>
      <div class="grid gap-4 xl:grid-cols-3">
        <div>
          <div class="grid gap-4 md:grid-cols-2">
            <div class="mb-4">
              <label for="firstname" :class="labelClass">Vorname:</label>
              <input id="firstname" name="firstname" type="text" autocomplete="given-name" v-model="form.firstname" :class="inputClass">
            </div>
            <div class="mb-4">
              <label for="name" :class="labelClass">Nachname*:</label>
              <input id="name" name="name" type="text" autocomplete="family-name" v-model="form.name" :class="inputClass" required>
            </div>
          </div>
          <div class="mb-4">
            <label for="group" :class="labelClass">Anlass / Gruppe:</label>
            <input id="group" name="group" type="text" v-model="form.group" :class="inputClass">
          </div>
          <div class="grid gap-4 md:grid-cols-4">
            <div class="mb-4 md:col-span-3">
              <label for="street" :class="labelClass">Strasse*:</label>
              <input id="street" name="street" type="text" autocomplete="address-line1" v-model="form.street" :class="inputClass" required>
            </div>
            <div class="mb-4">
              <label for="house_number" :class="labelClass">Nr.:</label>
              <input id="house_number" name="house_number" type="text" v-model="form.house_number" :class="inputClass">
            </div>
          </div>
          <div class="grid gap-4 md:grid-cols-4">
            <div class="mb-4">
              <label for="zipcode" :class="labelClass">PLZ*:</label>
              <input id="zipcode" name="zipcode" type="text" inputmode="numeric" autocomplete="postal-code" v-model="form.zipcode" :class="inputClass" required>
            </div>
            <div class="mb-4 md:col-span-3">
              <label for="city" :class="labelClass">Ortschaft*:</label>
              <input id="city" name="city" type="text" autocomplete="address-level2" v-model="form.city" :class="inputClass" required>
            </div>
          </div>
          <div class="mb-4">
            <label for="email" :class="labelClass">E-Mail*:</label>
            <input id="email" name="email" type="email" autocomplete="email" v-model="form.email" :class="inputClass" required>
          </div>
          <div class="mb-4">
            <label for="telephone" :class="labelClass">Telefon / Mobil:</label>
            <input id="telephone" name="telephone" type="tel" autocomplete="tel" v-model="form.telephone" :class="inputClass">
          </div>
          <div class="mb-4">
            <label for="marketing_comment" :class="labelClass">Wie bist du auf uns aufmerksam geworden?</label>
            <textarea id="marketing_comment" name="marketing_comment" rows="3" v-model="form.marketing_comment" :class="inputClass"></textarea>
          </div>
          <div class="mb-4 flex items-center gap-2">
            <input id="terms" name="terms" type="checkbox" value="" :class="checkboxClass" required>
            <label for="terms">Ich akzeptiere die <a href="/files/Hausordnung.pdf" target="_blank" rel="noopener" class="text-orientalpink hover:underline">Hausordnung</a>*</label>
          </div>
        </div>

        <div class="xl:col-span-2">
          <p class="mb-2 text-end text-xl font-medium">
            Buchung von {{ formatLong(start) }} bis {{ formatLong(end) }}
          </p>
          <input type="hidden" name="start_date" :value="start ?? ''">
          <input type="hidden" name="end_date" :value="end ?? ''">
          <input type="hidden" name="total_days" :value="nights">
          <input type="hidden" name="total_people" :value="totalPeople">
          <input type="hidden" name="total_amount" :value="totalAmount">
          <div class="relative overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-500 rtl:text-right dark:text-gray-400">
              <thead class="text-xs uppercase text-gray-700 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                  <th scope="col" class="w-[15%] px-2 py-3 sm:px-6">Anzahl</th>
                  <th scope="col" class="w-3/5 px-2 py-3 sm:px-6">Artikel</th>
                  <th scope="col" class="w-[10%] px-2 py-3 sm:px-6">Kosten</th>
                  <th scope="col" class="w-[15%] px-2 py-3 sm:px-6">Total</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="row in rows" :key="row.bexio_code" class="border-b border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                  <th scope="row" class="px-2 py-4 sm:px-6">
                    <template v-if="hasAmountInput(row)">
                      <label :for="'position_' + row.bexio_code" class="sr-only">Anzahl {{ row.name }}</label>
                      <input
                        :id="'position_' + row.bexio_code"
                        v-model="amounts[row.bexio_code]"
                        :name="`positions[${row.bexio_code}]`"
                        type="number"
                        min="0"
                        :class="inputClass"
                      >
                    </template>
                    <template v-else>1</template>
                  </th>
                  <td class="px-2 py-4 sm:px-6">{{ row.name }}</td>
                  <td class="px-2 py-4 sm:px-6">{{ formatPrice(row.price) }}.-</td>
                  <td class="px-2 py-4 sm:px-6">{{ formatPrice(row.total) }}.-</td>
                </tr>
              </tbody>
              <tfoot class="text-gray-900 dark:bg-gray-700 dark:text-white">
                <tr>
                  <td class="px-2 pt-3 sm:px-6"></td>
                  <th scope="row" class="px-2 pt-4 text-base font-semibold sm:px-6 sm:text-lg">
                    Total <span class="font-normal">({{ nightsText }})</span>
                  </th>
                  <td class="px-2 pt-3 sm:px-6"></td>
                  <td class="whitespace-nowrap px-2 pt-4 text-lg font-bold sm:px-6 sm:text-xl" aria-live="polite">{{ formatPrice(totalAmount) }}.-</td>
                </tr>
                <tr>
                  <td class="px-2 pt-3 sm:px-6"></td>
                  <td colspan="3" class="px-2 pb-3 pt-1 text-xs text-gray-600 sm:px-6 dark:text-gray-400">
                    Alle Preise in CHF. Die definitiven Preise werden im Mietvertrag festgelegt.
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>
          <div class="mb-4">
            <label for="comment" :class="labelClass">Bemerkungen</label>
            <textarea id="comment" name="comment" rows="3" v-model="form.comment" :class="inputClass" placeholder="z.B. Genossenschafts-Nr. oder Name der Genossenschafter, Anzahl Parkplätze etc."></textarea>
          </div>
        </div>
      </div>
      <div class="grid gap-4 md:grid-cols-3">
        <div class="mb-1">
          <button type="button" :class="[buttonClass, 'bg-grannysmith hover:bg-grannysmith']" @click="goTo(1)">Zurück</button>
        </div>
        <div class="mb-1 md:col-span-2">
          <button type="submit" :class="[buttonClass, 'bg-gladegreen hover:bg-gladegreen']" :disabled="submitting">
            {{ submitting ? 'Wird gesendet...' : 'Buchungsanfrage senden' }}
          </button>
        </div>
      </div>
    </div>
    </Transition>
  </div>
</template>

<script setup>
import { computed, nextTick, onMounted, reactive, ref } from 'vue'
import BookingCalendar from './BookingCalendar.vue'
import {
  fromISO,
  hasAmountInput,
  isBookable,
  nightsBetween,
  positionTotal,
  shortNoticeDiscount,
  todayISO,
} from '../libs/booking-calendar.js'

const LEGEND = [
  { state: 'FF', label: 'Frei' },
  { state: 'PP', label: 'Provisorisch besetzt' },
  { state: 'BB', label: 'Besetzt' },
  { state: 'FB', label: 'An-/Abreisetag' },
]

const props = defineProps({
  days: { type: Object, default: () => ({}) },
  // [{ bexio_code, name, price }]
  positions: { type: Array, default: () => [] },
  discountEnabled: { type: Boolean, default: false },
  // Eingaben nach einem Validierungsfehler (old())
  old: { type: Object, default: () => ({}) },
  showForm: { type: Boolean, default: false },
})

const labelClass = 'mb-2 block text-sm font-medium text-gray-900 dark:text-white'
const inputClass = 'block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500'
const checkboxClass = 'h-4 w-4 rounded-sm border-gray-300 bg-gray-100 text-blue-600 checked:border-transparent checked:bg-blue-600 focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:checked:bg-blue-600 dark:focus:ring-blue-600'
const buttonClass = 'btn btn-frontpage rounded-md px-4 py-2 text-white transition-colors focus:outline-hidden focus:ring-2 focus:ring-blue-500 disabled:cursor-wait disabled:opacity-70'

const root = ref(null)
const start = ref(props.old.start_date || null)
const end = ref(props.old.end_date || null)
const step = ref(props.showForm && start.value && end.value ? 2 : 1)
const message = ref(null)
const submitting = ref(false)
const amounts = reactive({ ...(props.old.positions ?? {}) })

// Eigener Zustand für die Felder: ein reines :value würde bei jedem Neuzeichnen die Eingaben überschreiben
const FIELDS = ['firstname', 'name', 'group', 'street', 'house_number', 'zipcode', 'city', 'email', 'telephone', 'marketing_comment', 'comment']
const form = reactive(Object.fromEntries(FIELDS.map((field) => [field, props.old[field] ?? ''])))

const nights = computed(() => (start.value && end.value ? nightsBetween(start.value, end.value) : 0))
const discount = computed(() => (start.value && nights.value > 0 ? shortNoticeDiscount(start.value, todayISO(), props.discountEnabled) : 0))

const rows = computed(() => props.positions.filter(isBookable).map((position) => ({
  ...position,
  total: positionTotal(position, amounts[position.bexio_code], nights.value, discount.value),
})))
const totalAmount = computed(() => rows.value.reduce((sum, row) => sum + row.total, 0))
const totalPeople = computed(() => rows.value.filter(hasAmountInput).reduce((sum, row) => sum + (Number(amounts[row.bexio_code]) || 0), 0))

const nightsText = computed(() => {
  const text = `${nights.value} ${nights.value === 1 ? 'Nacht' : 'Nächte'}`
  return discount.value > 0 ? `${text} inkl. ${discount.value}% Rabatt` : text
})

const selectionText = computed(() => {
  if (start.value && end.value) {
    return `${formatShort(start.value)} – ${formatShort(end.value)} · ${nightsText.value}`
  }
  if (start.value) {
    return `Anreise ${formatShort(start.value)} – wähle jetzt den Abreisetag.`
  }
  return 'Wähle im Kalender An- und Abreisetag.'
})

function formatShort(iso) {
  return fromISO(iso).toLocaleDateString('de-CH', { weekday: 'short', day: '2-digit', month: '2-digit', year: 'numeric', timeZone: 'UTC' })
}

function formatLong(iso) {
  return iso ? fromISO(iso).toLocaleDateString('de-CH', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric', timeZone: 'UTC' }) : ''
}

const priceFormat = new Intl.NumberFormat('de-CH', { maximumFractionDigits: 2 })
function formatPrice(value) {
  return priceFormat.format(value)
}

async function goTo(number) {
  step.value = number
  await nextTick()
  root.value.scrollIntoView({ behavior: 'smooth', block: 'start' })
}

function next() {
  if (!start.value || !end.value) {
    message.value = 'Es muss mindestens eine Übernachtung gewählt werden.'
    return
  }
  message.value = null
  goTo(2)
}

onMounted(() => {
  root.value.closest('form')?.addEventListener('submit', () => {
    submitting.value = true
  })
})
</script>
