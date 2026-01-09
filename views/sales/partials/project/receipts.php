<div class="d-flex justify-content-end mb-3">
    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addReceiptModal"> <i class="bi bi-plus"></i> Add Receipt</button>
</div>
<table class="table datatable">
    <thead>
        <tr>
            <th>Date</th>
            <th>Ref</th>
            <th>Amount</th>
            <th>Mode</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($receipts as $r): ?>
            <tr>
                <td><?= date('d M Y', strtotime($r['payment_date'])) ?></td>
                <td><?= $r['transaction_ref'] ?></td>
                <td class="text-success fw-bold">₹<?= number_format($r['amount']) ?></td>
                <td><?= $r['payment_mode'] ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($receipts)): ?>
            <tr>
                <td colspan="4" class="text-center text-muted">No receipts recorded.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>