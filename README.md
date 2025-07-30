<<<<<<< HEAD
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
=======
# 🛒 ShopBot AI - AI-Powered Shopping Assistant

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11.x-5a4fcf?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.2+-5a4fcf?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/OpenAI-GPT--4-5a4fcf?style=for-the-badge&logo=openai&logoColor=white" alt="OpenAI">
  <img src="https://img.shields.io/badge/AliExpress-API-f97316?style=for-the-badge&logo=aliexpress&logoColor=white" alt="AliExpress">
  <img src="https://img.shields.io/badge/Amazon-Database-f97316?style=for-the-badge&logo=amazon&logoColor=white" alt="Amazon">
  <img src="https://img.shields.io/badge/TailwindCSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind">
</p>

<p align="center" style="color: #1e293b;">
  <strong>Discover the perfect products with our AI-powered shopping assistant that integrates AliExpress and Amazon data for personalized recommendations.</strong>
>>>>>>> 80b369c691731733c4369a9a951585a8d4d63298
</p>

---

<<<<<<< HEAD
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
=======
## 🚀 Overview

ShopBot AI revolutionizes online shopping by combining the power of GPT-4 with AliExpress's real-time API and Amazon's extensive product database. Users can describe their needs in natural language, and the system provides tailored product recommendations through an intuitive chat interface.

![ShopBot AI Demo](path/to/demo-image.png)

### Why ShopBot AI?
- **Time-Saving**: Quickly find products that match your needs.
- **Personalized Experience**: AI tailors recommendations based on user preferences.
- **Dual Platform Integration**: Access a wide range of products from AliExpress and Amazon.

---

## ✨ Key Features

### 🧠 Intelligent Product Discovery

- **Natural Language Understanding**: Describe your needs in everyday language.
- **Dual Platform Integration**: Fetches products from AliExpress (via API) and Amazon (via database).
- **AI-Powered Recommendations**: Context-aware suggestions tailored to user preferences.

### 💬 Intuitive Chat Experience

- **Separate Chats**: Users can start new chats for different shopping inquiries, and each chat is saved separately for future reference.
- **Conversational Interface**: Human-like interactions for product discovery.
- **Platform-Specific Chatbots**: Separate chatbots for AliExpress and Amazon to tailor recommendations.

### 🔍 Smart Search & Filtering

- **Advanced Filtering**: Extracts price ranges, categories, and keywords.
- **Personalized Results**: Learns from user interactions to improve recommendations.

### 📂 User Dashboard Features

- **Saved Products**: Users can save products to their dashboard for easy access later.
- **Last Viewed Products**: A history of recently viewed products is maintained for both AliExpress and Amazon.
- **Organized Shopping**: Dashboard helps users manage their saved and viewed products efficiently.

### 👤 User-Centric Design

- **Responsive Interface**: Optimized for desktop and mobile.
- **Dark/Light Modes**: Comfortable browsing in any environment.
- **Session Management**: Organize shopping inquiries by topic.

---

## 🛠️ Technology Stack

- **Frontend**: TailwindCSS, Alpine.js
- **Backend**: Laravel 11, PHP 8.2+
- **AI**: OpenAI GPT-4
- **E-commerce**: AliExpress API, Amazon Database
- **Database**: MySQL/PostgreSQL
- **Caching**: Redis

---

## 🚀 Getting Started

### Prerequisites

- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL/PostgreSQL
- OpenAI API key
- AliExpress API credentials

### Quick Install

```bash
# Clone repository
git clone https://github.com/osamaalkhazali/ShopBotAi.git
cd shopbot-ai

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Configure database & API keys in .env file
# Add the following keys to your .env file:
# OPENAI_API_KEY=your_openai_api_key
# ALIEXPRESS_APP_KEY=your_aliexpress_app_key
# ALIEXPRESS_SECRET_KEY=your_aliexpress_secret_key

# Run migrations
php artisan migrate --seed

# Build assets
npm run build

# Start server
php artisan serve
```

Visit `http://localhost:8000` to start shopping with AI!

---

## 🧩 How It Works

1. **User submits a shopping query**: "I need waterproof hiking shoes for women under $100."

2. **AI processing**:
    - Extracts keywords: hiking shoes, waterproof, women's.
    - Identifies price constraint: <$100.
    - Maps to categories: Shoes > Outdoor > Hiking.

3. **Data Integration**:
    - **AliExpress**: Real-time API fetches matching products.
    - **Amazon**: Database query retrieves relevant items.

4. **Smart Ranking**: Results sorted by relevance, ratings, and price.

5. **Conversational Response**: AI presents findings with natural language explanations and product cards.

---

