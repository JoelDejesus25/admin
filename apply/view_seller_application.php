<?php
// Assuming this file connects to the database
include 'conn.php'; // Assuming this file connects to the database

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch only pending sellers from the database
$sql = "SELECT * FROM sellerapplication WHERE status = 'pending'";
$result = $conn->query($sql);
?>

<div class="seller-list-container">
    <h2>Seller Applicants</h2>

    <!-- Display the success or error message -->
    <?php if (isset($_GET['message'])): ?>
        <p class="message"><?php echo htmlspecialchars($_GET['message']); ?></p>
    <?php endif; ?>

    <table>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Business Name</th>
            <th>Business Address</th>
            <th>Business Description</th>
            <th>Valid ID</th>
            <th>Application Date</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php
        if ($result->num_rows > 0) {
            // Output data for each row
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                echo "<td>" . htmlspecialchars($row['business_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['business_address']) . "</td>";
                echo "<td>" . htmlspecialchars($row['business_description']) . "</td>";
                echo "<td><img src='" . htmlspecialchars($row['valid_id']) . "' alt='Valid ID' style='width: 100px; height: auto;' onclick='openModal(\"" . htmlspecialchars($row['valid_id']) . "\")'></td>";
                echo "<td>" . htmlspecialchars($row['application_date']) . "</td>";
                echo "<td>" . htmlspecialchars($row['status']) . "</td>"; // Display status here
                
                // Check if status is 'pending' to display action buttons
                echo "<td>";
                if (htmlspecialchars($row['status']) === 'pending') {
                    echo "<form action='accept_reject.php' method='POST' style='display:inline;'>
                            <input type='hidden' name='seller_id' value='" . htmlspecialchars($row['id']) . "'>
                            <div class='action-buttons'>
                                <button type='submit' name='action' value='accept' class='accept-button'>Accept</button>
                                <button type='submit' name='action' value='reject' class='reject-button'>Reject</button>
                            </div>
                          </form>";
                }
                echo "</td>";
                
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='10'>No applications found.</td></tr>"; // Adjust colspan for the new column
        }
        ?>
    </table>
</div>

<!-- Modal for Valid ID -->
<div id="validIdModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <img id="validIdImage" src="" alt="Valid ID" style="width: 100%; height: auto;">
    </div>
</div>

<script>
function openModal(imageSrc) {
    document.getElementById("validIdImage").src = imageSrc;
    document.getElementById("validIdModal").style.display = "block";
}

function closeModal() {
    document.getElementById("validIdModal").style.display = "none";
}
</script>

<?php
// Close the connection
$conn->close();
?>
