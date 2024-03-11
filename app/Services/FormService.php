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
    public static $tickImage = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAYAAACqaXHeAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAB2AAAAdgB+lymcgAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAAMLSURBVHic7dpPiFVlGMfxzzjjFaywxBKEXFQiulFwoWh/rECIIBdGFIIMSIERtBBxJeiiQJFAQqRFIARSRlaLghZCECJERosiRCpCqVBQCU1JvdfFWzhz59yZ95x73/M6er7wbIZ73/P9PWfOPe973kNDQ0NDQ8Ndy3BugZpYinfwCI5ndqmVediPa+jgBlpZjWqiha24IAT/v97KKVUXz+KU8cGv49WcUnVwP95D2/jwV/FiRq9aeAl/GR+8g7/xVEav5DyAj00M3sElPJ5PLT2r8Kvi8JfxdD61tIxgp/DDVhT+H+GH8I7kQXytOHhHuN8/l0suNcvwm97hO9iSzS4xrwjX9WTh92SzS8ybJt7bu+sTzMglmIoh7DV58A6+wz2ZHJPR0vv+PrZ+x/xMjslo4VNTh/8XqzM5JqOFz0wdvoPXMzkmYxhHxIX/MJNjUt4VF/405mZyTMYuceFvYO0gDzxS8LchLBT/vLCNM8LcvAovY0fkZ/cKU+GkfCvubIytU1hQ4VgrhMVLzDF+xqyKmaIZwdlIoe46gXtLHGuO3svZ7mrjib6SleAZXIkU664vFV9WRRwqMe6B/mOVY4Pe6+1ByL5WYrw/hP+W2tlk6kVIr9o2ybgLhWd1sWONDjRVSbYXCMVesxt7jPl5iXG+dxus8nar1oQrWNM11vqSYzyZLlY8Q3hftSacw6L/xhnGjyW+ezh5shIMC0JVmvALHsLmEt+5jiW1JCvBLBxVrQnHhDl87Oc/qClTaWYLYao0oczZX1xXoCrMw0/SNeBgbUn64GHhcdSgw7fd5md/LI8p3pDsp76oNcEAWGbiSwj91Lp69QfDWtUXT2PrpDDnmJa84NZ7OFVr2m9rjaq+eLqM+2o3TsAbqjXgUA7ZVLytfAOez2KakH3iw5/FzDya6ZghbF7ENGBfJsfktPCVqRtwR7/INBvf6B3+vPiHqNOWOfhBcQM+yuhVKwsU7wGMZnSqnUfxp1vh26rtJk1rluOi0IATmV2ysVKY+a3MLdLQ0NDQ0HCXcxMNSZj+UasGowAAAABJRU5ErkJggg==";

    // Handle LIC FORM
    private static function handleLICForm($ticket)
    {
        try {
			
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

		  $checkboxImageData = self::$tickImage;
		  
		   // BUY CASES
		  if ($ticket->type == 1) {
				$images[] =  [
					  "url" => $checkboxImageData, "x" => 39, "y" => 330.06, "width" => 17, "height" => 14, "pages" => "0", "keepAspectRatio" => true
				  ];

				$textannotations[] = ["text"=> "$total_amt", "x"=> 92.37, "y"=> 347,"size"=>7,"width"=> 137, "height"=> 10, "pages"=> "0", "type"=> "text"];
				$textannotations[] = ["text"=> "$word_text", "x"=> 236.72, "y"=> 346.39,  "width"=> 300,"size"=>7,"height"=> 13, "pages"=> "0", "type"=> "text"];		
				  
		  }
		  // SELL CASES
		  else if ($ticket->type == 2) {
			   $images[] = [
				"url" => $checkboxImageData, "x" => 39, "y" => 485.28,  "width" => 15, "height" => 14, "pages" => "0", "keepAspectRatio" => true
			  ];

		  }
		  
		  // INSERT PLAN NAME
		  $textannotations[] = ["text"=> "$sec_name", "x"=> 87.51, "y"=> 308.8,"size"=>7,"width"=> 210, "height"=> 10, "pages"=> "0", "type"=> "text"];
		  
		  // INSERT DATE 
		  $textannotations[] = ["text"=> date("d-m-Y", time()), "x"=> 61.57, "y"=> 620.91,"size"=>7,"width"=> 120, "height"=> 10, "pages"=> "0", "type"=> "text"];
	  
		  
		  // call API 
		  $urlToken = "filetoken://fdfcae17dcb7ec00fd43a19785bb7106d7a07b839682e03c09";
		  self::callAPIandSaveFile($urlToken, $images, $textannotations, $ticket->id);
		}
		catch (\Exception $e) 
		{
			dd($e->getMessage());
        }
    }


	// Handle MIRAE FORM
	private static function handleMIRAEForm($ticket) 
	{

		try 
		{	
		  
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

		  $checkboxImageData = self::$tickImage;
		  
		   // BUY CASES
		  if ($ticket->type == 1) {
			  // INSERT PLAN NAME
			  $textannotations[] = ["text"=> "$sec_name", "x"=> 58.72, "y"=> 232.43,"size"=>7,"width"=> 200, "height"=> 10, "pages"=> "0", "type"=> "text"];
			  
			  $textannotations[] = ["text"=> "$total_amt", "x"=> 252.77, "y"=> 322.58,"size"=>7, "width"=> 120, "height"=> 10, "pages"=> "0", "type"=> "text"];
			  
			  // INSERT UTR Number 
			  $utr_no = $ticket->utr_no;
			  if($utr_no !='')
			  {
				$textannotations[] = ["text"=> "$utr_no", "x"=> 26.48, "y"=> 321.98,"size"=>7,"width"=> 137, "height"=> 10, "pages"=> "0", "type"=> "text"];
			  }

		  }
		  // SELL CASES
		  else if ($ticket->type == 2) {
			 // INSERT PLAN NAME
			  $textannotations[] = ["text"=> "$sec_name", "x"=> 58.72, "y"=> 438.98,"size"=>7,"width"=> 200, "height"=> 10, "pages"=> "0", "type"=> "text"]; 

		  }
		  
		  
		  // call API 
		  $urlToken = "filetoken://3f6997fadf719169ba3441d8aad68aac8243ffd3be528001c5";
		  self::callAPIandSaveFile($urlToken, $images, $textannotations, $ticket->id);
		}
		catch (\Exception $e) 
		{
			dd($e->getMessage());
		}
	  
	}

    
	// Handle UTI FORM
	private static function handleUTIForm($ticket) 
	{

		try 
		{	
		  
		  $marker_json = [];
		  $textannotations = [];
		  $images = [];

		  // Payment TYPE
		  $payment_type = $ticket->payment_type ;
		  // Security Name
		  $sec_name = $ticket->security->name;
		  // UTR NO.
		  $utr_no = $ticket->utr_no;
		  
		  // OTHER DETAILS
		  $basket_size   = $ticket->basket_size;
		  $ticket_basket = $ticket->basket_no; // NO. of Basket
		  $total_units   = (double) $ticket->basket_size * (double) $ticket->basket_no;
		  $total_units_in_float = (float) $total_units;
		  $total_units_in_words = trim(self::NumberintoWords( $total_units_in_float)); // Total Units in Words
		  $total_units_in_words = ('' == $total_units_in_words ? 'Zero Only' : $total_units_in_words . ' Only');
		  $total_amt = $ticket->total_amt;
		  $word_text = trim(self::NumberintoWords($total_amt));
		  $word_text = ('' == $word_text ? 'Zero Only' : $word_text . ' Only');

		  $checkboxImageData = self::$tickImage;
		  
		  // BUY CASES
		  if ($ticket->type == 1) {
			  
			  $images[] =  [
				"url" => $checkboxImageData, "x"=>149.51, "y"=>276.0, "width"=>17, "height"=>14, "pages"=>"0", "keepAspectRatio" => true
			  ];
			 
		  }
		  // SELL CASES
		  else if ($ticket->type == 2) {
			 
			  $images[] = [
				"url" => $checkboxImageData, "x"=>442.0, "y"=>276.0, "width"=>17, "height"=>14, "pages"=>"0", "keepAspectRatio" => true
			  ];			 
		  }
		  
		  
		  if(strtolower($sec_name) == 'uti nifty etf')
		  {
			  $images[] = ["url" => $checkboxImageData, "x" => 124.85, "y" => 335.77,"size"=>7, "width" => 11, "height" =>10, "pages" => "0", "keepAspectRatio" => true];  
			  $textannotations[] = ["text" => "$ticket_basket", "x" => 256.4, "y" => 335.77,"size"=>7, "width" => 57.57, "height" => 11.37, "pages" => "0", "type" => "text"];	  
			  $textannotations[] = ["text"=> "$total_units", "x"=> 350.28,  "y"=> 438.61,"size"=>7,"width"=> 100.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
			  $textannotations[] = ["text"=> "$total_amt", "x"=>465.02, "y"=>438.61,"size"=>7,"width"=> 100.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
		  }
		  else if(strtolower($sec_name) == 'uti sensex etf')
		  {
			  $images[] = ["url" => $checkboxImageData, "x" => 124.85, "y" => 355.47,"size"=>7, "width" => 11, "height" =>10, "pages" => "0", "keepAspectRatio" => true];  
			  $textannotations[] = ["text" => "$ticket_basket", "x" => 256.4, "y" => 355.47,"size"=>7, "width" => 57.57, "height" => 11.37, "pages" => "0", "type" => "text"];	  
			  $textannotations[] = ["text"=> "$total_units", "x"=> 350.28,  "y"=> 355.47,"size"=>7,"width"=> 100.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
			  $textannotations[] = ["text"=> "$total_amt", "x"=>465.02, "y"=>355.47,"size"=>7,"width"=> 100.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
		  }
		  else if(strtolower($sec_name) == 'uti nifty next 50 etf')
		  {
			  $images[] = ["url" => $checkboxImageData, "x" => 124.85, "y" => 375.47,"size"=>7, "width" => 11, "height" =>10, "pages" => "0", "keepAspectRatio" => true];  
			  $textannotations[] = ["text" => "$ticket_basket", "x" => 256.4, "y" => 375.47,"size"=>7, "width" => 57.57, "height" => 11.37, "pages" => "0", "type" => "text"];	  
			  $textannotations[] = ["text"=> "$total_units", "x"=> 350.28,  "y"=> 375.47,"size"=>7,"width"=> 100.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
			  $textannotations[] = ["text"=> "$total_amt", "x"=>465.02, "y"=>375.47,"size"=>7,"width"=> 100.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
		  }
		  else if(strtolower($sec_name) == 'uti bank etf')
		  {
			  $images[] = ["url" => $checkboxImageData, "x" => 124.85, "y"=>395.47,"size"=>7, "width" => 11, "height" =>10, "pages" => "0", "keepAspectRatio" => true];  
			  $textannotations[] = ["text" => "$ticket_basket", "x" => 256.4, "y"=>395.47,"size"=>7, "width" => 57.57, "height" => 11.37, "pages" => "0", "type" => "text"];	  
			  $textannotations[] = ["text"=> "$total_units", "x"=> 350.28, "y"=>395.47,"size"=>7,"width"=> 100.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
			  $textannotations[] = ["text"=> "$total_amt", "x"=>465.02, "y"=>395.47,"size"=>7,"width"=> 100.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
		  }
		  else if(strtolower($sec_name) == 'uti s&p bse sensex next 50 etf')
		  {
			  $images[] = ["url" => $checkboxImageData, "x" => 124.85, "y"=>415.47,"size"=>7, "width" => 11, "height" =>10, "pages" => "0", "keepAspectRatio" => true];  
			  $textannotations[] = ["text" => "$ticket_basket", "x" => 256.4, "y"=>415.47,"size"=>7, "width" => 57.57, "height" => 11.37, "pages" => "0", "type" => "text"];	  
			  $textannotations[] = ["text"=> "$total_units", "x"=> 350.28, "y"=>415.47,"size"=>7,"width"=> 100.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
			  $textannotations[] = ["text"=> "$total_amt", "x"=>465.02, "y"=>415.47,"size"=>7,"width"=> 100.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
		  }
		  
		  // UTR and TOTAL Amount 
		  $textannotations[] = ["text"=> "$utr_no", "x"=>73.85, "y"=>700.82,"size"=>7,"width"=> 140.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
		  $textannotations[] = ["text"=> "$total_amt", "x"=>69.78, "y"=>718.48,"size"=>7,"width"=> 140.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
		  
		  // call API 
		  $urlToken = "filetoken://be6e5905de092f6eea647c5341dc305a222abdcf167a001fd5";
		  self::callAPIandSaveFile($urlToken, $images, $textannotations, $ticket->id);
		}
		catch (\Exception $e) 
		{
			dd($e->getMessage());
		}
	  
	}
	
	// Handle MOTILAL FORM
	private static function handleMOTILALForm($ticket) 
	{

		try 
		{	
		  
			$marker_json = [];
			$textannotations = [];
			$images = [];

			// Payment TYPE
			$payment_type = $ticket->payment_type ;
			// Security Name
			$sec_name = $ticket->security->name;
			// UTR NO.
			$utr_no = $ticket->utr_no;

			// OTHER DETAILS
			$basket_size   = $ticket->basket_size;
			$ticket_basket = $ticket->basket_no; // NO. of Basket
			$total_units   = (double) $ticket->basket_size * (double) $ticket->basket_no;
			$total_units_in_float = (float) $total_units;
			$total_units_in_words = trim(self::NumberintoWords( $total_units_in_float)); // Total Units in Words
			$total_units_in_words = ('' == $total_units_in_words ? 'Zero Only' : $total_units_in_words . ' Only');
			$total_amt = $ticket->total_amt;
			$word_text = trim(self::NumberintoWords($total_amt));
			$word_text = ('' == $word_text ? 'Zero Only' : $word_text . ' Only');

			$checkboxImageData = self::$tickImage;

			// BUY CASES
			if ($ticket->type == 1) {
			  
			  if ($payment_type == 1) 
			  {
				  $images[] =  [
					"url" => $checkboxImageData, "x"=>468.07, "y"=>427.55, "width"=>17, "height"=>14, "pages"=>"0", "keepAspectRatio" => true
				  ];
			  } else if ($payment_type == 2) {
				  
				  $images[] =  [
					"url" => $checkboxImageData, "x"=>468.07, "y"=>442.54, "width"=>17, "height"=>14, "pages"=>"0", "keepAspectRatio" => true
				  ];
			  }
			 
			}
			// SELL CASES
			else if ($ticket->type == 2) {
			 
			  if ($payment_type == 1) 
			  {
				  $images[] =  [
					"url" => $checkboxImageData, "x"=>562.67, "y"=>427.55, "width"=>17, "height"=>14, "pages"=>"0", "keepAspectRatio" => true
				  ];
			  } else if ($payment_type == 2) {
				  
				  $images[] =  [
					"url" => $checkboxImageData, "x"=>562.67, "y"=>442.54, "width"=>17, "height"=>14, "pages"=>"0", "keepAspectRatio" => true
				  ];
			  }			 
			}

			$productCheckArr = ["url" => $checkboxImageData, "x" => 15.27, "y" =>0.0, "size"=>7, "width" => 11, "height" =>10, "pages" => "0", "keepAspectRatio" => true];
			if(strtolower($sec_name) == 'motilal oswal nifty 50 etf (m50)')
			{
			  $productCheckArr['y'] = 393.76; 
			}
			else if(strtolower($sec_name) == 'motilal oswal nifty midcap 100 etf')
			{
			  $productCheckArr["y"] = 413.76; 
			}
			else if(strtolower($sec_name) == 'motilal oswal nasdaq 100 etf')
			{
			  $productCheckArr["y"] = 433.76; 
			}
			else if(strtolower($sec_name) == 'motilal oswal nifty 5 year benchmark g-sec etf')
			{
			  $productCheckArr["y"] = 453.76; 
			}
			else if(strtolower($sec_name) == 'motilal oswal nasdaq q 50 etf')
			{
			  $productCheckArr["y"] = 473.76; 
			}
			else if(strtolower($sec_name) == 'motilal oswal nifty 200 momentum 30 etf')
			{
			  $productCheckArr["y"] = 493.76; 
			}
			else if(strtolower($sec_name) == 'motilal oswal s&p bse low volatility etf')
			{
			  $productCheckArr["y"] = 513.76; 
			}
			else if(strtolower($sec_name) == 'motilal oswal s&p bse healthcare etf')
			{
			  $productCheckArr["y"] = 533.76; 
			}
			else if(strtolower($sec_name) == 'motilal oswal s&p bse quality etf')
			{
			  $productCheckArr["y"] = 553.76; 
			}
			else if(strtolower($sec_name) == 'motilal oswal s&p bse enhanced value etf')
			{
			  $productCheckArr["y"] = 573.76; 
			}
			else if(strtolower($sec_name) == 'motilal oswal nifty 500 etf')
			{
			  $productCheckArr["y"] = 593.76; 
			}

			$images[] = $productCheckArr;

			// Total Amount
			$textannotations[] = ["text"=> "$total_amt", "x"=>84.5, "y"=>702.61,"size"=>7,"width"=> 100.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
			
			// UTR
		    $textannotations[] = ["text"=> "$utr_no", "x"=>127.76, "y"=>727.41,"size"=>7,"width"=>180.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];

			// Ticket Basket
		    $textannotations[] = ["text" => "$ticket_basket", "x" =>126.03, "y" =>660.52,"size"=>7, "width" => 57.57, "height" => 11.37, "pages" => "0", "type" => "text"];	  
			
			// Total Units
			$textannotations[] = ["text"=> "$total_units", "x"=>419.05,  "y"=>661.1,"size"=>7,"width"=> 100.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
			  
		  
			// call API 
			$urlToken = "filetoken://e288c52222b635ada94d16f5ba83630aca4a08759ecc7ef93a";
			self::callAPIandSaveFile($urlToken, $images, $textannotations, $ticket->id);
		}
		catch (\Exception $e) 
		{
			dd($e->getMessage());
		}
	  
	}
	
  // HANDLE AXIS FORM thru API
  private static function handleAXISForm($ticket) {
	  
	try {  
	  
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
	  
	  $checkboxImageData = self::$tickImage;
	  
	  // BUY CASES
      if ($ticket->type == 1) {

		$images[] =  [
            "url" => $checkboxImageData, "x" => 121.54, "y" => 228.54, "width" => 17, "height" => 14, "pages" => "0", "keepAspectRatio" => true
        ];
        
		if ($payment_type == 1) {  // CASH
            $images[] =  [
                  "url" => $checkboxImageData, "x" => 180.91, "y" => 245.84, "width" => 17, "height" => 14, "pages" => "0", "keepAspectRatio" => true
              ];
        }

        if ($payment_type == 2) { // BASKET
          $images[] =  [
                "url" => $checkboxImageData, "x" => 229.55, "y" =>  245.84, "width" => 17,   "height" => 14, "pages" =>  "0", "keepAspectRatio" => true
            ];
        }
		
		// IF BUY CASE, add UTR NO, AMOUNT, AMount in WORDS
		$utr_no = $ticket->utr_no;
		if($utr_no !='')
		{
			$textannotations[] = ["text"=> "$utr_no", "x"=> 326.83, "y"=> 279.61,"size"=>7,"width"=> 137, "height"=> 10, "pages"=> "0", "type"=> "text"];
		}
		
		$textannotations[] = ["text"=> "$total_amt", "x"=> 89.08, "y"=> 294.44,"size"=>7,"width"=> 137, "height"=> 10, "pages"=> "0", "type"=> "text"];
		$textannotations[] = ["text"=> "$word_text", "x"=> 330.65, "y"=> 294.44,  "width"=> 300,"size"=>7,"height"=> 13, "pages"=> "0", "type"=> "text"];
		
      }
	  
	  // SELL CASES
      if ($ticket->type == 2) {
		  
		$images[] =  [
            "url" => $checkboxImageData, "x" => 298.8, "y" => 228.54, "width" => 17, "height" => 14, "pages" => "0", "keepAspectRatio" => true
        ];
		
        if ($payment_type == 1) {  // CASH
           $images[] = [
            "url" => $checkboxImageData, "x" => 128.98, "y" => 612.38,  "width" => 15, "height" => 14, "pages" => "0", "keepAspectRatio" => true
          ];
        }

        if ($payment_type == 2) { // BASKET
          $images[] = [
           "url" => $checkboxImageData, "x" => 177.62, "y" => 612.38, "width" => 15, "height" => 14,  "pages" => "0",  "keepAspectRatio" => true
         ];
        }
      }
	  
	  // SELECTION of PRODUCTS
	  if(strtolower($sec_name) == 'axis gold etf')
      {
          $images[] = ["url" => $checkboxImageData, "x" => 20.94, "y" => 438.6,"size"=>7, "width" => 11, "height" =>10, "pages" => "0", "keepAspectRatio" => true];  
		  $textannotations[] = ["text" => "$ticket_basket", "x" => 165.22, "y" => 436.6,"size"=>7, "width" => 57.57, "height" => 11.37, "pages" => "0", "type" => "text"];	  
          $textannotations[] = ["text"=> "$total_units", "x"=> 278.17,  "y"=> 438.61,"size"=>7,"width"=> 71.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
      }
	  else if(strtolower($sec_name) == 'axis nifty 50 etf')
      {
          $images[] = ["url" => $checkboxImageData, "x" => 20.94, "y" => 457.56,"size"=>7, "width" => 11, "height" =>10, "pages" => "0", "keepAspectRatio" => true];  
		  $textannotations[] = ["text" => "$ticket_basket", "x" => 165.22, "y" => 457.56,"size"=>7, "width" => 57.57, "height" => 11.37, "pages" => "0", "type" => "text"];	  
          $textannotations[] = ["text"=> "$total_units", "x"=> 278.17,  "y"=> 457.56,"size"=>7,"width"=> 71.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
      }
	  else if(strtolower($sec_name) == 'axis nifty bank etf')
      {
          $images[] = ["url" => $checkboxImageData, "x" => 20.94, "y" => 476.51,"size"=>7, "width" => 11, "height" =>10, "pages" => "0", "keepAspectRatio" => true];  
		  $textannotations[] = ["text" => "$ticket_basket", "x" => 165.22, "y" => 476.51,"size"=>7, "width" => 57.57, "height" => 11.37, "pages" => "0", "type" => "text"];	  
          $textannotations[] = ["text"=> "$total_units", "x"=> 278.17,  "y"=> 476.51,"size"=>7,"width"=> 71.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
      }
	  else if(strtolower($sec_name) == 'axis nifty it etf')
      {
          $images[] = ["url" => $checkboxImageData, "x" => 20.94, "y" => 494.63,"size"=>7, "width" => 11, "height" =>10, "pages" => "0", "keepAspectRatio" => true];  
		  $textannotations[] = ["text" => "$ticket_basket", "x" => 165.22, "y" => 494.63,"size"=>7, "width" => 57.57, "height" => 11.37, "pages" => "0", "type" => "text"];	  
          $textannotations[] = ["text"=> "$total_units", "x"=> 278.17,  "y"=> 494.63,"size"=>7,"width"=> 71.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
      }
	  else if(strtolower($sec_name) == 'axis nifty aaa bond plus sdl apr 2026 50:50 etf')
      {
          $images[] = ["url" => $checkboxImageData, "x" => 20.94, "y" => 513.52,"size"=>7, "width" => 11, "height" =>10, "pages" => "0", "keepAspectRatio" => true];  
		  $textannotations[] = ["text" => "$ticket_basket", "x" => 165.25, "y" => 513.52,"size"=>7, "width" => 57.57, "height" => 11.37, "pages" => "0", "type" => "text"];	  
          $textannotations[] = ["text"=> "$total_units", "x"=> 278.17,  "y"=> 513.52,"size"=>7,"width"=> 71.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
      }
	  else if(strtolower($sec_name) == 'axis nifty healthcare etf')
      {
          $images[] = ["url" => $checkboxImageData, "x" => 20.94, "y" => 532.47,"size"=>7, "width" => 11, "height" =>10, "pages" => "0", "keepAspectRatio" => true];  
		  $textannotations[] = ["text" => "$ticket_basket", "x" => 165.25, "y" => 532.47,"size"=>7, "width" => 57.57, "height" => 11.37, "pages" => "0", "type" => "text"];	  
          $textannotations[] = ["text"=> "$total_units", "x"=> 278.17,  "y"=> 532.47,"size"=>7,"width"=> 71.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
      }
	  else if(strtolower($sec_name) == 'axis nifty india consumption etf')
      {
          $images[] = ["url" => $checkboxImageData, "x" => 20.94, "y" => 551.42,"size"=>7, "width" => 11, "height" =>10, "pages" => "0", "keepAspectRatio" => true];  
		  $textannotations[] = ["text" => "$ticket_basket", "x" => 165.25, "y" => 551.42,"size"=>7, "width" => 57.57, "height" => 11.37, "pages" => "0", "type" => "text"];	  
          $textannotations[] = ["text"=> "$total_units", "x"=> 278.17,  "y"=> 551.42,"size"=>7,"width"=> 71.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
      }
	  else if(strtolower($sec_name) == 'axis silver etf')
      {
          $images[] = ["url" => $checkboxImageData, "x" => 20.94, "y" => 571.19,"size"=>7, "width" => 11, "height" =>10, "pages" => "0", "keepAspectRatio" => true];  
		  $textannotations[] = ["text" => "$ticket_basket", "x" => 165.25, "y" => 571.19,"size"=>7, "width" => 57.57, "height" => 11.37, "pages" => "0", "type" => "text"];	  
          $textannotations[] = ["text"=> "$total_units", "x"=> 278.17,  "y"=> 571.19,"size"=>7,"width"=> 71.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
      }
	  else if(strtolower($sec_name) == 'axis s&p bse sensex etf')
      {
          $images[] = ["url" => $checkboxImageData, "x" => 20.94, "y" => 590.14,"size"=>7, "width" => 11, "height" =>10, "pages" => "0", "keepAspectRatio" => true];  
		  $textannotations[] = ["text" => "$ticket_basket", "x" => 165.25, "y" => 590.14,"size"=>7, "width" => 57.57, "height" => 11.37, "pages" => "0", "type" => "text"];	  
          $textannotations[] = ["text"=> "$total_units", "x"=> 278.17,  "y"=> 590.14,"size"=>7,"width"=> 71.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
      }
	  
	  // call API 
	  $urlToken = "filetoken://81e457ba1f9ad8bfc357b4fb3cba61584b79d5a18bbb3f1312";
	  self::callAPIandSaveFile($urlToken, $images, $textannotations, $ticket->id);
	}
	catch (\Exception $e) 
	{
		dd($e->getMessage());
	}
  }


  // HANDLE BIRLA FORM thru API
  private static function handleBirlaForm($ticket) 
  {

      try 
	  {
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

		  $checkboxImageData = self::$tickImage;
		  
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
			  $textannotations[] = ["text"=> "$total_units", "x"=> 321.09,  "y"=> 435,"size"=>7, "width"=> 71.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
			  $textannotations[] = ["text"=> "$total_units_in_words", "x"=> 404, "y"=> 435,"size"=>7, "width"=> 156, "height"=> 13, "pages"=> "0", "type" => "text"];
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
			if($utr_no !='')
			{
				$textannotations[] = ["text"=> "$utr_no", "x"=> 391.48, "y"=> 550.84,"size"=>7,"width"=> 137, "height"=> 10, "pages"=> "0", "type"=> "text"];
			}
			$textannotations[] = ["text"=> "$total_amt", "x"=> 112.92, "y"=> 567.15,"size"=>7,"width"=> 137, "height"=> 10, "pages"=> "0", "type"=> "text"];
			$textannotations[] = ["text"=> "$word_text", "x"=> 307, "y"=> 566,  "width"=> 300,"size"=>7,"height"=> 13, "pages"=> "0", "type"=> "text"];
		  }

		   // call API and SAVE the file
		  $urlToken = "filetoken://96ede08acd4b7256958e2f2d0d16f3166e551f75d39e58d938";
		  self::callAPIandSaveFile($urlToken, $images, $textannotations, $ticket->id);
	  }
	  catch (\Exception $e) 
	  {
		dd($e->getMessage());
	  }	  

  }

    private static function callAPIandSaveFile(
        $urlToken,
        $images,
        $textannotations,
        $ticketId
    ) {
      try {
        // BUILD MARKER JSON
        $marker_array = [
            "url" => $urlToken,
            "async" => false,
            "encrypt" => false,
            "inline" => true,
            "annotations" => $textannotations,
            "images" => $images,
            "fields" => [],
        ];

        $marker_json = json_encode($marker_array);

        // CALL API
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.pdf.co/v1/pdf/edit/add",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $marker_json,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "x-api-key: " . env("PDFCO_API_KEY"),
            ],
        ]);

        $response = json_decode(curl_exec($curl), true);
        if (curl_exec($curl) === false) {
            // LOG
            Log::debug("PDF GEneration Error : " . curl_error($curl));
        } else {
            if (isset($response["url"]) && $response["url"] != "") {
                $filecontent = file_get_contents($response["url"]);
                if ($filecontent) {
                    $fileName = "ticket-" . $ticketId . ".pdf";
                    if (
                        file_exists(
                            storage_path() . "/app/public/ticketpdfs/$fileName"
                        )
                    ) {
                        @unlink(
                            storage_path() . "/app/public/ticketpdfs/$fileName"
                        );
                    }
                    Storage::put(
                        "public/ticketpdfs/" . $fileName,
                        $filecontent
                    );
                    // suraj || set permission public
                    // Storage::setVisibility('public/ticketpdfs/' . $fileName, 'public');
                } else {
                    // LOG
                    Log::debug("PDF GEneration Error : File CONTENT missing");
                }
            } else {
                // LOG
                Log::debug("PDF GEneration Error : Response missing URL KEY");
            }
        }
      } catch (\Exception $e) {
        dd($e->getMessage());
      }
    }

    public static function saveDocument($ticketid, $text)
    {
      try{
        Storage::put("public/ticketpdfs/ticket-" . $ticketid . ".html", $text);
        //$pdf_file_name = "../storage/app/public/ticketpdfs/ticket-" . $ticketid . ".pdf";
        //Pdf::loadHTML($text)->save($pdf_file_name);
      } catch (\Exception $e) {
        dd($e->getMessage());
      }
    }

    public static function GenerateDocument($ticket)
    {
      try{
        $sec_name = $ticket->security->name;

        if ($sec_name) {
            // Aditya BIRLA Form
            if (strpos($sec_name, "ADITYA BIRLA") !== false) {
                Log::info("Generating PDF for ADITYA BIRLA");
                self::handleBirlaForm($ticket);
            } elseif (strpos($sec_name, "AXIS") !== false) {
                Log::info("Generating PDF for AXIS");
                self::handleAXISForm($ticket);
            } elseif (strpos($sec_name, "LIC") !== false) {
                Log::info("Generating PDF for LIC");
                self::handleLICForm($ticket);
            } elseif (strpos($sec_name, "MIRAE") !== false) {
                Log::info("Generating PDF for MIRAE");
                self::handleMIRAEForm($ticket);
			} elseif (strpos($sec_name, "UTI") !== false) {
                Log::info("Generating PDF for UTI");
                self::handleUTIForm($ticket);
            } elseif (strpos($sec_name, "MOTILAL") !== false) {
                Log::info("Generating PDF for MOTILAL");
                self::handleMOTILALForm($ticket);
            }
        }
      } catch (\Exception $e) {
        dd($e->getMessage());
      }
    }

    public static function NumberintoWords(float $number)
    {
      try{
        $number_after_decimal =
            round($number - ($num = floor($number)), 2) * 100;

        // Check if there is any number after decimal
        $amt_hundred = null;
        $count_length = strlen($num);
        $x = 0;
        $string = [];
        $change_words = [
            0 => "Zero",
            1 => "One",
            2 => "Two",
            3 => "Three",
            4 => "Four",
            5 => "Five",
            6 => "Six",
            7 => "Seven",
            8 => "Eight",
            9 => "Nine",
            10 => "Ten",
            11 => "Eleven",
            12 => "Twelve",
            13 => "Thirteen",
            14 => "Fourteen",
            15 => "Fifteen",
            16 => "Sixteen",
            17 => "Seventeen",
            18 => "Eighteen",
            19 => "Nineteen",
            20 => "Twenty",
            30 => "Thirty",
            40 => "Fourty",
            50 => "Fifty",
            60 => "Sixty",
            70 => "Seventy",
            80 => "Eighty",
            90 => "Ninety",
        ];
        $here_digits = ["", "Hundred", "Thousand", "Lakh", "Crore"];
        while ($x < $count_length) {
            $get_divider = $x == 2 ? 10 : 100;
            $number = floor($num % $get_divider);
            $num = floor($num / $get_divider);
            $x += $get_divider == 10 ? 1 : 2;
            if ($number) {
                $add_plural =
                    ($counter = count($string)) && $number > 9 ? "" : null;
                $amt_hundred = $counter == 1 && $string[0] ? " and " : null;
                $string[] =
                    $number < 21
                        ? (isset($change_words[$number])
                                ? $change_words[$number]
                                : "") .
                            " " .
                            (isset($here_digits[$counter])
                                ? $here_digits[$counter]
                                : "") .
                            $add_plural .
                            " " .
                            $amt_hundred
                        : $change_words[floor($number / 10) * 10] .
                            ($number % 10 != 0
                                ? " " . $change_words[$number % 10]
                                : "") .
                            " " .
                            $here_digits[$counter] .
                            $add_plural .
                            " " .
                            $amt_hundred;
            } else {
                $string[] = null;
            }
        }
        $implode_to_Words = implode("", array_reverse($string));

        $get_word_after_point =
            $number_after_decimal > 0
                ? "Point " .
                    ($change_words[$number_after_decimal / 10] .
                        "
      " .
                        $change_words[$number_after_decimal % 10])
                : "";

        return ($implode_to_Words ? $implode_to_Words : "") .
            ($get_word_after_point != "" ? " " . $get_word_after_point : "");
      } catch (\Exception $e) {
        dd($e->getMessage());
      }
    }
}
