<!DOCTYPE html>
<html>
<head>
    <title>Check Transaction Status</title>
</head>
<body>
    <h1>Check Transaction Status</h1>

    <form action="/transaction" method="POST">
        @csrf
        <label for="transaction_id">Transaction ID:</label>
        <input type="text" id="transaction_id" name="transaction_id" required>
        <button type="submit">Check Status</button>
    </form>

    @if(isset($status))
        <p>Transaction Status: {{ $status }}</p>
    @elseif(isset($error))
        <p>Error: {{ $error }}</p>
    @endif
</body>
</html>
