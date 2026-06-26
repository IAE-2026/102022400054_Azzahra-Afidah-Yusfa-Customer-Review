<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OAT;

#[OAT\Tag(name: 'Reviews', description: 'Endpoint untuk mengelola customer review')]
class ReviewController extends Controller
{
    #[OAT\Get(
        path: '/api/v1/reviews',
        summary: 'Ambil semua review',
        security: [['ApiKeyAuth' => []]],
        tags: ['Reviews']
    )]
    #[OAT\Response(
        response: 200,
        description: 'Success',
        content: new OAT\JsonContent(
            properties: [
                new OAT\Property(property: 'status', type: 'string', example: 'success'),
                new OAT\Property(property: 'data', type: 'array', items: new OAT\Items(type: 'object'))
            ]
        )
    )]
    #[OAT\Response(response: 401, description: 'Unauthorized')]
    public function index()
    {
        $reviews = Review::all();
        return response()->json([
            'status' => 'success',
            'data'   => $reviews
        ], 200);
    }

    #[OAT\Get(
        path: '/api/v1/reviews/{id}',
        summary: 'Ambil detail review berdasarkan ID',
        security: [['ApiKeyAuth' => []]],
        tags: ['Reviews']
    )]
    #[OAT\Parameter(name: 'id', in: 'path', required: true, schema: new OAT\Schema(type: 'integer'))]
    #[OAT\Response(
        response: 200,
        description: 'Success',
        content: new OAT\JsonContent(
            properties: [
                new OAT\Property(property: 'status', type: 'string', example: 'success'),
                new OAT\Property(property: 'data', type: 'object')
            ]
        )
    )]
    #[OAT\Response(
        response: 404,
        description: 'Not Found',
        content: new OAT\JsonContent(
            properties: [
                new OAT\Property(property: 'status', type: 'string', example: 'error'),
                new OAT\Property(property: 'message', type: 'string', example: 'Review not found')
            ]
        )
    )]
    #[OAT\Response(response: 401, description: 'Unauthorized')]
    public function show($id)
    {
        $review = Review::find($id);

        if (!$review) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Review not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $review
        ], 200);
    }

    #[OAT\Get(
        path: '/api/v1/reviews/product/{product_id}',
        summary: 'Ambil review berdasarkan produk',
        security: [['ApiKeyAuth' => []]],
        tags: ['Reviews']
    )]
    #[OAT\Parameter(name: 'product_id', in: 'path', required: true, schema: new OAT\Schema(type: 'string'))]
    #[OAT\Response(response: 200, description: 'Success', content: new OAT\JsonContent())]
    #[OAT\Response(response: 404, description: 'Not Found', content: new OAT\JsonContent())]
    #[OAT\Response(response: 401, description: 'Unauthorized')]
    public function byProduct($product_id)
    {
        $reviews = Review::where('product_id', $product_id)->get();

        if ($reviews->isEmpty()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No reviews found for this product'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $reviews
        ], 200);
    }

    #[OAT\Post(
        path: '/api/v1/reviews',
        summary: 'Simpan review baru',
        security: [['ApiKeyAuth' => []]],
        tags: ['Reviews']
    )]
    #[OAT\RequestBody(
        required: true,
        content: new OAT\JsonContent(
            required: ['product_id', 'reviewer_name', 'rating', 'comment'],
            properties: [
                new OAT\Property(property: 'product_id', type: 'string', example: 'PROD-001'),
                new OAT\Property(property: 'reviewer_name', type: 'string', example: 'Azzahra'),
                new OAT\Property(property: 'rating', type: 'integer', example: 5),
                new OAT\Property(property: 'comment', type: 'string', example: 'Produk bagus!')
            ]
        )
    )]
    #[OAT\Response(
        response: 201,
        description: 'Created',
        content: new OAT\JsonContent(
            properties: [
                new OAT\Property(property: 'status', type: 'string', example: 'success'),
                new OAT\Property(property: 'message', type: 'string', example: 'Review created successfully'),
                new OAT\Property(property: 'data', type: 'object')
            ]
        )
    )]
    #[OAT\Response(response: 401, description: 'Unauthorized')]
    #[OAT\Response(response: 422, description: 'Validation Error')]
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_id'    => 'required|string',
                'reviewer_name' => 'required|string',
                'rating'        => 'required|integer|min:1|max:5',
                'comment'       => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $e->errors()
            ], 422);
        }

        $review = Review::create($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Review created successfully',
            'data'    => $review
        ], 201);
    }
}