<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Quản trị viên mặc định
        $admin = User::updateOrCreate(
            ['email' => 'admin@techshop.com'],
            [
                'name' => 'Admin TechShop',
                'password' => 'password',
                'role' => 'admin',
                'phone' => '0901234567',
                'email_verified_at' => Carbon::now()->subMonths(6),
                'created_at' => Carbon::now()->subMonths(6),
                'updated_at' => Carbon::now()->subMonths(6),
            ]
        );

        // 2. Khách hàng demo chính
        $customer = User::updateOrCreate(
            ['email' => 'customer@techshop.com'],
            [
                'name' => 'Nguyễn Văn Khách',
                'password' => 'password',
                'role' => 'customer',
                'phone' => '0987654321',
                'email_verified_at' => Carbon::now()->subMonths(5),
                'created_at' => Carbon::now()->subMonths(5),
                'updated_at' => Carbon::now()->subMonths(5),
            ]
        );

        Address::updateOrCreate(
            ['user_id' => $customer->id, 'address' => 'Số 123 Đường Cầu Giấy, Phường Dịch Vọng, Quận Cầu Giấy'],
            [
                'name' => 'Nguyễn Văn Khách',
                'phone' => '0987654321',
                'city' => 'Hà Nội',
                'is_default' => true,
                'ward_code' => null,
                'district_id' => null,
                'province_id' => null,
            ]
        );

        Address::updateOrCreate(
            ['user_id' => $customer->id, 'address' => 'Số 285 Đường Cách Mạng Tháng 8, Phường 12, Quận 10'],
            [
                'name' => 'Nguyễn Văn Khách (Văn phòng)',
                'phone' => '0987654321',
                'city' => 'TP. Hồ Chí Minh',
                'is_default' => false,
                'ward_code' => null,
                'district_id' => null,
                'province_id' => null,
            ]
        );

        // 3. 14 Khách hàng Việt Nam thật với địa chỉ thực tế trải khắp các tỉnh thành
        $vietnameseUsers = [
            [
                'name' => 'Nguyễn Minh Tuấn',
                'email' => 'nguyen.minh.tuan@gmail.com',
                'phone' => '0983124567',
                'days_ago' => 170,
                'addresses' => [
                    [
                        'name' => 'Nguyễn Minh Tuấn',
                        'phone' => '0983124567',
                        'address' => 'Số 142 Đường Hai Bà Trưng, Phường Bến Nghé, Quận 1',
                        'city' => 'TP. Hồ Chí Minh',
                        'is_default' => true,
                    ]
                ]
            ],
            [
                'name' => 'Trần Thị Thu Hà',
                'email' => 'tran.thi.thu.ha@gmail.com',
                'phone' => '0914892345',
                'days_ago' => 155,
                'addresses' => [
                    [
                        'name' => 'Trần Thị Thu Hà',
                        'phone' => '0914892345',
                        'address' => 'Số 58 Phố Tràng Tiền, Phường Tràng Tiền, Quận Hoàn Kiếm',
                        'city' => 'Hà Nội',
                        'is_default' => true,
                    ]
                ]
            ],
            [
                'name' => 'Lê Hoàng Long',
                'email' => 'le.hoang.long@gmail.com',
                'phone' => '0935671234',
                'days_ago' => 145,
                'addresses' => [
                    [
                        'name' => 'Lê Hoàng Long',
                        'phone' => '0935671234',
                        'address' => 'Số 215 Đường Nguyễn Văn Linh, Phường Nam Dương, Quận Hải Châu',
                        'city' => 'Đà Nẵng',
                        'is_default' => true,
                    ]
                ]
            ],
            [
                'name' => 'Phạm Thị Bích Ngọc',
                'email' => 'pham.thi.bich.ngoc@gmail.com',
                'phone' => '0976543210',
                'days_ago' => 135,
                'addresses' => [
                    [
                        'name' => 'Phạm Thị Bích Ngọc',
                        'phone' => '0976543210',
                        'address' => 'Số 89 Đường Cách Mạng Tháng 8, Phường An Thới, Quận Bình Thủy',
                        'city' => 'Cần Thơ',
                        'is_default' => true,
                    ]
                ]
            ],
            [
                'name' => 'Đặng Văn Đức',
                'email' => 'dang.van.duc@gmail.com',
                'phone' => '0903456789',
                'days_ago' => 125,
                'addresses' => [
                    [
                        'name' => 'Đặng Văn Đức',
                        'phone' => '0903456789',
                        'address' => 'Số 36 Đường Điện Biên Phủ, Phường Đa Kao, Quận 1',
                        'city' => 'TP. Hồ Chí Minh',
                        'is_default' => true,
                    ],
                    [
                        'name' => 'Đặng Văn Đức (Kho Hải Phòng)',
                        'phone' => '0903456789',
                        'address' => 'Số 12 Đường Hoàng Diệu, Phường Minh Khai, Quận Hồng Bàng',
                        'city' => 'Hải Phòng',
                        'is_default' => false,
                    ]
                ]
            ],
            [
                'name' => 'Vũ Hải Đăng',
                'email' => 'vu.hai.dang@gmail.com',
                'phone' => '0968112233',
                'days_ago' => 115,
                'addresses' => [
                    [
                        'name' => 'Vũ Hải Đăng',
                        'phone' => '0968112233',
                        'address' => 'Số 120 Đường Cầu Giấy, Phường Quan Hoa, Quận Cầu Giấy',
                        'city' => 'Hà Nội',
                        'is_default' => true,
                    ]
                ]
            ],
            [
                'name' => 'Hoàng Tuấn Anh',
                'email' => 'hoang.tuan.anh@gmail.com',
                'phone' => '0945998877',
                'days_ago' => 105,
                'addresses' => [
                    [
                        'name' => 'Hoàng Tuấn Anh',
                        'phone' => '0945998877',
                        'address' => 'Số 74 Đường Trần Phú, Phường Lộc Thọ',
                        'city' => 'Nha Trang',
                        'is_default' => true,
                    ]
                ]
            ],
            [
                'name' => 'Bùi Gia Huy',
                'email' => 'bui.gia.huy@gmail.com',
                'phone' => '0989334455',
                'days_ago' => 95,
                'addresses' => [
                    [
                        'name' => 'Bùi Gia Huy',
                        'phone' => '0989334455',
                        'address' => 'Số 45 Đường Nguyễn Huệ, Phường Bến Nghé, Quận 1',
                        'city' => 'TP. Hồ Chí Minh',
                        'is_default' => true,
                    ]
                ]
            ],
            [
                'name' => 'Đỗ Minh Trí',
                'email' => 'do.minh.tri@gmail.com',
                'phone' => '0918223344',
                'days_ago' => 85,
                'addresses' => [
                    [
                        'name' => 'Đỗ Minh Trí',
                        'phone' => '0918223344',
                        'address' => 'Số 310 Đường Quang Trung, Phường 10, Quận Gò Vấp',
                        'city' => 'TP. Hồ Chí Minh',
                        'is_default' => true,
                    ],
                    [
                        'name' => 'Đỗ Minh Trí (Chi nhánh Đà Nẵng)',
                        'phone' => '0918223344',
                        'address' => 'Số 68 Đường Phan Đăng Lưu, Phường Hòa Cường Nam, Quận Hải Châu',
                        'city' => 'Đà Nẵng',
                        'is_default' => false,
                    ]
                ]
            ],
            [
                'name' => 'Nguyễn Thanh Hằng',
                'email' => 'nguyen.thanh.hang@gmail.com',
                'phone' => '0977889900',
                'days_ago' => 75,
                'addresses' => [
                    [
                        'name' => 'Nguyễn Thanh Hằng',
                        'phone' => '0977889900',
                        'address' => 'Số 16 Phố Hàng Bài, Phường Hàng Bài, Quận Hoàn Kiếm',
                        'city' => 'Hà Nội',
                        'is_default' => true,
                    ]
                ]
            ],
            [
                'name' => 'Võ Hoàng Nam',
                'email' => 'vo.hoang.nam@gmail.com',
                'phone' => '0909556677',
                'days_ago' => 60,
                'addresses' => [
                    [
                        'name' => 'Võ Hoàng Nam',
                        'phone' => '0909556677',
                        'address' => 'Số 52 Đường Lê Duẩn, Phường Bến Nghé, Quận 1',
                        'city' => 'TP. Hồ Chí Minh',
                        'is_default' => true,
                    ]
                ]
            ],
            [
                'name' => 'Đinh Quốc Bảo',
                'email' => 'dinh.quoc.bao@gmail.com',
                'phone' => '0938445566',
                'days_ago' => 45,
                'addresses' => [
                    [
                        'name' => 'Đinh Quốc Bảo',
                        'phone' => '0938445566',
                        'address' => 'Số 204 Đường 30 Tháng 4, Phường Xuân Khánh, Quận Ninh Kiều',
                        'city' => 'Cần Thơ',
                        'is_default' => true,
                    ]
                ]
            ],
            [
                'name' => 'Cao Thanh Thảo',
                'email' => 'cao.thanh.thao@gmail.com',
                'phone' => '0982334411',
                'days_ago' => 30,
                'addresses' => [
                    [
                        'name' => 'Cao Thanh Thảo',
                        'phone' => '0982334411',
                        'address' => 'Số 15 Đường Lê Thánh Tôn, Phường Bến Nghé, Quận 1',
                        'city' => 'TP. Hồ Chí Minh',
                        'is_default' => true,
                    ]
                ]
            ],
            [
                'name' => 'Dương Đình Toàn',
                'email' => 'duong.dinh.toan@gmail.com',
                'phone' => '0966442211',
                'days_ago' => 15,
                'addresses' => [
                    [
                        'name' => 'Dương Đình Toàn',
                        'phone' => '0966442211',
                        'address' => 'Số 48 Phố Thái Hà, Phường Trung Liệt, Quận Đống Đa',
                        'city' => 'Hà Nội',
                        'is_default' => true,
                    ]
                ]
            ],
        ];

        foreach ($vietnameseUsers as $uData) {
            $userDate = Carbon::now()->subDays($uData['days_ago']);
            $u = User::updateOrCreate(
                ['email' => $uData['email']],
                [
                    'name' => $uData['name'],
                    'password' => 'password',
                    'role' => 'customer',
                    'phone' => $uData['phone'],
                    'email_verified_at' => $userDate,
                    'created_at' => $userDate,
                    'updated_at' => $userDate,
                ]
            );

            foreach ($uData['addresses'] as $addr) {
                Address::updateOrCreate(
                    ['user_id' => $u->id, 'address' => $addr['address']],
                    [
                        'name' => $addr['name'],
                        'phone' => $addr['phone'],
                        'city' => $addr['city'],
                        'is_default' => $addr['is_default'],
                        'ward_code' => null,
                        'district_id' => null,
                        'province_id' => null,
                    ]
                );
            }
        }

        $this->command?->info("✓ Đã seed tài khoản mẫu TechShop:");
        $this->command?->info("   [Admin]    Email: admin@techshop.com    | Mật khẩu: password");
        $this->command?->info("   [Customer] Email: customer@techshop.com | Mật khẩu: password");
        $this->command?->info("   [Customer] + 14 khách hàng Việt Nam thật với số điện thoại & địa chỉ chuẩn.");
    }
}
