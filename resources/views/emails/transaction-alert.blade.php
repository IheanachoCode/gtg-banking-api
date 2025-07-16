

<div class="header" style="background-color:#7b7d8c;color:white; padding:18px;">
    <h2>GTG SAVINGS</h2>
</div>
<div id="me" style="background-color:#F5F5F5; height:auto;width:auto; padding:18px;">
    Dear {{ $data['customerName'] }};<br /><br />

    <center><span>TRANSACTION ALERT SERVICE</span></center>
    <p>We wish to inform you that the following transaction occurred against your account with us.</p>
    <span>TRANSACTION DETAILS:</span>
    <table class="table-striped">
        <tbody>
            <tr>
                <td>Transaction Type:</td>
                <td>{{ $data['txType'] }}</td>
            </tr>
            <tr>
                <td>Account No:</td>
                <td>{{ $data['accountNo'] }}</td>
            </tr>
            <tr>
                <td>Transaction Description:</td>
                <td>{{ $data['description'] }}</td>
            </tr>
            <tr>
                <td>Transaction Amount:</td>
                <td>{{ number_format($data['amount'], 2) }}</td>
            </tr>
            <tr>
                <td>Transaction date:</td>
                <td>{{ $data['txDate'] }}</td>
            </tr>
            <tr>
                <td>Balance:</td>
                <td>{{ number_format($data['balance'], 2) }}</td>
            </tr>
        </tbody>
    </table><br />

    For enquiries, please call our 24/7 customer service center on {{ config('app.support_phone') }}.
    You can also send us email at <a href="mailto:{{ config('app.support_email') }}">{{ config('app.support_email') }}</a><br /><br />

    Thank you for choosing our service
</div>
