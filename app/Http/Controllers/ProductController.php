<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $per_page = $request->input('per_page',10);
        $products = Product::where('is_active',1)->paginate($per_page);

        return response()->json([
            'status' => 'success',
            'message' => 'products loaded',
            'data' => $products
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|integer|min:1', // cents
            'currency'    => 'nullable|string|size:3',
            'is_active'   => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return apiError(
                'Validation failed',
                422,
                $validator->errors()
            );
        }

        // Create product
        $product = Product::create([
            'name'        => $request->name,
            'user_id'     => $request->user()->id,
            'slug'        => $request->slug,
            'description' => $request->description,
            'price'       => $request->price,
            'currency'    => $request->currency ?? 'USD',
            'is_active'   => $request->is_active ?? true,
        ]);

        return apiSuccess(
            'Product created successfully',
            $product,
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


    public function buy($id)
    {
        $product = Product::where('id', $id)
            ->where('is_active', true)
            ->first();

        if (! $product) {
            return apiError('Product not available', 404);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = CheckoutSession::create([
            'payment_method_types' => ['card'],
            'mode' => 'payment',
            'line_items' => [[
                'price_data' => [
                    'currency' => strtolower($product->currency),
                    'product_data' => [
                        'name' => $product->name,
                        'description' => $product->description,
                    ],
                    'unit_amount' => $product->price, // cents
                ],
                'quantity' => 1,
            ]],
            'success_url' => config('app.url') . '/payment-success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => config('app.url') . '/payment-cancel',
            'metadata' => [
                'product_id' => $product->id,
                'user_id' => auth()->id(),
            ],
        ]);

        Order::create([
            'user_id' => auth()->id(),
            'product_id' => $product->id,
            'amount' => $product->price,
            'currency' => $product->currency,
            'stripe_session_id' => $session->id,
            'status' => 'pending',
        ]);

        return apiSuccess('Checkout session created', [
            'checkout_url' => $session->url,
        ]);
    }

}
