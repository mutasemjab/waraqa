@props([
    'model' => null,
    'fieldName' => 'selected_item',
    'label' => 'Select',
    'placeholder' => 'Search...',
    'limit' => 5,
    'required' => false,
    'value' => null,
    'displayColumn' => 'name',
])

@php
    $uniqueId = $fieldName . '_' . uniqid();
    $selectedText = '';
    if ($value && $model) {
        $item = $model::find($value);
        if ($item) {
            $selectedText = $item->{$displayColumn} ?? $item->name ?? '';
        }
    }
    $placeholderText = __('messages.Search');
@endphp

<div class="form-group">
    <label for="{{ $fieldName }}">
        {{ __('messages.' . $label) ?? $label }}
        @if($required)
            <span class="text-danger">*</span>
        @endif
    </label>

    <div class="search-select-wrapper position-relative" id="wrapper_{{ $uniqueId }}">
        <input type="hidden" name="{{ $fieldName }}" id="{{ $fieldName }}" value="{{ $value ?? '' }}">

        <input
            type="text"
            id="input_{{ $uniqueId }}"
            class="form-control"
            placeholder="{{ $placeholderText }}"
            autocomplete="off"
            value="{{ $selectedText }}"
            @if($required) required @endif
        >

        <div id="dropdown_{{ $uniqueId }}" class="search-select-dropdown">
            <div class="search-select-loading d-none">{{ __('messages.loading') }}</div>
            <div class="search-select-no-results d-none">{{ __('messages.no_results') }}</div>
            <div class="search-select-results"></div>
        </div>
    </div>
</div>

@pushOnce('styles')
<style>
    .search-select-wrapper {
        position: relative;
    }
    .search-select-dropdown {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 1050;
        background: #fff;
        border: 1px solid #ced4da;
        border-top: none;
        border-radius: 0 0 4px 4px;
        max-height: 250px;
        overflow-y: auto;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .search-select-dropdown.show {
        display: block;
    }
    .search-select-item {
        padding: 8px 12px;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
    }
    .search-select-item:last-child {
        border-bottom: none;
    }
    .search-select-item:hover,
    .search-select-item.active {
        background-color: #f8f9fa;
    }
    .search-select-item.selected {
        background-color: #e9ecef;
        font-weight: 500;
    }
    .search-select-loading,
    .search-select-no-results {
        padding: 10px 12px;
        color: #6c757d;
        text-align: center;
    }
</style>
@endPushOnce

@push('scripts')
<script>
(function() {
    const uniqueId = '{{ $uniqueId }}';
    const fieldName = '{{ $fieldName }}';
    const model = '{{ addslashes($model) }}';
    const limit = {{ $limit }};
    const apiUrl = '{{ route("search.items") }}';

    const wrapper = document.getElementById('wrapper_' + uniqueId);
    const input = document.getElementById('input_' + uniqueId);
    const dropdown = document.getElementById('dropdown_' + uniqueId);
    const hiddenInput = document.getElementById(fieldName);
    const resultsContainer = dropdown.querySelector('.search-select-results');
    const loadingEl = dropdown.querySelector('.search-select-loading');
    const noResultsEl = dropdown.querySelector('.search-select-no-results');

    let debounceTimer;
    let currentValue = hiddenInput.value;

    // فتح الـ dropdown
    function openDropdown() {
        dropdown.classList.add('show');
    }

    // إغلاق الـ dropdown
    function closeDropdown() {
        dropdown.classList.remove('show');
    }

    // عرض الـ loading
    function showLoading() {
        loadingEl.classList.remove('d-none');
        noResultsEl.classList.add('d-none');
        resultsContainer.innerHTML = '';
    }

    // إخفاء الـ loading
    function hideLoading() {
        loadingEl.classList.add('d-none');
    }

    // عرض رسالة "لا توجد نتائج"
    function showNoResults() {
        noResultsEl.classList.remove('d-none');
        resultsContainer.innerHTML = '';
    }

    // عرض النتائج
    function renderResults(items) {
        hideLoading();
        noResultsEl.classList.add('d-none');

        if (!items || items.length === 0) {
            showNoResults();
            return;
        }

        resultsContainer.innerHTML = items.map(item => `
            <div class="search-select-item ${item.id == currentValue ? 'selected' : ''}"
                 data-id="${item.id}"
                 data-text="${item.text}">
                ${item.text}
            </div>
        `).join('');

        // إضافة event listeners للنتائج
        resultsContainer.querySelectorAll('.search-select-item').forEach(el => {
            el.addEventListener('click', function() {
                selectItem(this.dataset.id, this.dataset.text);
            });
        });
    }

    // اختيار عنصر
    function selectItem(id, text) {
        currentValue = id;
        hiddenInput.value = id;
        input.value = text;
        closeDropdown();

        // Trigger change event
        hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
    }

    // البحث في الـ API
    function search(term) {
        showLoading();
        openDropdown();

        const displayColumn = '{{ $displayColumn ?? 'name' }}';
        const url = `${apiUrl}?model=${encodeURIComponent(model)}&limit=${limit}&term=${encodeURIComponent(term)}&displayColumn=${encodeURIComponent(displayColumn)}`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                hideLoading();
                // التعامل مع البيانات سواء كانت array مباشرة أو داخل results
                const items = Array.isArray(data) ? data : (data.results || []);
                renderResults(items);
            })
            .catch(error => {
                hideLoading();
                console.error('Search error:', error);
                showNoResults();
            });
    }

    // Event: الكتابة في الـ input
    input.addEventListener('input', function() {
        const term = this.value.trim();

        clearTimeout(debounceTimer);

        if (term.length < 1) {
            closeDropdown();
            return;
        }

        debounceTimer = setTimeout(() => {
            search(term);
        }, 300);
    });

    // Event: الـ focus على الـ input
    input.addEventListener('focus', function() {
        if (this.value.trim().length >= 1) {
            search(this.value.trim());
        } else {
            // عرض جميع البيانات عند الـ focus إذا لم يكن هناك بحث
            search('');
        }
    });

    // Event: الضغط خارج الـ component
    document.addEventListener('click', function(e) {
        if (!wrapper.contains(e.target)) {
            closeDropdown();
        }
    });

    // Event: الـ keyboard navigation
    input.addEventListener('keydown', function(e) {
        const items = resultsContainer.querySelectorAll('.search-select-item');
        const activeItem = resultsContainer.querySelector('.search-select-item.active');

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (!activeItem) {
                items[0]?.classList.add('active');
            } else {
                const next = activeItem.nextElementSibling;
                if (next) {
                    activeItem.classList.remove('active');
                    next.classList.add('active');
                    next.scrollIntoView({ block: 'nearest' });
                }
            }
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (activeItem) {
                const prev = activeItem.previousElementSibling;
                if (prev) {
                    activeItem.classList.remove('active');
                    prev.classList.add('active');
                    prev.scrollIntoView({ block: 'nearest' });
                }
            }
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (activeItem) {
                selectItem(activeItem.dataset.id, activeItem.dataset.text);
            }
        } else if (e.key === 'Escape') {
            closeDropdown();
        }
    });
})();
</script>
@endpush
