@if($ticket->security->amc->id == 1 || $ticket->security->amc->id == 7 || $ticket->security->amc->id == 12)
<div>
    <p> Please execute request for Additional <strong>{{ $ticket->type == 1 ? 'Creation' : 'Redemption' }}</strong> of the following on T+0, dated {{ $ticket->created_at->format('jS F, Y') }} (Form enclosed):</p>

    <div>
        <strong> {{ $ticket->type == 1 ? 'Creation' : 'Redemption' }} </strong>
    </div>

    <div style="padding-top: 1rem;padding-bottom: 2rem;">
        Date : {{ $ticket->created_at->format('jS F, Y') }}<br/>
        Investor Name : KANJALOCHANA FINSERVE PRIVATE LIMITED<br/>
        NSDL DP ID : IN300327<br/>
        Client ID : 10619369
    </div>

    <table class="table table-bordered" style="border-collapse: collapse; width: 100%;padding-top: 3rem;">
        <thead>
            <tr style="align-items: center;">
                <th style="border: 1px solid #dddddd;padding: 8px;">Scheme</th>
                <th style="border: 1px solid #dddddd;padding: 8px;">Basket Size</th>
                <th style="border: 1px solid #dddddd;padding: 8px;">No. of Baskets</th>
                <th style="border: 1px solid #dddddd;padding: 8px;">Total Units</th>
            </tr>
        </thead>
        <tbody>
            <tr style="align-items: center;">
                <td style="border: 1px solid #dddddd;padding: 8px;">{{ $ticket->security->name }}</td>
                <td style="border: 1px solid #dddddd;padding: 8px;">{{ $ticket->basket_size }}</td>
                <td style="border: 1px solid #dddddd;padding: 8px;">{{ $ticket->basket_no }}</td>
                <td style="border: 1px solid #dddddd;padding: 8px;">{{ $ticket->basket_size * $ticket->basket_no }}</td>
            </tr>
        </tbody>
    </table>

    <div style="padding-top: 3rem;">
        <u> Bank Details :</u><br/>
        Bank Name : ICICI BANK<br/>
        Branch : VAKOLA<br/>
        IFSC Code : ICIC0001200<br/>
        Account Number : 120005002164
    </div>

    <div style="padding-top: 3rem;">
	   <!-- SHOW it in all cash CASES -->
	   @if($ticket->payment_type != 2 )
		<!-- show it in all BUY cases only --> 
		@if($ticket->type != 2)  
          <strong><u> Payment details mentioned below:</u></strong>
          <div><strong>{{$ticket->utr_no}} : {{$ticket->total_amt ?? 'N/A'}}</strong></div>
	    @endif
	   @endif
        <div style="padding-top: 2rem;">
            With Regards,<br/>
            ETF Operations Team<br/>
            <img src="{{ asset('assets/img/mail-logo.jpg') }}" alt="logo" />
        </div>
    </div>
</div>
@elseif ($ticket->security->amc->id == 2 || $ticket->security->amc->id == 8 || $ticket->security->amc->id == 5)
<div>
    <p> Please execute request for Additional <strong>{{ $ticket->type == 1 ? 'Creation' : 'Redemption' }}</strong> of the following on T+0, dated {{ $ticket->created_at->format('jS F, Y') }} (Form enclosed):</p>

    <div>
        <strong> {{ $ticket->type == 1 ? 'Creation' : 'Redemption' }} </strong>
    </div>

    <div style="padding-top: 1rem;padding-bottom: 2rem;">
        Date : {{ $ticket->created_at->format('jS F, Y') }}<br/>
        Investor Name : KANJALOCHANA FINSERVE PRIVATE LIMITED<br/>
        PAN : AAGCK6770Q<br/>
        CDSL DP ID : 16014300<br/>
        Client ID : 00006241
    </div>

    <table class="table table-bordered" style="border-collapse: collapse; width: 100%;padding-top: 3rem;">
        <thead>
            <tr style="align-items: center;">
                <th style="border: 1px solid #dddddd;padding: 8px;">Scheme</th>
                <th style="border: 1px solid #dddddd;padding: 8px;">Basket Size</th>
                <th style="border: 1px solid #dddddd;padding: 8px;">No. of Baskets</th>
                <th style="border: 1px solid #dddddd;padding: 8px;">Total Units</th>
            </tr>
        </thead>
        <tbody>
            <tr style="align-items: center;">
                <td style="border: 1px solid #dddddd;padding: 8px;">{{ $ticket->security->name }}</td>
                <td style="border: 1px solid #dddddd;padding: 8px;">{{ $ticket->basket_size }}</td>
                <td style="border: 1px solid #dddddd;padding: 8px;">{{ $ticket->basket_no }}</td>
                <td style="border: 1px solid #dddddd;padding: 8px;">{{ $ticket->basket_size * $ticket->basket_no }}</td>
            </tr>
        </tbody>
    </table>

    <div style="padding-top: 3rem;">
        <u> Bank Details :</u><br/>
        Bank Name : ICICI BANK<br/>
         Branch : NARIMAN POINT<br/>
        IFSC Code : ICIC0000004<br/>
        Account Number : 000405133274
    </div>

    <div style="padding-top: 3rem;">
	   <!-- SHOW it in all cash CASES -->
	   @if($ticket->payment_type != 2 )
		<!-- show it in all BUY cases only --> 
		@if($ticket->type != 2)  
          <strong><u> Payment details mentioned below:</u></strong>
          <div><strong>{{$ticket->utr_no}} : {{$ticket->total_amt ?? 'N/A'}}</strong></div>
	    @endif
	   @endif
       		
        <div style="padding-top: 2rem;">
            With Regards,<br/>
            ETF Operations Team<br/>
            <img src="{{ asset('assets/img/mail-logo.jpg') }}" alt="logo" />
        </div>
    </div>
