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
</p>

---

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
