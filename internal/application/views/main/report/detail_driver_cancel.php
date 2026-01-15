<div class="content">
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Job Name</th>
                    <th>Driver Name</th>
                    <th>Job Type</th>
                    <th>Reason</th>
                    <th>Job Date</th>
                    <th>Cancel Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                foreach($job as $val): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= $val['JobName'] ?></td>
                    <td><?= $val['Fullname'] ?></td>
                    <td>
                        <?php
                            if($val['TypeJob'] == 1 ) {
                                echo 'Line Interrupt';
                            } elseif($val['TypeJob'] == 2) {
                                echo 'Reconnection';
                            } elseif($val['TypeJob'] == 3) {
                                echo 'Short Circuit';
                            }
                        ?>
                    </td>
                    <td><?= $val['Reason'] ?></td>
                    <td><?= return_date_format($val['JobDate']) ?></td>
                    <td><?= return_date_format($val['created_at']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>