<template>
  <section class="space-y-4">
    <div class="flex items-center justify-between pb-2 border-b border-outline-variant/30">
      <div class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
          <span class="material-symbols-outlined text-xl">newspaper</span>
        </div>
        <div>
          <h2 class="text-xl sm:text-2xl font-black text-on-surface">
            Góc Công Nghệ & Tư Vấn
          </h2>
        </div>
      </div>
      <RouterLink
        to="/products"
        class="text-xs font-bold text-primary hover:underline flex items-center gap-1"
      >
        <span>Xem tất cả tin tức</span>
        <span class="material-symbols-outlined text-sm">arrow_forward</span>
      </RouterLink>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <article
        v-for="article in articles"
        :key="article.id"
        class="bg-surface-container-low rounded-2xl overflow-hidden border border-outline-variant/30 hover:shadow-md transition-all duration-300 group flex flex-col justify-between"
      >
        <div class="aspect-video relative overflow-hidden bg-neutral-100">
          <img
            :src="article.image"
            :alt="article.title"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            loading="lazy"
          />
          <span class="absolute bottom-2 left-2 px-2 py-0.5 rounded-md bg-black/60 backdrop-blur text-white text-[10px] font-bold">
            {{ article.tag }}
          </span>
        </div>

        <div class="p-4 flex flex-col flex-grow justify-between gap-3">
          <div>
            <div class="text-[11px] text-outline flex items-center gap-2 mb-1.5 font-medium">
              <span class="material-symbols-outlined text-xs">schedule</span>
              <span>{{ article.date }}</span>
              <span>•</span>
              <span>{{ article.author }}</span>
            </div>
            <h3 class="text-sm font-bold text-on-surface line-clamp-2 group-hover:text-primary transition-colors leading-snug">
              {{ article.title }}
            </h3>
            <p class="text-xs text-on-surface-variant line-clamp-2 mt-1.5 font-normal">
              {{ article.summary }}
            </p>
          </div>

          <div class="flex items-center text-xs font-bold text-primary gap-1 group-hover:translate-x-1 transition-transform">
            <span>Đọc tiếp</span>
            <span class="material-symbols-outlined text-sm">arrow_forward</span>
          </div>
        </div>
      </article>
    </div>
  </section>
</template>

<script setup lang="ts">
import { RouterLink } from 'vue-router'

interface ArticleItem {
  id: number
  title: string
  summary: string
  tag: string
  date: string
  author: string
  image: string
}

const articles: ArticleItem[] = [
  {
    id: 1,
    title: 'So sánh iPhone 16 Pro Max và Galaxy S24 Ultra: Đâu là ông vua công nghệ 2026?',
    summary: 'Đánh giá chi tiết về camera AI, thời lượng pin và hiệu năng giữa hai siêu phẩm hàng đầu hiện nay.',
    tag: 'Đánh Giá',
    date: 'Hôm nay',
    author: 'TechShop Editor',
    image: 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&auto=format&fit=crop&q=80',
  },
  {
    id: 2,
    title: 'Top 5 mẫu laptop AI & văn phòng mỏng nhẹ đáng sở hữu nhất mùa tựu trường',
    summary: 'Tổng hợp các dòng máy chip Intel Core Ultra & Apple M3 với thời lượng pin trên 15 tiếng.',
    tag: 'Tư Vấn Mua Sắm',
    date: '1 ngày trước',
    author: 'Chuyên gia Laptop',
    image: 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=500&auto=format&fit=crop&q=80',
  },
  {
    id: 3,
    title: 'Bàn phím cơ Custom là gì? Hướng dẫn chọn switch gõ êm cho dân lập trình',
    summary: 'Phân biệt Linear, Tactile, Clicky và cách chọn bàn phím cơ Keychron phù hợp cho không gian văn phòng.',
    tag: 'Thủ Thuật Gear',
    date: '2 ngày trước',
    author: 'Gear Lab',
    image: 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=500&auto=format&fit=crop&q=80',
  },
  {
    id: 4,
    title: 'Cách tra cứu IMEI & Serial bảo hành chính hãng chính xác nhất 2026',
    summary: 'Mẹo nhận biết thiết bị điện tử chính hãng còn nguyên seal và hạn bảo hành điện tử chính xác.',
    tag: 'Cẩm Nang',
    date: '3 ngày trước',
    author: 'Bảo Hành TechShop',
    image: 'https://images.unsplash.com/photo-1563770660941-20978e870e26?w=500&auto=format&fit=crop&q=80',
  },
]
</script>

