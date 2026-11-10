<h1>Search events</h1>

<form method="GET" action="/events/search">
    <input type="text" name="q" value="{{ $term }}" placeholder="Search description">
    <button type="submit">Search</button>
</form>

<table border="1" cellpadding="4">
    <tr><th>Title</th><th>Venue</th><th>Description</th></tr>
    @foreach ($events as $event)
        <tr>
            <td>{{ $event->title }}</td>
            <td>{{ $event->venue->name }}</td>
            <td>{{ $event->description }}</td>
        </tr>
    @endforeach
</table>

<p><b>SQL:</b> {{ $sql }}</p>