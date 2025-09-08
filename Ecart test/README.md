# FlipMart+ - E-commerce Platform

A modern e-commerce platform built with PHP, MySQL, and Three.js for stunning 3D visualizations.

## 🚀 Features

- **User Authentication System**
  - Secure login and registration
  - Session management
  - User profile management

- **Modern UI/UX**
  - Responsive design
  - Glassmorphism effects
  - Smooth animations
  - Dark/light theme toggle

- **Three.js Integration**
  - Interactive 3D backgrounds
  - Animated particles and geometric shapes
  - Responsive 3D canvas

- **Shopping Features**
  - Product browsing and search
  - Shopping cart functionality
  - Product comparison
  - Wishlist management

## 📁 File Structure

```
├── config.php          # Database configuration
├── database.sql        # Database schema
├── index.php          # Landing page (public) - Entry point
├── main.php           # Main shopping page (requires login)
├── login.php          # User login
├── register.php       # User registration
├── logout.php         # User logout
├── profile.php        # User profile page
├── order-confirmation.php # Order confirmation page
├── common.js          # Shared JavaScript functionality
├── script.js          # Main application logic
├── script2.js         # Additional functionality
├── style.css          # Main stylesheet
├── .htaccess          # Server configuration
└── README.md          # This file
```

## 🛠️ Setup Instructions

1. **Database Setup**
   - Create a MySQL database named `ecommerce_db`
   - Import the `database.sql` file
   - Update database credentials in `config.php`

2. **Server Requirements**
   - PHP 7.4 or higher
   - MySQL 5.7 or higher
   - Web server (Apache/Nginx)

3. **Installation**
   - Place all files in your web server directory
   - Ensure proper file permissions
   - Access via web browser

## 🎨 Design Features

### Three.js Animations
- **Login Page**: Floating particles with pastel colors
- **Register Page**: Rotating geometric wireframe shapes
- **Profile Page**: Floating orbs with smooth animations
- **Landing Page**: Combined stars and geometric shapes
- **Main Page**: Starfield background

### UI Components
- Glassmorphism cards with backdrop blur
- Gradient buttons with hover effects
- Responsive navigation
- Toast notifications
- Form validation with real-time feedback

## 🔧 Technical Details

### Frontend
- **HTML5** with semantic markup
- **CSS3** with modern features (Grid, Flexbox, Custom Properties)
- **JavaScript ES6+** with modules
- **Three.js** for 3D graphics

### Backend
- **PHP** for server-side logic
- **MySQL** for data storage
- **Prepared statements** for security
- **Session management** for user authentication

### Security Features
- Password hashing with `password_hash()`
- SQL injection prevention
- XSS protection
- CSRF protection (basic)

## 🎯 User Flow

1. **Landing Page (index.php)** → Introduction to FlipMart+
2. **Registration** → Create new account
3. **Login** → Access existing account
4. **Main Shopping (main.php)** → Browse products, add to cart
5. **Profile** → View account information
6. **Logout** → Secure session termination

## 🌟 Key Features

### Authentication System
- Secure user registration and login
- Session-based authentication
- Password hashing and verification
- User profile management

### Shopping Experience
- Product catalog with search and filtering
- Shopping cart with persistent storage
- Product comparison functionality
- Responsive product grid

### Visual Appeal
- Three.js powered 3D backgrounds
- Smooth animations and transitions
- Modern glassmorphism design
- Responsive layout for all devices

## 🔮 Future Enhancements

- Payment gateway integration
- Order management system
- Admin panel
- Product reviews and ratings
- Advanced search filters
- Email notifications
- Mobile app development

## 📱 Responsive Design

The platform is fully responsive and works on:
- Desktop computers
- Tablets
- Mobile phones
- All modern browsers

## 🎨 Theme System

- Light and dark theme support
- Theme persistence using localStorage
- Smooth theme transitions
- Consistent color scheme across all pages

## 🔒 Security Considerations

- All user inputs are sanitized
- SQL injection prevention
- XSS protection
- Secure session handling
- Password strength requirements

---

**Made with ❤️ for amazing shopping experiences**
