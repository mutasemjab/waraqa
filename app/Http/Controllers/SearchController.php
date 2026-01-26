<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class SearchController extends Controller
{
    public function searchItems(Request $request)
    {
        try {
            $term = $request->get('term', '');
            $model = $request->get('model');
            $limit = (int)$request->get('limit', 5);
            $displayColumn = $request->get('displayColumn', 'name');
            $filter = $request->get('filter', '');
            $exclude = $request->get('exclude', '');

            Log::info('SearchController - Input:', [
                'term' => $term,
                'model' => $model,
                'limit' => $limit,
                'displayColumn' => $displayColumn,
                'filter' => $filter,
                'exclude' => $exclude
            ]);

            if (!$model || !class_exists($model)) {
                Log::error('SearchController - Invalid model: ' . $model);
                return response()->json([]);
            }

            $query = $model::query();

            // Apply filters based on model and filter parameter
            if ($model === 'App\Models\User') {
                if ($filter === 'without_roles') {
                    $query->withoutRoles();
                } elseif (strpos($filter, 'with_role:') === 0) {
                    $roleName = substr($filter, 10); // Remove 'with_role:' prefix
                    $query->role($roleName);
                } elseif (strpos($filter, 'with_roles:') === 0) {
                    // Support multiple roles: with_roles:seller,customer
                    $rolesString = substr($filter, 11); // Remove 'with_roles:' prefix
                    $roles = array_map('trim', explode(',', $rolesString));
                    $query->role($roles);
                }
            } elseif ($model === 'App\Models\Warehouse') {
                if ($filter === 'without_user') {
                    // Get only admin warehouses (warehouses without user_id)
                    $query->whereNull('user_id');
                }
            }

            // Apply exclude filter
            if ($exclude) {
                $excludeIds = is_array($exclude) ? $exclude : explode(',', $exclude);
                $excludeIds = array_filter(array_map('trim', $excludeIds));
                if (!empty($excludeIds)) {
                    $query->whereNotIn('id', $excludeIds);
                }
            }

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