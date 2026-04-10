import os
import re

directories = [
    'resources/views',
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
    'admin.html': '/admin',
    'admin-dashboard-premium.html': '/admin',
    'admin-orders.html': '/admin/orders',
    'admin-register.html': '/admin/register',
    'admin-inventory.html': '/admin/inventory',
    'admin-products.html': '/admin/products',
    'admin-credentials-generator.html': '/admin/credentials-generator'
}

for d in directories:
    if not os.path.exists(d):
        continue
    for root, dirs, files in os.walk(d):
        for f in files:
            if f.endswith('.blade.php') or f.endswith('.js'):
                filepath = os.path.join(root, f)
                with open(filepath, 'r', encoding='utf-8') as file:
                    content = file.read()
                
                new_content = content
                for old, new in mapping.items():
                    # Replace 'product.html' -> '/product'
                    # But if it's '/product.html', replacing 'product.html' gives '//product', so we should replace '/product.html' with '/product'
                    new_content = new_content.replace('/' + old, new)
                    
                    # replace normal 'product.html' -> '/product'
                    new_content = re.sub(r"(['\"])" + old + r"(['\"])", r"\g<1>" + new + r"\g<2>", new_content)
                    
                    # Replace 'product.html?id=1'
                    new_content = re.sub(r"(['\"])" + old + r"\?", r"\g<1>" + new + r"?", new_content)

                if new_content != content:
                    with open(filepath, 'w', encoding='utf-8') as file:
                        file.write(new_content)
                    print(f"Updated links in {filepath}")
