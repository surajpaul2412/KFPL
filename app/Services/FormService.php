<?php
namespace App\Services;

use Storage;
use Validator;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class FormService
{
  // Handle LIC FORM
  private static function handleLICForm($ticket) {

    $sec_name = $ticket->security->name;

    $filepath = storage_path('app/public/forms/lic.html');

    if (file_exists($filepath)) {

      // Get HTML Content
      $text = file_get_contents( $filepath );

      // Payment TYPE
      $payment_type = $ticket->payment_type ;

      // TOTAL UNITS
      $basket_size   = $ticket->basket_size;
      $ticket_basket = $ticket->basket_no; // NO. of Basket
      $total_units   = (double) $ticket->basket_size * (double) $ticket->basket_no;
      $total_units_in_float = (float) $total_units;

      $text = str_replace('<!--SECURITYNAME-->', $ticket->security->name, $text);

      // INSERT TOTAL VALUE
      $total_amt = $ticket->total_amt;
      $word_text = trim(self::NumberintoWords($total_amt));
      $word_text = ('' == $word_text ? 'Zero Only' : $word_text . ' Only');

      if ($ticket->type == 1) {   // BUY / PURCHASE
          $text = str_replace('<!--TICK-->','<div class="PURCHASETICK" style="position:absolute;bottom:494px;left:38px;"><span class="ff5 ws7" style="font-weight: bold;font-size: 11px;height: auto;display: inline-block;">ü</span></div>', $text);
          $text = str_replace('<!--TOTALPURCHASEAMOUNT-->', $ticket->total_amt, $text);
          $text = str_replace('<!--TOTALPURCHASEAMOUNTINWORDS-->', $word_text, $text);
      }

      if ($ticket->type == 2) { // SELL / REDEEM
          $text = str_replace('<!--TICK-->','<div class="REDEEMTICK" style="position:absolute;bottom:339px;left:38px;"><span class="ff5 ws7" style="font-weight: bold;font-size: 11px;height: auto;display: inline-block;">ü</span></div>', $text);
          $text = str_replace('<!--TOTALUNITS-->', $total_units_in_float, $text);
          $text = str_replace('<!--TOTALREDEEMAMOUNT-->', $ticket->total_amt, $text);
          $text = str_replace('<!--TOTALREDEEMAMOUNTINWORDS-->', $word_text, $text);
      }

      // NOW SAVE FILE
      self::saveDocument($ticket->id, $text);

      return 1;
    } else {
      return 0;
    }
  }


  // HANDLE BIRLA FORM thru API
  private static function handleBirlaFormNew($ticket) {

      $marker_json = [];
      $textannotations = [];
      $images = [];

      // Payment TYPE
      $payment_type = $ticket->payment_type ;
      // Security Name
      $sec_name = $ticket->security->name;

      $basket_size   = $ticket->basket_size;
      $ticket_basket = $ticket->basket_no; // NO. of Basket
      $total_units   = (double) $ticket->basket_size * (double) $ticket->basket_no;
      $total_units_in_float = (float) $total_units;
      $total_units_in_words = trim(self::NumberintoWords( $total_units_in_float)); // Total Units in Words
      $total_units_in_words = ('' == $total_units_in_words ? 'Zero Only' : $total_units_in_words . ' Only');

      $total_amt = $ticket->total_amt;
      $word_text = trim(self::NumberintoWords($total_amt));
      $word_text = ('' == $word_text ? 'Zero Only' : $word_text . ' Only');

      $checkboxImageData = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAYAAACqaXHeAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAB2AAAAdgB+lymcgAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAAMLSURBVHic7dpPiFVlGMfxzzjjFaywxBKEXFQiulFwoWh/rECIIBdGFIIMSIERtBBxJeiiQJFAQqRFIARSRlaLghZCECJERosiRCpCqVBQCU1JvdfFWzhz59yZ95x73/M6er7wbIZ73/P9PWfOPe973kNDQ0NDQ8Ndy3BugZpYinfwCI5ndqmVediPa+jgBlpZjWqiha24IAT/v97KKVUXz+KU8cGv49WcUnVwP95D2/jwV/FiRq9aeAl/GR+8g7/xVEav5DyAj00M3sElPJ5PLT2r8Kvi8JfxdD61tIxgp/DDVhT+H+GH8I7kQXytOHhHuN8/l0suNcvwm97hO9iSzS4xrwjX9WTh92SzS8ybJt7bu+sTzMglmIoh7DV58A6+wz2ZHJPR0vv+PrZ+x/xMjslo4VNTh/8XqzM5JqOFz0wdvoPXMzkmYxhHxIX/MJNjUt4VF/405mZyTMYuceFvYO0gDzxS8LchLBT/vLCNM8LcvAovY0fkZ/cKU+GkfCvubIytU1hQ4VgrhMVLzDF+xqyKmaIZwdlIoe46gXtLHGuO3svZ7mrjib6SleAZXIkU664vFV9WRRwqMe6B/mOVY4Pe6+1ByL5WYrw/hP+W2tlk6kVIr9o2ybgLhWd1sWONDjRVSbYXCMVesxt7jPl5iXG+dxus8nar1oQrWNM11vqSYzyZLlY8Q3hftSacw6L/xhnGjyW+ezh5shIMC0JVmvALHsLmEt+5jiW1JCvBLBxVrQnHhDl87Oc/qClTaWYLYao0oczZX1xXoCrMw0/SNeBgbUn64GHhcdSgw7fd5md/LI8p3pDsp76oNcEAWGbiSwj91Lp69QfDWtUXT2PrpDDnmJa84NZ7OFVr2m9rjaq+eLqM+2o3TsAbqjXgUA7ZVLytfAOez2KakH3iw5/FzDya6ZghbF7ENGBfJsfktPCVqRtwR7/INBvf6B3+vPiHqNOWOfhBcQM+yuhVKwsU7wGMZnSqnUfxp1vh26rtJk1rluOi0IATmV2ysVKY+a3MLdLQ0NDQ0HCXcxMNSZj+UasGowAAAABJRU5ErkJggg==";
      // BUY CASES
      if ($ticket->type == 1) {

        if ($payment_type == 1) {  // CASH
            $images[] =  [
                  "url" => $checkboxImageData, "x" => 131, "y" => 297, "width" => 17, "height" => 14, "pages" => "0", "keepAspectRatio" => true
              ];
        }

        if ($payment_type == 2) { // BASKET
          $images[] =  [
                "url" => $checkboxImageData, "x" => 185.82, "y" =>  298.92, "width" => 17,   "height" => 14, "pages" =>  "0", "keepAspectRatio" => true
            ];
        }

      }

      // SELL CASES
      if ($ticket->type == 2) {
        if ($payment_type == 1) {  // CASH
           $images[] = [
            "url" => $checkboxImageData, "x" => 404.04, "y" => 297,  "width" => 15, "height" => 14, "pages" => "0", "keepAspectRatio" => true
          ];
        }

        if ($payment_type == 2) { // BASKET
          $images[] = [
           "url" => $checkboxImageData, "x" => 458.86, "y" => 297.96, "width" => 15, "height" => 14,  "pages" => "0",  "keepAspectRatio" => true
         ];
        }
      }

      if(strtolower($sec_name) == 'aditya birla sun life nifty bank etf')
      {
          $images[] = ["url" => $checkboxImageData, "x" => 34.04, "y" => 392.04,"size"=>7, "width" => 11, "height" =>10, "pages" => "0", "keepAspectRatio" => true];
          $textannotations[] = ["text" => "$ticket_basket", "x" => 260.86, "y" => 391.11,"size"=>7, "width" => 57.57, "height" => 11.37, "pages" => "0", "type" => "text"];
          $textannotations[] = ["text"=> "$total_units", "x"=> 321.09,  "y"=> 391.94,"size"=>7,"width"=> 71.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
          $textannotations[] = ["text"=> "$total_units_in_words", "x"=> 404, "y"=> 392,"size"=>7,"width"=> 156, "height"=> 13, "pages"=> "0", "type" => "text"];
      }

      else if(strtolower($sec_name) == 'aditya birla sun life gold etf')
      {
          $images[] = ["url" => $checkboxImageData, "x" => 35.58, "y" => 408.45,"size"=>7, "width" => 11, "height" => 10, "pages" => "0", "keepAspectRatio" => true];
          $textannotations[] = ["text" => "$ticket_basket", "x" => 260.86, "y" => 404,"size"=>7, "width" => 57.57, "height" => 11.37, "pages" => "0", "type" => "text"];
          $textannotations[] = ["text"=> "$total_units", "x"=> 321.09,  "y"=> 404,"size"=>7, "width"=> 71.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
          $textannotations[] = ["text"=> "$total_units_in_words", "x"=> 404, "y"=> 404,"size"=>7, "width"=> 156, "height"=> 13, "pages"=> "0", "type" => "text"];
      }
      else if(strtolower($sec_name) == 'aditya birla sun life nifty 50 etf')
      {
          $images[] = ["url" => $checkboxImageData, "x" => 35.58, "y" => 423.45,"size"=>7, "width" => 11, "height" => 10, "pages" => "0", "keepAspectRatio" => true];
          $textannotations[] = ["text" => "$ticket_basket", "x" => 260.86, "y" => 420,"size"=>7, "width" => 57.57, "height" => 11.37, "pages" => "0", "type" => "text"];
          $textannotations[] = ["text"=> "$total_units", "x"=> 321.09,  "y"=> 420,"size"=>7, "width"=> 71.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
          $textannotations[] = ["text"=> "$total_units_in_words", "x"=> 404, "y"=> 420,"size"=>7,"width"=> 156, "height"=> 13, "pages"=> "0", "type" => "text"];
      }
      else if(strtolower($sec_name) == 'aditya birla sun life silver etf')
      {
          $images[] = ["url" => $checkboxImageData, "x" => 35.58, "y" => 435.86,"size"=>7, "width" => 11, "height" => 10, "pages" => "0", "keepAspectRatio" => true];
          $textannotations[] = ["text" => "$ticket_basket", "x" => 260.86, "y" => 435,"size"=>7, "width" => 57.57, "height" => 11.37, "pages" => "0", "type" => "text"];
          $textannotations[] = ["text"=> "$ticket_basket", "x"=> 321.09,  "y"=> 435,"size"=>7, "width"=> 71.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
          $textannotations[] = ["text"=> "$ticket_basket", "x"=> 404, "y"=> 435,"size"=>7, "width"=> 156, "height"=> 13, "pages"=> "0", "type" => "text"];
      }
      else if(strtolower($sec_name) == 'aditya birla sun life nifty next 50 etf')
      {
          $images[] = ["url" => $checkboxImageData, "x" => 35.58, "y" => 450,"size"=>7, "width" => 11, "height" => 10, "pages" => "0", "keepAspectRatio" => true];
          $textannotations[] = ["text" => "$ticket_basket", "x" => 260.86, "y" => 448,"size"=>7, "width" => 57.57, "height" => 11.37, "pages" => "0", "type" => "text"];
          $textannotations[] = ["text"=> "$total_units", "x"=> 321.09, "y"=> 448,"size"=>7,"width"=> 71.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
          $textannotations[] = ["text"=> "$total_units_in_words", "x"=> 404, "y"=> 448,"size"=>7,"width"=> 156, "height"=> 13, "pages"=> "0", "type" => "text"];
      }
      else if(strtolower($sec_name) == 'aditya birla sun life s & p bse sensex etf')
      {
          $images[] = ["url" => $checkboxImageData, "x" => 35.58, "y" => 464,"size"=>7, "width" => 11, "height" => 10, "pages" => "0", "keepAspectRatio" => true];
          $textannotations[] = ["text" => "$ticket_basket", "x" => 260.86, "y" => 461,"size"=>7, "width" => 57.57, "height" => 11.37, "pages" => "0", "type" => "text"];
          $textannotations[] = ["text"=> "$total_units", "x"=> 321.09, "y"=> 461,"size"=>7,"width"=> 71.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
          $textannotations[] = ["text"=> "$total_units_in_words", "x"=> 404, "y"=> 461,"size"=>7,"width"=> 156, "height"=> 13, "pages"=> "0", "type" => "text"];
      }
      else if(strtolower($sec_name) == 'aditya birla sun life nifty healthcare etf')
      {
          $images[] = ["url" => $checkboxImageData, "x" => 35.58, "y" => 478,"size"=>7, "width" => 11, "height" => 10, "pages" => "0", "keepAspectRatio" => true];
          $textannotations[] = ["text" => "$ticket_basket", "x" => 260.86, "y" => 476,"size"=>7, "width" => 57.57, "height" => 11.37, "pages" => "0", "type" => "text"];
          $textannotations[] = ["text"=> "$total_units", "x"=> 321.09, "y"=> 476,"size"=>7,"width"=> 71.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
          $textannotations[] = ["text"=> "$total_units_in_words", "x"=> 404, "y"=> 476,"size"=>7,"width"=> 156, "height"=> 13, "pages"=> "0", "type" => "text"];
      }
      else if(strtolower($sec_name) == 'aditya birla sun life nifty it etf')
      {
          $images[] = ["url" => $checkboxImageData, "x" => 35.58, "y" => 492,"size"=>7, "width" => 11, "height" => 10, "pages" => "0", "keepAspectRatio" => true];
          $textannotations[] = ["text" => "$ticket_basket", "x" => 260.86, "y" => 490,"size"=>7, "width" => 57.57, "height" => 11.37, "pages" => "0", "type" => "text"];
          $textannotations[] = ["text"=> "$total_units", "x"=> 321.09, "y"=> 490,"size"=>7,"width"=> 71.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
          $textannotations[] = ["text"=> "$total_units_in_words", "x"=> 404, "y"=> 490,"size"=>7,"width"=> 156, "height"=> 13, "pages"=> "0", "type" => "text"];
      }
      else if(strtolower($sec_name) == 'aditya birla sun life nifty 200 quality 30 etf')
      {
          $images[] = ["url" => $checkboxImageData, "x" => 35.58, "y" => 506,"size"=>7, "width" => 11, "height" => 10, "pages" => "0", "keepAspectRatio" => true];
          $textannotations[] = ["text" => "$ticket_basket", "x" => 260.86, "y" => 504,"size"=>7, "width" => 57.57, "height" => 11.37, "pages" => "0", "type" => "text"];
          $textannotations[] = ["text"=> "$total_units", "x"=> 321.09, "y"=> 504,"size"=>7,"width"=> 71.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
          $textannotations[] = ["text"=> "$total_units_in_words", "x"=> 404, "y"=> 504,"size"=>7,"width"=> 156, "height"=> 13, "pages"=> "0", "type" => "text"];
      }
      else if(strtolower($sec_name) == 'aditya birla sun life nifty 200 momentum 30 etf')
      {
          $images[] = ["url" => $checkboxImageData, "x" => 35.58, "y" => 520,"size"=>7, "width" => 11, "height" => 10, "pages" => "0", "keepAspectRatio" => true];
          $textannotations[] = ["text" => "$ticket_basket", "x" => 260.86, "y" => 517,"size"=>7, "width" => 57.57, "height" => 11.37, "pages" => "0", "type" => "text"];
          $textannotations[] = ["text"=> "$total_units", "x"=> 321.09, "y"=> 517,"size"=>7,"width"=> 71.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
          $textannotations[] = ["text"=> "$total_units_in_words", "x"=> 404, "y"=> 517,"size"=>7,"width"=> 156, "height"=> 13, "pages"=> "0", "type" => "text"];
      }

      // IF BUY CASE, add UTR NO, AMOUNT, AMount in WORDS
      if( $ticket->type == 1 )
      {
        $utr_no = $ticket->utr_no;
        $textannotations[] = ["text"=> "$utr_no", "x"=> 391.48, "y"=> 548.84,"size"=>7,"width"=> 137, "height"=> 10, "pages"=> "0", "type"=> "text"];
        $textannotations[] = ["text"=> "$total_amt", "x"=> 112.92, "y"=> 567.15,"size"=>7,"width"=> 137, "height"=> 10, "pages"=> "0", "type"=> "text"];
        $textannotations[] = ["text"=> "$word_text", "x"=> 307, "y"=> 566,  "width"=> 300,"size"=>7,"height"=> 13, "pages"=> "0", "type"=> "text"];
      }

      //Log::debug(strtolower($sec_name) . " Case => ");
      //Log::debug( print_r($images, true) );
      //Log::debug( print_r($textannotations, true) );

      // BUILD MARKER JSON
      $marker_array =
        [ "url" => "filetoken://170a15da3a9dba6242c1adc4c534efcc833d7d467c5f7de5c5",
          "async" => false, "encrypt" => false, "inline" => true,
          "annotations" => $textannotations,
          "images" => $images,
          "fields" => []
        ];

      $marker_json = json_encode( $marker_array );

      // CALL API
      $curl = curl_init();

      curl_setopt_array($curl, array(
      	CURLOPT_URL => 'https://api.pdf.co/v1/pdf/edit/add',
      	CURLOPT_RETURNTRANSFER => true,
      	CURLOPT_ENCODING  => '',
      	CURLOPT_MAXREDIRS => 10,
      	CURLOPT_TIMEOUT   => 0,
      	CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
      	CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
      	CURLOPT_CUSTOMREQUEST  => 'POST',
      	CURLOPT_POSTFIELDS     => $marker_json,
      	CURLOPT_HTTPHEADER     => array(
      		'Content-Type: application/json',
      		'x-api-key: ' . env('PDFCO_API_KEY')
      	),
      ));

      $response = json_decode(curl_exec($curl), true);
      if(curl_exec($curl) === false)
      {
         // LOG
         Log::debug( "PDF GEneration Error : " . curl_error($curl) );
      }
      else
      {
        if( isset($response['url']) && $response['url']!='' )
        {
           $filecontent = file_get_contents($response['url']);
           if($filecontent)
           {
               $fileName = 'ticket-' . $ticket->id . '.pdf';
               if(file_exists(storage_path() . "/app/public/ticketpdfs/$fileName"))
               {
                  @unlink( storage_path() . "/app/public/ticketpdfs/$fileName" );
               }
               Storage::put('public/ticketpdfs/' . $fileName, $filecontent);
           }
        }
      }
  }

  // Handle BIRLA FORM
  private static function handleBirlaForm($ticket) {

    $sec_name = $ticket->security->name;

    $filepath = storage_path('app/public/forms/birla.html');

    if (file_exists($filepath)) {

      // Get HTML Content
      $text = file_get_contents( $filepath );

      // Payment TYPE
      $payment_type = $ticket->payment_type ;

      // Security Name
      $sec_name_tag = str_replace('ADITYA BIRLA SUN LIFE ', '', $sec_name);
      $sec_name_tag = str_replace(' ', '', trim(strtolower($sec_name_tag)));

      // TARGET TAGs ARRAY
      $target_tags = ['niftybanketf', 'goldetf', 'nifty50etf',
                      'silveretf', 'niftynext50etf', 's&pbsesensexetf',
                      'niftyhealthcareetf',  'niftyitetf', 'nifty200quality30etf',
                      'nifty200momentum30etf'];

      // IF we find TAG then we proceed
      $bottom = 437;
      $found = 0;
      $target_c_tag = '';

      foreach ($target_tags as $target_tag) {
         if (strpos($target_tag, $sec_name_tag) !== false) {
           $target_c_tag = $target_tag;
           $found++;
           break;
         }

         $bottom = $bottom - 14;
      }

      // PROCEED if FOUND
      if ( $found ) {
        // TR TYPE
        if ($ticket->type == 1) {   // BUY / PURCHASE

          if ($payment_type == 1) {  // CASH
            $text = str_replace('<!--TRTYPEPURCHASECASH-->',
            '<span class="ff3 ws3" style="display:inline-block;margin-left: 50px;position: absolute;">3</span>', $text);
          }

          if ($payment_type == 2) { // BASKET
            $text = str_replace('<!--TRTYPEPURCHASEBASKET-->',
            '<span class="ff3 ws3" style="display:inline-block;margin-left: 117px;position: absolute;">3</span>', $text);
          }
        }

        if ($ticket->type == 2) { // SELL / REDEEM

          if ($payment_type == 1) { // CASH

            $text = str_replace('<!--TRTYPEREDEEMCASH-->',
            '<span class="ff3 ws3" style="display:inline-block;margin-left:-48px;position: absolute;">3</span>', $text);
          }

          if ($payment_type == 2) { // BASKET

            $text = str_replace('<!--TRTYPEREDEEMBASKET-->',
            '<span class="ff3 ws3" style="display:inline-block;margin-left:116px;position: absolute;">3</span>', $text);
          }
        }

        // ADD TICK next to Security Name
        $tick_target = '<!--' . $target_c_tag . ':tick-->';

        $text = str_replace($tick_target,
                            '<span class="ff3 ws3" style="display:inline-block;left:10px;margin-left:-54px;margin-top:5px;position: absolute;">3</span>',
                            $text
                           );

        // INSERT Pricing DETAILS
        $replace_str = '<div class="c x17 w9 h11" style="bottom:' . $bottom . 'px"><div class="t m0 x0 hf y50 ff7 fs3 fc2 sc0 ls0"><totalunits></div></div><div class="c x18 wa h11" style="bottom:' . $bottom . 'px"><div class="t m0 x0 hf y50 ff7 fs3 fc2 sc0 ls0"><totalbaskets></div></div><div class="c x19 wb h11" style="bottom:' . $bottom . 'px"><div class="t m0 x0 hf y50 ff7 fs3 fc2 sc0 ls0 wsb"><totalunitsinwords></div></div>';
        $basket_size   = $ticket->basket_size;
        $ticket_basket = $ticket->basket_no; // NO. of Basket
        $total_units   = (double) $ticket->basket_size * (double) $ticket->basket_no;
        $total_units_in_float = (float) $total_units;
        $total_units_in_words = trim(self::NumberintoWords( $total_units_in_float)); // Total Units in Words
        $total_units_in_words = ('' == $total_units_in_words ? 'Zero Only' : $total_units_in_words . ' Only');
        $replace_str = str_replace('<totalunits>', $total_units, $replace_str);
        $replace_str = str_replace('<totalbaskets>', $ticket_basket, $replace_str);
        $replace_str = str_replace('<totalunitsinwords>', $total_units_in_words, $replace_str);
        $text = str_replace('<!--PRICINGDETAILS-->', $replace_str, $text);

        // INSERT TOTAL VALUE
        $total_amt = $ticket->total_amt;
        $text = str_replace('<!--TOTALVALUE-->', $total_amt, $text);
        $word_text = trim(self::NumberintoWords($total_amt));
        $word_text = ('' == $word_text ? 'Zero Only' : $word_text . ' Only');
        $text = str_replace('<!--TOTALVALUEINWORDS-->', $word_text, $text);

        // NOW SAVE FILE
        self::saveDocument($ticket->id, $text);
        //Storage::put('public/ticketpdfs/ticket-' . $ticket->id . '.html', $text);
        //$pdf_file_name = "../storage/app/public/ticketpdfs/ticket-" . $ticket->id . ".pdf";
        //Pdf::loadHTML($text)->save($pdf_file_name);

        return 1;
      } else { // If FOUND
        return 0;
      }
    } else { // IF FORM FILE exists
      return 0;
    }
  }

  public static function saveDocument($ticketid, $text)
  {
    Storage::put('public/ticketpdfs/ticket-' . $ticketid . '.html', $text);
    //$pdf_file_name = "../storage/app/public/ticketpdfs/ticket-" . $ticketid . ".pdf";
    //Pdf::loadHTML($text)->save($pdf_file_name);
  }

  public static function GenerateDocument($ticket) {

    $sec_name = $ticket->security->name;

    if ($sec_name) {

        // Aditya BIRLA Form
        if ( strpos($sec_name, "ADITYA BIRLA") == 0 ) {
          self::handleBirlaFormNew($ticket);
        }

        //
        if ( strpos($sec_name, "UTI") == 0 ) {
          self::handleLICForm($ticket);
        }
    }
  }

  public static function NumberintoWords(float $number)
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
}
