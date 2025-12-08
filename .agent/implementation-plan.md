# HRMS v3 Major Refactoring - Implementation Plan

## Phase 1: UI/UX Improvements ✅
- [x] Add back buttons to modules/pages (already exists in create employee)
- [ ] Make success message banners auto-disappear after few seconds (already implemented in hrms.blade.php)
- [ ] Add back buttons to other create/edit pages where missing

## Phase 2: Settings Module Enhancement
### Profile Section
- [ ] Change personal details (already exists)
- [ ] Add emergency contacts section
- [ ] Change password (already exists)

## Phase 3: HR Role Menu Restructuring
### PERSONAL Menu
- [ ] My Attendance
  - [ ] Show only user's own attendance
  - [ ] Fix total hours/duration timezone conflict
- [ ] My Leaves
  - [ ] Show only user's leave history
  - [ ] Add "Request Leave" button
- [ ] My Payroll
  - [ ] List of months/payment dates with date covered
  - [ ] View payslip action
  - [ ] Payslip detail page with breakdown
  - [ ] Downloadable payslip

### COMPANY Menu (HR)
- [ ] Dashboard
  - [ ] Keep current analytics
- [ ] Employees
  - [ ] Add sort by functionality
  - [ ] Remove status column (and from DB if not needed)
  - [ ] Enhanced employee profile view:
    - [ ] Full-width banner with picture, name, emp ID, department, email
    - [ ] Login credentials section at bottom
    - [ ] Recent attendance with date range selector
- [ ] Employee Attendance
  - [ ] Weekly attendance chart
  - [ ] List of present employees
- [ ] Leave Management
  - [ ] Improve UI to match Figma
  - [ ] Remove "Relief Officer" button from module
  - [ ] Allow all employees (except superadmin) as relief officers
  - [ ] Implement leave recall feature

## Phase 4: Accountant/Payroll Manager Role
### COMPANY Menu
- [ ] Dashboard
  - [ ] Payroll-focused analytics
- [ ] Payroll Management
  - [ ] Employee list view
  - [ ] View employee shows pay history
  - [ ] Basic salary input in employee view
  - [ ] Global payroll rules
  - [ ] Job designation-specific templates (e.g., manager allowances)

### PERSONAL Menu
- [ ] My Attendance (same as all roles)
- [ ] My Leaves (same as all roles)
- [ ] My Payroll (same as all roles)

## Phase 5: Employee Role Simplification
- [ ] Dashboard
- [ ] My Attendance
- [ ] My Leaves
- [ ] My Payroll

## Phase 6: Super Admin Updates
- [ ] Dashboard
  - [ ] Show HR personnel list
  - [ ] Add "Create HR Personnel" button
- [ ] Remove "Create HR" from sidebar (move to dashboard)

## Database Changes Required
- [ ] Add emergency_contacts table/fields
- [ ] Consider removing status column from employees table
- [ ] Add payroll rules and templates tables
- [ ] Add relief_officer_id to leaves table (if not exists)
- [ ] Add recalled_date to leaves table

## Controller Updates Required
- [ ] AttendanceController - filter by user for personal view
- [ ] LeaveController - filter by user, add recall functionality
- [ ] PayrollController - add payslip view, download functionality
- [ ] EmployeeController - enhanced show view, sorting
- [ ] New: PayrollRulesController
- [ ] SettingsController - add emergency contacts

## Routes to Add
- [ ] Personal attendance route
- [ ] Personal leaves route
- [ ] Personal payroll route
- [ ] Payslip view route
- [ ] Payslip download route
- [ ] Leave recall route
- [ ] Emergency contacts routes

## Views to Create/Modify
- [ ] personal/attendance.blade.php
- [ ] personal/leaves.blade.php
- [ ] personal/payroll.blade.php
- [ ] payroll/payslip.blade.php
- [ ] employees/show.blade.php (enhanced)
- [ ] settings/emergency-contacts.blade.php
- [ ] hr/employee-attendance.blade.php
- [ ] Modify layouts/hrms.blade.php for new menu structure
