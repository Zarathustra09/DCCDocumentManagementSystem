<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Document Title</th>
            <th>Document No.</th>
            <th>Rev. No.</th>
            <th>Device Name</th>
            <th>Originator Name</th>
            <th>Customer</th>
            <th>Remarks</th>
            <th>Registration Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($entries as $entry)
            <tr>
                <td>{{ $entry->submitted_at ? $entry->submitted_at->format('Y-m-d H:i') : '' }}</td>
                <td>{{ $entry->document_title }}</td>
                <td>{{ $entry->document_no }}</td>
                <td>{{ $entry->revision_no }}</td>
                <td>{{ $entry->device_name ?? '' }}</td>
                <td>{{ $entry->originator_name }}</td>
                <td>{{ $entry->customer ? $entry->customer->code : '' }}</td>
                <td>{{ $entry->remarks ?? '' }}</td>
                <td>{{ ucfirst($entry->status->name ?? 'Unknown') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
