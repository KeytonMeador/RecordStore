<?php
/* 
List Partial — records-list.php
    Call records_all() and render a table matching the visual above.
    Escape all visible text with an HTML escaping function to prevent XSS. Why: user-provided values should never be rendered raw in HTML.
*/
$records = records_all();
?>

<table>
    <thead>
        <tr>
            <th>Title</th>
            <th>Artist</th>
            <th>Format</th>
            <th>Price</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($records as $record): ?>
            <tr>
                <td><?= htmlspecialchars($record['title']) ?></td>
                <td><?= htmlspecialchars($record['artist']) ?></td>
                <td><?= htmlspecialchars($record['format_name']) ?></td>
                <td>$<?= number_format((float)$record['price'], 2) ?></td>
                                <td>
                                    <form method="post" class="d-inline">
                                        <input type="hidden" name="id" value="<?= (int)$record['id'] ?>">
                                        <input type="hidden" name="action" value="add_to_cart">
                                        <button class="btn btn-sm btn-outline-success">Add to Cart</button>
                                    </form>

                                    <form method="post" class="d-inline" onsubmit="return confirm('Delete this record?');">
                                        <input type="hidden" name="id" value="<?= (int)$record['id'] ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>