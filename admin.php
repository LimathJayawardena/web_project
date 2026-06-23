<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set('Asia/Colombo');

$host = "sql213.infinityfree.com";
$username = "if0_42242725";
$password = "ol7kSn6wPS72";
$database = "if0_42242725_foodnest";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("<h1 style='color:white; text-align:center;'>Database Connection Failed!</h1>");
}

if (isset($_POST['login'])) {
    $admin_user = "admin";
    $admin_pass = "admin123"; 
    
    if ($_POST['username'] === $admin_user && $_POST['password'] === $admin_pass) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $login_error = "Invalid credentials. Access denied.";
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit();
}

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    
    if (isset($_GET['delete_order'])) {
        $id = $conn->real_escape_string($_GET['delete_order']);
        $conn->query("DELETE FROM orders WHERE id = '$id'");
        header("Location: admin.php");
        exit();
    }

    if (isset($_POST['add_product'])) {
        $name = $conn->real_escape_string($_POST['name']);
        $category = $conn->real_escape_string($_POST['category']);
        $price = $conn->real_escape_string($_POST['price']);
        $image_path = $conn->real_escape_string($_POST['image_path']);
        
        $sql = "INSERT INTO products (name, category, price, image_path) VALUES ('$name', '$category', '$price', '$image_path')";
        $conn->query($sql);
        header("Location: admin.php");
        exit();
    }

    if (isset($_GET['delete_item'])) {
        $id = $conn->real_escape_string($_GET['delete_item']);
        $conn->query("DELETE FROM products WHERE id = '$id'");
        header("Location: admin.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - NSBM Food Nest</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-dashboard { max-width: 1100px; margin: 2rem auto; padding: 2rem; background-color: #1e1e1e; border-radius: 8px; border: 1px solid #2d2d2d; }
        .admin-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #2d2d2d; padding-bottom: 1rem; margin-bottom: 2rem; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 1rem; font-size: 0.95rem; }
        .data-table th, .data-table td { padding: 12px; text-align: left; border-bottom: 1px solid #2d2d2d; vertical-align: top; }
        .data-table th { background-color: #2d2d2d; color: #008c4a; }
        .add-form { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr auto; gap: 10px; margin-bottom: 2rem; align-items: end; }
        .btn-delete { background-color: #ff4c4c; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; font-size: 0.85rem; display: inline-block; border: none; cursor: pointer; }
        .btn-delete:hover { background-color: #cc0000; }
        .btn-success { background-color: #008c4a; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; font-size: 0.85rem; display: inline-block; }
        .btn-success:hover { background-color: #006b38; }
        .logout-btn { background-color: #2d2d2d; border: 1px solid #3d3d3d; padding: 8px 16px; color: white; text-decoration: none; border-radius: 4px; }
        .logout-btn:hover { background-color: #3d3d3d; }
        .order-items-list { color: #a0a0a0; font-size: 0.9rem; line-height: 1.5; }
    </style>
</head>
<body>
    <header>
        <div class="brand-container">
            <img src="images/logo.png" alt="NSBM Logo" class="logo-img">
            <h1>NSBM Food Nest</h1>
        </div>
        <nav class="header-nav">
            <a href="index.html">Back to Home</a>
        </nav>
    </header>

    <main class="container">
        <?php if (!isset($_SESSION['admin_logged_in'])): ?>
            <div class="admin-login-container">
                <h2>Admin Access</h2>
                <?php if(isset($login_error)) echo "<p style='color:#ff4c4c; text-align:center; margin-bottom:1rem;'>$login_error</p>"; ?>
                <form method="POST" action="admin.php" class="checkout-form">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required>
                    </div>
                    <button type="submit" name="login" class="checkout-btn">Authenticate</button>
                </form>
            </div>

        <?php else: ?>
            <div class="admin-dashboard" style="border-color: #008c4a;">
                <div class="admin-header" style="border-bottom-color: #008c4a;">
                    <h2 style="color: #008c4a;">Live Order Queue</h2>
                    <a href="admin.php?logout=true" class="logout-btn">Log Out</a>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Student / Staff Details</th>
                            <th>Items Ordered</th>
                            <th>Total Amount</th>
                            <th>Time Placed</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $orders_result = $conn->query("SELECT * FROM orders ORDER BY order_date DESC");
                        if ($orders_result && $orders_result->num_rows > 0) {
                            while ($order = $orders_result->fetch_assoc()) {
                                $items = json_decode($order['order_items'], true);
                                $items_html = "<div class='order-items-list'>";
                                if (is_array($items)) {
                                    foreach ($items as $item) {
                                        $items_html .= "<strong>" . htmlspecialchars($item['quantity']) . "x</strong> " . htmlspecialchars($item['name']) . "<br>";
                                    }
                                }
                                $items_html .= "</div>";

                                $order_time = date('M d, Y - h:i A', strtotime($order['order_date']));

                                echo "<tr>";
                                echo "<td><strong style='font-size: 1.1rem;'>#" . $order['id'] . "</strong></td>";
                                echo "<td>
                                        <strong>" . htmlspecialchars($order['customer_name']) . "</strong><br>
                                        <span style='color:#a0a0a0;'>ID: " . htmlspecialchars($order['student_id']) . "</span><br>
                                        <span style='color:#a0a0a0;'>Tel: " . htmlspecialchars($order['phone']) . "</span>
                                      </td>";
                                echo "<td>" . $items_html . "</td>";
                                echo "<td><strong style='color:#008c4a;'>Rs. " . number_format($order['total_amount'], 2) . "</strong></td>";
                                echo "<td>" . $order_time . "</td>";
                                echo "<td><a href='admin.php?delete_order={$order['id']}' class='btn-success' onclick=\"return confirm('Handed over to student? Mark as Done?');\">Mark Done</a></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' style='text-align:center; padding:2rem; color:#a0a0a0;'>No pending orders right now.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="admin-dashboard">
                <div class="admin-header">
                    <h2>Inventory Management</h2>
                </div>

                <h3 style="color: #a0a0a0; margin-bottom: 1rem; font-size: 1.1rem;">Add New Product</h3>
                <form method="POST" action="admin.php" class="add-form">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" required placeholder="e.g., Fish Bun">
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category" style="padding: 0.5rem; background: #121212; border: 1px solid #2d2d2d; color: white;" required>
                            <option value="rice-curry">Rice & Curry</option>
                            <option value="fast-food">Fast Food</option>
                            <option value="beverages">Beverages</option>
                            <option value="desserts">Desserts</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Price (Rs.)</label>
                        <input type="number" name="price" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Image Path</label>
                        <input type="text" name="image_path" required placeholder="images/item.jpg">
                    </div>
                    <button type="submit" name="add_product" class="checkout-btn">Add to Database</button>
                </form>

                <h3 style="color: #a0a0a0; margin-bottom: 1rem; font-size: 1.1rem; border-top: 1px solid #2d2d2d; padding-top: 2rem;">Current Menu Items</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $products_result = $conn->query("SELECT * FROM products ORDER BY id DESC");
                        if ($products_result && $products_result->num_rows > 0) {
                            while ($row = $products_result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td><img src='" . htmlspecialchars($row['image_path']) . "' style='height:40px; border-radius:4px;'></td>";
                                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                                echo "<td>Rs. " . number_format($row['price'], 2) . "</td>";
                                echo "<td><a href='admin.php?delete_item={$row['id']}' class='btn-delete' onclick=\"return confirm('Drop this item from the menu?');\">Delete</a></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' style='text-align:center; padding:2rem;'>No products found in the database.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>