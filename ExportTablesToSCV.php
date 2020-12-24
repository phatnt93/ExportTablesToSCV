<?php 

/** 
* ExportTablesToSCV
* 
* Made by phatnt93
* 24/12/2020
* 
* @license MIT License
* @author phatnt <thanhphat.uit@gmail.com>
* @github https://github.com/phatnt93/ExportTablesToSCV
* @version 1.0.0
* 
*/

//////////////
// Function //
//////////////

function query($db, $sql){
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    return $stmt->fetchAll();
}

////////////////////
// END - Function //
////////////////////

////////////
// Define //
////////////

define('BASE_PATH', __DIR__);

$config = [
    'db_host' => 'localhost',
    'db_user' => 'root',
    'db_pass' => '',
    'db_name' => 'omniblu',
    'output_path' => BASE_PATH . DIRECTORY_SEPARATOR . 'output'
];

$tablesExport = ['captcha', 'admin'];
$outVersionPath = $config['output_path'] . DIRECTORY_SEPARATOR . date('YmndHis');
//////////////////
// END - Define //
//////////////////

// Start script
try {
    $conn = new PDO("mysql:host=" . $config['db_host'] . ";dbname=" . $config['db_name'] . ";charset=utf8", $config['db_user'], $config['db_pass']);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$tablesExist = query(
    $conn,
    "Show tables"
);

$colSearch = "Tables_in_" . $config['db_name'];
$tableCheck = $tablesExport;
foreach ($tablesExist as $ktb => $vtb) {
    $index = array_search($vtb[$colSearch], $tableCheck);
    if ($index !== false) {
        unset($tableCheck[$index]);
    }
}
if (count($tableCheck) > 0) {
    die('Tables ' . implode(', ', $tableCheck) . ' not found');
}
if (!file_exists($config['output_path'])) {
    mkdir($config['output_path']);
}
if (!file_exists($outVersionPath)) {
    mkdir($outVersionPath);
}
// Export each table into a file csv
foreach ($tablesExport as $kte => $vte) {
    $data = query(
        $conn,
        "SELECT * FROM " . $vte . " WHERE 1"
    );
    $cols = query(
        $conn,
        "SHOW COLUMNS from " . $vte
    );
    // Create table file
    $fp = fopen($outVersionPath . DIRECTORY_SEPARATOR . $vte . '.csv', 'w');
    $colArr = [];
    foreach ($cols as $kc => $vac) {
        $colArr[] = $vac['Field'];
    }
    // Add col name
    fputcsv($fp, $colArr);
    if (count($data) > 0) {
        foreach ($data as $kda => $vda) {
            $temp = [];
            foreach ($colArr as $kc => $vac) {
                if (array_key_exists($vac, $vda)) {
                    $temp[] = $vda[$vac];
                }else{
                    $temp[] = null;
                }
            }
            fputcsv($fp, $temp);
        }
    }
    fclose($fp);
}

echo 'Successfully exported tables in ' . $outVersionPath;