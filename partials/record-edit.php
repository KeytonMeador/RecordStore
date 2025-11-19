<?php
/* Edit Partial — record-edit.php
   Shows a form pre-filled with record data for editing.
*/

if (!isset($_GET['id'])) {
    echo "No id provided";
    return;
}

$id = (int) $_GET['id'];
$record = record_find($id);
if (!$record) {
    echo "Record not found";
    return;
}

$formats = formats_all();
?>

<form method="post" action="?view=edit">
    <input type="hidden" name="action" value="update">
    <input type="hidden" name="id" value="<?= (int)$record['id'] ?>">

    <label for="title">Title:</label>
    <input type="text" name="title" id="title" required value="<?= htmlspecialchars($record['title']) ?>">

    <label for="artist">Artist:</label>
    <input type="text" name="artist" id="artist" required value="<?= htmlspecialchars($record['artist']) ?>">

    <label for="price">Price:</label>
    <input type="number" name="price" id="price" step="0.01" required value="<?= htmlspecialchars($record['price']) ?>">

    <label for="format">Format:</label>
    <select name="format" id="format" required>
        <?php foreach ($formats as $format): ?>
            <option value="<?= (int)$format['id'] ?>" <?= ($format['id'] == $record['format_id']) ? 'selected' : '' ?>><?= htmlspecialchars($format['name']) ?></option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Update Record</button>
</form>
