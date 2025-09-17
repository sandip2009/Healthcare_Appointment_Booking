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

### For Optimize Clear
```bash

php artisan optimize

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
**Response**
```json
{
    "status": true,
    "message": "User registered successfully.",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john2@example.com",
            "email_verified_at": null
        },
        "token": "1|pvSJvUPurSiPtBtbLk6a7M0TpdJgrGMyK2PiXvLHa0fe5b69"
    }
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
**Response**
```json
{
    "status": true,
    "message": "User registered successfully.",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john2@example.com",
            "email_verified_at": null
        },
        "token": "1|pvSJvUPurSiPtBtbLk6a7M0TpdJgrGMyK2PiXvLHa0fe5b69"
    }
}
```

---

### 👨‍⚕️ Healthcare Professionals

#### List all professionals (with available days: SUN–SAT)
```http
GET /api/healthcare-professionals
```
**Response**
```json
{
    "data": [
        {
            "id": 1,
            "name": "Dr. Rosalyn Brekke",
            "about": "Suscipit dolorem.",
            "available": true,
            "speciality": "GeneralPhysician",
            "days": [
                {
                    "day": "WED",
                    "date": "17",
                    "full_date": "2025-09-17",
                    "available": false
                },
                {
                    "day": "THU",
                    "date": "18",
                    "full_date": "2025-09-18",
                    "available": false
                },
                {
                    "day": "FRI",
                    "date": "19",
                    "full_date": "2025-09-19",
                    "available": false
                },
                {
                    "day": "SAT",
                    "date": "20",
                    "full_date": "2025-09-20",
                    "available": false
                },
                {
                    "day": "SUN",
                    "date": "21",
                    "full_date": "2025-09-21",
                    "available": false
                },
                {
                    "day": "MON",
                    "date": "22",
                    "full_date": "2025-09-22",
                    "available": false
                },
                {
                    "day": "TUE",
                    "date": "23",
                    "full_date": "2025-09-23",
                    "available": false
                },
                {
                    "day": "WED",
                    "date": "24",
                    "full_date": "2025-09-24",
                    "available": false
                }
            ]
        }
    ],
    "links": {
        "first": "http://127.0.0.1:8000/api/healthcare-professionals?page=1",
        "last": "http://127.0.0.1:8000/api/healthcare-professionals?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "links": [
            {
                "url": null,
                "label": "&laquo; Previous",
                "page": null,
                "active": false
            },
            {
                "url": "http://127.0.0.1:8000/api/healthcare-professionals?page=1",
                "label": "1",
                "page": 1,
                "active": true
            },
            {
                "url": null,
                "label": "Next &raquo;",
                "page": null,
                "active": false
            }
        ],
        "path": "http://127.0.0.1:8000/api/healthcare-professionals",
        "per_page": 10,
        "to": 6,
        "total": 6
    },
    "success": true,
    "message": "Healthcare professionals fetched successfully."
}
```

#### Show professional (today’s slots + available days)
```http
GET /api/healthcare-professionals/{id}
```

**Response**
```json
{
    "success": true,
    "message": "Healthcare professionals fetched successfully.",
    "data": {
        "id": 1,
        "name": "Dr. Rosalyn Brekke",
        "about": "Suscipit dolorem.",
        "available": true,
        "speciality": "GeneralPhysician",
        "days": [
            {
                "day": "WED",
                "date": "17",
                "full_date": "2025-09-17",
                "available": true
            },
            {
                "day": "THU",
                "date": "18",
                "full_date": "2025-09-18",
                "available": true
            },
            {
                "day": "FRI",
                "date": "19",
                "full_date": "2025-09-19",
                "available": true
            },
            {
                "day": "SAT",
                "date": "20",
                "full_date": "2025-09-20",
                "available": true
            },
            {
                "day": "SUN",
                "date": "21",
                "full_date": "2025-09-21",
                "available": false
            },
            {
                "day": "MON",
                "date": "22",
                "full_date": "2025-09-22",
                "available": true
            },
            {
                "day": "TUE",
                "date": "23",
                "full_date": "2025-09-23",
                "available": false
            },
            {
                "day": "WED",
                "date": "24",
                "full_date": "2025-09-24",
                "available": true
            }
        ],
        "slots_available": [
            {
                "day": "WED",
                "date": "17",
                "full_date": "2025-09-17",
                "available": true,
                "slots": [
                    {
                        "time": "10:00 am",
                        "available": true
                    },
                    {
                        "time": "10:30 am",
                        "available": true
                    },
                    {
                        "time": "11:00 am",
                        "available": true
                    }
                    // ......................
                ]
            },
            {
                "day": "THU",
                "date": "18",
                "full_date": "2025-09-18",
                "available": true,
                "slots": [
                    {
                        "time": "10:00 am",
                        "available": true
                    },
                    {
                        "time": "10:30 am",
                        "available": true
                    },
                    {
                        "time": "11:00 am",
                        "available": true
                    }
                    // ................
                ]
            },
            {
                "day": "FRI",
                "date": "19",
                "full_date": "2025-09-19",
                "available": true,
                "slots": [
                    {
                        "time": "10:00 am",
                        "available": true
                    },
                    {
                        "time": "10:30 am",
                        "available": true
                    },
                    {
                        "time": "11:00 am",
                        "available": true
                    },
                    // ......................
                ]
            },
            {
                "day": "SAT",
                "date": "20",
                "full_date": "2025-09-20",
                "available": true,
                "slots": [
                    {
                        "time": "10:00 am",
                        "available": true
                    },
                    {
                        "time": "10:30 am",
                        "available": true
                    },
                    {
                        "time": "11:00 am",
                        "available": true
                    },
                    {
                        "time": "11:30 am",
                        "available": true
                    }
                    // ......................
                ]
            },
            {
                "day": "SUN",
                "date": "21",
                "full_date": "2025-09-21",
                "available": false,
                "slots": []
            },
            {
                "day": "MON",
                "date": "22",
                "full_date": "2025-09-22",
                "available": true,
                "slots": [
                    {
                        "time": "10:00 am",
                        "available": true
                    },
                    {
                        "time": "10:30 am",
                        "available": true
                    },
                    {
                        "time": "11:00 am",
                        "available": true
                    },
                    {
                        "time": "11:30 am",
                        "available": true
                    }
                    // .................
                ]
            },
            {
                "day": "TUE",
                "date": "23",
                "full_date": "2025-09-23",
                "available": false,
                "slots": []
            },
            {
                "day": "WED",
                "date": "24",
                "full_date": "2025-09-24",
                "available": true,
                "slots": [
                    {
                        "time": "10:00 am",
                        "available": true
                    },
                    {
                        "time": "10:30 am",
                        "available": true
                    },
                    {
                        "time": "11:00 am",
                        "available": true
                    }, "available": true
                    // ..................
                ]
            }
        ]
    }
}
```

