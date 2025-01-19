<?php
session_start(); 
$conn = new mysqli('localhost', 'root', '', 'kasitnet');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['schedule_id'])) {
        $schedule_id = intval($_POST['schedule_id']);

        //fetch the facility_id, date, and time from academic_schedule table
        $stmt = $conn->prepare("SELECT facility_id, date, time FROM academic_schedule WHERE id = ?");
        $stmt->bind_param("i", $schedule_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $facility_id = $row['facility_id'];
            $date = $row['date'];
            $time = $row['time'];

            //delete from academic_schedule 
            $stmt = $conn->prepare("DELETE FROM academic_schedule WHERE id = ?");
            $stmt->bind_param("i", $schedule_id);
            $stmt->execute();

            //delete from bookings table
            $stmt = $conn->prepare("DELETE FROM bookings WHERE facility_id = ? AND date = ? AND time = ?");
            $stmt->bind_param("iss", $facility_id, $date, $time);
            $stmt->execute();

            echo "<script>alert('Schedule and booking canceled successfully.'); window.location.href = 'calendar.php';</script>";
        } else {
            echo "<script>alert('Schedule not found.'); window.location.href = 'calendar.php';</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('No schedule ID provided.'); window.location.href = 'calendar.php';</script>";
    }
}

$conn->close();
?>
