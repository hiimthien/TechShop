import { inject, provide, type InjectionKey, type Ref, type ComputedRef } from 'vue'
import type { Product, ProductVariant } from '../types'

export interface ProductContext {
  product: Ref<Product | null>
  currentPrice: ComputedRef<number>
  currentOriginalPrice: ComputedRef<number | undefined>
  currentMainImage: ComputedRef<string>
  selectedVariant: Ref<ProductVariant | null>
  selectedStorage: Ref<string>
  activeColorName: ComputedRef<string>
  handleAddToCart: () => void
  handleBuyNow: () => void
}

export const ProductContextKey: InjectionKey<ProductContext> = Symbol('ProductContextKey')

export function provideProductContext(context: ProductContext) {
  provide(ProductContextKey, context)
}

export function useProductContext(): ProductContext {
  const context = inject(ProductContextKey)
  if (!context) {
    throw new Error('useProductContext must be used within a component providing ProductContextKey')
  }
  return context
}

