# 🛒 ShopBot AI - Intelligent Shopping Assistant

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/OpenAI-GPT--4-412991?style=for-the-badge&logo=openai&logoColor=white" alt="OpenAI">
  <img src="https://img.shields.io/badge/AliExpress-API-FF6A00?style=for-the-badge&logo=aliexpress&logoColor=white" alt="AliExpress">
  <img src="https://img.shields.io/badge/TailwindCSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind">
</p>

<p align="center">
  <strong>An AI-powered shopping assistant that helps users find products on AliExpress using natural language queries and intelligent recommendations.</strong>
</p>

---

## 🌟 Features

### 🤖 AI-Powered Shopping Assistant
- **Natural Language Processing**: Users can describe what they want in everyday language
- **GPT-4 Integration**: Advanced AI understanding for complex shopping queries
- **Smart Product Recommendations**: AI analyzes user intent and suggests relevant products
- **Multi-language Support**: Detects user language and processes queries accordingly

### 🛍️ AliExpress Integration
- **Real-time Product Search**: Direct integration with AliExpress API
- **Product Categories**: Intelligent category mapping and filtering
- **Price Filtering**: AI extracts budget constraints from user queries
- **Product Details**: Rich product information with images, prices, and ratings

### 💬 Interactive Chat Interface
- **Conversational UI**: Intuitive chat-based shopping experience
- **Chat History**: Save and manage multiple shopping conversations
- **Session Management**: Persistent chat sessions with user authentication
- **Product Saving**: Save interesting products for later review

### 👤 User Management
- **User Authentication**: Secure login and registration system
- **Profile Management**: User preferences and shopping history
- **Product Tracking**: View history and saved products dashboard
- **Admin Panel**: Comprehensive admin interface for user and product management

### 🎨 Modern UI/UX
- **Responsive Design**: Works seamlessly on desktop and mobile
- **Dark/Light Mode**: User preference-based theming
- **Real-time Updates**: Dynamic product loading and chat interactions
- **Intuitive Navigation**: Clean, modern interface built with Tailwind CSS

## 🚀 Quick Start

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js and npm
- MySQL/SQLite database
- OpenAI API key
- AliExpress API credentials

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-username/shopbot-ai.git
   cd shopbot-ai
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure your .env file**
   ```env
   # Database Configuration
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=shopbot_ai
   DB_USERNAME=your_username
   DB_PASSWORD=your_password

   # OpenAI Configuration
   OPENAI_API_KEY=your_openai_api_key_here

   # AliExpress API Configuration (Add these to your .env)
   ALIEXPRESS_APP_KEY=your_aliexpress_app_key
   ALIEXPRESS_APP_SECRET=your_aliexpress_app_secret
   ```

6. **Run database migrations**
   ```bash
   php artisan migrate --seed
   ```

7. **Build frontend assets**
   ```bash
   npm run build
   # Or for development
   npm run dev
   ```

8. **Start the development server**
   ```bash
   php artisan serve
   ```

Visit `http://localhost:8000` to access the application.

## 🏗️ Architecture Overview

### Backend (Laravel 11)
- **Controllers**: Handle HTTP requests and API interactions
  - `AliExpressController`: Manages AliExpress API integration
  - `AliExpressChatController`: Handles chat session management
  - `UserProfileController`: User data and preferences
  - Admin controllers for backend management

- **Models**: Data layer and business logic
  - `AliExpressChatSession`: Chat conversation management
  - `AliExpressChatMessage`: Individual chat messages
  - `AliExpressProduct`: Product data caching
  - User authentication and profile models

- **Services**: External API integrations
  - OpenAI GPT-4 for natural language processing
  - AliExpress API for product data
  - Custom helper functions for data processing

### Frontend
- **Tailwind CSS**: Modern, responsive styling
- **Alpine.js**: Lightweight JavaScript framework
- **Custom JavaScript**: Chat interface and product interactions
- **Blade Templates**: Server-side rendering with Laravel

### Database Schema
```sql
-- Core chat functionality
aliexpress_chat_sessions (id, user_id, name, status, tags, timestamps)
aliexpress_chat_messages (id, session_id, sender, content, order, timestamps)

-- Product management
aliexpress_products (id, title, price, image_url, product_url, category_id, etc.)
aliexpress_saved_products (user_id, product_id, timestamps)
aliexpress_viewed_products (user_id, product_id, timestamps)

-- User management
users (id, name, email, password, timestamps)
admins (id, name, email, role, timestamps)
```

