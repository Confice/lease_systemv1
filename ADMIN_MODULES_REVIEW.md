# Admin Navbar Modules - Complete Review

## Overview
This document provides a comprehensive explanation of each module in the admin navbar, detailing what each page does, its functionality, and key features.

---

## 📊 1. DASHBOARD

**Route:** `/admins/dashboard`  
**Controller:** `DashboardController@adminIndex`  
**View:** `resources/views/admins/dashboard.blade.php`

### What It Does:
The Dashboard is the main landing page for administrators after login. It provides an overview of key system metrics and statistics.

### Key Features:
- **Real-time Statistics Widgets:**
  - **Active Tenants** - Count of tenants with active contracts
  - **Vacant Stalls** - Number of stalls available for rent
  - **Expiring Contracts** - Contracts expiring within 30 days
  - **Rent Collected** - Total rent collected in the current month

- **Dynamic Data Loading:**
  - Statistics are loaded via AJAX from `/admins/dashboard/stats`
  - Updates automatically when data changes
  - Shows "-" placeholder while loading

- **Quick Navigation:**
  - Each widget card is clickable and links to relevant modules
  - Provides quick access to related pages

### Purpose:
Gives administrators a quick overview of the system's current state, helping them identify:
- How many active tenants are operating
- Available stalls for new tenants
- Contracts that need attention (expiring soon)
- Revenue performance for the month

---

## 🗺️ 2. MARKETPLACE MAP

**Route:** `/admins/marketplace`  
**Controller:** `MarketplaceMapController@index`  
**View:** `resources/views/admins/marketplace/index.blade.php`

### What It Does:
Displays an interactive map/visualization of all marketplaces and their stalls. Allows admins to view the physical layout and status of stalls.

### Key Features:
- **Marketplace Overview:**
  - Shows all marketplaces in the system
  - Visual representation of stall locations
  - Different views for different marketplaces (Hub, Bazaar, etc.)

- **Stall Status Visualization:**
  - Visual indicators for stall status (Vacant/Occupied)
  - Clickable stalls to view details
  - Filter by marketplace

- **Navigation:**
  - Tabs or links to switch between different marketplaces
  - Links to specific marketplace views (Hub, Bazaar)

### Purpose:
Helps administrators:
- Visualize the physical layout of marketplaces
- Quickly identify which stalls are available
- Understand the distribution of occupied vs. vacant stalls
- Plan stall assignments based on location

---

## 📄 3. LEASES

**Route:** `/admins/leases`  
**Controller:** `ContractController@leasesIndex`  
**View:** `resources/views/admins/leases/index.blade.php`

### What It Does:
Comprehensive lease/contract management system for viewing, managing, renewing, and terminating tenant contracts.

### Key Features:
- **Contract Listing:**
  - DataTable showing all contracts with:
    - Contract ID
    - Tenant name
    - Stall information
    - Marketplace
    - Monthly rent
    - Start/End dates
    - Days remaining
    - Contract status (Active, Expiring, Terminated)

- **Search & Filter:**
  - Search by contract ID, tenant name, or stall
  - Filter by status (All, Active, Expiring, Terminated)

- **Contract Actions:**
  - **View Details** - Opens offcanvas drawer showing:
    - Contract information
    - Tenant details (name, email, contact)
    - Stall information (number, marketplace, rent)
    - Related records (bills count, feedback count)
  
  - **Renew Contract** - Extend contract duration:
    - Select number of months (1-12)
    - Automatically generates bills for renewal period
    - Updates contract end date
  
  - **Terminate Contract** - End contract early:
    - Requires termination reason
    - Sets contract status to "Terminated"
    - Sets stall status back to "Vacant"
    - Sets contract end date to current date

- **Responsive Design:**
  - Adapts to different screen sizes
  - Hides less important columns on smaller screens
  - Maintains functionality on mobile devices

