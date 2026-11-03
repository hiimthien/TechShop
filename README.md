# 🛒 TechShop E-Commerce System

> Hệ thống thương mại điện tử thiết bị công nghệ bán lẻ đa kênh toàn diện (Full-stack Retail Tech E-Commerce & CMS) được xây dựng trên nền tảng **Laravel 11 REST API** và **Vue 3 (Composition API, TypeScript, Vite, Tailwind CSS v4, Pinia)**.

---

## 🌟 Tính Năng Nổi Bật (Key Features)

### 1. Trải Nghiệm Khách Hàng (Storefront)
* **Trang chủ hiện đại phong cách bán lẻ công nghệ hàng đầu**:
  - **Hero Slider Carousel**: Trình chiếu banner khuyến mãi tự động với khả năng tạm dừng khi hover, điều hướng mũi tên và chấm chỉ số trực quan.
  - **Cam kết dịch vụ (Trust Badges)**: 100% Hàng chính hãng, Giao siêu tốc 2H, Lỗi 1 đổi 1 30 ngày, Trả góp 0% lãi suất.
  - **Flash Sale Giá Sốc**: Đồng hồ đếm ngược Realtime (Giờ : Phút : Giây), huy hiệu giảm giá nổi bật và thanh tiến độ số lượng bán chạy.
  - **Khối danh mục chuyên biệt (Category Blocks)**: Banner poster độc quyền cùng thanh Tab chuyển nhanh thương hiệu (Apple, Samsung, Dell, Asus, Logitech...) lọc sản phẩm tức thì không reload trang.
  - **Góc công nghệ & Đánh giá**: Chuyên mục tin tức, cẩm nang tư vấn chọn mua và mẹo bảo hành sản phẩm.
* **Bộ lọc & Tìm kiếm sản phẩm thông minh**:
  - Lọc đa tiêu chí: Danh mục ngành hàng, thương hiệu, khoảng giá, tình trạng hàng.
  - Thanh tìm kiếm realtime hỗ trợ tìm theo tên thiết bị, dòng máy.
* **Chi tiết sản phẩm đa biến thể (Product Details)**:
  - Thư viện hình ảnh sắc nét, phóng to ảnh và chọn thumbnail linh hoạt.
  - Bộ chọn phiên bản cấu hình / dung lượng (128GB, 256GB, 512GB, 1TB...) và màu sắc tương ứng.
  - Đổi ảnh chính tự động theo màu sắc đang chọn.
  - Đánh giá sản phẩm (Reviews 1-5 sao) có xác thực mua hàng (`Verified Purchase`) và phản hồi chăm sóc khách hàng từ Admin.
  - Hỏi đáp (Q&A) tương tác trực tiếp với chuyên viên kỹ thuật.
* **Giỏ hàng & Đặt hàng nguyên tử (Cart & Checkout)**:
  - Đồng bộ giỏ hàng linh hoạt giữa LocalStorage và cơ sở dữ liệu khi đăng nhập.
  - Trừ kho nguyên tử (`Atomic lock/decrement`), bảo vệ chống đặt vượt quá số lượng kho khả dụng.
  - Áp dụng mã giảm giá (Coupon): Giảm theo % (có hạn mức trần) hoặc giảm số tiền cố định.
  - Lưu vết giá lịch sử (`price snapshot`) trên từng món hàng.
* **Cổng thanh toán trực tuyến**:
  - Tích hợp cổng thanh toán trực tuyến **VNPay (VNPay-QR, ATM nội địa, Thẻ quốc tế)** chuẩn mã hóa bảo mật SHA512 checksum kèm IPN & Redirect URL.
  - Hỗ trợ phương thức thanh toán khi nhận hàng (**COD**).
* **Bảo hành & Tra cứu điện tử (RMA System)**:
  - Tra cứu tiến độ bảo hành theo mã phiếu (VD: `BH-XXXXXX`), số điện thoại hoặc số Serial máy.
  - Gửi yêu cầu bảo hành trực tuyến kèm mô tả tình trạng lỗi.
* **Tài khoản người dùng (Account Management)**:
  - Quản lý thông tin cá nhân, sổ địa chỉ giao hàng mặc định.
  - Lịch sử đơn hàng chi tiết từng món đồ, trạng thái giao vận GHN và hóa đơn.
  - Đăng nhập chuẩn Email/Mật khẩu hoặc **Google OAuth 2.0 Single Sign-On (SSO)**.

---

