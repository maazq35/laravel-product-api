Laravel 8 Product & Cart API
📌 Overview
This is a Laravel 8 REST API project that provides:

Product Management (with multiple images)

Shopping Cart functionality

Authentication (Register, Login) via Laravel Sanctum

🚀 Installation & Setup
1️⃣ Clone the Repository

git clone https://github.com/your-username/product-cart-api.git
cd product-cart-api
2️⃣ Install Dependencies

composer install
3️⃣ Environment Setup
Copy the .env.example file to .env:


cp .env.example .env
Edit .env and set your database credentials:

env

Edit
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=product_cart
DB_USERNAME=root
DB_PASSWORD=your_password
4️⃣ Generate App Key

php artisan key:generate
5️⃣ Run Migrations
php artisan migrate

6️⃣ Create Storage Symlink (For Product Images)

php artisan storage:link
7️⃣ Run the Development Server

php artisan serve
Server will run at:
📍 http://127.0.0.1:8000

📂 Folder Structure

app/Http/Controllers/      # Controllers for Products, Cart, Auth
app/Models/                # Product, Image, Cart models
routes/api.php              # API routes
storage/app/public/         # Uploaded images
🔑 Authentication
Laravel Sanctum is used for token-based authentication.

Public APIs (like product listing) do not require authentication.

Private APIs require a Bearer token in the Authorization header.

📜 API Endpoints
Authentication
Method	Endpoint	Description
POST	/api/register	Register JSON raw data {"name":,"email":,"password":,}
POST	/api/login	Login & get token  JSON raw data { "email":, "password": }

Products
Method	Endpoint	Description	Auth Required
GET	/api/products	List all products	❌ No
GET	/api/products/{id}	Get single product	❌ No
POST	/api/products	Create product (with images)	✅ Yes
PUT	/api/products/{id}	Update product	✅ Yes
DELETE	/api/products/{id}	Delete product	✅ Yes

Cart
Method	Endpoint	Description	Auth Required
GET	/api/cart	View cart items	✅ Yes
POST	/api/cart/{id}	Add product to cart	✅ Yes
DELETE	/api/cart/{id}	Remove item from cart	✅ Yes

📷 Image Upload
Multiple product images are stored in storage/app/public/products/

Images are accessible via /storage/products/...

⚡ Example API Usage
Create Product (POST /api/products)

json
{
  "name": "Test Product",
  "price": 199.99,
  "images[]": [file1.jpg, file2.jpg]
}
Add to Cart (POST /api/cart/1)



{
  "quantity": 2
}


