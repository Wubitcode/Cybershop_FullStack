
Cybershop (PHP + MySQL + Bootstrap 5)

1) Setup

Create a MySQL database named cybershop.
Import the SQL schema: sql/schema.sql
Update database credentials in: app/config.php
Place this project in your web root, e.g.:
XAMPP: htdocs/cybershop/
Access the site:
Shop: /public/index.php
Admin dashboard: /admin/dashboard.php

2) Admin login

Replace with my own local credentials

Email: my_admin_email_here
Password: your_admin_password_here

3) Postman API endpoints (JSON)

Base URL example: http://localhost/cybershop

POST /api/register.php
{"fullName":"Test User","email":"test@example.com","password":"your_password_here"}
POST /api/login.php
{"email":"your_admin_email_here","password":"your_admin_password_here"}
GET /api/products.php
POST /api/orders_create.php
{"items":[{"productId":1,"qty":2},{"productId":2,"qty":1}]}
POST /api/admin/products_create.php (admin only)
{"name":"Product Name","price":99.99,"stock":10,"imageUrl":"/assets/images/example.png"}

4) Notes on images

Store imageUrl as a local path, e.g.: /assets/images/cybershop.png
Free image sources: AI-generated, Unsplash, Pexels, Pixabay

5) Current System Capabilities

User authentication
Product management
Cart & checkout
Orders lifecycle
Payment tracking (PCI-aware)
Admin control panel
Security logging (CYBER FEATURE ⭐)
Configurable system settings
Notifications
Settings Table Includes
site_name
contact_email
currency
maintenance_mode
pci_compliance_mode
enable_2fa
max_failed_attempts
session_timeout_minutes
low_stock_threshold
PCI Compliance Highlights
No card data stored
Payments separated
Status controlled
Settings configurable
Summary of What’s Built
Full e-commerce backend ✔
Admin dashboard ✔
Order lifecycle ✔
Payment tracking ✔
Security logging ✔
Configurable system settings ✔

This version is safe for GitHub, professional, and ready to show your capstone project.

If you want, I can also write a complete .gitignore + placeholders setup for your project so that you can push it right now safely, including your SQL schema and images.

Do you want me to do that next?

Current System StatusComponentProtocol StatusFile LocationAdd New AssetONLINEadmin/products/create.phpView All AssetsONLINEadmin/products/index.phpExecute PatchONLINEadmin/products/edit.phpDeconstruct NodeONLINEadmin/products/delete.phpData EngineOPTIMIZEDapp/repos/products_repo.php

In a "Cyber" themed shop, Order Traffic is the pulse of your business. It’s where you see the "Data Packets" (Orders) moving from customers to your database.

6. What does "Order Traffic" show?

Think of this as your Live Ledger. While the Inventory shows what you have, Order Traffic shows what is happening.

Transaction ID: The unique "hash" or number for the purchase.

Customer Node: Who bought the item (Email or Username).

Asset Dispatched: Which product was purchased.

Payload Value: The total price of the order.

Status Code: Is the order PENDING, SHIPPED, or COMPLETED?

Timestamp: Exactly when the "handshake" (purchase) occurred.
To make your CyberShop OS feel like a high-tier professional platform, we need to build the "Command & Control" sectors. In real-world apps, Analytics tracks performance, the Security Hub monitors access, and Settings configures the environment.

Since we are in the Admin directory, let's map out these three new modules.

7. Analytics (The Pulse)

This page doesn't just show numbers; it shows trends. In a real shop, you'd want to see which products are "Hot" and which are "Dead Nodes."

What to include in admin/analytics.php:

Inventory Health: Percentage of stock remaining vs. out-of-stock items.

Price Distribution: Average price of assets in your repository.

User Growth: A simple count of users joined per month.

8. Security Hub (The Firewall)

This is where you monitor the "Safety" of your shop. Since you are building a Cyber theme, this page is crucial for the vibe.

What to build in admin/security.php:

Admin Access Log: A table showing who logged into the admin panel and when.

Role Management: A way to see how many users have admin privileges vs. standard user privileges.

Session Termination: A button to "Force Logout" (clear sessions) if a breach is suspected.

9. Settings (System Configuration)

This is the "Control Panel" for the entire site. Instead of changing code, you change variables here.

What to include in admin/settings.php:

Site Identity: Change the Site Name (e.g., from "CyberShop" to "NeonStore").

Maintenance Mode: A toggle switch to take the public front-end "Offline" while you work on it.

Contact Info: The email address where customer support tickets are sent.

10. About setting.php and setting_update.php

To keep your code clean and professional, you need both, but they serve two very different purposes in a "real-life" web application. Think of it like a form and a processor.The DifferenceFilePurposeWhat the User Seessettings.phpThe InterfaceThe actual page with the input boxes, the "Cyber" theme, and the "Update" button.settings_update.phpThe LogicA "hidden" file that processes the data, saves it to the database, and then sends the user back to settings.php. 

11. Root Password Hashing (BCRYPT_ACTIVE)
In a professional system, we never store a password as plain text (e.g., "Admin123"). If a hacker steals the database, they would see everyone's password.

How it works: Bcrypt takes a password and turns it into a long, unbreakable string of gibberish (a "hash").

The "Active" status: This means your code is using password_hash() and password_verify(). Even if I saw your database right now, I couldn't tell you what your password is.

