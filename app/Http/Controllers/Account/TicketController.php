<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use Storage;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tickets = Ticket::whereIn('status_id', [3, 11])
         ->orderBy('id')
         ->paginate(10);

         return view('accounts.tickets.index', compact('tickets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id) {

        $ticket = Ticket::findOrFail($id);

        return view('accounts.tickets.edit', ['ticket' => $ticket]);
    }

    function NumberintoWords(float $number)
    {
        $number_after_decimal = round($number - ($num = floor($number)), 2) * 100;

        // Check if there is any number after decimal
        $amt_hundred = null;
        $count_length = strlen($num);
        $x = 0;
        $string = array();
        $change_words = array(
            0 => 'Zero', 1 => 'One', 2 => 'Two',
            3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
            7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
            10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
            13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
            16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
            19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
            40 => 'Fourty', 50 => 'Fifty', 60 => 'Sixty',
            70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety'
        );
        $here_digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
        while ($x < $count_length) {
            $get_divider = ($x == 2) ? 10 : 100;
            $number = floor($num % $get_divider);
            $num = floor($num / $get_divider);
            $x += $get_divider == 10 ? 1 : 2;
            if ($number) {
                $add_plural = (($counter = count($string)) && $number > 9) ? 's' : null;
                $amt_hundred = ($counter == 1 && $string[0]) ? ' and ' : null;
                $string[] = ($number < 21) ? ( $change_words[$number] ) . ' ' . $here_digits[$counter] . $add_plural . ' ' .
                            $amt_hundred : $change_words[floor($number / 10) * 10] .
    	                      ($number % 10 != 0 ? ' ' . $change_words[$number % 10] : '') . ' ' .
                            $here_digits[$counter] . $add_plural . ' ' . $amt_hundred;
            } else {
              $string[] = null;
            }
        }
        $implode_to_Words = implode('', array_reverse($string));

    	$get_word_after_point = ($number_after_decimal > 0) ? "Point " . ($change_words[$number_after_decimal / 10] . "
            " . $change_words[$number_after_decimal % 10]) : '';

    	return ($implode_to_Words ? $implode_to_Words : '') . ($get_word_after_point!=''? ' ' . $get_word_after_point:'');
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $ticket = Ticket::findOrFail($id);
        $data = $request->all();

        if ($ticket->status_id == 3) {
            $request->validate([
                'total_amt' => 'required|numeric',
                'utr_no' => 'required|string',
                'screenshot' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
            ]);

            if ($ticket->total_amt == $request->get('total_amt')) {
                // Screenshot Wrokings
                if ($request->hasFile('screenshot') && $ticket->screenshot) {
                    Storage::delete($ticket->screenshot);
                }
                if ($request->hasFile('screenshot')) {
                    $imagePath = $request->file('screenshot')->store('screenshot', 'public');
                    $ticket->screenshot = $imagePath;
                }

                $ticket->status_id = $request->get('utr_no');
                if ($ticket->type == 1 && $ticket->payment_type == 1) {
                    $ticket->status_id = 6;
                }

                $ticket->update($request->except('screenshot'));

                // Pdf Workings :: STRAT
                $sec_name = $ticket->security->name;

                if ($sec_name) {
                    // Aditya BIRLA Form
                    if ( strpos($sec_name, "ADITYA BIRLA") == 0 ) {
                      $filepath = storage_path('app/public/forms/birla.html');

                      if(file_exists($filepath)) {

                        // Get HTML Content
                        $text = file_get_contents( $filepath );

                        // Payment TYPE
                        $payment_type = $ticket->payment_type ;

                        // Security Name
                        $sec_name_tag = str_replace("ADITYA BIRLA SUN LIFE ","",$sec_name);
                        $sec_name_tag = str_replace(" ","",trim(strtolower($sec_name_tag)));

                        // TARGET TAGs ARRAY
                        $target_tags = ['niftybanketf', 'goldetf', 'nifty50etf',
                                        'silveretf', 'niftynext50etf', 's&pbsesensexetf',
                                        'niftyhealthcareetf',  'niftyitetf', 'nifty200quality30etf',
                                        'nifty200momentum30etf'];

                        // IF we find TAG then we proceed
                        $bottom = 437;
                        $found = 0;
                        $target_c_tag = '';
                        foreach($target_tags as $target_tag)
                        {
                           if( strpos($target_tag, $sec_name_tag) !== false )
                           {
                             $target_c_tag = $target_tag;
                             $found++;
                             break;
                           }
                           $bottom = $bottom - 14;
                        }

                        // PROCEED if FOUND
                        if ( $found ) {
                          // TR TYPE
                          if($ticket->type == 1) // BUY / PURCHASE
                          {
                            if($payment_type == 1) // CASH
                            {
                              $text = str_replace('<!--TRTYPEPURCHASECASH-->',
                              '<!--CHANDANcase1--><span class="ff3 ws3" style="display:inline-block;margin-left: 50px;position: absolute;">3</span>', $text);
                            }
                            if($payment_type == 2) // BASKET
                            {
                              $text = str_replace('<!--TRTYPEPURCHASEBASKET-->',
                              '<span class="ff3 ws3" style="display:inline-block;margin-left: 117px;position: absolute;">3</span>', $text);
                            }
                          }

                          if($ticket->type == 2) // SELL / REDEEM
                          {
                            if($payment_type == 1) // CASH
                            {
                              $text = str_replace('<!--TRTYPEREDEEMCASH-->',
                              '<span class="ff3 ws3" style="display:inline-block;margin-left:-48px;position: absolute;">3</span>', $text);
                            }
                            if($payment_type == 2) // BASKET
                            {
                              $text = str_replace('<!--TRTYPEREDEEMBASKET-->',
                              '<span class="ff3 ws3" style="display:inline-block;margin-left:116px;position: absolute;">3</span>', $text);
                            }
                          }

                          // ADD TICK next to Security Name
                          $tick_target = '<!--' . $target_c_tag . ':tick-->';
                          // dd($tick_target);  <!--niftybanketf:tick-->
                          $text = str_replace($tick_target,
                                              '<!--CHANDANcase2--><span class="ff3 ws3" style="display:inline-block;left:10px;margin-left:-54px;margin-top:5px;position: absolute;">3</span>',
                                              $text
                                             );

                          // INSERT Pricing DETAILS
                          $replace_str = '<div class="c x17 w9 h11" style="bottom:' . $bottom . 'px"><div class="t m0 x0 hf y50 ff7 fs3 fc2 sc0 ls0"><totalunits></div></div><div class="c x18 wa h11" style="bottom:' . $bottom . 'px"><div class="t m0 x0 hf y50 ff7 fs3 fc2 sc0 ls0"><totalbaskets></div></div><div class="c x19 wb h11" style="bottom:' . $bottom . 'px"><div class="t m0 x0 hf y50 ff7 fs3 fc2 sc0 ls0 wsb"><totalunitsinwords></div></div>';
                          $basket_size   = $ticket->basket_size;
                          $ticket_basket = $ticket->basket_no; // NO. of Basket
                          $total_units   = (double) $ticket->basket_size * (double) $ticket->basket_no;
                          //$total_units   = number_format( $total_units, 0, '.', ',' ); // Total Units
                          $total_units_in_float = (float) $total_units;
                          $total_units_in_words = trim($this->NumberintoWords( $total_units_in_float)); // Total Units in Words
                          $total_units_in_words = ('' == $total_units_in_words ? 'Zero Only' : $total_units_in_words . ' Only');
                          $replace_str = str_replace('<totalunits>', $total_units, $replace_str);
                          $replace_str = str_replace('<totalbaskets>', $ticket_basket, $replace_str);
                          $replace_str = str_replace('<totalunitsinwords>', $total_units_in_words, $replace_str);
                          $text = str_replace('<!--PRICINGDETAILS-->', $replace_str, $text);

                          // INSERT TOTAL VALUE
                          $total_amt = $ticket->total_amt;
                          $text = str_replace('<!--TOTALVALUE-->', $total_amt, $text);
                          $word_text = trim($this->NumberintoWords($total_amt));
                          $word_text = ('' == $word_text ? 'Zero Only' : $word_text . ' Only');
                          $text = str_replace('<!--TOTALVALUEINWORDS-->', $word_text, $text);

                          // NOW Generate DOM PDF from HTML  


                        } // IF FOUND, ends here
                      }
                    }
                }
                // Pdf Workings :: END
            } else {
                return redirect()->back()->with('error', 'Please verify your entered amount.');
            }
        } elseif ($ticket->status_id == 11) {
            // $request->validate([
            // ]);

            if ($ticket->type == 1) {
                $ticket->status_id = 13;
            } else {
                $ticket->status_id = 12;
            }

            $ticket->update();
        }

        return redirect()->route('accounts.tickets.index')->with('success', 'Ticket updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
