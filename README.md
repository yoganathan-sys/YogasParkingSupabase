# Yoga's Parking System - PHP + Supabase

This version converts the original MySQL/MariaDB `mysqli` backend to Supabase PostgreSQL through the Supabase REST/PostgREST API.

## Files

- `index.php` - complete parking page, add, update and display
- `del1.php` - delete backend
- `config.php` - Supabase REST connection/helper
- `schema.sql` - Supabase database table
- `.env.example` - environment variable template
- `.gitignore` - prevents local secrets from being committed
- `car-1.jpeg` - put your existing parking background image here

## Supabase setup

1. Create/open a Supabase project.
2. Open SQL Editor.
3. Run `schema.sql`.
4. Go to Project Settings -> API.
5. Copy the Project URL.
6. Copy the server-side Service Role key.
7. Set:

SUPABASE_URL
SUPABASE_SERVICE_ROLE_KEY

## Local XAMPP

PHP must have cURL enabled.

Put the folder inside:

C:\xampp\htdocs\YogasParkingSupabase

Set the environment variables in your PHP/Apache environment, or temporarily configure them in Apache for local testing.

Do NOT hard-code the service role key into `index.php`.

## Hosting

This project uses PHP and cURL to call Supabase REST API.

Your PHP hosting must support:
- PHP
- cURL
- outbound HTTPS requests

If using Vercel, remember that Vercel does not provide the traditional XAMPP/Apache PHP hosting model. You need a PHP-compatible deployment/runtime. For the simplest deployment, use a PHP host such as Render/Railway or another PHP server.

## Important security rule

The `SUPABASE_SERVICE_ROLE_KEY` is a secret server key. Never:
- put it in JavaScript
- put it in HTML
- commit it to GitHub
- send it to the browser

The browser communicates with your PHP backend; PHP communicates with Supabase.
