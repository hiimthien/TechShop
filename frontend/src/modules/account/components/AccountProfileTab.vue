<template>
  <div class="space-y-6">
    <!-- Profile Info Box -->
    <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/30 p-6 md:p-8 space-y-6 shadow-sm">
      <div class="flex items-center justify-between border-b border-outline-variant/20 pb-4">
        <div>
          <h3 class="text-base md:text-lg font-black text-on-surface">Thông tin hồ sơ</h3>
          <p class="text-xs text-on-surface-variant">Cập nhật họ tên, số điện thoại để thuận tiện khi nhận hàng.</p>
        </div>
      </div>

      <!-- Profile Feedback Alert -->
      <BaseAlert v-if="profileMessage" :type="profileMessageType" class="text-xs">
        {{ profileMessage }}
      </BaseAlert>

      <form @submit.prevent="submitProfileUpdate" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <BaseInput
            label="Họ và tên"
            type="text"
            placeholder="Nguyễn Văn A"
            v-model="profileForm.name"
            required
          />
          <BaseInput
            label="Số điện thoại"
            type="tel"
            placeholder="0912345678"
            v-model="profileForm.phone"
          />
        </div>

        <div class="space-y-1.5">
          <label class="block text-xs font-semibold text-on-surface">Địa chỉ Email</label>
          <input
            type="email"
            :value="user.email"
            disabled
            class="w-full px-4 py-3 bg-surface-container-low/60 border border-outline-variant/30 rounded-xl text-xs text-on-surface-variant cursor-not-allowed select-none"
          />
          <p class="text-[11px] text-outline">Email được liên kết với tài khoản và không thể thay đổi.</p>
        </div>

        <!-- Avatar File Upload Section -->
        <div class="space-y-2 pt-1">
          <label class="block text-xs font-semibold text-on-surface">Ảnh đại diện</label>
          <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 p-4 bg-surface-container-low/40 rounded-2xl border border-outline-variant/30">
            <div class="relative shrink-0 group">
              <img
                v-if="currentAvatarUrl"
                :src="currentAvatarUrl"
                alt="Avatar Preview"
                class="w-20 h-20 rounded-2xl object-cover ring-2 ring-primary/20 shadow-sm"
              />
              <div
                v-else
                class="w-20 h-20 rounded-2xl bg-gradient-to-br from-primary to-primary-container text-white flex items-center justify-center text-3xl font-black shadow-sm"
              >
                {{ (profileForm.name || user.name || 'U').charAt(0).toUpperCase() }}
              </div>
              <button
                type="button"
                @click="triggerFileInput"
                class="absolute inset-0 bg-black/40 text-white rounded-2xl opacity-0 group-hover:opacity-100 flex flex-col items-center justify-center transition-opacity text-[11px] font-bold"
                title="Đổi ảnh đại diện"
              >
                <span class="material-symbols-outlined text-[20px]">photo_camera</span>
                <span>Đổi ảnh</span>
              </button>
            </div>

            <div class="flex-1 space-y-2">
              <div class="flex flex-wrap items-center gap-2">
                <button
                  type="button"
                  @click="triggerFileInput"
                  :disabled="isUploadingAvatar"
                  class="px-4 py-2 bg-primary/10 text-primary border border-primary/20 hover:bg-primary hover:text-white rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5 disabled:opacity-50"
                >
                  <span v-if="isUploadingAvatar" class="w-3.5 h-3.5 border-2 border-current border-t-transparent rounded-full animate-spin"></span>
                  <span v-else class="material-symbols-outlined text-[16px]">upload</span>
                  {{ isUploadingAvatar ? 'Đang tải lên...' : 'Tải ảnh từ máy tính' }}
                </button>
                <button
                  v-if="avatarPreview"
                  type="button"
                  @click="cancelAvatarSelection"
                  class="px-3 py-2 text-xs text-outline hover:text-on-surface rounded-xl border border-outline-variant/30 transition-colors"
                >
                  Hủy
                </button>
              </div>
              <p class="text-[11px] text-outline">
                Định dạng hỗ trợ: JPG, PNG, WEBP, GIF. Kích thước tối đa: 5MB.
              </p>
            </div>

            <!-- Hidden File Input -->
            <input
              type="file"
              ref="fileInputRef"
              accept="image/png,image/jpeg,image/webp,image/gif"
              class="hidden"
              @change="handleAvatarFileChange"
            />
          </div>
        </div>

        <div class="flex justify-end pt-2">
          <BaseButton
            type="submit"
            :loading="isSavingProfile"
            class="px-6 py-2.5 bg-primary text-white text-xs font-bold rounded-xl hover:bg-primary-hover shadow-sm"
          >
            Lưu thay đổi
          </BaseButton>
        </div>
      </form>
    </div>

    <!-- Password Change Box -->
    <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/30 p-6 md:p-8 space-y-6 shadow-sm">
      <div class="flex items-center justify-between border-b border-outline-variant/20 pb-4">
        <div>
          <h3 class="text-base md:text-lg font-black text-on-surface">Đổi mật khẩu</h3>
          <p class="text-xs text-on-surface-variant">Để bảo mật tài khoản, vui lòng không chia sẻ mật khẩu cho người khác.</p>
        </div>
      </div>

      <!-- Password Feedback Alert -->
      <BaseAlert v-if="passwordMessage" :type="passwordMessageType" class="text-xs">
        {{ passwordMessage }}
      </BaseAlert>

      <form @submit.prevent="submitPasswordUpdate" class="space-y-4">
        <BaseInput
          label="Mật khẩu hiện tại"
          type="password"
          placeholder="••••••••"
          v-model="passwordForm.current_password"
        />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <BaseInput
            label="Mật khẩu mới"
            type="password"
            placeholder="Tối thiểu 6 ký tự"
            v-model="passwordForm.new_password"
            required
          />
          <BaseInput
            label="Xác nhận mật khẩu mới"
            type="password"
            placeholder="Nhập lại mật khẩu mới"
            v-model="passwordForm.new_password_confirmation"
            required
          />
        </div>

        <div class="flex justify-end pt-2">
          <BaseButton
            type="submit"
            :loading="isSavingPassword"
            class="px-6 py-2.5 bg-primary text-white text-xs font-bold rounded-xl hover:bg-primary-hover shadow-sm"
          >
            Cập nhật mật khẩu
          </BaseButton>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { accountService, type UserProfile } from '../services/accountService'