### 2. Quản Trị Hệ Thống (Admin CMS)
* **Bảng điều khiển kinh doanh (Analytics Dashboard)**:
  - Lọc theo mốc thời gian đa dạng: Hôm nay (`today`), 7 ngày qua (`7days`), 30 ngày qua (`30days`), Tháng này (`this_month`), Năm nay (`this_year`), Toàn thời gian (`all`) và Tùy chỉnh ngày (`from_date` - `to_date`).
  - Thống kê 4 chỉ số cốt lõi: Doanh thu thực tế, Tổng đơn hàng, Khách hàng mới, Giá trị trung bình mỗi đơn (AOV).
  - Biểu đồ tăng trưởng doanh thu theo ngày/tháng (tương thích đa cơ sở dữ liệu MySQL / SQLite).
  - Lọc nhanh danh sách đơn hàng gần đây theo trạng thái thanh toán và vận chuyển.
* **Quản lý danh mục & thương hiệu (Categories & Brands)**:
  - Thêm, sửa, xóa danh mục và hãng sản xuất có upload Logo.
  - Ràng buộc an toàn: Chặn xóa danh mục/thương hiệu khi đang có sản phẩm liên kết.
  - Tự động xóa cache MegaMenu và Brand List (`Cache::forget`) khi có thay đổi.
* **Quản lý sản phẩm & biến thể (Products & Variants)**:
  - Thêm mới, chỉnh sửa sản phẩm, thông số kỹ thuật (`specifications`), danh mục, thương hiệu.
  - Quản lý đa biến thể: Tên màu, SKU, giá bán, tồn kho và hình ảnh riêng biệt cho từng biến thể.
  - Hỗ trợ tải lên nhiều file ảnh cùng lúc qua `multipart/form-data`.
* **Quản lý đơn hàng (Orders Workflow)**:
  - Phân trang, tìm kiếm theo tên khách hàng, số điện thoại, mã vận đơn GHN.
  - Cập nhật trạng thái đơn hàng: Chờ xác nhận $\rightarrow$ Đang xử lý $\rightarrow$ Đang giao $\rightarrow$ Đã giao $\rightarrow$ Đã hủy.
  - Quản lý trạng thái thanh toán: Chưa thanh toán $\rightarrow$ Đã thanh toán $\rightarrow$ Hoàn tiền.
  - Tự động cấp phát số Serial cho từng sản phẩm trong đơn hàng.
* **Quản lý khuyến mãi (Coupons)**:
  - Tạo mã giảm giá theo số tiền hoặc phần trăm, thời hạn hiệu lực, giá trị đơn tối thiểu, giới hạn lượt dùng và bật/tắt nhanh.
* **Quản lý bảo hành & sửa chữa (Warranty Claims)**:
  - Tiếp nhận yêu cầu bảo hành, phân loại tình trạng kiểm tra kỹ thuật (`pending`, `inspecting`, `completed`, `rejected`), cập nhật ghi chú nội bộ.

---

## 🏛️ Kiến Trúc Kỹ Thuật (Architecture)

### Backend (Laravel 11 - Enterprise Layered Pattern)
```
backend/app/
├── Http/
│   ├── Controllers/Api/       # Thin Controllers điều phối request và trả JSON
│   ├── Requests/Admin/        # Form Requests xác thực đầu vào & phân quyền
│   └── Resources/             # API Resources định dạng cấu trúc JSON đồng nhất
├── Services/                  # Service Layer chứa nghiệp vụ & Transaction nguyên tử
├── Repositories/
│   ├── Contracts/             # Interfaces trừu tượng hóa truy vấn dữ liệu
│   └── Eloquent/              # Triển khai truy vấn Eloquent ORM tách biệt
└── Models/                    # Eloquent Models & Relationships
```

### Frontend (Vue 3 - Modular / Feature-Driven Architecture)
```
frontend/src/
├── core/
│   ├── components/ui/         # Base UI components (BaseModal, BaseButton, BaseAlert...)
│   ├── composables/           # Shared composables (useFormatters, useToast...)
│   └── services/              # Axios client với Interceptors & JWT Token handler
└── modules/
    ├── home/                  # Banner Slider, Trust Badges, Flash Sale, Category Blocks
    ├── products/              # Danh sách lọc, Chi tiết sản phẩm, Gallery, Reviews & Q&A
    ├── cart/                  # Giỏ hàng & Pinia Cart Store
    ├── checkout/              # Thanh toán, VNPay Gateway & xác thực đơn hàng
    ├── admin/                 # Dashboard Analytics, CRUD Products, Categories, Orders...
    ├── account/               # Quản lý hồ sơ, đơn mua, lịch sử bảo hành
    └── auth/                  # Đăng nhập, Đăng ký, Google OAuth SSO Callback
```

