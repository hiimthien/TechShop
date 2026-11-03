<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $isDetail = $this->relationLoaded('variants');

        $data = [
            'id'             => $this->id,
            'name'           => $this->name,
            'slug'           => $this->slug,
            'brand'          => $this->whenLoaded('brand', fn() => $this->brand->name),
            'brand_id'       => $this->brand_id,
            'category'       => $this->whenLoaded('category', fn() => $this->category->name),
            'category_id'    => $this->category_id,
            'thumbnail'      => $this->whenLoaded('images', fn() => optional($this->images->first())->image_url),
            'starting_price' => $this->variants_min_price ?? $this->whenLoaded('variants', fn() => $this->variants->min('price')),
            'original_price' => $this->whenLoaded('variants', function () {
                $minPrice = $this->variants->min('price');
                return $minPrice ? (float) round((float) $minPrice * 1.08, -4) : null;
            }),
            'discount_percent' => 8,
            'rating'         => 5.0,
            'reviews_count'  => 128,
        ];

        if ($isDetail) {
            $data['description'] = $this->description;
            $data['variants'] = ProductVariantResource::collection($this->variants);
            $data['images'] = $this->images->map(fn($img) => $img->image_url);
            $data['specifications'] = $this->resolveSpecifications();
            $data['promotions'] = $this->resolvePromotions();
            $data['related_products'] = $this->when(isset($this->related_products), fn() => ProductResource::collection($this->related_products));
        }

        return $data;
    }

    /**
     * Xử lý bảng thông số kỹ thuật chi tiết
     */
    protected function resolveSpecifications(): array
    {
        if (!empty($this->specifications) && is_array($this->specifications)) {
            return $this->specifications;
        }

        $categorySlug = $this->relationLoaded('category') && $this->category ? $this->category->slug : '';
        $name = $this->name;

        if (str_contains($categorySlug, 'dien-thoai') || str_contains(strtolower($name), 'iphone') || str_contains(strtolower($name), 'galaxy') || str_contains(strtolower($name), 'xiaomi')) {
            return [
                'Hệ điều hành' => str_contains(strtolower($name), 'iphone') ? 'iOS 18 (Hỗ trợ Apple Intelligence)' : 'Android 14 (OneUI / HyperOS)',
                'Chipset (Vi xử lý)' => str_contains(strtolower($name), 'iphone 16') ? 'Apple A18 Pro tiến trình 3nm' : (str_contains(strtolower($name), 'iphone 15') ? 'Apple A17 Pro' : (str_contains(strtolower($name), 's24') ? 'Snapdragon 8 Gen 3 for Galaxy' : 'MediaTek Dimensity 9300+')),
                'Kích thước màn hình' => str_contains(strtolower($name), 'max') || str_contains(strtolower($name), 'plus') || str_contains(strtolower($name), 'ultra') ? '6.8 - 6.9 inches' : '6.1 - 6.36 inches',
                'Công nghệ màn hình' => 'Super Retina XDR OLED / Dynamic AMOLED 2X 120Hz ProMotion',
                'Độ phân giải' => '2868 x 1320 pixels (Mật độ 460 ppi)',
                'Camera sau' => 'Chính 48MP / 200MP + Góc siêu rộng 48MP + Telephoto Zoom tiềm vọng 5x',
                'Camera trước' => '12MP TrueDepth Autofocus / 32MP Selfie sắc nét',
                'Dung lượng pin' => '4,422 mAh - 5,000 mAh (Xem video liên tục 33 giờ)',
                'Chuẩn kháng nước bụi' => 'IP68 (Kháng nước ở độ sâu 6 mét trong 30 phút)',
                'Tính năng đặc biệt' => 'Nút Camera Control, Action Button, Trí tuệ nhân tạo AI, Sạc không dây MagSafe',
            ];
        }

        if (str_contains($categorySlug, 'laptop') || str_contains(strtolower($name), 'macbook') || str_contains(strtolower($name), 'laptop')) {
            return [
                'Hệ điều hành' => str_contains(strtolower($name), 'macbook') ? 'macOS Sonoma / Sequoia' : 'Windows 11 Home Bản Quyền Vĩnh Viễn',
                'Vi xử lý (CPU)' => str_contains(strtolower($name), 'macbook') ? 'Apple M3 Pro / Max 14-core CPU' : 'Intel Core Ultra 7 / AMD Ryzen 9 8945HS',
                'Bộ nhớ RAM' => '16GB / 32GB LPDDR5X Tốc độ Bus 6400MHz',
                'Ổ cứng lưu trữ' => '512GB / 1TB SSD PCIe Gen4 NVMe M.2 siêu tốc',
                'Màn hình' => '14.0 inch - 16.0 inch Độ phân giải 3K - 4K OLED 120Hz 100% DCI-P3',
                'Card đồ họa (GPU)' => str_contains(strtolower($name), 'gaming') || str_contains(strtolower($name), 'rtx') ? 'NVIDIA GeForce RTX 4070 8GB GDDR6 140W TGP' : 'Apple 18-Core GPU / Intel Arc Graphics',
                'Cổng giao tiếp' => '3x Thunderbolt 4 / USB-C, 1x HDMI 2.1, 1x Jack 3.5mm, Khe thẻ SDXC',
                'Trọng lượng máy' => '1.24 kg - 1.85 kg (Khung kim loại nguyên khối CNC cao cấp)',
            ];
        }

        if (str_contains($categorySlug, 'dong-ho') || str_contains(strtolower($name), 'watch')) {
            return [
                'Kích thước mặt' => '42mm / 46mm / 49mm (Kính tinh thể Sapphire chống trầy xước)',
                'Chất liệu viền' => 'Titanium hàng không cấp 5 / Thép không gỉ 316L',
                'Thời lượng pin' => 'Từ 36 đến 72 giờ sử dụng (Hỗ trợ sạc nhanh không dây từ tính)',
                'Kháng nước' => 'Đạt chuẩn 10 ATM / Lặn biển độ sâu 100m, chuẩn quân đội MIL-STD 810H',
                'Cảm biến sức khỏe' => 'Đo điện tâm đồ ECG, nồng độ oxy SpO2, đo nhịp tim liên tục, phân tích giấc ngủ AI',
                'Định vị vệ tinh' => 'GPS băng tần kép L1 + L5 chuẩn xác cao (Glonass, Galileo, BDS)',
            ];
        }

        if (str_contains($categorySlug, 'tai-nghe') || str_contains(strtolower($name), 'airpods') || str_contains(strtolower($name), 'sony') || str_contains(strtolower($name), 'marshall')) {
            return [
                'Công nghệ chống ồn' => 'Chống ồn chủ động thông minh Active Noise Cancelling (ANC) thế hệ mới',
                'Thời lượng pin' => '6 - 8 giờ nghe liên tục (Lên đến 30 - 36 giờ khi kèm hộp sạc)',
                'Cổng sạc' => 'Cổng Type-C hiện đại & Hỗ trợ sạc không dây chuẩn Qi / MagSafe',
                'Chuẩn kết nối' => 'Bluetooth 5.3 đa điểm, Codec âm thanh cao cấp LDAC / AAC Hi-Res Wireless',
                'Kháng nước & bụi' => 'Chuẩn IP54 / IPX4 an toàn tuyệt đối khi tập luyện thể thao dưới mưa',
                'Âm thanh vòm' => 'Personal Spatial Audio theo dõi chuyển động đầu không gian 360 độ',
            ];
        }

        return [
            'Thương hiệu' => $this->brand ? $this->brand->name : 'Chính hãng',
            'Thời gian bảo hành' => '24 tháng chính hãng tại các trung tâm bảo hành toàn quốc',
            'Tình trạng hàng hóa' => 'Máy mới 100% nguyên seal nguyên hộp từ nhà máy',
            'Hình thức mua hàng' => 'Giao hàng tận nơi miễn phí toàn quốc hoặc nhận tại cửa hàng',
        ];
    }

    /**
     * Danh sách khuyến mãi đặc quyền
     */
    protected function resolvePromotions(): array
    {
        return [
            [
                'id' => 1,
                'title' => 'Ưu đãi thanh toán đối tác',
                'content' => 'Giảm thêm đến 500.000đ khi thanh toán qua ví VNPay-QR hoặc quét mã QR ngân hàng.',
            ],
            [
                'id' => 2,
                'title' => 'Trợ giá lên đời máy mới',
                'content' => 'Thu cũ đổi mới trợ giá thêm đến 2.000.000đ (Cam kết thu giá cao nhất thị trường).',
            ],
            [
                'id' => 3,
                'title' => 'Trả góp 0% lãi suất',
                'content' => 'Hỗ trợ trả góp 0% lãi suất kỳ hạn 3 - 6 - 12 tháng qua thẻ tín dụng và công ty tài chính.',
            ],
            [
                'id' => 4,
                'title' => 'Quà tặng & Phụ kiện mua kèm',
                'content' => 'Tặng SIM 5G data tốc độ cao miễn phí tháng đầu & Giảm ngay 30% khi mua kèm củ sạc, ốp lưng, tai nghe.',
            ],
        ];
    }
}
