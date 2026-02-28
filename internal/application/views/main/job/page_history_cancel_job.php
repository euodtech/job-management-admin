<div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr class="whitespace-nowrap">
                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600 text-left">No</th>
                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600 text-left">Driver Name</th>
                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600 text-left">Reason</th>
                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600 text-left">Cancel Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            foreach($history as $val): ?>
            <tr class="border-b border-gray-100 hover:bg-gray-50">
                <td class="px-4 py-3"><?= $no++; ?></td>
                <td class="px-4 py-3"><?= $val['Fullname'] ?></td>
                <td class="px-4 py-3"><?= $val['Reason'] ?></td>
                <td class="px-4 py-3 whitespace-nowrap"><?= return_date_format_with_time($val['created_at']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
