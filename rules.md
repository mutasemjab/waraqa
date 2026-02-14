# AI Development Rules for Aseel ERP

## Core Principles

### 1. Requirement Understanding & Clarification

**MANDATORY: Always clarify before implementing**

-   **NEVER assume** requirements that are not explicitly stated
-   **ALWAYS ask** when any requirement is unclear, ambiguous, or incomplete
-   **NEVER proceed** with implementation if you have doubts about what is needed
-   **ALWAYS confirm** your understanding before making any code changes

**When to ask questions:**

-   Requirements are vague or incomplete
-   Multiple interpretations are possible
-   Technical approach is unclear
-   Dependencies or relationships are not specified
-   Expected behavior is not defined

**Question format:**

```
I need clarification on [specific aspect].
The requirement states [quote requirement].
I need to understand: [what you need to know]
```

### 2. Scope Control - No Unauthorized Modifications

**STRICT RULE: Only implement what is explicitly requested**

-   **NEVER add** features, fields, or functionality not requested
-   **NEVER modify** code outside the scope of the task
-   **NEVER refactor** unless explicitly asked to do so
-   **NEVER optimize** unless performance issues are identified and requested
-   **NEVER add** "nice to have" features without approval

**Before making any change, verify:**

1. Is this change explicitly requested?
2. Is this change necessary to fulfill the requirement?
3. Will this change affect other parts of the system?
4. Has the user approved this specific change?

### 3. Code Comments - English Only

**MANDATORY: All comments must be in English**

-   **ALWAYS write** code comments in English language
-   **NEVER write** code comments in Arabic or any other language
-   **ALWAYS use** clear, concise, and professional English
-   **ALWAYS explain** WHY, not just WHAT

**Comment guidelines:**

```php
// ✅ CORRECT - English comment
// Validate the product quantity to prevent negative inventory
if ($product->quantity < $request->quantity) {
    throw new ValidationException('Insufficient stock');
}

// ❌ WRONG - Arabic comment
// التحقق من كمية المنتج لمنع المخزون السالب
if ($product->quantity < $request->quantity) {
    throw new ValidationException('Insufficient stock');
}
```

### 4. Step-by-Step Logic Documentation

**MANDATORY: Document logic steps with numbered comments**

**Format:**

```php
// 1. Validate the incoming request data
// 2. Check if product exists in inventory
// 3. Calculate the total price including tax
// 4. Create the invoice record
// 5. Attach products to the invoice
// 6. Update inventory quantities
// 7. Generate journal entry for accounting
// 8. Return success response
```

**Example implementation:**

```php
public function store(Request $request): RedirectResponse
{
    // 1. Validate the incoming request data
    $validated = $request->validate([
        'customer_id' => 'required|exists:customers,id',
        'products' => 'required|array',
        'products.*.id' => 'required|exists:products,id',
        'products.*.quantity' => 'required|integer|min:1',
    ]);

    // 2. Check if all products exist in inventory
    foreach ($validated['products'] as $productData) {
        $product = Product::find($productData['id']);
        if ($product->quantity < $productData['quantity']) {
            return back()->with('error', __('messages.insufficient_stock'));
        }
    }

    // 3. Calculate the total price including tax
    $subtotal = 0;
    foreach ($validated['products'] as $productData) {
        $product = Product::find($productData['id']);
        $subtotal += $product->price * $productData['quantity'];
    }
    $tax = $subtotal * 0.15; // 15% tax rate
    $total = $subtotal + $tax;

    // 4. Create the invoice record
    $invoice = Invoice::create([
        'customer_id' => $validated['customer_id'],
        'subtotal' => $subtotal,
        'tax' => $tax,
        'total' => $total,
        'created_by' => auth()->id(),
    ]);

    // 5. Attach products to the invoice
    foreach ($validated['products'] as $productData) {
        $product = Product::find($productData['id']);
        $invoice->products()->attach($product->id, [
            'quantity' => $productData['quantity'],
            'price' => $product->price,
            'total' => $product->price * $productData['quantity'],
        ]);
    }

    // 6. Update inventory quantities
    foreach ($validated['products'] as $productData) {
        $product = Product::find($productData['id']);
        $product->decrement('quantity', $productData['quantity']);
    }

    // 7. Generate journal entry for accounting
    $this->generateJournalEntry($invoice);

    // 8. Return success response
    return redirect()->route('invoices.show', $invoice->id)
        ->with('success', __('messages.invoice_created'));
}
```

