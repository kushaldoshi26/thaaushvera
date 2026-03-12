@extends('layouts.admin')
@section('title', 'Analytics & Reports')
@section('page-title', 'Dashboard Analytics')

@section('content')
<div class="analytics-container">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="stat-card">
            <div class="stat-label">Total Revenue</div>
            <div class="stat-value" id="totalRevenue">$0.00</div>
            <div class="stat-trend positive">↑ 12% from last month</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Orders</div>
            <div class="stat-value" id="totalOrders">0</div>
            <div class="stat-trend positive">↑ 8% from last month</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Active Users</div>
            <div class="stat-value" id="activeUsers">0</div>
            <div class="stat-trend neutral">→ Stable</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Avg Order Value</div>
            <div class="stat-value" id="avgOrderValue">$0.00</div>
            <div class="stat-trend positive">↑ 5% from last month</div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Sales Chart -->
        <div class="admin-card">
            <div class="card-header">
                <h3>Sales Trend</h3>
                <select id="periodSelect" onchange="loadSalesData()">
                    <option value="day">Today</option>
                    <option value="week">Last 7 Days</option>
                    <option value="month" selected>Last 30 Days</option>
                    <option value="year">Last Year</option>
                </select>
            </div>
            <canvas id="salesChart" style="height: 300px;"></canvas>
        </div>

        <!-- Top Products Chart -->
        <div class="admin-card">
            <div class="card-header">
                <h3>Top 10 Products</h3>
            </div>
            <canvas id="topProductsChart" style="height: 300px;"></canvas>
        </div>
    </div>

    <!-- Top Customers & Products -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Top Customers -->
        <div class="admin-card">
            <h3 class="mb-4 font-bold">Top Customers</h3>
            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Orders</th>
                            <th>Total Spent</th>
                        </tr>
                    </thead>
                    <tbody id="topCustomersAble">
                        <tr><td colspan="3" class="text-center">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Product Performance -->
        <div class="admin-card">
            <h3 class="mb-4 font-bold">Product Performance</h3>
            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Sold</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody id="topProductsTable">
                        <tr><td colspan="3" class="text-center">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Export Section -->
    <div class="admin-card mb-8">
        <h3 class="mb-4 font-bold">Export Data</h3>
        <div class="flex flex-wrap gap-2">
            <button class="btn bg-blue-600" onclick="exportOrders()">Export Orders</button>
            <button class="btn bg-green-600" onclick="exportUsers()">Export Users</button>
            <button class="btn bg-purple-600" onclick="exportProducts()">Export Products</button>
        </div>
    </div>

    <!-- Detailed Tables -->
    <div class="admin-card">
        <h3 class="mb-4 font-bold">Recent Orders</h3>
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody id="recentOrdersTable">
                    <tr><td colspan="5" class="text-center">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.analytics-container {
    padding: 20px;
}

.stat-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.stat-label {
    color: #6b7280;
    font-size: 14px;
    margin-bottom: 8px;
}

.stat-value {
    font-size: 28px;
    font-weight: bold;
    color: #1f2937;
    margin-bottom: 8px;
}

.stat-trend {
    font-size: 12px;
}

.stat-trend.positive {
    color: #059669;
}

.stat-trend.negative {
    color: #dc2626;
}

.stat-trend.neutral {
    color: #6b7280;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.card-header select {
    padding: 8px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 4px;
    background: white;
    cursor: pointer;
}

.btn {
    padding: 10px 16px;
    border: none;
    border-radius: 4px;
    color: white;
    cursor: pointer;
    font-size: 14px;
    transition: opacity 0.3s;
}