#### Get available slots by day
```http
POST /api/healthcare-professionals/availableSlotsByDate/{id}
```
**Payload:**
```json
{
    "full_date": "2025-09-18"
}
```

**Response**
```json
{
    "success": true,
    "message": "Available slots fetched successfully.",
    "full_date": "2025-09-18",
    "slots": [
        {
            "time": "10:00 am",
            "available": true
        },
        {
            "time": "10:30 am",
            "available": true
        },
        {
            "time": "11:00 am",
            "available": true
        },
        {
            "time": "11:30 am",
            "available": true
        },
        ...

        {
            "time": "10:00 pm",
            "available": true
        }
    ]
}
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

```json
{
    "data": [
        {
            "id": 1,
            "user_id": 1,
            "healthcare_professional_id": 3,
            "appointment_start_time": "2025-09-17 11:00:00",
            "appointment_end_time": "2025-09-17 12:30:00",
            "appointment_date": "2025-09-17",
            "appointment_day": "WED",
            "appointment_slot": "11:00 am - 12:30 pm",
            "status": "booked",
            "description": "Regular health checkup",
            "created_at": "2025-09-17 10:17:37",
            "updated_at": "2025-09-17 10:17:37"
        }
    ],
    "links": {
        "first": "http://127.0.0.1:8000/api/appointments?page=1",
        "last": "http://127.0.0.1:8000/api/appointments?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "links": [
            {
                "url": null,
                "label": "&laquo; Previous",
                "page": null,
                "active": false
            },
            {
                "url": "http://127.0.0.1:8000/api/appointments?page=1",
                "label": "1",
                "page": 1,
                "active": true
            },
            {
                "url": null,
                "label": "Next &raquo;",
                "page": null,
                "active": false
            }
        ],
        "path": "http://127.0.0.1:8000/api/appointments",
        "per_page": 10,
        "to": 1,
        "total": 1
    },
    "success": true,
    "message": "Pppointments list fetched successfully."
}
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
**Response**
```json
{
    "success": true,
    "message": "Appointment booked successfully.",
    "data": {
        "id": 1,
        "user_id": 1,
        "healthcare_professional_id": 3,
        "appointment_start_time": "2025-09-17 11:00:00",
        "appointment_end_time": "2025-09-17 12:30:00",
        "appointment_date": "2025-09-17",
        "appointment_day": "WED",
        "appointment_slot": "11:00 am - 12:30 pm",
        "status": null,
        "description": "Regular health checkup",
        "created_at": "2025-09-17 10:17:37",
        "updated_at": "2025-09-17 10:17:37"
    }
}
```

#### Cancel an appointment
```http
POST /api/appointments/cancel
```
Payload
```json
{
    "appointment_id": 2
}
```
**Response**
```json
{
    "success": true,
    "message": "Appointment cancelled successfully.",
    "data": {
        "id": 2,
        "user_id": 1,
        "healthcare_professional_id": 3,
        "appointment_start_time": "2025-09-19 11:00:00",
        "appointment_end_time": "2025-09-19 12:30:00",
        "appointment_date": "2025-09-19",
        "appointment_day": "FRI",
        "appointment_slot": "11:00 am - 12:30 pm",
        "status": "cancelled",
        "description": "Regular health checkup",
        "created_at": "2025-09-17 10:25:51",
        "updated_at": "2025-09-17 10:26:05"
    }
}
```
---

## 📜 License
This project is open-sourced under the [MIT License](https://opensource.org/licenses/MIT).
