<div class="p-4">
    <h6 class="font-bold text-gray-800 mb-3">Job Information :</h6>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <tbody>
                <tr>
                    <th class="px-4 py-2 text-left text-gray-600 w-[30%]">Job Name</th>
                    <td class="px-4 py-2">: <?= $detail[0]['JobName'] ?></td>
                </tr>
                <tr>
                    <th class="px-4 py-2 text-left text-gray-600">Type Job</th>
                    <td class="px-4 py-2">: <?= $detail[0]['TypeJob'] ?></td>
                </tr>
                <tr>
                    <th class="px-4 py-2 text-left text-gray-600">Job Create</th>
                    <td class="px-4 py-2">: <?= return_date_format_with_time($detail[0]['created_at']) ?></td>
                </tr>
                <tr>
                    <th class="px-4 py-2 text-left text-gray-600">Job Date</th>
                    <td class="px-4 py-2">: <?= return_date_format_with_time($detail[0]['JobDate']) ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<?php if(count($detail[0]['StatusCancelJob']) > 0): ?>
<div class="p-4">
    <h6 class="font-bold text-red-600 mb-3">History Cancel Job:</h6>
    <div class="overflow-x-auto">
        <?php foreach($detail[0]['StatusCancelJob'] as $val): ?>
            <table class="w-full text-sm mb-3">
                <tbody>
                    <tr>
                        <th class="px-4 py-2 text-left text-red-600 w-[30%]">Driver Name</th>
                        <td class="px-4 py-2">: <?= $val['Fullname'] ?></td>
                    </tr>
                    <tr>
                        <th class="px-4 py-2 text-left text-red-600">Cancel Time</th>
                        <td class="px-4 py-2">: <?= return_date_format_detail($val['created_at']) ?></td>
                    </tr>
                    <tr>
                        <th class="px-4 py-2 text-left text-red-600">Reason</th>
                        <td class="px-4 py-2">: <?= $val['Reason'] ?></td>
                    </tr>
                </tbody>
            </table>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>
<hr class="border-gray-200">
<div class="p-4">
    <h6 class="font-bold text-gray-800 mb-3">Customer Information :</h6>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <tbody>
                <tr>
                    <th class="px-4 py-2 text-left text-gray-600 w-[30%]">Customer Name</th>
                    <td class="px-4 py-2">: <?= $detail[0]['CustomerName'] ?></td>
                </tr>
                <tr>
                    <th class="px-4 py-2 text-left text-gray-600">Customer Address</th>
                    <td class="px-4 py-2">: <?= $detail[0]['Address'] ?></td>
                </tr>
                <tr>
                    <th class="px-4 py-2 text-left text-gray-600">Customer Contact</th>
                    <td class="px-4 py-2">: <?= $detail[0]['PhoneNumber'] ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<hr class="border-gray-200">
<div class="p-4">
    <h6 class="font-bold text-gray-800 mb-3">Driver Information :</h6>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <tbody>
                <tr>
                    <th class="px-4 py-2 text-left text-gray-600 w-[30%]">Driver Name</th>
                    <td class="px-4 py-2">: <?= $detail[0]['Fullname'] ?></td>
                </tr>
                <tr>
                    <th class="px-4 py-2 text-left text-gray-600">Driver Contact</th>
                    <td class="px-4 py-2">: <?= $detail[0]['Email'] ?> - <?= $detail[0]['PhoneNumber'] ?></td>
                </tr>
                <tr>
                    <th class="px-4 py-2 text-left text-gray-600">Get Job</th>
                    <td class="px-4 py-2">: <?= return_date_format_detail($detail[0]['AssignWhen']) ?></td>
                </tr>
                <tr>
                    <th class="px-4 py-2 text-left text-gray-600">Finished Job</th>
                    <td class="px-4 py-2">: <?= return_date_format_detail($detail[0]['FinishWhen']) ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<?php if(!empty($detail[0]['Notes'])): ?>
<hr class="border-gray-200">
<div class="p-4">
    <h6 class="font-bold text-gray-800 mb-3">Rider Notes :</h6>
    <div class="bg-gray-50 rounded-lg border border-gray-200 px-4 py-3">
        <p class="text-sm text-gray-700 whitespace-pre-line"><?= htmlspecialchars($detail[0]['Notes']) ?></p>
    </div>
</div>
<?php endif; ?>
<hr class="border-gray-200">
<div class="p-4">
    <h6 class="font-bold text-gray-800 mb-3">Assets Job :</h6>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <?php foreach($detail[0]['AssetsJob'] as $val): ?>
        <div>
            <img src="<?= $this->config->item('base_api_url') . $val['Photo'] ?>" class="w-full rounded-lg shadow-sm" alt="">
        </div>
        <?php endforeach; ?>
    </div>
</div>
