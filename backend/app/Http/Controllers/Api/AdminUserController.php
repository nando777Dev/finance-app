<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

class AdminUserController extends Controller
{
    private UserRepositoryInterface $users;

    public function __construct(UserRepositoryInterface $users)
    {
        $this->users = $users;
    }

    #[OA\Get(
        path: '/api/admin/users',
        summary: 'List all users (admin)',
        tags: ['Admin'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'List of users'),
            new OA\Response(response: 403, description: 'Unauthorized'),
        ]
    )]
    public function index()
    {
        return response()->json($this->users->all());
    }

    #[OA\Patch(
        path: '/api/admin/users/{id}/activate',
        summary: 'Activate a user',
        tags: ['Admin'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'User activated'),
        ]
    )]
    public function activate(User $user)
    {
        $updated = $this->users->update($user->id, ['is_active' => true]);

        return response()->json($updated);
    }

    #[OA\Patch(
        path: '/api/admin/users/{id}/deactivate',
        summary: 'Deactivate a user',
        tags: ['Admin'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'User deactivated'),
        ]
    )]
    public function deactivate(User $user)
    {
        $updated = $this->users->update($user->id, ['is_active' => false]);

        return response()->json($updated);
    }

    #[OA\Put(
        path: '/api/admin/users/{id}',
        summary: 'Update a user',
        tags: ['Admin'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'john@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'newpassword123'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'User updated'),
        ]
    )]
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $updated = $this->users->update($user->id, $validated);

        return response()->json($updated);
    }

    #[OA\Delete(
        path: '/api/admin/users/{id}',
        summary: 'Delete a user',
        tags: ['Admin'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'User deleted'),
        ]
    )]
    public function destroy(User $user)
    {
        $this->users->delete($user->id);

        return response()->json(['message' => 'User deleted successfully']);
    }
}
