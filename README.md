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

## Database Initialization

The application includes a robust database initialization system that ensures the database is properly set up on startup:

1. When the containers start, the database service initializes first
2. The application container waits for the database to be ready before starting
3. On startup, the application automatically:
   - Checks if the database is accessible
   - Verifies that all required tables exist
   - Creates any missing tables using the schema.sql file
   - Ensures the default admin user is created

This automatic initialization process means you don't need to manually run any scripts or commands to set up the database. The system will handle everything for you, even if:

- This is the first time you're running the application
- The database volume was deleted or corrupted
- Some tables are missing due to partial initialization

The application won't start until the database is properly initialized, preventing errors like `Table 'mysportsapp_suite.users' doesn't exist`.

### Troubleshooting Deployment Issues

If you encounter issues during deployment, here are some common problems and solutions:

#### Container Health Check Failures

If you see an error like `dependency failed to start: container mysportsapp_php_app is unhealthy`, it means the application container failed its health check. This could be due to:

1. **Database initialization taking too long**: The application container might be trying to connect to the database before it's fully initialized.
   - Solution: The system is designed to wait for the database to be ready, but in some environments (especially with limited resources), it might need more time.
   - Check the logs of the app container for more details: `docker logs mysportsapp_php_app`

2. **Missing or corrupted schema file**: If the schema.sql file is missing or corrupted, the database tables won't be created properly.
   - Solution: Verify that the schema.sql file exists in the database directory and has the correct content.

3. **Network issues between containers**: In some environments, there might be network issues preventing the app container from connecting to the database.
   - Solution: Check that the containers can communicate with each other. The app container should be able to reach the database container at the hostname `db`.

#### Viewing Detailed Logs

For more detailed troubleshooting, you can view the logs of each container:

```bash
# View logs of the database container
docker logs mysportsapp_php_db

# View logs of the application container
docker logs mysportsapp_php_app

# View logs of the web server container
docker logs mysportsapp_php_web
```

The application and database initialization scripts include detailed logging to help diagnose issues. Look for error messages or warnings in these logs.

#### Manual Database Reset

If you need to reset the database completely:

1. Stop the containers:
   ```bash
   docker-compose down
   ```

2. Remove the database volume:
   ```bash
   docker volume rm mysportsapp-suite_mysportsapp_php_db_data
   ```

3. Start the containers again:
   ```bash
   docker-compose up -d
   ```

This will recreate the database from scratch, applying the schema.sql file to create all tables and the default admin user.

---

## Security Notes

This is a **starter** and needs hardening before production:

- Add CSRF tokens on forms.
- Add input validation / sanitation.
- Put app behind HTTPS.
- Restrict access to analytics DB credentials and secrets at OS level.

But functionally, the full suite is wired and ready to extend.
