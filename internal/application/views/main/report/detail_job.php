<div class="content">
    <h6 style="font-weight: bold;">Job Information :</h6>
    <div class="table-responsive">
        <table class="table table-borderless">
            <tbody>
                <tr>
                    <th style="width: 30%">Job Name</th>
                    <td>: <?= $detail[0]['JobName'] ?></td>
                </tr>
                <tr>
                    <th>Type Job</th>
                    <td>: <?= $detail[0]['TypeJob'] ?></td>
                </tr>
                <tr>
                    <th>Job Create</th>
                    <td>: <?= return_date_format($detail[0]['created_at']) ?></td>
                </tr>
                <tr>
                    <th>Job Date</th>
                    <td>: <?= return_date_format($detail[0]['JobDate']) ?></td>
                </tr>
            </tbody>
        </table>

    </div>
</div>
<?php if(count($detail[0]['StatusCancelJob']) > 0): ?>
<div class="content">
    <h6 style="font-weight: bold;" class="text-danger">History Cancel Job:</h6>
    <div class="table-responsive">
        <?php foreach($detail[0]['StatusCancelJob'] as $val): ?>
            <table class="table table-borderless">
                <tbody>
                    <tr>
                        <th style="width: 30%" class="text-danger">Driver Name</th>
                        <td>: <?= $val['Fullname'] ?></td>
                    </tr>
                    <tr>
                        <th class="text-danger">Cancel Time</th>
                        <td>: <?= return_date_format_detail($val['created_at']) ?></td>
                    </tr>
                    <tr>
                        <th class="text-danger">Reason</th>
                        <td>: <?= $val['Reason'] ?></td>
                    </tr>
                </tbody>
            </table>
        <?php endforeach; ?>

    </div>
</div>
<?php endif; ?>
<hr>
<div class="content">
    <h6 style="font-weight: bold;">Customer Information :</h6>
    <div class="table-responsive">
        <table class="table table-borderless">
            <tbody>
                <tr>
                    <th style="width: 30%">Customer Name</th>
                    <td>: <?= $detail[0]['CustomerName'] ?></td>
                </tr>
                <tr>
                    <th>Customer Address</th>
                    <td>: <?= $detail[0]['Address'] ?></td>
                </tr>
                <tr>
                    <th>Customer Contact</th>
                    <td>: <?= $detail[0]['PhoneNumber'] ?></td>
                </tr>
            </tbody>
        </table>

    </div>
</div>
<hr>
<div class="content">
    <h6 style="font-weight: bold;">Driver Information :</h6>
    <div class="table-responsive">
        <table class="table table-borderless">
            <tbody>
                <tr>
                    <th style="width: 30%">Driver Name</th>
                    <td>: <?= $detail[0]['Fullname'] ?></td>
                </tr>
                <tr>
                    <th>Driver Contact</th>
                    <td>: <?= $detail[0]['Email'] ?> - <?= $detail[0]['PhoneNumber'] ?></td>
                </tr>
                <tr>
                    <th>Get Job</th>
                    <td>: <?= return_date_format_detail($detail[0]['AssignWhen']) ?></td>
                </tr>
                <tr>
                    <th>Finished Job</th>
                    <td>: <?= return_date_format_detail($detail[0]['FinishWhen']) ?></td>
                </tr>
            </tbody>
        </table>

    </div>
</div>
<hr>
<div class="content">
    <h6 style="font-weight: bold;">Assets Job :</h6>
    <div class="row">
        <?php foreach($detail[0]['AssetsJob'] as $val): ?>
        <div class="col-md-6">
            <img src="<?= $val['Photo'] ?>" style="width: 100%;" alt="">
        </div>
        <?php endforeach; ?>
    </div>
</div>

