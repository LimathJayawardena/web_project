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
            <img src="logo.png" alt="NSBM Logo" class="logo-img">
            <h1>NSBM Food Nest</h1>
        </div>
    </header>

    <main class="container">
        <div class="receipt-container">
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $name = htmlspecialchars($_POST['customer_name']);
                $student_id = htmlspecialchars($_POST['student_id']);
                $total = htmlspecialchars($_POST['order_total']);
                $order_data = json_decode($_POST['order_details'], true);

                if (empty($order_data)) {
                    echo "<h1>Error</h1>";
                    echo "<p>Your cart was empty. Please return to the shop.</p>";
                    echo "<br><a href='index.html' class='checkout-btn' style='text-decoration:none; display:inline-block;'>Back to Menu</a>";
                } else {
                    echo "<h1>Order Received!</h1>";
                    echo "<p>Thank you, <strong>$name</strong> (ID: $student_id).</p>";
                    echo "<p>Your order has been placed successfully.</p>";
                    echo "<br><hr style='border-color: #2d2d2d;'><br>";
                    
                    echo "<div style='text-align: left;'>";
                    foreach ($order_data as $item) {
                        $itemTotal = $item['price'] * $item['quantity'];
                        echo "<p>{$item['quantity']}x {$item['name']} - Rs. " . number_format($itemTotal, 2) . "</p>";
                    }
                    echo "</div>";

                    echo "<br><hr style='border-color: #2d2d2d;'><br>";
                    echo "<h3>Total Paid: Rs. " . number_format((float)$total, 2) . "</h3>";
                    echo "<br><a href='index.html' class='checkout-btn' style='text-decoration:none; display:inline-block;'>Return to Home</a>";
                }
            } else {
                header("Location: index.html");
                exit();
            }
            ?>
        </div>
    </main>
</body>
</html>