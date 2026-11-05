# BÁO CÁO KHẢO SÁT & KIỂM KÊ DỮ LIỆU TECHSHOP (LARAVEL 11)

> **Thời điểm kiểm tra**: 2026-11-05 14:19:31
> **Chế độ kiểm tra**: Read-only (Chỉ SELECT / Không thay đổi schema / Không can thiệp DB)
> **Môi trường Database**: MySQL (Database: `techshop`)

---

## 1. SCHEMA HỆ THỐNG CƠ SỞ DỮ LIỆU

### Bảng: `users`
> Người dùng và phân quyền (admin/customer)

| Tên cột | Kiểu dữ liệu | Nullable | Khóa / Mặc định | Ghi chú / Enum |
| :--- | :--- | :---: | :--- | :--- |
| `id` | `bigint unsigned` | No | PRI (AUTO_INCREMENT) | - |
| `google_id` | `varchar(255)` | Yes | UNI (`NULL`) | - |
| `name` | `varchar(255)` | No |  (-) | - |
| `phone` | `varchar(11)` | Yes |  (`NULL`) | - |
| `avatar` | `varchar(255)` | Yes |  (`NULL`) | - |
| `role` | `varchar(255)` | No |  (`customer`) | `admin`, `customer` |
| `email` | `varchar(255)` | No | UNI (-) | - |
| `email_verified_at` | `timestamp` | Yes |  (`NULL`) | - |
| `password` | `varchar(255)` | Yes |  (`NULL`) | - |
| `remember_token` | `varchar(100)` | Yes |  (`NULL`) | - |
| `created_at` | `timestamp` | Yes |  (`NULL`) | - |
| `updated_at` | `timestamp` | Yes |  (`NULL`) | - |

- **Khóa ngoại (FK)**: Không có
- **Ràng buộc Unique**: `email` (users_email_unique), `google_id` (users_google_id_unique)

### Bảng: `categories`
> Danh mục sản phẩm đa cấp

| Tên cột | Kiểu dữ liệu | Nullable | Khóa / Mặc định | Ghi chú / Enum |
| :--- | :--- | :---: | :--- | :--- |
| `id` | `bigint unsigned` | No | PRI (AUTO_INCREMENT) | - |
| `name` | `varchar(255)` | No |  (-) | - |
| `slug` | `varchar(255)` | No | UNI (-) | - |
| `description` | `varchar(255)` | Yes |  (`NULL`) | - |
| `created_at` | `timestamp` | Yes |  (`NULL`) | - |
| `updated_at` | `timestamp` | Yes |  (`NULL`) | - |

- **Khóa ngoại (FK)**: Không có
- **Ràng buộc Unique**: `slug` (categories_slug_unique)

### Bảng: `brands`
> Thương hiệu / Hãng sản xuất

| Tên cột | Kiểu dữ liệu | Nullable | Khóa / Mặc định | Ghi chú / Enum |
| :--- | :--- | :---: | :--- | :--- |
| `id` | `bigint unsigned` | No | PRI (AUTO_INCREMENT) | - |
| `name` | `varchar(255)` | No |  (-) | - |
| `slug` | `varchar(255)` | No | UNI (-) | - |
| `description` | `text` | Yes |  (`NULL`) | - |
| `logo_url` | `varchar(255)` | Yes |  (`NULL`) | - |
| `created_at` | `timestamp` | Yes |  (`NULL`) | - |
| `updated_at` | `timestamp` | Yes |  (`NULL`) | - |

- **Khóa ngoại (FK)**: Không có
- **Ràng buộc Unique**: `slug` (brands_slug_unique)

### Bảng: `products`
> Thông tin sản phẩm chính

| Tên cột | Kiểu dữ liệu | Nullable | Khóa / Mặc định | Ghi chú / Enum |
| :--- | :--- | :---: | :--- | :--- |
| `id` | `bigint unsigned` | No | PRI (AUTO_INCREMENT) | - |
| `name` | `varchar(255)` | No |  (-) | - |
| `slug` | `varchar(255)` | No | UNI (-) | - |
| `brand_id` | `bigint unsigned` | No | MUL (-) | - |
| `category_id` | `bigint unsigned` | No | MUL (-) | - |
| `is_active` | `tinyint(1)` | No |  (`1`) | 0: Ẩn / 1: Kích hoạt |
| `description` | `text` | Yes |  (`NULL`) | - |
| `specifications` | `json` | Yes |  (`NULL`) | - |
| `created_at` | `timestamp` | Yes |  (`NULL`) | - |
| `updated_at` | `timestamp` | Yes |  (`NULL`) | - |

- **Khóa ngoại (FK)**: `brand_id` $\rightarrow$ `brands.id`, `category_id` $\rightarrow$ `categories.id`
- **Ràng buộc Unique**: `slug` (products_slug_unique)

### Bảng: `product_variants`
> Phiên bản / Biến thể sản phẩm (SKU, giá, tồn kho)

