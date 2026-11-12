# Signature by Farah

A salon website and appointment-management application built with Laravel 12, Inertia, Vue 3, and Tailwind CSS.

## Requirements

- PHP 8.2+
- Composer
- Node.js 22+
- MySQL 8 (or another Laravel-supported database)

## Local setup

```powershell
composer install
npm install
Copy-Item .env.example .env
php artisan key:generate
```

Configure the database and mail transport in `.env`, then migrate and seed the salon catalog:

```powershell
php artisan migrate --seed
php artisan storage:link
npm run build
php artisan serve
```

Run `npm run dev` in a second terminal while developing the frontend.
Image uploads use Laravel's public disk; keep the storage link in place and configure PHP's `upload_max_filesize` to at least 6M and `post_max_size` to at least 20M. The Docker image applies these limits and creates the storage link at startup.

The salon seeder adds starter service categories, services priced in Pakistani Rupees (PKR), stylists, testimonials, gallery entries, and contact details. Replace the sample imagery and business details with your salon's own content in the studio dashboard.

## Features

- Public home, about, services and service details, team, gallery, pricing, and contact pages.
- Search metadata, local-business structured data, `robots.txt`, and a sitemap generated from active services. Set `APP_URL` to the canonical public HTTPS URL before deployment.
- Customer registration and email/password login without verification codes; password reset remains available.
- Appointment availability from Tuesday to Saturday, 9am–7pm, with 30-minute slots and overlap protection.
- Customer dashboard for account-linked appointments.
- Studio administration for appointments, customers, services, categories, stylists, gallery, testimonials, homepage content, and contact details.

Studio administration is restricted to users with the existing `super_admin` role. Assign that role to authorized staff accounts through the application's role-management tools.

## Tests

Run the Laravel suite with `php artisan test`. The salon feature tests exercise booking conflicts, appointment management, admin authorization, password authentication, registration, and the verification-field removal migration.