import { useAuthStore } from '@/modules/auth/stores/authStore'
import BaseInput from '@/core/components/BaseInput.vue'
import BaseButton from '@/core/components/BaseButton.vue'
import BaseAlert from '@/core/components/BaseAlert.vue'

interface Props {
  user: UserProfile
}

const props = defineProps<Props>()
const emit = defineEmits<{
  (e: 'updated', user: UserProfile): void
}>()

const authStore = useAuthStore()

const profileForm = ref({
  name: props.user.name || '',
  phone: props.user.phone || '',
  avatar: props.user.avatar || '',
})

watch(
  () => props.user,
  (newVal) => {
    profileForm.value.name = newVal.name || ''
    profileForm.value.phone = newVal.phone || ''
    profileForm.value.avatar = newVal.avatar || ''
  },
  { deep: true }
)

const isSavingProfile = ref(false)
const profileMessage = ref('')
const profileMessageType = ref<'success' | 'error'>('success')

// Avatar Upload State
const fileInputRef = ref<HTMLInputElement | null>(null)
const avatarFile = ref<File | null>(null)
const avatarPreview = ref<string | null>(null)
const isUploadingAvatar = ref(false)

const currentAvatarUrl = computed<string>(() => {
  return avatarPreview.value || profileForm.value.avatar || props.user.avatar || ''
})

function triggerFileInput() {
  fileInputRef.value?.click()
}

