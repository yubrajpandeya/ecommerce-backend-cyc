# E-commerce API Documentation

## Base URL

```
http://localhost:8000/api/v1
```

## Response Format

All API responses follow this standard format:

```json
{
  "success": true,
  "data": {...}
}
```

---

## Authentication API

### Register User

**Endpoint:** `POST /auth/register`

**Description:** Register a new user account.

**Request Body:**

```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Response:**

```json
{
    "success": true,
    "message": "User registered successfully.",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "email_verified_at": null,
            "created_at": "2024-01-15T10:30:00Z"
        },
        "token": "1|abc123def456ghi789",
        "token_type": "Bearer"
    }
}
```

### Login User

**Endpoint:** `POST /auth/login`

**Description:** Authenticate user and return access token.

**Request Body:**

```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response:**

```json
{
    "success": true,
    "message": "Login successful.",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "email_verified_at": null,
            "created_at": "2024-01-15T10:30:00Z"
        },
        "token": "1|abc123def456ghi789",
        "token_type": "Bearer"
    }
}
```

### Forgot Password

**Endpoint:** `POST /auth/forgot-password`

**Description:** Send password reset link to user's email.

**Request Body:**

```json
{
    "email": "john@example.com"
}
```

**Response:**

```json
{
    "success": true,
    "message": "Password reset link has been sent to your email address."
}
```

### Reset Password

**Endpoint:** `POST /auth/reset-password`

**Description:** Reset user password using reset token.

**Request Body:**

```json
{
    "email": "john@example.com",
    "token": "reset-token-here",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}
```

**Response:**

```json
{
    "success": true,
    "message": "Password has been reset successfully."
}
```

### Get User Profile

**Endpoint:** `GET /auth/profile`

**Description:** Get authenticated user's profile information.

**Headers:**

```
Authorization: Bearer {token}
```

**Response:**

```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "email_verified_at": null,
            "created_at": "2024-01-15T10:30:00Z",
            "updated_at": "2024-01-15T10:30:00Z"
        }
    }
}
```

### Update User Profile

**Endpoint:** `PUT /auth/profile`

**Description:** Update authenticated user's profile information.

**Headers:**

```
Authorization: Bearer {token}
```

**Request Body:**

```json
{
    "name": "John Updated",
    "email": "john.updated@example.com"
}
```

**Response:**

```json
{
    "success": true,
    "message": "Profile updated successfully.",
    "data": {
        "user": {
            "id": 1,
            "name": "John Updated",
            "email": "john.updated@example.com",
            "email_verified_at": null,
            "created_at": "2024-01-15T10:30:00Z",
            "updated_at": "2024-01-16T08:45:00Z"
        }
    }
}
```

### Change Password

**Endpoint:** `POST /auth/change-password`

**Description:** Change authenticated user's password.

**Headers:**

```
Authorization: Bearer {token}
```

**Request Body:**

```json
{
    "current_password": "currentpassword123",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}
```

**Response:**

```json
{
    "success": true,
    "message": "Password changed successfully. Please login again with your new password."
}
```

### Logout User

**Endpoint:** `POST /auth/logout`

**Description:** Revoke user's access token (logout).

**Headers:**

```
Authorization: Bearer {token}
```

**Response:**

```json
{
    "success": true,
    "message": "Logged out successfully."
}
```

---

## Categories API

### Get Active Categories

**Endpoint:** `GET /categories`

**Description:** Retrieve all active categories for the frontend navigation and filtering.

