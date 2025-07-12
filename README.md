# ID-GROW Backend Repository

A modern, scalable backend system built with **Laravel 11** using a clean architecture approach with **Repository-Service Pattern**, JWT-based authentication (with **refresh tokens**), standardized API all handler exceptios and successfully response.

---

## Project Specifications

### Tech Stack

- **Framework**: Laravel 11
- **Architecture**: Repository-Service Pattern
- **Authentication**: Access & Refresh Token
- **API Standardization**: Uniform success/error responses
- **Deployment**: Docker with Nginx for webserver & Traefik for api gateway

---

## Resources

- Postman Documentation: [https://documenter.getpostman.com/view/41973164/2sB34fngTH](https://documenter.getpostman.com/view/41973164/2sB34fngTH)  
- API Deployment URL: [https://api-grow-case.amy-dev.my.id/api/users](https://api-grow-case.amy-dev.my.id/api/users)

## How to Run Locally

```bash
git clone https://github.com/gedehariyogananda/id-grow.git
cd id-grow

composer install

cp .env.example .env

php artisan key:generate

# Configure your PostgreSQL database in `.env`:
#    - DB_CONNECTION=pgsql
#    - DB_HOST=127.0.0.1
#    - DB_PORT=5432
#    - DB_DATABASE=id-grow-db
#    - DB_USERNAME=your_postgres_username
#    - DB_PASSWORD=your_postgres_password
# then run
php artisan migrate

php artisan migrate:fresh --seed

php artisan serve
```

## Overview Core Tasks

### 1. CRUD All Modules

All modules implement complete **Create**, **Read**, **Update**, and **Delete** functionality in every folder postman.

![CRUD](https://github.com/user-attachments/assets/45a89372-d107-4b5e-a239-46a92a546f5c)

---

### 2. User Mutation History

#### Get All Users with Mutations

- `GET /users?embed=mutations`

![User List](https://github.com/user-attachments/assets/d277533b-3878-413b-bd6e-3bcd4ddd7da9)

🔗 [Postman Docs](https://documenter.getpostman.com/view/41973164/2sB34fngTH#13e36867-bf09-4e74-881d-080b1fdb54c5)

#### Get User Detail with Mutations

- `GET /users/:id`

![User Detail](https://github.com/user-attachments/assets/d8806e34-c4db-494a-ae7e-49a305c179dd)

🔗 [Postman Docs](https://documenter.getpostman.com/view/41973164/2sB34fngTH#07afd5c0-46d8-4e29-b105-b224170a68d6)

---

### 3. Product Mutation History

Displays product mutation based on each **product location**.

![Product Location](https://github.com/user-attachments/assets/4382a839-85d9-44cb-9560-cb36fadeb37c)

#### Get All Products with Mutations

- `GET /products?embed=mutations`

![Product List](https://github.com/user-attachments/assets/837d2171-bf02-48cc-9aa4-271cb5315a31)

🔗 [Postman Docs](https://documenter.getpostman.com/view/41973164/2sB34fngTH#61ee6f9f-f83b-4eaa-824e-8560d4ef5758)

#### Get Product Detail with Mutations

- `GET /products/:id?embed=mutations`

![Product Detail](https://github.com/user-attachments/assets/15afeac8-bf1b-41c6-85b8-97fe1610bd64)

🔗 [Postman Docs](https://documenter.getpostman.com/view/41973164/2sB34fngTH#2fed624f-a243-48ff-9e2d-f967f52b24bb)

---

### 4. Stock Mutation Module

> This module manages **stock in/out mutations** via a pivot table (`product_location`). It ensures accurate stock adjustment and rollback logic when mutation types or quantities are updated or deleted.

![Mutation Logic](https://github.com/user-attachments/assets/e8b53b5c-e261-410f-ad80-ec5a114e3d65)

#### Create Mutation

- `POST /mutations`
- Generates a unique mutation code.
- Validates if stock is sufficient for `out` type.
- Increases or decreases stock accordingly.

![Add Mutation](https://github.com/user-attachments/assets/3fe32a59-7c99-4021-97f1-61eef24616b8)

🔗 [Postman Docs](https://documenter.getpostman.com/view/41973164/2sB34fngTH#b0be368b-83bc-40c3-8931-f940daf6e771)

---

#### Update Mutation

- `PUT /mutations/:id`
- Reverts old mutation's stock effect first.
- Validates and applies new mutation data.
- Prevents update if resulting stock would be invalid.

![Update Mutation](https://github.com/user-attachments/assets/82d200e0-bff1-42b0-ba31-5856175bd823)

🔗 [Postman Docs](https://documenter.getpostman.com/view/41973164/2sB34fngTH#dca431fc-6a81-463c-bba6-910dc2514f03)

---

#### Delete Mutation

- `DELETE /mutations/:id`
- Reverses the mutation effect on stock before deletion.
- Safely removes the mutation record.

![Delete Mutation](https://github.com/user-attachments/assets/a25e029f-04eb-4c14-8488-c5046ba4af5e)

🔗 [Postman Docs](https://documenter.getpostman.com/view/41973164/2sB34fngTH#b4f69165-5926-403b-9679-d7efcc8f7faa)

---

Thankfull

