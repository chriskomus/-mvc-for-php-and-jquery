<?php
/*
 * Copyright (c) 2022 Chris Komus - GNU GPLv3
 * https://github.com/chriskomus/
 */

?>

<?php if(isset($data['error_type'])): ?>
    <div class="alert alert-dismissible alert-<?= $data['error_type'] ?>">
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        <p class="mb-0"><?= $data['error_message'] ?></p>
    </div>
<?php endif; ?>