<?php
/* 
Create Partial — record-form.php

    Inputs: title (text), artist (text), price (number with step), and a format dropdown.
    Populate the dropdown from the formats_all() function. (No optional second dropdown for this lab.)
    Include a hidden field to signal the create action.
    Submitting the form triggers the create action; after insert, show the record-created.php partial.

*/

$formats = formats_all();

?>
<form method="post" action="?view=create">
    <input type="hidden" name="action" value="create">
    <label for="title">Title:</label>
    
    <input type="text" name="title" id="title" required>
    <label for="artist">Artist:</label>
    
    <input type="text" name="artist" id="artist" required>
    <label for="price">Price:</label>
    
    <input type="number" name="price" id="price" step="0.01" required>
    <label for="format">Format:</label>
    
    <select name="format" id="format" required>
        <?php foreach ($formats as $format): ?>
            <option value="<?= htmlspecialchars($format['id']) ?>"><?= htmlspecialchars($format['name']) ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Create Record</button>
</form>