### 5. Blade Template Organization

**MANDATORY: Break down large Blade pages into partials**

**Rules:**

-   **NEVER create** Blade files with more than 200 lines
-   **ALWAYS extract** repeated sections into partials
-   **ALWAYS use** anonymous components for reusable UI elements
-   **ALWAYS organize** partials in logical directories

**Directory structure:**

```
resources/views/
├── components/              # Anonymous components
│   ├── forms/
│   │   ├── input.blade.php
│   │   ├── select.blade.php
│   │   └── textarea.blade.php
│   ├── tables/
│   │   ├── table.blade.php
│   │   └── actions.blade.php
│   └── cards/
│       └── card.blade.php
├── partials/               # Reusable partials
│   ├── headers/
│   ├── footers/
│   └── forms/
└── custom_pages/
    └── {module}/
        ├── index.blade.php
        ├── create.blade.php
        ├── edit.blade.php
        └── partials/
            ├── form.blade.php
            ├── table.blade.php
            └── filters.blade.php
```

**Example - Large page broken down:**

**Main file:** `resources/views/custom_pages/warehouse/products/index.blade.php`

```blade
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Page Header --}}
    @include('custom_pages.warehouse.products.partials.header')

    {{-- Filters Section --}}
    @include('custom_pages.warehouse.products.partials.filters')

    {{-- Data Table --}}
    @include('custom_pages.warehouse.products.partials.table')

    {{-- Pagination --}}
    @include('partials.pagination')
</div>
@endsection
```

**Partial:** `resources/views/custom_pages/warehouse/products/partials/header.blade.php`

```blade
<div class="page-header">
    <h1>{{ __('messages.products') }}</h1>
    <a href="{{ route('products.create') }}" class="btn btn-primary">
        {{ __('messages.add_product') }}
    </a>
</div>
```

**Anonymous Component:** `resources/views/components/tables/table.blade.php`

```blade
@props([
    'headers' => [],
    'rows' => [],
    'actions' => true,
])

<table class="table table-striped">
    <thead>
        <tr>
            @foreach($headers as $header)
                <th>{{ $header }}</th>
            @endforeach
            @if($actions)
                <th>{{ __('messages.actions') }}</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
            <tr>
                @foreach($headers as $key => $header)
                    <td>{{ $row[$key] }}</td>
                @endforeach
                @if($actions)
                    <td>
                        {{ $slot }}
                    </td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>
```

### 6. No Hardcoded Text - Use Translations

**MANDATORY: All user-facing text must use translation function**

**Rules:**

-   **NEVER hardcode** any text that users see
-   **ALWAYS use** `__('messages.key')` for translations
-   **ALWAYS add** translations to both `lang/ar/messages.php` and `lang/en/messages.php`
-   **ALWAYS use** descriptive translation keys

**Translation key naming convention:**

```
{module}_{entity}_{attribute}
```

**Examples:**

```php
// ✅ CORRECT - Using translations
return redirect()->route('products.index')
    ->with('success', __('messages.product_created_successfully'));

throw new ValidationException([
    'name' => __('messages.product_name_required'),
]);

// ❌ WRONG - Hardcoded text
return redirect()->route('products.index')
    ->with('success', 'Product created successfully');

throw new ValidationException([
    'name' => 'Product name is required',
]);
```

**Blade templates:**

```blade
{{-- ✅ CORRECT --}}
<h1>{{ __('messages.products') }}</h1>
<button>{{ __('messages.add_product') }}</button>

{{-- ❌ WRONG --}}
<h1>Products</h1>
<button>Add Product</button>
```

**Translation files:**

`lang/en/messages.php`

