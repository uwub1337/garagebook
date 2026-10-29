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
  item: '',
  date: '',
  mileage: '',
  cost: '',
  next_due_date: '',
  next_due_mileage: '',
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
    form.item = record?.item ?? ''
    form.date = record?.date ?? today()
    form.mileage = record?.mileage ?? ''
    form.cost = record?.cost ?? ''
    form.next_due_date = record?.next_due_date ?? ''
    form.next_due_mileage = record?.next_due_mileage ?? ''
    form.notes = record?.notes ?? ''
    error.value = ''
  },
)

async function submit() {
  saving.value = true
  error.value = ''

  const payload = {
    item: form.item,
    date: form.date,
    mileage: Number(form.mileage),
    cost: Number(form.cost),
    next_due_date: form.next_due_date || null,
    next_due_mileage: form.next_due_mileage === '' ? null : Number(form.next_due_mileage),
    notes: form.notes || null,
  }

  try {
    if (props.record) {
      await http.put(`/maintenance/${props.record.id}`, payload)
    } else {
      await http.post(`/cars/${props.carId}/maintenance`, payload)
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
  <Modal :open="open" :title="record ? '編輯保養紀錄' : '新增保養紀錄'" @close="$emit('close')">
    <form class="grid grid-cols-1 gap-4 sm:grid-cols-2" @submit.prevent="submit">
      <div class="sm:col-span-2">
        <label class="label" for="m-item">保養項目 *</label>
        <input id="m-item" v-model="form.item" class="input" placeholder="例如：機油更換" required />
      </div>

      <div>
        <label class="label" for="m-date">保養日期 *</label>
        <input id="m-date" v-model="form.date" type="date" class="input" required />
      </div>

      <div>
        <label class="label" for="m-mileage">保養時里程（km）*</label>
        <input id="m-mileage" v-model="form.mileage" type="number" min="0" class="input" required />
      </div>

      <div>
        <label class="label" for="m-cost">花費金額（NT$）*</label>
        <input id="m-cost" v-model="form.cost" type="number" min="0" step="1" class="input" required />
      </div>

      <div>
        <label class="label" for="m-next-date">下次更換日期</label>
        <input id="m-next-date" v-model="form.next_due_date" type="date" class="input" />
      </div>

      <div>
        <label class="label" for="m-next-mileage">下次更換里程（km）</label>
        <input id="m-next-mileage" v-model="form.next_due_mileage" type="number" min="0" class="input" />
      </div>

      <div class="sm:col-span-2">
        <label class="label" for="m-notes">備註</label>
        <textarea id="m-notes" v-model="form.notes" rows="2" class="input resize-none" placeholder="使用的機油、店家、其他備註…"></textarea>
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
