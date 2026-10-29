<script setup>
import { reactive, ref, watch } from 'vue'
import http from '../api/http'
import { apiError } from '../utils/format'
import Modal from './Modal.vue'

const props = defineProps({
  open: { type: Boolean, default: false },
  carId: { type: [Number, String], required: true },
  record: { type: Object, default: null },
})

const emit = defineEmits(['close', 'saved'])

const form = reactive({
  date: '',
  mileage: '',
  liters: '',
  cost: '',
  notes: '',
})

const error = ref('')
const saving = ref(false)

const today = () => {
  const date = new Date()
  const pad = (value) => String(value).padStart(2, '0')
  return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`
}

watch(
  () => props.open,
  (open) => {
    if (!open) return

    const record = props.record
    form.date = record?.date ?? today()
    form.mileage = record?.mileage ?? ''
    form.liters = record?.liters ?? ''
    form.cost = record?.cost ?? ''
    form.notes = record?.notes ?? ''
    error.value = ''
  },
)

async function submit() {
  saving.value = true
  error.value = ''

  const payload = {
    date: form.date,
    mileage: Number(form.mileage),
    liters: Number(form.liters),
    cost: Number(form.cost),
    notes: form.notes || null,
  }

  try {
    if (props.record) {
      await http.put(`/fuel/${props.record.id}`, payload)
    } else {
      await http.post(`/cars/${props.carId}/fuel`, payload)
    }

    emit('saved')
    emit('close')
  } catch (err) {
    error.value = apiError(err)
  } finally {
    saving.value = false
  }
}
</script>

<template>
  <Modal :open="open" :title="record ? '編輯加油紀錄' : '新增加油紀錄'" @close="$emit('close')">
    <form class="grid grid-cols-1 gap-4 sm:grid-cols-2" @submit.prevent="submit">
      <div>
        <label class="label" for="f-date">加油日期 *</label>
        <input id="f-date" v-model="form.date" type="date" class="input" required />
      </div>

      <div>
        <label class="label" for="f-mileage">加油時里程（km）*</label>
        <input id="f-mileage" v-model="form.mileage" type="number" min="0" class="input" required />
      </div>

      <div>
        <label class="label" for="f-liters">加油量（公升）*</label>
        <input id="f-liters" v-model="form.liters" type="number" min="0.01" step="0.01" class="input" required />
      </div>

      <div>
        <label class="label" for="f-cost">花費金額（NT$）*</label>
        <input id="f-cost" v-model="form.cost" type="number" min="0" step="1" class="input" required />
      </div>

      <div class="sm:col-span-2">
        <label class="label" for="f-notes">備註</label>
        <textarea id="f-notes" v-model="form.notes" rows="2" class="input resize-none" placeholder="加油站、油品…"></textarea>
      </div>

      <p
        v-if="error"
        class="rounded-xl border border-rose-500/20 bg-rose-500/10 px-3 py-2 text-xs text-rose-300 sm:col-span-2"
      >
        {{ error }}
      </p>

      <div class="flex justify-end gap-2 sm:col-span-2">
        <button type="button" class="btn btn-ghost" @click="$emit('close')">取消</button>
        <button type="submit" class="btn btn-primary" :disabled="saving">
          {{ saving ? '儲存中…' : '儲存' }}
        </button>
      </div>
    </form>
  </Modal>
</template>