| Tên cột | Kiểu dữ liệu | Nullable | Khóa / Mặc định | Ghi chú / Enum |
| :--- | :--- | :---: | :--- | :--- |
| `id` | `bigint unsigned` | No | PRI (AUTO_INCREMENT) | - |
| `product_id` | `bigint unsigned` | No | MUL (-) | - |
| `sku` | `varchar(255)` | No | UNI (-) | - |
| `price` | `decimal(12,2)` | No |  (-) | - |
| `stock` | `int` | No |  (`0`) | - |
| `image_url` | `varchar(255)` | Yes |  (`NULL`) | - |
| `sale_price` | `decimal(12,2)` | Yes |  (`NULL`) | - |
| `sale_starts_at` | `datetime` | Yes |  (`NULL`) | - |
| `sale_ends_at` | `datetime` | Yes |  (`NULL`) | - |
| `created_at` | `timestamp` | Yes |  (`NULL`) | - |
| `updated_at` | `timestamp` | Yes |  (`NULL`) | - |

- **Khóa ngoại (FK)**: `product_id` $\rightarrow$ `products.id`
- **Ràng buộc Unique**: `sku` (product_variants_sku_unique)

### Bảng: `product_images`
> Bộ sưu tập ảnh sản phẩm

| Tên cột | Kiểu dữ liệu | Nullable | Khóa / Mặc định | Ghi chú / Enum |
| :--- | :--- | :---: | :--- | :--- |
| `id` | `bigint unsigned` | No | PRI (AUTO_INCREMENT) | - |
| `product_id` | `bigint unsigned` | No | MUL (-) | - |
| `option_id` | `bigint unsigned` | Yes | MUL (`NULL`) | - |
| `image_url` | `varchar(255)` | No |  (-) | - |
| `is_primary` | `tinyint(1)` | No |  (`0`) | - |
| `created_at` | `timestamp` | Yes |  (`NULL`) | - |
| `updated_at` | `timestamp` | Yes |  (`NULL`) | - |

- **Khóa ngoại (FK)**: `option_id` $\rightarrow$ `product_attribute_options.id`, `product_id` $\rightarrow$ `products.id`
- **Ràng buộc Unique**: Không có

### Bảng: `product_attributes`
> Thuộc tính kỹ thuật (Phiên bản, Màu sắc...)

| Tên cột | Kiểu dữ liệu | Nullable | Khóa / Mặc định | Ghi chú / Enum |
| :--- | :--- | :---: | :--- | :--- |
| `id` | `bigint unsigned` | No | PRI (AUTO_INCREMENT) | - |
| `product_id` | `bigint unsigned` | No | MUL (-) | - |
| `name` | `varchar(255)` | No |  (-) | - |
| `created_at` | `timestamp` | Yes |  (`NULL`) | - |
| `updated_at` | `timestamp` | Yes |  (`NULL`) | - |

- **Khóa ngoại (FK)**: `product_id` $\rightarrow$ `products.id`
- **Ràng buộc Unique**: Không có

### Bảng: `product_attribute_options`
> Giá trị thuộc tính (128GB, Titan Tự Nhiên...)

| Tên cột | Kiểu dữ liệu | Nullable | Khóa / Mặc định | Ghi chú / Enum |
| :--- | :--- | :---: | :--- | :--- |
| `id` | `bigint unsigned` | No | PRI (AUTO_INCREMENT) | - |
| `product_attribute_id` | `bigint unsigned` | No | MUL (-) | - |
| `value` | `varchar(255)` | No |  (-) | - |
| `created_at` | `timestamp` | Yes |  (`NULL`) | - |
| `updated_at` | `timestamp` | Yes |  (`NULL`) | - |

- **Khóa ngoại (FK)**: `product_attribute_id` $\rightarrow$ `product_attributes.id`
- **Ràng buộc Unique**: Không có

### Bảng: `product_variant_options`
> Liên kết biến thể và giá trị thuộc tính

| Tên cột | Kiểu dữ liệu | Nullable | Khóa / Mặc định | Ghi chú / Enum |
| :--- | :--- | :---: | :--- | :--- |
| `variant_id` | `bigint unsigned` | No | PRI (-) | - |
| `option_id` | `bigint unsigned` | No | PRI (-) | - |

- **Khóa ngoại (FK)**: `option_id` $\rightarrow$ `product_attribute_options.id`, `variant_id` $\rightarrow$ `product_variants.id`
- **Ràng buộc Unique**: Không có

### Bảng: `carts`
> Giỏ hàng người dùng

| Tên cột | Kiểu dữ liệu | Nullable | Khóa / Mặc định | Ghi chú / Enum |
| :--- | :--- | :---: | :--- | :--- |
| `id` | `bigint unsigned` | No | PRI (AUTO_INCREMENT) | - |
| `user_id` | `bigint unsigned` | No | UNI (-) | - |
| `created_at` | `timestamp` | Yes |  (`NULL`) | - |
| `updated_at` | `timestamp` | Yes |  (`NULL`) | - |

- **Khóa ngoại (FK)**: `user_id` $\rightarrow$ `users.id`
- **Ràng buộc Unique**: `user_id` (carts_user_id_unique)

### Bảng: `cart_items`
> Chi tiết từng sản phẩm trong giỏ hàng

