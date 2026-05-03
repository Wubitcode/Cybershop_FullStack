🛡️ CYBERSHOP: Secure Digital Frontier
A Scalable, Enterprise-Grade E-commerce Infrastructure for Cybersecurity Assets

📖 Project Overview
Cybershop is a full-stack, security-first web application designed to simulate a high-tech marketplace for professional cybersecurity hardware and services. The platform features an immersive "hacker-terminal" aesthetic, utilizing modern UI/UX principles like glassmorphism and high-contrast navigation sidebars.  
+1

The project demonstrates a specialized focus on Role-Based Access Control (RBAC) and real-time administrative monitoring via a dedicated "Security Hub".  

🚀 Key Features
Lazy-Loaded Angular Architecture: Optimized for performance and scalability, ensuring that feature modules are only loaded when required.  

Security Hub (Admin Node_01): A robust dashboard for real-time sales overview, system integrity monitoring (AES-256 active), and server log tracking.  

Live Access Logs: Transparent logging of administrative actions, including timestamps, operator IDs, and IP source tracking.  

Asset Management Protocol: A secure CRUD system for registering new hardware assets with unique SKU validation and integrity checks.  

Secure Authentication Portal: A customized system access terminal designed for authorized personnel only.  

🛠️ Technical Stack
Frontend (Angular)
Modular Design: Implemented Lazy Loading to reduce initial bundle size and improve scalability.  

Component-Based UI: Built with professional-grade components for the Dashboard and Inventory Management systems.  

Reactive Programming: Utilized RxJS for handling dynamic data streams and state management.  

Backend (PHP & MySQL)
RESTful API: PHP-based server-side logic handling communication between the frontend and database.  

Relational Database: MySQL managed via XAMPP, optimized for secure inventory and transaction logging.  

Role Management: Backend logic for enforcing administrative permissions across secure nodes.  

📂 Repository Structure
Plaintext
Cybershop_Project/
├── cybershop-frontend/    # Angular source code (Lazy-loaded modules)
├── cybershop-backend/     # PHP API endpoints and server logic
├── database/              # MySQL .sql export file for schema reconstruction
├── documentation/         # Presentation materials and system diagrams
└── README.md              # Project documentation
⚙️ Installation & Setup
Clone the Repository:

Backend Configuration:

Move the cybershop-backend folder to your xampp/htdocs/ directory.  

Import cybershop.sql into phpMyAdmin.  

Frontend Configuration:

Navigate to cybershop-frontend.

Run npm install to load dependencies.

Run ng serve to launch the development server.

Access:

Client: http://localhost:4200

Admin Portal: Accessible via the login terminal using authorized credentials.  

🔒 Security Implementation
As a student focusing on Cyber Security Engineering and Mobile Web Development, I integrated several security protocols into this capstone:  

Auditability: Every administrative login is logged in the "Security Hub" for transparency.  

Data Integrity: Unique identifier validation prevents duplicate assets and database corruption.  

Session Security: Active session monitoring to detect and display live connections within the admin node.  

About the Developer
I am a current student at Trios College (expected graduation 2027) pursuing a diploma in Mobile Web Development using AI. My background includes a Diploma in Cyber Security Engineering, which informs the "security-first" architecture of all my projects.  


Portfolio: [(https://github.com/Wubitcode/ReactApp2)]

LinkedIn: [Link to your LinkedIn]