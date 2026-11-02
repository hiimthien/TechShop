<template>
  <div class="max-w-container-max mx-auto px-lg py-8 md:py-12">
    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-xs text-xs text-on-surface-variant mb-6">
      <RouterLink to="/" class="hover:text-primary transition-colors">Trang chủ</RouterLink>
      <span class="material-symbols-outlined text-[16px]">chevron_right</span>
      <RouterLink to="/cart" class="hover:text-primary transition-colors">Giỏ hàng</RouterLink>
      <span class="material-symbols-outlined text-[16px]">chevron_right</span>
      <span class="text-on-surface font-medium">Thanh toán</span>
    </nav>

    <!-- SUCCESS SCREEN -->
    <div v-if="isSuccess" class="max-w-[800px] mx-auto py-8">
      <section class="text-center mb-10">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-primary-container text-on-primary mb-4 shadow-lg ring-8 ring-primary-fixed/20">
          <span class="material-symbols-outlined text-4xl" style="font-variation-settings: 'FILL' 1;">check_circle</span>
        </div>
        <h1 class="text-2xl md:text-4xl font-black text-on-surface mb-2">Đặt hàng thành công!</h1>
        <p class="text-sm md:text-base text-on-surface-variant max-w-md mx-auto">
          Cảm ơn bạn đã mua sắm tại TechVina. Đơn hàng của bạn đang được nhân viên chuẩn bị và sẽ sớm được giao đến bạn.
        </p>
      </section>

      <div class="bg-surface-container-lowest p-6 md:p-8 rounded-2xl shadow-sm border border-outline-variant/30 space-y-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-6 border-b border-outline-variant/20">
          <div>
            <span class="text-xs uppercase tracking-wider text-outline block">Mã đơn hàng</span>
            <span class="text-xl font-black text-primary">{{ successOrderNumber }}</span>
          </div>
          <div class="sm:text-right">
            <span class="text-xs uppercase tracking-wider text-outline block">Dự kiến giao hàng</span>
            <span class="text-base font-bold text-on-surface">2 - 3 ngày làm việc</span>
          </div>
        </div>

        <!-- Progress bar -->
        <div class="space-y-3">
          <div class="flex justify-between text-xs font-semibold">
            <span class="text-primary font-bold">1. Đã đặt hàng</span>
            <span class="text-on-surface-variant">2. Đang chuẩn bị</span>
            <span class="text-outline">3. Đang giao</span>
            <span class="text-outline">4. Đã nhận</span>
          </div>
          <div class="w-full h-2 bg-surface-container-highest rounded-full overflow-hidden">
            <div class="h-full bg-primary w-1/4 rounded-full transition-all duration-500"></div>
          </div>
        </div>

        <!-- Shipping detail -->
        <div class="pt-4 border-t border-outline-variant/20 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs md:text-sm">
          <div>
            <h4 class="font-bold text-on-surface mb-1">Địa chỉ nhận hàng</h4>
            <p class="text-on-surface-variant leading-relaxed">
              <strong>{{ shippingInfo.name }}</strong> ({{ shippingInfo.phone }})<br />
              {{ shippingInfo.address }}, {{ shippingInfo.city }}
            </p>
          </div>
          <div>
            <h4 class="font-bold text-on-surface mb-1">Phương thức thanh toán</h4>
            <p class="text-on-surface-variant leading-relaxed">
              {{ paymentMethodLabel }}<br />
              <span class="text-primary font-bold">Tổng thanh toán: {{ formatPrice(lastTotalAmount) }}</span>
            </p>
          </div>
        </div>
      </div>

      <div class="mt-8 flex justify-center gap-4">
        <RouterLink
          to="/products"
          class="px-8 py-3.5 bg-primary text-on-primary font-bold rounded-xl text-sm hover:bg-primary-container transition-all shadow-md active:scale-95"
        >
          Tiếp tục mua sắm
        </RouterLink>
      </div>
    </div>

    <!-- EMPTY CART WARNING -->
    <div
      v-else-if="cartStore.items.length === 0"
      class="py-16 text-center bg-surface-container-lowest rounded-2xl border border-outline-variant/30 flex flex-col items-center justify-center gap-4"
    >
      <span class="material-symbols-outlined text-6xl text-outline">shopping_bag</span>
      <h2 class="text-xl font-bold text-on-surface">Giỏ hàng của bạn đang trống</h2>
      <p class="text-sm text-on-surface-variant max-w-sm">
        Vui lòng thêm ít nhất một sản phẩm vào giỏ hàng trước khi tiến hành thanh toán.
      </p>
      <RouterLink
        to="/products"
        class="mt-2 px-6 py-2.5 bg-primary text-on-primary font-bold rounded-xl text-sm hover:bg-primary-container transition-all"
      >
        Khám phá sản phẩm
      </RouterLink>
    </div>

    <!-- MAIN CHECKOUT FORM -->
    <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
      <!-- Left Column: Shipping & Payment (8 cols) -->
      <div class="lg:col-span-8 flex flex-col gap-6">
        <!-- Error Alert -->
        <div
          v-if="errorMessage"
          class="p-4 rounded-xl bg-error-container text-on-error-container text-sm flex items-center gap-2"
        >
          <span class="material-symbols-outlined text-[18px]">error</span>
          <span>{{ errorMessage }}</span>
        </div>

        <!-- Section 1: Shipping Address -->
        <section class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm border border-outline-variant/30">
          <div class="flex items-center justify-between mb-6 pb-3 border-b border-outline-variant/20">
            <div class="flex items-center gap-2">
              <span class="material-symbols-outlined text-primary text-2xl">local_shipping</span>
              <h2 class="text-lg font-black text-on-surface">Thông tin nhận hàng</h2>
            </div>
            <button
              v-if="savedAddresses.length > 0 && !showNewAddressForm"
              type="button"
              @click="showNewAddressForm = true"
              class="text-xs text-primary font-bold hover:underline flex items-center gap-1"
            >
              <span class="material-symbols-outlined text-sm">add</span>
              Thêm địa chỉ mới
            </button>
            <button
              v-if="showNewAddressForm && savedAddresses.length > 0"
              type="button"
              @click="showNewAddressForm = false"
              class="text-xs text-outline hover:text-on-surface font-medium"
            >
              Chọn từ danh sách đã lưu
            </button>
          </div>

          <!-- List Saved Addresses -->
          <div v-if="savedAddresses.length > 0 && !showNewAddressForm" class="flex flex-col gap-3">
            <label
              v-for="addr in savedAddresses"
              :key="addr.id"
              class="flex items-start gap-3 p-4 rounded-xl border cursor-pointer transition-all hover:bg-surface-container-low"
              :class="selectedAddressId === addr.id ? 'border-2 border-primary bg-primary/5 shadow-sm' : 'border-outline-variant/50'"
            >
              <input
                type="radio"
                name="saved_address"
                :value="addr.id"
                v-model="selectedAddressId"
                class="mt-1 text-primary focus:ring-primary"
              />
              <div class="flex-grow text-xs md:text-sm">
                <div class="flex items-center gap-2">
                  <span class="font-bold text-on-surface">{{ addr.name }}</span>
                  <span class="text-outline font-medium">• {{ addr.phone }}</span>
                  <span v-if="addr.is_default" class="bg-primary/10 text-primary text-[10px] font-bold px-1.5 py-0.5 rounded">Mặc định</span>
                </div>
                <p class="text-on-surface-variant mt-1 leading-relaxed">
                  {{ addr.address }}, {{ addr.city }}
                </p>
              </div>
            </label>
          </div>

          <!-- New Address Form -->
          <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-1">
              <label class="text-xs font-semibold text-on-surface-variant">Họ và tên người nhận *</label>
              <input
                v-model="newAddress.name"
                type="text"
                placeholder="Nguyễn Văn A"
                class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant/60 text-sm focus:outline-none focus:border-primary bg-surface-container-low"
              />
            </div>
            <div class="space-y-1">
              <label class="text-xs font-semibold text-on-surface-variant">Số điện thoại *</label>
              <input
                v-model="newAddress.phone"
                type="tel"
                placeholder="0901 234 567"
                class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant/60 text-sm focus:outline-none focus:border-primary bg-surface-container-low"
              />
            </div>
            <div class="space-y-1 md:col-span-2">
              <label class="text-xs font-semibold text-on-surface-variant">Tỉnh / Thành phố *</label>
              <input
                v-model="newAddress.city"
                type="text"
                placeholder="Ví dụ: TP. Hồ Chí Minh hoặc Hà Nội"
                class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant/60 text-sm focus:outline-none focus:border-primary bg-surface-container-low"
              />
            </div>
            <div class="space-y-1 md:col-span-2">
              <label class="text-xs font-semibold text-on-surface-variant">Địa chỉ chi tiết (Số nhà, đường, phường/xã) *</label>
              <textarea
                v-model="newAddress.address"
                rows="2"
                placeholder="Ví dụ: 123 Lê Lợi, Phường Bến Nghé, Quận 1"
                class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant/60 text-sm focus:outline-none focus:border-primary bg-surface-container-low"
              ></textarea>
            </div>
          </div>
        </section>

        <!-- Section 2: Payment Methods -->
        <section class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm border border-outline-variant/30">
          <div class="flex items-center gap-2 mb-6 pb-3 border-b border-outline-variant/20">
            <span class="material-symbols-outlined text-primary text-2xl">account_balance_wallet</span>
            <h2 class="text-lg font-black text-on-surface">Phương thức thanh toán</h2>
          </div>

          <div class="space-y-3">
            <!-- COD -->
            <label
              class="flex items-center p-4 rounded-xl border cursor-pointer transition-all hover:bg-surface-container-low"
              :class="paymentMethod === 'cod' ? 'border-2 border-primary bg-primary/5 shadow-sm' : 'border-outline-variant/50'"
            >
              <input
                type="radio"
                name="payment_method"
                value="cod"
                v-model="paymentMethod"
                class="text-primary focus:ring-primary w-4 h-4"
              />
              <div class="ml-3 flex flex-col">
                <span class="text-sm font-bold text-on-surface">Thanh toán khi nhận hàng (COD)</span>
                <span class="text-xs text-on-surface-variant">Thanh toán tiền mặt cho nhân viên khi nhận kiện hàng</span>
              </div>
              <span class="material-symbols-outlined ml-auto text-primary text-2xl">payments</span>
            </label>

            <!-- Bank Transfer -->
            <label
              class="flex items-center p-4 rounded-xl border cursor-pointer transition-all hover:bg-surface-container-low"
              :class="paymentMethod === 'bank' ? 'border-2 border-primary bg-primary/5 shadow-sm' : 'border-outline-variant/50'"
            >
              <input
                type="radio"
                name="payment_method"
                value="bank"
                v-model="paymentMethod"
                class="text-primary focus:ring-primary w-4 h-4"
              />
              <div class="ml-3 flex flex-col">
                <span class="text-sm font-bold text-on-surface">Chuyển khoản ngân hàng</span>
                <span class="text-xs text-on-surface-variant">Quét mã QR VietQR (Xác nhận tự động trong 1 phút)</span>
              </div>
              <span class="material-symbols-outlined ml-auto text-secondary text-2xl">qr_code_2</span>
            </label>

            <!-- MoMo -->
            <label
              class="flex items-center p-4 rounded-xl border cursor-pointer transition-all hover:bg-surface-container-low"
              :class="paymentMethod === 'momo' ? 'border-2 border-primary bg-primary/5 shadow-sm' : 'border-outline-variant/50'"
            >
              <input
                type="radio"
                name="payment_method"
                value="momo"
                v-model="paymentMethod"
                class="text-primary focus:ring-primary w-4 h-4"
              />
              <div class="ml-3 flex flex-col">
                <span class="text-sm font-bold text-on-surface">Ví điện tử MoMo</span>
                <span class="text-xs text-on-surface-variant">Thanh toán siêu tốc qua ứng dụng MoMo</span>
              </div>
              <span class="ml-auto w-7 h-7 rounded bg-[#ae2070] text-white text-[9px] font-black flex items-center justify-center">MOMO</span>
            </label>
          </div>
        </section>
      </div>

      <!-- Right Column: Order Summary (4 cols) -->
      <div class="lg:col-span-4">
        <aside class="sticky top-24">
          <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm border border-outline-variant/30 flex flex-col gap-6">
            <div class="flex items-center justify-between pb-3 border-b border-outline-variant/20">
              <h2 class="text-lg font-black text-on-surface">Đơn hàng của bạn</h2>
              <span class="bg-primary/10 text-primary text-xs font-bold px-2 py-0.5 rounded-full">
                {{ cartStore.totalItems }} sản phẩm
              </span>
            </div>

            <!-- Items List -->
            <div class="divide-y divide-outline-variant/20 max-h-72 overflow-y-auto pr-1">
              <div
                v-for="item in cartStore.items"
                :key="item.variant_id"
                class="py-3 flex gap-3 items-center"
              >
                <div class="w-14 h-14 rounded-lg bg-surface-container-low border border-outline-variant/30 p-1 flex-shrink-0 flex items-center justify-center">
                  <img
                    :src="item.thumbnail || fallbackImage"
                    :alt="item.product_name"
                    class="w-full h-full object-contain"
                  />
                </div>
                <div class="flex-grow min-w-0">
                  <h4 class="text-xs font-bold text-on-surface truncate">{{ item.product_name }}</h4>
                  <p class="text-[11px] text-on-surface-variant">SL: {{ item.quantity }}</p>
                  <p class="text-xs font-bold text-primary mt-0.5">{{ formatPrice(item.price * item.quantity) }}</p>
                </div>
              </div>
            </div>

            <!-- Price Breakdown -->
            <div class="space-y-2.5 pt-3 border-t border-outline-variant/20 text-xs">
              <div class="flex justify-between text-on-surface-variant">
                <span>Tạm tính</span>
                <span class="font-bold text-on-surface">{{ formatPrice(cartStore.subtotal) }}</span>
              </div>
              <div class="flex justify-between text-on-surface-variant">
                <span>Phí vận chuyển</span>
                <span v-if="cartStore.shippingFee === 0" class="text-primary font-bold">Miễn phí</span>
                <span v-else class="font-bold text-on-surface">{{ formatPrice(cartStore.shippingFee) }}</span>
              </div>
              <div class="flex justify-between pt-3 border-t border-outline-variant/20 text-sm">
                <span class="font-bold text-on-surface">Tổng cộng</span>
                <span class="text-xl font-black text-primary">{{ formatPrice(cartStore.totalAmount) }}</span>
              </div>
            </div>

            <!-- Submit CTA -->
            <button
              type="button"
              :disabled="isSubmitting"
              @click="handlePlaceOrder"
              class="w-full py-4 bg-primary text-on-primary font-bold rounded-xl text-sm shadow-md hover:bg-primary-container transition-all active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
            >
              <span v-if="isSubmitting" class="material-symbols-outlined animate-spin text-sm">progress_activity</span>
              <span>{{ isSubmitting ? 'Đang tạo đơn hàng...' : 'Xác nhận đặt hàng' }}</span>
            </button>

            <p class="text-[11px] text-outline text-center leading-relaxed">
              Bằng cách nhấn "Xác nhận đặt hàng", bạn đồng ý với các Điều khoản & Chính sách giao dịch của TechVina.
            </p>
          </div>
        </aside>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { useCartStore } from '@/modules/cart/stores/cartStore'
