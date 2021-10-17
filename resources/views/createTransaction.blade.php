<!DOCTYPE html>

<html>

<head>

    <title>{{ 'Create transaction' }}</title>

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.css" integrity="sha256-b5ZKCi55IX+24Jqn638cP/q3Nb2nlx+MH/vMMqrId6k=" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.26.0/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js" integrity="sha256-5YmaxAwMjIpMrVlK84Y/+NjCpKnFYa8bWWBbUHSBGfU=" crossorigin="anonymous"></script>
</head>
<body>

<div class="container">
    <form method="post">
        @csrf
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <div class="form-group">
                            <label for="sender">{{ 'Sender' }}</label>
                            <select
                                required="required"
                                class="form-control @error('sender') is-invalid @enderror"
                                name="sender"
                                id="sender"
                            >
                                <option>{{ 'Select sender' }}</option>
                                @foreach($senders as $sender)
                                    <option value="{{ $sender['id'] }}" {{ old('sender') == $sender['id'] ? "selected" :""}}>
                                        {{ $sender['name'] . " (Balance: " . $sender['balance'] . ")" }}
                                    </option>
                                @endforeach
                            </select>
                            @error('sender')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="recipient">{{ 'Recipient' }}</label>
                            <select
                                required="required"
                                class="form-control @error('recipient') is-invalid @enderror"
                                name="recipient"
                                id="recipient"
                               >
                            </select>
                            @error('recipient')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="amount">{{ 'Amount' }}</label>
                            <input
                                required="required"
                                type="text"
                                min="1"
                                class="form-control @error('amount') is-invalid @enderror"
                                name="amount"
                                value="{{ old('amount') }}"
                                maxlength="127"
                                id="amount"
                            >
                            @error('amount')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="datetime">{{ 'Send datetime' }}</label>
                            <input
                                required="required"
                                type="text"
                                class="form-control datetimepicker @error('datetime') is-invalid @enderror"
                                name="datetime"
                                value="{{ old('datetime') }}"
                            >
                            @error('datetime')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            {{ 'Submit' }}
                        </button>

                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <table class="table" >
                <thead>
                <tr>
                    <td>{{ 'Name' }}</td>
                    <td>{{ 'Recipient name' }}</td>
                    <td>{{ 'Amount' }}</td>
                    <td>{{ 'Completed date' }}</td>
                </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->completed ? $user->recipient_name : '' }}</td>
                        <td>{{ $user->completed ? $user->amount : '' }}</td>
                        <td>{{ $user->completed ?: '' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $("#sender").change(function () {
        var id_sender = $(this).val();
        var token = $("input[name='_token']").val();

        $.ajax({
            url: "{{ route('select_recipient') }}",
            method: 'POST',
            data: {id_sender: id_sender, _token: token},
            success: function (data) {
                $("#recipient").html('');
                $("#recipient").html(data.options);
            }
        });

        $.ajax({
            url: "{{ route('sender_max_amount') }}",
            method: 'POST',
            data: {id_sender: id_sender, _token: token},
            success: function (data) {
                $("#amount").attr({
                    "max" : data.max_amount
                });
            }
        });
    });

    $(function () {
        $( "#amount" ).change(function() {
            var max = parseInt($(this).attr('max'));
            var min = parseInt($(this).attr('min'));
            if ($(this).val() > max)
            {
                $(this).val(max);
            }
            else if ($(this).val() < min)
            {
                $(this).val(min);
            }
        });
    });

    $(function () {
        $('.datetimepicker').datetimepicker({
            sideBySide: true,
            format: 'DD/MM/YYYY HH',
            minDate: moment().add(1, 'hours'),
        });
    });

</script>

</body>
</html>
