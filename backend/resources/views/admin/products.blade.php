@extends('layouts.admin')
@section('title', 'Products')

@section('content')
<div class="page-bar">
    <div class="page-bar-title">Products</div>
    <div class="search-bar">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="productSearch" placeholder="Search products..." oninput="filterTable(this.value)">
    </div>
    <button class="btn btn-primary" onclick="openAddModal()">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add Product
    </button>
</div>

<div class="card">
    <div class="table-wrap">
        <table id="productsTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $p)
                <tr class="product-row" data-name="{{ strtolower($p->name) }}">
                    <td class="text-muted text-sm">{{ $p->id }}</td>
                    <td>
                        <img class="product-thumb"
                             src="{{ $p->image ?? asset('assets/img/product.jpeg') }}"
                             alt="{{ $p->name }}"
                             onerror="this.src='{{ asset('assets/img/product.jpeg') }}'">
                    </td>
                    <td><strong>{{ $p->name }}</strong></td>
                    <td class="text-muted">{{ $p->category->name ?? '—' }}</td>
                    <td><strong>₹{{ number_format($p->price, 2) }}</strong></td>
                    <td>
                        @if(($p->stock ?? 0) == 0)
                            <span class="badge badge-cancelled">Out of Stock</span>
                        @elseif(($p->stock ?? 0) <= 10)
                            <span class="badge badge-pending">{{ $p->stock }} — Low</span>
                        @else
                            <span class="badge badge-active">{{ $p->stock ?? '—' }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="flex gap-1">
                            <button class="btn btn-outline btn-sm" onclick="editProduct({{ $p->id }})">Edit</button>
                            <button class="btn btn-danger btn-sm" onclick="deleteProduct({{ $p->id }}, '{{ addslashes($p->name) }}')">Delete</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center; padding:2rem; color:var(--admin-muted);">No products yet. Add your first product!</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Add/Edit Modal --}}
<div class="modal-overlay" id="productModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title" id="modalTitle">Add Product</h3>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <form id="productForm" onsubmit="saveProduct(event)">
            <div class="modal-body">
                <input type="hidden" id="editProductId">
                <div class="grid-2">
                    <div class="admin-form-group">
                        <label>Product Name <span style="color:#ef4444">*</span></label>
                        <input type="text" id="pName" class="admin-input" required placeholder="e.g. Ashvattha Tea">
                    </div>
                    <div class="admin-form-group">
                        <label>Category</label>
                        <select id="pCategory" class="admin-input admin-select">
                            <option value="">— Select Category —</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="admin-form-group">
                    <label>Description <span style="color:#ef4444">*</span></label>
                    <textarea id="pDesc" class="admin-input admin-textarea" rows="3" required placeholder="Product description..."></textarea>
                </div>
                <div class="grid-2">
                    <div class="admin-form-group">
                        <label>Price (₹) <span style="color:#ef4444">*</span></label>
                        <input type="number" id="pPrice" class="admin-input" step="0.01" min="0" required placeholder="0.00">
                    </div>
                    <div class="admin-form-group">
                        <label>Original Price (₹)</label>
                        <input type="number" id="pOriginalPrice" class="admin-input" step="0.01" min="0" placeholder="MRP / before discount">
                    </div>
                </div>
                <div class="admin-form-group">
                    <label>Stock Quantity</label>
                    <input type="number" id="pStock" class="admin-input" min="0" placeholder="Leave blank if not tracked">
                </div>
                <div class="admin-form-group">
                    <label>Product Image</label>
                    <input type="file" id="pImageFile" class="admin-input" accept="image/*">
                    <input type="hidden" id="pImage">
                    <div id="pImagePreview" style="margin-top:10px; max-width:100px; display:none;"><img src="" style="width:100%; border-radius:4px;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary" id="saveBtn">Save Product</button>
            </div>
        </form>
    </div>
</div>

{{-- Confirm Delete Modal --}}
<div class="modal-overlay" id="deleteModal">
    <div class="modal" style="max-width:420px;">
        <div class="modal-header">
            <h3 class="modal-title">Delete Product</h3>
            <button class="modal-close" onclick="closeDelete()">×</button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete <strong id="deleteProductName"></strong>? This cannot be undone.</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeDelete()">Cancel</button>
            <button class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

// Table search
function filterTable(q) {
    const rows = document.querySelectorAll('.product-row');
    rows.forEach(r => r.style.display = r.dataset.name.includes(q.toLowerCase()) ? '' : 'none');
}

// Image Preview on Select
document.getElementById('pImageFile').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('pImagePreview');
    if (file) {
        preview.style.display = 'block';
        preview.querySelector('img').src = URL.createObjectURL(file);
    } else {
        if (!document.getElementById('pImage').value) {
            preview.style.display = 'none';
        }
    }
});

