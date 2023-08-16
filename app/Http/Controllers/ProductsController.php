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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\TryCatch;

class ProductsController extends Controller
{
    public function index()
    {
        try {
            $products = Products::all();
            return RequestResponse::success($products);
        } catch (\Exception $e) {
            return RequestResponse::error('Internal Server Error', $e, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function create(StoreProductsRequest $request)
    {
        try {
            $product = new Products([
                'name' => $request->name,
                'price' => $request->price,
                'description' => $request->description,
            ]);

            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $photoPath = $photo->storeAs('photos', uniqid() . '.' . $photo->getClientOriginalExtension(), 'public');
                $product->photo = $photoPath;
            }

            $product->save();

            return RequestResponse::success($product);
        } catch (ValidationException $e) {
            return RequestResponse::error('Validation Error', $e);
        } catch (\Exception $e) {
            return RequestResponse::error('Internal Server Error', $e, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $product = Products::find($id);
            if (!$product) {
                return RequestResponse::error('Product not found', [], Response::HTTP_NOT_FOUND);
            }
            return RequestResponse::success($product);
        } catch (\Exception $e) {
            return RequestResponse::error('Internal Server Error', $e, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdateProductsRequest $request, $id)
    {
        try {
            dd($request->id);
            $product = Products::find($id);
            if (!$product) {
                return RequestResponse::error('Product not found', [], Response::HTTP_NOT_FOUND);
            }

            $product->name = $request->name;
            $product->price = $request->price;
            $product->description = $request->description;

            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $photoPath = $photo->storeAs('photos', uniqid() . '.' . $photo->getClientOriginalExtension(), 'public');
                $product->photo = $photoPath;
            }
            $product->save();
            return RequestResponse::success($product);
        } catch (ValidationException $e) {
            return RequestResponse::error('Validation Error', $e);
        } catch (\Exception $e) {
            return RequestResponse::error('Internal Server Error', $e, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            $product = Products::find($id);
            if (!$product) {
                return RequestResponse::error('Product not found', [], Response::HTTP_NOT_FOUND);
            }

            if ($product->photo) {
                Storage::disk('public')->delete($product->photo);
            }

            $product->delete();
            return RequestResponse::success('Product deleted successfully', Response::HTTP_ACCEPTED);
        } catch (ValidationException $e) {
            return RequestResponse::error('Validation Error', $e);
        } catch (\Exception $e) {
            return RequestResponse::error('Internal Server Error', $e, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
