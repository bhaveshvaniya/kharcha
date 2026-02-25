# 📈 StockVault – Laravel 12 Stock Management System

A full-featured stock portfolio and market management system built with **Laravel 12**, Bootstrap 5, and Chart.js.

---

## 🚀 Features

### 📊 Dashboard
- **Portfolio Summary** – Total invested, current value, P&L, and number of stocks
- **Top Gainers & Losers** – Real-time ranking based on daily price change
- **Holdings Overview** – Quick view of all portfolio positions
- **Recent Transactions** – Latest buy/sell activity

### 📈 Stock Management
- **Add / Edit / Delete Stocks** – Full CRUD with symbol, company name, sector, exchange
- **Daily Price Tracking** – Record and update OHLCV (Open, High, Low, Close, Volume) for each stock
- **Price History Chart** – Interactive 30-day High/Low/Close line chart via Chart.js
- **Daily High & Low** – Track intraday high/low, shown inline on stock listing
- **Change % Calculation** – Automatic change amount and percentage vs. previous close
- **Search** – Client-side search on stock listing

### 💼 Portfolio Management
- **Buy Stocks** – Record purchases with quantity, price, brokerage, date
- **Sell Stocks** – Sell with automatic quantity validation
- **Average Cost Calculation** – Automatically updates average buy price on multiple purchases
- **P&L Tracking** – Real-time profit/loss calculation per holding and total portfolio
- **Transaction History** – Paginated full history with buy/sell badges, net amounts

---

## 🗄️ Database Schema

```
stocks
  id, symbol, company_name, sector, exchange,
  current_price, previous_close, is_active, timestamps

stock_prices
  id, stock_id, price_date, open_price, high_price,
  low_price, close_price, volume, change_amount, change_percent, timestamps
  UNIQUE(stock_id, price_date)

portfolio_holdings
  id, stock_id, quantity, average_buy_price,
  total_invested, first_purchase_date, notes, timestamps

transactions
  id, stock_id, type (buy/sell), quantity, price_per_share,
  total_amount, brokerage, transaction_date, notes, timestamps
```

---

## ⚙️ Installation & Setup

### Prerequisites
- PHP 8.2+
- Composer
- Laravel 12
- MySQL / PostgreSQL / SQLite

### Step 1 – Create a new Laravel 12 project

```bash
composer create-project laravel/laravel stock-management
cd stock-management
```

### Step 2 – Copy the provided files

Copy all files from this package into your Laravel project maintaining the directory structure:

```
app/Models/Stock.php
app/Models/StockPrice.php
app/Models/PortfolioHolding.php
app/Models/Transaction.php

app/Http/Controllers/DashboardController.php
app/Http/Controllers/StockController.php
app/Http/Controllers/PortfolioController.php

database/migrations/ (all 4 migration files)
database/seeders/DatabaseSeeder.php

resources/views/layouts/app.blade.php
resources/views/dashboard/index.blade.php
resources/views/stocks/index.blade.php
resources/views/stocks/create.blade.php
resources/views/stocks/edit.blade.php
resources/views/stocks/show.blade.php
resources/views/portfolio/index.blade.php
resources/views/portfolio/transactions.blade.php

routes/web.php
```

### Step 3 – Configure Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=stockvault
DB_USERNAME=root
DB_PASSWORD=
```

### Step 4 – Run Migrations & Seed

```bash
php artisan migrate
php artisan db:seed
```

The seeder will add **6 sample stocks** (RELIANCE, TCS, INFY, HDFCBANK, WIPRO, SBIN) with 30 days of price history and sample portfolio holdings.

### Step 5 – Start the Server

```bash
php artisan serve
```

Visit: **http://localhost:8000**

---

## 🗺️ Routes

| Method | URL | Name | Description |
|--------|-----|------|-------------|
| GET | / | dashboard | Dashboard |
| GET | /stocks | stocks.index | All stocks |
| GET | /stocks/create | stocks.create | Add stock form |
| POST | /stocks | stocks.store | Save new stock |
| GET | /stocks/{id} | stocks.show | Stock detail + chart |
| GET | /stocks/{id}/edit | stocks.edit | Edit stock |
| PUT | /stocks/{id} | stocks.update | Update stock |
| DELETE | /stocks/{id} | stocks.destroy | Delete stock |
| POST | /stocks/{id}/update-price | stocks.update-price | Add daily OHLCV |
| GET | /portfolio | portfolio.index | Holdings + P&L |
| POST | /portfolio/buy | portfolio.buy | Record buy |
| POST | /portfolio/sell | portfolio.sell | Record sell |
| GET | /portfolio/transactions | portfolio.transactions | Transaction history |

---

## 📱 How to Use

### Managing Daily High & Low
1. Go to **All Stocks** and click on any stock
2. Click **"Update Price"** button (top right)
3. Enter the date, Open, **High**, **Low**, Close, and Volume
4. Click **Save Price**
5. The chart and table update automatically
6. If the date is today, the stock's current price is also updated

### Adding a Stock
1. Go to **All Stocks → Add New Stock**
2. Enter symbol (e.g., `TATAMOTORS`), company name, sector, exchange
3. Enter the previous close and current price
4. Click **Add Stock**

### Buying Shares
1. Click **Buy Stock** on the Portfolio page, or open a stock and click **Buy**
2. Enter quantity, price per share, brokerage, and date
3. The system automatically calculates your average cost if you already own shares

### Selling Shares
1. Click **Sell Stock** on the Portfolio page, or open a stock you own and click **Sell**
2. The system validates you have sufficient shares and updates your holding

---

## 🛠️ Technology Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 12 (PHP 8.2+) |
| Frontend | Bootstrap 5.3 + Bootstrap Icons |
| Charts | Chart.js 4 |
| Database | MySQL / SQLite |
| Styling | Custom CSS (no Tailwind required) |

---

## 🔮 Future Enhancements

- User authentication (multi-user support)
- Stock alerts (price target notifications)
- Import prices from CSV/Excel
- API integration (NSE/BSE live data)
- Dividend tracking
- Tax P&L report (STCG/LTCG)
- Export reports to PDF/Excel

---

## 📄 License

MIT License – Free for personal and commercial use.
