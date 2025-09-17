# Healthcare Booking API

This is a Laravel 10+ API for managing healthcare professionals, their availability, and patient appointments.  
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
- sqlite | MySQL or MariaDB
- Laravel 12

---

## ⚙️ Setup Instructions

**Clone the repository**
git clone https://github.com/sandip2009/Healthcare_Appointment_Booking.git
cd Healthcare_Appointment_Booking

**Install dependencies**
composer install

**Copy .env file**
cp .env.example .env

**Generate application key**
php artisan key:generate //optional

**Configure database**
DB_CONNECTION=mysql | DB_CONNECTION=sqlite
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=booking_app
DB_USERNAME=root
DB_PASSWORD=your_password

**Run migrations**
php artisan migrate  --seed


**Seed sample data**
php artisan db:seed

**Run the development server**
http://127.0.0.1:8000/api



📖 **API Endpoints**

Healthcare Professionals

User Login and Register 

Book an appointment
POST /api/login

Payload:
{
  "email": "john2@example.com",
  "password": "password123"
}

Book an appointment
POST /api/register

Payload:
{
  "name": "John Doe",
  "email": "john2@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}

List professionals with avelaible days like  SUN MON TUE WED THU FRI SAT 
GET /api/healthcare-professionals

Show professional (today’s slots only) with avelaible days like  SUN MON TUE WED THU FRI SAT and Slots 10:00 am, 10:30 am, 11:00 am, 11:30 am ....etc
GET /api/healthcare-professionals/{id}

Get professional’s available slots selected day like SUN | MON
Post api/healthcare-professionals/availableSlotsByDate/2


Appointments

Get appointments List
GET /api/appointments

Get appointments Details
GET /api/appointments/1

Book an appointment
POST /api/appointments

Payload:
{
  "healthcare_professional_id": 2,
  "appointment_start_time": "2025-09-18 10:00:00",
  "appointment_end_time": "2025-09-18 12:30:00",
  "description": "Regular health checkup"
}

Cancel an appointment
POST /api/appointments/1/cancel
