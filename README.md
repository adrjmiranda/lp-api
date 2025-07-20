# LP API

**Version:** 1.0.0-beta
**Author:** [Adriano Miranda](https://github.com/adrjmiranda)
**License:** Proprietary

---

## 📦 Overview

**LP API** is a collection of reusable PHP modules focused on common landing page functionality, such as form submission, reCAPTCHA validation, Latte templates, Monolog logging, and more.

This project is in the **beta** phase and is intended for use in web applications that require a lightweight, flexible, and secure framework for backend APIs with modern PHP.

---

## ⚙️ Technologies Used

- **Slim Framework** (v4) – Microframework for creating APIs
- **Latte** – Modern and secure templating engine
- **PHP-DI** – Dependency injection
- **Monolog** – Logging system
- **PHPMailer** – Sending emails via SMTP
- **DotEnv** – Environment variable management
- **Symfony VarDumper** (dev) – Variable debugging
- **Respect Validation** Data validation

---

## 📁 Project Structure

- index.php # Main file
- .env # Environment variables
- logs/ # Log files
- temp/cache/ # Cache files templates
- templates/ # Latte templates
- src/
  - controllers/ # Application controllers
  - services/ # Reusable services
- helpers.php # Utility functions
  - composer.json # Project configuration

---

## 🚀 How to Run the Project

1. Clone the repository:

```bash
git clone https://github.com/adrjmiranda/lp-api.git
cd lp-api
```

2. Install the dependencies:

```bash
composer install
```

3. Configure the .env file with your variables (example included).

4. Start the local server:

```bash
composer serve
```

5. Access via browser:

```
http://localhost:8000
```

## 🧪 Example

Here's a basic usage example to illustrate how LP API works in a real scenario:

```php
<?php

use LpApi\Helpers\App;
use LpApi\Validation\DefaultMailerValidator;
use Respect\Validation\Validator as v;

require_once __DIR__ . "/bootstrap.php";

// Add validation rules for the mailer
DefaultMailerValidator::add("name", fn(mixed $input): bool =>
    v::stringType()->notEmpty()->validate($input), "Name is required"
);

DefaultMailerValidator::add("email", fn(mixed $input): bool =>
    v::email()->validate($input), "Invalid email"
);

// Load routes
require_once App::rootPath() . "/src/routes/mailer.php";

// Run the application
$app->run();
```

### ✅ What this example does:

- Loads application bootstrap

- Adds custom validation rules for name and email

- Loads the mailer routes

- Starts the Slim application

You can now send a `POST` request to `/mailer/send` with a JSON payload like:

```json
{
	"name": "John Doe",
	"email": "john@example.com",
	"message": "Hello, I would like to get in touch."
}
```

If validation passes, the email will be sent using PHPMailer, and logs will be recorded via Monolog.

## 🛡️ Security

- Google reCAPTCHA v3 support
- Data sanitization and validation
- Clear separation of responsibilities (controllers/services/helpers)

## 📮 Current Features

- Sending emails with PHPMailer
- ReCAPTCHA v3 support
- Templates with Latte
- Logs with Monolog
- Debugging with Symfony VarDumper and Whoops
- Autoloading via PSR-4

## 🔖 Versioning

This project follows [SemVer Versioning](https://semver.org/).
Current version: 1.0.0-beta

## 📫 Contact

[Adriano Miranda](https://github.com/adrjmiranda)
📧 adrjmiranda@gmail.com

## 📝 License

This project is proprietary and its use is restricted. Contact the author for more information.
