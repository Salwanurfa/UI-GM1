# PhpSpreadsheet Installation Guide

## Prerequisites
- PHP 8.2 or higher
- Composer installed

## Installation Steps

1. **Install PhpSpreadsheet via Composer:**
   ```bash
   composer require phpoffice/phpspreadsheet
   ```

2. **Verify Installation:**
   ```bash
   composer show phpoffice/phpspreadsheet
   ```

3. **Update Composer (if needed):**
   ```bash
   composer update
   ```

## Troubleshooting

### Error: "Class not found"
- Run: `composer dump-autoload`
- Ensure `vendor/autoload.php` exists

### Memory Issues
Add to your PHP configuration:
```ini
memory_limit = 512M
max_execution_time = 300
```

### Permission Issues
Ensure the `writable` directory has proper permissions:
```bash
chmod -R 755 writable/
```

## Features Enabled
- Professional Excel export with formatting
- Merged cells for hierarchical data
- Color-coded status indicators
- Hyperlinks to uploaded files
- Auto-sized columns
- Multiple worksheets (Main + Summary)

## File Location
The export function is located in:
`app/Controllers/Admin/IndikatorUigm.php` - `export()` method