| Tên cột | Kiểu dữ liệu | Nullable | Khóa / Mặc định | Ghi chú / Enum |
| :--- | :--- | :---: | :--- | :--- |
| `id` | `bigint unsigned` | No | PRI (AUTO_INCREMENT) | - |
| `cart_id` | `bigint unsigned` | No | MUL (-) | - |
| `variant_id` | `bigint unsigned` | No | MUL (-) | - |
| `quantity` | `int` | No |  (`1`) | - |
| `created_at` | `timestamp` | Yes |  (`NULL`) | - |
| `updated_at` | `timestamp` | Yes |  (`NULL`) | - |

- **Khóa ngoại (FK)**: `cart_id` $\rightarrow$ `carts.id`, `variant_id` $\rightarrow$ `product_variants.id`
- **Ràng buộc Unique**: `cart_id` (cart_items_cart_id_variant_id_unique), `variant_id` (cart_items_cart_id_variant_id_unique)

### Bảng: `orders`
> Đơn hàng, giao dịch, vận chuyển

| Tên cột | Kiểu dữ liệu | Nullable | Khóa / Mặc định | Ghi chú / Enum |
| :--- | :--- | :---: | :--- | :--- |
| `id` | `bigint unsigned` | No | PRI (AUTO_INCREMENT) | - |
| `user_id` | `bigint unsigned` | No | MUL (-) | - |
| `address_id` | `bigint unsigned` | Yes | MUL (`NULL`) | - |
| `total_price` | `decimal(12,2)` | No |  (-) | - |
| `shipping_fee` | `decimal(10,2)` | No |  (`0.00`) | - |
| `ghn_order_code` | `varchar(255)` | Yes |  (`NULL`) | - |
| `expected_delivery_at` | `date` | Yes |  (`NULL`) | - |
| `status` | `varchar(255)` | No |  (-) | `pending`, `processing`, `shipped`, `delivered`, `cancelled` |
| `payment_method` | `varchar(255)` | No |  (`cod`) | `cod`, `vnpay`, `bank_transfer` |
| `payment_status` | `varchar(255)` | No |  (`unpaid`) | `unpaid`, `pending`, `paid`, `refunded`, `failed` |
| `payment_transaction_id` | `varchar(255)` | Yes |  (`NULL`) | - |
| `created_at` | `timestamp` | Yes |  (`NULL`) | - |
| `updated_at` | `timestamp` | Yes |  (`NULL`) | - |
| `coupon_id` | `bigint unsigned` | Yes | MUL (`NULL`) | - |
| `discount_amount` | `decimal(10,2)` | No |  (`0.00`) | - |

- **Khóa ngoại (FK)**: `address_id` $\rightarrow$ `addresses.id`, `coupon_id` $\rightarrow$ `coupons.id`, `user_id` $\rightarrow$ `users.id`
- **Ràng buộc Unique**: Không có

### Bảng: `order_items`
> Chi tiết sản phẩm đơn hàng & Serial/IMEI

| Tên cột | Kiểu dữ liệu | Nullable | Khóa / Mặc định | Ghi chú / Enum |
| :--- | :--- | :---: | :--- | :--- |
| `id` | `bigint unsigned` | No | PRI (AUTO_INCREMENT) | - |
| `order_id` | `bigint unsigned` | No | MUL (-) | - |
| `variant_id` | `bigint unsigned` | Yes | MUL (`NULL`) | - |
| `quantity` | `int` | No |  (-) | - |
| `serial_number` | `varchar(255)` | Yes |  (`NULL`) | - |
| `price` | `decimal(12,2)` | No |  (-) | - |
| `created_at` | `timestamp` | Yes |  (`NULL`) | - |
| `updated_at` | `timestamp` | Yes |  (`NULL`) | - |

- **Khóa ngoại (FK)**: `order_id` $\rightarrow$ `orders.id`, `variant_id` $\rightarrow$ `product_variants.id`
- **Ràng buộc Unique**: Không có

### Bảng: `coupons`
> Mã giảm giá / Voucher

| Tên cột | Kiểu dữ liệu | Nullable | Khóa / Mặc định | Ghi chú / Enum |
| :--- | :--- | :---: | :--- | :--- |
| `id` | `bigint unsigned` | No | PRI (AUTO_INCREMENT) | - |
| `code` | `varchar(255)` | No | UNI (-) | - |
| `type` | `enum('fixed','percentage')` | No |  (-) | `percentage`, `fixed` |
| `value` | `decimal(10,2)` | No |  (-) | - |
| `min_order_value` | `decimal(10,2)` | Yes |  (`NULL`) | - |
| `max_discount` | `decimal(10,2)` | Yes |  (`NULL`) | - |
| `usage_limit` | `int` | Yes |  (`NULL`) | - |
| `used_count` | `int` | No |  (`0`) | - |
| `expires_at` | `date` | Yes |  (`NULL`) | - |
| `is_active` | `tinyint(1)` | No |  (`1`) | 0: Ẩn / 1: Kích hoạt |
| `created_at` | `timestamp` | Yes |  (`NULL`) | - |
| `updated_at` | `timestamp` | Yes |  (`NULL`) | - |

- **Khóa ngoại (FK)**: Không có
- **Ràng buộc Unique**: `code` (coupons_code_unique)

### Bảng: `addresses`
> Sổ địa chỉ nhận hàng của khách

