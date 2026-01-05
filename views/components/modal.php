<?php

/**
 * REUSABLE MODAL COMPONENT
 * 
 * Usage:
 * $modalConfig = [
 *     'id'    => 'userModal',
 *     'title' => 'User Details',
 *     'size'  => 'modal-lg', // optional: modal-sm, modal-lg, modal-xl
 *     'form'  => ['action' => '/users/save', 'method' => 'POST', 'ajax' => true] // optional
 * ];
 * include 'path/to/modal.php';
 * 
 * Inside the included file, use these slots:
 * - $modalBody: The HTML content for the body
 * - $modalFooter: The HTML content for the footer (usually buttons)
 */

$modalId = $modalConfig['id'] ?? 'genericModal';
$modalTitle = $modalConfig['title'] ?? 'Modal Title';
$modalSize = $modalConfig['size'] ?? '';
$isForm = isset($modalConfig['form']);
$formAction = $modalConfig['form']['action'] ?? '';
$formMethod = $modalConfig['form']['method'] ?? 'POST';
$isAjaxForm = $modalConfig['form']['ajax'] ?? false;
?>

<div class="modal fade" id="<?= $modalId ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog <?= $modalSize ?>">
        <?php if ($isForm): ?>
            <form action="<?= $formAction ?>" method="<?= $formMethod ?>" <?= $isAjaxForm ? 'data-ajax' : '' ?> class="modal-content">
            <?php else: ?>
                <div class="modal-content">
                <?php endif; ?>

                <div class="modal-header">
                    <h5 class="modal-title"><?= $modalTitle ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <?= $modalBody ?? '<!-- Body Content Here -->' ?>
                </div>

                <div class="modal-footer">
                    <?php if (isset($modalFooter)): ?>
                        <?= $modalFooter ?>
                    <?php else: ?>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <?php if ($isForm): ?>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <?php if ($isForm): ?>
            </form>
        <?php else: ?>
    </div>
<?php endif; ?>
</div>
</div>