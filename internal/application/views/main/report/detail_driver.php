<div class="content">
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Driver Name</th>
                    <th>Customer Name</th>
                    <th>Job Name</th>
                    <th>Job Type</th>
                    <th>Job Date</th>
                    <th>Assign Date</th>
                    <th class="<?= ($type_job == 1) ? "d-none" :  "" ?>" >Finish Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                foreach($job as $val): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= $val['Fullname'] ?></td>
                    <td><?= $val['CustomerName'] ?></td>
                    <td><?= $val['JobName'] ?></td>
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
                    <td style="white-space: nowrap;"><?= return_date_format($val['JobDate']) ?></td>
                    <td style="white-space: nowrap;"><?= return_date_format($val['AssignWhen']) ?></td>
                    <td class="<?= ($type_job == 1) ? "d-none" :  "" ?>"><?= return_date_format($val['FinishWhen']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>