async function handleAvatarFileChange(e: Event) {
  const target = e.target as HTMLInputElement
  const file = target.files?.[0]
  if (!file) return

  if (file.size > 5 * 1024 * 1024) {
    profileMessage.value = 'Kích thước ảnh đại diện không được vượt quá 5MB.'
    profileMessageType.value = 'error'
    return
  }

  if (!file.type.startsWith('image/')) {
    profileMessage.value = 'Vui lòng chọn file hình ảnh hợp lệ (JPG, PNG, WEBP, GIF).'
    profileMessageType.value = 'error'
    return
  }

  avatarFile.value = file
  avatarPreview.value = URL.createObjectURL(file)

  isUploadingAvatar.value = true
  profileMessage.value = ''
  try {
    const res = await accountService.uploadAvatar(file)
    profileForm.value.avatar = res.avatar_url
    if (authStore.user) {
      authStore.user.avatar = res.avatar_url
    }
    emit('updated', { ...props.user, avatar: res.avatar_url })
    profileMessage.value = 'Tải ảnh đại diện lên thành công!'
    profileMessageType.value = 'success'
  } catch (err: unknown) {
    profileMessage.value =
      (err as { response?: { data?: { message?: string } } })?.response?.data?.message ||
      'Không thể tải ảnh đại diện lên máy chủ.'
    profileMessageType.value = 'error'
    avatarPreview.value = null
  } finally {
    isUploadingAvatar.value = false
  }
}

function cancelAvatarSelection() {
  avatarFile.value = null
  avatarPreview.value = null
  if (fileInputRef.value) {
    fileInputRef.value.value = ''
  }
}

async function submitProfileUpdate() {
  if (!profileForm.value.name.trim()) return
  isSavingProfile.value = true
  profileMessage.value = ''

  try {
    const res = await accountService.updateProfile({
      name: profileForm.value.name.trim(),
      phone: profileForm.value.phone.trim() || undefined,
      avatar: profileForm.value.avatar.trim() || undefined,
    })
    profileMessage.value = res.message || 'Cập nhật thành công!'
    profileMessageType.value = 'success'
    if (authStore.user) {
      authStore.user.name = res.user.name
      authStore.user.phone = res.user.phone || undefined
      authStore.user.avatar = res.user.avatar || undefined
    }
    emit('updated', res.user)
  } catch (err: unknown) {
    profileMessage.value =
      (err as { response?: { data?: { message?: string } } })?.response?.data?.message ||
      'Không thể cập nhật hồ sơ, vui lòng thử lại.'
    profileMessageType.value = 'error'
  } finally {
    isSavingProfile.value = false
  }
}

// Password Form
const passwordForm = ref({
  current_password: '',
  new_password: '',
  new_password_confirmation: '',
})
const isSavingPassword = ref(false)
const passwordMessage = ref('')
const passwordMessageType = ref<'success' | 'error'>('success')

async function submitPasswordUpdate() {
  if (!passwordForm.value.new_password) return
  if (passwordForm.value.new_password !== passwordForm.value.new_password_confirmation) {
    passwordMessage.value = 'Mật khẩu xác nhận không khớp.'
    passwordMessageType.value = 'error'
    return
  }

  isSavingPassword.value = true
  passwordMessage.value = ''

  try {
    const res = await accountService.updatePassword(passwordForm.value)
    passwordMessage.value = res.message || 'Đổi mật khẩu thành công!'
    passwordMessageType.value = 'success'
    passwordForm.value = {
      current_password: '',
      new_password: '',
      new_password_confirmation: '',
    }
  } catch (err: unknown) {
    passwordMessage.value =
      (err as { response?: { data?: { message?: string } } })?.response?.data?.message ||
      'Không thể đổi mật khẩu, vui lòng kiểm tra lại.'
    passwordMessageType.value = 'error'
  } finally {
    isSavingPassword.value = false
  }
}
</script>

