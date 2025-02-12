<?php
$conn = mysqli_connect("localhost", "root", "", "osticket_db");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get the latest ticket's ID
$ticket_id_query = "SELECT id FROM ost_ticket ORDER BY created DESC LIMIT 1";
$result = mysqli_query($conn, $ticket_id_query);
$ticket = mysqli_fetch_assoc($result);
$ticket_id = $ticket['id'];

if (!$ticket_id) {
    die("No ticket found.");
}

// Get District & Plan from `ost_form_entry_values`
$district_query = "SELECT value FROM ost_form_entry_values 
                   WHERE entry_id = (SELECT id FROM ost_form_entry WHERE object_id = $ticket_id LIMIT 1)
                   AND field_id = (SELECT id FROM ost_form_field WHERE label = 'District' LIMIT 1)";
$district_result = mysqli_query($conn, $district_query);
$district = mysqli_fetch_assoc($district_result)['value'];

$plan_query = "SELECT value FROM ost_form_entry_values 
               WHERE entry_id = (SELECT id FROM ost_form_entry WHERE object_id = $ticket_id LIMIT 1)
               AND field_id = (SELECT id FROM ost_form_field WHERE label = 'Plan' LIMIT 1)";
$plan_result = mysqli_query($conn, $plan_query);
$plan = mysqli_fetch_assoc($plan_result)['value'];

// Assign ticket based on conditions
if ($district == "Pune" && $plan == "Advanced") {
    $agent_id = 3; // Assign to Sanjot (Agent ID: 3)
} else {
    $agent_id = 2; // Keep assigned to Bhaskar (Agent ID: 2)
}

// Update ticket assignment
$update_ticket = "UPDATE ost_ticket SET assigned_agent = $agent_id WHERE id = $ticket_id";
if (mysqli_query($conn, $update_ticket)) {
    echo "Ticket reassigned successfully!";
} else {
    echo "Error updating ticket: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
