<table border="2">
    <thead>
    <tr>
        <td>{{ 'Name' }}</td>
        <td>{{ 'Recipient name' }}</td>
        <td>{{ 'Amount' }}</td>
        <td>{{ 'Completed date' }}</td>
    </tr>
    </thead>
    <tbody >
    @foreach($items as $item)
        <tr>
            <td>{{ $item->name }}</td>
            <td>{{ $item->recipient_name }}</td>
            <td>{{ $item->amount / 100 }}</td>
            <td>{{ $item->completed }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
