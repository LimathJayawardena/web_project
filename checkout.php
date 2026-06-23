<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "sql213.infinityfree.com";
$username = "if0_42242725";
$password = "ol7kSn6wPS72";
$database = "if0_42242725_foodnest";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("<h1 style='color:white;'>Database Connection Failed!</h1><p style='color:white;'>" . $conn->connect_error . "</p>");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - NSBM Food Nest</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="brand-container">
            <img src="images/logo.png" alt="NSBM Logo" class="logo-img">
            <h1>NSBM Food Nest</h1>
        </div>
    </header>

    <main class="container">
        <div class="receipt-container">
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                
                if (!isset($_POST['order_details']) || !isset($_POST['order_total'])) {
                    die("<h1>Error</h1><p>Missing order data. Please try checking out again.</p>");
                }

                $name = $conn->real_escape_string($_POST['customer_name']);
                $student_id = $conn->real_escape_string($_POST['student_id']);
                $phone = $conn->real_escape_string($_POST['phone']);
                $email = $conn->real_escape_string($_POST['email']);
                $total = $conn->real_escape_string($_POST['order_total']);
                
                $raw_order_details = $_POST['order_details']; 
                $order_data = json_decode($raw_order_details, true);

                if (empty($order_data)) {
                    echo "<h1>Error</h1>";
                    echo "<p>Your cart was empty. Please return to the shop.</p>";
                    echo "<br><a href='index.html' class='checkout-btn' style='text-decoration:none; display:inline-block;'>Back to Menu</a>";
                } else {
                    
                    $sql = "INSERT INTO orders (customer_name, student_id, phone, email, total_amount, order_items) 
                            VALUES ('$name', '$student_id', '$phone', '$email', '$total', '$raw_order_details')";

                    if ($conn->query($sql) === TRUE) {
                        echo "<h1>Order Received!</h1>";
                        echo "<p>Thank you, <strong>" . htmlspecialchars($name) . "</strong> (ID: " . htmlspecialchars($student_id) . ").</p>";
                        echo "<p style='color:#a0a0a0; font-size: 0.9rem;'>Contact: " . htmlspecialchars($phone) . " | " . htmlspecialchars($email) . "</p>";
                        echo "<p>Your order has been placed successfully and recorded in the database.</p>";
                        echo "<br><hr style='border-color: #2d2d2d;'><br>";
                        
                        echo "<div style='text-align: left;'>";
                        foreach ($order_data as $item) {
                            $itemTotal = $item['price'] * $item['quantity'];
                            echo "<p>{$item['quantity']}x " . htmlspecialchars($item['name']) . " - Rs. " . number_format($itemTotal, 2) . "</p>";
                        }
                        echo "</div>";

                        echo "<br><hr style='border-color: #2d2d2d;'><br>";
                        echo "<h3>Total Paid: Rs. " . number_format((float)$total, 2) . "</h3>";
                        echo "<br><a href='index.html' class='checkout-btn' style='text-decoration:none; display:inline-block;'>Return to Home</a>";
                    } else {
                        echo "<h1>Database Error</h1>";
                        echo "<p>Sorry, there was an issue recording your order: " . $conn->error . "</p>";
                    }
                }
            } else {
                header("Location: index.html");
                exit();
            }
            
            $conn->close();
            ?>
        </div>
    </main>
</body>
</html>