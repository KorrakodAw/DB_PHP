<?php
// Database connection credentials
include "server.php";

// Get the selected country (COUNTYFP) from the request (default is empty, meaning all countries)
$countyfp = isset($_GET['country']) ? $_GET['country'] : '';

// Prepare the SQL query to filter by county if provided
if (!empty($countyfp)) {
  $sql = "SELECT COUNTYFP, NEVER, RARELY, SOMETIMES, FREQUENTLY, ALWAYS FROM `mask-use-by-country` WHERE COUNTYFP = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $countyfp);
  $stmt->execute();
  $result = $stmt->get_result();
} else {
  // If no specific county is selected, return all data
  $sql = "SELECT COUNTYFP, NEVER, RARELY, SOMETIMES, FREQUENTLY, ALWAYS FROM `mask-use-by-country`";
  $result = $conn->query($sql);
}

// Check if data is available
if ($result->num_rows > 0) {
  $maskData = array();
  // Output data into an array
  while($row = $result->fetch_assoc()) {
    $maskData[] = $row;
  }
  // Return JSON response
  echo json_encode($maskData);
} else {
  echo json_encode([]);
}

$conn->close();
?>

