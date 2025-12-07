# ☕ CoffeeShopPG: The Ultimate Laravel Coffee Ordering System

[](https://laravel.com)
[](https://midtrans.com)
[](https://opensource.org/licenses/MIT)

## ✨ Introduction

**CoffeeShopPG** is a modern, full-stack web application built on the **Laravel** framework, designed to handle online ordering for a coffee shop. Its primary strength lies in its seamless integration of the **Midtrans Snap Payment Gateway**, offering a smooth and robust transaction experience for both registered users and quick-order guests.

## 🚀 Key Features

| Feature Category        | Description                                                                       | Implementation Details                                                                           |
| :---------------------- | :-------------------------------------------------------------------------------- | :----------------------------------------------------------------------------------------------- |
| **💳 Payment Gateway**  | **Instant and Secure Payments** via Midtrans Snap.                                | Supports all major payment channels (VA, e-Wallets, Credit Cards) via a single pop-up interface. |
| **👤 Guest Checkout**   | Customers can place and pay for orders **without needing to register or log in**. | `username` input in the cart handles guest naming, simplifying the checkout flow.                |
| **🛒 E-commerce Core**  | Full Shopping Cart functionality with item management.                            | Uses session-based cart logic (`CartController`).                                                |
| **📦 Order Management** | Comprehensive tracking of order status from initial placement to completion.      | Detailed status logging (`OrderLog`) and dedicated order tracking view.                          |
| **🛡️ Access Control**   | Role-Based Access Control (RBAC) for Admin and regular users.                     | Implemented using Laravel's native authentication and the Spatie/Permission package.             |
| **⭐️ Review System**   | Allows customers to submit product reviews upon order completion.                 | Dedicated models and controllers (`ReviewController`).                                           |

## ⚙️ Installation & Setup

Follow these steps to get your local development environment running.

### Prerequisites

-   PHP \>= 8.1
-   Composer
-   Node.js & NPM
-   MySQL or other database supported by Laravel

### Steps

1.  **Clone the Repository:**

    ```bash
    git clone https://github.com/risang443/CoffeeShopPG.git
    cd CoffeeShopPG
    ```

2.  **Install Dependencies:**

    ```bash
    composer install
    npm install
    npm run dev
    ```

3.  **Configure Environment:**
    Copy the example environment file and generate the application key.

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4.  **Database & Migration:**
    Set up your database credentials in the `.env` file, then run migrations and seed the database.

    ```bash
    # Run Migrations (Includes tables for Orders, Products, and Midtrans fields)
    php artisan migrate

    # Seed the database (Optional: Adds initial products and Admin user)
    php artisan db:seed
    ```

## 💳 Midtrans Payment Configuration

This step is crucial for enabling the payment functionality.

1.  **Obtain Keys:** Get your **Server Key** and **Client Key** from your [Midtrans Dashboard](https://www.google.com/search?q=https://dashboard.midtrans.com/).

2.  **Update `.env`:** Add the following variables to your `.env` file, replacing the placeholders:

    ```dotenv
    # MIDTRANS CONFIGURATION
    MIDTRANS_SERVER_KEY="<YOUR_MIDTRANS_SERVER_KEY>"
    MIDTRANS_CLIENT_KEY="<YOUR_MIDTRANS_CLIENT_KEY>"

    # Set to true for Production / false for Sandbox
    MIDTRANS_IS_PRODUCTION=false
    ```

3.  **Webhook Setup (Local Testing):**
    If developing locally, you must use a tunneling tool (like **Ngrok**) to receive payment status updates.

    -   Run Laravel: `php artisan serve` (Default port 8000)
    -   Run Ngrok: `ngrok http 8000`
    -   **Set Notification URL in Midtrans Dashboard (Sandbox):** Copy the Ngrok HTTPS URL and append the notification path: `https://[ngrok-url-anda]/payment/notification`

## 👤 Default Credentials (After `db:seed`)

| Role      | Username     | Email             | Password |
| :-------- | :----------- | :---------------- | :------- |
| **Admin** | admin_user   | admin@example.com | password |
| **User**  | regular_user | user@example.com  | password |

## 💖 Contributing

We welcome contributions\! If you have suggestions for new coffee products or improved payment flow, please open an issue or submit a pull request.

## 📄 License

This project is open-source and licensed under the [MIT License](LICENSE.md).

---

_Built with ❤️ and Laravel._
