<div>
	<strong><u>Payment details mentioned below:</u></strong>
	<div><strong>{{$ticket->utr_no}} :
		@if($ticket->type == 1 && $ticket->payment_type == 2)
		  {{$ticket->cashcomp ?? 'N/A'}}
		@elseif($ticket->type == 2 && $ticket->payment_type == 2)
		  {{$ticket->totalstampduty ?? 'N/A'}}
		@endif
	</strong></div>
</div>
<div style="padding-top: 2rem;">
	With Regards,<br/>
	ETF Operations Team<br/>
	<img src="{{ asset('assets/img/mail-logo.jpg') }}" alt="logo" />
</div>
