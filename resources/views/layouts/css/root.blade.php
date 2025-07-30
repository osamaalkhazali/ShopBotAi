/* Site Settings CSS - Global Variables */
:root {
    /* Main Brand Colors */
    --primary: #5a4fcf; /* Main primary color */
    --primary-dark: #4a3fb8; /* Darker shade for hover states */
    --primary-light: #7b71e3; /* Lighter for accents */
    --primary-lighter: #a59bff;
    --primary-lightest: #e5e4ff;

    /* RGB Values for CSS rgba() usage */
    --primary-rgb: 90, 79, 207;
    --primary-dark-rgb: 74, 63, 184;
    --primary-light-rgb: 123, 113, 227;
    --secondary-rgb: 30, 41, 59;
    --secondary-light-rgb: 30, 41, 59;
    --secondary-dark-rgb: 15, 23, 42;
    --bg-dark-rgb: 17, 24, 39;
    --bg-darker-rgb: 11, 15, 25;
    --indigo-500-rgb: 99, 102, 241;

    /* Secondary Colors */
    --secondary: #1e293b; /* Deep blue-gray */
    --secondary-dark: #0f172a;
    --secondary-light: #334155;
    --secondary-lighter: #475569;

    /* Accent Colors */
    --accent: #f97316; /* Orange accent */
    --accent-dark: #ea580c;
    --accent-light: #fb923c;

    /* Background Colors */
    --bg-dark: #111827; /* Main dark background */
    --bg-darker: #0b0f19; /* Even darker background */
    --bg-medium: #1f2937; /* Medium dark background for cards */
    --bg-light: #374151; /* Lighter backgrounds for inputs */
    --bg-lighter: #4b5563; /* Even lighter backgrounds */
    --bg-gradient-from: #111827; /* Gradient start for backgrounds */
    --bg-gradient-to: #1f2937; /* Gradient end for backgrounds */

    /* Success, Warning, Error Colors */
    --success: #10b981;
    --success-light: #34d399;
    --warning: #f59e0b;
    --warning-light: #fbbf24;
    --error: #ef4444;
    --error-light: #f87171;

    /* Text Colors */
    --text-light: #f6f8fd;
    --text-dark: #111827;
    --text-muted: #6b7280;

    /* Grayscale */
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-800: #1f2937;
    --gray-900: #111827;

    /* Navbar Specific Colors */
    --navbar-bg: var(--bg-dark);
    --navbar-text: var(--text-light);
    --navbar-link: var(--gray-300);
    --navbar-link-hover: white;
    --navbar-link-active: var(--primary);
    --navbar-dropdown-bg: var(--bg-light);
    --navbar-dropdown-text: var(--gray-800);
    --navbar-dropdown-hover: var(--gray-100);
    --navbar-dropdown-button: var(--gray-300);
    --navbar-dropdown-button-hover: white;
    --navbar-border: var(--bg-lighter);
    --navbar-hamburger: var(--gray-400);
    --navbar-hamburger-hover: white;

    /* Auth Pages Colors */
    --auth-bg: var(--bg-dark);
    --auth-card-bg: var(--bg-medium);
    --auth-text: var(--text-light);
    --auth-text-muted: var(--gray-400);
    --auth-link: var(--primary);
    --auth-link-hover: var(--primary-light);
    --auth-input-bg: var(--bg-light);
    --auth-input-border: var(--bg-lighter);
    --auth-input-focus-border: var(--primary);
    --auth-label: var(--gray-300);
    --auth-btn-primary-bg: var(--primary);
    --auth-btn-primary-hover: var(--primary-dark);
    --auth-btn-primary-text: white;
    --auth-logo-color: var(--primary);
    --auth-error-text: var(--error);
    --auth-success-text: var(--success);

    /* RGB Values for transparency effects */
    --bg-medium-rgb: 45, 55, 72;
    --bg-light-rgb: 55, 65, 81;
    --primary-rgb: 108, 99, 255;
    --error-rgb: 239, 68, 68;
    --success-rgb: 16, 185, 129;

    /* App Layout Colors */
    --app-bg: var(--bg-dark);
    --app-text: var(--text-light);
    --app-header-bg: var(--bg-medium);
    --app-header-text: var(--text-light);
    --app-card-bg: var(--bg-medium);
    --app-card-border: var(--bg-lighter);
    --app-card-hover-border: var(--primary);
    --app-section-title: var(--text-light);
    --app-section-title-icon: var(--primary-light);
    --app-muted-text: var(--gray-400);
    --app-link: var(--primary);
    --app-link-hover: var(--primary-light);
    --app-input-bg: var(--bg-light);
    --app-input-border: var(--bg-lighter);
    --app-input-text: var(--text-light);
    --app-btn-primary-bg: var(--primary);
    --app-btn-primary-hover: var(--primary-dark);
    --app-btn-primary-text: white;
    --app-btn-secondary-bg: var(--bg-light);
    --app-btn-secondary-hover: var(--bg-lighter);
    --app-btn-secondary-text: var(--text-light);
    --app-btn-danger-bg: var(--error);
    --app-btn-danger-hover: var(--error-light);
    --app-btn-danger-text: white;

    /* Welcome Page Specific Colors */
    --welcome-header-bg: var(--bg-dark);
    --welcome-body-bg-from: var(--bg-dark);
    --welcome-body-bg-to: var(--bg-medium);
    --welcome-body-bg-dark-from: var(--bg-darker);
    --welcome-body-bg-dark-to: var(--bg-dark);
    --welcome-text: rgba(255, 255, 255, 0.7);
    --welcome-text-dark: rgba(255, 255, 255, 0.7);
    --welcome-nav-text: var(--gray-300);
    --welcome-nav-text-hover: var(--primary);
    --welcome-nav-text-dark: var(--gray-300);
    --welcome-nav-text-dark-hover: white;
    --welcome-button-primary-bg: var(--primary);
    --welcome-button-primary-hover: var(--primary-dark);
    --welcome-button-secondary-bg: var(--bg-medium);
    --welcome-button-secondary-text: var(--gray-300);
    --welcome-button-secondary-border: var(--bg-lighter);
    --welcome-button-secondary-border-hover: var(--primary);
    --welcome-features-bg: var(--bg-medium);
    --welcome-features-bg-dark: var(--bg-dark);
    --welcome-card-bg: var(--bg-medium);
    --welcome-card-bg-dark: var(--bg-dark);
    --welcome-card-shadow: var(--shadow-color);
    --welcome-card-text: var(--gray-300);
    --welcome-card-text-dark: var(--gray-300);
    --welcome-card-description: var(--gray-400);
    --welcome-card-description-dark: var(--gray-300);
    --welcome-demo-bg: rgba(79, 70, 229, 0.3);
    --welcome-demo-bg-dark: rgba(79, 70, 229, 0.3);
    --welcome-demo-text: var(--gray-300);
    --welcome-demo-text-dark: var(--gray-300);
    --welcome-example-query-bg: rgba(79, 70, 229, 0.2);
    --welcome-example-query-bg-dark: rgba(79, 70, 229, 0.2);
    --welcome-example-query-text: var(--indigo-300);
    --welcome-example-query-text-dark: var(--indigo-300);
    --welcome-cta-bg: var(--primary);
    --welcome-cta-text: white;
    --welcome-cta-button-bg: var(--bg-medium);
    --welcome-cta-button-text: var(--primary);
    --welcome-cta-button-hover: var(--bg-light);
    --welcome-footer-bg: var(--bg-dark);
    --welcome-footer-bg-dark: var(--bg-darker);
    --welcome-footer-text: var(--gray-400);
    --welcome-footer-text-dark: var(--gray-400);
    --welcome-footer-link-hover: var(--primary);
    --welcome-footer-link-hover-dark: var(--indigo-400);
    --welcome-chatbot-header-bg: var(--primary);
    --welcome-chatbot-message-bg: rgba(79, 70, 229, 0.3);
    --welcome-chatbot-message-bg-dark: rgba(79, 70, 229, 0.3);
    --welcome-product-title: var(--gray-300);
    --welcome-product-title-dark: var(--gray-300);
    --welcome-price-color: var(--indigo-400);
    --welcome-price-color-dark: var(--indigo-400);

    /* Additional Colors */
    --indigo-50: #eef2ff;
    --indigo-100: #e0e7ff;
    --indigo-300: #a5b4fc;
    --indigo-400: #818cf8;
    --indigo-500: #6366f1;
    --indigo-600: #4f46e5;
    --indigo-700: #4338ca;
    --indigo-800: #3730a3;
    --indigo-900: #312e81;

    /* Green/Emerald Colors */
    --green-500: #10b981;
    --green-600: #059669;
    --green-700: #047857;

    /* Amber Colors */
    --amber-400: #fbbf24;
    --amber-500: #f59e0b;

    /* Layout */
    --sidebar-width: 280px;

    /* Stars Rating Color */
    --stars-color: #fbbf24;

    /* Shadow Colors */
    --shadow-color: rgba(0, 0, 0, 0.25);
    --shadow-color-darker: rgba(0, 0, 0, 0.5);
    --primary-shadow: rgba(108, 99, 255, 0.2);

    /* Transparent Colors for Overlays and Backgrounds */
    --text-light-transparent: rgba(255, 255, 255, 0.7);
    --text-muted-transparent: rgba(255, 255, 255, 0.5);
    --bg-light-transparent: rgba(255, 255, 255, 0.08);
    --bg-lighter-transparent: rgba(255, 255, 255, 0.15);
    --bg-lighter-hover-transparent: rgba(255, 255, 255, 0.25);
    --bg-transparent: rgba(0, 0, 0, 0);
    --bg-dark-transparent: rgba(0, 0, 0, 0.15);
    --bg-darker-transparent: rgba(0, 0, 0, 0.3);
    --bg-border-transparent: rgba(255, 255, 255, 0.1);
    --bg-gradient-overlay-from: rgba(0, 0, 0, 0.2);
    --bg-gradient-overlay-to: rgba(0, 0, 0, 0.3);
}