### Purpose:
Central hub for managing all tenant contracts, allowing administrators to:
- Monitor active leases
- Identify contracts expiring soon
- Renew contracts when tenants want to extend
- Terminate contracts when needed
- Track contract history and related records

---

## 👥 4. PROSPECTIVE TENANTS

**Route:** `/tenants/prospective`  
**Controller:** `ContractController@index`  
**View:** `resources/views/admins/prospective_tenants/index.blade.php`

### What It Does:
Manages tenant applications for vacant stalls. Shows stalls that are available for rent and the applications submitted by prospective tenants.

### Key Features:
- **Vacant Stalls Listing:**
  - Shows only stalls with:
    - Status: "Vacant"
    - Application deadline set (not null)
    - Deadline not yet passed
  - Displays:
    - Stall number and location
    - Marketplace
    - Size and rental fee
    - Application deadline
    - Number of applications received

- **Application Management:**
  - View applications for each stall
  - See application details:
    - Tenant information
    - Application status
    - Date applied
    - Documents submitted
  - Schedule presentations/interviews
  - Review tenant proposals

- **Actions:**
  - **View Applications** - See all applications for a specific stall
  - **Application Details** - View full application with documents
  - **Schedule Presentation** - Set date/time for tenant presentation
  - **Assign Tenant** - Approve application and create contract

### Purpose:
Streamlines the tenant application process by:
- Centralizing all applications in one place
- Making it easy to compare applicants for the same stall
- Managing the application review process
- Facilitating the transition from application to active contract

---

## 🏪 5. STALLS

**Route:** `/stalls`  
**Controller:** `StallController@index`  
**View:** `resources/views/admins/stalls/index.blade.php`

### What It Does:
Complete stall management system for creating, editing, viewing, and managing all stalls in the system.

### Key Features:
- **Stall Listing:**
  - DataTable showing all stalls with:
    - Stall number
    - Marketplace
    - Size
    - Rental fee
    - Status (Vacant/Occupied)
    - Current tenant (if occupied)
    - Application deadline

- **Stall Management:**
  - **Create New Stall:**
    - Stall number
    - Marketplace selection
    - Size
    - Rental fee
    - Initial status
    - Application deadline (optional)
  
  - **Edit Stall:**
    - Update all stall properties
    - Change status
    - Modify rental fee
    - Update application deadline
  
  - **Assign Tenant:**
    - Select tenant from active tenants list
    - Automatically creates contract
    - Updates stall status to "Occupied"
  
  - **View Stall Details:**
    - Full stall information
    - Current contract (if any)
    - Rental history
    - Related bills

- **Requirements Management:**
  - Set requirements for stall applications
  - Define required documents
  - Manage application prerequisites

- **Bulk Operations:**
  - Archive multiple stalls at once
  - Export to CSV
  - Print stall list

- **Search & Filter:**
  - Search by stall number, marketplace, or tenant
  - Filter by status
  - Filter by marketplace

### Purpose:
Core module for managing the physical assets (stalls) of the business:
- Maintain inventory of all stalls
- Set rental rates
- Track which stalls are available
- Manage stall assignments
- Define application requirements

---

## 💳 6. BILLS

**Route:** `/admins/bills`  
**Controller:** `BillController@adminIndex`  
**View:** `resources/views/admins/bills/index.blade.php`

### What It Does:
Comprehensive billing system for managing all bills, payment tracking, and revenue collection.

### Key Features:
- **Bill Listing:**
  - DataTable showing all bills with:
    - Bill ID
    - Tenant name
    - Stall information
    - Amount
    - Due date
    - Date paid (if paid)
    - Status (Pending, Paid, Invalid, Due)
    - Payment proof indicator

- **Bill Management:**
  - **View Payment Proof:**
    - Opens modal to view uploaded payment proof
    - Supports PDF, JPG, PNG files
    - Shows upload date
  
  - **Update Bill Status:**
    - Change status to: Paid, Invalid, Pending, or Due
    - When marking as "Paid":
      - Automatically sets `datePaid` to current timestamp
    - When marking as "Invalid":
      - Clears `datePaid` field
    - Validates status transitions
  
  - **Generate Monthly Bills:**
    - Manual trigger to generate bills for all active contracts
    - Creates bills for the next billing cycle
    - Calculates due dates based on contract terms
    - Prevents duplicate bills