| Tên cột | Kiểu dữ liệu | Nullable | Khóa / Mặc định | Ghi chú / Enum |
| :--- | :--- | :---: | :--- | :--- |
| `id` | `bigint unsigned` | No | PRI (AUTO_INCREMENT) | - |
| `user_id` | `bigint unsigned` | No | MUL (-) | - |
| `name` | `varchar(255)` | No |  (-) | - |
| `phone` | `varchar(11)` | No |  (-) | - |
| `address` | `varchar(255)` | No |  (-) | - |
| `city` | `varchar(255)` | No |  (-) | - |
| `ward_code` | `varchar(255)` | Yes |  (`NULL`) | - |
| `district_id` | `int` | Yes |  (`NULL`) | - |
| `province_id` | `int` | Yes |  (`NULL`) | - |
| `is_default` | `tinyint(1)` | No |  (`0`) | - |
| `created_at` | `timestamp` | Yes |  (`NULL`) | - |
| `updated_at` | `timestamp` | Yes |  (`NULL`) | - |

- **Khóa ngoại (FK)**: `user_id` $\rightarrow$ `users.id`
- **Ràng buộc Unique**: Không có

### Bảng: `warranty_claims`
> Yêu cầu bảo hành thiết bị

| Tên cột | Kiểu dữ liệu | Nullable | Khóa / Mặc định | Ghi chú / Enum |
| :--- | :--- | :---: | :--- | :--- |
| `id` | `bigint unsigned` | No | PRI (AUTO_INCREMENT) | - |
| `claim_code` | `varchar(255)` | No | UNI (-) | - |
| `user_id` | `bigint unsigned` | Yes | MUL (`NULL`) | - |
| `order_id` | `bigint unsigned` | Yes | MUL (`NULL`) | - |
| `customer_name` | `varchar(255)` | No |  (-) | - |
| `customer_phone` | `varchar(255)` | No |  (-) | - |
| `customer_email` | `varchar(255)` | Yes |  (`NULL`) | - |
| `product_name` | `varchar(255)` | No |  (-) | - |
| `serial_number` | `varchar(255)` | Yes |  (`NULL`) | - |
| `purchase_date` | `date` | Yes |  (`NULL`) | - |
| `issue_description` | `text` | No |  (-) | - |
| `status` | `enum('pending','received','inspecting','completed','rejected')` | No |  (`pending`) | `pending`, `inspecting`, `completed`, `rejected` |
| `admin_note` | `text` | Yes |  (`NULL`) | - |
| `resolved_at` | `timestamp` | Yes |  (`NULL`) | - |
| `created_at` | `timestamp` | Yes |  (`NULL`) | - |
| `updated_at` | `timestamp` | Yes |  (`NULL`) | - |

- **Khóa ngoại (FK)**: `order_id` $\rightarrow$ `orders.id`, `user_id` $\rightarrow$ `users.id`
- **Ràng buộc Unique**: `claim_code` (warranty_claims_claim_code_unique)

### Bảng: `product_reviews`
> Đánh giá & sao sản phẩm

| Tên cột | Kiểu dữ liệu | Nullable | Khóa / Mặc định | Ghi chú / Enum |
| :--- | :--- | :---: | :--- | :--- |
| `id` | `bigint unsigned` | No | PRI (AUTO_INCREMENT) | - |
| `product_id` | `bigint unsigned` | No | MUL (-) | - |
| `user_id` | `bigint unsigned` | Yes | MUL (`NULL`) | - |
| `customer_name` | `varchar(255)` | No |  (-) | - |
| `customer_phone` | `varchar(255)` | Yes |  (`NULL`) | - |
| `rating` | `tinyint` | No |  (`5`) | 1 đến 5 sao |
| `content` | `text` | No |  (-) | - |
| `admin_reply` | `text` | Yes |  (`NULL`) | - |
| `replied_at` | `timestamp` | Yes |  (`NULL`) | - |
| `is_verified_purchase` | `tinyint(1)` | No |  (`1`) | - |
| `is_active` | `tinyint(1)` | No |  (`1`) | 0: Ẩn / 1: Kích hoạt |
| `created_at` | `timestamp` | Yes |  (`NULL`) | - |
| `updated_at` | `timestamp` | Yes |  (`NULL`) | - |

- **Khóa ngoại (FK)**: `product_id` $\rightarrow$ `products.id`, `user_id` $\rightarrow$ `users.id`
- **Ràng buộc Unique**: Không có

### Bảng: `product_questions`
> Hỏi đáp Q&A sản phẩm

| Tên cột | Kiểu dữ liệu | Nullable | Khóa / Mặc định | Ghi chú / Enum |
| :--- | :--- | :---: | :--- | :--- |
| `id` | `bigint unsigned` | No | PRI (AUTO_INCREMENT) | - |
| `product_id` | `bigint unsigned` | No | MUL (-) | - |
| `user_id` | `bigint unsigned` | Yes | MUL (`NULL`) | - |
| `customer_name` | `varchar(255)` | No |  (-) | - |
| `customer_phone` | `varchar(255)` | Yes |  (`NULL`) | - |
| `question` | `text` | No |  (-) | - |
| `admin_answer` | `text` | Yes |  (`NULL`) | - |
| `answered_at` | `timestamp` | Yes |  (`NULL`) | - |
| `is_active` | `tinyint(1)` | No |  (`1`) | 0: Ẩn / 1: Kích hoạt |
| `created_at` | `timestamp` | Yes |  (`NULL`) | - |
| `updated_at` | `timestamp` | Yes |  (`NULL`) | - |

