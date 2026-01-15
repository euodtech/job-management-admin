<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>No</th>
            <th>Create Job</th>
            <th>Reschedule Job</th>
            <th>Reason</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1;
        foreach($history as $val): ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= return_date_format($val['CurrentDateJob']) ?></td>
            <td><?= return_date_format($val['RescheduledDateJob'])?></td>
            <td><?= $val['Reason']?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>