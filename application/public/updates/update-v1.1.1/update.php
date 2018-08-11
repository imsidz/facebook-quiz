<?php

return function() {
    $sqlPatchQuery = file_get_contents(__DIR__ . '/db-patch.sql');

    // Perform the query
    try {
        DB::unprepared($sqlPatchQuery);
    } catch (\PDOException $e) {
        die("Update failed: Error running query : \"" . substr($sqlPatchQuery, 0, 120) . '...' . "\". " . $e->getMessage());
    }

    die("Update successful");
};