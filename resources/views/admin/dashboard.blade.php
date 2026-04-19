@extends('layouts.admin')
@section('title', 'Dashboard')

@push('styles')
<style>
.dash-wrap { padding: 24px 28px; font-family: 'Inter', sans-serif; }

/* Stat Cards */
.kpi-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px; }
.kpi-card {
  background:#fff; border:1px solid #e9ecef; border-radius:14px;
  padding:22px 20px; display:flex; align-items:center; gap:14px;
  transition:transform .2s,box-shadow .2s;
}
.kpi-card:hover { transform:translateY(-3px); box-shadow:0 8px 24px rgba(0,0,0,.08); }
.kpi-icon { width:50px;height:50px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0; }
.kpi-icon.blue   { background:#eff6ff; }
.kpi-icon.green  { background:#f0fdf4; }
.kpi-icon.amber  { background:#fffbeb; }
.kpi-icon.purple { background:#f5f3ff; }
.kpi-label { font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.7px;color:#6b7280;margin-bottom:4px; }
.kpi-value { font-size:26px;font-weight:700;color:#111827;line-height:1; }
.kpi-sub   { font-size:11px;color:#10b981;margin-top:4px; }
.kpi-sub.down { color:#ef4444; }

/* Chart Row */
.chart-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:20px; }
.chart-card-wrap { display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; margin-bottom:20px; }
.chart-card {
  background:#fff; border:1px solid #e9ecef; border-radius:14px; padding:22px;
}
.chart-card h3 { font-size:14px;font-weight:600;color:#374151;margin:0 0 18px; }
.chart-card canvas { max-height:260px; }

/* Tables */
.table-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
.table-card { background:#fff;border:1px solid #e9ecef;border-radius:14px;padding:20px; }
.table-card h3 { font-size:14px;font-weight:600;color:#374151;margin:0 0 14px;display:flex;justify-content:space-between;align-items:center; }
.table-card h3 a { font-size:12px;font-weight:500;color:#c9a96e;text-decoration:none; }
table.mini { width:100%;border-collapse:collapse; }
table.mini th { font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.6px;color:#9ca3af;padding:8px 10px;text-align:left;border-bottom:1px solid #f3f4f6; }
table.mini td { font-size:13px;color:#374151;padding:10px;border-bottom:1px solid #f9fafb; }
table.mini tr:last-child td { border-bottom:none; }
.badge-sm { padding:2px 10px;border-radius:20px;font-size:11px;font-weight:600; }
.badge-pending    { background:#fef3c7;color:#92400e; }
.badge-paid       { background:#d1fae5;color:#065f46; }
.badge-processing { background:#dbeafe;color:#1e40af; }
.badge-shipped    { background:#e0e7ff;color:#3730a3; }
.badge-delivered  { background:#d1fae5;color:#065f46; }
.badge-cancelled  { background:#fee2e2;color:#991b1b; }

/* Loading spinner */
.spin { display:inline-block;width:18px;height:18px;border:2px solid #e5e7eb;border-top-color:#c9a96e;border-radius:50%;animation:sp .7s linear infinite;vertical-align:middle; }
@keyframes sp{to{transform:rotate(360deg)}}

/* Quick actions */
.quick-acts { display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap; }
.qa-btn { padding:9px 18px;border-radius:8px;border:1px solid #e9ecef;background:#fff;font-size:13px;font-weight:500;cursor:pointer;transition:all .2s;color:#374151;display:flex;align-items:center;gap:7px; }
.qa-btn:hover { background:#fafafa;border-color:#c9a96e;color:#c9a96e; }

.ai-shortcut { background:linear-gradient(135deg,#c9a96e15,#8b5cf610);border-color:#c9a96e40;color:#92400e; }
.ai-shortcut:hover { background:linear-gradient(135deg,#c9a96e25,#8b5cf620); }
</style>
@endpush

@section('content')
<div class="dash-wrap">

  {{-- Quick Actions --}}
  <div class="quick-acts">
    <button class="qa-btn" onclick="location.href='{{ url('/admin/products') }}'">📦 Add Product</button>
    <button class="qa-btn" onclick="location.href='{{ url('/admin/orders') }}'">🛒 View Orders</button>
    <button class="qa-btn" onclick="location.href='{{ url('/admin/banners') }}'">🎨 Manage Banners</button>
    <button class="qa-btn ai-shortcut" onclick="location.href='{{ url('/admin/ai') }}'">🤖 AI Agent</button>
    <button class="qa-btn" onclick="location.href='{{ url('/admin/analytics') }}'">📈 Analytics</button>
  </div>

  {{-- KPI Cards --}}
  <div class="kpi-grid">
    <div class="kpi-card">
      <div class="kpi-icon blue">📦</div>
      <div>
        <div class="kpi-label">Total Products</div>
        <div class="kpi-value" id="kpiProducts"><span class="spin"></span></div>
      </div>
    </div>
    <div class="kpi-card">
      <div class="kpi-icon amber">🛒</div>
      <div>
        <div class="kpi-label">Total Orders</div>
        <div class="kpi-value" id="kpiOrders"><span class="spin"></span></div>
        <div class="kpi-sub" id="kpiPending"></div>
      </div>
    </div>
    <div class="kpi-card">
      <div class="kpi-icon green">💰</div>
      <div>
        <div class="kpi-label">Revenue</div>
        <div class="kpi-value" id="kpiRevenue"><span class="spin"></span></div>
        <div class="kpi-sub" id="kpiToday"></div>
      </div>
    </div>
    <div class="kpi-card">
      <div class="kpi-icon purple">👥</div>
      <div>
        <div class="kpi-label">Total Users</div>
        <div class="kpi-value" id="kpiUsers"><span class="spin"></span></div>
      </div>
    </div>
  </div>

  {{-- Charts Row 1 --}}
  <div class="chart-row">
    <div class="chart-card">
      <h3>📈 Monthly Revenue</h3>
      <canvas id="revenueChart"></canvas>
    </div>
    <div class="chart-card">
      <h3>📋 Order Status Distribution</h3>
      <canvas id="orderStatusChart"></canvas>
    </div>
  </div>

  {{-- Charts Row 2 --}}
  <div class="chart-card-wrap">
    <div class="chart-card">
      <h3>📅 Daily Orders (Last 7 days)</h3>
      <canvas id="dailyOrderChart"></canvas>
    </div>
    <div class="chart-card">
      <h3>🌟 Top Products</h3>
      <canvas id="topProductChart"></canvas>
    </div>
    <div class="chart-card">
      <h3>👤 User Registrations</h3>
      <canvas id="userGrowthChart"></canvas>
    </div>
  </div>

  {{-- Tables --}}
  <div class="table-row">
    <div class="table-card">
      <h3>Recent Orders <a href="{{ url('/admin/orders') }}">View All →</a></h3>
      <table class="mini">
        <thead><tr><th>ID</th><th>Customer</th><th>Amount</th><th>Status</th></tr></thead>
        <tbody id="recentOrdersBody"><tr><td colspan="4" style="text-align:center;color:#9ca3af;padding:20px"><span class="spin"></span></td></tr></tbody>
      </table>
    </div>
    <div class="table-card">
      <h3>Low Stock Alert <a href="{{ url('/admin/inventory') }}">Manage →</a></h3>
      <table class="mini">
        <thead><tr><th>Product</th><th>Stock</th><th>Status</th></tr></thead>
        <tbody id="lowStockBody"><tr><td colspan="3" style="text-align:center;color:#9ca3af;padding:20px"><span class="spin"></span></td></tr></tbody>
      </table>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const token = localStorage.getItem('auth_token');
const authHeaders = token ? { 'Authorization': 'Bearer ' + token } : {};
const fmt = n => '₹' + parseFloat(n||0).toLocaleString('en-IN', {maximumFractionDigits:0});

Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.color = '#6b7280';
const GOLD = '#c9a96e';
const GOLD2 = 'rgba(201,169,110,.15)';
const PURPLE = '#8b5cf6';

async function loadDashboard() {
  try {
    const [statsRes, productsRes] = await Promise.all([
      fetch('{{ url("/api/admin/dashboard") }}', {headers: authHeaders}).then(r=>r.ok?r.json():{}).catch(()=>({})),
      fetch('{{ url("/api/products") }}').then(r=>r.json()).catch(()=>({data:[]})),
    ]);

    const stats = statsRes;
    const products = productsRes.data || productsRes || [];

    // KPI Cards
    document.getElementById('kpiProducts').textContent = stats.total_products ?? products.length ?? 0;
    document.getElementById('kpiOrders').textContent   = stats.total_orders   ?? 0;
    document.getElementById('kpiRevenue').textContent  = fmt(stats.total_revenue);
    document.getElementById('kpiUsers').textContent    = stats.total_users    ?? 0;
    if (stats.pending_orders) document.getElementById('kpiPending').textContent = stats.pending_orders + ' pending';
    if (stats.today_revenue)  document.getElementById('kpiToday').textContent   = 'Today: ' + fmt(stats.today_revenue);

    // Revenue Chart
    const monthly = stats.monthly_revenue || [];
    new Chart(document.getElementById('revenueChart'), {
      type: 'line',
      data: {
        labels: monthly.length ? monthly.map(m => m.month) : ['Jan','Feb','Mar','Apr','May','Jun'],
        datasets: [{
          label: 'Revenue (₹)',
          data: monthly.length ? monthly.map(m => m.revenue) : [0,0,0,0,0,0],
          borderColor: GOLD, backgroundColor: GOLD2,
          fill: true, tension: .4, pointRadius: 4, pointBackgroundColor: GOLD,
        }]
      },
      options: { responsive:true, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}} }
    });

    // Order Status Donut
    const statusLabels = ['Pending','Processing','Shipped','Delivered','Cancelled'];
    const statusData   = stats.status_counts ? [
      stats.status_counts.pending    || 0,
      stats.status_counts.processing || 0,
      stats.status_counts.shipped    || 0,
      stats.status_counts.delivered  || 0,
      stats.status_counts.cancelled  || 0,
    ] : [1, 0, 0, 0, 0];

    new Chart(document.getElementById('orderStatusChart'), {
      type: 'doughnut',
      data: {
        labels: statusLabels,
        datasets: [{
          data: statusData,
          backgroundColor: ['#fbbf24','#60a5fa','#818cf8','#34d399','#f87171'],
          borderWidth: 2, borderColor: '#fff',
        }]
      },
      options: { responsive:true, cutout:'65%', plugins:{legend:{position:'bottom'}} }
    });

    // Daily Orders bar (last 7 days)
    const days = [];
    for(let i=6;i>=0;i--){
      const d = new Date(); d.setDate(d.getDate()-i);
      days.push(d.toLocaleDateString('en-IN',{weekday:'short'}));
    }
    const dailyCounts = stats.daily_orders || Array(7).fill(0);
    new Chart(document.getElementById('dailyOrderChart'), {
      type:'bar',
      data:{ labels:days, datasets:[{label:'Orders', data:dailyCounts, backgroundColor:GOLD2, borderColor:GOLD, borderWidth:2, borderRadius:6 }]},
      options:{ responsive:true, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true,ticks:{precision:0}}} }
    });

    // Top Products
    const topProds = stats.top_selling_products || [];
    new Chart(document.getElementById('topProductChart'), {
      type:'bar',
      data:{
        labels: topProds.length ? topProds.map(p=>p.name?.substring(0,14)+'…') : ['No data'],
        datasets:[{label:'Units Sold', data: topProds.map(p=>p.sold||0), backgroundColor:PURPLE+'55', borderColor:PURPLE, borderWidth:2, borderRadius:6}]
      },
      options:{ indexAxis:'y', responsive:true, plugins:{legend:{display:false}}, scales:{x:{beginAtZero:true}} }
    });

    // User Growth (simulated since we don't have monthly user data)
    new Chart(document.getElementById('userGrowthChart'), {
      type:'line',
      data:{
        labels: ['Jan','Feb','Mar','Apr','May','Jun'],
        datasets:[{
          label:'Users', data: stats.monthly_users || [0,0,0,0,0, stats.total_users||0],
          borderColor: PURPLE, backgroundColor: PURPLE+'15',
          fill:true, tension:.4, pointRadius:4, pointBackgroundColor:PURPLE,
        }]
      },
      options:{ responsive:true, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true,ticks:{precision:0}}} }
    });

    // Recent Orders Table
    if (stats.recent_orders) {
      buildOrdersTable(stats.recent_orders);
    }

    // Low Stock
    const lowStock = products.filter(p => p.stock <= 10);
    const lsBody   = document.getElementById('lowStockBody');
    lsBody.innerHTML = lowStock.length ? lowStock.slice(0,6).map(p=>`
      <tr>
        <td>${p.name?.substring(0,20)}</td>
        <td><strong style="color:${p.stock==0?'#ef4444':'#f59e0b'}">${p.stock}</strong></td>
        <td><span class="badge-sm ${p.stock==0?'badge-cancelled':'badge-pending'}">${p.stock==0?'Out of Stock':'Low Stock'}</span></td>
      </tr>`).join('') : '<tr><td colspan="3" style="text-align:center;color:#9ca3af;padding:16px">All products well-stocked ✓</td></tr>';

  } catch(err) {
    console.warn('Dashboard load error:', err);
    // Set zeros gracefully
    ['kpiProducts','kpiOrders','kpiRevenue','kpiUsers'].forEach(id => {
      const el = document.getElementById(id);
      if(el) el.textContent = id === 'kpiRevenue' ? '₹0' : '0';
    });
    buildOrdersTable([]);
    document.getElementById('lowStockBody').innerHTML = '<tr><td colspan="3" style="text-align:center;color:#9ca3af;padding:16px">No data available</td></tr>';
  }
}

function buildOrdersTable(orders) {
  const tbody = document.getElementById('recentOrdersBody');
  tbody.innerHTML = orders.length ? orders.slice(0,5).map(o=>`
    <tr>
      <td><strong>#${o.id}</strong></td>
      <td>${o.user?.name||'—'}</td>
      <td>${fmt(o.total_amount)}</td>
      <td><span class="badge-sm badge-${o.status}">${o.status}</span></td>
    </tr>`).join('') : '<tr><td colspan="4" style="text-align:center;color:#9ca3af;padding:16px">No orders yet</td></tr>';
}

loadDashboard();
</script>
@endpush
