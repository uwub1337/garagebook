export const money = (value) =>
  `NT$ ${Number(value || 0).toLocaleString('zh-TW', { maximumFractionDigits: 0 })}`

export const km = (value) =>
  `${Number(value || 0).toLocaleString('zh-TW')} km`

export const liters = (value) =>
  `${Number(value || 0).toLocaleString('zh-TW', { maximumFractionDigits: 1 })} L`

export const dateText = (value) => {
  if (!value) return '—'
  const [year, month, day] = String(value).slice(0, 10).split('-')
  return `${year}/${month}/${day}`
}

export const fuelTypeLabel = {
  gasoline: '汽油',
  diesel: '柴油',
  hybrid: '油電',
  electric: '純電',
}

export function apiError(error) {
  const data = error?.response?.data
  if (data?.errors) {
    const first = Object.values(data.errors)[0]
    if (Array.isArray(first) && first.length) return first[0]
  }
  return data?.message || '發生錯誤，請稍後再試'
}