**Parameters:** None

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Electronics",
            "slug": "electronics",
            "description": "Latest electronic gadgets and devices"
        }
    ]
}
```

---

## Products API

### Get Products (Paginated)

**Endpoint:** `GET /products`

**Description:** Retrieve active products with pagination support.

**Query Parameters:**

-   `per_page` (optional): Number of products per page (default: 12)
-   `page` (optional): Page number (default: 1)

**Example Request:**

```
GET /products?per_page=8&page=2
```

**Response:**

```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "category_id": 1,
                "name": "Wireless Bluetooth Headphones",
                "slug": "wireless-bluetooth-headphones",
                "description": "High-quality wireless headphones with noise cancellation",
                "price": "2999.00",
                "stock": 50,
                "is_featured": true,
                "is_upcoming": false,
                "available_from": null,
                "image_url": "https://api.chooseyourcart.com/storage/products/01K3V39HPB2JERPBX4PC3GRXTR.jpg",
                "category": {
                    "id": 1,
                    "name": "Electronics",
                    "slug": "electronics"
                }
            }
        ],
        "first_page_url": "https://api.chooseyourcart.com/api/v1/products?page=1",
        "from": 1,
        "last_page": 5,
        "last_page_url": "https://api.chooseyourcart.com/api/v1/products?page=5",
        "links": [
            {
                "url": null,
                "label": "&laquo; Previous",
                "active": false
            },
            {
                "url": "https://api.chooseyourcart.com/api/v1/products?page=1",
                "label": "1",
                "active": true
            },
            {
                "url": "https://api.chooseyourcart.com/api/v1/products?page=2",
                "label": "2",
                "active": false
            }
        ],
        "next_page_url": "https://api.chooseyourcart.com/api/v1/products?page=2",
        "path": "https://api.chooseyourcart.com/api/v1/products",
        "per_page": 12,
        "prev_page_url": null,
        "to": 12,
        "total": 58
    }
}
```

### Search Products

**Endpoint:** `GET /products/search`

**Description:** Search products by name or description with optional category filtering.

**Query Parameters:**

-   `q` (optional): Search query string
-   `category_id` (optional): Filter by category ID
-   `per_page` (optional): Number of products per page (default: 12)
-   `page` (optional): Page number (default: 1)

**Example Requests:**

```
GET /products/search?q=headphones
GET /products/search?q=cotton&category_id=2
GET /products/search?category_id=1&per_page=8
```

**Response:** Same pagination format as `/products` endpoint above.

### Get Featured Products

**Endpoint:** `GET /products/featured`

**Description:** Retrieve products marked as featured for homepage display.

**Query Parameters:**

-   `per_page` (optional): Number of products per page (default: 8)
-   `page` (optional): Page number (default: 1)

**Example Request:**

```
GET /products/featured?per_page=6
```

**Response:** Same pagination format as `/products` endpoint above, but only featured products.

### Get Upcoming Products

**Endpoint:** `GET /products/upcoming`

**Description:** Retrieve products marked as upcoming, ordered by availability date.

**Query Parameters:**

-   `per_page` (optional): Number of products per page (default: 12)
-   `page` (optional): Page number (default: 1)

**Example Request:**

```
GET /products/upcoming?per_page=10
```

**Response:**

```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 15,
        "category_id": 1,
        "name": "iPhone 16 Pro",
        "slug": "iphone-16-pro",
        "description": "Latest iPhone with advanced features",
        "price": "129999.00",
        "stock": 0,
        "is_featured": true,
        "is_upcoming": true,
        "available_from": "2024-09-15",
        "image_url": "http://localhost:8000/storage/products/03M5X41JPD4LGTRDY6RE5ITZWV.jpg",
        "category": {
          "id": 1,
          "name": "Electronics",
          "slug": "electronics"
        }
      }
    ],
            "first_page_url": "http://localhost:8000/api/v1/products/upcoming?page=1",
        "from": 1,
        "last_page": 2,
        "last_page_url": "http://localhost:8000/api/v1/products/upcoming?page=2",
        "links": [...],
        "next_page_url": "http://localhost:8000/api/v1/products/upcoming?page=2",
        "path": "http://localhost:8000/api/v1/products/upcoming",
    "per_page": 12,
    "prev_page_url": null,
    "to": 12,
    "total": 23
  }
}
```

---

## Sliders API

### Get Active Sliders

**Endpoint:** `GET /sliders`

**Description:** Retrieve all active sliders for homepage carousel, ordered by position.

**Parameters:** None

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "Electronics Tv"
            "link_url": "https://example.com/summer-sale",
            "position": 1,
            "image_url": "http://localhost:8000/storage/sliders/01K3V39HPB2JERPBX4PC3GRXTR.jpg"
        }
    ]
}
```