</div>
@elseif ($ticket->security->amc->id == 9 || $ticket->security->amc->id == 10 || $ticket->security->amc->id == 16)
<div>
    <p> Please execute request for Additional <strong>{{ $ticket->type == 1 ? 'Creation' : 'Redemption' }}</strong> of the following on T+0, dated {{ $ticket->created_at->format('jS F, Y') }} (Form enclosed):</p>

    <div>
        <strong> {{ $ticket->type == 1 ? 'Creation' : 'Redemption' }} </strong>
    </div>

    <div style="padding-top: 1rem;padding-bottom: 2rem;">
        Date : {{ $ticket->created_at->format('jS F, Y') }}<br/>
        Investor Name : KANJALOCHANA FINSERVE PRIVATE LIMITED<br/>
        PAN : AAGCK6770Q<br/>
        CDSL DP ID : 16014300<br/>
        Client ID : 00006241
    </div>

    <table class="table table-bordered" style="border-collapse: collapse; width: 100%;padding-top: 3rem;">
        <thead>
            <tr style="align-items: center;">
                <th style="border: 1px solid #dddddd;padding: 8px;">Scheme</th>
                <th style="border: 1px solid #dddddd;padding: 8px;">Basket Size</th>
                <th style="border: 1px solid #dddddd;padding: 8px;">No. of Baskets</th>
                <th style="border: 1px solid #dddddd;padding: 8px;">Total Units</th>
            </tr>
        </thead>
        <tbody>
            <tr style="align-items: center;">
                <td style="border: 1px solid #dddddd;padding: 8px;">{{ $ticket->security->name }}</td>
                <td style="border: 1px solid #dddddd;padding: 8px;">{{ $ticket->basket_size }}</td>
                <td style="border: 1px solid #dddddd;padding: 8px;">{{ $ticket->basket_no }}</td>
                <td style="border: 1px solid #dddddd;padding: 8px;">{{ $ticket->basket_size * $ticket->basket_no }}</td>
            </tr>
        </tbody>
    </table>

    <div style="padding-top: 3rem;">
        <u> Bank Details :</u><br/>
        Bank Name : ICICI BANK<br/>
        Branch : VAKOLA<br/>
        IFSC Code : ICIC0001200<br/>
        Account Number : 120005002164
    </div>

    <div style="padding-top: 3rem;">
		<!-- SHOW it in all cash CASES -->
	   @if($ticket->payment_type != 2 )
		<!-- show it in all BUY cases only --> 
		@if($ticket->type != 2)  
          <strong><u> Payment details mentioned below:</u></strong>
          <div><strong>{{$ticket->utr_no}} : {{$ticket->total_amt ?? 'N/A'}}</strong></div>
	    @endif
	   @endif
        <div style="padding-top: 2rem;">
            With Regards,<br/>
            ETF Operations Team<br/>
            <img src="{{ asset('assets/img/mail-logo.jpg') }}" alt="logo" />
        </div>
    </div>
