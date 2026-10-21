# AIO Employee Management API

Laravel 13 REST API for employee records and payroll calculations. The backend uses the Laravel Modules package; the employee domain, API, repository, payroll service, validation, and migrations live in `Modules/Employee`.

## Setup

Requirements: PHP 8.3+, Composer, and MySQL 8+.

1. Install dependencies and prepare the local environment:

   ```powershell
   composer install
   Copy-Item .env.example .env
   php artisan key:generate
   ```

2. Create a MySQL database and enter its connection details in `.env`:

   ```dotenv
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=
   DB_USERNAME=
   DB_PASSWORD=
   ```

   These database name and credential fields are intentionally blank in the example. Keep real credentials in `.env` only.

3. Create the database tables and optional sample employees:

   ```powershell
   php artisan migrate --seed
   ```

4. Start the API:

   ```powershell
   php artisan serve
   ```

The Vue development server proxies `/api` to `http://127.0.0.1:8000`. Set `FRONTEND_URL` in `.env` if the frontend is hosted on a different origin.

## Payroll rules

- Designations: `Intern`, `Associate`, `Senior`, `Manager`.
- Monthly tax follows the provided fixed 150,000 calculation base: salary package `>= 150,000` is taxed at `150,000 × 5% = 7,500`; salary package `>= 100,000` and `< 150,000` is taxed at `150,000 × 3% = 4,500`; below `100,000` is untaxed.
- Yearly increasing bonus is calculated on monthly salary package: Manager 5%, Senior 3%, Associate 1%, Intern 0%.
- Monthly net salary is monthly salary package minus monthly tax.
- Yearly net salary is monthly net salary × 12 plus the yearly increasing bonus.
- Derived payroll values are calculated and stored on create and recalculated when salary changes. Monetary calculation uses rounded cents.

## API

Validation errors return HTTP 422 with field-level messages.

| Method | Path | Description |
| --- | --- | --- |
| GET | `/api/health` | Health check |
| GET | `/api/employees` | List all employees and payroll fields |
| GET | `/api/employees/search?phone=...` | Search employees by a partial phone number |
| GET | `/api/employees/report` | Payroll report and aggregate totals |
| POST | `/api/employees` | Add employee; tax and salary values are calculated by the API |
| GET | `/api/employees/{id}` | View one employee |
| PATCH/PUT | `/api/employees/{id}` | Edit phone and monthly salary; payroll values are recalculated |
| DELETE | `/api/employees/{id}` | Delete employee |

Create payload:

```json
{
  "name": "Jamie Smith",
  "email": "jamie@example.com",
  "phone": "+1 555 010 1234",
  "designation": "Manager",
  "monthly_salary_package": 200000
}
```

The employee response includes `monthly_tax_value`, `yearly_increasing_bonus`, `monthly_net_salary`, and `yearly_net_salary` in addition to the input details.

## Tests

```powershell
php artisan test
```

Tests use in-memory SQLite and do not require MySQL credentials.
