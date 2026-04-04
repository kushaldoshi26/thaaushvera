@extends('layouts.admin')
@section('title', 'Categories')
@section('page-title', 'Categories Management')



@section('content')
<div class="page-bar">
    <div class="page-bar-title">Categories</div>
    <div class="search-bar">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="categorySearch" placeholder="Search categories..." oninput="filterTable(this.value)">
    </div>
    <button class="btn btn-primary" onclick="openAddModal()">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add Category
    </button>
</div>

<div class="card">
    <div class="table-wrap">
        <table id="categoriesTableMain">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="categoriesTable">
                <tr><td colspan="4" style="text-align:center; padding:2rem; color:var(--admin-muted);">Loading...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit Category Modal -->
<div class="modal-overlay" id="categoryModal">
    <div class="modal" style="max-width:500px;">
        <div class="modal-header">
            <h3 class="modal-title" id="modalTitle">Add Category</h3>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <form id="categoryForm">
            <div class="modal-body">
                <input type="hidden" id="categoryId">
                <div class="admin-form-group">
                    <label>Category Name <span style="color:#ef4444">*</span></label>
                    <input type="text" id="categoryName" class="admin-input" required placeholder="e.g. Wellness Teas">
                </div>
                <div class="admin-form-group">
                    <label>Description</label>
                    <textarea id="categoryDescription" class="admin-textarea" rows="3" placeholder="Category description..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Category</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const token = api.getToken();
const headers = { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' };

async function loadCategories() {
    try {
        const res = await fetch('{{ url("/api/categories") }}');
        const data = await res.json();
        const categories = data.data || data || [];
        const tbody = document.getElementById('categoriesTable');

        if (categories.length > 0) {
            tbody.innerHTML = categories.map(c => `
                <tr class="cat-row" data-name="${(c.name||'').toLowerCase()}">
                    <td class="text-muted text-sm">${c.id}</td>
                    <td><strong>${c.name}</strong></td>
                    <td class="text-muted">${c.description || '—'}</td>
                    <td>
                        <div class="flex gap-1">
                            <button class="btn btn-outline btn-sm" onclick="editCategory(${c.id}, '${(c.name||'').replace(/'/g,"\\'")}', '${(c.description||'').replace(/'/g,"\\'")}')">Edit</button>
                            <button class="btn btn-danger btn-sm" onclick="deleteCategory(${c.id})">Delete</button>
                        </div>
                    </td>
                </tr>
            `).join('');
        } else {
            tbody.innerHTML = '<tr><td colspan="4" style="text-align:center; padding:2rem; color:var(--admin-muted);">No categories found. Add your first one!</td></tr>';
        }
    } catch (e) {
        document.getElementById('categoriesTable').innerHTML = '<tr><td colspan="4" class="text-center">Error loading categories</td></tr>';
    }
}

function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Add Category';
    document.getElementById('categoryForm').reset();
    document.getElementById('categoryId').value = '';
    showModal();
}

function editCategory(id, name, description) {
    document.getElementById('modalTitle').textContent = 'Edit Category';
    document.getElementById('categoryId').value = id;
    document.getElementById('categoryName').value = name;
    document.getElementById('categoryDescription').value = description;
    showModal();
}

async function deleteCategory(id) {
    if (!confirm('Are you sure you want to delete this category?')) return;
    try {
        const res = await fetch(`{{ url('/api/admin/categories') }}/${id}`, {
            method: 'DELETE',
            headers
        });
        if (res.ok) {
            alert('Category deleted successfully');
            loadCategories();

            // If page was opened with ?open_add=1, open the Add Category modal automatically.
            document.addEventListener('DOMContentLoaded', function () {
                try {
                    const params = new URLSearchParams(window.location.search);
                    if (params.get('open_add') === '1') {
                        if (typeof openAddModal === 'function') {
                            openAddModal();
                            const url = new URL(window.location);
                            url.searchParams.delete('open_add');
                            window.history.replaceState({}, '', url);
                        }
                    }
                } catch (e) {
                    // ignore in older browsers
                }
            });
        } else {
            const err = await res.json();
            alert('Failed to delete: ' + (err.message || 'Unknown error'));
        }
    } catch (e) {
        alert('Error deleting category');
    }
}

function filterTable(q) {
    const rows = document.querySelectorAll('.cat-row');
    rows.forEach(r => r.style.display = r.dataset.name.includes(q.toLowerCase()) ? '' : 'none');
}

function showModal() {
    document.getElementById('categoryModal').classList.add('open');
}

function closeModal() {
    document.getElementById('categoryModal').classList.remove('open');
}

document.getElementById('categoryForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('categoryId').value;
    const payload = {
        name: document.getElementById('categoryName').value,
        description: document.getElementById('categoryDescription').value || null,
    };

    const url = id
        ? `{{ url('/api/admin/categories') }}/${id}`
        : '{{ url('/api/admin/categories') }}';

    try {
        const res = await fetch(url, {
            method: id ? 'PUT' : 'POST',
            headers: { ...headers, 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        if (res.ok) {
            alert(id ? 'Category updated!' : 'Category created!');
            closeModal();
            loadCategories();
        } else {
            const err = await res.json();
            alert('Failed: ' + (err.message || JSON.stringify(err.errors || {})));
        }
    } catch (e) {
        alert('Error saving category');
    }
});

loadCategories();
</script>
@endpush
