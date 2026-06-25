<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class ReviewController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/reviews",
     *     summary="Ambil semua review",
     *     security={{"ApiKeyAuth": {}}},
     *     @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function index()
    {
        $reviews = Review::all();
        return response()->json([
            'success' => true,
            'data' => $reviews
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/reviews/{id}",
     *     summary="Ambil detail review berdasarkan ID",
     *     security={{"ApiKeyAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *     @OA\Response(response=404, description="Not Found", @OA\JsonContent()),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent())
     * )
     */
    public function show($id)
    {
        $review = Review::find($id);

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $review
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/reviews/product/{product_id}",
     *     summary="Ambil review berdasarkan produk",
     *     security={{"ApiKeyAuth": {}}},
     *     @OA\Parameter(
     *         name="product_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *     @OA\Response(response=404, description="Not Found", @OA\JsonContent()),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent())
     * )
     */
    public function byProduct($product_id)
    {
        $reviews = Review::where('product_id', $product_id)->get();

        if ($reviews->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No reviews found for this product'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $reviews
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/reviews",
     *     summary="Simpan review baru",
     *     security={{"ApiKeyAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_id", "reviewer_name", "rating", "comment"},
     *             @OA\Property(property="product_id", type="string", example="PROD-001"),
     *             @OA\Property(property="reviewer_name", type="string", example="Azzahra"),
     *             @OA\Property(property="rating", type="integer", example=5),
     *             @OA\Property(property="comment", type="string", example="Produk bagus!")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Created", @OA\JsonContent()),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent())
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id'    => 'required|string',
            'reviewer_name' => 'required|string',
            'rating'        => 'required|integer|min:1|max:5',
            'comment'       => 'required|string',
        ]);

        $review = Review::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Review created successfully',
            'data' => $review
        ], 201);
    }
}