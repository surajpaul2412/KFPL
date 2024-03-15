<div class="row px-md-4">
    <div class="col-3">
        <div>Name</div>
        <div class="font-weight-bold">{{$ticket->security->name ?? 'N/A'}}</div>
    </div>
    <div class="col-3">
        <div>Symbol</div>
        <div class="font-weight-bold">{{$ticket->security->symbol ?? 'N/A'}}</div>
    </div>
    <div class="col-3">
        <div>Ticket Type</div>
        <div class="font-weight-bold">
          {{$ticket->type == 1 ? "Buy" : "Sell"}}
        </div>
    </div>
    <div class="col-3">
        <div>Payment Mode</div>
        <div class="font-weight-bold">
          @php
          if($ticket->payment_type == 1) echo "Cash";
          else if($ticket->payment_type == 2) echo "Basket";
          else if($ticket->payment_type == 3) echo "Net Settlement";
          @endphp
        </div>
    </div>
</div>
<hr/>
<div class="row px-md-4">
    <div class="col-3">
        <div>Number of Baskets</div>
        <div class="font-weight-bold"> {{$ticket->basket_no  ?? 'N/A'}} </div>
    </div>
    <div class="col-3">
        <div>Basket Size</div>
        <div class="font-weight-bold">  {{$ticket->basket_size  ?? 'N/A'}}  </div>
    </div>
    <div class="col-3">
        <div>Ticket Rate</div>
        <div class="font-weight-bold"> {{$ticket->rate  ?? 'N/A'}} </div>
    </div>
    <div class="col-3">
        <div>Total Amount</div>
        <div class="font-weight-bold"> {{$ticket->total_amt  ?? 'N/A'}} </div>
    </div>
</div>
<hr/>
<div class="row px-md-4">
    <div class="col-3">
        <div>Markup Percentage</div>
        <div class="font-weight-bold"> {{$ticket->markup_percentage ?? 'N/A'}} </div>
    </div>
    <div class="col-3">
        <div>UTR Number</div>
        <div class="font-weight-bold">{{$ticket->utr_no ?? 'N/A'}}</div>
    </div>
    <div class="col-3">
        <div>AMC Form </div>
        @if (Storage::exists('public/ticketpdfs/ticket-' . $ticket->id . '.pdf'))
        <div class="font-weight-bold"><a href="{{ asset("storage/ticketpdfs/ticket-{$ticket->id}.pdf") }}" target="_blank" download>Download <i class="ri-download-2-line"></i></a></div>
        @else
        <div class="font-weight-bold">N/A</div>
        @endif
    </div>
    <div class="col-3">
        <div>Demate PDF</div>
        <div class="font-weight-bold"> <a href="{{ asset($ticket->security->amc->pdf->path) }}" target="_blank" download>Download <i class="ri-download-2-line"></i></a> </div>
    </div>
</div>
<hr/>
<div class="row px-md-4">
    <div class="col-3">
        <div>Trade Value</div>
        <div class="font-weight-bold"> {{$ticket->actual_total_amt ?? 'N/A'}} </div>
    </div>
    <div class="col-3">
        <div>NAV</div>
        <div class="font-weight-bold"> {{$ticket->nav ?? '0'}} </div>
    </div>
</div>