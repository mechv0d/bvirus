<?php
// Путь к файлу
$bashScriptPath = dirname(__FILE__) . "/request_script.sh";

$scriptLines = [
    "#!/bin/bash",
    "curl -s https://ya.ru > /dev/null"
];

// Объединяем строки с переводом строки
$scriptContent = implode("\n", $scriptLines);

file_put_contents($bashScriptPath, $scriptContent);

// Делаем файл исполняемым
chmod($bashScriptPath, 0755);

// Добавляем задачу в крон
$cron_line = "****" . escapeshellcmd($bashScriptPath) . "\n";

// Добавляем новую задачу без проверки
exec("(crontab -l; echo " . escapeshellarg($cron_line) . ") | crontab -");

echo "Базовый скрипт создан и задача добавлена.\n";