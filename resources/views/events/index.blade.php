<h1>Events ({{ $mode }})</h1>

<table border="1" cellpadding="4">
    <tr><th>Title</th><th>Venue</th><th>Participants</th></tr>
    @foreach ($events as $event)
        <tr>
            <td>{{ $event->title }}</td>
            <td>{{ $event->venue->name }}</td>
            <td>{{ $mode === 'fixed' ? $event->participants_count : $event->participants->count() }}</td>
        </tr>
    @endforeach
</table>

<p><b>Queries run:</b> {{ count(\DB::getQueryLog()) }}</p>
<p><b>SQL:</b> {{ $sql }}</p>   