# 💵 Simple Spending Tracker

A mobile-first spending tracker built with Laravel 12, Vue 3, and Tailwind CSS.  
Designed to be used alongside a finance spreadsheet for fast spending entry, clear visuals, and simple monthly tracking.

## Features

- ✅ Email login via Laravel
- 📆 Track transactions (name, amount, date)
- 📊 See total and projected spending for the current month
- 📱 Mobile-first layout, large font/buttons for ease of use
- 🧠 Clean inline editing — no popups or modals

## Tech Stack

- **Laravel 12**
- **Vue 3**
- **TailwindCSS**
- **MySQL**

## TODO

### ✅ Core Setup

- [x] Install Laravel + Breeze (Vue)
- [x] Auth working (login/register)
- [x] Email/password settings page

### 🧱 Backend

- [x] Create `transactions` table migration
     - Fields: `id`, `user_id`, `name`, `amount`, `date`, `timestamps`
- [x] Create `Transaction` model
- [x] Setup API routes:
     - [x] GET: fetch transactions (current month only)
     - [/] POST: create transaction
     - [/] PUT: update transaction
     - [/] DELETE: delete transaction
- [x] Add auth middleware to routes
- [x] Apply policy to restrict to authenticated user’s transactions only

### 🖼️ Frontend

- [x] Optional dark mode toggle
- [x] Mobile-first layout
     - [x] Centered, max-w-md container
     - [x] Large fonts + buttons
     - [/] Minimal animation, accessible for sensitive users
- [x] Total and projected spend
- [/] Current month nav
- [ ] Add transaction
- [ ] Remove transaction
- [ ] Edit Transaction
- [ ] Total spent: sum of all transactions in current month
- [ ] Projected total:
     - Avg daily spend × total days in month
     - Use `dayjs` or `date-fns` if needed

### 🧪 Future

- [ ] Ability to choose a different month for filtering
- [ ] Format copy/paste for sheets integration
- [ ] CSV export
