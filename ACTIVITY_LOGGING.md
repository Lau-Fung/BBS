# Activity Logging Implementation

This document describes the comprehensive activity logging system implemented in the Laravel BBS application.

## Overview

The activity logging system tracks all user activities including:
- **Create/Update/Delete** operations on all models
- **Login/Logout** events
- **Import/Export** operations
- **Bulk operations** (e.g., bulk updates)

## Implementation Details

### 1. Core Components

#### Trait: `App\Traits\LogsActivity`
- Custom trait that wraps Spatie Activity Log
- Provides consistent logging configuration across all models
- Automatically logs fillable fields with dirty tracking
- Generates human-readable descriptions for events

#### Service: `App\Services\ActivityLogService`
- Centralized service for logging specific activities
- Methods for import/export logging
- Bulk operation logging
- Activity statistics and filtering

#### Listener: `App\Listeners\LogAuthenticationActivity`
- Handles authentication events (login, logout, failed attempts)
- Logs IP addresses and user agents
- Tracks login attempts and failures

### 2. Model Integration

All major models now use the `LogsActivity` trait:
- `Client`
- `ClientSheetRow`
- `Assignment`
- `Vehicle`
- `Device`
- `Sim`
- `Sensor`
- `Carrier`

### 3. Activity Types Logged

#### Model Operations
- **Created**: When new records are created
- **Updated**: When existing records are modified
- **Deleted**: When records are soft deleted
- **Restored**: When soft deleted records are restored

#### Authentication Events
- **Login**: Successful user logins with IP and user agent
- **Logout**: User logout events
- **Failed Login**: Failed login attempts with email and IP

#### Import/Export Operations
- **Import**: Excel file imports with record counts and metadata
- **Export**: Data exports with format and filter information
- **Bulk Operations**: Mass updates with record counts

### 4. Database Schema

The `activity_log` table stores:
- `log_name`: Category of the activity
- `description`: Human-readable description
- `subject_type`: Model class that was affected
- `subject_id`: ID of the affected record
- `causer_type`: User model class
- `causer_id`: ID of the user who performed the action
- `event`: Type of event (created, updated, deleted, etc.)
- `properties`: JSON data with additional context
- `created_at`: Timestamp of the activity

### 5. User Interface

#### Activity Logs Index (`/activity-logs`)
- Statistics dashboard with activity counts
- Filterable table of all activities
- Search functionality
- Pagination support

#### Activity Log Details (`/activity-logs/{id}`)
- Detailed view of individual activities
- Property changes for update events
- User and subject information
- JSON property viewer

### 6. Permissions

- `activity_logs.view`: Required to view activity logs
- Assigned to Admin and Manager roles by default

### 7. Usage Examples

#### Logging Import Activity
```php
ActivityLogService::logImport('assignments', $recordCount, $fileName, [
    'created' => $created,
    'updated' => $updated,
    'sheets_processed' => $sheetCount,
]);
```

#### Logging Export Activity
```php
ActivityLogService::logExport('clients', 'xlsx', $recordCount, $filters);
```

#### Logging Bulk Operations
```php
ActivityLogService::logBulkOperation('update', 'ClientSheetRow', $count, [
    'client_id' => $client->id,
    'client_name' => $client->name,
]);
```

### 8. Configuration

The activity logging is configured in the `LogsActivity` trait with:
- **Log Fillable**: Only logs fillable fields
- **Log Only Dirty**: Only logs changed fields
- **Don't Submit Empty Logs**: Skips logs with no changes
- **Custom Log Names**: Uses model class names as log names
- **Event Descriptions**: Generates human-readable descriptions

### 9. Performance Considerations

- Indexes on frequently queried columns
- Pagination for large activity logs
- Optional cleanup of old logs (not implemented)
- Efficient queries with proper relationships

### 10. Security

- IP address tracking for all activities
- User agent logging
- Permission-based access control
- No sensitive data in logs (passwords, etc.)

## Files Created/Modified

### New Files
- `app/Traits/LogsActivity.php`
- `app/Services/ActivityLogService.php`
- `app/Listeners/LogAuthenticationActivity.php`
- `app/Http/Controllers/ActivityLogController.php`
- `app/Policies/ActivityLogPolicy.php`
- `app/Providers/EventServiceProvider.php`
- `database/seeders/ActivityLogPermissionSeeder.php`
- `database/migrations/2025_09_29_010107_create_activity_log_table.php`
- `resources/views/activity-logs/index.blade.php`
- `resources/views/activity-logs/show.blade.php`
- `resources/views/components/activity-summary.blade.php`

### Modified Files
- All model files (added `LogsActivity` trait)
- `app/Http/Controllers/ImportAssignmentsController.php` (added import/export logging)
- `app/Http/Controllers/ClientController.php` (added export logging)
- `app/Http/Controllers/ClientSheetRowController.php` (added bulk operation logging)
- `routes/web.php` (added activity log routes)
- `database/seeders/DatabaseSeeder.php` (added activity log seeder)

## Testing

To test the activity logging:

1. **Run the migration and seeder**:
   ```bash
   php artisan migrate
   php artisan db:seed --class=ActivityLogPermissionSeeder
   ```

2. **Create some test data** and perform operations to generate activity logs

3. **Visit `/activity-logs`** to view the activity logs interface

4. **Check the database** for logged activities in the `activity_log` table

## Future Enhancements

- Activity log cleanup/archival
- Real-time activity notifications
- Activity log export functionality
- Advanced filtering and search
- Activity log analytics and reporting
- Integration with external logging services