## 💡 How It Works

### AI-Powered Product Search
1. **User Input**: User describes what they're looking for in natural language
2. **AI Processing**: GPT-4 analyzes the query and extracts:
   - Product keywords
   - Price constraints
   - Category preferences
   - User intent
3. **API Integration**: Processed parameters sent to AliExpress API
4. **Smart Filtering**: Results filtered and ranked by relevance
5. **Response Generation**: AI creates friendly, conversational responses

### Example Interactions
```
User: "I need a gift for my chef friend under $50"
AI Analysis:
- Keywords: kitchen, chef, tools, cooking, gift
- Price: max $50
- Categories: Kitchen & Dining, Tools
- Intent: Gift recommendation

User: "Looking for wireless earbuds with noise cancellation"
AI Analysis:
- Keywords: wireless, earbuds, bluetooth, noise, cancellation
- Categories: Electronics, Audio
- Intent: Audio equipment purchase
```

## 📱 Key Components

### Chat Interface (`/aliexpress-chatbot`)
- Real-time conversational interface
- Product recommendations with images and details
- Chat history and session management
- Product saving and sharing capabilities

### User Dashboard (`/account/history`)
- View saved products
- Browse search history
- Manage user preferences
- Track product views and interactions

### Admin Panel (`/admin`)
- User management and analytics
- Product catalog management
- Chat session monitoring
- System configuration and API testing

## 🔧 Configuration

### OpenAI Integration
The application uses GPT-4 for natural language processing. Configure in `config/services.php`:
```php
'openai' => [
    'key' => env('OPENAI_API_KEY'),
],
```

### AliExpress API Setup
AliExpress integration is handled through the custom SDK in `/AliExpressSDK/`. Configure credentials in your environment file and update the helper functions in `app/Helpers/helpers.php`.

### Customization
- **AI Prompts**: Modify system prompts in `AliExpressController@recommendWithAI`
- **Product Categories**: Update category mappings in the database
- **UI Themes**: Customize Tailwind configuration in `tailwind.config.js`
- **Chat Behavior**: Modify chat logic in `public/js/aliexpress-chatbot.js`

## 🛡️ Security Features

- **CSRF Protection**: All forms protected against cross-site request forgery
- **Authentication**: Secure user login with Laravel Breeze
- **Input Validation**: Comprehensive request validation
- **API Rate Limiting**: Prevents abuse of external APIs
- **XSS Protection**: Safe rendering of user content
- **SQL Injection Prevention**: Eloquent ORM parameterized queries

## 🧪 Testing

Run the test suite:
```bash
# Run all tests
php artisan test

# Run specific test suites
./vendor/bin/pest tests/Feature
./vendor/bin/pest tests/Unit

# Generate coverage report
php artisan test --coverage
```

## 📈 Performance Optimization

- **Database Indexing**: Optimized queries for chat and product data
- **Caching**: Redis/database caching for frequently accessed data
- **Asset Optimization**: Vite for efficient frontend bundling
- **API Throttling**: Rate limiting for external API calls
- **Lazy Loading**: Efficient product image loading

## 🔍 API Endpoints

### Public API Routes
```
GET  /api/aliexpress/search          - Search products
POST /api/aliexpress/recommend       - AI recommendations
GET  /api/aliexpress/categories      - Product categories
```

### Authenticated Routes
```
POST /api/aliexpress/chat/sessions   - Create chat session
GET  /api/aliexpress/chat/sessions   - Get user sessions
POST /api/aliexpress/chat/messages   - Save chat message
POST /api/products/view/{id}         - Track product view
```

## 🤝 Contributing

We welcome contributions! Please see our [Contributing Guidelines](CONTRIBUTING.md) for details.

### Development Workflow
1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Add tests for new functionality
5. Commit your changes (`git commit -m 'Add amazing feature'`)
6. Push to the branch (`git push origin feature/amazing-feature`)
7. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- **Laravel Framework**: Powerful PHP framework for rapid development
- **OpenAI**: Advanced AI capabilities for natural language processing
- **AliExpress**: Product data and e-commerce integration
- **Tailwind CSS**: Modern utility-first CSS framework
- **Community**: Thanks to all contributors and users

## 📞 Support

- **Documentation**: Check the `/docs` folder for detailed documentation
- **Issues**: Report bugs and feature requests on GitHub Issues
- **Discussions**: Join our community discussions for questions and ideas

---

<p align="center">
  Made with ❤️ by the ShopBot AI Team
</p>
