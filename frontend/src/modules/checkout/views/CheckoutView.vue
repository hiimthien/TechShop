<template>
  <div class="max-w-7xl mx-auto px-4 md:px-8 py-8 md:py-12">
    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-1.5 text-xs text-on-surface-variant mb-6">
      <RouterLink to="/" class="hover:text-primary transition-colors">Trang chủ</RouterLink>
      <span class="material-symbols-outlined text-[16px]">chevron_right</span>
      <RouterLink to="/cart" class="hover:text-primary transition-colors">Giỏ hàng</RouterLink>
      <span class="material-symbols-outlined text-[16px]">chevron_right</span>
      <span class="text-on-surface font-medium">Thanh toán</span>
    </nav>

    <!-- 1. MÀN HÌNH ĐẶT HÀNG THÀNH CÔNG -->
    <CheckoutSuccessScreen
      v-if="isSuccess"
      :order-number="successOrderNumber"
      :shipping-info="shippingInfo"
      :payment-method-label="paymentMethodLabel"
      :total-amount="lastTotalAmount"
    />

    <!-- 2. TRƯỜNG HỢP GIỎ HÀNG TRỐNG -->
    <div
      v-else-if="cartStore.items.length === 0"
      class="py-16 text-center bg-surface-container-lowest rounded-2xl border border-outline-variant/30 flex flex-col items-center justify-center p-8"
    >
      <EmptyState
        icon="shopping_bag"
        title="Giỏ hàng của bạn đang trống"
        description="Vui lòng thêm ít nhất một sản phẩm vào giỏ hàng trước khi tiến hành thanh toán."
        action-text="Khám phá sản phẩm"
        action-icon="storefront"
        @action="$router.push('/products')"
      />
    </div>

    <!-- 3. KHỐI FORM THANH TOÁN CHÍNH -->
    <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
      <!-- Cột trái: Địa chỉ & Phương thức thanh toán (8 cols) -->
      <div class="lg:col-span-8 flex flex-col gap-6">
        <!-- Error Alert nếu có -->
        <div
          v-if="errorMessage"
          class="p-4 rounded-xl bg-error-container text-on-error-container text-sm flex items-center gap-2"
        >
          <span class="material-symbols-outlined text-[18px]">error</span>
          <span>{{ errorMessage }}</span>
        </div>

        <!-- Khối 1: Thông tin địa chỉ nhận hàng -->
        <CheckoutAddressSection
          :saved-addresses="savedAddresses"
          v-model:selected-address-id="selectedAddressId"
          v-model:show-new-address-form="showNewAddressForm"
          :new-address="newAddress"
        />

        <!-- Khối 2: Phương thức thanh toán -->
        <CheckoutPaymentSection v-model="paymentMethod" />
      </div>

      <!-- Cột phải: Tóm tắt đơn hàng & Nút Xác nhận (4 cols) -->
      <div class="lg:col-span-4">
        <CheckoutSummarySection
          :items="cartStore.items"
          :total-items="cartStore.totalItems"
          :subtotal="cartStore.subtotal"
          :shipping-fee="cartStore.shippingFee"
          :submitting="isSubmitting"
          @place-order="handlePlaceOrder"
          @update:coupon="(c) => appliedCoupon = c"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useCartStore } from '@/modules/cart/stores/cartStore'
import { cartService } from '@/modules/cart/services/cartService'
import { checkoutService, type UserAddress } from '../services/checkoutService'
import EmptyState from '@/core/components/ui/EmptyState.vue'

// Sub-components
import CheckoutAddressSection, { type NewAddressForm } from '../components/CheckoutAddressSection.vue'
import CheckoutPaymentSection from '../components/CheckoutPaymentSection.vue'
import CheckoutSummarySection from '../components/CheckoutSummarySection.vue'
import CheckoutSuccessScreen from '../components/CheckoutSuccessScreen.vue'

const cartStore = useCartStore()

const savedAddresses = ref<UserAddress[]>([])
const selectedAddressId = ref<number | null>(null)
const showNewAddressForm = ref<boolean>(false)

const newAddress = ref<NewAddressForm>({
  name: '',
  phone: '',
  city: '',
  address: '',
})

const paymentMethod = ref<string>('vnpay')
const isSubmitting = ref<boolean>(false)
const errorMessage = ref<string>('')
const appliedCoupon = ref<{ code: string; discount_amount: number } | null>(null)

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

const paymentMethodLabel = computed(() => {
  if (paymentMethod.value === 'vnpay') return 'Cổng thanh toán VNPAY'
  if (paymentMethod.value === 'bank_transfer') return 'Chuyển khoản VietQR'
  return 'Thanh toán khi nhận hàng (COD)'
})

async function loadAddresses() {
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

async function handlePlaceOrder() {
  errorMessage.value = ''

  if (cartStore.items.length === 0) {
    errorMessage.value = 'Giỏ hàng của bạn đang trống.'
    return
  }

  isSubmitting.value = true

  try {
    let finalAddressId = selectedAddressId.value

    if (showNewAddressForm.value || !finalAddressId) {
      if (
        !newAddress.value.name.trim() ||
        !newAddress.value.phone.trim() ||
        !newAddress.value.city.trim() ||
        !newAddress.value.address.trim()
      ) {
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

    // Đảm bảo đồng bộ items sang Backend Cart
    for (const item of cartStore.items) {
      try {
        await cartService.addToCart(item.variant_id, item.quantity)
      } catch {
        // ignore nếu đã có
      }
    }

    // Tạo đơn hàng an toàn với Coupon & Phương thức thanh toán
    const discount = appliedCoupon.value?.discount_amount || 0
    lastTotalAmount.value = Math.max(0, cartStore.subtotal - discount) + cartStore.shippingFee

    const orderRes = await checkoutService.createOrder({
      address_id: finalAddressId,
      shipping_fee: cartStore.shippingFee,
      coupon_code: appliedCoupon.value?.code,
      payment_method: paymentMethod.value,
    })

    const realOrderId = orderRes?.order_id
    successOrderNumber.value = realOrderId
      ? `#TS-${String(realOrderId).padStart(6, '0')}`
      : `#TS-${Math.floor(100000 + Math.random() * 900000)}`

    // Nếu chọn thanh toán VNPAY, chuyển hướng sang cổng thanh toán Sandbox
    if (paymentMethod.value === 'vnpay' && realOrderId) {
      try {
        const vnpayData = await checkoutService.createVnpayPaymentUrl(realOrderId)
        if (vnpayData?.payment_url) {
          cartStore.clearCart()
          window.location.href = vnpayData.payment_url
          return
        }
      } catch (payErr) {
        console.error('Lỗi khi lấy liên kết VNPay:', payErr)
        // Fallback: Nếu không lấy được URL VNPay thì hiển thị màn hình thành công COD
      }
    }

    cartStore.clearCart()
    isSuccess.value = true
    window.scrollTo({ top: 0, behavior: 'smooth' })
  } catch (error: unknown) {
    console.error('Lỗi khi tạo đơn hàng:', error)
    const errData = (error as { response?: { data?: { message?: string } } })?.response?.data
    errorMessage.value = errData?.message || 'Có lỗi xảy ra khi tạo đơn hàng. Vui lòng thử lại.'
  } finally {
    isSubmitting.value = false
  }
}

onMounted(() => {
  loadAddresses()
})
</script>
