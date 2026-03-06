# 💰 Kharcha — Personal Expense Manager

A full-featured personal finance management application built with **Laravel 10** and **Blade + Bootstrap-free custom CSS**.

---

## ✨ Features

- **Dashboard** — Monthly income, expense, balance & savings rate with 6-month chart
- **Transactions** — Add, edit, delete with full CRUD. Filter by type, category, date range, search
- **Reports & Downloads** — Filter reports, download as **CSV**, **JSON**, or **HTML (printable PDF)**
- **Savings Goals** — Track financial goals with progress bars and deadline tracking
- **Authentication** — Full login/register with Laravel session auth
- **Multi-user** — Each user sees only their own data (policy-protected)

---

## 🚀 Installation

### Requirements
- PHP >= 8.1
- Composer
- MySQL (or PostgreSQL / SQLite)
- Node.js & npm (optional, for asset compilation)

### Steps

```bash
# 1. Clone / extract the project
cd kharcha

# 2. Install PHP dependencies
composer install

# 3. Copy environment file
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Configure database in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kharcha
DB_USERNAME=root
DB_PASSWORD=your_password

# 6. Run migrations
php artisan migrate

# 7. (Optional) Seed with demo data
php artisan db:seed

# 8. Start development server
php artisan serve
```

Then visit: **http://localhost:8000**

Demo credentials (after seeding):
- Email: `demo@kharcha.com`
- Password: `password`

---

## 📁 Project Structure

```
kharcha/
├── app/
│   ├── Http/Controllers/
│   │   ├── AuthController.php         # Login, Register, Logout
│   │   ├── DashboardController.php    # Dashboard with stats & charts
│   │   ├── TransactionController.php  # Full CRUD + filters
│   │   ├── ReportController.php       # Reports + CSV/JSON/PDF download
│   │   └── GoalController.php         # Savings goals CRUD
│   ├── Models/
│   │   ├── User.php
│   │   ├── Transaction.php            # Scopes: income, expense, forMonth, forUser
│   │   └── Goal.php                   # Computed: progress_percentage, remaining
│   └── Policies/
│       ├── TransactionPolicy.php
│       └── GoalPolicy.php
├── database/
│   ├── migrations/
│   │   ├── create_users_table.php
│   │   ├── create_transactions_table.php
│   │   └── create_goals_table.php
│   └── seeders/
│       └── DatabaseSeeder.php         # Demo user + 3 months sample data
├── resources/views/
│   ├── layouts/app.blade.php          # Main layout with sidebar
│   ├── auth/
│   │   ├── login.blade.php
│   │   └── register.blade.php
│   ├── dashboard/index.blade.php
│   ├── transactions/
│   │   ├── index.blade.php            # List + filters + table
│   │   ├── create.blade.php
│   │   └── edit.blade.php
│   ├── reports/
│   │   ├── index.blade.php            # Reports with charts + download buttons
│   │   └── pdf.blade.php              # Printable HTML report
│   └── goals/
│       ├── index.blade.php
│       ├── create.blade.php
│       └── edit.blade.php
├── public/
│   ├── css/app.css                    # Full custom CSS (dark theme)
│   └── js/app.js
└── routes/web.php
```

---

## 📊 Transaction Categories

Food & Dining, Transport, Shopping, Entertainment, Healthcare, Bills & Utilities, Education, Salary, Freelance, Investment, Rent, Travel, Other

---

## 📥 Download Formats

| Format | Endpoint |
|--------|----------|
| CSV    | `GET /reports/download/csv` |
| JSON   | `GET /reports/download/json` |
| HTML/Print | `GET /reports/download/pdf` |

All download endpoints support the same filters as the reports page.

---

## 🛠️ Tech Stack

- **Backend**: Laravel 10, PHP 8.1+
- **Frontend**: Blade templates, custom CSS (dark theme), Chart.js
- **Auth**: Laravel session-based authentication
- **Database**: MySQL (configurable)
- **Charts**: Chart.js (CDN)

---

## 🔒 Security

- CSRF protection on all forms
- Policy-based authorization (users can only access their own data)
- Hashed passwords via Laravel's `bcrypt`
- SQL injection protection via Eloquent ORM