- **Khóa ngoại (FK)**: `product_id` $\rightarrow$ `products.id`, `user_id` $\rightarrow$ `users.id`
- **Ràng buộc Unique**: Không có

### Bảng: `notifications`
> Thông báo hệ thống trong ứng dụng

| Tên cột | Kiểu dữ liệu | Nullable | Khóa / Mặc định | Ghi chú / Enum |
| :--- | :--- | :---: | :--- | :--- |
| `id` | `char(36)` | No | PRI (-) | - |
| `type` | `varchar(255)` | No |  (-) | - |
| `notifiable_type` | `varchar(255)` | No | MUL (-) | - |
| `notifiable_id` | `bigint unsigned` | No |  (-) | - |
| `data` | `text` | No |  (-) | - |
| `read_at` | `timestamp` | Yes |  (`NULL`) | - |
| `created_at` | `timestamp` | Yes |  (`NULL`) | - |
| `updated_at` | `timestamp` | Yes |  (`NULL`) | - |

- **Khóa ngoại (FK)**: Không có
- **Ràng buộc Unique**: Không có


---

## 2. SEEDERS VÀ FACTORIES HIỆN CÓ

### 2.1 Thứ tự thực thi trong `DatabaseSeeder.php`

Khi chạy lệnh `php artisan db:seed`, `DatabaseSeeder` gọi tuần tự 5 Seeder chính:

1. **`CategorySeeder::class`**: Nạp danh mục sản phẩm gốc.
2. **`BrandSeeder::class`**: Nạp danh sách thương hiệu sản xuất.
3. **`UserSeeder::class`**: Tạo tài khoản admin và khách hàng mẫu mặc định.
4. **`ProductSeeder::class`**: Truncate sạch các bảng sản phẩm/biến thể và điều phối 5 Sub-Seeder catalog chuyên biệt:
   - `SmartphoneSeeder::class`: Nạp danh mục Điện thoại thông minh.
   - `LaptopSeeder::class`: Nạp danh mục Laptop cao cấp & gaming.
   - `AccessorySeeder::class`: Nạp Chuột, Bàn phím cơ & Màn hình đồ họa.
   - `AudioSeeder::class`: Nạp Tai nghe không dây & Thiết bị âm thanh.
   - `SmartwatchSeeder::class`: Nạp Đồng hồ thông minh & Smartwatch thể thao.
5. **`AdminDummySeeder::class`**: Nạp dữ liệu giả lập cho Admin Dashboard (Voucher, Đơn hàng 90 ngày, Yêu cầu bảo hành).

### 2.2 Chi tiết từng Seeder

| Tên Seeder | Dữ liệu khởi tạo | Có dùng Faker? | Locale / Nguồn dữ liệu |
| :--- | :--- | :---: | :--- |
| `CategorySeeder.php` | 8 danh mục hàng công nghệ (Điện thoại, Laptop, Bàn phím, Chuột...) | **Không** | Mảng tĩnh tiếng Việt thủ công |
| `BrandSeeder.php` | 23 hãng công nghệ (Apple, Samsung, Dell, Asus, Logitech...) | **Không** | Mảng tĩnh thủ công |
| `UserSeeder.php` | 1 Admin (`admin@techshop.com`), 1 Khách hàng (`customer@techshop.com`) | **Không** | Dữ liệu cứng thủ công |
| `ProductSeeder.php` | Truncate dọn dẹp các bảng phụ thuộc và gọi 5 Seeder ngành hàng | **Không** | Điều phối | 
| `SmartphoneSeeder.php` | 14 điện thoại kèm biến thể màu sắc, RAM/ROM, thông số kỹ thuật JSON | **Không** | Dữ liệu tĩnh thật kèm URL ảnh Unsplash |
| `LaptopSeeder.php` | 15 laptop đồ họa & gaming kèm biến thể RAM, SSD, cấu hình | **Không** | Dữ liệu tĩnh thật kèm URL ảnh Unsplash |
| `AccessorySeeder.php` | 11 phụ kiện: chuột không dây, phím cơ switch, màn hình 2K/4K | **Không** | Dữ liệu tĩnh thật kèm URL ảnh Unsplash |
| `AudioSeeder.php` | 6 sản phẩm tai nghe chống ồn, loa Bluetooth Marshall/Sony | **Không** | Dữ liệu tĩnh thật kèm URL ảnh Unsplash |
| `SmartwatchSeeder.php` | 6 đồng hồ thông minh Apple Watch, Galaxy Watch, Garmin | **Không** | Dữ liệu tĩnh thật kèm URL ảnh Unsplash |
| `AdminDummySeeder.php` | 5 mã coupon, 28 đơn hàng trải rộng 90 ngày, 4 phiếu bảo hành | **Không** | Dữ liệu giả lập random ngày & số liệu |

### 2.3 Chi tiết Factory

- **File hiện có**: Duy nhất `database/factories/UserFactory.php`.
- **Cách hoạt động**: Sử dụng `fake()->name()`, `fake()->unique()->safeEmail()` để sinh user ngẫu nhiên.
- **Locale**: `en_US` (cấu hình mặc định trong `config/app.php`: `APP_FAKER_LOCALE=en_US`).

