<?php
echo "<h1>Environment Variables Test</h1>";

echo "<h2>getenv()</h2>";
echo "SESSION_DRIVER: " . (getenv('SESSION_DRIVER') ?: '<span style="color:red">NOT SET</span>') . "<br>";
echo "DATABASE_DEFAULT_HOSTNAME: " . (getenv('DATABASE_DEFAULT_HOSTNAME') ?: '<span style="color:red">NOT SET</span>') . "<br>";

echo "<h2>\$_ENV</h2>";
echo "SESSION_DRIVER: " . ($_ENV['SESSION_DRIVER'] ?? '<span style="color:red">NOT SET</span>') . "<br>";
echo "DATABASE_DEFAULT_HOSTNAME: " . ($_ENV['DATABASE_DEFAULT_HOSTNAME'] ?? '<span style="color:red">NOT SET</span>') . "<br>";

echo "<h2>CodeIgniter Env</h2>";
try {
    $env = \Config\Services::dotenv();
    $env->load();
    echo "SESSION_DRIVER after load: " . (getenv('SESSION_DRIVER') ?: '<span style="color:red">NOT SET</span>') . "<br>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}
