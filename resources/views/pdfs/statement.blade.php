<!-- filepath: resources/views/pdfs/statement.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Account Statement</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #04AA6D; color: white; }
        .header-table td { background-color: #ddd; color: black; }
    </style>
</head>
<body>
    <center>
        <h4><b>ACCOUNT STATEMENT</b></h4>
        <span>Transaction Date: From <b id="from">{{ $fromdate }}</b> to <b id="tod">{{ $todate }}</b></span><br />
        <img src="https://glorytogloryfortune.com/images/gtgLOGO2.png" height="50px" width="70px" alt="image">
    </center>
    <br>
    <table class="header-table" style="width:70%;margin:auto;">
        <tr>
            <td>Account Name:</td>
            <td>{{ $lastname }} {{ $othernames }}</td>
        </tr>
        <tr>
            <td>Account Type:</td>
            <td>{{ $account_type }}</td>
        </tr>
        <tr>
            <td>Account Number:</td>
            <td>{{ $account_no }}</td>
        </tr>
        <tr>
            <td>Debit:</td>
            <td>{{ number_format($Debit, 2) }}</td>
        </tr>
        <tr>
            <td>Credit:</td>
            <td>{{ number_format($Credit, 2) }}</td>
        </tr>
        <tr>
            <td>Available Balance:</td>
            <td>{{ number_format($AvailableBal, 2) }}</td>
        </tr>
        <tr>
            <td>Ledger Balance:</td>
            <td>{{ number_format($mainBalance, 2) }}</td>
        </tr>
    </table>
    <br>
    <center><h4><b>TRANSACTIONS</b></h4></center>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Tx_type</th>
                <th>Description</th>
                <th>Credit</th>
                <th>Debit</th>
                <th>Balance</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $row)
                <tr>
                    <td>{{ $row['transaction_date'] }}</td>
                    <td>{{ $row['transaction_type'] }}</td>
                    <td>{{ $row['description'] }}</td>
                    <td>
                        @if($row['transaction_type'] == 'Credit')
                            {{ number_format($row['amount'], 2) }}
                        @else
                            --------
                        @endif
                    </td>
                    <td>
                        @if($row['transaction_type'] == 'Debit')
                            {{ number_format($row['amount'], 2) }}
                        @else
                            --------
                        @endif
                    </td>
                    <td>{{ number_format($row['current_balance'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>