- **Search & Filter:**
  - Search by bill ID, tenant name, or stall number
  - Filter by status (All, Pending, Paid, Invalid, Due)

- **Revenue Tracking:**
  - View total revenue collected
  - Track pending payments
  - Monitor overdue bills

### Purpose:
Manages the financial aspect of the lease system:
- Track all bills and payments
- Verify payment proofs
- Generate recurring monthly bills
- Monitor revenue collection
- Identify overdue payments

---

## 💬 7. TENANT FEEDBACK

**Route:** `/tenant-feedback`  
**Controller:** `FeedbackController@adminIndex`  
**View:** `resources/views/admins/feedback_form/index.blade.php`

### What It Does:
Displays and manages feedback submitted by tenants about their renting experience and system usage.

### Key Features:
- **Feedback Listing:**
  - DataTable showing all tenant feedback with:
    - Tenant name
    - Submission date
    - Average ratings (by category)
    - Comments preview
    - Archive status

- **Feedback Categories:**
  - **Marketplace & Stall Experience:**
    - Stall location and accessibility
    - Marketplace facilities
    - Overall renting experience
  
  - **Lease Operations & Support:**
    - Application process
    - Contract management
    - Support responsiveness
  
  - **System Experience:**
    - System usability
    - Interface design
    - Functionality

- **Feedback Actions:**
  - **View Details:**
    - Opens offcanvas drawer showing:
      - Tenant information (name, contact)
      - Submission date
      - Contract/stall details
      - All individual ratings (1-5 scale)
      - Full comments
  
  - **Archive Feedback:**
    - Soft archive (sets `archived_at` timestamp)
    - Removes from main view
    - Can be restored from Archived Items

- **Search & Filter:**
  - Search by tenant name or comments
  - Filter archived/unarchived

### Purpose:
Collects and analyzes tenant feedback to:
- Improve service quality
- Identify system issues
- Understand tenant satisfaction
- Make data-driven improvements
- Track feedback trends over time

---

## 📈 8. ANALYTICS

**Route:** `/analytics`  
**Status:** ⚠️ **Not Yet Implemented**

### What It Should Do:
Based on the navbar link, this module should provide:
- Revenue analytics and trends
- Occupancy rates
- Tenant retention statistics
- Payment patterns
- Contract duration analysis
- Feedback analytics

### Current Status:
The route exists in the navbar but the page/controller is not yet implemented. This is a planned feature.

---

## 👤 9. USERS

**Route:** `/users`  
**Controller:** `UserController@index`  
**View:** `resources/views/admins/users/index.blade.php`

### What It Does:
Complete user management system for creating, editing, activating, and deactivating user accounts (both Lease Managers and Tenants).

### Key Features:
- **User Listing:**
  - DataTable showing all users with:
    - User ID
    - Full name
    - Email
    - Role (Lease Manager/Tenant)
    - Account status (Active, Pending, Deactivated)
    - Creation date

- **User Management:**
  - **Create New User:**
    - First name, middle name, last name
    - Email (unique)
    - Role selection
    - Contact number
    - Home address
    - Birth date (must be 18+)
    - Creates account with "Pending" status
    - Sends activation email
  
  - **Edit User:**
    - Update all user information
    - Change role
    - Update account status
    - Add deactivation reason (if deactivating)
  
  - **View User Details:**
    - Full user profile
    - Account status history
    - Related contracts
    - Activity history
  
  - **Deactivate User:**
    - Requires reason for deactivation
    - Prevents login
    - Maintains data integrity
  
  - **Reset Password:**
    - Sends password reset email
    - Allows user to set new password

