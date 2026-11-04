# IT Helpdesk & Service Desk Portal
### Hardware, Networking & Technical Support Management System

A production-ready organizational IT Service Desk and E-Ticketing System built with **Laravel 13**, **Filament v5**, **Livewire**, and **PHP 8.4**.

---

## 1. System Overview

The system allows all organizational departments to lodge complaints related to IT hardware, networking, internet connectivity, printers, computers, software, email, and technical services.

### Core Workflow Roles:
1. **Super Administrator:** Organization-wide governance, complete user directory, departments management, SLA policies, encrypted SMTP settings, and global analytics.
2. **IT Hardware & Networking HOD:** Unassigned incoming complaints queue, technician assignment with live workload visibility, SLA monitoring, and technical reviews.
3. **Department HOD:** Departmental employee management (strictly isolated to their department), departmental complaint history, and performance reports.
4. **IT Support Technician:** Personal assigned ticket workbench, start work status updates, public customer progress comments, confidential internal technical notes, and resolution submissions.
5. **Department Employee / Complainant:** Lodge complaints, unique server-generated tracking IDs (`ITHN-2026-000001`), real-time activity timeline, public discussion threads, confirm resolved with 1–5 star ratings, or reopen complaints if issues persist.

---

## 2. Seeded User Accounts for Testing

All seeded accounts use the password: `password`

| Role | Username | Email | Name | Department |
| :--- | :--- | :--- | :--- | :--- |
| **Super Administrator** | `superadmin` | `admin@helpdesk.local` | Super Administrator | IT Infrastructure |
| **IT HOD** | `ithod` | `ithod@helpdesk.local` | Muhammad Tariq | IT Hardware & Networking |
| **IT Technician 1** | `tech_ahmed` | `ahmed.tech@helpdesk.local` | Ahmed Ali | IT Hardware & Networking |
| **IT Technician 2** | `tech_usman` | `usman.tech@helpdesk.local` | Usman Farooq | IT Hardware & Networking |
| **HR HOD** | `hr_hod` | `hrhod@helpdesk.local` | Sara Khan | Human Resources |
| **HR Employee** | `hr_staff` | `hrstaff@helpdesk.local` | Zainab Bibi | Human Resources |
| **Finance HOD** | `fin_hod` | `finhod@helpdesk.local` | Kamran Malik | Finance & Accounts |
| **Finance Employee**| `fin_staff` | `finstaff@helpdesk.local` | Bilal Hassan | Finance & Accounts |
| **Academic HOD** | `acad_hod` | `acadhod@helpdesk.local` | Dr. Asim Qureshi | Academic Affairs |
| **Academic Employee**| `acad_staff` | `acadstaff@helpdesk.local` | Fatima Noor | Academic Affairs |

> **Login Tip:** The login portal supports either **Username** or **Email Address**.

---

## 3. Installation & Setup Instructions

### Prerequisites
- PHP 8.4+ (with `pdo_mysql`, `mbstring`, `openssl`, `gd`, `fileinfo`)
- MySQL 8.0+ or MariaDB 10.4+
- Composer 2.x
- Node.js & NPM (for asset compilation if modifying CSS)

### Step 1: Clone or Navigate to Directory
```bash
cd d:\laragon\www\it-helpdesk
```

### Step 2: Configure Environment
Copy `.env.example` to `.env` and verify MySQL database credentials:
```env
APP_NAME="IT Helpdesk & Service Desk Portal"
APP_TIMEZONE="Asia/Karachi"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=it_helpdesk
DB_USERNAME=root
DB_PASSWORD=
```

### Step 3: Run Database Migrations and Seeders
```bash
php artisan migrate:fresh --seed
```

### Step 4: Storage Symlink
```bash
php artisan storage:link
```

### Step 5: Start the Development Server
```bash
php artisan serve
```
Open your browser and navigate to:
**`http://localhost:8000`** (automatically redirects to `/admin` login).

---

## 4. End-to-End Acceptance Lifecycle Workflow

The system is tested and verified for the complete lifecycle scenario:
1. **Lodge Complaint:** Log in as `hr_staff` (password: `password`), click **"Lodge New Complaint"**, fill details and submit. A tracking ID such as `ITHN-2026-000001` is generated.
2. **IT Review & Assignment:** Log in as `ithod`, view incoming complaints, click **"Assign"**, select `Ahmed Ali` (viewing his current active workload count), and submit.
3. **Start Investigation:** Log in as `tech_ahmed`, open assigned tickets, click **"Start Work"**. Status transitions to `In Progress` with investigation timestamp.
4. **Mark Resolved:** As `tech_ahmed`, click **"Mark Resolved"** and enter a resolution summary. Status changes to `Awaiting Confirmation`.
5. **Reopen (Issue Persists):** Log in as `hr_staff`, click **"No, Issue Persists"** and provide reasons. Status reopens while preserving the original tracking ID, SLA targets, and complete history.
6. **Final Fix & Confirmation:** As `tech_ahmed`, complete additional work and submit resolution. Log in as `hr_staff`, click **"Yes, Issue Resolved"**, select 5-star satisfaction rating and submit. The complaint closes with a permanent, auditable log.

---

## 5. Security and Privacy Isolation

- **Department Isolation:** Departmental HODs are strictly restricted to employees of their own department. Manipulation of request parameters is blocked at the Model Policy, Resource Query, and Service levels.
- **Complaint Privacy:** Complainants can only view their own lodged tickets. Direct URL tampering is prevented by Laravel Policies.
- **Confidential Internal Notes:** Technical notes are marked `is_internal = true` and are visible only to IT staff members. Complainants only receive public progress updates.
- **Encrypted SMTP Credentials:** Mail server passwords and app passwords are encrypted using `Crypt::encryptString()` and stored encrypted in the database. Plaintext passwords are never revealed back to the browser.
- **Audit Logging:** Administrative changes, user creation, ticket assignment, and status transitions are recorded in an append-only `audit_logs` table.

---

## 6. Reports & PDF / Excel Generation

Access **Complaints & Support Tickets > Reports & Analytics**:
- **Date Range Filters:** Filter by submission dates.
- **Department & Category Filters:** Role-aware departmental filters.
- **KPI Metrics:** Total complaints, open tasks, overdue SLA breaches, reopened counts, and resolution rate %.
- **Export Options:**
  - **Export Official PDF:** Generates clean, printable management report with institutional headers.
  - **Export Excel (.xlsx):** Generates structured spreadsheets using PhpSpreadsheet.
  - **Export CSV:** High-performance streamed CSV download.
  - **Individual Ticket PDF:** Printable docket containing complete timeline and resolution report for any individual ticket.

---

## 7. Queue Workers & Background Scheduler

To process queued notifications and SLA monitoring in background:
```bash
php artisan queue:work --tries=3 --timeout=90
```

Add the Laravel scheduler to your Windows Task Scheduler or cron:
```bash
php artisan schedule:run
```

---

## 8. Automated Testing

Run the automated test suite covering full workflow lifecycle, security policies, and report exports:
```bash
php artisan test
```
All feature and security tests execute against an in-memory test database without impacting your MySQL development records.