---

## 3. DỮ LIỆU THỰC TẾ TRONG DATABASE

### 3.1 Số lượng bản ghi từng bảng (Record Count)

| STT | Bảng dữ liệu | Số bản ghi | Mô tả |
| :---: | :--- | :---: | :--- |
| 1 | `users` | **10** | Hoạt động |
| 2 | `categories` | **8** | Hoạt động |
| 3 | `brands` | **23** | Hoạt động |
| 4 | `products` | **52** | Hoạt động |
| 5 | `product_attributes` | **52** | Hoạt động |
| 6 | `product_attribute_options` | **95** | Hoạt động |
| 7 | `product_variants` | **95** | Hoạt động |
| 8 | `product_variant_options` | **95** | Hoạt động |
| 9 | `product_images` | **95** | Hoạt động |
| 10 | `carts` | **3** | Hoạt động |
| 11 | `cart_items` | **1** | Hoạt động |
| 12 | `orders` | **28** | Hoạt động |
| 13 | `order_items` | **28** | Hoạt động |
| 14 | `coupons` | **5** | Hoạt động |
| 15 | `addresses` | **4** | Hoạt động |
| 16 | `warranty_claims` | **4** | Hoạt động |
| 17 | `product_reviews` | **14** | Hoạt động |
| 18 | `product_questions` | **6** | Hoạt động |
| 19 | `notifications` | **1** | Hoạt động |
| 20 | `personal_access_tokens` | **7** | Hoạt động |

### 3.2 Toàn bộ Danh mục (Categories)

| ID | Tên danh mục | Slug | Mô tả | Ngày tạo |
| :---: | :--- | :--- | :--- | :--- |
| 1 | **Chuột máy tính** | `chuot` | Chuột gaming độ nhạy cao, chuột công thái học văn phòng Bluetooth. | 2026-06-06 15:26:12 |
| 2 | **Bàn phím cơ** | `ban-phim` | Bàn phím cơ gaming không dây, switch cơ học cao cấp gõ êm mượt. | 2026-06-06 15:26:12 |
| 3 | **Tai nghe** | `tai-nghe` | _Không có mô tả_ | 2026-06-06 15:26:12 |
| 4 | **Màn hình** | `man-hinh` | Màn hình máy tính gaming, đồ họa sắc nét 2K, 4K, 144Hz, 165Hz. | 2026-06-06 15:26:12 |
| 5 | **Laptop** | `laptop` | Laptop gaming đồ họa, laptop mỏng nhẹ doanh nhân MacBook, Asus ROG, Dell XPS. | 2026-06-06 15:26:12 |
| 6 | **Điện thoại** | `dien-thoai` | Điện thoại thông minh chính hãng Apple, Samsung, Xiaomi, OPPO giá tốt nhất. | 2026-11-02 15:41:47 |
| 7 | **Đồng hồ thông minh** | `dong-ho-thong-minh` | Smartwatch theo dõi sức khỏe và thể thao đỉnh cao: Apple Watch, Galaxy Watch, Garmin. | 2026-11-02 15:41:47 |
| 8 | **Tai nghe & Âm thanh** | `tai-nghe-am-thanh` | Tai nghe không dây Bluetooth, tai nghe chụp tai chống ồn chủ động Sony, Marshall, AirPods. | 2026-11-02 15:41:47 |

### 3.3 Danh sách 15 sản phẩm đầu tiên (First 15 Products)

| ID | Tên sản phẩm | Danh mục | Giá khởi điểm | Khoảng giá biến thể | Tổng tồn kho | Có ảnh? | Trạng thái |
| :---: | :--- | :--- | :---: | :---: | :---: | :---: | :---: |
| 1 | **iPhone 16 Pro Max** | Điện thoại | 34.990.000 đ | 34.990.000 - 46.990.000 đ | 145 | Có | Active |
| 2 | **iPhone 16 Pro** | Điện thoại | 28.990.000 đ | 28.990.000 - 37.990.000 đ | 100 | Có | Active |
| 3 | **iPhone 16** | Điện thoại | 22.490.000 đ | 22.490.000 - 25.490.000 đ | 155 | Có | Active |
| 4 | **iPhone 15 Pro Max** | Điện thoại | 29.490.000 đ | 29.490.000 - 35.490.000 đ | 50 | Có | Active |
| 5 | **iPhone 13** | Điện thoại | 13.490.000 đ | 13.490.000 đ | 135 | Có | Active |
| 6 | **Samsung Galaxy S24 Ultra 5G** | Điện thoại | 29.990.000 đ | 29.990.000 - 34.490.000 đ | 110 | Có | Active |
| 7 | **Samsung Galaxy Z Fold 6 5G** | Điện thoại | 41.990.000 đ | 41.990.000 - 45.990.000 đ | 40 | Có | Active |
| 8 | **Samsung Galaxy Z Flip 6 5G** | Điện thoại | 26.990.000 đ | 26.990.000 đ | 55 | Có | Active |
| 9 | **Samsung Galaxy A55 5G** | Điện thoại | 9.690.000 đ | 9.690.000 - 10.490.000 đ | 95 | Có | Active |
| 10 | **Xiaomi 14 Ultra 5G Leica** | Điện thoại | 29.990.000 đ | 29.990.000 đ | 45 | Có | Active |
| 11 | **Xiaomi 14 5G** | Điện thoại | 19.990.000 đ | 19.990.000 đ | 75 | Có | Active |
| 12 | **Xiaomi Redmi Note 13 Pro+ 5G** | Điện thoại | 9.990.000 đ | 9.990.000 đ | 105 | Có | Active |
| 13 | **OPPO Find N3 Flip** | Điện thoại | 22.990.000 đ | 22.990.000 đ | 45 | Có | Active |
| 14 | **OPPO Reno 12 Pro 5G** | Điện thoại | 15.990.000 đ | 15.990.000 đ | 75 | Có | Active |
| 15 | **MacBook Pro 16 inch Chip Apple M3 Max** | Laptop | 89.990.000 đ | 89.990.000 - 102.990.000 đ | 25 | Có | Active |

