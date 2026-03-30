## Assumptions Made

This section documents the technical and business decisions made during development, along with their rationale.

### Architecture & Design Decisions

**1. MVC Architecture Pattern**
- Implemented Model-View-Controller separation with dedicated models, controllers, and API endpoints
- It provides clean separation of concerns, makes code maintainable, and follows industry best practices

**2. File-Based API Endpoints**
- Each API operation has its own PHP file (e.g., `get_products.php`, `create_sale.php`)
- This is a simple and easy to understand approach for this project scope

**3. RESTful API Without Versioning**
- Standard REST endpoints without API versioning (no `/v1/` prefix)

### Stock Management

**4. Automatic Stock Adjustment**
- Stock automatically decreases on sale creation, increases on sale deletion/update
- It will eliminate manual stock tracking and reduces human error

**5. Stock Validation**
- System prevents sales that exceed available stock
- Returns HTTP 400 with clear error message ("Insufficient stock. Available: X")

### Development Environment

**6. PHP Built-in Server for Development**
- Used `php -S localhost:8001` for development testing
- Its simple, requires no Apache/Nginx configuration, sufficient for development

---