<div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr class="whitespace-nowrap">
                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600 text-left">No</th>
                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600 text-left">Create Job</th>
                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600 text-left">Reschedule Job</th>
                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600 text-left">Reason</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            foreach($history as $val): ?>
            <tr class="border-b border-gray-100 hover:bg-gray-50">
                <td class="px-4 py-3"><?= $no++; ?></td>
                <td class="px-4 py-3 whitespace-nowrap"><?= return_date_format($val['CurrentDateJob']) ?></td>
                <td class="px-4 py-3 whitespace-nowrap"><?= return_date_format($val['RescheduledDateJob'])?></td>
                <td class="px-4 py-3"><?= $val['Reason']?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