```php
return [
    // Products
    'products' => 'Products',
    'product_name' => 'Product Name',
    'product_price' => 'Product Price',
    'product_created_successfully' => 'Product created successfully',
    'product_updated_successfully' => 'Product updated successfully',
    'product_deleted_successfully' => 'Product deleted successfully',
    'add_product' => 'Add Product',
    'edit_product' => 'Edit Product',
    'delete_product' => 'Delete Product',

    // Common
    'actions' => 'Actions',
    'save' => 'Save',
    'cancel' => 'Cancel',
    'delete' => 'Delete',
    'edit' => 'Edit',
    'view' => 'View',
    'search' => 'Search',
    'filter' => 'Filter',
    'export' => 'Export',
];
```

`lang/ar/messages.php`

```php
return [
    // Products
    'products' => 'المنتجات',
    'product_name' => 'اسم المنتج',
    'product_price' => 'سعر المنتج',
    'product_created_successfully' => 'تم إنشاء المنتج بنجاح',
    'product_updated_successfully' => 'تم تحديث المنتج بنجاح',
    'product_deleted_successfully' => 'تم حذف المنتج بنجاح',
    'add_product' => 'إضافة منتج',
    'edit_product' => 'تعديل المنتج',
    'delete_product' => 'حذف المنتج',

    // Common
    'actions' => 'الإجراءات',
    'save' => 'حفظ',
    'cancel' => 'إلغاء',
    'delete' => 'حذف',
    'edit' => 'تعديل',
    'view' => 'عرض',
    'search' => 'بحث',
    'filter' => 'تصفية',
    'export' => 'تصدير',
];
```

### 7. Token Efficiency

**MANDATORY: Write concise, efficient code**

**Rules:**

-   **ALWAYS prefer** built-in Laravel helpers over verbose alternatives
-   **ALWAYS use** Eloquent relationships instead of raw queries
-   **ALWAYS leverage** Laravel's collection methods
-   **NEVER include** unnecessary code or comments
-   **ALWAYS remove** commented-out code before finalizing

**Efficient vs Inefficient examples:**

```php
// ✅ EFFICIENT - Using Laravel helpers
$products = Product::with(['category', 'warehouse'])
    ->where('status', 'active')
    ->get()
    ->map(fn($p) => [
        'id' => $p->id,
        'name' => $p->name,
        'category' => $p->category->name,
    ]);

// ❌ INEFFICIENT - Verbose and redundant
$products = DB::table('products')
    ->join('categories', 'products.category_id', '=', 'categories.id')
    ->select('products.*', 'categories.name as category_name')
    ->where('products.status', '=', 'active')
    ->get();

$result = [];
foreach ($products as $product) {
    $result[] = [
        'id' => $product->id,
        'name' => $product->name,
        'category' => $product->category_name,
    ];
}
```

### 8. Code Maintainability

**MANDATORY: Write clean, maintainable code**

**Rules:**

-   **ALWAYS follow** PSR-12 coding standards
-   **ALWAYS use** meaningful variable and function names
-   **ALWAYS keep** functions focused on single responsibility
-   **ALWAYS extract** complex logic into services
-   **ALWAYS use** type hints where possible
-   **ALWAYS document** public methods with PHPDoc

**Example - Well-structured code:**

```php
<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    /**
     * Create a new invoice with products.
     *
     * @param array $data Validated invoice data
     * @return Invoice The created invoice
     * @throws \Exception If transaction fails
     */
    public function create(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            // 1. Create the invoice record
            $invoice = $this->createInvoiceRecord($data);

            // 2. Attach products to the invoice
            $this->attachProducts($invoice, $data['products']);

            // 3. Update inventory quantities
            $this->updateInventory($data['products']);

            // 4. Generate journal entry
            $this->generateJournalEntry($invoice);

            return $invoice;
        });
    }

    /**
     * Create the base invoice record.
     */
    private function createInvoiceRecord(array $data): Invoice
    {
        return Invoice::create([
            'customer_id' => $data['customer_id'],
            'subtotal' => $data['subtotal'],
            'tax' => $data['tax'],
            'total' => $data['total'],
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Attach products to the invoice.
     */
    private function attachProducts(Invoice $invoice, array $products): void
    {
        foreach ($products as $product) {
            $invoice->products()->attach($product['id'], [
                'quantity' => $product['quantity'],
                'price' => $product['price'],
                'total' => $product['quantity'] * $product['price'],
            ]);
        }
    }

    /**
     * Update inventory quantities.
     */
    private function updateInventory(array $products): void
    {
        foreach ($products as $product) {
            Product::where('id', $product['id'])
                ->decrement('quantity', $product['quantity']);
        }
    }

    /**
     * Generate journal entry for accounting.
     */
    private function generateJournalEntry(Invoice $invoice): void
    {
        // Journal entry logic here
    }
}
```

