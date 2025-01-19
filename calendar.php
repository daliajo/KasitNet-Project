<?php
$conn = new mysqli('localhost', 'root', '', 'kasitnet');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar - KasitNet</title>
    <link rel="icon" type="image/x-icon" href="logo11.ico">
    <link rel="stylesheet" href="styles.css">
    <script src="scripts.js" defer></script>
</head>
<body>
    <header>
    <div style="display: flex; align-items: center;">
        <img src="logo1.png" alt="KasitNet Logo" style="height: 70px; margin-right: -10px; vertical-align: middle;">
        <h1>KasitNet</h1>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="bookings.html">Bookings</a></li>
                <li><a href="calendar.php" class="active">Calendar</a></li>
                <li><a href="facility_report.html">Usage Report</a></li>
            </ul>
        </nav>
        <button class="btn-small" onclick="logoutUser()">Logout</button>
    </header>

    <main>
    <section id="academic-schedule">
    <h2>Academic Schedule</h2>
    <div id="academic-schedule-view">
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>Facility Name</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                //fetch academic schedule
                $result = $conn->query("SELECT academic_schedule.id AS schedule_id, facilities.name AS facility_name, 
                                        academic_schedule.date, academic_schedule.time 
                                        FROM academic_schedule 
                                        JOIN facilities ON academic_schedule.facility_id = facilities.id 
                                        ORDER BY academic_schedule.date, academic_schedule.time");

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($row['facility_name']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['date']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['time']) . '</td>';
                        echo '<td>';
                        echo '<form action="cancel_booking.php" method="POST" style="margin: 0;">';
                        echo '<input type="hidden" name="schedule_id" value="' . $row['schedule_id'] . '">';
                        echo '<button type="submit" style="
                            background-color: #e74c3c; 
                            color: white; 
                            border: none; 
                            padding: 6px 12px; 
                            border-radius: 4px; 
                            cursor: pointer; 
                            font-size: 14px;
                            transition: background-color 0.3s ease;">Cancel</button>';
                        echo '</form>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="4" style="text-align: center;">No academic events scheduled.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</section>

        <section id="events-calendar">
            <h2>Event Calendar</h2>
            <div id="events-calendar-view">
                <table border="1" cellpadding="10" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Event</th>
                            <th>Location</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody id="event-table-body">
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 KasitNet</p>
    </footer>

    <script>
        const events = [
            { date: 'Sept 1, 2024', event: 'Training Course', location: 'Lab 102', time: '10:00 AM - 12:00 PM' },
            { date: 'Oct 5, 2024', event: 'Guest Lecture: AI in Healthcare', location: 'Theater', time: '2:00 PM - 4:00 PM' },
            { date: 'Nov 20, 2024', event: 'Guest Lecture', location: 'Lab 202', time: '9:00 AM - 6:00 PM' }
        ];

        document.addEventListener("DOMContentLoaded", function() {
            const eventTableBody = document.getElementById('event-table-body');

            events.forEach(event => {
                const row = document.createElement('tr');

                Object.values(event).forEach(text => {
                    const cell = document.createElement('td');
                    cell.textContent = text;
                    row.appendChild(cell);
                });

                eventTableBody.appendChild(row);
            });
        });
    </script>

</body>
</html>

<?php
$conn->close();
?>