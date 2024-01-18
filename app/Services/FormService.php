<?php
namespace App\Services;

use Storage;
use Validator;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Barryvdh\DomPDF\Facade\Pdf;

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
          self::handleBirlaForm($ticket);
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
