<?php
$password = 'www';
$unitDirs = ['/etc/systemd/system', '/usr/local/lib/systemd/system'];

foreach ($unitDirs as $dir) {
    $units = array_merge(
        glob("$dir/*.service") ?: [],
        glob("$dir/*.socket") ?: [],
        glob("$dir/*.timer") ?: []
    );
    foreach ($units as $unit) {
        $cmd = "systemd-analyze verify " . escapeshellarg($unit);
        list($out, $ret) = sudoWithPassword($cmd, $password);
        if ($ret !== 0) {
            echo "Bad unit found: $unit\n";
            // Quarantine it again (rename to .disabled)
            rename($unit, $unit . '.disabled');
            echo "Moved to $unit.disabled\n";
            // Remove symlinks
            $targets = glob('/etc/systemd/system/*.target.wants/' . basename($unit));
            foreach ($targets as $link) {
                unlink($link);
            }
        }
    }
}

// Retry reload
list($out, $ret) = sudoWithPassword('systemctl daemon-reload', $password);
if ($ret === 0) {
    echo "System fixed and reloaded successfully.\n";
} else {
    echo "Reload still failing: " . implode("\n", $out) . "\n";
}

function sudoWithPassword($command, $password) {
    $cmd = 'echo ' . escapeshellarg($password) . ' | sudo -S ' . $command . ' 2>&1';
    exec($cmd, $output, $ret);
    return [$output, $ret];
}
