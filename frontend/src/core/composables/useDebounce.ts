import { ref, watch, type Ref } from 'vue'

/**
 * useDebounce - Debounce reactive values or search queries in Vue 3
 * Perfect for ecommerce live search & price filter range inputs.
 */
export function useDebouncedRef<T>(source: Ref<T>, delay: number = 300): Ref<T> {
  const debounced = ref(source.value) as Ref<T>
  let timer: ReturnType<typeof setTimeout> | null = null

  watch(
    source,
    (newVal) => {
      if (timer) clearTimeout(timer)
      timer = setTimeout(() => {
        debounced.value = newVal
      }, delay)
    },
    { deep: true }
  )

  return debounced
}

/**
 * useDebounceFn - Debounce arbitrary functions
 */
export function useDebounceFn<TArgs extends unknown[]>(
  fn: (...args: TArgs) => void,
  delay: number = 300
) {
  let timer: ReturnType<typeof setTimeout> | null = null

  return (...args: TArgs) => {
    if (timer) clearTimeout(timer)
    timer = setTimeout(() => {
      fn(...args)
    }, delay)
  }
}

