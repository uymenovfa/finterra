@if(!empty($recipients))
    @foreach($recipients as $recipient)
        <option value="{{ $recipient['id'] }}">{{ $recipient['name'] }}</option>
    @endforeach
@endif
