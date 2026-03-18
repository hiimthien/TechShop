# TechShop API Design v1

## Auth
- POST /api/register
- POST /api/login
- POST /api/logout
- GET /api/me

## Catalog
- GET /api/products
- GET /api/products/{slug}
- GET /api/categories
- GET /api/brands

## Cart
- GET /api/cart
- POST /api/cart/items
- PUT /api/cart/items/{id}
- DELETE /api/cart/items/{id}

## Orders
- POST /api/orders
- GET /api/orders
- GET /api/orders/{id}