### 3.4 Thống kê sản phẩm (Product Metrics)

- **Giá thấp nhất (Min price)**: 2.290.000 đ
- **Giá cao nhất (Max price)**: 102.990.000 đ
- **Giá trung bình (Avg price)**: 22.959.474 đ
- **Số sản phẩm có giá = 0**: `0`
- **Số sản phẩm / biến thể có tồn kho = 0**: `0`
- **Số sản phẩm / biến thể có tồn kho âm (< 0)**: `0`
- **Số sản phẩm không có biến thể**: `0`
- **Số sản phẩm không có ảnh đại diện / ảnh gallery**: `0`
- **Số sản phẩm có tên bị trùng lặp**: `0`

### 3.5 Toàn bộ Mã giảm giá (Coupons)

| ID | Mã Code | Loại | Giá trị | Đơn tối thiểu | Giảm tối đa | Hạn sử dụng | Đã dùng / Giới hạn | Kích hoạt |
| :---: | :--- | :---: | :---: | :---: | :---: | :---: | :---: | :---: |
| 1 | `TECHVINA10` | `percentage` | 10% | 500.000 đ | 200.000 đ | 2026-12-03 | 18 / 100 | ✅ Bật |
| 2 | `TECHVINA50K` | `fixed` | 50.000 đ | 300.000 đ | Không giới hạn | 2027-01-02 | 45 / 200 | ✅ Bật |
| 3 | `GAMINGVIP` | `percentage` | 15% | 2.000.000 đ | 500.000 đ | 2026-11-18 | 12 / 50 | ✅ Bật |
| 4 | `SUMMERSALE` | `percentage` | 5% | 100.000 đ | 100.000 đ | 2026-10-29 | 120 / 500 | ❌ Tắt |
| 5 | `FREESHIP` | `fixed` | 30.000 đ | 400.000 đ | Không giới hạn | 2026-12-18 | 88 / 300 | ✅ Bật |

### 3.6 Thống kê Người dùng (Users)

- **Phân bố số lượng theo Role**:
  - Role `admin`: **1** tài khoản
  - Role `customer`: **9** tài khoản
- **Danh sách tài khoản Admin**:
  - ID: `1` | Tên: **Admin** | Email: `admin@techshop.com` | Ngày tạo: `2026-06-06 15:26:13`

### 3.7 Thống kê Đơn hàng (Orders)

- **Phân bố trạng thái đơn hàng**:
  - Trạng thái `delivered`: **16** đơn hàng
  - Trạng thái `processing`: **4** đơn hàng
  - Trạng thái `shipped`: **4** đơn hàng
  - Trạng thái `pending`: **2** đơn hàng
  - Trạng thái `cancelled`: **2** đơn hàng
- **Khoảng thời gian đơn hàng**:
  - Đơn cũ nhất: `2026-08-10 21:32:28`
  - Đơn mới nhất: `2026-11-03 07:32:28`
- **Số đơn mỗi ngày trong 30 ngày gần nhất**:

| Ngày đặt | Số lượng đơn hàng |
| :---: | :---: |
| `2026-10-12` | **1** đơn |
| `2026-10-13` | **3** đơn |
| `2026-10-16` | **1** đơn |
| `2026-10-18` | **1** đơn |
| `2026-10-19` | **1** đơn |
| `2026-10-21` | **1** đơn |
| `2026-10-26` | **1** đơn |
| `2026-10-27` | **1** đơn |
| `2026-10-28` | **2** đơn |
| `2026-10-29` | **2** đơn |
| `2026-10-30` | **1** đơn |
| `2026-10-31` | **1** đơn |
| `2026-11-01` | **2** đơn |
| `2026-11-02` | **2** đơn |
| `2026-11-03` | **3** đơn |

- **Kiểm tra tính toàn vẹn đơn hàng**:
  - Số đơn hàng không có `order_items`: **0** đơn
  - Số đơn hàng có tổng tiền (`total_price`) không khớp tổng items + ship - coupon: **0** đơn

### 3.8 Kiểm tra Tồn kho so với Đã bán (Stock vs Sales Audit)

- **Top 10 biến thể bán chạy nhất trong DB**:

