# HRMS Database - Entity Relationship Diagram (ERD)

## Database Schema Overview

This document provides a comprehensive Entity Relationship Diagram (ERD) for the Human Resource Management System (HRMS) database.

---

## ERD Diagram (Mermaid Format)

```mermaid
erDiagram
    %% Core User & Employee Management
    users ||--o| employees : "has one"
    users ||--o{ notifications : "receives"
    
    %% Department & Designation Structure
    departments ||--o{ designations : "contains"
    departments ||--o{ employees : "has"
    departments ||--o{ job_listings : "posts"
    
    designations ||--o{ employees : "assigned to"
    designations ||--o| designation_payroll_templates : "has template"
    
    %% Employee Relationships
    employees ||--o{ attendance : "records"
    employees ||--o{ leaves : "requests"
    employees ||--o{ payrolls : "receives"
    employees ||--o{ documents : "uploads"
    employees ||--o{ onboarding_tasks : "assigned"
    employees ||--o{ leaves : "relieves (as relief officer)"
    
    %% Leave Management
    leave_types ||--o{ leaves : "categorizes"
    
    %% Recruitment
    job_listings ||--o{ candidates : "attracts"
    
    %% Payroll Components
    payroll_rules ||--o{ payrolls : "applies to"
    allowances ||--o{ payrolls : "adds to"
    deductions ||--o{ payrolls : "subtracts from"
    
    %% Company
    companies ||--o{ announcements : "publishes"

    %% Entity Definitions
    
    users {
        bigint id PK
        string name
        string email UK
        string username UK
        string password
        string temp_password
        enum role "super_admin, hr, employee, payroll_manager"
        timestamp email_verified_at
        timestamp created_at
        timestamp updated_at
    }
    
    employees {
        bigint id PK
        bigint user_id FK
        bigint department_id FK
        bigint designation_id FK
        string employee_id UK
        string first_name
        string last_name
        string email UK
        string phone
        date joining_date
        decimal hourly_rate
        enum gender "male, female, other"
        text address
        enum status "active, inactive, terminated"
        string access_code
        timestamp created_at
        timestamp updated_at
    }
    
    departments {
        bigint id PK
        string name UK
        text description
        timestamp created_at
        timestamp updated_at
    }
    
    designations {
        bigint id PK
        bigint department_id FK
        string name
        text description
        timestamp created_at
        timestamp updated_at
    }
    
    attendance {
        bigint id PK
        bigint employee_id FK
        date date
        time check_in
        time check_out
        decimal total_hours
        enum status "present, late, absent, on_leave"
        text notes
        timestamp created_at
        timestamp updated_at
    }
    
    leaves {
        bigint id PK
        bigint employee_id FK
        bigint leave_type_id FK
        bigint relief_officer_id FK
        date start_date
        date end_date
        integer total_days
        text reason
        enum status "pending, approved, rejected, cancelled"
        text admin_remarks
        date recalled_date
        timestamp created_at
        timestamp updated_at
    }
    
    leave_types {
        bigint id PK
        string name UK
        integer max_days
        boolean is_paid
        text description
        timestamp created_at
        timestamp updated_at
    }
    
    payrolls {
        bigint id PK
        bigint employee_id FK
        string month_year
        decimal hourly_rate
        decimal total_hours
        decimal base_salary
        decimal total_allowance
        decimal total_deduction
        decimal net_salary
        json allowances_breakdown
        json deductions_breakdown
        enum status "pending, paid, cancelled"
        timestamp created_at
        timestamp updated_at
    }
    
    payroll_rules {
        bigint id PK
        string rule_name UK
        enum rule_type "percentage, fixed_amount, multiplier"
        decimal value
        text description
        boolean is_active
        timestamp created_at
        timestamp updated_at
    }
    
    designation_payroll_templates {
        bigint id PK
        bigint designation_id FK UK
        decimal base_allowance
        decimal overtime_multiplier
        json benefits
        text description
        timestamp created_at
        timestamp updated_at
    }
    
    allowances {
        bigint id PK
        string name UK
        enum type "fixed, percentage"
        decimal value
        text description
        boolean is_taxable
        timestamp created_at
        timestamp updated_at
    }
    
    deductions {
        bigint id PK
        string name UK
        enum type "fixed, percentage"
        decimal value
        text description
        boolean is_mandatory
        timestamp created_at
        timestamp updated_at
    }
    
    documents {
        bigint id PK
        bigint employee_id FK
        string title
        string file_path
        string file_type
        integer file_size
        text description
        timestamp created_at
        timestamp updated_at
    }
    
    onboarding_tasks {
        bigint id PK
        bigint employee_id FK
        string task_name
        text description
        date due_date
        enum status "pending, in_progress, completed"
        timestamp completed_at
        timestamp created_at
        timestamp updated_at
    }
    
    job_listings {
        bigint id PK
        bigint department_id FK
        string title
        text description
        text requirements
        text responsibilities
        enum employment_type "full_time, part_time, contract"
        decimal salary_min
        decimal salary_max
        date application_deadline
        enum status "open, closed, filled"
        timestamp created_at
        timestamp updated_at
    }
    
    candidates {
        bigint id PK
        bigint job_listing_id FK
        string first_name
        string last_name
        string email UK
        string phone
        string resume_path
        text cover_letter
        enum status "applied, screening, interview, offered, rejected, hired"
        timestamp created_at
        timestamp updated_at
    }
    
    notifications {
        bigint id PK
        bigint user_id FK
        string title
        text message
        enum type "info, success, warning, error"
        boolean read
        timestamp read_at
        timestamp created_at
        timestamp updated_at
    }
    
    announcements {
        bigint id PK
        string title
        text content
        enum priority "low, normal, high, urgent"
        date publish_date
        date expiry_date
        boolean is_active
        timestamp created_at
        timestamp updated_at
    }
    
    companies {
        bigint id PK
        string name
        string email
        string phone
        text address
        string logo_path
        string website
        text description
        timestamp created_at
        timestamp updated_at
    }
```

