# MySportsApp Suite – PHP + Docker

This is a **modular admin suite** for MySportsApp, built in plain PHP and
designed to run in Docker (Nginx + PHP-FPM + MySQL).

## Features

- Role-based access:
  - `super_admin` – full control, can:
    - Manage analytics DB connections (multi-DB)
    - Configure Paystack + Xero credentials
    - Manage knowledge base
    - View and manage tickets
    - View all finance data and run Paystack sync
  - `support` – support console:
    - Dashboard (read-only)
    - Tickets (view/update)
    - Knowledge base (manage)
    - Finance read-only
  - `accounting` – finance console:
    - Dashboard (read-only)
    - Finance payouts & breakdown (read-only)

- Advanced Analytics Dashboard
  - Pulls data from **multiple external DBs** with the same sports schema
    (`provinces`, `regions`, `clubs`, `members`).
  - National totals: players, clubs, coaches, referees, REOs.
  - Province → Region breakdown in an expandable table.

- Knowledge Base + Public FAQ
  - Internal KB management for admins.
  - Public FAQ page at `?route=faq` with accordion layout.

- Ticketing System
  - Public ticket submission form at `?route=ticket_public`.
  - Internal ticket list + detail view with status + notes.

- Finance Module
  - Real Paystack integration (no mocks):
    - Settlements synced into `paystack_settlements`.
    - Settlement transactions synced into `paystack_transactions`.
  - Each transaction reference is parsed as:
    `PROVINCE-REGION-CLUB-UNIQUE`
    e.g. `KZN-HarryGwala-united-fc-7whb`
  - For each settlement (payout) you get:
    - National total amount.
    - Breakdown by province.
    - Breakdown by region inside province.
  - Finance settings page to configure Paystack + Xero.

- Xero Integration (stub)
  - `Finance Settings` includes Xero configuration and "Connect Xero" button.
  - `src/Services/XeroService.php` provides clear TODOs to finish OAuth & API calls.

- UI
  - Tailwind CSS via CDN.
  - Dark, NocoBase-style admin UI in your colors:
    - Deep navy background.
    - Vibrant orange + light blue accents.
    - Glassmorphism cards & gradient headers.

- Docker
  - `docker-compose.yml` for app (PHP-FPM), web (Nginx), db (MySQL).
  - `database/schema.sql` auto-applied on **first run** via MySQL init.
  - Includes a seeded **superadmin user**.

---

## Default Super Admin

After first `docker-compose up`, the DB is created and seeded with:

- Email: `admin@mysportsapp.local`
- Password: `Admin123!`
- Role: `super_admin`

You can log in at `/index.php?route=login`.

---

## Running with Docker (VPS, e.g. Hostinger)

### Option 1: Manual Deployment

1. Copy this repo to your VPS (git clone or upload).
2. From the project root:

```bash
docker-compose build
docker-compose up -d
```

3. MySQL container will create DB `mysportsapp_suite` and auto-run `database/schema.sql`
   on the first start (because of the `docker-entrypoint-initdb.d` mount).

4. Visit your server IP/domain in a browser:
   - If not logged in → redirects to `?route=login`.
   - Public endpoints (no login required):
     - `?route=faq` – FAQ / KB public view.
     - `?route=ticket_public` – ticket submission form.

### Option 2: Automated Deployment from GitHub URL

#### For Linux VPS (e.g., Hostinger)

1. Download the deployment script to your VPS:

```bash
curl -O https://raw.githubusercontent.com/yourusername/MySportsApp-Suite/main/deploy.sh
chmod +x deploy.sh
```

2. Run the script with your GitHub repository URL:

```bash
sudo ./deploy.sh "https://github.com/yourusername/MySportsApp-Suite.git" "main"
```

The script will:
- Install Docker and Docker Compose if they're not already installed
- Clone the repository from the provided GitHub URL
- Build and start the Docker containers
- Display the URL and login credentials

#### For Windows Development Environment

1. Download the PowerShell deployment script:

```powershell
Invoke-WebRequest -Uri "https://raw.githubusercontent.com/yourusername/MySportsApp-Suite/main/deploy.ps1" -OutFile "deploy.ps1"
```

2. Run the script with your GitHub repository URL:

```powershell
.\deploy.ps1 -GitHubUrl "https://github.com/yourusername/MySportsApp-Suite.git" -Branch "main"
```

The script will:
- Check if Docker Desktop is installed and running
- Clone the repository from the provided GitHub URL
- Build and start the Docker containers
- Display the URL and login credentials

---

## Analytics DBs

External sports DBs are **not** created by this repo.

You connect them in the UI:

1. Login as `super_admin`.
2. Go to **System → Analytics Sources**.
3. Add one or more DBs with DSN, user, password.
4. These DBs must each have:
   - `provinces` (id, name, code)
   - `regions` (id, name, province_id)
   - `clubs` (id, name, region_id)
   - `members` (id, club_id, type ENUM('player','coach','referee','reo',...))

The dashboard will aggregate metrics across all active analytics sources.

---

## Paystack

1. As `super_admin`, go to **System → Finance Integrations**.
2. Enter:
   - Paystack Secret Key
   - Paystack Public Key
3. Save.

Then in **Finance → Payouts**:

- Click **"Sync Paystack"** (super_admin only).
- This downloads latest settlements + settlement transactions.
- Payout list shows each settlement; clicking one shows full breakdown:
  - National total.
  - Province totals.
  - Region totals inside each province.

---

## Xero

This repo does **not** fully implement Xero OAuth – that needs tenant-specific
setup. You get:

- Config fields for client ID, secret, redirect URI.
- A placeholder **Connect Xero** button.
- A `XeroService` stub where you can plug in:

  - Building the auth URL.
  - Exchanging `code` for tokens.
  - Refresh token flow.
  - Creating invoices from settlements.

---

## Security Notes

This is a **starter** and needs hardening before production:

- Add CSRF tokens on forms.
- Add input validation / sanitation.
- Put app behind HTTPS.
- Restrict access to analytics DB credentials and secrets at OS level.

But functionally, the full suite is wired and ready to extend.
