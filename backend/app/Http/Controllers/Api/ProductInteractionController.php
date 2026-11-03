<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductQuestion;
use App\Models\ProductReview;
use App\Events\ReviewCreated;
use App\Events\QuestionCreated;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductInteractionController extends Controller
{
    /**
     * Lấy danh sách đánh giá kèm thống kê điểm số
     */
    public function getReviews(int|string $productId): JsonResponse
    {
        $product = Product::find($productId);
        if (!$product) {
            $product = Product::where('slug', $productId)->firstOrFail();
        }

        $reviews = $product->reviews()->get();

        // Nếu sản phẩm chưa có review nào trong DB, tự động nạp sẵn đánh giá mẫu thực tế
        if ($reviews->isEmpty()) {
            $sampleReviews = [
                [
                    'customer_name' => 'Nguyễn Minh Tuấn',
                    'customer_phone' => '098****214',
                    'rating' => 5,
                    'content' => 'Máy mua đập hộp rất ưng ý, đóng gói kỹ càng, giao hàng hỏa tốc trong 2 giờ. Nhân viên TechShop tư vấn tận tình, đã dán sẵn màn hình và tặng ốp lưng rất đẹp!',
                    'admin_reply' => 'Dạ TechShop cảm ơn anh Tuấn đã tin tưởng và ủng hộ sản phẩm. Chúc anh có những trải nghiệm tuyệt vời cùng máy mới ạ!',
                    'replied_at' => now()->subDays(1),
                    'created_at' => now()->subDays(2),
                ],
                [
                    'customer_name' => 'Trần Thị Thu Hà',
                    'customer_phone' => '091****893',
                    'rating' => 5,
                    'content' => 'Màu máy ở ngoài đẹp hơn trên ảnh nhiều, cầm rất đầm tay và viền kim loại sang trọng. Màn hình 120Hz mượt mà, camera chụp đêm đỉnh chóp!',
                    'admin_reply' => 'Dạ TechShop cảm ơn chị Thu Hà rất nhiều ạ. Cần hỗ trợ thêm về kỹ thuật hoặc phụ kiện chị cứ nhắn lại hotline bên em nhé!',
                    'replied_at' => now()->subDays(3),
                    'created_at' => now()->subDays(4),
                ],
                [
                    'customer_name' => 'Lê Hoàng Long',
                    'customer_phone' => '093****567',
                    'rating' => 5,
                    'content' => 'Hàng chính hãng VN/A kích hoạt bảo hành điện tử chuẩn chỉ. Giá tại TechShop đang tốt nhất thị trường lại còn được trợ giá thu cũ đổi mới 2 triệu.',
                    'admin_reply' => null,
                    'replied_at' => null,
                    'created_at' => now()->subDays(6),
                ],
                [
                    'customer_name' => 'Đặng Văn Đức',
                    'customer_phone' => '090****432',
                    'rating' => 4,
                    'content' => 'Sản phẩm dùng tốt, pin trâu lướt cả ngày không hết. Chỉ có điều hộp máy hơi xước nhẹ bên ngoài do vận chuyển nhưng máy bên trong seal nguyên vẹn không sao.',
                    'admin_reply' => 'Dạ TechShop chân thành xin lỗi anh Đức về sự bất tiện ở vỏ hộp bên ngoài ạ. Bên em sẽ nhắc nhở kỹ đối tác vận chuyển để hoàn thiện hơn ạ!',
                    'replied_at' => now()->subDays(8),
                    'created_at' => now()->subDays(9),
                ],
            ];

            foreach ($sampleReviews as $s) {
                ProductReview::create([
                    'product_id' => $product->id,
                    'customer_name' => $s['customer_name'],
                    'customer_phone' => $s['customer_phone'],
                    'rating' => $s['rating'],
                    'content' => $s['content'],
                    'admin_reply' => $s['admin_reply'],
                    'replied_at' => $s['replied_at'],
                    'is_verified_purchase' => true,
                    'is_active' => true,
                    'created_at' => $s['created_at'],
                ]);
            }

            $reviews = $product->reviews()->get();
        }

        $total = $reviews->count();
        $avg = $total > 0 ? round($reviews->avg('rating'), 1) : 5.0;

        $starCounts = [
            5 => $reviews->where('rating', 5)->count(),
            4 => $reviews->where('rating', 4)->count(),
            3 => $reviews->where('rating', 3)->count(),
            2 => $reviews->where('rating', 2)->count(),
            1 => $reviews->where('rating', 1)->count(),
        ];

        return response()->json([
            'status' => true,
            'summary' => [
                'average' => $avg,
                'total_reviews' => $total,
                'star_counts' => $starCounts,
            ],
            'data' => $reviews,
        ]);
    }

    /**
     * Gửi đánh giá mới từ khách hàng
     */
    public function storeReview(Request $request, int|string $productId): JsonResponse
    {
        $product = Product::find($productId);
        if (!$product) {
            $product = Product::where('slug', $productId)->firstOrFail();
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'nullable|string|max:20',
            'rating' => 'required|integer|min:1|max:5',
            'content' => 'required|string|min:5|max:1000',
        ]);

        $review = ProductReview::create([
            'product_id' => $product->id,
            'user_id' => $request->user()?->id,
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'] ?? null,
            'rating' => (int) $validated['rating'],
            'content' => $validated['content'],
            'is_verified_purchase' => true,
            'is_active' => true,
        ]);

        // Broadcast realtime event
        event(new ReviewCreated($review));

        return response()->json([
            'status' => true,
            'message' => 'Cảm ơn bạn đã gửi đánh giá! Đánh giá của bạn đã được ghi nhận thành công.',
            'data' => $review,
        ], 201);
    }

    /**
     * Lấy danh sách hỏi đáp (Q&A)
     */
    public function getQuestions(int|string $productId): JsonResponse
    {
        $product = Product::find($productId);
        if (!$product) {
            $product = Product::where('slug', $productId)->firstOrFail();
        }

        $questions = $product->questions()->get();

        // Nếu sản phẩm chưa có câu hỏi nào, tự động nạp sẵn các câu hỏi thường gặp
        if ($questions->isEmpty()) {
            $sampleQuestions = [
                [
                    'customer_name' => 'Vũ Hải Đăng',
                    'question' => 'Sản phẩm này trong hộp có tặng kèm củ sạc nhanh và ốp lưng không shop?',
                    'admin_answer' => 'Dạ chào bạn, hộp sản phẩm chuẩn của nhà sản xuất bao gồm máy và cáp sạc Type-C. Tuy nhiên khi mua máy tại TechShop, bạn sẽ được tặng kèm voucher giảm giá 30% khi mua kèm củ sạc chính hãng và ốp lưng cao cấp ạ!',
                    'answered_at' => now()->subDays(1),
                    'created_at' => now()->subDays(2),
                ],
                [
                    'customer_name' => 'Phạm Kiều Oanh',
                    'question' => 'Em muốn mua trả góp 0% thì cần những giấy tờ gì và xét duyệt trong bao lâu ạ?',
                    'admin_answer' => 'Dạ chào bạn, bạn chỉ cần có Căn cước công dân (CCCD) gắn chip là có thể làm thủ tục duyệt hồ sơ online trong 5 - 10 phút, hoặc trả góp 0% qua thẻ tín dụng hơn 25 ngân hàng liên kết cực kỳ tiện lợi ạ!',
                    'answered_at' => now()->subDays(3),
                    'created_at' => now()->subDays(4),
                ],
                [
                    'customer_name' => 'Bùi Gia Huy',
                    'question' => 'Máy mua tại TechShop có được bảo hành chính hãng tại các trung tâm bảo hành của Apple / Samsung tại Việt Nam không?',
                    'admin_answer' => 'Dạ 100% sản phẩm bán ra tại TechShop là hàng chính hãng VN/A có xuất hóa đơn VAT đầy đủ, được bảo hành toàn quốc tại tất cả các trung tâm bảo hành ủy quyền chính hãng trên toàn quốc bạn hoàn toàn yên tâm nhé!',
                    'answered_at' => now()->subDays(5),
                    'created_at' => now()->subDays(5),
                ],
            ];

            foreach ($sampleQuestions as $q) {
                ProductQuestion::create([
                    'product_id' => $product->id,
                    'customer_name' => $q['customer_name'],
                    'question' => $q['question'],
                    'admin_answer' => $q['admin_answer'],
                    'answered_at' => $q['answered_at'],
                    'is_active' => true,
                    'created_at' => $q['created_at'],
                ]);
            }

            $questions = $product->questions()->get();
        }

        return response()->json([
            'status' => true,
            'total' => $questions->count(),
            'data' => $questions,
        ]);
    }

    /**
     * Gửi câu hỏi thắc mắc mới
     */
    public function storeQuestion(Request $request, int|string $productId): JsonResponse
    {
        $product = Product::find($productId);
        if (!$product) {
            $product = Product::where('slug', $productId)->firstOrFail();
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'nullable|string|max:20',
            'question' => 'required|string|min:5|max:1000',
        ]);

        $question = ProductQuestion::create([
            'product_id' => $product->id,
            'user_id' => $request->user()?->id,
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'] ?? null,
            'question' => $validated['question'],
            'is_active' => true,
        ]);

        // Broadcast realtime event
        event(new QuestionCreated($question));

        return response()->json([
            'status' => true,
            'message' => 'Câu hỏi của bạn đã được gửi thành công! Chuyên viên TechShop sẽ phản hồi trong ít phút.',
            'data' => $question,
        ], 201);
    }
}

