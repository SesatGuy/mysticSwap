<?php
session_start();

$adminFile = 'json/admin.json';
if (!file_exists($adminFile)) {
    die("Admin credentials missing.");
}
$adminData = json_decode(file_get_contents($adminFile), true);

$failedLoginsFile = 'json/failed_logins.json';
$failedLogins = file_exists($failedLoginsFile) ? json_decode(file_get_contents($failedLoginsFile), true) : [];

$userIP = $_SERVER['REMOTE_ADDR'];
$currentTime = time();

if (isset($failedLogins[$userIP]) && $failedLogins[$userIP]['count'] >= 5 && ($currentTime - $failedLogins[$userIP]['time']) < 600) {
    die("Too many failed login attempts. Try again later.");
}

if (!isset($_SESSION['admin_logged_in'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
        $bcrypt_cost = 10;
        if ($_POST['username'] === $adminData['username'] && password_verify($_POST['password'], $adminData['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['failed_attempts'] = 0;
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            unset($failedLogins[$userIP]);
            file_put_contents($failedLoginsFile, json_encode($failedLogins));

            header("Location: admin.php");
            exit();
        } else {
            $failedLogins[$userIP]['count'] = ($failedLogins[$userIP]['count'] ?? 0) + 1;
            $failedLogins[$userIP]['time'] = $currentTime;
            file_put_contents($failedLoginsFile, json_encode($failedLogins));
            $error_message = "Invalid login!";
        }
    }

    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Admin Login</title>
        <link rel="stylesheet" href="css/admin-style.css">
    </head>
    <body>
        <div class="login-box">
            <h2>Admin Login</h2>
            <form method="POST">
                <?php if (isset($error_message)) echo "<p class='error'>$error_message</p>"; ?>
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">Login</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit();
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit();
}

$ordersFile = 'json/orders.json';
if (!file_exists($ordersFile)) {
    file_put_contents($ordersFile, json_encode([]));
}
$orders = json_decode(file_get_contents($ordersFile), true);

// Pagination setup
$ordersPerPage = 20; // Number of orders per page
$totalOrders = count($orders); // Total number of orders
$totalPages = ceil($totalOrders / $ordersPerPage); // Total number of pages

// Get the current page from the query string, default to 1 if not set
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($currentPage < 1) $currentPage = 1; // Ensure the page number is valid
if ($currentPage > $totalPages) $currentPage = $totalPages; // Avoid exceeding total pages

// Calculate the offset for fetching orders
$offset = ($currentPage - 1) * $ordersPerPage;
$paginatedOrders = array_slice($orders, $offset, $ordersPerPage); // Slice the orders array

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token.");
    }

    $order_id = $_POST['order_id'];

    if (isset($_POST['status'])) {
        // Update order status
        foreach ($orders as &$order) {
            if ($order['id'] == $order_id) {
                $order['status'] = $_POST['status'];
                break;
            }
        }
        file_put_contents($ordersFile, json_encode($orders, JSON_PRETTY_PRINT));
        echo "<script>alert('Order status updated!'); window.location='admin.php';</script>";
    } elseif (isset($_POST['delete'])) {
        // Delete order
        $orders = array_filter($orders, function ($order) use ($order_id) {
            return $order['id'] != $order_id;
        });
        file_put_contents($ordersFile, json_encode(array_values($orders), JSON_PRETTY_PRINT));
        echo "<script>alert('Order deleted!'); window.location='admin.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="css/admin-style.css">
</head>
<body>
    <div class="admin-container">
        <h1>Admin Panel - Orders</h1>
        <p><a href="admin.php?logout=true" class="logout">Logout</a></p>

        <table>
            <tr>
                <th>ID</th>
                <th>From</th>
                <th>To</th>
                <th>Amount</th>
                <th>Received</th>
                <th>Wallet</th>
                <th>Email</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>

            <?php foreach ($paginatedOrders as $order): ?>
            <tr>
                <td><?= htmlspecialchars($order['id']); ?></td>
                <td><?= strtoupper(htmlspecialchars($order['from'])); ?></td>
                <td><?= strtoupper(htmlspecialchars($order['to'])); ?></td>
                <td><?= number_format($order['amount'], 2); ?></td>
                <td><?= number_format($order['receive'], 2); ?></td>
                <td><?= htmlspecialchars($order['wallet']); ?></td>
                <td><?= htmlspecialchars($order['gmail']); ?></td>
                <td class="status <?= htmlspecialchars($order['status']); ?>"><?= ucfirst(htmlspecialchars($order['status'])); ?></td>
                <td><?= htmlspecialchars($order['created_at']); ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['id']); ?>">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>"> <!-- CSRF Token -->
                        <button type="submit" name="status" value="completed" class="complete">Complete</button>
                        <button type="submit" name="status" value="canceled" class="cancel">Cancel</button>
                        <button type="submit" name="delete" value="true" class="delete" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <!-- Pagination -->
        <div class="pagination">
            <?php if ($currentPage > 1): ?>
                <a href="admin.php?page=1">&laquo; First</a>
                <a href="admin.php?page=<?= $currentPage - 1; ?>">Previous</a>
            <?php endif; ?>

            <span>Page <?= $currentPage; ?> of <?= $totalPages; ?></span>

            <?php if ($currentPage < $totalPages): ?>
                <a href="admin.php?page=<?= $currentPage + 1; ?>">Next</a>
                <a href="admin.php?page=<?= $totalPages; ?>">Last &raquo;</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