.btn:hover {
    opacity: 0.9;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const API_URL = 'http://localhost:8000/api';
const token = localStorage.getItem('token');

let salesChart, topProductsChart;

async function loadDashboard() {
    try {
        // Load summary stats
        const [orders, users, products] = await Promise.all([
            fetch(`${API_URL}/admin/orders`, { headers: { 'Authorization': `Bearer ${token}` } }).then(r => r.json()),
            fetch(`${API_URL}/admin/users`, { headers: { 'Authorization': `Bearer ${token}` } }).then(r => r.json()),
            fetch(`${API_URL}/products`, { headers: { 'Authorization': `Bearer ${token}` } }).then(r => r.json())
        ]);

        // Calculate totals
        let totalRevenue = 0;
        let orderCount = 0;
        if (orders.data) {
            orderCount = orders.data.length;
            totalRevenue = orders.data.reduce((sum, order) => sum + (order.total_amount || 0), 0);
        }

        document.getElementById('totalRevenue').textContent = '$' + totalRevenue.toFixed(2);
        document.getElementById('totalOrders').textContent = orderCount;
        document.getElementById('activeUsers').textContent = users.data ? users.data.length : 0;
        document.getElementById('avgOrderValue').textContent = '$' + (orderCount > 0 ? (totalRevenue / orderCount).toFixed(2) : '0.00');

        // Load charts
        await loadSalesData();
        await loadTopProducts();
        await loadTopCustomers();
        await loadRecentOrders();
    } catch (error) {
        console.error('Error loading dashboard:', error);
    }
}

async function loadSalesData() {
    try {
        const period = document.getElementById('periodSelect').value;
        const response = await fetch(`${API_URL}/admin/analytics/sales-report?period=${period}`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const data = await response.json();

        const labels = data.sales_data.map(d => d.period);
        const revenues = data.sales_data.map(d => d.revenue || 0);

        if (salesChart) {
            salesChart.destroy();
        }

        const ctx = document.getElementById('salesChart').getContext('2d');
        salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Revenue ($)',
                    data: revenues,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                }
            }
        });
    } catch (error) {
        console.error('Error loading sales data:', error);
    }
}

async function loadTopProducts() {
    try {
        const response = await fetch(`${API_URL}/admin/analytics/top-products`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const data = await response.json();

        const labels = data.top_products.map(p => p.name).slice(0, 10);
        const sold = data.top_products.map(p => p.total_sold).slice(0, 10);

        if (topProductsChart) {
            topProductsChart.destroy();
        }

        const ctx = document.getElementById('topProductsChart').getContext('2d');
        topProductsChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: sold,
                    backgroundColor: [
                        '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6',
                        '#ec4899', '#14b8a6', '#f97316', '#6366f1', '#84cc16'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Update products table
        const tbody = document.getElementById('topProductsTable');
        tbody.innerHTML = data.top_products.slice(0, 10).map(p => `
            <tr>
                <td>${p.name}</td>
                <td>${p.total_sold}</td>
                <td>$${(p.total_revenue || 0).toFixed(2)}</td>
            </tr>
        `).join('');
    } catch (error) {
        console.error('Error loading top products:', error);
    }
}

async function loadTopCustomers() {
    try {
        const response = await fetch(`${API_URL}/admin/analytics/top-customers`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const data = await response.json();

        const tbody = document.getElementById('topCustomersAble');
        tbody.innerHTML = data.top_customers.map(c => `
            <tr>
                <td>${c.user.name}</td>
                <td>${c.order_count}</td>
                <td>$${(c.total_spent || 0).toFixed(2)}</td>
            </tr>
        `).join('');
    } catch (error) {
        console.error('Error loading top customers:', error);
    }
}

async function loadRecentOrders() {
    try {
        const response = await fetch(`${API_URL}/admin/orders`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const data = await response.json();

        const tbody = document.getElementById('recentOrdersTable');
        tbody.innerHTML = (data.data || []).slice(0, 10).map(o => `
            <tr>
                <td>#${o.id}</td>
                <td>${o.user?.name || 'Guest'}</td>
                <td>$${(o.total_amount || 0).toFixed(2)}</td>
                <td><span class="badge badge-${o.status}">${o.status}</span></td>
                <td>${new Date(o.created_at).toLocaleDateString()}</td>
            </tr>
        `).join('');
    } catch (error) {
        console.error('Error loading recent orders:', error);
    }
}

async function exportOrders() {
    window.location.href = `${API_URL}/admin/export/orders`;
}

async function exportUsers() {
    window.location.href = `${API_URL}/admin/export/users`;
}

async function exportProducts() {
    window.location.href = `${API_URL}/admin/export/products`;
}

// Load dashboard on page load
loadDashboard();
</script>
@endsection