---

## Data Types

### Product Object

```typescript
interface Product {
    id: number;
    category_id: number;
    name: string;
    slug: string;
    description: string | null;
    price: string; // Decimal as string (e.g., "2999.00")
    stock: number;
    is_featured: boolean;
    is_upcoming: boolean;
    available_from: string | null; // Date in YYYY-MM-DD format
    image_url: string | null; // Full URL to product image
    category: {
        id: number;
        name: string;
        slug: string;
    };
}
```

### Category Object

```typescript
interface Category {
    id: number;
    name: string;
    slug: string;
    description: string | null;
}
```

### Slider Object

```typescript
interface Slider {
    id: number;
    title: string | null;
    link_url: string | null;
    position: number;
    image_url: string | null; // Full URL to slider image
}
```

### Pagination Object

```typescript
interface PaginatedResponse<T> {
    current_page: number;
    data: T[];
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: Array<{
        url: string | null;
        label: string;
        active: boolean;
    }>;
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
}
```

---

## Error Handling

All endpoints return appropriate HTTP status codes:

-   `200 OK` - Successful request
-   `400 Bad Request` - Invalid parameters
-   `404 Not Found` - Resource not found
-   `500 Internal Server Error` - Server error

Error responses follow this format:

```json
{
    "success": false,
    "message": "Error description",
    "errors": {
        "field_name": ["Validation error message"]
    }
}
```

---

## Notes for Frontend Development

1. **Image URLs**: All `image_url` fields contain full URLs. If no image is uploaded, the field will be `null` or an empty string.

2. **Price Format**: Prices are returned as decimal strings (e.g., "2999.00"). Convert to number for calculations, but display with proper currency formatting (Rs. 2,999.00).

3. **Pagination**: Use Laravel's standard pagination format. The `links` array can be used to build pagination UI.

4. **Search**: The search endpoint supports both text search and category filtering. Combine parameters as needed.

5. **Upcoming Products**: These have `is_upcoming: true` and may have `available_from` dates. Use this to show "Coming Soon" or "Available from" messaging.

6. **Sliders**: Ordered by `position` field, then by creation date. Use for homepage carousel display.

7. **Categories**: Use `slug` for URL-friendly routing, `name` for display.

8. **Stock**: Products with `stock: 0` should show "Out of Stock" messaging.

9. **Authentication**: All protected endpoints require `Authorization: Bearer {token}` header. Store tokens securely in localStorage/sessionStorage.

10. **Token Management**: After password change, user must login again as all existing tokens are revoked for security.

11. **Password Reset**: The reset URL format is: `http://localhost:3000/reset-password?token={token}&email={email}`

12. **Email Verification**: Currently not implemented but can be added later if needed.

## Authentication Flow Examples

### Complete Registration Flow

```javascript
// 1. Register user
const registerResponse = await fetch("/api/v1/auth/register", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
        name: "John Doe",
        email: "john@example.com",
        password: "password123",
        password_confirmation: "password123",
    }),
});

const { token } = await registerResponse.json();

// 2. Store token
localStorage.setItem("authToken", token);

// 3. Use token for authenticated requests
const profileResponse = await fetch("/api/v1/auth/profile", {
    headers: {
        Authorization: `Bearer ${token}`,
        "Content-Type": "application/json",
    },
});
```

### Complete Password Reset Flow

```javascript
// 1. Request password reset
await fetch("/api/v1/auth/forgot-password", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ email: "john@example.com" }),
});

// 2. User clicks link in email, frontend extracts token and email from URL
// URL: /reset-password?token=abc123&email=john@example.com

// 3. Reset password
await fetch("/api/v1/auth/reset-password", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
        email: "john@example.com",
        token: "abc123",
        password: "newpassword123",
        password_confirmation: "newpassword123",
    }),
});
```
