<?php
$lockFile = __DIR__ . '/build_index.lock';

if (file_exists($lockFile)) {
    $pid = file_get_contents($lockFile);
    // Check if the process is still running
    if (posix_kill($pid, 0)) {
        echo "Indexing in progress (PID: $pid).\n";
        exit;
    } else {
        echo "Previous indexing process (PID: $pid) is no longer running. Removing stale lock.\n";
        unlink($lockFile); // Remove stale lock
    }
}

file_put_contents($lockFile, getmypid()); // Simple lock

require_once 'database.php';

try {
    echo "Indexing started at " . date('Y-m-d H:i:s') . "\n";
    $pdo = openConnection();
    createGenres($pdo);
    createProfession($pdo);
    indexDB($pdo);
    echo "Indexing complete at " . date('Y-m-d H:i:s') . "\n";
} catch (Throwable $e) {
    echo "Indexing failed: " . $e->getMessage() . "\n";
} finally {
    unlink($lockFile); // Always release the lock
}