// Auto-detect API base URL: production vs local development
if (typeof API_BASE_URL === 'undefined') {
    var API_BASE_URL = '/api';
}

var api = {
    getToken() {
        return localStorage.getItem('auth_token');
    },

    getHeaders(includeAuth = false) {
        const headers = {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        };
        if (includeAuth) {
            const token = this.getToken();
            if (token) headers['Authorization'] = `Bearer ${token}`;
        }
        return headers;
    },

    async request(endpoint, options = {}) {
        const url = `${API_BASE_URL}${endpoint}`;
        const headers = this.getHeaders(options.auth);
        
        const fetchOptions = {
            method: options.method || 'GET',
            headers: headers,
            ...options
        };
        
        // Don't modify body if it's already a string (JSON.stringify was called)
        if (options.body && typeof options.body === 'object') {
            fetchOptions.body = JSON.stringify(options.body);
        } else if (options.body) {
            fetchOptions.body = options.body;
        }
        
        try {
            const response = await fetch(url, fetchOptions);
        
        try {
            const response = await fetch(url, fetchOptions);
            
            // Handle non-JSON responses
            const contentType = response.headers.get('content-type');
            let data;
            
            if (contentType && contentType.includes('application/json')) {
                data = await response.json();
            } else {
                const text = await response.text();
                data = { message: text, success: !response.ok };
            }
            
            if (!response.ok) {
                console.error('API Error:', response.status, data);
                throw data;
            }
            return data;
        } catch (error) {
            console.error('Request failed:', error);
            throw error;
        }
    },

    // Auth
    async register(userData) {
        return this.request('/register', {
            method: 'POST',
            body: userData
        });
    },

    async login(email, password) {
        return this.request('/login', {
            method: 'POST',
            body: { email, password }
        });
    },

    async logout() {
        return this.request('/logout', { method: 'POST', auth: true });
    },

    async getUser() {
        return this.request('/user', { auth: true });
    },

    // Products
    async getProducts(params = {}) {
        const query = new URLSearchParams(params).toString();
        return this.request(`/products${query ? '?' + query : ''}`);
    },

    async getProduct(id) {
        return this.request(`/products/${id}`);
    },

    // Cart
    async getCart() {
        return this.request('/cart', { auth: true });
    },

    async addToCart(productId, quantity = 1) {
        return this.request('/cart/add', {
            method: 'POST',
            auth: true,
            body: JSON.stringify({ product_id: productId, quantity })
        });
    },

    async updateCartItem(itemId, quantity) {
        return this.request(`/cart/items/${itemId}`, {
            method: 'PUT',
            auth: true,
            body: JSON.stringify({ quantity })
        });
    },

    async removeCartItem(itemId) {
        return this.request(`/cart/items/${itemId}`, {
            method: 'DELETE',
            auth: true
        });
    },

    async clearCart() {
        return this.request('/cart/clear', { method: 'DELETE', auth: true });
    },

    async getCartCount() {
        return this.request('/cart/count', { auth: true });
    },

    // Orders
    async checkout() {
        return this.request('/checkout', { method: 'POST', auth: true });
    },

    async getOrders() {
        return this.request('/orders', { auth: true });
    },

    async getOrder(id) {
        return this.request(`/orders/${id}`, { auth: true });
    },

    async payOrder(id, method) {
        return this.request(`/orders/${id}/pay`, {
            method: 'POST',
            auth: true,
            body: JSON.stringify({ method })
        });
    },

    async cancelOrder(id) {
        return this.request(`/orders/${id}/cancel`, {
            method: 'PUT',
            auth: true
        });
    }
};
