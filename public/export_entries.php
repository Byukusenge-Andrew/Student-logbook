<?php
require_once '../auth.php';

if (!isLoggedIn() || $_SESSION['role'] != 'student') {
    header('Location: login.php');
    exit;
}

$entries = getLogbookEntries($_SESSION['user_id']);

header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=logbook_entries.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['Date', 'Time', 'Days', 'Week', 'Activity', 'Hours']);

foreach ($entries as $entry) {
    fputcsv($output, [
        $entry['entry_date'],
        $entry['entry_time'],
        $entry['days'],
        $entry['week'],
        $entry['activity_description'],
        $entry['working_hours']
    ]);
}

fclose($output);
exit;

