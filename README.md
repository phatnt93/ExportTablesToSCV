# ExportTablesToSCV
Export Tables To SCV

## Begin
B1: Open "ExportTablesToSCV.php" file and edit config to connect to db.
```
$config = [
    'db_host' => 'localhost',
    'db_user' => 'root',
    'db_pass' => '',
    'db_name' => 'omniblu',
    'output_path' => BASE_PATH . DIRECTORY_SEPARATOR . 'output'
];
```
B2: Change tables to export in "tablesExport" variable.
```
$tablesExport = ['captcha', 'admin'];
```
B3: Run script.
```
php ExportTableToSCV.php
```
