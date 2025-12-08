# HRMS v3 - Phase 2 Implementation Plan

## Priority 1: Critical Fixes (Immediate)
1. ✅ Fix payroll payment_date error → use created_at or month_year
2. Fix employee create page (blank page error)
3. Fix case-sensitive login
4. Remove Settings from sidebar for all users

## Priority 2: UI/UX Improvements
1. Change color palette to green/white theme (especially super admin purple → green)
2. Floating toast notifications (upper-right) for success messages
3. Phone number input validation (numbers only)
4. Address input as textarea (non-resizable, scrollable)
5. Modal exit confirmation (if has input)
6. Change dashboard icon to 4-squares grid
7. Move Company modules before Personal in sidebar (HR & Accountant)

## Priority 3: Employee Profile Enhancement
1. Add tabs: Personal Info, Financial Details, Emergency Contacts
2. Add profile picture upload
3. Make all sections editable

## Priority 4: Super Admin Improvements
1. HR Personnel Management:
   - Add back button to create HR page
   - Remove banner from create HR page
   - Action column: View, Edit, Delete
   - Delete confirmation (type "delete" + email)
2. Remove Company Settings

## Priority 5: HR Module Improvements
1. Dashboard:
   - Fix pending leave requests UI (show name, type, view action only)
   - Fix recruitment chart logic (use joining_date not created_at)
   - Add date/time display
   - Fix card layouts (icon left, number right-justified)
   - Total Staff → Total Employees (all roles)
2. Manage Employees:
   - Add tabs: Employees List, Employee Attendance, Departments, Jobs
   - Remove status column
   - Make search bar functional
   - Employee view/edit/delete with confirmation
3. Leave Management:
   - Leave History: Remove reason column, add view action
   - Leave Recall: Implement Figma design
   - Remove Relief Officers tab
4. Remove Employee Attendance from sidebar (move to tab)

## Priority 6: Charts & Exports
1. Install Chart.js locally (not CDN)
2. Change bar chart to line chart for attendance
3. Add PDF generation for payslips (DomPDF)
4. Add CSV/Excel export functionality

## Priority 7: Notifications System
1. Create notifications table
2. Implement notifications for:
   - Leave approved/rejected
   - Leave recalled
   - Chosen as relief officer
   - Payroll posted
3. Add notification bell icon
4. Mark as read functionality

## Files to Create
- Notification migration & model
- Profile picture upload handling
- Department & Job CRUD controllers
- Toast notification component
- PDF payslip template

## Files to Modify
- All blade layouts (color scheme)
- Flash message system (toast)
- Login controller (case-insensitive)
- Employee controller (tabs, search)
- Leave controller (recall modal)
- Dashboard controllers (all roles)
- Sidebar navigation
- Form inputs (phone, address validation)

## Database Changes Needed
- notifications table
- profile_picture column in employees
- Remove status column from employees (if not used)
