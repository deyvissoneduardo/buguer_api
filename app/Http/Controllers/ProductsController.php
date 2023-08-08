<?php

namespace App\Http\Controllers;

use App\Helpers\RequestResponse;
use App\Http\Requests\ProductDeleteRequest;
use App\Http\Requests\StoreProductsRequest;
use App\Http\Requests\UpdateProductsRequest;
use App\Models\Products;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return RequestResponse::success(Products::all());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(StoreProductsRequest $request)
    {
        try {
            $product = Products::create([
                'name' => $request->name,
                'price' => $request->price,
                'description' => $request->description,
            ]);

            return RequestResponse::success($product);
        } catch (ValidationException $e) {
            return RequestResponse::error('Validation Error', $e->getMessage());
        } catch (\Exception $e) {
            return RequestResponse::error('Internal Server Error', $e, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductsRequest $request)
    {
    }

    /**
     * Display the specified resource.
     */
    public function show(Products $products)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(String $id, StoreProductsRequest $request)
    {
        try {
            if (!$id) {
                return RequestResponse::error([], 'Bad Request');
            }

            $product = Products::find($id);
            if (!$product) {
                return RequestResponse::error([], 'Product Not Found', Response::HTTP_NOT_FOUND);
            }

            $product->name = $request->name;
            $product->price = $request->price;
            $product->description = $request->description;
            $product->save();

            return RequestResponse::success($product);
        } catch (ValidationException $e) {
            return RequestResponse::error('Validation Error', $e->getMessage());
        } catch (\Exception $e) {
            return RequestResponse::error('Internal Server Error', $e, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductsRequest $request, Products $products)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        try {
            if (!$request->id) {
                return RequestResponse::error([], 'Bad Request');
            }

            $product = Products::find($request->id);
            if (!$product) {
                return RequestResponse::error([], 'Product Not Found', Response::HTTP_NOT_FOUND);
            }

            $product = Products::find($request->id);
            $product->delete();

            return RequestResponse::success(['message' => 'Deleted success']);
        } catch (ValidationException $e) {
            return RequestResponse::error('Validation Error', $e->getMessage());
        } catch (\Exception $e) {
            return RequestResponse::error('Internal Server Error', $e, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
