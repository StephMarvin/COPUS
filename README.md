Classroom Observation Protocol for Undergraduate STEM (COPUS)

---

📌 Overview

The COPUS System is a web-based application designed to streamline and digitize classroom observation processes in STEM education. It replaces traditional paper-based observation methods with a centralized platform that enables real-time data recording, feedback collection, and report generation.

The system improves efficiency, reduces human error, and provides actionable insights into teaching practices.

---

🎯 Objectives

- Digitize classroom observation workflows
- Provide real-time monitoring of teaching practices
- Improve feedback accuracy and accessibility
- Support data-driven decision-making for educators and administrators

---

🧩 Key Features

- User Authentication & Role-Based Access Control (RBAC)
- Real-Time Observation Recording
- Automated Report Generation
- User Dashboards (Admin, Observer, Teacher, Dean)
- Secure Data Storage
- CRUD Operations for Data Management
- Feedback Collection and Analysis

---

👥 User Roles

Role     | Description
Admin    | Full system control, manages users and data
Observer | Records classroom observations
Teacher  | Views feedback and reports
Dean     | Monitors performance and analytics

---

🏗️ System Architecture

The system follows a Three-Tier Architecture:

1. Presentation Layer
   
   - User interface (GUI)
   - Dashboards and input forms

2. Application Layer
   
   - Business logic
   - Data processing
   - Authentication and validation

3. Data Layer
   
   - MySQL database
   - Stores observations, users, and reports

---

⚙️ Technology Stack

Layer    | Technology
Frontend | Bootstrap, JavaScript
Backend  | PHP
Database | MySQL
Server   | XAMPP (Apache)

---

🔐 Security Features

- Password hashing/encryption
- Input validation and sanitization
- OTP and reset token authentication
- Role-based access control

---

🚀 Installation Guide

Prerequisites

- XAMPP (Apache, MySQL, PHP)
- Web browser (Chrome, Edge, etc.)

Steps

1. Clone the Repository
   
   git clone https://github.com/StephMarvin/COPUS.git

2. Move Project Folder
   
   - Place the folder inside:
     C:\xampp\htdocs\

3. Start XAMPP
   
   - Start Apache and MySQL

4. Import Database
   
   - Open "phpMyAdmin"
   - Create a new database (e.g., "copus_db")
   - Import the provided ".sql" file

5. Configure Database Connection
   
   - Update database credentials in:
     config.php

6. Run the System
   
   - Open browser and go to:
     http://localhost/copus-system

---

📊 System Modules

- Landing Page
- Account Type Selection
- Login Module
- Dashboard
- Observation Recording Module
- Report Generation Module

---

📈 Methodology

The system was developed using the Agile Methodology:

1. Plan
2. Design
3. Develop
4. Test
5. Deploy
6. Review

This ensures continuous improvement and adaptability to user feedback.

---

📉 Limitations

- Requires stable internet connection
- Performance may vary on older devices

---

🔮 Future Improvements

- Mobile application support
- Cloud integration
- Advanced analytics and visualization
- Customizable observation templates

---

📚 Project Context

This system was developed as a Capstone Project for the Bachelor of Science in Information Technology, aiming to enhance classroom observation processes within academic institutions.

---

👨‍💻 Contributors

- Marvin M. Agudines
- John Mark L. Failanza
- Kishia O. Laubenia

---

📄 License

This project is intended for academic purposes. Licensing terms may be defined based on institutional requirements.

---

📬 Contact

For inquiries or improvements, please open an issue in the repository or contact the developers.

---