---

## Table Relationships Summary

### **Core Entities:**

1. **users** (Authentication & Authorization)
   - One-to-One with `employees`
   - One-to-Many with `notifications`

2. **employees** (Employee Profiles)
   - Belongs to `users`, `departments`, `designations`
   - Has many `attendance`, `leaves`, `payrolls`, `documents`, `onboarding_tasks`
   - Can be a relief officer for `leaves`

3. **departments** (Organizational Structure)
   - Has many `designations`, `employees`, `job_listings`

4. **designations** (Job Positions)
   - Belongs to `departments`
   - Has many `employees`
   - Has one `designation_payroll_templates`

### **Attendance & Leave Management:**

5. **attendance** (Daily Attendance Records)
   - Belongs to `employees`

6. **leaves** (Leave Requests)
   - Belongs to `employees`, `leave_types`
   - Has relief officer (`employees`)

7. **leave_types** (Leave Categories)
   - Has many `leaves`

### **Payroll Management:**

8. **payrolls** (Salary Records)
   - Belongs to `employees`
   - Uses `payroll_rules`, `allowances`, `deductions`

9. **payroll_rules** (Calculation Rules)
   - Applied to `payrolls`

10. **designation_payroll_templates** (Position-based Templates)
    - Belongs to `designations`

11. **allowances** (Salary Additions)
    - Applied to `payrolls`

12. **deductions** (Salary Subtractions)
    - Applied to `payrolls`

### **Recruitment:**

13. **job_listings** (Job Postings)
    - Belongs to `departments`
    - Has many `candidates`

14. **candidates** (Job Applicants)
    - Belongs to `job_listings`

### **Document & Onboarding:**

15. **documents** (Employee Files)
    - Belongs to `employees`

16. **onboarding_tasks** (New Hire Tasks)
    - Belongs to `employees`

### **Communication:**

17. **notifications** (User Alerts)
    - Belongs to `users`

18. **announcements** (Company-wide Messages)
    - Belongs to `companies`

19. **companies** (Organization Info)
    - Has many `announcements`

---

## Key Relationships Explained

### **1. User → Employee (1:1)**
- Each user account can have one employee profile
- Employees must have a user account for system access

### **2. Department → Designation → Employee (Hierarchy)**
- Departments contain multiple designations
- Designations belong to departments
- Employees are assigned to both department and designation

### **3. Employee → Payroll (1:N)**
- Each employee can have multiple payroll records (monthly)
- Payroll uses designation templates for base calculations

### **4. Employee → Attendance (1:N)**
- Daily attendance records for each employee
- Used to calculate total hours for payroll

### **5. Employee → Leave (1:N with self-reference)**
- Employees request leaves
- Employees can also be relief officers for other employees' leaves

### **6. Payroll Calculation Chain:**
```
Employee → Attendance (hours) 
        → Hourly Rate 
        → Designation Template (allowances, multipliers)
        → Payroll Rules (deductions)
        → Final Payroll Record
```

---

## Database Statistics

- **Total Tables:** 19
- **Core Entities:** 4 (users, employees, departments, designations)
- **Transactional Tables:** 6 (attendance, leaves, payrolls, documents, onboarding_tasks, notifications)
- **Configuration Tables:** 5 (leave_types, payroll_rules, allowances, deductions, designation_payroll_templates)
- **Recruitment Tables:** 2 (job_listings, candidates)
- **System Tables:** 2 (companies, announcements)

---

## Indexing Recommendations

For optimal performance, the following indexes are recommended:

1. **Foreign Keys:** All FK columns should be indexed
2. **Unique Constraints:** 
   - `users.email`, `users.username`
   - `employees.employee_id`, `employees.email`
   - `departments.name`
   - `leave_types.name`
   - `payroll_rules.rule_name`
   
3. **Frequently Queried Columns:**
   - `attendance.date`, `attendance.employee_id`
   - `leaves.status`, `leaves.start_date`, `leaves.end_date`
   - `payrolls.month_year`, `payrolls.status`
   - `employees.status`

---

## Data Integrity Rules

1. **Cascade Deletes:**
   - Deleting an employee cascades to: attendance, leaves, documents, onboarding_tasks
   - Payroll records are preserved for audit purposes

2. **Required Relationships:**
   - Employees must have: user, department, designation
   - Leaves must have: employee, leave_type
   - Payrolls must have: employee

3. **Enum Validations:**
   - User roles: super_admin, hr, employee, payroll_manager
   - Employee status: active, inactive, terminated
   - Leave status: pending, approved, rejected, cancelled
   - Payroll status: pending, paid, cancelled

---

**Generated:** December 9, 2025  
**System:** HRMS v3  
**Total Entities:** 19 tables