12. Session Management (PHP_SESSION_LOCK)

When you log in, the server gives your browser a "Session ID" (like a temporary ID card).

The Risk: "Session Hijacking," where someone steals that ID to pretend to be you.

The "Lock" status: This usually means your config.php or auth.php is using secure settings like session.cookie_httponly (prevents JavaScript from stealing the ID) and session.cookie_secure (only sends the ID over HTTPS). It "locks" the session to your specific browser.

13. SQL Injection Prevention (PDO_PREPARED_STMTS)
This is the most important one. SQL Injection is when a hacker types code into a login box (like ' OR 1=1 --) to trick your database into letting them in without a password.

The Fix: PDO Prepared Statements. Instead of sending a raw command to the database, you send a "Template" first, then send the data separately.

Analogy: It’s like a bank teller asking you to put your money in a secure drawer rather than you reaching over the counter to put it in the vault yourself. The database "prepares" the query, making it impossible for the user's input to be executed as code.

14. Root Password Hashing (The BCRYPT Logic)
When a user registers or update password in Settings, we never save the "text." We use a one-way mathematical "shredder."

In users_repo.php or auth.php:

PHP
// REGISTERING A USER
$plain_password = "UserPassword123";
$hashed_password = password_hash($plain_password, PASSWORD_BCRYPT); 
// This saves something like: $2y$10$8K153... (Safe in DB)

// LOGGING IN
if (password_verify($input_password, $hashed_password_from_db)) {
    // Access Granted!
}
15. SQL Injection Prevention (The PDO Logic)

This is the most critical "real" code. Instead of putting variables directly into a query (which is how hackers break in), we use "placeholders" (?).

In products_repo.php or db.php:

PHP
// UNSAFE (Hacker can break this)
// $db->query("SELECT * FROM products WHERE id = " . $_GET['id']);

// SAFE (Real Professional Code)
$stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$_GET['id']]); // The ID is sent separately as "data," not "code"
$product = $stmt->fetch();

16. Session Management (The LOCK Logic)

To prevent "Session Hijacking" (where someone steals your login cookie), we configure the PHP engine to be "Strict."

In the app/config.php or at the top of auth.php:

PHP
// This tells the browser: "Don't let JavaScript see my login cookie!"
ini_set('session.cookie_httponly', '1');

// This tells the browser: "Only send my login cookie over secure HTTPS!"
ini_set('session.cookie_secure', '1');

// This starts the secure session
session_start();

To implement these "Hardened" security features for  CyberShop node, I need to modify the core authentication and configuration files. Since I am in a development environment (XAMPP/Apache), I will use a "Simulation" approach for MFA and a strict configuration for logging.

🛡️ 1. Enable Multi-Factor Authentication (MFA)

Since a full TOTP (Google Authenticator) integration requires external libraries, I can implement a Secure PIN Challenge that triggers after the password is correct.

Update my app/auth.php (The Logic):

PHP
function verify_mfa(int $userId, string $pin): bool {
    // In production, this PIN would be emailed or sent via SMS
    // For CyberShop Dev: The hardcoded bypass is '123456'
    $storedPin = "123456"; 
    
    if ($pin === $storedPin) {
        $_SESSION['mfa_verified'] = true;
        return true;
    }
    return false;
}

function require_mfa() {
    if (!isset($_SESSION['mfa_verified']) || $_SESSION['mfa_verified'] !== true) {
        header("Location: " . BASE_URL . "/public/mfa_challenge.php");
        exit;
    }
}
🚫 2. Strict IP Address Lockdown
This prevents "Session Hijacking." If a hacker steals your session cookie but tries to use it from a different internet connection (IP), the system will automatically log them out.

Add this to the top of app/config.php:

PHP
session_start();

// LOCKDOWN: Capture the user's IP during their first login
if (!isset($_SESSION['user_ip'])) {
    $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
}

// VALIDATION: If the IP changes mid-session, kill the session
if ($_SESSION['user_ip'] !== $_SERVER['REMOTE_ADDR']) {
    session_unset();
    session_destroy();
    header("Location: login.php?error=IP_MISMATCH_DETECTED");
    exit;
}
📜 3. Developer Verbose Logging

To see every "handshake" happening in your shop, I create a dedicated security log file. This is better than display_errors because it keeps a permanent record of attacks or failures.

Add this function to app/helpers.php:

PHP
function cyber_log(string $message, string $level = 'INFO'): void {
    $logFile = __DIR__ . '/../logs/security.log';
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'];
    
    // Format: [TIMESTAMP] [LEVEL] [IP] Message
    $formattedMessage = "[$timestamp] [$level] [$ip] $message" . PHP_EOL;
    
    // Ensure the logs directory exists
    if (!is_dir(__DIR__ . '/../logs')) {
        mkdir(__DIR__ . '/../logs');
    }

    file_put_contents($logFile, $formattedMessage, FILE_APPEND);
}

// Example usage in payment.php:
// cyber_log("Payment authorized for Order #$orderId", "SUCCESS");

📋 Implementation Checklist

MFA: Create a public/mfa_challenge.php file with a single input for the PIN.

IP Lockdown: This is active immediately once you add it to your config—it’s great for local testing.

Logs: Create a folder named logs in your root directory and make sure it is "Writable."

Would you like me to draft the mfa_challenge.php file so you can test the PIN system right away?


