# 💵 Simple Spending Tracker

A mobile-first spending tracker built with Laravel 12, Vue 3, and Tailwind CSS.  
Designed for fast entry, clear visuals, and simple monthly tracking.

## Features

- ✅ Email login via Laravel Breeze (Vue starter kit)
- 📆 Track transactions (name, amount, date)
- 📊 See total and projected spending for the current month
- 📱 Mobile-first layout, large font/buttons for ease of use
- 🧠 Clean inline editing — no popups or modals

---

## Tech Stack

- **Laravel 12**
- **Vue 3**
- **TailwindCSS**
- **MySQL or SQLite** (dev)

---

## TODO

### ✅ Core Setup

- [x] Install Laravel + Breeze (Vue)
- [x] Auth working (login/register)
- [x] Email/password settings page

### 🧱 Backend

- [ ] Create `transactions` table migration
     - Fields: `id`, `user_id`, `name`, `amount`, `date`, `timestamps`
- [ ] Create `Transaction` model
- [ ] Setup API routes:
     - [ ] GET: fetch transactions (current month only)
     - [ ] POST: create transaction
     - [ ] PUT: update transaction
     - [ ] DELETE: delete transaction
- [ ] Add auth middleware to routes
- [ ] Apply policy to restrict to authenticated user’s transactions only

### 🖼️ Frontend (Vue)

- [ ] Create `Dashboard.vue` layout
     - [ ] Display total + projected total at top
     - [ ] Include AddTransaction and TransactionTable components
- [ ] Create `AddTransaction.vue`
     - [ ] Inline form for name, amount, date
     - [ ] Submits to backend
- [ ] Create `TransactionTable.vue`
     - [ ] Loops through transactions
     - [ ] Shows: date, name, amount, [edit], [delete]
- [ ] Create `TransactionRow.vue`
     - [ ] Inline editing for name, amount, date
     - [ ] Emits save/cancel/delete
- [ ] Mobile-first layout
     - [ ] Centered, max-w-md container
     - [ ] Large fonts + buttons
     - [ ] Minimal animation, accessible for sensitive users
- [ ] Optional dark mode toggle

### 📊 Logic

- [ ] Total spent: sum of all transactions in current month
- [ ] Projected total:
     - Avg daily spend × total days in month
     - Use `dayjs` or `date-fns` if needed
- [ ] Ability to choose a different month for filtering
- [ ] CSV export
