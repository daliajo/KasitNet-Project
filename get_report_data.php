<?php
include 'session_check.php';
$conn = new mysqli('localhost', 'root', '', 'kasitnet');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

header('Content-Type: application/json');

$startDate = $_GET['startDate'];
$endDate = $_GET['endDate'];
$facilityType = $_GET['facilityType'];
$specificRoom = $_GET['specificRoom'];

$query = "SELECT facility_name AS facility, COUNT(*) AS frequency, 
                 MAX(CONCAT(start_time, ' - ', end_time)) AS peakTime, 
                 SUM(TIMESTAMPDIFF(HOUR, start_time, end_time)) AS totalHours 
          FROM bookings 
          WHERE date BETWEEN ? AND ?";

$params = [$startDate, $endDate];
$types = "ss";

if ($facilityType !== 'all') {
    $query .= " AND facility_type = ?";
    $params[] = $facilityType;
    $types .= "s";
}

if (!empty($specificRoom)) {
    $query .= " AND facility_name = ?";
    $params[] = $specificRoom;
    $types .= "s";
}

$query .= " GROUP BY facility_name";

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
