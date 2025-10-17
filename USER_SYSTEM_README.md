# User System Documentation

This Laravel application now supports a two-type user system with **Admin** and **Merchant** roles.

## Features Implemented

### 1. Database Structure
- Added `user_type` field to users table with enum values: `admin`, `merchant`
- Default user type is `merchant`

### 2. User Model Enhancements
- Added role constants: `User::TYPE_ADMIN` and `User::TYPE_MERCHANT`
- Added helper methods:
  - `isAdmin()` - Check if user is admin
  - `isMerchant()` - Check if user is merchant
  - `getUserTypes()` - Get all available user types

### 3. Middleware Protection
- Created `CheckUserRole` middleware for role-based access control
- Registered as `role` middleware alias
- Usage: `Route::middleware(['auth', 'role:admin'])`

### 4. Authentication System
- Custom login form with demo credentials
- Role-based redirects after login:
  - Admin users → `/admin/dashboard`
  - Merchant users → `/merchant/dashboard`
- Logout functionality

### 5. Sample Routes
- **Admin Routes** (admin only):
  - `/admin/dashboard` - Admin dashboard
  - `/admin/users` - User management page
  
- **Merchant Routes** (merchant only):
  - `/merchant/dashboard` - Merchant dashboard
  - `/merchant/products` - Product management
  
- **Common Routes** (authenticated users):
  - `/profile` - User profile page

### 6. Demo Users
Created sample users for testing:

**Admin User:**
- Email: `admin@example.com`
- Password: `password`
- Type: `admin`

**Merchant User:**
- Email: `merchant@example.com`
- Password: `password`
- Type: `merchant`

## Usage Examples

### Protecting Routes
```php
// Admin only routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return 'Admin Dashboard';
    });
});

// Merchant only routes
Route::middleware(['auth', 'role:merchant'])->group(function () {
    Route::get('/merchant/dashboard', function () {
        return 'Merchant Dashboard';
    });
});
```

### Checking User Roles in Controllers
```php
public function index()
{
    $user = auth()->user();
    
    if ($user->isAdmin()) {
        // Admin logic
    } elseif ($user->isMerchant()) {
        // Merchant logic
    }
}
```

### Creating Users Programmatically
```php
// Create admin user
User::create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'password' => Hash::make('password'),
    'user_type' => User::TYPE_ADMIN,
]);

// Create merchant user
User::create([
    'name' => 'Merchant User',
    'email' => 'merchant@example.com',
    'password' => Hash::make('password'),
    'user_type' => User::TYPE_MERCHANT,
]);
```

## Testing the System

1. Visit the homepage: `http://your-app-url/`
2. Click "Log in" to access the login form
3. Use the demo credentials to test different user types
4. Try accessing protected routes to see role-based access control in action

## Files Modified/Created

- `database/migrations/2025_10_16_060811_add_user_type_to_users_table.php`
- `app/Models/User.php`
- `app/Http/Middleware/CheckUserRole.php`
- `bootstrap/app.php`
- `database/seeders/UserSeeder.php`
- `database/seeders/DatabaseSeeder.php`
- `app/Http/Controllers/AuthController.php`
- `routes/web.php`
- `resources/views/auth/login.blade.php`
- `resources/views/admin/users.blade.php`
- `resources/views/welcome.blade.php`

The system is now ready for use with proper role-based access control!