import { cartService } from '@/modules/cart/services/cartService'
import { checkoutService, type UserAddress } from '../services/checkoutService'

const cartStore = useCartStore()

const savedAddresses = ref<UserAddress[]>([])
const selectedAddressId = ref<number | null>(null)
const showNewAddressForm = ref<boolean>(false)

const newAddress = ref({
  name: '',
  phone: '',
  city: '',
  address: '',
})

const paymentMethod = ref<'cod' | 'bank' | 'momo'>('cod')
const isSubmitting = ref<boolean>(false)
const errorMessage = ref<string>('')

// Trạng thái thành công
const isSuccess = ref<boolean>(false)
const successOrderNumber = ref<string>('')
const lastTotalAmount = ref<number>(0)
const shippingInfo = ref({
  name: '',
  phone: '',
  city: '',
  address: '',
})

const fallbackImage =
  'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><rect fill="%23f0eded" width="100" height="100"/><text fill="%237b7487" font-family="sans-serif" font-size="12" dy="4" font-weight="bold" x="50%" y="50%" text-anchor="middle">TechShop</text></svg>'

const paymentMethodLabel = computed(() => {
  if (paymentMethod.value === 'bank') return 'Chuyển khoản VietQR'
  if (paymentMethod.value === 'momo') return 'Ví MoMo'
  return 'Thanh toán khi nhận hàng (COD)'
})

