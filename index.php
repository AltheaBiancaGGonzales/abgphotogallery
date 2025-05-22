<?php
// Define the path to the JSON data file
define('DATA_FILE', 'items.json');

/**
 * Reads item data from the JSON file.
 * If the file doesn't exist, it initializes with sample data.
 * @return array The array of items.
 */
function readItems() {
    if (file_exists(DATA_FILE)) {
        $json_data = file_get_contents(DATA_FILE);
        // Decode JSON data, return empty array if decoding fails
        return json_decode($json_data, true) ?: [];
    } else {
        // Initialize with sample data if the file doesn't exist
        $sample_items = [
            ["name" => "Laptop", "stock" => 15, "price" => 1200.00],
            ["name" => "Mouse", "stock" => 50, "price" => 25.50],
            ["name" => "Keyboard", "stock" => 30, "price" => 75.00],
            ["name" => "Monitor", "stock" => 10, "price" => 300.00],
            ["name" => "Webcam", "stock" => 20, "price" => 50.00]
        ];
        writeItems($sample_items); // Write sample data to the file
        return $sample_items;
    }
}

/**
 * Writes item data to the JSON file.
 * @param array $items The array of items to write.
 */
function writeItems($items) {
    // Encode the array to JSON format
    $json_data = json_encode($items, JSON_PRETTY_PRINT);
    // Write the JSON data to the file
    file_put_contents(DATA_FILE, $json_data);
}

// Initialize items array
$items = readItems();
$message = '';
$message_type = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addItem'])) {
    $itemName = trim($_POST['itemName'] ?? '');
    $itemStock = filter_var($_POST['itemStock'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
    $itemPrice = filter_var($_POST['itemPrice'] ?? '', FILTER_VALIDATE_FLOAT, ['options' => ['min_range' => 0]]);

    // Validate inputs
    if (empty($itemName) || $itemStock === false || $itemPrice === false) {
        $message = 'Please enter valid item details (Name, Stock, Price). Stock and Price must be non-negative numbers.';
        $message_type = 'danger';
    } else {
        // Add new item to the items array
        $items[] = ["name" => $itemName, "stock" => $itemStock, "price" => $itemPrice];
        writeItems($items); // Save updated items to file
        $message = 'Item added successfully!';
        $message_type = 'success';

        // Redirect to prevent form re-submission on refresh
        // This also clears the POST data from the URL
        header('Location: index.php?msg=' . urlencode($message) . '&type=' . urlencode($message_type));
        exit(); // Terminate script execution after redirection
    }
}

// Check for messages passed via URL parameters after redirection
if (isset($_GET['msg']) && isset($_GET['type'])) {
    $message = htmlspecialchars($_GET['msg']);
    $message_type = htmlspecialchars($_GET['type']);
}

?>
    
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Item Stock Checker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom CSS styles */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            padding-top: 20px;
        }
        .container {
            max-width: 960px;
        }
        .card {
            border-radius: 0.75rem; /* Rounded corners for cards */
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .table {
            border-radius: 0.75rem; /* Rounded corners for table */
            overflow: hidden; /* Ensures rounded corners apply to table content */
        }
        .table thead th {
            background-color: #0d6efd; /* Bootstrap primary blue */
            color: white;
            border-bottom: none;
        }
        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
            border-radius: 0.5rem; /* Rounded corners for buttons */
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
        }
        .form-control {
            border-radius: 0.5rem; /* Rounded corners for form inputs */
        }
        /* Custom alert styles for success and danger messages */
        .alert-custom {
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
            display: none; /* Hidden by default, shown by JS */
        }
        .alert-success {
            background-color: #d1e7dd;
            color: #0f5132;
            border-color: #badbcc;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container mx-auto p-4">
        <h1 class="text-4xl font-bold text-center mb-8 text-gray-800">Item Stock Checker</h1>

        <div id="messageBox" class="alert-custom <?php echo !empty($message) ? 'block alert-' . $message_type : ''; ?>" role="alert">
            <?php echo !empty($message) ? $message : ''; ?>
        </div>

        <div class="card mb-6 bg-white shadow-lg">
            <div class="card-body p-6">
                <h2 class="text-2xl font-semibold mb-4 text-gray-700">Add New Item</h2>
                <form id="addItemForm" method="POST" action="index.php" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="mb-3 md:mb-0">
                        <label for="itemName" class="form-label text-gray-600">Item Name</label>
                        <input type="text" class="form-control p-2 border border-gray-300 focus:ring-blue-500 focus:border-blue-500" id="itemName" name="itemName" required>
                    </div>
                    <div class="mb-3 md:mb-0">
                        <label for="itemStock" class="form-label text-gray-600">Stock</label>
                        <input type="number" class="form-control p-2 border border-gray-300 focus:ring-blue-500 focus:border-blue-500" id="itemStock" name="itemStock" required min="0">
                    </div>
                    <div class="mb-3 md:mb-0">
                        <label for="itemPrice" class="form-label text-gray-600">Price ($)</label>
                        <input type="number" step="0.01" class="form-control p-2 border border-gray-300 focus:ring-blue-500 focus:border-blue-500" id="itemPrice" name="itemPrice" required min="0">
                    </div>
                    <div class="col-span-1 md:col-span-3 flex justify-end">
                        <input type="hidden" name="addItem" value="1">
                        <button type="submit" class="btn btn-primary px-6 py-2 text-lg font-medium">Add Item</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card bg-white shadow-lg">
            <div class="card-body p-6">
                <h2 class="text-2xl font-semibold mb-4 text-gray-700">Current Stock</h2>
                <div class="table-responsive">
                    <table class="table table-striped table-hover text-gray-700">
                        <thead>
                            <tr>
                                <th scope="col" class="py-3 px-4 text-left text-sm font-medium">#</th>
                                <th scope="col" class="py-3 px-4 text-left text-sm font-medium">Item Name</th>
                                <th scope="col" class="py-3 px-4 text-left text-sm font-medium">Stock</th>
                                <th scope="col" class="py-3 px-4 text-left text-sm font-medium">Price</th>
                            </tr>
                        </thead>
                        <tbody id="itemTableBody">
                            <?php if (empty($items)): ?>
                                <tr><td colspan="4" class="text-center py-4">No items in stock. Add some!</td></tr>
                            <?php else: ?>
                                <?php foreach ($items as $index => $item): ?>
                                    <tr>
                                        <td class="py-2 px-4"><?php echo $index + 1; ?></td>
                                        <td class="py-2 px-4"><?php echo htmlspecialchars($item['name']); ?></td>
                                        <td class="py-2 px-4"><?php echo htmlspecialchars($item['stock']); ?></td>
                                        <td class="py-2 px-4">$<?php echo htmlspecialchars(number_format($item['price'], 2)); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JavaScript for handling the message box display
        document.addEventListener('DOMContentLoaded', function() {
            const messageBox = document.getElementById('messageBox');
            // If the message box has content, make it visible and then hide after 3 seconds
            if (messageBox.textContent.trim() !== '') {
                messageBox.style.display = 'block';
                setTimeout(() => {
                    messageBox.style.display = 'none';
                    // Clear URL parameters for cleaner display after message is gone
                    const url = new URL(window.location.href);
                    url.searchParams.delete('msg');
                    url.searchParams.delete('type');
                    window.history.replaceState({}, document.title, url.toString());
                }, 3000);
            }
        });
    </script>
</body>
</html>
