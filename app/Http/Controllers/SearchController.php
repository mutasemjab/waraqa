<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
    public function searchItems(Request $request)
    {
        try {
            $term = $request->get('term', '');
            $model = $request->get('model');
            $limit = (int)$request->get('limit', 5);
            $displayColumn = $request->get('displayColumn', 'name');

            Log::info('SearchController - Input:', [
                'term' => $term,
                'model' => $model,
                'limit' => $limit,
                'displayColumn' => $displayColumn
            ]);

            if (!$model || !class_exists($model)) {
                Log::error('SearchController - Invalid model: ' . $model);
                return response()->json([]);
            }

            $query = $model::query();

            // Build search query - search in all columns
            if ($term) {
                $query->where(function ($q) use ($term) {
                    $columns = \Schema::getColumnListing($q->getModel()->getTable());
                    foreach ($columns as $column) {
                        if ($column !== 'id' && $column !== 'created_at' && $column !== 'updated_at') {
                            $q->orWhere($column, 'LIKE', "%{$term}%");
                        }
                    }
                });
            }

            // Get results
            $results = $query->limit($limit)->get();

            Log::info('SearchController - Results count: ' . $results->count());

            // Format results
            $formatted = $results->map(function ($item) use ($displayColumn) {
                $text = $item->{$displayColumn} ?? $item->name ?? (string)$item;
                return [
                    'id' => $item->id,
                    'text' => $text
                ];
            })->toArray();

            return response()->json($formatted);
        } catch (\Exception $e) {
            Log::error('SearchController - Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}