- **Bulk Operations:**
  - Archive multiple users at once
  - Export to CSV
  - Print user list

- **Search & Filter:**
  - Search by name or email
  - Filter by role
  - Filter by status

### Purpose:
Manages all user accounts in the system:
- Create accounts for new tenants or staff
- Control access and permissions
- Monitor account status
- Maintain user database
- Handle account lifecycle

---

## 📋 10. ACTIVITY LOGS

**Route:** `/activity-logs`  
**Status:** ⚠️ **Not Yet Implemented**

### What It Should Do:
Based on the ActivityLog model and logging system, this module should display:
- All system activities
- User actions (login, logout, CRUD operations)
- Timestamps and user information
- Filterable by user, action type, date range
- Searchable log entries

### Current Status:
The route exists in the navbar but the page/controller is not yet implemented. However, activity logging is already implemented in the backend (ActivityLogService), so this is ready to be built.

---

## 📦 11. ARCHIVED ITEMS

**Route:** `/archived-items`  
**Controller:** `ArchivedItemsController@index`  
**View:** `resources/views/admins/archived_items/index.blade.php`

### What It Does:
Centralized location to view and restore all archived items from different modules (Users, Stalls, Feedback).

### Key Features:
- **Unified Archive View:**
  - Shows archived items from:
    - **Users** - Soft-deleted user accounts
    - **Stalls** - Soft-deleted stalls
    - **Tenant Feedback** - Archived feedback entries
  
  - Displays:
    - Reference ID (formatted)
    - Source module
    - Archive date
    - Item type

- **Restore Functionality:**
  - Restore individual items
  - Bulk restore multiple items
  - Restores to original module
  - Maintains data integrity

- **Search & Filter:**
  - Search by reference ID
  - Filter by module type
  - Filter by archive date

### Purpose:
Provides a safety net for accidentally deleted items:
- Recover deleted users
- Restore archived stalls
- Unarchive feedback
- Maintain data retention
- Audit trail of deletions

---

## 📊 SUMMARY TABLE

| Module | Status | Primary Function | Key Actions |
|--------|--------|------------------|-------------|
| **Dashboard** | ✅ Active | Overview & Statistics | View metrics, Quick navigation |
| **Marketplace Map** | ✅ Active | Visual stall layout | View maps, Check availability |
| **Leases** | ✅ Active | Contract management | View, Renew, Terminate contracts |
| **Prospective Tenants** | ✅ Active | Application management | Review apps, Schedule presentations |
| **Stalls** | ✅ Active | Stall management | Create, Edit, Assign tenants |
| **Bills** | ✅ Active | Billing & payments | Generate bills, Update status, View proofs |
| **Tenant Feedback** | ✅ Active | Feedback management | View feedback, Archive |
| **Analytics** | ⚠️ Planned | Analytics & reports | (Not yet implemented) |
| **Users** | ✅ Active | User management | Create, Edit, Deactivate users |
| **Activity Logs** | ⚠️ Planned | Audit trail | (Not yet implemented) |
| **Archived Items** | ✅ Active | Archive management | View, Restore archived items |

---

## 🔗 MODULE INTERCONNECTIONS

The modules work together in a workflow:

1. **Users** → Create tenant accounts
2. **Stalls** → Create available stalls
3. **Prospective Tenants** → Tenants apply for stalls
4. **Leases** → Approve applications, create contracts
5. **Bills** → Generate monthly bills for active contracts
6. **Tenant Feedback** → Collect tenant satisfaction data
7. **Archived Items** → Manage deleted/archived records

---

## 📝 NOTES

- All active modules have full CRUD (Create, Read, Update, Delete) functionality
- Most modules use DataTables for interactive tables
- Search and filter functionality is consistent across modules
- Responsive design ensures mobile compatibility
- Activity logging is implemented but the Activity Logs view page is pending
- Analytics module is planned but not yet implemented

---

**Last Updated:** {{ date('Y-m-d H:i:s') }}

