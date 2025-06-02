# ABC Supermarket E-commerce Website

A simple PHP-based e-commerce website for ABC Supermarket, featuring user authentication, product browsing, shopping cart functionality, and a checkout process.

## Features

- User registration and authentication
- Product browsing by categories (Grocery, Fashion, Electronics)
- Shopping cart functionality
- Checkout process with order summary
- Responsive design with modern UI
- Search functionality
- Free shipping on orders over AED 50

## Prerequisites

- XAMPP (or similar local server stack with PHP and MySQL)
- Web browser
- Git (optional)

## Installation

1. Clone or download this repository to your XAMPP's `htdocs` directory:
   ```bash
   cd /path/to/xampp/htdocs
   git clone https://github.com/yourusername/abc-supermarket.git
   ```
   Or simply extract the downloaded ZIP file to `htdocs/abc-supermarket`

2. Start XAMPP and ensure Apache and MySQL services are running

3. Create the database:
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create a new database named `abc_supermarket`
   - Import the `database.sql` file from the project root directory

4. Configure the database connection:
   - Open `config/database.php`
   - Update the database credentials if needed (default uses root with no password)

5. Access the website:
   Open your web browser and navigate to:
   ```
   http://localhost/abc-supermarket
   ```

## Project Structure

```
abc-supermarket/
├── assets/
│   ├── css/
│   │   └── style.css
│   └── images/
├── config/
│   └── database.php
├── includes/
│   ├── header.php
│   └── footer.php
├── add_to_cart.php
├── cart.php
├── checkout.php
├── database.sql
├── index.php
├── login.php
├── logout.php
├── order_success.php
├── products.php
├── register.php
├── update_cart.php
└── README.md
```

## Usage

1. Register a new account or login with existing credentials
2. Browse products by category
3. Add items to cart
4. Update cart quantities or remove items
5. Proceed to checkout
6. Fill in shipping and payment information
7. Place order and receive confirmation

## Security Features

- Password hashing
- Prepared SQL statements to prevent SQL injection
- Input validation and sanitization
- Session-based authentication
- CSRF protection
- XSS prevention through output escaping

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support, email support@abcsupermarket.com or create an issue in the repository. 