### 9. Anonymous Components Usage

**MANDATORY: Use anonymous components for reusable UI elements**

**When to use anonymous components:**

-   Form inputs (text, select, checkbox, etc.)
-   Cards and panels
-   Buttons with consistent styling
-   Table rows or cells
-   Modals and alerts
-   Any repeated UI pattern

**Example - Form input component:**

`resources/views/components/forms/input.blade.php`

```blade
@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'required' => false,
    'placeholder' => null,
    'error' => null,
])

<div class="form-group {{ $error ? 'has-error' : '' }}">
    @if($label)
        <label for="{{ $name }}">
            {{ $label }}
            @if($required) <span class="required">*</span> @endif
        </label>
    @endif

    <input
        type="{{ $type }}"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        {{ $required ? 'required' : '' }}
        {{ $placeholder ? 'placeholder="' . $placeholder . '"' : '' }}
        class="form-control"
    >

    @if($error)
        <span class="help-block">{{ $error }}</span>
    @endif
</div>
```

**Usage in Blade:**

```blade
<x-forms.input
    name="product_name"
    :label="__('messages.product_name')"
    :required="true"
    :placeholder="__('messages.enter_product_name')"
/>

<x-forms.input
    name="product_price"
    type="number"
    :label="__('messages.product_price')"
    :required="true"
    step="0.01"
/>
```

### 10. Validation & Error Handling

**MANDATORY: Proper validation and error handling**

**Rules:**

-   **ALWAYS validate** all incoming data
-   **ALWAYS use** Form Request classes for complex validation
-   **ALWAYS return** user-friendly error messages using translations
-   **ALWAYS log** errors for debugging
-   **NEVER expose** sensitive information in error messages

