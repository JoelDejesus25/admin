<?php
// Database connection
$servername = "localhost"; // your server name
$username = "root"; // your database username
$password = ""; // your database password
$dbname = "apply"; // your database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch total sellers count directly from the sellers table, considering only accepted sellers
$totalSellersResult = $conn->query("SELECT COUNT(*) AS total FROM sellerapplication WHERE status = 'accepted'");
$totalSellers = ($totalSellersResult) ? $totalSellersResult->fetch_assoc()['total'] : 0;

// Fetch total applicants, excluding accepted and rejected
$totalApplicantsResult = $conn->query("SELECT COUNT(*) AS total FROM sellerapplication WHERE status NOT IN ('accepted', 'rejected')");
$totalApplicants = ($totalApplicantsResult) ? $totalApplicantsResult->fetch_assoc()['total'] : 0;

// Fetch reported sellers count
$reportedResult = $conn->query("SELECT COUNT(*) AS total FROM sellerapplication WHERE status = 'reported'");
$reportedCount = ($reportedResult) ? $reportedResult->fetch_assoc()['total'] : 0;

// Fetch total accepted applicants
$acceptedCountResult = $conn->query("SELECT COUNT(*) AS total FROM sellerapplication WHERE status = 'accepted'");
$acceptedCount = ($acceptedCountResult) ? $acceptedCountResult->fetch_assoc()['total'] : 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="dash.css">   
</head>
<body>

<div class="dashboard-container">
    <nav class="navbar">
        <h2>Admin Dashboard</h2>
        <ul class="nav-items">
            <li><a href="dash.php">Home</a></li>
            <li><a href="#total-sellers">Total Sellers</a></li>
            <li><a href="dash.php?seller_applicants=true">Seller Applicants</a></li>
            <li><a href="dash.php?reported_sellers=true">Reported Sellers</a></li> <!-- Link to view reported sellers -->
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="main-content">
        <h4>Total Counts</h4>
        <div class="total-counts">
            <p><strong>Total Sellers:</strong> <?php echo $totalSellers; ?></p>
            <p><strong>Total Seller Applicants:</strong> <?php echo $totalApplicants; ?></p>
            <p><strong>Total Reported Sellers:</strong> <?php echo $reportedCount; ?></p>
            <p><strong>Total Accepted Applicants:</strong> <?php echo $acceptedCount; ?></p>
        </div>

        <?php
        // Check if the seller applicants button was clicked
        if (isset($_GET['seller_applicants'])) {
            include 'view_seller_application.php'; // Include seller applicants view
        } elseif (isset($_GET['reported_sellers'])) { // Check if reported sellers button was clicked
            include 'reported.php'; // Include reported sellers view
        } else {
            echo "<h4>Sellers Information</h4>";
            echo "<table>
                    <tr>
                        <th>Name</th>
                        <th>Application Date</th>
                        <th>Status</th>
                    </tr>";

            // Fetch seller applications for display, showing only accepted sellers
            $sql = "SELECT * FROM sellerapplication WHERE status = 'accepted'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr onclick='openModal(`" . htmlspecialchars(json_encode($row)) . "`)'>";
                    echo "<td><a href='#'>" . htmlspecialchars($row['name']) . "</a></td>";
                    echo "<td>" . htmlspecialchars($row['application_date']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No accepted sellers found.</td></tr>";
            }
            echo "</table>";
        }
        ?>
    </div>
</div>

<!-- Modal to display seller details -->
<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <p><strong>Name:</strong> <span id="modalName"></span></p>
        <p><strong>Email:</strong> <span id="modalEmail"></span></p>
        <p><strong>Business Name:</strong> <span id="modalBusinessName"></span></p>
        <p><strong>Business Address:</strong> <span id="modalBusinessAddress"></span></p>
    </div>
</div>

<script>
    function openModal(sellerData) {
        const seller = JSON.parse(sellerData);
        document.getElementById('modalName').textContent = seller.name;
        document.getElementById('modalEmail').textContent = seller.email;
        document.getElementById('modalBusinessName').textContent = seller.business_name;
        document.getElementById('modalBusinessAddress').textContent = seller.business_address;
        document.getElementById('myModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('myModal').style.display = 'none';
    }

    window.onclick = function(event) {
        var modal = document.getElementById('myModal');
        if (event.target == modal) {
            closeModal();
        }
    }
</script>

</body>
</html>
