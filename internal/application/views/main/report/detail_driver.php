<div class="p-4">
    <div class="overflow-x-auto">
        <table class="w-full text-sm" data-paginated-table data-per-page="6" data-searchable>
            <thead>
                <tr>
                    <th class="bg-gray-50 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 border-b border-gray-200">No</th>
                    <th class="bg-gray-50 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 border-b border-gray-200">Driver Name</th>
                    <th class="bg-gray-50 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 border-b border-gray-200">Customer Name</th>
                    <th class="bg-gray-50 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 border-b border-gray-200">Job Name</th>
                    <th class="bg-gray-50 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 border-b border-gray-200">Job Type</th>
                    <th class="bg-gray-50 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 border-b border-gray-200">Job Date</th>
                    <th class="bg-gray-50 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 border-b border-gray-200">Assign Date</th>
                    <th class="bg-gray-50 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 border-b border-gray-200 <?= ($type_job == 1) ? 'hidden' : '' ?>">Finish Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                foreach($job as $val): ?>
                <tr class="border-b border-gray-100 hover:bg-gray-50">
                    <td class="px-4 py-3"><?= $no++; ?></td>
                    <td class="px-4 py-3"><?= $val['Fullname'] ?></td>
                    <td class="px-4 py-3"><?= $val['CustomerName'] ?></td>
                    <td class="px-4 py-3"><?= $val['JobName'] ?></td>
                    <td class="px-4 py-3">
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
                    <td class="px-4 py-3 whitespace-nowrap"><?= return_date_format_with_time($val['JobDate']) ?></td>
                    <td class="px-4 py-3 whitespace-nowrap"><?= return_date_format_with_time($val['AssignWhen']) ?></td>
                    <td class="px-4 py-3 whitespace-nowrap <?= ($type_job == 1) ? 'hidden' : '' ?>"><?= return_date_format_with_time($val['FinishWhen']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div data-pagination-controls class="mt-3 px-2"></div>
</div>
