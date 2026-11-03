import { ref, type Ref } from 'vue'

/**
 * useAsyncAction - Composable to safely handle async operations in Ecommerce
 * Features:
 * - Prevents double-submission / race conditions
 * - Auto manages loading & error state
 * - Returns result or throws error cleanly
 */
export function useAsyncAction<TArgs extends unknown[], TResult>(
  actionFn: (...args: TArgs) => Promise<TResult>
) {
  const loading = ref(false)
  const error: Ref<string | null> = ref(null)

  async function execute(...args: TArgs): Promise<TResult | undefined> {
    if (loading.value) {
      // Prevent duplicate submissions while action is running
      return
    }

    loading.value = true
    error.value = null

    try {
      const result = await actionFn(...args)
      return result
    } catch (err: unknown) {
      const msg =
        (err as { response?: { data?: { message?: string } } })?.response?.data?.message ||
        (err as Error)?.message ||
        'Có lỗi xảy ra, vui lòng thử lại.'
      error.value = msg
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    loading,
    error,
    execute,
  }
}