## 🌐 Integration Details

### AliExpress Integration

- **Source**: [AliExpress Affiliate Program](https://portals.aliexpress.com/affiportals/web/portals.htm)
- **How It Works**:
  - Real-time API fetches product data.
  - Affiliate links embedded in chat responses generate revenue.
- **Challenges**:
  - Rate limiting and data consistency.

### Amazon Integration

- **Source**: [Amazon Products Dataset (1.4M Products)](https://www.kaggle.com/datasets/asaniczka/amazon-products-dataset-2023-1-4m-products/data)
- **How It Works**:
  - Preloaded database enables fast queries.
  - AI extracts keywords and filters results.
- **Challenges**:
  - Data maintenance and scalability.

### Comparison of Difficulties

| Aspect                | AliExpress API                          | Amazon Database                          |
|-----------------------|------------------------------------------|------------------------------------------|
| **Data Source**       | Real-time API                           | Preloaded database                       |
| **Complexity**        | High (API rate limits, parameter mapping)| Moderate (database maintenance)          |
| **Performance**       | Dependent on API response times         | Faster due to local queries              |
| **Scalability**       | Limited by API rate limits              | Requires database optimization           |
| **Real-Time Updates** | Yes                                     | No                                       |

---

## 🛠️ Controller Details

### AliExpressController

- **Purpose**: Handles interactions with the AliExpress API.
- **Key Methods**:
  - `search(Request $request)`: Searches for products based on user-provided parameters.
  - `recommendWithAI(Request $request)`: Uses AI to generate product recommendations.
  - `getCategories()`: Fetches product categories from the AliExpress API.
- **Technical Notes**:
  - Embeds affiliate links in product recommendations to generate revenue.

### ProductController

- **Purpose**: Manages Amazon product data stored in the local database.
- **Key Methods**:
  - `search(Request $request)`: Searches the database for products matching user queries.
  - `recommendWithAI(Request $request)`: Uses AI to extract keywords and recommend products.
- **Technical Notes**:
  - Utilizes the [Amazon Products Dataset](https://www.kaggle.com/datasets/asaniczka/amazon-products-dataset-2023-1-4m-products/data) for product data.

---

## 📚 Use Cases

1. **Personal Shopping Assistant**:
   - Example: "Find me a durable backpack for under $50."
   - Result: AI fetches options from AliExpress and Amazon, sorted by durability and price.

2. **Gift Recommendations**:
   - Example: "Suggest a birthday gift for a 10-year-old boy."
   - Result: AI provides age-appropriate gift ideas with links to purchase.

3. **Niche Product Discovery**:
   - Example: "I need eco-friendly kitchen utensils."
   - Result: AI identifies and ranks products based on eco-friendliness.

4. **Dashboard Management**:
   - Example: "Save this product for later."
   - Result: Product is added to the user's dashboard for future reference.

5. **Last Viewed Products**:
   - Example: "What did I look at yesterday?"
   - Result: Displays a history of recently viewed products for both AliExpress and Amazon.

---

## 🤝 Contribution Guidelines

We welcome contributions from the community! To get started:

1. **Fork the Repository**: Create your own copy of the project.
2. **Create a Branch**: Use a descriptive name like `feature/add-chatbot`.
3. **Make Changes**: Implement your feature or fix.
4. **Test Your Changes**: Ensure everything works as expected.
5. **Submit a Pull Request**: Describe your changes and link to any relevant issues.

---

## 📜 License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

---

## 🛠️ Admin Features

The admin panel provides comprehensive tools for managing the platform:

#### Dashboard Management
- Access a centralized dashboard for monitoring system performance and user activity.
- View analytics, insights, and activity timelines for both AliExpress and Amazon platforms.

#### User Management
- View and manage user accounts, including roles and permissions.
- Create and edit user accounts with detailed information.

#### AliExpress Management
- Manage AliExpress products: create, edit, view, and delete products.
- Manage AliExpress categories: create, edit, view, and delete categories.
- View and manage chat sessions for AliExpress users.
- Test and configure the AliExpress API for seamless integration.
- View and restore trashed products and categories.

#### Amazon Management
- Manage Amazon products: create, edit, view, and delete products.
- Manage Amazon categories: create, edit, view, and delete categories.
- View and manage chat sessions for Amazon users.
- View and restore trashed products and categories.

#### Site Settings
- Configure site-wide settings, including branding, API keys, and other configurations.
- Control application name, logo, and theme colors to match your brand identity.

#### Super Admin Features
- Manage admin accounts with role-based access control.
- Access advanced tools for platform-wide management.
>>>>>>> 80b369c691731733c4369a9a951585a8d4d63298