const formatPrice = (price: number | string | null | undefined): string => {
  if (price === null || price === undefined || price === '') return '0 ₫'
  const numeric = typeof price === 'string' ? parseFloat(price) : price
  if (isNaN(numeric)) return '0 ₫'
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND',
  }).format(numeric)
}

const loadAddresses = async () => {
  try {
    const list = await checkoutService.getAddresses()
    savedAddresses.value = list
    if (list.length > 0) {
      const defaultAddr = list.find((a) => a.is_default) || list[0]
      if (defaultAddr) {
        selectedAddressId.value = defaultAddr.id
      }
      showNewAddressForm.value = false
    } else {
      showNewAddressForm.value = true
    }
  } catch (error) {
    console.error('Không thể tải danh sách địa chỉ:', error)
    showNewAddressForm.value = true
  }
}

const handlePlaceOrder = async () => {
  errorMessage.value = ''

  if (cartStore.items.length === 0) {
    errorMessage.value = 'Giỏ hàng của bạn đang trống.'
    return
  }

  isSubmitting.value = true

  try {
    let finalAddressId = selectedAddressId.value

    // Nếu đang nhập địa chỉ mới, cần lưu địa chỉ trước
    if (showNewAddressForm.value || !finalAddressId) {
      if (!newAddress.value.name.trim() || !newAddress.value.phone.trim() || !newAddress.value.city.trim() || !newAddress.value.address.trim()) {
        errorMessage.value = 'Vui lòng điền đầy đủ các thông tin nhận hàng.'
        isSubmitting.value = false
        return
      }

      await checkoutService.createAddress({
        name: newAddress.value.name.trim(),
        phone: newAddress.value.phone.trim(),
        city: newAddress.value.city.trim(),
        address: newAddress.value.address.trim(),
        is_default: true,
      })

      // Tải lại để lấy ID vừa tạo
      const updatedList = await checkoutService.getAddresses()
      savedAddresses.value = updatedList
      const newlyCreated = updatedList[updatedList.length - 1]
      if (!newlyCreated) {
        throw new Error('Không thể khởi tạo địa chỉ giao hàng.')
      }
      finalAddressId = newlyCreated.id
      selectedAddressId.value = finalAddressId

      shippingInfo.value = {
        name: newlyCreated.name,
        phone: newlyCreated.phone,
        city: newlyCreated.city,
        address: newlyCreated.address,
      }
    } else {
      const chosen = savedAddresses.value.find((a) => a.id === finalAddressId)
      if (chosen) {
        shippingInfo.value = {
          name: chosen.name,
          phone: chosen.phone,
          city: chosen.city,
          address: chosen.address,
        }
      }
    }

    if (!finalAddressId) {
      errorMessage.value = 'Vui lòng chọn hoặc thêm địa chỉ nhận hàng.'
      isSubmitting.value = false
      return
    }

    // Đảm bảo các items trong giỏ hàng đã đồng bộ vào Backend Cart của user
    for (const item of cartStore.items) {
      try {
        await cartService.addToCart(item.variant_id, item.quantity)
      } catch {
        // Nếu đã có trong giỏ backend thì bỏ qua
      }
    }

    // Gọi API tạo đơn hàng
    lastTotalAmount.value = cartStore.totalAmount
    await checkoutService.createOrder({
      address_id: finalAddressId,
      shipping_fee: cartStore.shippingFee,
    })

    // Thành công
    successOrderNumber.value = '#TV' + Math.floor(100000 + Math.random() * 900000)
    cartStore.clearCart()
    isSuccess.value = true
    window.scrollTo({ top: 0, behavior: 'smooth' })
  } catch (error: unknown) {
    console.error('Lỗi khi tạo đơn hàng:', error)
    const errData = (error as { response?: { data?: { message?: string } } })?.response?.data
    errorMessage.value =
      errData?.message || 'Có lỗi xảy ra khi tạo đơn hàng. Vui lòng thử lại.'
  } finally {
    isSubmitting.value = false
  }
}

onMounted(() => {
  loadAddresses()
})
</script>