function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Add Product';
    document.getElementById('productForm').reset();
    document.getElementById('editProductId').value = '';
    document.getElementById('pImagePreview').style.display = 'none';
    document.getElementById('productModal').classList.add('open');
}
function closeModal() { document.getElementById('productModal').classList.remove('open'); }

async function editProduct(id) {
    try {
        const res = await fetch(`/api/products/${id}`);
        const data = await res.json();
        const p = data.data || data;
        document.getElementById('modalTitle').textContent = 'Edit Product';
        document.getElementById('editProductId').value = p.id;
        document.getElementById('pName').value = p.name || '';
        document.getElementById('pDesc').value = p.description || '';
        document.getElementById('pPrice').value = p.price || '';
        document.getElementById('pOriginalPrice').value = p.original_price || '';
        document.getElementById('pStock').value = p.stock || '';
        document.getElementById('pImage').value = p.image || '';
        document.getElementById('pImageFile').value = ''; // Reset file input
        const preview = document.getElementById('pImagePreview');
        if (p.image) {
            preview.style.display = 'block';
            preview.querySelector('img').src = p.image;
        } else {
            preview.style.display = 'none';
        }
        
        const catSel = document.getElementById('pCategory');
        if (p.category_id) catSel.value = p.category_id;
        document.getElementById('productModal').classList.add('open');
    } catch (e) {
        alert('Error loading product details');
    }
}

async function saveProduct(e) {
    e.preventDefault();
    const id = document.getElementById('editProductId').value;
    const btn = document.getElementById('saveBtn');
    btn.disabled = true; btn.textContent = 'Saving...';
    
    const formData = new FormData();
    formData.append('name', document.getElementById('pName').value);
    formData.append('description', document.getElementById('pDesc').value);
    formData.append('price', document.getElementById('pPrice').value);
    
    if (document.getElementById('pOriginalPrice').value) formData.append('original_price', document.getElementById('pOriginalPrice').value);
    if (document.getElementById('pStock').value) formData.append('stock', document.getElementById('pStock').value);
    if (document.getElementById('pCategory').value) formData.append('category_id', document.getElementById('pCategory').value);
    
    const fileInput = document.getElementById('pImageFile');
    if (fileInput && fileInput.files[0]) {
        formData.append('image_file', fileInput.files[0]);
    } else if (document.getElementById('pImage').value) {
        formData.append('image', document.getElementById('pImage').value);
    }

    if (id) {
        formData.append('_method', 'PUT');
    }

    try {
        const url = id ? `/api/admin/products/${id}` : '/api/admin/products';
        const method = 'POST'; // using POST due to _method spoofing for FormData
        const token = localStorage.getItem('auth_token');
        const res = await fetch(url, {
            method,
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF, ...(token ? { Authorization: 'Bearer ' + token } : {}) },
            body: formData
        });
        if (res.ok) {
            closeModal();
            window.location.reload();
        } else {
            const err = await res.json();
            alert(err.message || 'Failed to save product');
        }
    } catch { alert('Network error'); }
    finally { btn.disabled = false; btn.textContent = 'Save Product'; }
}

let deleteTarget = null;
function deleteProduct(id, name) {
    deleteTarget = id;
    document.getElementById('deleteProductName').textContent = name;
    document.getElementById('deleteModal').classList.add('open');
}
function closeDelete() { document.getElementById('deleteModal').classList.remove('open'); deleteTarget = null; }

document.getElementById('confirmDeleteBtn').addEventListener('click', async () => {
    if (!deleteTarget) return;
    const btn = document.getElementById('confirmDeleteBtn');
    btn.disabled = true; btn.textContent = 'Deleting...';
    try {
        const token = localStorage.getItem('auth_token');
        const res = await fetch(`/api/admin/products/${deleteTarget}`, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF, ...(token ? { Authorization: 'Bearer ' + token } : {}) }
        });
        if (res.ok) { closeDelete(); window.location.reload(); }
        else { const e = await res.json(); alert(e.message || 'Failed to delete'); }
    } catch { alert('Network error'); }
    finally { btn.disabled = false; btn.textContent = 'Delete'; }
});
</script>
@endpush
