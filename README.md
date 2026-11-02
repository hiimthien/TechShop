# 🛒 TechShop E-Commerce System

> Hệ thống thương mại điện tử công nghệ cao cấp toàn diện (Full-stack E-Commerce & CMS) được xây dựng trên nền tảng **Laravel 11 REST API** và **Vue 3 (Composition API, TypeScript, Vite, Tailwind CSS v4)**.

---

## 🌟 Tính Năng Nổi Bật

### 1. Phía Khách Hàng (Storefront)
* **Danh mục sản phẩm thông minh**: Bộ lọc đa tiêu chí (danh mục ngành hàng, đa thương hiệu, khoảng giá, RAM, ổ cứng SSD, kiểu kết nối có dây/không dây, tần số quét màn hình).
* **Chi tiết sản phẩm đa biến thể**:
  - Hỗ trợ xem trước thư viện ảnh (Image Gallery).
  - Chọn phiên bản/màu sắc (VD: Titan Tự Nhiên - 256GB), ảnh lớn tự động chuyển đổi theo đúng màu sắc được chọn và ngược lại.
* **Giỏ hàng & Đặt hàng (Cart & Checkout)**:
  - Đồng bộ giỏ hàng lưu trữ máy khách và database.
  - Kiểm tra tồn kho nguyên tử (`atomic lock/decrement`), bảo vệ chống đặt vượt quá số lượng kho.
  - Lưu vết giá lịch sử (`price snapshot`) trên từng món hàng.
* **Bảo hành & Hỗ trợ kỹ thuật (RMA)**: Tra cứu tiến độ bảo hành theo mã phiếu / SĐT / số Serial và gửi yêu cầu bảo hành trực tuyến.
* **Chính sách & Giới thiệu**: Hệ thống các trang tĩnh tiêu chuẩn (Giới thiệu, Liên hệ, Chính sách vận chuyển, Chính sách bảo hành).

### 2. Phía Quản Trị Hệ Thống (Admin CMS)
* **Tổng quan (Dashboard)**: Thống kê doanh thu thực tế, số lượng đơn hàng theo từng trạng thái, khách hàng, biểu đồ tăng trưởng doanh thu 6 tháng gần nhất.
* **Quản lý sản phẩm (Products)**:
  - Hỗ trợ tải lên nhiều file ảnh cùng lúc (`multipart/form-data`) lưu trữ chuẩn Laravel Storage.
  - Quản lý cấu hình biến thể (Tên phiên bản, SKU, Giá bán, Tồn kho, và chọn gắn ảnh tương ứng cho từng biến thể).
* **Quản lý ngành hàng (Categories)**: Thêm/sửa danh mục, đếm số lượng sản phẩm, chặn xóa danh mục khi đang có sản phẩm thuộc về.
* **Quản lý thương hiệu (Brands)**: Thêm/sửa hãng sản xuất, hỗ trợ tải lên file Logo thương hiệu.
* **Quản lý đơn hàng (Orders)**: Danh sách đơn hàng phân trang, lọc theo trạng thái, tìm kiếm theo tên/SĐT/mã vận đơn GHN, cập nhật trạng thái đơn hàng (Chờ xác nhận, Đang đóng gói, Đang giao, Hoàn thành, Hủy).
* **Mã giảm giá (Coupons/Vouchers)**: CRUD mã giảm giá theo số tiền cố định hoặc phần trăm, giá trị đơn tối thiểu, giới hạn lượt dùng, bật/tắt nhanh trạng thái.
* **Quản lý bảo hành (Warranty Claims)**: Tiếp nhận yêu cầu bảo hành của khách hàng, ghi chú kỹ thuật và cập nhật trạng thái xử lý máy.

---

## 🏛️ Kiến Trúc Hệ Thống (Architecture)

### Backend (Laravel 11 - Repository & Service Pattern)
Backend áp dụng kiến trúc phân tầng chuẩn Enterprise (5 lớp) tương tự dự án `cinereserve`:
* **Form Requests (`app/Http/Requests/Admin/`)**: Tách biệt logic xác thực và phân quyền đầu vào.
* **Thin Controllers (`app/Http/Controllers/Api/`)**: Chỉ đóng vai trò điều phối HTTP request và trả JSON response.
* **Service Layer (`app/Services/`)**: Chứa toàn bộ nghiệp vụ phức tạp, điều phối transaction đa bảng nguyên tử (`DB::transaction`).
* **Repository Contracts (`app/Repositories/Contracts/`)**: Định nghĩa interface hợp đồng cho từng thực thể (`Product`, `Order`, `Category`, `Brand`, `Analytics`).
* **Eloquent Repositories (`app/Repositories/Eloquent/`)**: Trực tiếp thực thi truy vấn Eloquent, tách biệt hoàn toàn truy vấn cơ sở dữ liệu.
* **Service Provider (`app/Providers/AppServiceProvider.php`)**: Tự động liên kết Interface $\leftrightarrow$ Implementation thông qua Dependency Injection.

### Frontend (Vue 3 - Modular / Feature-Driven Architecture)
* **`src/core/`**: Chứa các thành phần cốt lõi dùng chung (Router & Auth Navigation Guards, Axios Client & Interceptors, Base UI Components như `BaseModal`, `BaseButton`, `BaseInput`, `BasePagination`, `BaseAlert`).
* **`src/modules/`**: Phân rã theo từng domain tính năng độc lập (`admin/`, `auth/`, `cart/`, `checkout/`, `products/`, `pages/`, `home/`).
* **Type-Safety**: 100% TypeScript nghiêm ngặt, 0 lỗi `any`, 0 lỗi lint (Oxlint & ESLint), 0 lỗi type-check (`vue-tsc`).

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

# 1. Cài đặt dependencies
composer install

# 2. Tạo file môi trường .env và cấu hình cơ sở dữ liệu
cp .env.example .env
php artisan key:generate

# 3. Tạo symlink cho thư mục lưu trữ ảnh công khai
php artisan storage:link

# 4. Chạy migration và seed dữ liệu mẫu
php artisan migrate --seed

# 5. Khởi động backend server
php artisan serve
```
Backend API sẽ chạy tại: `http://localhost:8000`

---

### 3. Cài đặt Frontend (Vue 3)

```bash
cd frontend

# 1. Cài đặt dependencies
npm install

# 2. Khởi chạy môi trường phát triển (Dev server)
npm run dev

# 3. (Tùy chọn) Kiểm tra Lint & TypeScript
npm run lint
npm run build
```
Frontend ứng dụng sẽ chạy tại: `http://localhost:5173`

---

## 🔑 Tài Khoản Truy Cập Mặc Định

| Vai trò | Email đăng nhập | Mật khẩu | Quyền hạn |
| :--- | :--- | :--- | :--- |
| **Quản trị viên (Admin)** | `admin@techshop.com` | `password` | Toàn quyền truy cập CMS (`/admin/dashboard`) |
| **Khách hàng (Customer)** | `customer@techshop.com` | `password` | Mua hàng, tra cứu bảo hành, quản lý đơn cá nhân |

---

## 🧪 Kiểm Thử & Đảm Bảo Chất Lượng (Quality Assurance)

```bash
# Kiểm thử Backend PHPUnit
cd backend
php artisan test

# Kiểm tra Linter Frontend (Oxlint & ESLint)
cd frontend
npm run lint

# Kiểm tra TypeScript Compile & Build Frontend
npm run build
```

---

## 📄 Bản Quyền & Giấy Phép
Dự án được phát triển cho mục đích học tập và xây dựng giải pháp thương mại điện tử chuyên nghiệp.
Giấy phép: MIT License.
