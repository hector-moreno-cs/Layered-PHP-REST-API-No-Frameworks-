# Layered PHP REST API v1 (No Framework)

This project is a backend API built in plain PHP as part of a university coursework assignment. The goal was to implement a working API, so I designed a structured backend architecture using separation of concerns and layered design principles.
The project intentionally avoids frameworks to demonstrate how routing, dependency injection, and validation can be implemented manually in PHP.

---

## Overview

The API manages three main components:

- Device Types
- Manufacturers
- Devices

It supports basic CRUD operations, search functionality, and data validation.

The project was designed to emulate how backend systems are structured in real-world applications.

---

## Architecture

The system follows a layered architecture approach:

```text
Client Request
↓
Router
↓
Controller Layer
↓
Service Layer (Application Logic)
↓
Data Access Layer
↓
Database
```

### Layers Explained

**Router**
- Handles incoming HTTP requests
- Maps endpoints to controller methods

**Controller Layer**
- Extracts and validates request input
- Calls the service layer
- Formats API responses

**Service Layer**
- Contains business logic and validation rules
- Handles error management and logging
- Coordinates between controllers and data access

**Data Access Layer**
- Executes SQL queries using prepared statements
- Handles direct interaction with the database

**Infrastructure**
- Database connection handled via a reusable database class
- Logging system stores application events and errors in a dedicated database

---

## Technologies Used

- PHP (no frameworks)
- MySQL
- Custom routing system
- Custom logging system

---

## Key Features

- Separation of concerns
- Composition root
- Input validation at controller and service levels
- Centralized routing system
- Structured logging system stored in a database
- Dependency injection
- Error handling with standardized API responses
- Duplicate entry detection (MySQL error handling)

---

## API Structure

The API uses custom endpoints such as:

- `api/get_all_device_types`
- `api/add_device_type`
- `api/modify_device_type`
- `api/get_all_devices`
- `api/search_by_serial`

All responses follow a consistent format:

```json
{
  "status": "success | error",
  "message": "Description of result",
  "data": []
}
```

## Example Request

### GET All Devices

```http
GET api/get_all_devices
```

### Response

```json
{
  "status": "success",
  "message": "Devices retrieved",
  "data": [
    {
      "d_id": 1,
      "dt_name": "television",
      "m_name": "TLC",
      "serial_number": "87ca1..."
    }
  ]
}
```

## Project Structure

```text
Project Root (provided by Nginx)
│
├── api/
│   └── api.php
│
├── api-logic/
│   ├── Controllers/
│   ├── ApplicationLayer/
│   ├── DataAccessLayer/
│   ├── router.php
│   ├── database.php
│   └── logger.php
```
