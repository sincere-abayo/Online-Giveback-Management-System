# GMS - Grant Management System

## Architecture
- **Type**: PHP web application with MySQL database  
- **Structure**: MVC-like pattern with pages/ directory for views, classes/ for models, admin/ for backend
- **Database**: MySQLi connection via classes/DBConnection.php, config in initialize.php
- **Frontend**: Bootstrap + jQuery, plugins/ for third-party libraries

## Testing & Build
- **No testing framework** - manual testing required
- **No build process** - direct PHP execution
- **Database**: Import db/gms\ \(New\).sql for fresh setup

## Development Commands
- **Local server**: `php -S localhost:8000` (run from project root)
- **Database config**: Edit initialize.php constants (DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME)

## Code Style
- **Classes**: Extend DBConnection, use camelCase methods
- **Error handling**: JSON response format: `{'status': 'failed', 'msg': 'error'}` 
- **SQL**: Direct MySQLi queries, use prepared statements for user input
- **Files**: Snake_case for files, camelCase for methods
- **Security**: Use `addslashes()` and `htmlentities()` for output escaping
