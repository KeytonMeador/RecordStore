<?php
include __DIR__ . "/data/db.php";
include __DIR__ . "/data/functions.php";

session_start();

$view   = filter_input(INPUT_GET, 'view') ?: 'list';
$action = filter_input(INPUT_POST, 'action');

// This Function checks if the user is logged in, if not, redirects to login page
function require_login(): void {
    if (empty($_SESSION['user_id'])) {
        header('Location: ?view=login');
        exit;
    }
}

$public_views   = ['login', 'register'];
$public_actions = ['login', 'register'];

if ($action && !in_array($action, $public_actions, true)) {
    require_login();
}

if (!$action && !in_array($view, $public_views, true)) {
    require_login();
}

include __DIR__ . '/components/nav.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Store</title>
    <link rel="stylesheet" href="assets/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <h2>Unit Test 1 — Formats</h2>
    <?php
    $formats = formats_all();
    $format_names = [];
    foreach ($formats as $f) {
        $format_names[] = $f['name'];
    }
    $out = '';
    foreach ($format_names as $i => $name) {
        if ($i > 0) $out .= ', ';
        $out .= $name;
    }
    echo 'Formats: ' . $out;
    ?>
    <hr>

    <h2>Unit Test 2 — Records JOIN</h2>
    <?php
    $records = records_all();
    $lines = [];
    for ($i = 0; $i < 3 && $i < count($records); $i++) {
        $r = $records[$i];
        $lines[] = "{$r['title']} — {$r['format_name']} - \${$r['price']}";
    }
    foreach ($lines as $line) {
        echo $line . "\n";
    }
    ?>
    <hr>

    <h2>Unit Test 3 — Insert</h2>
    <?php
    $insert_data = [
        'title' => 'Demo Title',
        'format_id' => 1,
        'artist' => 'Demo Artist',
        'price' => 9.99
    ];

    // Note: record_insert() now only runs on POST create. To test insert, use the Create form.
    $records = records_all();
    if (count($records) > 0) {
        $newest = $records[0];
        echo " \n Newest: {$newest['title']} - {$newest['format_name']}";
    } else {
        echo "No records yet. Use Create to add one.";
    }

    ?>
    <hr>

</body>

<!-- Routing — All views on index.php

    Read a view value from the URL. Default to list.
    Read a hidden action from the form on POST. When it is create, insert the record then set view to created.
    Based on view, include the matching partial from partials/.
-->

<?php
// handle actions
switch ($action) {
    
    // This case handels user login, it is different from register because it verifies existing users
    case 'login':
        $username = trim((string)($_POST['username'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        if ($username && $password) {
            $user = user_find_by_username($username);
            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = (int)$user['id'];
                $_SESSION['full_name'] = $user['full_name'];
                $view = 'list';
            } else {
                $login_error = "Invalid username or password.";
                $view = 'login';
            }
        } else {
            $login_error = "Enter both fields.";
            $view = 'login';
        }
        break;

    case 'logout':
        $_SESSION = [];
        session_destroy();
        session_start();
        $view = 'login';
        break;
        
    // This case handels user registration, it is different from login because it creates new users
    case 'register':
        $username  = trim((string)($_POST['username'] ?? ''));
        $full_name = trim((string)($_POST['full_name'] ?? ''));
        $password  = (string)($_POST['password'] ?? '');
        $confirm   = (string)($_POST['confirm_password'] ?? '');

        if ($username && $full_name && $password && $password === $confirm) {
            $existing = user_find_by_username($username);
            if ($existing) {
                $register_error = "That username already exists.";
                $view = 'register';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                user_create($username, $full_name, $hash);

                $user = user_find_by_username($username);
                $_SESSION['user_id'] = (int)$user['id'];
                $_SESSION['full_name'] = $user['full_name'];
                $view = 'list';
            }
        } else {
            $register_error = "Complete all fields and match passwords.";
            $view = 'register';
        }
        break;

    case 'add_to_cart':
        require_login();
        $record_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if ($record_id) {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
            $_SESSION['cart'][] = $record_id;
        }
        $view = 'list';
        break;

    case 'checkout':
        require_login();
        $cart_ids = $_SESSION['cart'] ?? [];

        if ($cart_ids) {
            foreach ($cart_ids as $rid) {
                purchase_create((int)$_SESSION['user_id'], (int)$rid);
            }
            $_SESSION['cart'] = [];
        }
        $view = 'checkout_success';
        break;

    case 'create':
        record_insert();
        $view = 'created';
        break;

    case 'update':
        record_update();
        $view = 'updated';
        break;

    case 'delete':
        record_delete();
        $view = 'deleted';
        break;

    default:
        // no action
        break;
}

if ($view === 'cart') {
    $cart_ids = $_SESSION['cart'] ?? [];
    $records_in_cart = records_by_ids($cart_ids);
}
?>

<body class="bg-light">
    <div class="container py-4">
        <?php
        if ($view === 'login') {
            include __DIR__ . '/partials/login_form.php';
        }
        elseif ($view === 'register') {
            include __DIR__ . '/partials/register_form.php';
        }
        elseif ($view === 'cart') {
            include __DIR__ . '/partials/cart.php';
        }
        elseif ($view === 'checkout_success') {
            include __DIR__ . '/partials/checkout_success.php';
        }
        elseif ($view === 'list') {
            include __DIR__ . '/partials/records-list.php';
        }
        elseif ($view === 'create') {
            include __DIR__ . '/partials/record-form.php';
        }
        elseif ($view === 'created') {
            include __DIR__ . '/partials/record-created.php';
        }
        elseif ($view === 'deleted') {
            include __DIR__ . '/partials/record-deleted.php';
        }
        ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>