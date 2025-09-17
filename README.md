# Healthcare Booking API

This is a **Laravel 10+ API** for managing healthcare professionals, their availability, and patient appointments.  

It supports:

- Managing healthcare professionals with availability schedules (`available_days` JSON config).
- Generating booking slots dynamically based on working hours and appointments.
- Avoiding double-booking by marking overlapping slots as unavailable.
- Returning slot availability per day or only for the current date.

<img width="1573" height="812" alt="image" src="https://github.com/user-attachments/assets/268e3d72-865a-4505-85aa-c34ca25f57f5" />


---

## 🚀 Requirements

- PHP 8.2+
- Composer
- SQLite / MySQL / MariaDB
- Laravel 12

---

## ⚙️ Setup Instructions

### Clone the repository
```bash
git clone https://github.com/sandip2009/Healthcare_Appointment_Booking.git
cd Healthcare_Appointment_Booking
```

### Install dependencies
```bash
composer install
```

### Copy `.env` file
```bash
cp .env.example .env
```

### Generate application key
```bash
php artisan key:generate
```


### Configure database (`.env`)
```env
DB_CONNECTION=mysql   # or sqlite
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=booking_app
DB_USERNAME=root
DB_PASSWORD=your_password
```



### Run migrations & seed sample data

```bash
php artisan migrate:fresh  # if used sqlite database
```

```bash
php artisan migrate --seed
```

### Run the development server
```bash
php artisan serve
```

API will be available at:  
👉 [http://127.0.0.1:8000/api](http://127.0.0.1:8000/api)

---

## 📖 API Endpoints

### 🔐 Authentication
#### Register
```http
POST /api/register
```
**Payload:**
```json
{
  "name": "John Doe",
  "email": "john2@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

#### Login
```http
POST /api/login
```
**Payload:**
```json
{
  "email": "john2@example.com",
  "password": "password123"
}
```

---

### 👨‍⚕️ Healthcare Professionals

#### List all professionals (with available days: SUN–SAT)
```http
GET /api/healthcare-professionals
```

#### Show professional (today’s slots + available days)
```http
GET /api/healthcare-professionals/{id}
```

#### Get available slots by day
```http
POST /api/healthcare-professionals/availableSlotsByDate/{id}
```

---

### 📅 Appointments

#### List all appointments
```http
GET /api/appointments
```

#### Get appointment details
```http
GET /api/appointments/{id}
```

#### Book an appointment
```http
POST /api/appointments
```
**Payload:**
```json
{
  "healthcare_professional_id": 2,
  "appointment_start_time": "2025-09-18 10:00:00",
  "appointment_end_time": "2025-09-18 12:30:00",
  "description": "Regular health checkup"
}
```

#### Cancel an appointment
```http
POST /api/appointments/{id}/cancel
```

---

## 📜 License
This project is open-sourced under the [MIT License](https://opensource.org/licenses/MIT).