**Example - Form Request:**

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('Warehouse-product-add');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'has_batch' => 'boolean',
            'has_expiry_date' => 'boolean',
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => __('messages.product_name_required'),
            'name_ar.required' => __('messages.product_name_ar_required'),
            'category_id.required' => __('messages.category_required'),
            'price.required' => __('messages.price_required'),
            'price.numeric' => __('messages.price_must_be_numeric'),
        ];
    }
}
```

### 11. Database Operations

**MANDATORY: Safe and efficient database operations**

**Rules:**

-   **ALWAYS use** transactions for multi-step operations
-   **ALWAYS use** Eloquent relationships instead of raw queries
-   **ALWAYS eager load** relationships to prevent N+1 queries
-   **NEVER use** `DB::raw()` unless absolutely necessary
-   **ALWAYS use** parameter binding to prevent SQL injection

**Example:**

```php
// ✅ CORRECT - Using transactions and relationships
public function transferStock(int $fromWarehouse, int $toWarehouse, array $products): void
{
    DB::transaction(function () use ($fromWarehouse, $toWarehouse, $products) {
        // 1. Validate source warehouse has enough stock
        foreach ($products as $product) {
            $stock = ProductWarehouse::where('warehouse_id', $fromWarehouse)
                ->where('product_id', $product['id'])
                ->first();

            if (!$stock || $stock->quantity < $product['quantity']) {
                throw new \Exception(__('messages.insufficient_stock'));
            }
        }

        // 2. Deduct from source warehouse
        foreach ($products as $product) {
            ProductWarehouse::where('warehouse_id', $fromWarehouse)
                ->where('product_id', $product['id'])
                ->decrement('quantity', $product['quantity']);
        }

        // 3. Add to destination warehouse
        foreach ($products as $product) {
            ProductWarehouse::updateOrCreate(
                [
                    'warehouse_id' => $toWarehouse,
                    'product_id' => $product['id'],
                ],
                [
                    'quantity' => DB::raw("quantity + {$product['quantity']}"),
                ]
            );
        }

        // 4. Log the transfer
        StockTransfer::create([
            'from_warehouse_id' => $fromWarehouse,
            'to_warehouse_id' => $toWarehouse,
            'transferred_by' => auth()->id(),
        ]);
    });
}
```

### 12. Git Operations - No Automatic Commits

**STRICT RULE: Never add or commit any git changes**

-   **NEVER execute** `git add` command
-   **NEVER execute** `git commit` command
-   **NEVER execute** `git push` command
-   **NEVER modify** the `.git` directory
-   **NEVER create** git hooks or modify existing ones
-   **ALWAYS leave** git operations to the user

**What you CAN do:**

-   Read files to understand the codebase
-   Write and edit code files
-   Create new files
-   Delete files (with user approval)
-   Run commands that don't affect git (e.g., `php artisan migrate`, `npm install`, `git status` ...etc)

**What you CANNOT do:**

```bash
# ❌ FORBIDDEN - Never execute these commands
git add .
git commit -m "message"
git push origin main
git commit --amend
git rebase
```

### 13. File Editing - No sed Commands

**STRICT RULE: Never use sed command to build or edit files**

-   **NEVER use** `sed` command for file modifications
-   **NEVER use** `awk` command for file modifications
-   **NEVER use** stream editors for code changes
-   **ALWAYS use** proper file editing tools (write_file, edit_file)
-   **ALWAYS maintain** file integrity and proper formatting

**Why this rule exists:**

-   `sed` can corrupt file structure
-   `sed` doesn't understand code syntax
-   `sed` can break indentation and formatting
-   `sed` makes changes hard to track and review
-   Proper editing tools preserve file context

**Correct approach:**

```php
// ✅ CORRECT - Use edit_file or write_to_file tools
// This preserves file structure, indentation, and context

// ❌ WRONG - Never use sed
// sed -i 's/old/new/g' file.php
```

### 14. Context Management - Handle Large Contexts

**MANDATORY: Proactively manage context to prevent hallucinations**

**Signs of context becoming too large:**

-   Repeating the same information multiple times
-   Forgetting previously discussed requirements
-   Making inconsistent suggestions
-   Providing contradictory code examples
-   Losing track of the task scope
-   Difficulty recalling file contents

**When context becomes too large, you MUST:**

**Option 1: Start a new session**

```
I notice the context has become quite large and I'm starting to lose track of some details.
To ensure I provide accurate and consistent work, I recommend starting a new session.

Please start a new session and provide a summary of what we've accomplished so far.
```

**Option 2: Create a context file and start new session**

```
I notice the context has become quite large and I'm starting to lose track of some details.
To ensure I provide accurate and consistent work, I recommend:

1. Create a context file documenting the current state
2. Start a new session
3. Reference the context file in the new session

Would you like me to create a context file now?
```

**Context file structure:**

```markdown
# Context Summary

## Task Description

[Brief description of the original task]

## Completed Work

-   [x] Item 1 completed
-   [x] Item 2 completed
-   [x] Item 3 completed

## Current State

-   Files created: [list of files]
-   Files modified: [list of files]
-   Current implementation status: [description]

## Remaining Work

-   [ ] Item 1 pending
-   [ ] Item 2 pending
-   [ ] Item 3 pending

## Important Notes

-   [Any important decisions or constraints]
-   [Any patterns or conventions established]
-   [Any dependencies or relationships]
```

**Proactive context management:**

-   **ALWAYS monitor** your own responses for consistency
-   **ALWAYS verify** you're not repeating yourself
-   **ALWAYS check** that new suggestions align with previous work
-   **NEVER continue** if you notice signs of context overload
-   **ALWAYS speak up** before making mistakes due to context issues


## Final Reminder

**When in doubt, ASK. It is better to ask a clarifying question than to implement something incorrectly.**

**Quality over speed. A correct implementation is always better than a fast but incorrect one.**
