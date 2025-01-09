<?php
ini_set("display_errors", 0);  // Prevent errors from displaying in the response
error_reporting(E_ALL);  // Log all errors but do not display them

// Set the content type to JSON
header('Content-Type: application/json');

// Database connection and logic (MongoDB or MySQL, depending on your configuration)
require_once "dbconfig.php";

if (isset($_REQUEST["page"]) && isset($_REQUEST['username'])) {
    $username = $_REQUEST['username'];

    // Check if Redis is available (if using Redis session management)
    if (isset($redis)) {
        if ($_REQUEST["page"] == 'login') {
            $user = $redis->get('logged_in_user');
            if ($user == $_REQUEST['username']) {
                echo json_encode(['status' => 'Success']);
                exit;  // Stop further execution
            } else {
                echo json_encode(['status' => 'Failure']);
                exit;  // Stop further execution
            }
        }
    }

    // Profile Viewing Logic (fetch profile from MongoDB)
    if ($_REQUEST['page'] == 'profile_view') {
        try {
            $m = new MongoClient("mongodb://localhost:27017");  // MongoDB connection
            $mdb = $m->pms;
            $collection = $mdb->profile;
            $profile = $collection->find(["username" => $username]);

            // Check if profile exists
            if ($profile->count() > 0) {
                $profileArray = iterator_to_array($profile);
                echo json_encode($profileArray);  // Return profile data as JSON
            } else {
                echo json_encode(['error' => 'Profile not found']);
            }
        } catch (Exception $e) {
            // Handle MongoDB connection or query errors
            echo json_encode(['error' => 'Failed to fetch profile: ' . $e->getMessage()]);
        }
    }

    // Profile Update Logic (updating user profile)
    if ($_REQUEST['page'] == 'profile_update') {
        try {
            $m = new MongoClient("mongodb://localhost:27017");  // MongoDB connection
            $mdb = $m->pms;
            $collection = $mdb->profile;

            // Prepare the document for updating
            $document = [
                "username" => $username,
                "age" => $_POST['age'],
                "dob" => $_POST['dob'],
                "contact" => $_POST['contact']
            ];

            // Check if the profile already exists
            $profile = $collection->findOne(["username" => $username]);

            if ($profile) {
                // Update the existing profile
                $collection->updateOne(
                    ["username" => $username],
                    ['$set' => $document]
                );
                echo json_encode(['status' => 'Success']);
            } else {
                // Insert new profile if it doesn't exist
                $collection->insertOne($document);
                echo json_encode(['status' => 'Success']);
            }
        } catch (Exception $e) {
            // Handle MongoDB update errors
            echo json_encode(['error' => 'Error updating profile: ' . $e->getMessage()]);
        }
    }
}
?>
