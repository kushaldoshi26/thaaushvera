import os
import re

directories = [
    'public/assets/js',
    'public/js',
    'public'
]

mapping = {
    'index.html': '/',
    'about.html': '/about',
    'philosophy.html': '/philosophy',
    'products.html': '/products',
    'product.html': '/product',
    'ritual.html': '/ritual',
    'contact.html': '/contact',
    'chart.html': '/cart',
    'profile.html': '/profile',
    'login.html': '/login',
    'admin-dashboard-premium.html': '/admin',
    'admin-orders.html': '/admin/orders',
    'admin-register.html': '/admin/register',
    'admin-inventory.html': '/admin/inventory',
    'admin-products.html': '/admin/products'
}

# we only parse .js files
for d in directories:
    if not os.path.exists(d):
        continue
    for root, dirs, files in os.walk(d):
        for f in files:
            if f.endswith('.js'):
                filepath = os.path.join(root, f)
                with open(filepath, 'r', encoding='utf-8') as file:
                    content = file.read()
                
                new_content = content
                for old, new in mapping.items():
                    # Replace exactly 'about.html' or "about.html"
                    new_content = re.sub(r"(['\"])" + old + r"(['\"])", r"\g<1>" + new + r"\g<2>", new_content)
                    
                    # Also replace with parameters like 'about.html?id=1' -> '/about?id=1'
                    new_content = re.sub(r"(['\"])" + old + r"\?", r"\g<1>" + new + r"?", new_content)

                if new_content != content:
                    with open(filepath, 'w', encoding='utf-8') as file:
                        file.write(new_content)
                    print(f"Updated JS links in {filepath}")