---

## ⚙️ Hướng Dẫn Cài Đặt & Khởi Chạy

### 1. Yêu cầu môi trường
* **PHP**: `>= 8.2` (Khuyên dùng PHP 8.3)
* **Node.js**: `>= 20.x`
* **Composer**: `>= 2.x`
* **MySQL**: `>= 8.0` hoặc MariaDB `>= 10.4`

---

### 2. Cài đặt Backend (Laravel)

```bash
cd backend

# 1. Cài đặt thư viện PHP
composer install

# 2. Thiết lập cấu hình môi trường .env
cp .env.example .env
php artisan key:generate

# 3. Tạo symlink liên kết thư mục lưu trữ media
php artisan storage:link

# 4. Chạy migration và nạp bộ dữ liệu chuẩn
php artisan migrate --seed

# 5. Xóa cache ứng dụng
php artisan cache:clear

# 6. Khởi động máy chủ backend
php artisan serve
```
*Backend API chạy tại:* `http://localhost:8000`

---

### 3. Cài đặt Frontend (Vue 3)

```bash
cd frontend

# 1. Cài đặt các gói phụ thuộc
npm install

# 2. Khởi chạy môi trường phát triển (Dev server)
npm run dev
```
*Frontend ứng dụng chạy tại:* `http://localhost:5173`

---

## 🔑 Tài Khoản Truy Cập Mặc Định

| Vai trò | Email đăng nhập | Mật khẩu | Quyền hạn |
| :--- | :--- | :--- | :--- |
| **Quản trị viên (Admin)** | `admin@techshop.com` | `password` | Toàn quyền truy cập Dashboard và CMS (`/admin/dashboard`) |
| **Khách hàng mẫu (Customer)** | `customer@techshop.com` | `password` | Mua sắm, quản lý đơn hàng, tra cứu bảo hành cá nhân |

---

## 🧪 Kiểm Thử Hệ Thống (Quality Assurance & Testing)

Dự án được trang bị đầy đủ bộ kiểm thử tự động cho cả Backend và Frontend:

### 1. Kiểm thử Backend (PHPUnit - 16 Feature & Unit Tests)
```bash
cd backend
php artisan test
```
*Kiểm thử bao gồm:*
- `CatalogApiTest`: Danh mục, Mega Menu, Aliases lọc ngành hàng.
- `OrderCheckoutTest`: Đặt hàng, trừ kho nguyên tử, dispatch notifications.
- `CouponApiTest`: Tính toán giảm giá %, chặn trần tối đa, kiểm tra hết hạn.
- `VnpayPaymentTest`: Tạo URL thanh toán VNPay, chữ ký bảo mật SHA512, chống thanh toán trùng lặp.
- `AdminDashboardFilterTest`: Lọc doanh thu theo ngày/tháng/tùy chọn mốc thời gian.
- `WarrantyClaimTest`: Tra cứu hồ sơ bảo hành RMA công khai.
- `GoogleAuthTest`: Xác thực luồng Single Sign-On OAuth 2.0.

### 2. Kiểm thử Frontend (Vitest - 4 Test Suites)
```bash
cd frontend
npm run test
```
*Kiểm thử bao gồm:*
- `cartStore.spec.ts`: Thao tác thêm/sửa/xóa và tính toán tổng tiền giỏ hàng.
- `ProductCard.spec.ts`: Hiển thị thẻ sản phẩm, badge ưu đãi, giá định dạng VNĐ.
- `FlashSaleSection.spec.ts`: Hiển thị khu vực Flash Sale và thanh đếm ngược.
- `TrustBadgesBar.spec.ts`: Thanh cam kết dịch vụ khách hàng.

### 3. Kiểm tra TypeScript & Production Build
```bash
cd frontend
npm run lint    # Kiểm tra chuẩn code bằng Oxlint & ESLint
npm run build   # Kiểm tra type-check (vue-tsc) và đóng gói Vite Production
```

---

## 📄 Bản Quyền & Giấy Phép
Dự án được phát triển cho mục đích học tập và giải pháp thương mại điện tử công nghệ cao cấp.  
Giấy phép phát hành: **MIT License**.
