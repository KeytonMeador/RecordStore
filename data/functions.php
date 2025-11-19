<?php
require_once __DIR__ . '/db.php';
/* 
formats_all(): array

Return an array id and name from formats, ordered by name.

On index.php, label output as Unit Test 1 — Formats and print the returned formats
    cd, 8 track, mp4, 45, 728 
*/
function formats_all(): array
{
    $pdo = get_pdo();
    $stmt = $pdo->query('SELECT id, name FROM formats ORDER BY name ASC');
    return $stmt->fetchAll();
}


/* 
records_all(): array

Use a prepared SELECT with a JOIN so each record shows title, artist, price, and format name.

On index.php, label output as Unit Test 2 — Records JOIN and print first 3 lines like:
    Abbey Road — 45 - $19.99
*/
function records_all(): array
{
    $pdo = get_pdo();
    // Join formats to get the format name
    $sql = 'SELECT records.id, records.title, records.artist, records.price, formats.name AS format_name
            FROM records
            JOIN formats ON records.format_id = formats.id
            ORDER BY records.id DESC';
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

/*
record_insert(): void
Create these variables inside the function for now: $title, $artist, $price, $format_id.

Use a prepared INSERT with named placeholders (:title, :artist, etc.).

After execution, check how many rows were affected with $stmt->rowCount(). It returns the number of rows changed — if one record was added, it should be 1.

On index.php, label output as Unit Test 3 — Insert and echo something like:
    Insert success: true, rows: 1
    
Then call records_all() again to confirm your new record appears at the top.
*/

function record_insert(): void
{
    // Only perform an insert when a POST create action is present
    $is_post_create = ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create');
    if (!$is_post_create) {
        // do nothing when called without POST create
        return;
    }

    // Collect and validate POSTed values
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $artist = isset($_POST['artist']) ? trim($_POST['artist']) : '';
    $price = isset($_POST['price']) ? trim($_POST['price']) : '';
    // form field is named "format" in the form partial
    $format_id = isset($_POST['format']) ? (int) $_POST['format'] : 0;

    // Basic validation
    if ($title === '' || $artist === '' || $price === '' || $format_id <= 0) {
        echo "Insert success: false, reason: missing required fields";
        return;
    }

    if (!is_numeric($price)) {
        echo "Insert success: false, reason: invalid price";
        return;
    }

    $price = (float) $price;

    $pdo = get_pdo();
    $stmt = $pdo->prepare('INSERT INTO records (title, artist, price, format_id) VALUES (:title, :artist, :price, :format_id)');

    $stmt->execute([
        ':title' => $title,
        ':artist' => $artist,
        ':price' => $price,
        ':format_id' => $format_id,
    ]);

    if ($stmt->rowCount() === 1) {
        echo "Insert success: true, rows: 1";
    } else {
        echo "Insert success: false, rows: 0";
    }
}

// helper: demo insert (kept separate if you want to seed during tests)
function record_insert_demo(): void
{
    $pdo = get_pdo();
    $stmt = $pdo->prepare('INSERT INTO records (title, artist, price, format_id) VALUES (:title, :artist, :price, :format_id)');
    $stmt->execute([
        ':title' => 'Demo Title',
        ':artist' => 'Demo Artist',
        ':price' => 9.99,
        ':format_id' => 1,
    ]);
}
/*
Creates new user in the users table

@param string $username
@param string $full_name
@param string $hash

@return void
*/

function user_create(string $username, string $full_name, string $hash): void {
    $pdo = get_pdo();
    $sql = "INSERT INTO users (username, full_name, password_hash)
            VALUES (:u, :f, :p)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':u'=>$username, ':f'=>$full_name, ':p'=>$hash]);
}

/*
Finds a user by their username and returns an associative array of their data or null if not found.

@param string $username

@return array|null
*/

function user_find_by_username(string $username): ?array {
    $pdo = get_pdo();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :u");
    $stmt->execute([':u'=>$username]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

/*
Find multiple records by an array of ids

@param array $ids

@return array
*/

function records_by_ids(array $ids): array {
    if (empty($ids)) return [];
    $pdo = get_pdo();
    $ph = implode(',', array_fill(0, count($ids), '?'));
    $sql = "SELECT r.id, r.title, r.artist, r.price, f.name
            FROM records r
            JOIN formats f ON r.format_id = f.id
            WHERE r.id IN ($ph)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($ids);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/*
Creates a purchase record in the purchases table

@param int $user_id
@param int $record_id

@return void
*/

function purchase_create(int $user_id, int $record_id): void {
    $pdo = get_pdo();
    $sql = "INSERT INTO purchases (user_id, record_id, purchase_date)
            VALUES (:u, :r, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':u'=>$user_id, ':r'=>$record_id]);
}

/**
 * Find a single record by id and return it (includes format_name)
 */
function record_find(int $id)
{
    $pdo = get_pdo();
    $sql = 'SELECT records.id, records.title, records.artist, records.price, records.format_id, formats.name AS format_name
            FROM records
            JOIN formats ON records.format_id = formats.id
            WHERE records.id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}

/**
 * Update a record using POST data when available. Expects POST['action'] === 'update' and POST['id'] set.
 */
function record_update(): void
{
    if (!isset($_POST['id'])) {
        echo "Update success: false, reason: missing id";
        return;
    }

    $id = (int) $_POST['id'];
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $artist = isset($_POST['artist']) ? trim($_POST['artist']) : '';
    $price = isset($_POST['price']) ? trim($_POST['price']) : '';
    $format_id = isset($_POST['format']) ? (int) $_POST['format'] : 0;

    if ($title === '' || $artist === '' || $price === '' || $format_id <= 0) {
        echo "Update success: false, reason: missing required fields";
        return;
    }

    if (!is_numeric($price)) {
        echo "Update success: false, reason: invalid price";
        return;
    }

    $price = (float) $price;

    $pdo = get_pdo();
    $sql = 'UPDATE records SET title = :title, artist = :artist, price = :price, format_id = :format_id WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':title' => $title,
        ':artist' => $artist,
        ':price' => $price,
        ':format_id' => $format_id,
        ':id' => $id,
    ]);

    if ($stmt->rowCount() === 1) {
        echo "Update success: true, rows: 1";
    } else {
        echo "Update success: false, rows: 0";
    }
}

/**
 * Delete a record by id. Reads POST['id'] when called from a form.
 */
function record_delete(): void
{
    if (!isset($_POST['id'])) {
        echo "Delete success: false, reason: missing id";
        return;
    }

    $id = (int) $_POST['id'];
    $pdo = get_pdo();
    $stmt = $pdo->prepare('DELETE FROM records WHERE id = :id');
    $stmt->execute([':id' => $id]);

    if ($stmt->rowCount() === 1) {
        echo "Delete success: true, rows: 1";
    } else {
        echo "Delete success: false, rows: 0";
    }
}
