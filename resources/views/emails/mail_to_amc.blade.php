<div>
    <p>Please execute the request for the creation of the following on T+0 dated {{ $ticket->created_at->format('jS F, Y') }} (Form enclosed):</p>

    <label>Date:</label>
    <span style="font-weight: bold;">{{ $ticket->created_at }}</span><br>

    <label>Investor Name:</label>
    <span style="font-weight: bold;">{{ $ticket->user->name }}</span><br>

    <label>AMC Name:</label>
    <span style="font-weight: bold;">{{ $ticket->security->amc->name }}</span><br>

    <label>Security Name:</label>
    <span style="font-weight: bold;">{{ $ticket->security->name }}</span><br><br>

    <table class="table table-bordered" style="border-collapse: collapse; width: 100%;">
        <thead>
            <tr>
                <th style="border: 1px solid #dddddd; text-align: left; padding: 8px;">Scheme</th>
                <th style="border: 1px solid #dddddd; text-align: left; padding: 8px;">Basket Size</th>
                <th style="border: 1px solid #dddddd; text-align: left; padding: 8px;">No. of Baskets</th>
                <th style="border: 1px solid #dddddd; text-align: left; padding: 8px;">Total Units</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border: 1px solid #dddddd; text-align: left; padding: 8px;">{{ $ticket->id }}</td>
                <td style="border: 1px solid #dddddd; text-align: left; padding: 8px;">{{ $ticket->basket_size }}</td>
                <td style="border: 1px solid #dddddd; text-align: left; padding: 8px;">{{ $ticket->basket_no }}</td>
                <td style="border: 1px solid #dddddd; text-align: left; padding: 8px;">{{ $ticket->basket_size * $ticket->basket_no }}</td>
            </tr>
        </tbody>
    </table>
</div>
