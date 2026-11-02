<template>
  <div class="max-w-container-max mx-auto px-lg py-12 md:py-16">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-xs text-xs text-on-surface-variant mb-8">
      <RouterLink to="/" class="hover:text-primary transition-colors">Trang chủ</RouterLink>
      <span class="material-symbols-outlined text-[16px]">chevron_right</span>
      <span class="text-on-surface font-medium">Giao hàng & Đổi trả</span>
    </nav>

    <div class="max-w-3xl mx-auto space-y-8">
      <div>
        <span class="inline-block px-3 py-1 bg-primary/10 text-primary text-xs font-bold rounded-full uppercase tracking-wider mb-2">
          Dịch vụ vận chuyển
        </span>
        <h1 class="text-3xl md:text-4xl font-black text-on-surface leading-tight">
          Chính sách giao hàng & Đổi trả
        </h1>
        <p class="text-xs text-on-surface-variant mt-2">Đảm bảo hàng hóa tới tay khách hàng an toàn, nhanh chóng và minh bạch.</p>
      </div>

      <!-- Shipping Policy -->
      <section class="bg-surface-container-lowest p-6 md:p-8 rounded-2xl border border-outline-variant/30 space-y-4 shadow-xs">
        <h2 class="text-lg font-bold text-on-surface flex items-center gap-2">
          <span class="material-symbols-outlined text-primary">local_shipping</span>
          1. Thời gian & Cước phí giao hàng
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs md:text-sm">
          <div class="p-4 rounded-xl bg-surface-container-low border border-outline-variant/30 space-y-2">
            <h3 class="font-bold text-on-surface flex items-center gap-1.5">
              <span class="material-symbols-outlined text-primary text-base">bolt</span>
              Nội thành (Hà Nội, TP.HCM)
            </h3>
            <p class="text-on-surface-variant leading-relaxed">
              Giao nhanh trong <strong>2 - 4 giờ</strong> hoặc nhận hàng trong ngày. Miễn phí cho đơn hàng từ <strong>500.000₫</strong>.
            </p>
          </div>
          <div class="p-4 rounded-xl bg-surface-container-low border border-outline-variant/30 space-y-2">
            <h3 class="font-bold text-on-surface flex items-center gap-1.5">
              <span class="material-symbols-outlined text-primary text-base">public</span>
              Liên tỉnh toàn quốc
            </h3>
            <p class="text-on-surface-variant leading-relaxed">
              Thời gian giao hàng từ <strong>2 - 3 ngày làm việc</strong>. Hàng hóa được đóng gói thùng carton 3 lớp chèn túi khí an toàn.
            </p>
          </div>
        </div>
      </section>

      <!-- Inspection -->
      <section class="bg-surface-container-lowest p-6 md:p-8 rounded-2xl border border-outline-variant/30 space-y-4 shadow-xs">
        <h2 class="text-lg font-bold text-on-surface flex items-center gap-2">
          <span class="material-symbols-outlined text-primary">fact_check</span>
          2. Quy định đồng kiểm khi nhận hàng
        </h2>
        <p class="text-xs md:text-sm text-on-surface-variant leading-relaxed">
          TechVina khuyến khích khách hàng thực hiện <strong>đồng kiểm</strong> (mở thùng kiểm tra ngoại quan cùng nhân viên giao hàng):
        </p>
        <ul class="list-disc list-inside text-xs md:text-sm text-on-surface-variant space-y-2 pl-2">
          <li>Kiểm tra tình trạng hộp bên ngoài, tem niêm phong và phụ kiện đi kèm.</li>
          <li>Nếu phát hiện hộp có dấu hiệu cấn móp, ướt rách hoặc thiếu phụ kiện, quý khách vui lòng từ chối nhận hàng và gọi ngay hotline chăm sóc khách hàng.</li>
        </ul>
      </section>

      <!-- Returns -->
      <section class="bg-surface-container-lowest p-6 md:p-8 rounded-2xl border border-outline-variant/30 space-y-4 shadow-xs">
        <h2 class="text-lg font-bold text-on-surface flex items-center gap-2">
          <span class="material-symbols-outlined text-primary">assignment_return</span>
          3. Quy trình đổi trả hàng
        </h2>
        <ol class="list-decimal list-inside text-xs md:text-sm text-on-surface-variant space-y-2 pl-2">
          <li>Liên hệ tổng đài hoặc mang trực tiếp sản phẩm đến trung tâm bảo hành của TechVina.</li>
          <li>Kỹ thuật viên kiểm tra xác nhận tình trạng lỗi phần cứng.</li>
          <li>Thực hiện đổi máy mới ngay tại quầy hoặc gửi hàng đổi về tận nhà khách hàng hoàn toàn miễn phí.</li>
        </ol>
      </section>
    </div>
  </div>
</template>

<script setup lang="ts">
import { RouterLink } from 'vue-router'
</script>

