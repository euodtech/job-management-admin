<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>No</th>
            <th>Driver Name</th>
            <th>Reason</th>
            <th>Cancel Date</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1;
        foreach($history as $val): ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= $val['Fullname'] ?></td>
            <td><?= $val['Reason'] ?></td>
            <td><?= return_date_format($val['created_at']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>