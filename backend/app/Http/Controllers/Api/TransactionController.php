<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class TransactionController extends Controller
{
    private TransactionRepositoryInterface $transactions;

    public function __construct(TransactionRepositoryInterface $transactions)
    {
        $this->transactions = $transactions;
    }

    #[OA\Get(
        path: '/api/transactions',
        summary: 'List all transactions of the authenticated user',
        tags: ['Transactions'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'List of transactions'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function index(Request $request)
    {
        return response()->json($this->transactions->forUser($request->user()->id));
    }

    #[OA\Post(
        path: '/api/transactions',
        summary: 'Create a new transaction',
        tags: ['Transactions'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['description', 'amount', 'date', 'type'],
                properties: [
                    new OA\Property(property: 'category_id', type: 'integer', example: 1),
                    new OA\Property(property: 'description', type: 'string', example: 'Compra no mercado'),
                    new OA\Property(property: 'amount', type: 'number', format: 'float', example: 150.50),
                    new OA\Property(property: 'date', type: 'string', format: 'date', example: '2024-04-06'),
                    new OA\Property(property: 'type', type: 'string', enum: ['credito', 'debito'], example: 'debito'),
                    new OA\Property(property: 'status', type: 'string', enum: ['pago', 'pendente'], example: 'pago'),
                    new OA\Property(property: 'observations', type: 'string', example: 'Compra de itens essenciais'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Transaction created'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'type' => 'required|in:credito,debito',
            'status' => 'sometimes|in:pago,pendente',
            'observations' => 'nullable|string',
        ]);

        $transaction = $this->transactions->create(array_merge($validated, [
            'user_id' => $request->user()->id,
        ]));

        return response()->json($transaction->load('category'), 201);
    }

    #[OA\Get(
        path: '/api/transactions/{id}',
        summary: 'Get transaction details',
        tags: ['Transactions'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Transaction details'),
            new OA\Response(response: 404, description: 'Transaction not found'),
        ]
    )]
    public function show(Request $request, Transaction $transaction)
    {
        if ($transaction->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($transaction->load('category'));
    }

    #[OA\Put(
        path: '/api/transactions/{id}',
        summary: 'Update a transaction',
        tags: ['Transactions'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'category_id', type: 'integer', example: 1),
                    new OA\Property(property: 'description', type: 'string', example: 'Compra no mercado'),
                    new OA\Property(property: 'amount', type: 'number', format: 'float', example: 150.50),
                    new OA\Property(property: 'date', type: 'string', format: 'date', example: '2024-04-06'),
                    new OA\Property(property: 'type', type: 'string', enum: ['credito', 'debito'], example: 'debito'),
                    new OA\Property(property: 'status', type: 'string', enum: ['pago', 'pendente'], example: 'pago'),
                    new OA\Property(property: 'observations', type: 'string', example: 'Compra de itens essenciais'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Transaction updated'),
            new OA\Response(response: 404, description: 'Transaction not found'),
        ]
    )]
    public function update(Request $request, Transaction $transaction)
    {
        if ($transaction->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'sometimes|string|max:255',
            'amount' => 'sometimes|numeric',
            'date' => 'sometimes|date',
            'type' => 'sometimes|in:credito,debito',
            'status' => 'sometimes|in:pago,pendente',
            'observations' => 'nullable|string',
        ]);

        $transaction = $this->transactions->update($transaction->id, $validated);

        return response()->json($transaction->load('category'));
    }

    #[OA\Delete(
        path: '/api/transactions/{id}',
        summary: 'Delete a transaction',
        tags: ['Transactions'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Transaction deleted'),
            new OA\Response(response: 404, description: 'Transaction not found'),
        ]
    )]
    public function destroy(Request $request, Transaction $transaction)
    {
        if ($transaction->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $this->transactions->delete($transaction->id);

        return response()->json(['message' => 'Transaction deleted successfully']);
    }
}
