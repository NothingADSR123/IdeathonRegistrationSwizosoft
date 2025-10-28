# 💡 Ideathon Registration System (PHP + MySQL)

A simple, efficient web application for managing **Ideathon registrations**, built with a **PHP backend** and a **MySQL database**.

---

## ✨ Features

* **Platform:** Developed using **PHP** for the backend logic and **MySQL** for data persistence.
* **Payment Proof:** Allows participants to **upload a GPay (Google Pay) screenshot** as proof of payment.
    * *Storage:* Uploaded files are securely stored in the `assets/uploads/` directory.
* **Real-time Validation:** Includes an **AJAX-based check** for **team name duplication** during the registration process, providing immediate feedback.
* **QR Code Generation:** Generates unique **QR codes** for each registration using the reliable **Google Chart API**.

---

## 🛠️ Installation Guide

Follow these steps to get the Ideathon Registration System running on your local machine or web server.

1.  **Database Setup:**
    * Import the provided database schema file, `database/ideathon.sql`, into your MySQL server. This creates the necessary tables.

2.  **Database Connection:**
    * Open and edit the file `backend/db_connect.php`.
    * Update the database connection details (hostname, username, password, and database name) with your specific MySQL credentials.

3.  **File Permissions:**
    * Ensure that the file upload directory, `assets/uploads/`, has the correct **write permissions** so that your web server (e.g., Apache) can save the payment screenshots.

4.  **Deployment:**
    * Place all the project files onto a web host that supports **PHP**.
    * A web server like **Apache** with PHP installed is the recommended environment.

5.  **Access:**
    * Navigate to the primary entry point of the application: `index.php` in your web browser.

---

## 🛡️ Security Recommendations

While this is a basic system, implementing the following security measures is **crucial** before a public launch:

* **Use HTTPS:** Always serve the application over **HTTPS** to encrypt data transmitted between the user's browser and the server.
* **Secure Uploads Directory (`.htaccess`):**
    * Verify that the included `.htaccess` file in the `assets/uploads/` directory is present and configured correctly.
    * *Purpose:* This file typically prevents the direct execution of scripts (like PHP files) within the upload folder, mitigating a major security risk.
* **Enhanced Validation and Rate Limiting:**
    * **Increase Server-Side Validation:** Implement more robust checks on all submitted data (e.g., file type, file size, email format, etc.) directly on the server.
    * **Add Server-Side Rate-Limits:** Introduce measures to limit the number of requests a single user or IP address can make in a short period to prevent spam or brute-force attacks.