</div>
@elseif ($ticket->security->amc->id == 11 || $ticket->security->amc->id == 18 || $ticket->security->amc->id == 15 || $ticket->security->amc->id == 14 || $ticket->security->amc->id == 6 || $ticket->security->amc->id == 4 || $ticket->security->amc->id == 13)
<div>
    <p> Please execute request for Additional <strong>{{ $ticket->type == 1 ? 'Creation' : 'Redemption' }}</strong> of the following on T+0, dated {{ $ticket->created_at->format('jS F, Y') }} (Form enclosed):</p>

    <div>
        <strong> {{ $ticket->type == 1 ? 'Creation' : 'Redemption' }} </strong>
    </div>

    <div style="padding-top: 1rem;padding-bottom: 2rem;">
        Date : {{ $ticket->created_at->format('jS F, Y') }}<br/>
        Investor Name : KANJALOCHANA FINSERVE PRIVATE LIMITED<br/>
        PAN : AAGCK6770Q<br/>
        NSDL DP ID : IN300327<br/>
        ClientID : 10619369
    </div>

    <table class="table table-bordered" style="border-collapse: collapse; width: 100%;padding-top: 3rem;">
        <thead>
            <tr style="align-items: center;">
                <th style="border: 1px solid #dddddd;padding: 8px;">Scheme</th>
                <th style="border: 1px solid #dddddd;padding: 8px;">Basket Size</th>
                <th style="border: 1px solid #dddddd;padding: 8px;">No. of Baskets</th>
                <th style="border: 1px solid #dddddd;padding: 8px;">Total Units</th>
            </tr>
        </thead>
        <tbody>
            <tr style="align-items: center;">
                <td style="border: 1px solid #dddddd;padding: 8px;">{{ $ticket->security->name }}</td>
                <td style="border: 1px solid #dddddd;padding: 8px;">{{ $ticket->basket_size }}</td>
                <td style="border: 1px solid #dddddd;padding: 8px;">{{ $ticket->basket_no }}</td>
                <td style="border: 1px solid #dddddd;padding: 8px;">{{ $ticket->basket_size * $ticket->basket_no }}</td>
            </tr>
        </tbody>
    </table>

    <div style="padding-top: 3rem;">
        <u> Bank Details :</u><br/>
        Bank Name : ICICI BANK<br/>
        Branch : VAKOLA<br/>
        IFSC Code : ICIC0000004<br/>
        Account Number : 000405133274
    </div>

    <div style="padding-top: 3rem;">		
	   <!-- SHOW it in all cash CASES -->
	   @if($ticket->payment_type != 2 )
		<!-- show it in all BUY cases only --> 
		@if($ticket->type != 2)  
		  <strong><u> Payment details mentioned below:</u></strong>
		  <div><strong>{{$ticket->utr_no}} : {{$ticket->total_amt ?? 'N/A'}}</strong></div>
		@endif
	   @endif
		   
        <div style="padding-top: 2rem;">
            With Regards,<br/>
            ETF Operations Team<br/>
            <img src="{{ asset('assets/img/mail-logo.jpg') }}" alt="logo" />
        </div>
    </div>
</div>
@elseif ($ticket->security->amc->id == 3)
<div>
    <p> Please execute request for Additional <strong>{{ $ticket->type == 1 ? 'Creation' : 'Redemption' }}</strong> of the following on T+0, dated {{ $ticket->created_at->format('jS F, Y') }} (Form enclosed):</p>

    <div>
        <strong> {{ $ticket->type == 1 ? 'Creation' : 'Redemption' }} </strong>
    </div>

    <div style="padding-top: 1rem;padding-bottom: 2rem;">
        Date : {{ $ticket->created_at->format('jS F, Y') }}<br/>
        Investor Name : KANJALOCHANA FINSERVE PRIVATE LIMITED<br/>
        Folio Number : 7927057<br/>
        NSDL DP ID : IN300327<br/>
        Client ID : 10619369
    </div>

    <table class="table table-bordered" style="border-collapse: collapse; width: 100%;padding-top: 3rem;">
        <thead>
            <tr style="align-items: center;">
                <th style="border: 1px solid #dddddd;padding: 8px;">Scheme</th>
                <th style="border: 1px solid #dddddd;padding: 8px;">Basket Size</th>
                <th style="border: 1px solid #dddddd;padding: 8px;">No. of Baskets</th>
                <th style="border: 1px solid #dddddd;padding: 8px;">Total Units</th>
            </tr>
        </thead>
        <tbody>
            <tr style="align-items: center;">
                <td style="border: 1px solid #dddddd;padding: 8px;">{{ $ticket->security->name }}</td>
                <td style="border: 1px solid #dddddd;padding: 8px;">{{ $ticket->basket_size }}</td>
                <td style="border: 1px solid #dddddd;padding: 8px;">{{ $ticket->basket_no }}</td>
                <td style="border: 1px solid #dddddd;padding: 8px;">{{ $ticket->basket_size * $ticket->basket_no }}</td>
            </tr>
        </tbody>
    </table>

    <div style="padding-top: 3rem;">
        <u> Bank Details :</u><br/>
        Bank Name : ICICI BANK<br/>
        Branch : VAKOLA<br/>
        IFSC Code : ICIC0001200<br/>
        Account Number : 120005002164
    </div>

    <div style="padding-top: 3rem;">
       <!-- SHOW it in all cash CASES -->
       @if($ticket->payment_type != 2 )
        <!-- show it in all BUY cases only --> 
        @if($ticket->type != 2)  
          <strong><u> Payment details mentioned below:</u></strong>
          <div><strong>{{$ticket->utr_no}} : {{$ticket->total_amt ?? 'N/A'}}</strong></div>
        @endif
       @endif
        <div style="padding-top: 2rem;">
            With Regards,<br/>
            ETF Operations Team<br/>
            <img src="{{ asset('assets/img/mail-logo.jpg') }}" alt="logo" />
        </div>
    </div>
</div>
@endif
