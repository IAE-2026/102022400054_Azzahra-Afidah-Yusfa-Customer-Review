<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use OpenApi\Attributes as OAT;

class ReviewController extends Controller
{
    #[OAT\Get(
        path: "/api/v1/reviews",
        summary: "Ambil semua review",
        security: [["ApiKeyAuth" => []]]
    )]
    #[OAT\Response(response: 200, description: "Success", content: new OAT\JsonContent())]
    #[OAT\Response(response: 401, description: "Unauthorized")]
    public function index()
    {
        $reviews = Review::all();
        return response()->json([
            'success' => true,
            'data' => $reviews
        ], 200);
    }

    #[OAT\Get(
        path: "/api/v1/reviews/{id}",
        summary: "Ambil detail review berdasarkan ID",
        security: [["ApiKeyAuth" => []]]
    )]
    #[OAT\Parameter(name: "id", in: "path", required: true, schema: new OAT\Schema(type: "integer"))]
    #[OAT\Response(response: 200, description: "Success", content: new OAT\JsonContent())]
    #[OAT\Response(response: 404, description: "Not Found", content: new OAT\JsonContent())]
    #[OAT\Response(response: 401, description: "Unauthorized", content: new OAT\JsonContent())]
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

    #[OAT\Get(
        path: "/api/v1/reviews/product/{product_id}",
        summary: "Ambil review berdasarkan produk",
        security: [["ApiKeyAuth" => []]]
    )]
    #[OAT\Parameter(name: "product_id", in: "path", required: true, schema: new OAT\Schema(type: "string"))]
    #[OAT\Response(response: 200, description: "Success", content: new OAT\JsonContent())]
    #[OAT\Response(response: 404, description: "Not Found", content: new OAT\JsonContent())]
    #[OAT\Response(response: 401, description: "Unauthorized", content: new OAT\JsonContent())]
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

    #[OAT\Post(
        path: "/api/v1/reviews",
        summary: "Simpan review baru",
        security: [["ApiKeyAuth" => []]]
    )]
    #[OAT\RequestBody(
        required: true,
        content: new OAT\JsonContent(
            required: ["product_id", "reviewer_name", "rating", "comment"],
            properties: [
                new OAT\Property(property: "product_id", type: "string", example: "PROD-001"),
                new OAT\Property(property: "reviewer_name", type: "string", example: "Azzahra"),
                new OAT\Property(property: "rating", type: "integer", example: 5),
                new OAT\Property(property: "comment", type: "string", example: "Produk bagus!")
            ]
        )
    )]
    #[OAT\Response(response: 201, description: "Created", content: new OAT\JsonContent())]
    #[OAT\Response(response: 401, description: "Unauthorized", content: new OAT\JsonContent())]
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