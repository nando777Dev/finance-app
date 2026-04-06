<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class CategoryController extends Controller
{
    #[OA\Get(
        path: "/api/categories",
        summary: "List all categories of the authenticated user",
        tags: ["Categories"],
        security: [["sanctum" => []]],
        responses: [
            new OA\Response(response: 200, description: "List of categories"),
            new OA\Response(response: 401, description: "Unauthenticated")
        ]
    )]
    public function index(Request $request)
    {
        return response()->json($request->user()->categories);
    }

    #[OA\Post(
        path: "/api/categories",
        summary: "Create a new category",
        tags: ["Categories"],
        security: [["sanctum" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "type"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Alimentação"),
                    new OA\Property(property: "type", type: "string", enum: ["receita", "despesa"], example: "despesa"),
                    new OA\Property(property: "color", type: "string", example: "#FF5733"),
                    new OA\Property(property: "icon", type: "string", example: "utensils")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Category created"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:receita,despesa',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:255',
        ]);

        $category = $request->user()->categories()->create($validated);

        return response()->json($category, 201);
    }

    #[OA\Get(
        path: "/api/categories/{id}",
        summary: "Get category details",
        tags: ["Categories"],
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Category details"),
            new OA\Response(response: 404, description: "Category not found")
        ]
    )]
    public function show(Request $request, Category $category)
    {
        if ($category->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return response()->json($category);
    }

    #[OA\Put(
        path: "/api/categories/{id}",
        summary: "Update a category",
        tags: ["Categories"],
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Alimentação"),
                    new OA\Property(property: "type", type: "string", enum: ["receita", "despesa"], example: "despesa"),
                    new OA\Property(property: "color", type: "string", example: "#FF5733"),
                    new OA\Property(property: "icon", type: "string", example: "utensils")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Category updated"),
            new OA\Response(response: 404, description: "Category not found")
        ]
    )]
    public function update(Request $request, Category $category)
    {
        if ($category->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'type' => 'sometimes|in:receita,despesa',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:255',
        ]);

        $category->update($validated);

        return response()->json($category);
    }

    #[OA\Delete(
        path: "/api/categories/{id}",
        summary: "Delete a category",
        tags: ["Categories"],
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Category deleted"),
            new OA\Response(response: 404, description: "Category not found")
        ]
    )]
    public function destroy(Request $request, Category $category)
    {
        if ($category->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }
}
