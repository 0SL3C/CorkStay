# CorkStay - Property Rental Platform

## Setup Instructions

### Environment Variables Setup

1. Copy the example environment file:
   ```bash
   cp .env.example .env
   ```

2. Edit the `.env` file with your actual database credentials:
   ```
   DB_HOST=your_database_host
   DB_USERNAME=your_database_username
   DB_PASSWORD=your_database_password
   DB_NAME=your_database_name
   SESSION_TIMEOUT=3600
   ```

3. Ensure the `.env` file is not committed to version control (it's already in `.gitignore`).

### Database Setup

Make sure your MySQL database is running and accessible with the credentials specified in your `.env` file.

## Project Tasks

| Task | Description | Priority | Status |
|------|-------------|----------|--------|
| **Database Setup** | | | |
| Design and create MySQL database schema | Tables: Users, Properties, Testimonials; Ensure proper relationships (e.g., foreign keys for landlord properties) | **High** | ❎ |
| Export database dump file | e.g., `s3000001.sql` | **High** | ❎ |
| Implement prepared statements for secure queries | Use prepared statements for all MySQL queries | **High** | ❎ |
| **User Authentication** | | | |
| Session management | Use `session_start()` in all scripts requiring authentication | **High** | ❎ |
| Signup page | Secure form handling with server-side validation and sanitization; Store user data in MySQL with hashed passwords | **High** | ❎ |
| Login page | Authenticate users and set session variables | **High** | ❎ |
| Logout functionality | Destroy session and redirect to `home.php` with confirmation message | **High** | ❎ |
| Password reset mechanism | Secure password reset with email verification or security questions | Medium | ❎ |
| **Core Pages** | | | |
| Home page (`home.php`) | Links/buttons to signup/sign in; CorkStay agency information and 3 feature boxes showcasing new properties (title, detail, image, optional link) | **High** | ❎ |
| Search page | Search bar for properties by rental price, bedrooms, and tenancy length; Display results dynamically from MySQL | **High** | ❎ |
| Property Listing (Landlord-only) | CRUD operations for properties (1 bed, 2 bed, 3 bed, 4 bed); Options for tenancy Shia length (3-month, 6-month, 1-year) and rental price; Restrict access to authenticated landlords | **High** | ❎ |
| Testimonial page | Display list of tenant testimonials (service name, date, tenant’s first name, comment) | Medium | ❎ |
| Testimonial Add (Tenant-only) | Form for logged-in tenants to submit testimonials; Restrict access to authenticated tenants | Medium | ❎ |
| **Frontend Design** | | | |
| Create professional CSS styles | Responsive navigation, layout, and imagery; Consistent branding for CorkStay | Medium | ❎ |
| Validate HTML and CSS | Ensure cross-browser compatibility | Medium | ❎ |
| Add comments to HTML/CSS | Explain structure and styling decisions | Low | ❎ |
| **Code Quality & Security** | | | |
| Comment PHP and MySQL code | Clearly explain logic and functionality | Medium | ❎ |
| Implement server-side validation for all forms | Sanitize inputs to prevent XSS and SQL injection | **High** | ❎ |
| Debug and test application | Ensure all features work as expected | Medium | ❎ |
| **Deliverables Preparation** | | | |
| Complete SWD_project PDF | Use Moodle Word template and export as PDF | Low | ❎ |
| Prepare PowerPoint presentation | Use Moodle template, export as PDF | Low | ❎ |
| Create zipped submission file | Include web application folder, database dump, and documentation | Low | ❎ |

separate this table into more tables and put the % of marks for each