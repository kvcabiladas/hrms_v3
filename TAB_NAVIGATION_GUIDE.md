# Employee Management Tab Navigation System

## Overview
The Employee Management module now supports tab-specific navigation using URL parameters. This allows you to redirect to specific tabs after performing actions like creating a department or job.

## How It Works

### URL Structure
```
/employees?tab=employees    # Employees List tab
/employees?tab=attendance   # Employee Attendance tab
/employees?tab=departments  # Departments tab
/employees?tab=jobs         # Jobs tab
```

### Automatic Tab Detection
- The system automatically reads the `tab` parameter from the URL
- If no tab parameter is present, it defaults to the "employees" tab
- When you switch tabs, the URL updates automatically (using browser history API)

## Usage Examples

### 1. Redirecting to Specific Tabs in Controllers

**Department Controller:**
```php
// After creating a department
return redirect()->route('employees.index', ['tab' => 'departments'])
    ->with('success', 'Department created successfully!');
```

**Designation Controller:**
```php
// After creating a job
return redirect()->route('employees.index', ['tab' => 'jobs'])
    ->with('success', 'Job created successfully!');
```

**Employee Controller:**
```php
// After creating an employee
return redirect()->route('employees.index', ['tab' => 'employees'])
    ->with('success', 'Employee created successfully!');
```

### 2. Creating Links to Specific Tabs

**In Blade Templates:**
```blade
<!-- Link to Departments tab -->
<a href="{{ route('employees.index', ['tab' => 'departments']) }}">
    View Departments
</a>

<!-- Link to Jobs tab -->
<a href="{{ route('employees.index', ['tab' => 'jobs']) }}">
    View Jobs
</a>

<!-- Link to Attendance tab -->
<a href="{{ route('employees.index', ['tab' => 'attendance']) }}">
    View Attendance
</a>
```

### 3. Back Button Navigation

The back button in employee creation form now redirects to the employees tab:
```blade
<a href="{{ route('employees.index', ['tab' => 'employees']) }}">
    Back to Employee List
</a>
```

## Implementation Details

### Alpine.js Integration
The tab system uses Alpine.js to:
1. Read the `tab` parameter from the URL on page load
2. Update the URL when tabs are switched (without page reload)
3. Maintain browser history for back/forward navigation

```javascript
x-data="{ 
    activeTab: new URLSearchParams(window.location.search).get('tab') || 'employees'
}"

x-init="
    $watch('activeTab', value => {
        const url = new URL(window.location);
        url.searchParams.set('tab', value);
        window.history.pushState({}, '', url);
    })
"
```

### Controller Redirects
All CRUD operations in the following controllers now redirect to their respective tabs:

- **DepartmentController** → `tab=departments`
  - `store()` - Create department
  - `update()` - Update department
  - `destroy()` - Delete department

- **DesignationController** → `tab=jobs`
  - `store()` - Create job
  - `update()` - Update job
  - `destroy()` - Delete job

- **EmployeeController** → `tab=employees`
  - `store()` - Create employee

## Benefits

1. **Better UX**: Users stay on the relevant tab after performing actions
2. **Shareable URLs**: You can share direct links to specific tabs
3. **Browser History**: Back/forward buttons work correctly with tabs
4. **No Page Reload**: Tab switching is instant (client-side)
5. **Consistent Navigation**: All related actions redirect to the correct tab

## Available Tabs

| Tab Name | URL Parameter | Description |
|----------|---------------|-------------|
| Employees List | `tab=employees` | Main employee list with search and view |
| Employee Attendance | `tab=attendance` | Attendance records with date filtering |
| Departments | `tab=departments` | Department management with CRUD |
| Jobs | `tab=jobs` | Job/designation management with CRUD |

## Example Workflow

1. User clicks "Add Department" button
2. Modal opens, user fills in department name
3. Form submits to `DepartmentController@store`
4. Controller creates department and redirects to `/employees?tab=departments`
5. Page loads with Departments tab active
6. Success message displays: "Department created successfully!"
7. User sees the new department in the list

## Notes

- The tab parameter is preserved in the URL for bookmarking
- If an invalid tab name is provided, it defaults to "employees"
- The system is extensible - you can easily add more tabs in the future
- All existing functionality remains intact
