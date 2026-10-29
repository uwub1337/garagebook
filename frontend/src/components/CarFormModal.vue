<script setup>
import { reactive, ref, watch } from 'vue'
import http from '../api/http'
import { apiError } from '../utils/format'
import Modal from './Modal.vue'

const props = defineProps({
  open: { type: Boolean, default: false },
  car: { type: Object, default: null },
})

const emit = defineEmits(['close', 'saved'])

const form = reactive({
  name: '',
  make: '',
  model: '',
  year: '',
  plate: '',
  current_mileage: '',
  fuel_type: 'gasoline',
})

const photoFile = ref(null)
const preview = ref(null)
const error = ref('')
const saving = ref(false)

watch(
  () => props.open,
  (open) => {
    if (open) reset()
  },
)

function reset() {
  const car = props.car
  form.name = car?.name ?? ''
  form.make = car?.make ?? ''
  form.model = car?.model ?? ''
  form.year = car?.year ?? ''
  form.plate = car?.plate ?? ''
  form.current_mileage = car?.current_mileage ?? ''
  form.fuel_type = car?.fuel_type ?? 'gasoline'
  photoFile.value = null
  preview.value = car?.photo_url ?? null
  error.value = ''
}

function onFileChange(event) {
  const file = event.target.files?.[0]
  if (!file) return

  if (preview.value?.startsWith('blob:')) {
    URL.revokeObjectURL(preview.value)
  }

  photoFile.value = file
  preview.value = URL.createObjectURL(file)
}

async function submit() {
  saving.value = true
  error.value = ''

  try {
    const body = new FormData()
    body.append('name', form.name)
    body.append('make', form.make)
    body.append('model', form.model)
    body.append('year', form.year)
    body.append('plate', form.plate)
    body.append('current_mileage', form.current_mileage)
    body.append('fuel_type', form.fuel_type)

    if (photoFile.value) body.append('photo', photoFile.value)

    let saved

    if (props.car) {
      body.append('_method', 'PUT')
      const { data } = await http.post(`/cars/${props.car.id}`, body)
      saved = data
    } else {
      const { data } = await http.post('/cars', body)
      saved = data
    }

    emit('saved', saved)
    emit('close')
  } catch (err) {
    error.value = apiError(err)
  } finally {
    saving.value = false
  }
}
</script>

<template>
  <Modal :open="open" :title="car ? '編輯車輛' : '新增車輛'" @close="$emit('close')">
    <form class="grid grid-cols-1 gap-4 sm:grid-cols-2" @submit.prevent="submit">
      <div>
        <label class="label" for="car-name">車輛暱稱 *</label>
        <input id="car-name" v-model="form.name" class="input" placeholder="例如：小白" required />
      </div>

      <div>
        <label class="label" for="car-fuel">燃料種類 *</label>
        <select id="car-fuel" v-model="form.fuel_type" class="input">
          <option value="gasoline">汽油</option>
          <option value="diesel">柴油</option>
          <option value="hybrid">油電</option>
          <option value="electric">純電</option>
        </select>
      </div>

      <div>
        <label class="label" for="car-make">廠牌 *</label>
        <input id="car-make" v-model="form.make" class="input" placeholder="Toyota" required />
      </div>

      <div>
        <label class="label" for="car-model">車型 *</label>
        <input id="car-model" v-model="form.model" class="input" placeholder="Corolla Cross" required />
      </div>

      <div>
        <label class="label" for="car-year">年份</label>
        <input id="car-year" v-model="form.year" type="number" min="1900" max="2100" class="input" placeholder="2022" />
      </div>

      <div>
        <label class="label" for="car-plate">車牌</label>
        <input id="car-plate" v-model="form.plate" class="input" placeholder="ABC-1234" />
      </div>

      <div class="sm:col-span-2">
        <label class="label" for="car-mileage">目前里程（km）*</label>
        <input
          id="car-mileage"
          v-model="form.current_mileage"
          type="number"
          min="0"
          max="5000000"
          class="input"
          placeholder="40300"
          required
        />
      </div>

      <div class="sm:col-span-2">
        <span class="label">車輛照片（選填）</span>
        <label class="flex cursor-pointer items-center gap-4 rounded-xl border border-dashed border-white/15 bg-slate-950/40 p-3 transition hover:border-amber-400/40">
          <span class="grid h-16 w-24 shrink-0 place-items-center overflow-hidden rounded-lg bg-slate-800 text-slate-600">
            <img v-if="preview" :src="preview" alt="車輛照片預覽" class="h-full w-full object-cover" />
            <span v-else class="text-xl">＋</span>
          </span>
          <span class="text-xs text-slate-400">
            <span class="block font-medium text-slate-300">上傳照片</span>
            <span class="mt-0.5 block text-slate-500">JPG / PNG / WebP，最大 5MB</span>
          </span>
          <input type="file" accept="image/jpeg,image/png,image/webp" class="hidden" @change="onFileChange" />
        </label>
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