| Variant ID | SKU | Tên sản phẩm | Tồn kho hiện tại | Đã bán (đơn hàng) |
| :---: | :--- | :--- | :---: | :---: |
| 60 | `RAZER-DAV3P-WHITE` | Chuột Gaming Không Dây Razer DeathAdder V3 Pro | 30 | **4** |
| 2 | `IP16PM-512-NATURAL` | iPhone 16 Pro Max | 35 | **3** |
| 65 | `LOGI-GPXTKL-WHT` | Bàn Phím Cơ Gaming Logitech G Pro X TKL Lightspeed | 25 | **3** |
| 79 | `MARSHALL-M4-BLACK` | Tai Nghe On-Ear Không Dây Marshall Major IV Bluetooth | 50 | **3** |
| 88 | `AW-S9-45-MID` | Đồng Hồ Apple Watch Series 9 GPS 45mm | 35 | **3** |
| 12 | `IP15PM-512-BLUE` | iPhone 15 Pro Max | 20 | **2** |
| 43 | `DELL-XPS15-16-512` | Laptop Dell XPS 15 9530 OLED | 20 | **2** |
| 14 | `IP13-128-WHITE` | iPhone 13 | 65 | **2** |
| 33 | `OPPO-R12P-BROWN` | OPPO Reno 12 Pro 5G | 35 | **2** |
| 28 | `RN13P-256-PURPLE` | Xiaomi Redmi Note 13 Pro+ 5G | 55 | **2** |

- **Biến thể tồn kho = 0 nhưng có phát sinh bán**: 0 biến thể
- **Biến thể tồn kho âm (< 0)**: 0 biến thể

---

## 4. CÁC VẤN ĐỀ VÀ DẤU HIỆU DỮ LIỆU BẤT THƯỜNG PHÁT HIỆN ĐƯỢC

- **Dữ liệu người dùng lẫn lộn giữa Tiếng Anh (Faker) và Tiếng Việt thực tế**:
  - Có 5 tài khoản mẫu từ thời kỳ khởi tạo sơ khai sinh bởi Faker `en_US` với tên nước ngoài như `Prof. Carol Runolfsdottir III`, `Hailie Smitham`, `Dustin Padberg` (ID từ 2 đến 6).
  - Lẫn với các tài khoản tiếng Việt thực tế như `Cao Lương Thiện`, `Thiện Cao Lương`, `Nguyễn Văn Khách`.

- **Toàn bộ 28 đơn hàng trong DB đều thuộc về duy nhất 1 khách hàng**:
  - 100% đơn hàng (28/28 đơn) đều được gắn cứng vào `user_id = 9` (`Nguyễn Văn Khách`) và `address_id = 5` (`Số 123 Đường Cầu Giấy, Hà Nội`).
  - Các khách hàng khác trong DB không sở hữu bất kỳ đơn hàng nào.

- **Mã giảm giá cũ chưa đồng bộ với Seeder mới**:
  - Trong Database, 2 mã coupon đầu tiên vẫn mang tiền tố thương hiệu cũ `TECHVINA10` và `TECHVINA50K` do dữ liệu được nạp từ Seeder đợt trước và chưa được migrate/seed lại.

- **Trùng lặp và thừa danh mục `Tai nghe` (Category ID 3)**:
  - Trong DB tồn tại cùng lúc 2 danh mục: ID 3 (`Tai nghe`, slug `tai-nghe`, 0 sản phẩm, không có mô tả) và ID 8 (`Tai nghe & Âm thanh`, slug `tai-nghe-am-thanh`, 8 sản phẩm, có mô tả chi tiết).
  - Danh mục ID 3 hoàn toàn rác/bị bỏ trống.

- **Thương hiệu có tới 7 hãng rỗng (0 sản phẩm)**:
  - Bảng `brands` có 23 hãng nhưng có tới 7 hãng không liên kết với bất kỳ sản phẩm nào: `Vivo`, `Realme`, `Honor`, `HP`, `Acer`, `Huawei`, `LG`.

- **Nội dung Đánh giá (Reviews) bị gán sai bản chất sản phẩm**:
  - Đánh giá của Product ID 4 (`iPhone 15 Pro Max`) và Product ID 8 (`Samsung Galaxy Z Flip 6 5G`) lại chứa nội dung: _"Bàn phím gõ rất đầm tay, âm thanh gõ đanh gọn không bị vang kim loại"_ (do Seeder gán vòng lặp review bàn phím cơ sang sản phẩm điện thoại).
  - Nhiều review và hỏi đáp có cùng chính xác giây khởi tạo (`15:32:28`).

- **Số Serial bảo hành không khớp với Serial trong đơn hàng đã mua**:
  - 4 phiếu yêu cầu bảo hành mẫu trong bảng `warranty_claims` mang các mã Serial cố định (`LT-9821034`, `AS-5544211`, `AK-1100223`, `HX-7788990`).
  - Nhưng trong bảng `order_items`, serial được sinh ngẫu nhiên dạng `SN-XXXXXXXXXX`. Do đó không có bất kỳ phiếu bảo hành nào tra cứu ra thông tin thiết bị đã mua tương ứng.

- **Mỗi đơn hàng chỉ có duy nhất 1 item**:
  - Cả 28 đơn hàng đều chỉ chứa đúng 1 bản ghi `order_items` (`count(orders) == 28` và `count(order_items) == 28`), không có đơn hàng nào mua giỏ hàng đa sản phẩm.
