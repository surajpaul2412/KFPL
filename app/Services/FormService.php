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
				
				// SHOW Total AMOUNT for only BUY/CASH cases
				if($ticket->payment_type == 1)
				{
					$textannotations[] = ["text"=> "$total_amt", "x"=> 92.37, "y"=> 347,"size"=>7,"width"=> 137, "height"=> 10, "pages"=> "0", "type"=> "text"];
					$textannotations[] = ["text"=> "$word_text", "x"=> 238.43, "y"=> 343.58, "width"=> 158.47,"size"=>6,"height"=> 20.16, "pages"=> "0", "type"=> "text"];		
				}	  
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
	  
		  Log::info("About to call API");
		  
		  // call API 
		  $urlToken = "filetoken://45143685a2149be764a4187019f1868a0242a2cb545987edd5";
		  self::callAPIandSaveFile($urlToken, $images, $textannotations, $ticket->id);
		}
		catch (\Exception $e) 
		{
			Log::info("Exception in LIC FORM");
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
		  $utr_no = $ticket->utr_no;
		  $checkboxImageData = self::$tickImage;
		  
		   // BUY CASES
		  if ($ticket->type == 1) {
			  
			// CASH OR BASKET 
			if($ticket->payment_type == 1)
			{
				$images[] = ["url" => $checkboxImageData, "x"=>138.15, "y"=>435.14, "width"=>10, "height"=>10, "pages"=>"0", "keepAspectRatio" => true];
			}
			else if($ticket->payment_type == 2)
			{
				$images[] = ["url" => $checkboxImageData, "x"=>192.19, "y"=>435.14, "width"=>10, "height"=>10, "pages"=>"0", "keepAspectRatio" => true];
			}

			// SHOW Total AMOUNT for only BUY/CASH cases
			if($ticket->payment_type == 1)
			{
				// INSERT Total Amount 
				$textannotations[] = ["text"=> "$total_amt", "x"=> 74.46, "y"=> 530.13,"size"=>7,"width"=>221.97, "height"=>12.76, "pages"=>"0", "type"=>"text"];
			}			  
			// INSERT UTR Number 
			if($utr_no !='')
			{
				$textannotations[] = ["text"=> "$utr_no", "x"=>103.54, "y"=> 542.7,"size"=>5,"width"=>197.85, "height"=>19.14, "pages"=>"0", "type"=>"text"];
			}
		  }
		  // SELL CASES
		  else if ($ticket->type == 2) {
			  
			  // CASH OR BASKET 
			  if($ticket->payment_type == 1)
			  {
				  $images[] = ["url" => $checkboxImageData, "x"=>475.14, "y"=>435.14, "width"=>10, "height"=>10, "pages"=>"0", "keepAspectRatio" => true];
			  }
			  else if($ticket->payment_type == 2)
			  {
				  $images[] = ["url" => $checkboxImageData, "x"=>524.06, "y"=>435.14, "width"=>10, "height"=>10, "pages"=>"0", "keepAspectRatio" => true];
			  }
		  }
		  
		  // INSERT PLAN NAME
		  $textannotations[] = ["text"=> "$sec_name", "x"=>26.24, "y"=> 474.26,"size"=>7,"width"=>102.12, "height"=> 40.41, "pages"=> "0", "type"=> "text"];
		  // BAsket SIZE
		  $textannotations[] = ["text"=> "$basket_size", "x"=>131.9, "y"=> 474.97,"size"=>7, "width"=> 110, "height"=> 12, "pages"=> "0", "type"=> "text"];
		  // No. of BAsket
		  $textannotations[] = ["text"=> "$ticket_basket", "x"=>246.66, "y"=> 474.97,"size"=>7, "width"=> 82, "height"=> 12, "pages"=> "0", "type"=> "text"];
		  // Total Units in BAsket
		  $textannotations[] = ["text"=> "$total_units_in_float", "x"=>331.88, "y"=> 474.97,"size"=>7, "width"=> 139.7, "height"=> 12, "pages"=> "0", "type"=> "text"];
		  // Total Units in BAsket, in WORDS
		  $textannotations[] = ["text"=> "$total_units_in_words", "x"=>472.59, "y"=> 474.97,"size"=>7, "width"=> 139.7, "height"=> 39, "pages"=> "0", "type"=> "text"];
		  
		  // call API 
		  Log::info("About to call API");
		  $urlToken = "filetoken://8a3a7b7bbc03bc04f7de356ed489a17827d47e6cee7e2ceefb";
		  self::callAPIandSaveFile($urlToken, $images, $textannotations, $ticket->id);
		}
		catch (\Exception $e) 
		{
			Log::info("Exception in MIRAE FORM");
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
			  if ($ticket->payment_type == 1) { 
				  $images[] =  [
					"url" => $checkboxImageData, "x"=>150.5, "y"=>254.81, "width"=>14, "height"=>14, "pages"=>"0", "keepAspectRatio" => true
				  ];
			  }
			  else 
			  {
				  $images[] =  [
					"url" => $checkboxImageData, "x"=>185.7, "y"=>254.81, "width"=>14, "height"=>14, "pages"=>"0", "keepAspectRatio" => true
				  ];
			  }
			 
			}
			// SELL CASES
			else if ($ticket->type == 2) {
			 
			  if ($ticket->payment_type == 1) { 
				  $images[] =  [
					"url" => $checkboxImageData, "x"=>443.01, "y"=>254.81, "width"=>14, "height"=>14, "pages"=>"0", "keepAspectRatio" => true
				  ];
			  }
			  else 
			  {
				  $images[] =  [
					"url" => $checkboxImageData, "x"=>479.49, "y"=>254.81, "width"=>14, "height"=>14, "pages"=>"0", "keepAspectRatio" => true
				  ];
			  }			 
			}

			if(strtolower($sec_name) == 'uti nifty 50 etf')
			{
				$images[] = ["url" => $checkboxImageData, "x" => 151.13, "y" => 309.31,"size"=>7, "width" => 11, "height" =>10, "pages" => "0", "keepAspectRatio" => true];  
				$textannotations[] = ["text" => "$ticket_basket", "x" => 272.67, "y" =>309.23,"size"=>7, "width" => 83.21, "height" => 11.94, "pages" => "0", "type" => "text"]; 
				$textannotations[] = ["text"=> "$total_units", "x"=>362.29,  "y"=>309.23,"size"=>7,"width"=>96.65, "height"=> 11.94, "pages"=> "0", "type" => "text"];
				
				// SHOW TOTAL AMOUNT :: BUY CASH CASES
				if ($ticket->type == 1 && $ticket->payment_type == 1) {
					$textannotations[] = ["text"=> "$total_amt", "x"=>467.26, "y"=>309.23,"size"=>7,"width"=> 100.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
				}
			}
			else if(strtolower($sec_name) == 'uti s&p bse sensex etf')
			{
				$images[] = ["url" => $checkboxImageData, "x" => 151.13, "y" => 322.11,"size"=>7, "width" => 11, "height" =>10, "pages" => "0", "keepAspectRatio" => true];  
				$textannotations[] = ["text" => "$ticket_basket", "x"=>272.67, "y"=>322.11,"size"=>7, "width"=>83.21, "height"=>11.94, "pages"=>"0", "type"=> "text"];
				$textannotations[] = ["text"=> "$total_units", "x"=>362.29, "y"=>322.11,"size"=>7,"width"=>96.65, "height"=> 11.94, "pages"=> "0", "type" => "text"];
				
				// SHOW TOTAL AMOUNT :: BUY CASH CASES
				if ($ticket->type == 1 && $ticket->payment_type == 1) {
					$textannotations[] = ["text"=> "$total_amt", "x"=>467.26, "y"=>322.11,"size"=>7,"width"=> 100.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
				}
			}
		  
			else if(strtolower($sec_name) == 'uti nifty next 50 etf')
			{
				$images[] = ["url" => $checkboxImageData, "x" => 151.13, "y" =>336.19,"size"=>7, "width" => 11, "height" =>10, "pages" => "0", "keepAspectRatio" => true];  
				$textannotations[] = ["text" => "$ticket_basket", "x"=>272.67, "y"=>335.47,"size"=>7, "width"=>83.21, "height"=>11.94, "pages"=>"0", "type"=> "text"];
				$textannotations[] = ["text"=> "$total_units", "x"=>362.29, "y"=>335.47,"size"=>7,"width"=>96.65, "height"=> 11.94, "pages"=> "0", "type" => "text"];
				
				// SHOW TOTAL AMOUNT :: BUY CASH CASES
				if ($ticket->type == 1 && $ticket->payment_type == 1) {
					$textannotations[] = ["text"=> "$total_amt", "x"=>467.26, "y"=>335.47,"size"=>7,"width"=> 100.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
				}
			}
			else if(strtolower($sec_name) == 'uti nifty bank etf')
			{
				$images[] = ["url" => $checkboxImageData, "x" => 151.13, "y" =>348.35,"size"=>7, "width" => 11, "height" =>10, "pages" => "0", "keepAspectRatio" => true]; 
				$textannotations[] = ["text" => "$ticket_basket", "x"=>272.67, "y"=>348.91,"size"=>7, "width"=>83.21, "height"=>11.94, "pages"=>"0", "type"=> "text"];
				$textannotations[] = ["text"=> "$total_units", "x"=>362.29, "y"=>348.91,"size"=>7,"width"=>96.65, "height"=> 11.94, "pages"=> "0", "type" => "text"];
				
				// SHOW TOTAL AMOUNT :: BUY CASH CASES
				if ($ticket->type == 1 && $ticket->payment_type == 1) {
					$textannotations[] = ["text"=> "$total_amt", "x"=>467.26, "y"=>348.91,"size"=>7,"width"=> 100.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
				}
			}
			else if(strtolower($sec_name) == 'uti s&p bse sensex next 50 etf')
			{
				$images[] = ["url" => $checkboxImageData, "x" => 151.13, "y" =>361.79,"size"=>7, "width" => 11, "height" =>10, "pages" => "0", "keepAspectRatio" => true]; 
				$textannotations[] = ["text" => "$ticket_basket", "x"=>272.67, "y"=>361.71,"size"=>7, "width"=>83.21, "height"=>11.94, "pages"=>"0", "type"=> "text"];
				$textannotations[] = ["text"=> "$total_units", "x"=>362.29, "y"=>361.71,"size"=>7,"width"=>96.65, "height"=> 11.94, "pages"=> "0", "type" => "text"];
				
				// SHOW TOTAL AMOUNT :: BUY CASH CASES
				if ($ticket->type == 1 && $ticket->payment_type == 1) {
					$textannotations[] = ["text"=> "$total_amt", "x"=>467.26, "y"=>361.71,"size"=>7,"width"=> 100.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
				}
			}
			else if(strtolower($sec_name) == 'uti nifty midcap 150 etf')
			{
				$images[] = ["url" => $checkboxImageData, "x" => 151.13, "y" =>373.95,"size"=>7, "width" => 11, "height" =>10, "pages" => "0", "keepAspectRatio" => true]; 
				$textannotations[] = ["text" => "$ticket_basket", "x"=>272.67, "y"=>375.15,"size"=>7, "width"=>83.21, "height"=>11.94, "pages"=>"0", "type"=> "text"];
				$textannotations[] = ["text"=> "$total_units", "x"=>362.29, "y"=>375.15,"size"=>7,"width"=>96.65, "height"=> 11.94, "pages"=> "0", "type" => "text"];
				
				// SHOW TOTAL AMOUNT :: BUY CASH CASES
				if ($ticket->type == 1 && $ticket->payment_type == 1) {
					$textannotations[] = ["text"=> "$total_amt", "x"=>467.26, "y"=>375.15,"size"=>7,"width"=> 100.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
				}
			}
			else if(strtolower($sec_name) == 'uti nifty it etf')
			{
				$images[] = ["url" => $checkboxImageData, "x" => 151.13, "y" =>388.03,"size"=>7, "width" => 11, "height" =>10, "pages" => "0", "keepAspectRatio" => true]; 
				$textannotations[] = ["text" => "$ticket_basket", "x"=>272.67, "y"=>387.31,"size"=>7, "width"=>83.21, "height"=>11.94, "pages"=>"0", "type"=> "text"];
				$textannotations[] = ["text"=> "$total_units", "x"=>362.29, "y"=>387.31,"size"=>7,"width"=>96.65, "height"=> 11.94, "pages"=> "0", "type" => "text"];
				
				// SHOW TOTAL AMOUNT :: BUY CASH CASES
				if ($ticket->type == 1 && $ticket->payment_type == 1) {
					$textannotations[] = ["text"=> "$total_amt", "x"=>467.26, "y"=>387.31,"size"=>7,"width"=> 100.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
				}
			}
			else if(strtolower($sec_name) == 'uti nifty 5 yr benchmark g-sec etf')
			{
				$images[] = ["url" => $checkboxImageData, "x" => 151.13, "y" =>401.47,"size"=>7, "width" => 11, "height" =>10, "pages" => "0", "keepAspectRatio" => true]; 
				$textannotations[] = ["text" => "$ticket_basket", "x"=>272.67, "y"=>400.11,"size"=>7, "width"=>83.21, "height"=>11.94, "pages"=>"0", "type"=> "text"];
				$textannotations[] = ["text"=> "$total_units", "x"=>362.29, "y"=>400.11,"size"=>7,"width"=>96.65, "height"=> 11.94, "pages"=> "0", "type" => "text"];
				
				// SHOW TOTAL AMOUNT :: BUY CASH CASES
				if ($ticket->type == 1 && $ticket->payment_type == 1) {
					$textannotations[] = ["text"=> "$total_amt", "x"=>467.26, "y"=>400.11,"size"=>7,"width"=> 100.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
				}
			}
			else if(strtolower($sec_name) == 'uti nifty 10 yr benchmark g-sec etf')
			{
				$images[] = ["url" => $checkboxImageData, "x" => 151.13, "y" =>413.63,"size"=>7, "width" => 11, "height" =>10, "pages" => "0", "keepAspectRatio" => true]; 
				$textannotations[] = ["text" => "$ticket_basket", "x"=>272.67, "y"=>414.84,"size"=>7, "width"=>83.21, "height"=>11.94, "pages"=>"0", "type"=> "text"];
				$textannotations[] = ["text"=> "$total_units", "x"=>362.29, "y"=>414.84,"size"=>7,"width"=>96.65, "height"=> 11.94, "pages"=> "0", "type" => "text"];
				
				// SHOW TOTAL AMOUNT :: BUY CASH CASES
				if ($ticket->type == 1 && $ticket->payment_type == 1) {
					$textannotations[] = ["text"=> "$total_amt", "x"=>467.26, "y"=>414.84,"size"=>7,"width"=> 100.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
				}
			}

			// UTR and TOTAL Amount 
			$textannotations[] = ["text"=> "$utr_no", "x"=>69.13, "y"=>696.56,"size"=>6,"width"=>512.71, "height"=> 15, "pages"=> "0", "type" => "text"];
			// SHOW TOTAL AMOUNT :: BUY CASH CASES
			if ($ticket->type == 1 && $ticket->payment_type == 1) {
				$textannotations[] = ["text"=> "$total_amt", "x"=>76.81, "y"=>715.13,"size"=>7,"width"=>236.83, "height"=> 11.94, "pages"=> "0", "type" => "text"];
			}

			// call API 
			Log::info("About to call API");
			$urlToken = "filetoken://604c96a606a9eabf8944c684bed999bbf41c81c5347ac66f30";
			self::callAPIandSaveFile($urlToken, $images, $textannotations, $ticket->id);
		}
		catch (\Exception $e) 
		{
			Log::info("Exception in UTI PDF generation");
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
			$prodFound = 0;
			if(strtolower($sec_name) == 'motilal oswal nifty 50 etf')
			{
			  $productCheckArr['y'] = 405; $prodFound = 1;
			}
			else if(strtolower($sec_name) == 'motilal oswal nifty midcap 100 etf')
			{
			  $productCheckArr["y"] = 423; $prodFound = 1;
			}
			else if(strtolower($sec_name) == 'motilal oswal nasdaq 100 etf')
			{
			  $productCheckArr["y"] = 441; $prodFound = 1;
			}
			else if(strtolower($sec_name) == 'motilal oswal nifty 5 year benchmark g-sec etf')
			{
			  $productCheckArr["y"] = 459; $prodFound = 1;
			}
			else if(strtolower($sec_name) == 'motilal oswal nasdaq q 50 etf')
			{
			  $productCheckArr["y"] = 477; $prodFound = 1;
			}
			else if(strtolower($sec_name) == 'motilal oswal nifty 200 momentum 30 etf')
			{
			  $productCheckArr["y"] = 495; $prodFound = 1;
			}
			else if(strtolower($sec_name) == 'motilal oswal s&p bse low volatility etf')
			{
			  $productCheckArr["y"] = 513; $prodFound = 1;
			}
			else if(strtolower($sec_name) == 'motilal oswal s&p bse healthcare etf')
			{
			  $productCheckArr["y"] = 531; $prodFound = 1;
			}
			else if(strtolower($sec_name) == 'motilal oswal s&p bse quality etf')
			{
			  $productCheckArr["y"] = 553; $prodFound = 1;
			}
			else if(strtolower($sec_name) == 'motilal oswal s&p bse enhanced value etf')
			{
			  $productCheckArr["y"] = 574; $prodFound = 1;
			}
			else if(strtolower($sec_name) == 'motilal oswal nifty 500 etf')
			{
			  $productCheckArr["y"] = 593; $prodFound = 1;
			}
			else if(strtolower($sec_name) == 'motilal oswal nifty realty etf')
			{
			  $productCheckArr["y"] = 611; $prodFound = 1;
			}
			else if(strtolower($sec_name) == 'motilal oswal nifty smallcap 250 etf')
			{
			  $productCheckArr["y"] = 630; $prodFound = 1;
			}
			
			if( $prodFound )
			{
			  $images[] = $productCheckArr;
			}
			
			// DATE 
			$date = date("d-m-Y", time());
			$textannotations[] = ["text"=> "$date", "x"=>52.68, "y"=>51.18,"size"=>10,"width"=> 115.45, "height"=> 12.97, "pages"=> "0", "type" => "text"];
			
			if ($ticket->type == 1) {
			
				// SHOW TOTAL AMOUNT :: BUY CASH CASES
				if ($ticket->type == 1 && $ticket->payment_type == 1) {
					// Total Amount
					$textannotations[] = ["text"=> "$total_amt", "x"=>84.5, "y"=>724.96,"size"=>10,"width"=> 200.74, "height"=> 14.94, "pages"=> "0", "type" => "text"];
				}
				
				// UTR
				$textannotations[] = ["text"=> "$utr_no", "x"=>117.61, "y"=>747.02,"size"=>6,"width"=>459.63, "height"=>18.74, "pages"=> "0", "type" => "text"];

			}
			
			// Ticket Basket
		    $textannotations[] = ["text" => "$ticket_basket", "x" =>124.11, "y" =>682.43,"size"=>10, "width" => 168.84, "height" => 14.37, "pages" => "0", "type" => "text"];	  
			
			// Total Units
			$textannotations[] = ["text"=> "$total_units", "x"=>413.45,  "y"=>682.43,"size"=>10,"width"=> 168.21, "height"=> 14.34, "pages"=> "0", "type" => "text"];
			  
			// call API 
			Log::info("About to call PDF API");
			$urlToken = "filetoken://f91edf47d3637b6ed791f83c6e02dbee311694bb59b0d6cb2e";
			self::callAPIandSaveFile($urlToken, $images, $textannotations, $ticket->id);
		}
		catch (\Exception $e) 
		{
			Log::info("Exception in MOTILAL PDF generation");
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
	
	  // ALIAS for TOTAL Unites
      $tuif = $total_units_in_float;
	  
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
		
		// SHOW TOTAL AMOUNT :: BUY CASH CASES
		if ( $ticket->payment_type == 1) {
			$textannotations[] = ["text"=> "$total_amt", "x"=> 89.08, "y"=> 294.44,"size"=>7,"width"=> 137, "height"=> 10, "pages"=> "0", "type"=> "text"];
			$textannotations[] = ["text"=> "$word_text", "x"=> 330.65, "y"=> 294.44,  "width"=> 300,"size"=>7,"height"=> 13, "pages"=> "0", "type"=> "text"];
		}
		
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
		  $textannotations[] = ["text" => "$tuif", "x" => 165.22, "y" => 436.6,"size"=>7, "width" => 57.57, "height" => 11.37, "pages" => "0", "type" => "text"];	
          
		  // SHOW TOTAL AMOUNT :: BUY CASH CASES
		  if ( $ticket->type == 1 && $ticket->payment_type == 1) {		  
			$textannotations[] = ["text"=> "$total_amt", "x"=> 278.17,  "y"=> 438.61,"size"=>7,"width"=> 71.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
		  }
      }
	  else if(strtolower($sec_name) == 'axis nifty 50 etf')
      {
          $images[] = ["url" => $checkboxImageData, "x" => 20.94, "y" => 457.56,"size"=>7, "width" => 11, "height" =>10, "pages" => "0", "keepAspectRatio" => true];  
		  $textannotations[] = ["text" => "$tuif", "x" => 165.22, "y" => 457.56,"size"=>7, "width" => 57.57, "height" => 11.37, "pages" => "0", "type" => "text"];	  
		  
		  // SHOW TOTAL AMOUNT :: BUY CASH CASES
		  if ( $ticket->type == 1 && $ticket->payment_type == 1) {		  
			$textannotations[] = ["text"=> "$total_amt", "x"=> 278.17,  "y"=> 457.56,"size"=>7,"width"=> 71.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
		  }
      }
	  else if(strtolower($sec_name) == 'axis nifty bank etf')
      {
          $images[] = ["url" => $checkboxImageData, "x" => 20.94, "y" => 476.51,"size"=>7, "width" => 11, "height" =>10, "pages" => "0", "keepAspectRatio" => true];  
		  $textannotations[] = ["text" => "$tuif", "x" => 165.22, "y" => 476.51,"size"=>7, "width" => 57.57, "height" => 11.37, "pages" => "0", "type" => "text"];	  
		  
		  // SHOW TOTAL AMOUNT :: BUY CASH CASES
		  if ( $ticket->type == 1 && $ticket->payment_type == 1) {		  
			$textannotations[] = ["text"=> "$total_amt", "x"=> 278.17,  "y"=> 476.51,"size"=>7,"width"=> 71.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
		  }
      }
	  else if(strtolower($sec_name) == 'axis nifty it etf')
      {
          $images[] = ["url" => $checkboxImageData, "x" => 20.94, "y" => 494.63,"size"=>7, "width" => 11, "height" =>10, "pages" => "0", "keepAspectRatio" => true];  
		  $textannotations[] = ["text" => "$tuif", "x" => 165.22, "y" => 494.63,"size"=>7, "width" => 57.57, "height" => 11.37, "pages" => "0", "type" => "text"];	

		  // SHOW TOTAL AMOUNT :: BUY CASH CASES
		  if ( $ticket->type == 1 && $ticket->payment_type == 1) {		  		  
			$textannotations[] = ["text"=> "$total_amt", "x"=> 278.17,  "y"=> 494.63,"size"=>7,"width"=> 71.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
		  }
      }
	  else if(strtolower($sec_name) == 'axis nifty aaa bond plus sdl apr 2026 50:50 etf')
      {
          $images[] = ["url" => $checkboxImageData, "x" => 20.94, "y" => 513.52,"size"=>7, "width" => 11, "height" =>10, "pages" => "0", "keepAspectRatio" => true];  
		  $textannotations[] = ["text" => "$tuif", "x" => 165.25, "y" => 513.52,"size"=>7, "width" => 57.57, "height" => 11.37, "pages" => "0", "type" => "text"];	
		  
		  // SHOW TOTAL AMOUNT :: BUY CASH CASES
		  if ( $ticket->type == 1 && $ticket->payment_type == 1) {		  		  
			$textannotations[] = ["text"=> "$total_amt", "x"=> 278.17,  "y"=> 513.52,"size"=>7,"width"=> 71.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
		  }
      }
	  else if(strtolower($sec_name) == 'axis nifty healthcare etf')
      {
          $images[] = ["url" => $checkboxImageData, "x" => 20.94, "y" => 532.47,"size"=>7, "width" => 11, "height" =>10, "pages" => "0", "keepAspectRatio" => true];  
		  $textannotations[] = ["text" => "$tuif", "x" => 165.25, "y" => 532.47,"size"=>7, "width" => 57.57, "height" => 11.37, "pages" => "0", "type" => "text"];	  
		  
		  // SHOW TOTAL AMOUNT :: BUY CASH CASES
		  if ( $ticket->type == 1 && $ticket->payment_type == 1) {		  
			$textannotations[] = ["text"=> "$total_amt", "x"=> 278.17,  "y"=> 532.47,"size"=>7,"width"=> 71.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
		  }
      }
	  else if(strtolower($sec_name) == 'axis nifty india consumption etf')
      {
          $images[] = ["url" => $checkboxImageData, "x" => 20.94, "y" => 551.42,"size"=>7, "width" => 11, "height" =>10, "pages" => "0", "keepAspectRatio" => true];  
		  $textannotations[] = ["text" => "$tuif", "x" => 165.25, "y" => 551.42,"size"=>7, "width" => 57.57, "height" => 11.37, "pages" => "0", "type" => "text"];	

		  // SHOW TOTAL AMOUNT :: BUY CASH CASES
		  if ( $ticket->type == 1 && $ticket->payment_type == 1) {		  		  
			$textannotations[] = ["text"=> "$total_amt", "x"=> 278.17,  "y"=> 551.42,"size"=>7,"width"=> 71.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
		  }
      }
	  else if(strtolower($sec_name) == 'axis silver etf')
      {
          $images[] = ["url" => $checkboxImageData, "x" => 20.94, "y" => 571.19,"size"=>7, "width" => 11, "height" =>10, "pages" => "0", "keepAspectRatio" => true];  
		  $textannotations[] = ["text" => "$tuif", "x" => 165.25, "y" => 571.19,"size"=>7, "width" => 57.57, "height" => 11.37, "pages" => "0", "type" => "text"];	  
          
		  // SHOW TOTAL AMOUNT :: BUY CASH CASES
		  if ( $ticket->type == 1 && $ticket->payment_type == 1) {
			$textannotations[] = ["text"=> "$total_amt", "x"=> 278.17,  "y"=> 571.19,"size"=>7,"width"=> 71.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
		  }
      }
	  else if(strtolower($sec_name) == 'axis s&p bse sensex etf')
      {
          $images[] = ["url" => $checkboxImageData, "x" => 20.94, "y" => 590.14,"size"=>7, "width" => 11, "height" =>10, "pages" => "0", "keepAspectRatio" => true];  
		  $textannotations[] = ["text" => "$tuif", "x" => 165.25, "y" => 590.14,"size"=>7, "width" => 57.57, "height" => 11.37, "pages" => "0", "type" => "text"];	  
		  
		  // SHOW TOTAL AMOUNT :: BUY CASH CASES
		  if ( $ticket->type == 1 && $ticket->payment_type == 1) {			
			$textannotations[] = ["text"=> "$total_amt", "x"=> 278.17,  "y"=> 590.14,"size"=>7,"width"=> 71.21, "height"=> 11.94, "pages"=> "0", "type" => "text"];
		  }
      }
	  
	  // call API 
	  Log::info("About to call PDF API for AXIS AMC");
	  $urlToken = "filetoken://81e457ba1f9ad8bfc357b4fb3cba61584b79d5a18bbb3f1312";
	  self::callAPIandSaveFile($urlToken, $images, $textannotations, $ticket->id);
	}
	catch (\Exception $e) 
	{
		Log::info("Exception in AXIS PDF generation");
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
					  "url" => $checkboxImageData, "x" => 135.85, "y" => 292.15, "width" => 17, "height" => 14, "pages" => "0", "keepAspectRatio" => true
				  ];
			}

			if ($payment_type == 2) { // BASKET
			  $images[] =  [
					"url" => $checkboxImageData, "x" => 190.19, "y" =>  292.15, "width" => 17,   "height" => 14, "pages" =>  "0", "keepAspectRatio" => true
				];
			}

		  }

		  // SELL CASES
		  if ($ticket->type == 2) {
			if ($payment_type == 1) {  // CASH
			   $images[] = [
				"url" => $checkboxImageData, "x" => 411.67, "y" => 292.15,  "width" => 15, "height" => 14, "pages" => "0", "keepAspectRatio" => true
			  ];
			}

			if ($payment_type == 2) { // BASKET
			  $images[] = [
			   "url" => $checkboxImageData, "x" => 466.01, "y" => 292.15, "width" => 15, "height" => 14,  "pages" => "0",  "keepAspectRatio" => true
			 ];
			}
		  }

		  if(strtolower($sec_name) == 'aditya birla sun life nifty bank etf')
		  {
			  $images[] = ["url" => $checkboxImageData, "x" => 36.23, "y" => 382.68,"size"=>7, "width" => 11, "height" =>10, "pages" => "0", "keepAspectRatio" => true];
			  $textannotations[] = ["text" => "$ticket_basket", "x" => 285.7, "y" => 381.03,"size"=>7, "width" => 55.99, "height" => 13.5, "pages" => "0", "type" => "text"];
			  $textannotations[] = ["text"=> "$total_units", "x"=> 344.16,  "y"=> 381.03,"size"=>7,"width"=> 73.28, "height"=> 13.5, "pages"=> "0", "type" => "text"];
			  $textannotations[] = ["text"=> "$total_units_in_words", "x"=> 419.9, "y"=> 381.03,"size"=>7,"width"=> 170.43, "height"=> 13.5, "pages"=> "0", "type" => "text"];
		  }

		  else if(strtolower($sec_name) == 'aditya birla sun life gold etf')
		  {
			  $images[] = ["url" => $checkboxImageData, "x" => 36.23, "y" => 395.84,"size"=>7, "width" => 11, "height" => 10, "pages" => "0", "keepAspectRatio" => true];
			  $textannotations[] = ["text" => "$ticket_basket", "x" => 285.7, "y" => 395.85,"size"=>7, "width" => 55.99, "height" => 11.37, "pages" => "0", "type" => "text"];
			  $textannotations[] = ["text"=> "$total_units", "x"=> 344.16,  "y"=> 395.85,"size"=>7, "width"=> 73.28, "height"=> 11.94, "pages"=> "0", "type" => "text"];
			  $textannotations[] = ["text"=> "$total_units_in_words", "x"=> 419.9, "y"=> 395.85,"size"=>7, "width"=> 170.43, "height"=> 13, "pages"=> "0", "type" => "text"];
		  }
		  else if(strtolower($sec_name) == 'aditya birla sun life nifty 50 etf')
		  {
			  $images[] = ["url" => $checkboxImageData, "x" => 36.23, "y" => 409.83,"size"=>7, "width" => 11, "height" => 10, "pages" => "0", "keepAspectRatio" => true];
			  $textannotations[] = ["text" => "$ticket_basket", "x" => 285.7, "y" => 409.02,"size"=>7, "width" => 55.99, "height" => 11.37, "pages" => "0", "type" => "text"];
			  $textannotations[] = ["text"=> "$total_units", "x"=> 344.16,  "y"=> 409.02,"size"=>7, "width"=> 73.28, "height"=> 11.94, "pages"=> "0", "type" => "text"];
			  $textannotations[] = ["text"=> "$total_units_in_words", "x"=> 419.9, "y"=> 409.02,"size"=>7,"width"=> 170.43, "height"=> 13, "pages"=> "0", "type" => "text"];
		  }
		  else if(strtolower($sec_name) == 'aditya birla sun life silver etf')
		  {
			  $images[] = ["url" => $checkboxImageData, "x" => 36.23, "y" => 423.82,"size"=>7, "width" => 11, "height" => 10, "pages" => "0", "keepAspectRatio" => true];
			  $textannotations[] = ["text" => "$ticket_basket", "x" => 285.7, "y" => 423.83,"size"=>7, "width" => 55.99, "height" => 11.37, "pages" => "0", "type" => "text"];
			  $textannotations[] = ["text"=> "$total_units", "x"=> 344.16,  "y"=> 423.83,"size"=>7, "width"=> 73.28, "height"=> 11.94, "pages"=> "0", "type" => "text"];
			  $textannotations[] = ["text"=> "$total_units_in_words", "x"=> 419.9, "y"=> 423.83,"size"=>7, "width"=> 170.43, "height"=> 13, "pages"=> "0", "type" => "text"];
		  }
		  else if(strtolower($sec_name) == 'aditya birla sun life nifty next 50 etf')
		  {
			  $images[] = ["url" => $checkboxImageData, "x" => 36.23, "y" => 440.28,"size"=>7, "width" => 11, "height" => 10, "pages" => "0", "keepAspectRatio" => true];
			  $textannotations[] = ["text" => "$ticket_basket", "x" => 285.7, "y" => 437.82,"size"=>7, "width" => 55.99, "height" => 11.37, "pages" => "0", "type" => "text"];
			  $textannotations[] = ["text"=> "$total_units", "x"=> 344.16, "y"=> 437.82,"size"=>7,"width"=> 73.28, "height"=> 11.94, "pages"=> "0", "type" => "text"];
			  $textannotations[] = ["text"=> "$total_units_in_words", "x"=> 419.9, "y"=> 437.82,"size"=>7,"width"=> 170.43, "height"=> 13, "pages"=> "0", "type" => "text"];
		  }
		  else if(strtolower($sec_name) == 'aditya birla sun life s & p bse sensex etf')
		  {
			  $images[] = ["url" => $checkboxImageData, "x" => 36.23, "y" => 454.27, "size"=>7, "width" => 11, "height" => 10, "pages" => "0", "keepAspectRatio" => true];
			  $textannotations[] = ["text" => "$ticket_basket", "x" => 285.7, "y" => 451.81, "size"=>7, "width" => 55.99, "height" => 11.37, "pages" => "0", "type" => "text"];
			  $textannotations[] = ["text"=> "$total_units", "x"=> 344.16, "y"=> 451.81, "size"=>7,"width"=> 73.28, "height"=> 11.94, "pages"=> "0", "type" => "text"];
			  $textannotations[] = ["text"=> "$total_units_in_words", "x"=> 419.9, "y"=> 451.81, "size"=>7,"width"=> 170.43, "height"=> 13, "pages"=> "0", "type" => "text"];
		  }
		  else if(strtolower($sec_name) == 'aditya birla sun life nifty healthcare etf')
		  {
			  $images[] = ["url" => $checkboxImageData, "x" => 36.23, "y" => 467.44,"size"=>7, "width" => 11, "height" => 10, "pages" => "0", "keepAspectRatio" => true];
			  $textannotations[] = ["text" => "$ticket_basket", "x" => 285.7, "y" => 466.62,"size"=>7, "width" => 55.99, "height" => 11.37, "pages" => "0", "type" => "text"];
			  $textannotations[] = ["text"=> "$total_units", "x"=> 344.16, "y"=> 466.62,"size"=>7,"width"=> 73.28, "height"=> 11.94, "pages"=> "0", "type" => "text"];
			  $textannotations[] = ["text"=> "$total_units_in_words", "x"=> 419.9, "y"=> 466.62,"size"=>7,"width"=> 170.43, "height"=> 13, "pages"=> "0", "type" => "text"];
		  }
		  else if(strtolower($sec_name) == 'aditya birla sun life nifty it etf')
		  {
			  $images[] = ["url" => $checkboxImageData, "x" => 36.23, "y" => 481.43,"size"=>7, "width" => 11, "height" => 10, "pages" => "0", "keepAspectRatio" => true];
			  $textannotations[] = ["text" => "$ticket_basket", "x" => 285.7, "y" => 480.61,"size"=>7, "width" => 55.99, "height" => 11.37, "pages" => "0", "type" => "text"];
			  $textannotations[] = ["text"=> "$total_units", "x"=> 344.16, "y"=> 480.61,"size"=>7,"width"=> 73.28, "height"=> 11.94, "pages"=> "0", "type" => "text"];
			  $textannotations[] = ["text"=> "$total_units_in_words", "x"=> 419.9, "y"=> 490,"size"=>7,"width"=> 170.43, "height"=> 13, "pages"=> "0", "type" => "text"];
		  }
		  else if(strtolower($sec_name) == 'aditya birla sun life nifty 200 quality 30 etf')
		  {
			  $images[] = ["url" => $checkboxImageData, "x" => 36.23, "y" => 496.25,"size"=>7, "width" => 11, "height" => 10, "pages" => "0", "keepAspectRatio" => true];
			  $textannotations[] = ["text" => "$ticket_basket", "x" => 285.7, "y" => 494.6,"size"=>7, "width" => 55.99, "height" => 11.37, "pages" => "0", "type" => "text"];
			  $textannotations[] = ["text"=> "$total_units", "x"=> 344.16, "y"=> 494.6,"size"=>7,"width"=> 73.28, "height"=> 11.94, "pages"=> "0", "type" => "text"];
			  $textannotations[] = ["text"=> "$total_units_in_words", "x"=> 419.9, "y"=> 494.6,"size"=>7,"width"=> 170.43, "height"=> 13, "pages"=> "0", "type" => "text"];
		  }
		  else if(strtolower($sec_name) == 'aditya birla sun life nifty 200 momentum 30 etf')
		  {
			  $images[] = ["url" => $checkboxImageData, "x" => 36.23, "y" => 510.24,"size"=>7, "width" => 11, "height" => 10, "pages" => "0", "keepAspectRatio" => true];
			  $textannotations[] = ["text" => "$ticket_basket", "x" => 285.7, "y" => 508.59,"size"=>7, "width" => 55.99, "height" => 11.37, "pages" => "0", "type" => "text"];
			  $textannotations[] = ["text"=> "$total_units", "x"=> 344.16, "y"=> 508.59,"size"=>7,"width"=> 73.28, "height"=> 11.94, "pages"=> "0", "type" => "text"];
			  $textannotations[] = ["text"=> "$total_units_in_words", "x"=> 419.9, "y"=> 508.59,"size"=>7,"width"=> 170.43, "height"=> 13, "pages"=> "0", "type" => "text"];
		  }
		  else if(strtolower($sec_name) == 'aditya birla sun life nifty pse etf')
		  {
			  $images[] = ["url" => $checkboxImageData, "x" => 36.23, "y" => 523.41,"size"=>7, "width" => 11, "height" => 10, "pages" => "0", "keepAspectRatio" => true];
			  $textannotations[] = ["text" => "$ticket_basket", "x" => 285.7, "y" => 522.58,"size"=>7, "width" => 55.99, "height" => 11.37, "pages" => "0", "type" => "text"];
			  $textannotations[] = ["text"=> "$total_units", "x"=> 344.16, "y"=> 522.58,"size"=>7,"width"=> 73.28, "height"=> 11.94, "pages"=> "0", "type" => "text"];
			  $textannotations[] = ["text"=> "$total_units_in_words", "x"=> 419.9, "y"=> 522.58,"size"=>7,"width"=> 170.43, "height"=> 13, "pages"=> "0", "type" => "text"];
		  }
		  
		  // IF BUY CASE, add UTR NO, AMOUNT, AMount in WORDS
		  if( $ticket->type == 1 )
		  {
			$utr_no = $ticket->utr_no;
			if($utr_no !='')
			{
				$textannotations[] = ["text"=> "$utr_no", "x"=> 405.91, "y"=> 545.62,"size"=>7,"width"=> 184.43, "height"=> 18.11, "pages"=> "0", "type"=> "text"];
			}
			
			// SHOW TOTAL AMOUNT :: BUY CASH CASES
		    if ( $ticket->payment_type == 1) {
				$textannotations[] = ["text"=> "$total_amt", "x"=> 111.15, "y"=> 567.02,"size"=>7,"width"=> 122.68, "height"=> 11.52, "pages"=> "0", "type"=> "text"];
				$textannotations[] = ["text"=> "$word_text", "x"=> 308.75, "y"=> 563.73,  "width"=> 276.64,"size"=>7,"height"=> 13, "pages"=> "0", "type"=> "text"];
			}
		  }

		   // call API and SAVE the file
		  Log::info("Birla PDF -- ABout to call API");
		  $urlToken = "filetoken://1b8ee3dcb0e30848bbd1207d690b67a31d0e15d791f46f429f";
		  self::callAPIandSaveFile($urlToken, $images, $textannotations, $ticket->id);
	  }
	  catch (\Exception $e) 
	  {
		Log::info("Exception in Aditya BIRLA PDF generation");
		dd($e->getMessage());
	  }	  

    }

    
	
	// Handle KOTAK FORM
	private static function handleKOTAKForm($ticket) 
	{

		try 
		{	
		    // VARIABLES
			$marker_json = [];
			$textannotations = [];
			$images = [];
			$urlToken = "";
			
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
			
			$date = date("d-m-Y", time());
			
			$base = ["height"=> 11.94, "pages"=> "0", "type" => "text", "alignment" => "center", "size"=>10];
		 
		    $config = [];
			
			// SHOW TOTAL AMOUNT :: BUY-CASH CASES
		    if ($ticket->type == 1 && $ticket->payment_type == 1 ) 
			{
				// total amount 
				$config[] = array_merge($base, ["text"=> "$total_amt", "x"=>373.53, "y"=>349.18, "width"=> 150.21]);	
				$config[] = array_merge($base, ["text"=> "$total_amt", "x"=>373.53, "y"=>401.76, "width"=> 150.21]);
			}
			
			if(strtolower($sec_name) == 'kotak nifty bank etf')
			{
				// DATE 
			    $config[] = array_merge($base, ["text"=> "$date", "x"=>455.36, "y"=>113.83, "width"=> 80.21]);
				// UNITS 
				$config[] = array_merge($base, ["text"=> "$total_units_in_float", "x"=>375.2, "y"=>279.91, "width"=> 150.21]);
				
				$urlToken = "filetoken://ac84fe76ffc23d1f417f33982609e6ce050a3f314670ac1f8e";
			}
			else if(strtolower($sec_name) == 'kotak nifty india consumption etf')
			{
				// DATE 
			    $config[] = array_merge($base, ["text"=> "$date", "x"=>456.2, "y"=>116.34, "width"=> 80.21]);
				// UNITS 
				$config[] = array_merge($base, ["text"=> "$total_units_in_float", "x"=>346.82, "y"=>280.75, "width"=> 150.21]);
				
				$urlToken = "filetoken://e797c13ef8b6a618895597a3a12e581beceb6ceec856397774";
			}
			else if(strtolower($sec_name) == 'kotak nifty it etf')
			{
				// DATE 
			    $config[] = array_merge($base, ["text"=> "$date", "x"=>456.2, "y"=>116.34, "width"=> 80.21]);
				
				// UNITS 
				// $config[] = array_merge($base, ["text"=> "$total_units_in_float", "x"=>346.82, "y"=>280.75, "width"=> 150.21]);
				
				$urlToken = "filetoken://3fad23df98f0fffae59b782a722a1dfcf7af61723d00ee3ba5";
				
				// total amount 
				if ($ticket->type == 1) 
			    {
				  $config[] = array_merge($base, ["text"=> "$total_amt", "x"=>366.86, "y"=>280.83, "width"=> 180.21]);	
				}
			}
			else if(strtolower($sec_name) == 'kotak nifty midcap 50 etf')
			{
				$config = [];
				// DATE 
			    $config[] = array_merge($base, ["text"=> "$date", "x"=>456.2, "y"=>116.34, "width"=> 80.21]);
				// UNITS 
				$config[] = array_merge($base, ["text"=> "$total_units_in_float", "x"=>346.82, "y"=>280.75, "width"=> 150.21]);
				
				// SHOW total amount for BUY-CASH cases
				if ($ticket->type == 1 && $ticket->payment_type == 1) 
			    {
				  $config[] = array_merge($base, ["text"=> "$total_amt", "x"=>366.86, "y"=>353.35, "width"=> 150.21]);	
				  $config[] = array_merge($base, ["text"=> "$total_amt", "x"=>366.86, "y"=>416.78, "width"=> 150.21]);
				}
				$urlToken = "filetoken://799b8c0c867aa67874135a5744d4490fc69994f8f3f11c32cd";
			}
			else if(strtolower($sec_name) == 'kotak nifty mnc etf')
			{
				// DATE 
			    $config[] = array_merge($base, ["text"=> "$date", "x"=>456.2, "y"=>116.34, "width"=> 80.21]);
				// UNITS 
				$config[] = array_merge($base, ["text"=> "$total_units_in_float", "x"=>357.68, "y"=>280.75, "width"=> 150.21]);
				
				$urlToken = "filetoken://757f407d30f9c50269a135817fb74a0dba5b36d49f2c6b53c5";
			}
			else if(strtolower($sec_name) == 'kotak nifty 100 low volatility 30 etf')
			{
				// DATE 
				$config = [];
				
			    $config[] = array_merge($base, ["text"=> "$date", "x"=>456.2, "y"=>110.34, "width"=> 80.21]);
				// UNITS 
				$config[] = array_merge($base, ["text"=> "$total_units_in_float", "x"=>357.68, "y"=>280.75, "width"=> 150.21]);
				
				// SHOW total amount for BUY-CASH cases
				if ($ticket->type == 1 && $ticket->payment_type == 1) 
			    {
				  $config[] = array_merge($base, ["text"=> "$total_amt", "x"=>366.86, "y"=>358.35, "width"=> 150.21]);	
				  $config[] = array_merge($base, ["text"=> "$total_amt", "x"=>366.86, "y"=>420.78, "width"=> 150.21]);
				}
				
				$urlToken = "filetoken://d5f6df853deaf8f1745f60e8abc83c30bdbc44ae8151858a6d";
			}
			else if(strtolower($sec_name) == 'kotak nifty alpha 50 etf')
			{
				// DATE 
				$config = [];
				
			    $config[] = array_merge($base, ["text"=> "$date", "x"=>472.9, "y"=>109.66, "width"=> 80.21]);
				// UNITS 
				$config[] = array_merge($base, ["text"=> "$total_units_in_float", "x"=>364.36, "y"=>279.91, "width"=> 150.21]);
				
				// SHOW total amount for BUY-CASH cases
				if ($ticket->type == 1 && $ticket->payment_type == 1) 
			    {
				  $config[] = array_merge($base,["text"=> "$total_amt", "x"=>366.86, "y"=>352.52, "width"=> 150.21]);	
				  $config[] = array_merge($base,["text"=> "$total_amt", "x"=>366.86, "y"=>418.45, "width"=> 150.21]);
				}
				
				$urlToken = "filetoken://6f5337a5cec5644e81f7fac9dac1087951bb3481863d24ee7e";
			}
			else if(strtolower($sec_name) == 'kotak nifty 50 etf')
			{
				// DATE 
			    $config[] = array_merge($base, ["text"=> "$date", "x"=>456.19, "y"=>114.67, "width"=> 80.21]);
				// UNITS 
				$config[] = array_merge($base, ["text"=> "$total_units_in_float", "x"=>371.03, "y"=>280.75, "width"=> 150.21]);
				// FILE URL TOKEN
				$urlToken = "filetoken://6eef43fa3c68ef5471c2f5d0a87ad8246b5c1a3b08f0316b14";
			}
			else if(strtolower($sec_name) == 'kotak nifty 50 value 20 etf')
			{
				// DATE 
			    $config[] = array_merge($base, ["text"=> "$date", "x"=>456.19, "y"=>114.67, "width"=> 80.21]);
				// UNITS 
				$config[] = array_merge($base, ["text"=> "$total_units_in_float", "x"=>371.03, "y"=>280.75, "width"=> 150.21]);
				// FILE URL TOKEN
				$urlToken = "filetoken://ef3a0b963e58953291f1f6378e6f1f9e5e7205ffb6498bbaca";
			}
			else if(strtolower($sec_name) == 'kotak nifty psu bank etf')
			{
				// DATE 
				$config = [];
				$day    = date("d", time());
				$month  = date("m", time());
				$year   = date("Y", time());
			    $config[] = array_merge($base, ["text"=> "$day", "x"=>455.35, "y"=>116.34, "width"=> 20.21]);
				$config[] = array_merge($base, ["text"=> "$month", "x"=>478.73, "y"=>116.34, "width"=> 20.21]);
				$config[] = array_merge($base, ["text"=> "$year", "x"=>501.73, "y"=>116.34, "width"=> 40.21]);
				
				// UNITS 
				$config[] = array_merge($base, ["text"=> "$total_units_in_float", "x"=>371.03, "y"=>286.09, "width"=> 150.21]);
				
				// SHOW total amount for BUY-CASH cases
				if ($ticket->type == 1 && $ticket->payment_type == 1) 
			    {
					// total amount 
					$config[] = array_merge($base, ["text"=> "$total_amt", "x"=>373.53, "y"=>354.19, "width"=> 150.21]);	
					$config[] = array_merge($base, ["text"=> "$total_amt", "x"=>373.53, "y"=>404.26, "width"=> 150.21]);
				}
				// FILE URL TOKEN
				$urlToken = "filetoken://a319f60d76c3e85dc2a9e3d421b62d8738b8b332d904138141";
			}
			else if(strtolower($sec_name) == 'kotak s&p bse sensex etf')
			{
				// DATE 
			    $config[] = array_merge($base, ["text"=> "$date", "x"=>456.19, "y"=>114.67, "width"=> 80.21]);
				// UNITS 
				$config[] = array_merge($base, ["text"=> "$total_units_in_float", "x"=>371.03, "y"=>280.75, "width"=> 150.21]);
				// FILE URL TOKEN
				$urlToken = "filetoken://75fad0e01a5d84e1e4b19fdd228c651bfd2d73a53c3fda8092";
			}
			
			$textannotations = $config;

			// call API 
			Log::info("About to call PDF API");
			self::callAPIandSaveFile($urlToken, $images, $textannotations, $ticket->id);
		}
		catch (\Exception $e) 
		{
			Log::info("Exception in KOTAK PDF generation");
			dd($e->getMessage());
		}
	  
	}
	
	// Handle TATA FORM
	private static function handleTATAForm($ticket) 
	{

		try 
		{	
		    // VARIABLES
			$marker_json = [];
			$textannotations = [];
			$images = [];
			$urlToken = "";
			
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
			
			$date = date("d-m-Y", time());
			
			$base = ["height"=> 11.94, "pages"=> "0", "type" => "text", "alignment" => "center", "size"=>7];
		 
		    $config = [];
			$imageArr = [ "url" => $checkboxImageData, "width" => 17, "height" => 14, "pages" => "0", "keepAspectRatio" => true ];
			// BUY CASES
			if ($ticket->type == 1) {
				if ($payment_type == 1) {  // CASH
					$images[] = array_merge($imageArr, ["x" => 135.49, "y" => 420.78]);
				}
				if ($payment_type == 2) { // BASKET
				  $images[] = array_merge($imageArr, ["x" => 216.3, "y" =>  420.78]);
				}
			}
			// SELL CASES
			if ($ticket->type == 2) {
				if ($payment_type == 1) {  // CASH
					$images[] = array_merge($imageArr, ["x" => 413.28, "y" => 420.78]);
				}
				if ($payment_type == 2) { // BASKET
				  $images[] = array_merge($imageArr, ["x" => 494.81, "y" =>  420.78]);
				}
			}

		    // Number of BAsket 
			$config[] = array_merge($base, ["text"=> "$ticket_basket", "x"=>235.06, "y"=>475.56, "width"=> 80.21]);	
			// Total Number of Units
			$config[] = array_merge($base, ["text"=> "$total_units", "x"=>297.11, "y"=>474.84, "width"=> 110.21]);		
			// Total Number of Units in WORDS
			$config[] = array_merge($base, ["text"=> "$total_units_in_words", "x"=>384.42, "y"=>476.28, "width"=> 175.34, "height"=>18.74]);		
			
			if ($ticket->type == 1) {
				// UTR NUMBER 
				$fs = strlen($utr_no) > 30 ? 5 : 7;
				$config[] = array_merge($base, ["text"=> "$utr_no", "x"=>441.59, "y"=>507.28, "width"=>150.21, "size"=>$fs, "height"=>14.42]);	
				
				// SHOW total amount for BUY-CASH cases
				if ($ticket->type == 1 && $ticket->payment_type == 1) 
			    {
					$config[] = array_merge($base, ["text"=> "$total_amt", "x"=>120.34, "y"=>526.01, "width"=> 99.21]);		
					$config[] = array_merge($base, ["text"=> "$word_text", "x"=>295.68, "y"=>526.01, "width"=> 290.78]);		
				}
			}
			
			if(strtolower($sec_name) == 'tata nifty india digital etf')
			{
				$urlToken = "filetoken://e49c86f792d673d36940d563d0fa2cbab7ca15ba4e423ff491";
			}
			else if(strtolower($sec_name) == 'tata nifty etf')
			{
				$urlToken = "filetoken://83610c24a1c810232abedd085a243496c99f14d9dc164b2060";
			}
			else if(strtolower($sec_name) == 'tata nifty private bank etf')
			{
				$urlToken = "filetoken://e53f455c262a6bd2b46b78e257ec319dc4b234ce2f4ad942b0";
			}
			
			$textannotations = $config;

			// call API 
			Log::info("ABOUT to call PDF API");
			self::callAPIandSaveFile($urlToken, $images, $textannotations, $ticket->id);
		}
		catch (\Exception $e) 
		{
			Log::info("Exception in TATA PDF generation");
			dd($e->getMessage());
		}
	  
	}
	
	// Handle INVESCO FORM
	private static function handleINVESCOForm($ticket) 
	{

		try 
		{	
		    // VARIABLES
			$marker_json = [];
			$textannotations = [];
			$images = [];
			$urlToken = "";
			
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
			
			$date = date("d-m-Y", time());
			
			$base = ["height"=> 11.94, "pages"=> "0", "type" => "text", "alignment" => "center", "size"=>8];
		 
		    $config = [];
			$imageArr = [ "url" => $checkboxImageData, "height" => 14, "pages" => "0", "keepAspectRatio" => true ];
			
			// Date 1
			$textannotations[] = array_merge($base, ["text"=> "$date", "x"=>100.42, "y"=>112.59, "width"=>84.59, "size"=>9 ]);	
			
			// Date 2
			$textannotations[] = array_merge($base, ["text"=> "$date", "x"=>316.84, "y"=>209.54, "width"=>84.59, "size"=>9 ]);	
				
			// BUY CASES
			if ($ticket->type == 1) {
				
				// Scheme Name 
				$textannotations[] = array_merge($base, ["text"=> "$sec_name", "x"=>70.66, "y"=>338.8, "width"=>201.49, "height"=>27.68 ]);
				
				// Basket Size 
				$textannotations[] = array_merge($base, ["text"=> "$basket_size", "x"=>280.7, "y"=>338.8, "width"=>74.6, "height"=>17.69 ]);

				// Basket Number 
				$textannotations[] = array_merge($base, ["text"=> "$ticket_basket", "x"=>365.29, "y"=>338.8, "width"=>77.67, "height"=>17.69 ]); 
				
				// Total No. Units 
				$textannotations[] = array_merge($base, ["text"=> "$total_units_in_float", "x"=>451.42, "y"=>338.8, "width"=>100, "height"=>17.69 ]); 
				
				// FILE TOKEN
				$urlToken = "filetoken://0b80ce1398d30f343fb3cfb558458d28ebc6c3b3eb0525815e";
			}
			
			// SELL CASES
			if ($ticket->type == 2) {
				
				// Scheme Name 
				$textannotations[] = array_merge($base, ["text"=> "$sec_name", "x"=>69.98, "y"=>327.39, "width"=>185.34, "height"=>27.68 ]);
				
				// Basket Size 
				$textannotations[] = array_merge($base, ["text"=> "$basket_size", "x"=>280.7, "y"=>327.39, "width"=>83.82, "height"=>17.69 ]);

				// Basket Number 
				$textannotations[] = array_merge($base, ["text"=> "$ticket_basket", "x"=>365.29, "y"=>327.39, "width"=>86.9, "height"=>17.69 ]); 
				
				// Total No. Units 
				$textannotations[] = array_merge($base, ["text"=> "$total_units_in_float", "x"=>451.42, "y"=>327.39, "width"=>100, "height"=>17.69 ]); 
				
				// FILE TOKEN
				$urlToken = "filetoken://7629bc88885108b6ad0d4e9a2454d00144ac553bb1797cadb2";
			}
		

			// call API 
			Log::info("ABOUT to call PDF API");
			self::callAPIandSaveFile($urlToken, $images, $textannotations, $ticket->id);
		}
		catch (\Exception $e) 
		{
			Log::info("Exception in TATA PDF generation");
			dd($e->getMessage());
		}
	  
	}
	
	// Handle NIPPON FORM
	private static function handleNIPPONForm($ticket) 
	{

		try 
		{	
		    // VARIABLES
			$marker_json = [];
			$textannotations = [];
			$images = [];
			$urlToken = "";
			
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
			
			$date = date("d-m-Y", time());
			
			$base = ["height"=> 11.94, "pages"=> "0", "type" => "text", "alignment" => "center", "size"=>7];
		 
		    $config = ["size"=>7,"height"=> 10, "pages"=> "0", "type"=> "text"];
			$imageArr = [ "url" => $checkboxImageData, "width" => 10, "height" => 10.25, "pages" => "0", "keepAspectRatio" => true ];
			
			// BUY CASES
			if ($ticket->type == 1) {
				if ($payment_type == 1) {  // CASH
					$images[] = array_merge($imageArr, ["x" => 14.74, "y" => 374.81]);
				}
				if ($payment_type == 2) { // BASKET
				  $images[] = array_merge($imageArr, ["x" => 58.31, "y" =>  374.81]);
				}
			}
			// SELL CASES
			if ($ticket->type == 2) {
				if ($payment_type == 1) {  // CASH
					$images[] = array_merge($imageArr, ["x" => 174.93, "y" => 372.89]);
				}
				if ($payment_type == 2) { // BASKET
				  $images[] = array_merge($imageArr, ["x" => 214.02, "y" =>  373.53]);
				}
			}

			if(strtolower($sec_name) == 'nippon india etf nifty 50 bees') {
				$images[] = array_merge($imageArr, ["x" => 203.76, "y" => 469.64]);
				$textannotations[] = array_merge($config, ["text"=>"$ticket_basket", "x"=>342.81, "y"=> 469, "width"=> 80]);
				$textannotations[] = array_merge($config, ["text"=>"$total_units_in_float", "x"=>427.39, "y"=>469, "width"=>61.51]);
				
				// SHOW total amount for BUY-CASH cases
				if ($ticket->type == 1 && $ticket->payment_type == 1) 
			    {
					$textannotations[] = array_merge($config, ["text"=>"$total_amt", "x"=>492.75, "y"=>469, "width"=> 85.86]);
				}
			}
			else if(strtolower($sec_name) == 'nippon india etf nifty next 50 junior bees'){
				$images[] = array_merge($imageArr, ["x" => 203.76, "y" => 485.64]);
				$textannotations[] = array_merge($config, ["text"=>"$ticket_basket", "x"=>342.81, "y"=> 483, "width"=> 80]);
				$textannotations[] = array_merge($config, ["text"=>"$total_units_in_float", "x"=>427.39, "y"=>483, "width"=>61.51]);
				
				// SHOW total amount for BUY-CASH cases
				if ($ticket->type == 1 && $ticket->payment_type == 1) 
			    {
					$textannotations[] = array_merge($config, ["text"=>"$total_amt", "x"=>492.75, "y"=>483, "width"=> 85.86]);
				}
			}
			else if(strtolower($sec_name) == 'nippon india etf nifty midcap 150'){
				$images[] = array_merge($imageArr, ["x" => 203.76, "y" => 498.47]);
				$textannotations[] = array_merge($config, ["text"=>"$ticket_basket", "x"=>342.81, "y"=> 495, "width"=> 80]);
				$textannotations[] = array_merge($config, ["text"=>"$total_units_in_float", "x"=>427.39, "y"=>495, "width"=>61.51]);
				
				// SHOW total amount for BUY-CASH cases
				if ($ticket->type == 1 && $ticket->payment_type == 1) 
			    {
					$textannotations[] = array_merge($config, ["text"=>"$total_amt", "x"=>492.75, "y"=>495, "width"=> 85.86]);
				}
			}
			else if(strtolower($sec_name) == 'nippon india etf nifty 100'){
				$images[] = array_merge($imageArr, ["x" => 203.76, "y" => 512.57]);
				$textannotations[] = array_merge($config, ["text"=>"$ticket_basket", "x"=>342.81, "y"=> 510.64, "width"=> 80]);
				$textannotations[] = array_merge($config, ["text"=>"$total_units_in_float", "x"=>427.39, "y"=>510.64, "width"=>61.51]);
				
				// SHOW total amount for BUY-CASH cases
				if ($ticket->type == 1 && $ticket->payment_type == 1) 
			    {
					$textannotations[] = array_merge($config, ["text"=>"$total_amt", "x"=>492.75, "y"=>510.64, "width"=> 85.86]);
				}
			}
			else if(strtolower($sec_name) == 'nippon india etf nifty 50 shariah bees'){
				$images[] = array_merge($imageArr, ["x" => 203.76, "y" => 526.02]);
				$textannotations[] = array_merge($config, ["text"=>"$ticket_basket", "x"=>342.81, "y"=> 524.09, "width"=> 80]);
				$textannotations[] = array_merge($config, ["text"=>"$total_units_in_float", "x"=>427.39, "y"=>524.09, "width"=>61.51]);
				
				// SHOW total amount for BUY-CASH cases
				if ($ticket->type == 1 && $ticket->payment_type == 1) 
			    {
					$textannotations[] = array_merge($config, ["text"=>"$total_amt", "x"=>492.75, "y"=>524.09, "width"=> 85.86]);
				}
			}
			else if(strtolower($sec_name) == 'nippon india etf nifty bank bees'){
				$images[] = array_merge($imageArr, ["x" => 203.76, "y" => 540.12]);
				$textannotations[] = array_merge($config, ["text"=>"$ticket_basket", "x"=>342.81, "y"=> 538.19, "width"=> 80]);
				$textannotations[] = array_merge($config, ["text"=>"$total_units_in_float", "x"=>427.39, "y"=>538.19, "width"=>61.51]);
				
				// SHOW total amount for BUY-CASH cases
				if ($ticket->type == 1 && $ticket->payment_type == 1) 
			    {
					$textannotations[] = array_merge($config, ["text"=>"$total_amt", "x"=>492.75, "y"=>538.19, "width"=> 85.86]);
				}
			}
			else if(strtolower($sec_name) == 'nippon india etf nifty psu bank bees'){
				$images[] = array_merge($imageArr, ["x" => 203.76, "y" => 553.57]);
				$textannotations[] = array_merge($config, ["text"=>"$ticket_basket", "x"=>342.81, "y"=> 552.92, "width"=> 80]);
				$textannotations[] = array_merge($config, ["text"=>"$total_units_in_float", "x"=>427.39, "y"=>552.92, "width"=>61.51]);
				
				// SHOW total amount for BUY-CASH cases
				if ($ticket->type == 1 && $ticket->payment_type == 1) 
			    {
					$textannotations[] = array_merge($config, ["text"=>"$total_amt", "x"=>492.75, "y"=>552.92, "width"=> 85.86]);
				}
			}
			else if(strtolower($sec_name) == 'nippon india etf nifty 50 value 20'){
				$images[] = array_merge($imageArr, ["x" => 203.76, "y" => 567.02]);
				$textannotations[] = array_merge($config, ["text"=>"$ticket_basket", "x"=>342.81, "y"=> 565.09, "width"=> 80]);
				$textannotations[] = array_merge($config, ["text"=>"$total_units_in_float", "x"=>427.39, "y"=>565.09, "width"=>61.51]);
				
				// SHOW total amount for BUY-CASH cases
				if ($ticket->type == 1 && $ticket->payment_type == 1) 
			    {
					$textannotations[] = array_merge($config, ["text"=>"$total_amt", "x"=>492.75, "y"=>565.09, "width"=> 85.86]);
				}
			}
			else if(strtolower($sec_name) == 'nippon india etf nifty infrastructure bees'){
				$images[] = array_merge($imageArr, ["x" => 203.76, "y" => 581.12]);
				$textannotations[] = array_merge($config, ["text"=>"$ticket_basket", "x"=>342.81, "y"=> 580.47, "width"=> 80]);
				$textannotations[] = array_merge($config, ["text"=>"$total_units_in_float", "x"=>427.39, "y"=>580.47, "width"=>61.51]);
				
				// SHOW total amount for BUY-CASH cases
				if ($ticket->type == 1 && $ticket->payment_type == 1) 
			    {
					$textannotations[] = array_merge($config, ["text"=>"$total_amt", "x"=>492.75, "y"=>580.47, "width"=> 85.86]);
				}
			}
			else if(strtolower($sec_name) == 'nippon india etf nifty india consumption'){
				$images[] = array_merge($imageArr, ["x" => 203.76, "y" => 595.21]);
				$textannotations[] = array_merge($config, ["text"=>"$ticket_basket", "x"=>342.81, "y"=>593.28, "width"=> 80]);
				$textannotations[] = array_merge($config, ["text"=>"$total_units_in_float", "x"=>427.39, "y"=>593.28, "width"=>61.51]);
				
				// SHOW total amount for BUY-CASH cases
				if ($ticket->type == 1 && $ticket->payment_type == 1) 
			    {
					$textannotations[] = array_merge($config, ["text"=>"$total_amt", "x"=>492.75, "y"=>593.28, "width"=> 85.86]);
				}
			}
			else if(strtolower($sec_name) == 'nippon india etf nifty dividend opportunities 50'){
				$images[] = array_merge($imageArr, ["x" => 203.76, "y" => 608.66]);
				$textannotations[] = array_merge($config, ["text"=>"$ticket_basket", "x"=>342.81, "y"=>606.73, "width"=> 80]);
				$textannotations[] = array_merge($config, ["text"=>"$total_units_in_float", "x"=>427.39, "y"=>606.73, "width"=>61.51]);
				
				// SHOW total amount for BUY-CASH cases
				if ($ticket->type == 1 && $ticket->payment_type == 1) 
			    {
					$textannotations[] = array_merge($config, ["text"=>"$total_amt", "x"=>492.75, "y"=>606.73, "width"=> 85.86]);
				}
			}
			else if(strtolower($sec_name) == 'nippon india etf nifty it'){
				$images[] = array_merge($imageArr, ["x" => 203.76, "y" => 622.76]);
				$textannotations[] = array_merge($config, ["text"=>"$ticket_basket", "x"=>342.81, "y"=>621.47, "width"=> 80]);
				$textannotations[] = array_merge($config, ["text"=>"$total_units_in_float", "x"=>427.39, "y"=>621.47, "width"=>61.51]);
				
				// SHOW total amount for BUY-CASH cases
				if ($ticket->type == 1 && $ticket->payment_type == 1) 
			    {
					$textannotations[] = array_merge($config, ["text"=>"$total_amt", "x"=>492.75, "y"=>621.47, "width"=> 85.86]);
				}
			}
			else if(strtolower($sec_name) == 'nippon india nifty pharma etf'){
				$images[] = array_merge($imageArr, ["x" => 203.76, "y" => 635.57]);
				$textannotations[] = array_merge($config, ["text"=>"$ticket_basket", "x"=>342.81, "y"=>634.92, "width"=> 80]);
				$textannotations[] = array_merge($config, ["text"=>"$total_units_in_float", "x"=>427.39, "y"=>634.92, "width"=>61.51]);
				
				// SHOW total amount for BUY-CASH cases
				if ($ticket->type == 1 && $ticket->payment_type == 1) 
			    {
					$textannotations[] = array_merge($config, ["text"=>"$total_amt", "x"=>492.75, "y"=>634.92, "width"=> 85.86]);
				}
			}
			else if(strtolower($sec_name) == 'nippon india nifty auto etf'){
				$images[] = array_merge($imageArr, ["x" => 203.76, "y" => 651.59]);
				$textannotations[] = array_merge($config, ["text"=>"$ticket_basket", "x"=>342.81, "y"=>649.66, "width"=> 80]);
				$textannotations[] = array_merge($config, ["text"=>"$total_units_in_float", "x"=>427.39, "y"=>649.66, "width"=>61.51]);
				
				// SHOW total amount for BUY-CASH cases
				if ($ticket->type == 1 && $ticket->payment_type == 1) 
			    {
					$textannotations[] = array_merge($config, ["text"=>"$total_amt", "x"=>492.75, "y"=>649.66, "width"=> 85.86]);
				}
			}
			else if(strtolower($sec_name) == 'cpse etf'){
				$images[] = array_merge($imageArr, ["x" => 203.76, "y" => 663.76]);
				$textannotations[] = array_merge($config, ["text"=>"$ticket_basket", "x"=>342.81, "y"=>663.11, "width"=> 80]);
				$textannotations[] = array_merge($config, ["text"=>"$total_units_in_float", "x"=>427.39, "y"=>663.11, "width"=>61.51]);
				
				// SHOW total amount for BUY-CASH cases
				if ($ticket->type == 1 && $ticket->payment_type == 1) 
			    {
					$textannotations[] = array_merge($config, ["text"=>"$total_amt", "x"=>492.75, "y"=>663.11, "width"=> 85.86]);
				}
			}
			else if(strtolower($sec_name) == 'nippon india etf s&p bse sensex next 50'){
				$images[] = array_merge($imageArr, ["x" => 203.76, "y" => 677.86]);
				$textannotations[] = array_merge($config, ["text"=>"$ticket_basket", "x"=>342.81, "y"=>677.21, "width"=> 80]);
				$textannotations[] = array_merge($config, ["text"=>"$total_units_in_float", "x"=>427.39, "y"=>677.21, "width"=>61.51]);
				
				// SHOW total amount for BUY-CASH cases
				if ($ticket->type == 1 && $ticket->payment_type == 1) 
			    {
					$textannotations[] = array_merge($config, ["text"=>"$total_amt", "x"=>492.75, "y"=>677.21, "width"=> 85.86]);
				}
			}
			else if(strtolower($sec_name) == 'nippon india etf s&p bse sensex'){
				$images[] = array_merge($imageArr, ["x" => 203.76, "y" => 691.31]);
				$textannotations[] = array_merge($config, ["text"=>"$ticket_basket", "x"=>342.81, "y"=>690.66, "width"=> 80]);
				$textannotations[] = array_merge($config, ["text"=>"$total_units_in_float", "x"=>427.39, "y"=>690.66, "width"=>61.51]);
				
				// SHOW total amount for BUY-CASH cases
				if ($ticket->type == 1 && $ticket->payment_type == 1) 
			    {
					$textannotations[] = array_merge($config, ["text"=>"$total_amt", "x"=>492.75, "y"=>690.66, "width"=> 85.86]);
				}
			}
			else if(strtolower($sec_name) == 'nippon india etf hang seng bees'){
				$images[] = array_merge($imageArr, ["x" => 203.76, "y" => 707.33]);
				$textannotations[] = array_merge($config, ["text"=>"$ticket_basket", "x"=>342.81, "y"=>704.76, "width"=> 80]);
				$textannotations[] = array_merge($config, ["text"=>"$total_units_in_float", "x"=>427.39, "y"=>704.76, "width"=>61.51]);
				
				// SHOW total amount for BUY-CASH cases
				if ($ticket->type == 1 && $ticket->payment_type == 1) 
			    {
					$textannotations[] = array_merge($config, ["text"=>"$total_amt", "x"=>492.75, "y"=>704.76, "width"=> 85.86]);
				}
			}
			else if(strtolower($sec_name) == 'nippon india etf nifty 8-13 yr g-sec long term gilt'){
				$images[] = array_merge($imageArr, ["x" => 203.76, "y" => 719.5]);
				$textannotations[] = array_merge($config, ["text"=>"$ticket_basket", "x"=>342.81, "y"=>718.21, "width"=> 80]);
				$textannotations[] = array_merge($config, ["text"=>"$total_units_in_float", "x"=>427.39, "y"=>718.21, "width"=>61.51]);
				
				// SHOW total amount for BUY-CASH cases
				if ($ticket->type == 1 && $ticket->payment_type == 1) 
			    {
					$textannotations[] = array_merge($config, ["text"=>"$total_amt", "x"=>492.75, "y"=>718.21, "width"=> 85.86]);
				}
			}
			else if(strtolower($sec_name) == 'nippon india etf nifty 5 yr benchmark g-sec'){
				$images[] = array_merge($imageArr, ["x" => 203.76, "y" => 734.24]);
				$textannotations[] = array_merge($config, ["text"=>"$ticket_basket", "x"=>342.81, "y"=>732.95, "width"=> 80]);
				$textannotations[] = array_merge($config, ["text"=>"$total_units_in_float", "x"=>427.39, "y"=>732.95, "width"=>61.51]);
				
				// SHOW total amount for BUY-CASH cases
				if ($ticket->type == 1 && $ticket->payment_type == 1) 
			    {
					$textannotations[] = array_merge($config, ["text"=>"$total_amt", "x"=>492.75, "y"=>732.95, "width"=> 85.86]);
				}
			}
			else if(strtolower($sec_name) == 'nippon india etf nifty cpse bond plus sdl sep 2024 50:50'){
				$images[] = array_merge($imageArr, ["x" => 203.76, "y" => 747.05]);
				$textannotations[] = array_merge($config, ["text"=>"$ticket_basket", "x"=>342.81, "y"=>745.76, "width"=> 80]);
				$textannotations[] = array_merge($config, ["text"=>"$total_units_in_float", "x"=>427.39, "y"=>745.76, "width"=>61.51]);
				
				// SHOW total amount for BUY-CASH cases
				if ($ticket->type == 1 && $ticket->payment_type == 1) 
			    {
					$textannotations[] = array_merge($config, ["text"=>"$total_amt", "x"=>492.75, "y"=>745.76, "width"=> 85.86]);
				}
			}
			else if(strtolower($sec_name) == 'nippon india etf nifty sdl apr 2026 top 20 equal weight'){
				$images[] = array_merge($imageArr, ["x" => 203.76, "y" => 761.15]);
				$textannotations[] = array_merge($config, ["text"=>"$ticket_basket", "x"=>342.81, "y"=>761.14, "width"=> 80]);
				$textannotations[] = array_merge($config, ["text"=>"$total_units_in_float", "x"=>427.39, "y"=>761.14, "width"=>61.51]);
				
				// SHOW total amount for BUY-CASH cases
				if ($ticket->type == 1 && $ticket->payment_type == 1) 
			    {
					$textannotations[] = array_merge($config, ["text"=>"$total_amt", "x"=>492.75, "y"=>761.14, "width"=> 85.86]);
				}
			}
			else if(strtolower($sec_name) == 'nippon india etf gold bees'){
				$images[] = array_merge($imageArr, ["x" => 203.76, "y" => 775.25]);
				$textannotations[] = array_merge($config, ["text"=>"$ticket_basket", "x"=>342.81, "y"=>773.95, "width"=> 80]);
				$textannotations[] = array_merge($config, ["text"=>"$total_units_in_float", "x"=>427.39, "y"=>773.95, "width"=>61.51]);
				
				// SHOW total amount for BUY-CASH cases
				if ($ticket->type == 1 && $ticket->payment_type == 1) 
			    {
					$textannotations[] = array_merge($config, ["text"=>"$total_amt", "x"=>492.75, "y"=>773.95, "width"=> 85.86]);
				}
			}
			else if(strtolower($sec_name) == 'nippon india silver etf'){
				$images[] = array_merge($imageArr, ["x" => 203.76, "y" => 788.9]);
				$textannotations[] = array_merge($config, ["text"=>"$ticket_basket", "x"=>342.81, "y"=>787.4, "width"=> 80]);
				$textannotations[] = array_merge($config, ["text"=>"$total_units_in_float", "x"=>427.39, "y"=>787.4, "width"=>61.51]);
				
				// SHOW total amount for BUY-CASH cases
				if ($ticket->type == 1 && $ticket->payment_type == 1) 
			    {
					$textannotations[] = array_merge($config, ["text"=>"$total_amt", "x"=>492.75, "y"=>787.4, "width"=> 85.86]);
				}
			}

		    // Scheme 
			$textannotations[] = array_merge($config, ["text"=> "$sec_name", "x"=>76.89, "y"=>80.73, "width"=>279.38, "pages" => "1"]);	
			
			if ($ticket->type == 1) {
				
				// SHOW total amount for BUY-CASH cases
				if ($ticket->payment_type == 1) 
			    {
					// Total AMOUNT
					$textannotations[] = array_merge($config, ["text"=> "$total_amt", "x"=>61.51, "y"=>90.98, "width"=> 279.38, "pages" => "1"]);		
				}
				
				// UTR
				$textannotations[] = array_merge($config, ["text"=> "$utr_no", "x"=>96.75, "y"=>125.3, "width"=> 279.38, "pages" => "1"]);		
			}			
		 
		    $urlToken = "filetoken://d114bfa9288de2f2f206e79c52b37cce3a7fbc60c203b7ff43";
			
			// call API 
			Log::info("About to call PDF API");
			self::callAPIandSaveFile($urlToken, $images, $textannotations, $ticket->id);
		}
		catch (\Exception $e) 
		{
			Log::info("Exception in NIPPON PDF generation");
			dd($e->getMessage());
		}
	  
	}
	
	// Handle ICICI FORM
	private static function handleICICIForm($ticket) 
	{

		try 
		{	
		    // VARIABLES
			$marker_json = [];
			$textannotations = [];
			$images = [];
			$urlToken = "";
			
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
			
			$date = date("d-m-Y", time());
			
			$base = ["height"=> 11.94, "pages"=> "0", "type" => "text", "alignment" => "center", "size"=>7];
		 
		    $config = ["size"=>7,"height"=> 10, "pages"=> "0", "type"=> "text"];
			
			$imageArr = [ "url" => $checkboxImageData, "width" => 10, "height" => 10.25, "pages" => "0", "keepAspectRatio" => true ];
			
			// BUY CASES
			if ($ticket->type == 1) {
				if ($payment_type == 1) {  // CASH
					$images[] = array_merge($imageArr, ["x" => 205.47, "y" => 477.01]);
				}
				if ($payment_type == 2) { // BASKET
				  $images[] = array_merge($imageArr, ["x" => 255.98, "y" =>  477.01]);
				}
			}
			// SELL CASES
			if ($ticket->type == 2) {
				if ($payment_type == 1) {  // CASH
					$images[] = array_merge($imageArr, ["x" => 385.86, "y" => 477.01]);
				}
				if ($payment_type == 2) { // BASKET
				  $images[] = array_merge($imageArr, ["x" => 439.25, "y" =>  477.01]);
				}
			}


			if(strtolower($sec_name) == 'icici prudential s&p bse sensex etf') {
				$images[] = array_merge($imageArr, ["x" => 56.97, "y" => 491.55, "width" => 7.05, "height" => 6.41]);
			}
			else if(strtolower($sec_name) == 'icici prudential nifty 50 etf') {
				$images[] = array_merge($imageArr, ["x" => 56.97, "y" => 500.2, "width" => 7.05, "height" => 6.41]);
			}
			else if(strtolower($sec_name) == 'icici prudential nifty 100 etf') {
				$images[] = array_merge($imageArr, ["x" => 56.97, "y" => 508.27, "width" => 7.05, "height" => 6.41]);
			}
			else if(strtolower($sec_name) == 'icici prudential nifty 50 value 20 etf') {
				$images[] = array_merge($imageArr, ["x" => 56.97, "y" => 515.77, "width" => 7.05, "height" => 6.41]);
			}
			else if(strtolower($sec_name) == 'bharat 22 etf') {
				$images[] = array_merge($imageArr, ["x" => 56.97, "y" => 522.69, "width" => 7.05, "height" => 6.41]);
			}
			else if(strtolower($sec_name) == 'icici prudential s&p bse 500 etf') {
				$images[] = array_merge($imageArr, ["x" => 56.97, "y" => 530.19, "width" => 7.05, "height" => 6.41]);
			}
			else if(strtolower($sec_name) == 'icici prudential india consumption etf') {
				$images[] = array_merge($imageArr, ["x" => 56.97, "y" => 537.69, "width" => 7.05, "height" => 6.41]);
			}
			else if(strtolower($sec_name) == 'icici prudential s&p bse liquid rate etf') {
				$images[] = array_merge($imageArr, ["x" => 56.97, "y" => 545.18, "width" => 7.05, "height" => 6.41]);
			}
			
			
			else if(strtolower($sec_name) == 'icici prudential nifty bank etf') {
				$images[] = array_merge($imageArr, ["x" => 170.03, "y" => 491.55, "width" => 7.05, "height" => 6.41]);
			}
			else if(strtolower($sec_name) == 'icici prudential nifty private banks etf') {
				$images[] = array_merge($imageArr, ["x" => 170.03, "y" => 500.2, "width" => 7.05, "height" => 6.41]);
			}
			else if(strtolower($sec_name) == 'icici prudential nifty midcap 150 etf') {
				$images[] = array_merge($imageArr, ["x" => 170.03, "y" => 508.27, "width" => 7.05, "height" => 6.41]);
			}
			else if(strtolower($sec_name) == 'icici prudential gold etf') {
				$images[] = array_merge($imageArr, ["x" => 170.03, "y" => 515.77, "width" => 7.05, "height" => 6.41]);
			}
			else if(strtolower($sec_name) == 'icici prudential nifty it etf') {
				$images[] = array_merge($imageArr, ["x" => 170.03, "y" => 522.69, "width" => 7.05, "height" => 6.41]);
			}
			else if(strtolower($sec_name) == 'icici prudential nifty healthcare etf') {
				$images[] = array_merge($imageArr, ["x" => 170.03, "y" => 530.19, "width" => 7.05, "height" => 6.41]);
			}
			else if(strtolower($sec_name) == 'icici prudential nifty fmcg etf') {
				$images[] = array_merge($imageArr, ["x" => 170.03, "y" => 537.69, "width" => 7.05, "height" => 6.41]);
			}
			else if(strtolower($sec_name) == 'icici prudential nifty next 50 etf') {
				$images[] = array_merge($imageArr, ["x" => 170.03, "y" => 545.18, "width" => 7.05, "height" => 6.41]);
			}
			
			
			else if(strtolower($sec_name) == 'icici prudential nifty auto etf') {
				$images[] = array_merge($imageArr, ["x" => 284.24, "y" => 491.55, "width" => 7.05, "height" => 6.41]);
			}
			else if(strtolower($sec_name) == 'icici prudential silver etf') {
				$images[] = array_merge($imageArr, ["x" => 284.24, "y" => 500.2, "width" => 7.05, "height" => 6.41]);
			}
			else if(strtolower($sec_name) == 'icici prudential nifty 100 low volatility 30 etf') {
				$images[] = array_merge($imageArr, ["x" => 284.24, "y" => 508.27, "width" => 7.05, "height" => 6.41]);
			}
			else if(strtolower($sec_name) == 'icici prudential s&p bse midcap select etf') {
				$images[] = array_merge($imageArr, ["x" => 284.24, "y" => 515.77, "width" => 7.05, "height" => 6.41]);
			}
			else if(strtolower($sec_name) == 'icici prudential nifty alpha low - volatility 30 etf') {
				$images[] = array_merge($imageArr, ["x" => 284.24, "y" => 522.69, "width" => 7.05, "height" => 6.41]);
			}
			else if(strtolower($sec_name) == 'icici prudential nifty 5 yr benchmark g-sec etf') {
				$images[] = array_merge($imageArr, ["x" => 284.24, "y" => 530.19, "width" => 7.05, "height" => 6.41]);
			}
			else if(strtolower($sec_name) == 'icici prudential nifty 200 momentum 30 etf') {
				$images[] = array_merge($imageArr, ["x" => 284.24, "y" => 537.69, "width" => 7.05, "height" => 6.41]);
			}
			else if(strtolower($sec_name) == 'icici prudential nifty infrastructure etf') {
				$images[] = array_merge($imageArr, ["x" => 284.24, "y" => 545.18, "width" => 7.05, "height" => 6.41]);
			}
			
			
			else if(strtolower($sec_name) == 'icici prudential nifty financial services ex-bank etf') {
				$images[] = array_merge($imageArr, ["x" => 423.83, "y" => 491.55, "width" => 7.05, "height" => 6.41]);
			}
			else if(strtolower($sec_name) == 'icici prudential nifty 10 yr benchmark g-sec etf') {
				$images[] = array_merge($imageArr, ["x" => 423.83, "y" => 500.2, "width" => 7.05, "height" => 6.41]);
			}
			else if(strtolower($sec_name) == 'icici prudential nifty commodities etf') {
				$images[] = array_merge($imageArr, ["x" => 423.83, "y" => 508.27, "width" => 7.05, "height" => 6.41]);
			}
			else if(strtolower($sec_name) == 'icici prudential nifty psu bank etf') {
				$images[] = array_merge($imageArr, ["x" => 423.83, "y" => 515.77, "width" => 7.05, "height" => 6.41]);
			}
			else if(strtolower($sec_name) == 'icici prudential nifty 200 quality 30 etf') {
				$images[] = array_merge($imageArr, ["x" => 423.83, "y" => 522.69, "width" => 7.05, "height" => 6.41]);
			}

								
			// Total Units in Figure 
			if ($ticket->type == 1) {	
				$textannotations[] = array_merge($config, ["text"=>"$total_units_in_float", "x"=>166.12, "y"=>581.25, "width"=> 100]);		
				// Total Units in WORDS 
				$textannotations[] = array_merge($config, ["text"=>"$total_units_in_words", "x"=>301.48, "y"=>581.25, "width"=> 288.41]);	
				
				// SHOW total amount for BUY-CASH cases
				if ($ticket->type == 1 && $ticket->payment_type == 1) 
			    {
					// Total Amount in Figure 
					$textannotations[] = array_merge($config, ["text"=>"$total_amt", "x"=>160.97, "y"=>591.25, "width"=> 110]);
					// Total Amount in WORDS 
					$textannotations[] = array_merge($config, ["text"=>"$word_text", "x"=>305.33, "y"=>592.78, "width"=> 285.33]);				
				}
				
				// UTR 
			    $textannotations[] = array_merge($config, ["text"=>"$utr_no", "x"=>260.18, "y"=>658.9, "width"=>122.29, "size"=>5]);				
			}
			
			if ($ticket->type == 2) {	
				$textannotations[] = array_merge($config, ["text"=>"$total_units_in_float", "x"=>162.29, "y"=>556.1, "width"=> 100]);		
				// Total Units in WORDS 
				$textannotations[] = array_merge($config, ["text"=>"$total_units_in_words", "x"=>297.64, "y"=>555.11, "width"=> 292.25]);		
			}
			
		 
		    $urlToken = "filetoken://9b035d81d734e6c9ad815cb99ed3b9d1ed7d59dd0e3ff440e5";
			
			// call API 
			Log::info("About to call PDF API");
			self::callAPIandSaveFile($urlToken, $images, $textannotations, $ticket->id);
		}
		catch (\Exception $e) 
		{
			Log::info("Exception in ICICI PDF generation");
			dd($e->getMessage());
		}
	  
	}
	
	// Handle BAJAJ FORM
	private static function handleBAJAJForm($ticket) 
	{

		try 
		{	
		    // VARIABLES
			$marker_json = [];
			$textannotations = [];
			$images = [];
			$urlToken = "";
			
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
			
			$date = date("d-m-Y", time());
			
		    $config = ["size"=>7,"height"=> 10, "pages"=> "0", "type"=> "text"];
			
			$imageArr = ["url" => $checkboxImageData, "width" => 10, "height" => 10.25, "pages" => "0", "keepAspectRatio" => true ];
			
			// BUY CASES
			if ($ticket->type == 1) {
				if ($payment_type == 1) {  // CASH
					$images[] = array_merge($imageArr, ["x" => 150.82, "y" => 402.61]);
				}
				if ($payment_type == 2) { // BASKET
				  $images[] = array_merge($imageArr, ["x" => 200.02, "y" =>  402.61]);
				}
			}
			// SELL CASES
			if ($ticket->type == 2) {
				if ($payment_type == 1) {  // CASH
					$images[] = array_merge($imageArr, ["x" => 422.94, "y" => 402.61]);
				}
				if ($payment_type == 2) { // BASKET
				  $images[] = array_merge($imageArr, ["x" => 483.08, "y" =>  402.61]);
				}
			}

			if(strtolower($sec_name) == 'bajaj finserv nifty 50 etf') {
				$textannotations[] = array_merge($config, ["text"=>"$total_units_in_float", "x"=>225.96, "y"=>432.78, "width"=>102.65]);
				$textannotations[] = array_merge($config, ["text"=>"$total_units_in_words", "x"=>331.05, "y"=>432.78, "width"=>245]);
				
				// SHOW total amount for BUY-CASH cases
				if ($ticket->type == 1 && $ticket->payment_type == 1) 
			    {
				   $textannotations[] = array_merge($config, ["text"=>"$total_amt", "x"=>225.96, "y"=>445.53, "width"=> 102.65]);
				   $textannotations[] = array_merge($config, ["text"=>"$word_text", "x"=>331.05, "y"=>445.53, "width"=> 245]);
				}
				$images[] = array_merge($imageArr, ["x" => 13.46, "y" =>  437.6]);
			}
			else if(strtolower($sec_name) == 'bajaj finserv nifty bank etf') {
				$textannotations[] = array_merge($config, ["text"=>"$total_units_in_float", "x"=>225.96, "y"=>458.28, "width"=>102.65]);
				$textannotations[] = array_merge($config, ["text"=>"$total_units_in_words", "x"=>331.05, "y"=>458.28, "width"=>245]);
				
				// SHOW total amount for BUY-CASH cases
				if ($ticket->type == 1 && $ticket->payment_type == 1) 
			    {
				   $textannotations[] = array_merge($config, ["text"=>"$total_amt", "x"=>225.96, "y"=>471.63, "width"=> 102.65]);
				   $textannotations[] = array_merge($config, ["text"=>"$word_text", "x"=>331.05, "y"=>471.63, "width"=> 245]);
				}
				$images[] = array_merge($imageArr, ["x" => 13.46, "y" =>  464.51]);
			}
			
			// UTR 	
			if ($ticket->type == 1) 
			{
			   $textannotations[] = array_merge($config, ["text"=>"$utr_no", "x"=>364.45, "y"=>567.74, "width"=> 214.66, "height"=>16.66, "size" => 7]);
			}
			
		    $urlToken = "filetoken://8bcaa2ec1226204cd84217223fa3554f97cebf9a600058cf08";
			
			// call API 
			self::callAPIandSaveFile($urlToken, $images, $textannotations, $ticket->id);
		}
		catch (\Exception $e) 
		{
			dd($e->getMessage());
		}
	  
	}
	
	// Handle HDFC FORM
	private static function handleHDFCForm($ticket) 
	{

		try 
		{	
		    // VARIABLES
			$marker_json = [];
			$textannotations = [];
			$images = [];
			$urlToken = "";
			
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
			
			$date = date("d-m-Y", time());
			
		    $config = ["size"=>7,"height"=> 10, "pages"=> "1", "type"=> "text"];
			
			$imageArr = ["url" => $checkboxImageData, "width" => 10, "height" => 10.25, "pages" => "1", "keepAspectRatio" => true ];
			
			// DEFAULT VALUES
			$X1=57.71; $X2=522.39; $X3=235.69; $X4=285.5; $X5=354.14; $X6=405.17;
			
			if(strtolower($sec_name) == 'hdfc nifty bank etf') { $Y1=380.58; }
			if(strtolower($sec_name) == 'hdfc nifty 50 etf') { $Y1=392.72; }
			if(strtolower($sec_name) == 'hdfc s&p bse sensex etf') { $Y1=404.86; }
			if(strtolower($sec_name) == 'hdfc nifty 100 etf') { $Y1=417.17;}
			if(strtolower($sec_name) == 'hdfc nifty next 50 etf') { $Y1=428.7;}
			if(strtolower($sec_name) == 'hdfc nifty100 quality 30 etf') { $Y1=439.51;}
			if(strtolower($sec_name) == 'hdfc nifty200 momentum 30 etf') { $Y1=452.48;}
			if(strtolower($sec_name) == 'hdfc nifty100 low volatility 30 etf') { $Y1=464.01;}
			if(strtolower($sec_name) == 'hdfc nifty growth sectors 15 etf') { $Y1=476.26;}
			if(strtolower($sec_name) == 'hdfc nifty 50 value 20 etf') { $Y1=488.51;}
			if(strtolower($sec_name) == 'hdfc nifty private bank etf') { $Y1=500.04;}
			if(strtolower($sec_name) == 'hdfc nifty it etf') { $Y1=510.85;}
			if(strtolower($sec_name) == 'hdfc s&p bse 500 etf') { $Y1=523.1;}
			if(strtolower($sec_name) == 'hdfc nifty midcap 150 etf') { $Y1=534.64; }
			if(strtolower($sec_name) == 'hdfc nifty smallcap 250 etf') { $Y1=547.89;}
			if(strtolower($sec_name) == 'hdfc nifty psu bank etf') { $Y1=559.86;}
			if(strtolower($sec_name) == 'hdfc gold exchange traded fund') { $Y1=571.39;}
			if(strtolower($sec_name) == 'hdfc silver etf') { $Y1=582.92;}

			$images[] = array_merge($imageArr, ["x"=> $X1, "y"=>$Y1]);
			$textannotations[] = array_merge($config, ["text"=>"$total_units_in_float", "x"=>$X2, "y"=>$Y1, "width"=> 67.42]);
			if ($ticket->type == 1) { // BUY CASES
				if ($payment_type == 1) {  // CASH
					$images[] = array_merge($imageArr, ["x" =>$X3, "y" =>$Y1]);
				}
				if ($payment_type == 2) { // BASKET
				  $images[] = array_merge($imageArr, ["x" => $X4, "y" =>$Y1]);
				}
			}
			if ($ticket->type == 2) { // SELL CASES
				if ($payment_type == 1) {  // CASH
					$images[] = array_merge($imageArr, ["x" =>$X5, "y" =>$Y1]);
				}
				if ($payment_type == 2) { // BASKET
				  $images[] = array_merge($imageArr, ["x" => $X6, "y" =>$Y1]);
				}
			}
			
			// Total UNits In Words (second page)
			$textannotations[] = array_merge($config, ["text"=>"$total_units_in_words", "x"=>124.83, "y"=>594.66, "width"=> 358.61, "pages"=>"1"]);
			
			// SHOW total amount for BUY-CASH cases
			if ($ticket->type == 1 && $ticket->payment_type == 1) 
			{
				// total AMount in figures
				$textannotations[] = array_merge($config, ["text"=>"$total_amt", "x"=>129.88, "y"=>606.91, "width"=> 88.75, "pages"=>"1"]);

				// total AMount in WORDS
				$textannotations[] = array_merge($config, ["text"=>"$word_text", "x"=>305.22, "y"=>606.91, "width"=> 270.58, "pages"=>"1"]);
			}				
			
			
			// Total UNits In Words (Third Page)
			$t = $total_units_in_float . " (" . $total_units_in_words . ")";
			$textannotations[] = array_merge($config, ["text"=>"$t", "x"=>158.74, "y"=>55.5, "width"=>356.44, "pages"=>"2"]);
			
			// IF BUY CASE
			if ($ticket->type == 1) 
			{
				// Total Amount
				$t = $total_amt . " (" . $word_text . ")";
				$textannotations[] = array_merge($config, ["text"=>"$t", "x"=>119.06, "y"=>69.92, "width"=>398.29, "pages"=>"2"]);
				
				// UTR NUMBER 
				$textannotations[] = array_merge($config, ["text"=>"$utr_no", "x"=>253.98, "y"=>209.03, "width"=>67.1, "height"=>18.74, "pages"=>"2"]);
		
				// SHOW total amount for BUY-CASH cases
				if ($ticket->payment_type == 1) 
			    {
					// Total AMount 
					$textannotations[] = array_merge($config, ["text"=>"$total_amt", "x"=>387.47, "y"=>207.59, "width"=>88.03, "height"=>18.74, "pages"=>"2"]);
				}
			}

			// Todays DATE 
			$date = date("d-m-Y", time());
			$textannotations[] = array_merge($config, ["text"=>"$date", "x"=>321.81, "y"=>209.03, "width"=>67.1, "height"=>18.74, "pages"=>"2"]);

		    $urlToken = "filetoken://36ecc0df2b8cc2568be589ccd70651de0ecd8597e91e39ec7b";
			
			// call API 
			self::callAPIandSaveFile($urlToken, $images, $textannotations, $ticket->id);
		}
		catch (\Exception $e) 
		{
			dd($e->getMessage());
		}
	  
	}
	
	// Handle SBI FORM
	private static function handleSBIForm($ticket) 
	{

		try 
		{	
		    // VARIABLES
			$marker_json = [];
			$textannotations = [];
			$images = [];
			$urlToken = "";
			
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
			
			$date = date("d-m-Y", time());
			
		    $config = ["size"=>7,"height"=> 10, "pages"=> "0", "type"=> "text"];
			
			$imageArr = ["url" => $checkboxImageData, "width" => 10, "height" => 10.25, "pages" => "0", "keepAspectRatio" => true ];
			
			// MARK -> BUY / SELL
			if ($ticket->type == 1) { // BUY CASES
				if ($payment_type == 1) {  // CASH
					$images[] = array_merge($imageArr, ["x" =>144.54, "y" =>379.56]);
				}
				if ($payment_type == 2) { // BASKET
				  $images[] = array_merge($imageArr, ["x" =>225.87, "y" =>379.56]);
				}
			}
			if ($ticket->type == 2) { // SELL CASES
				if ($payment_type == 1) {  // CASH
					$images[] = array_merge($imageArr, ["x" =>420.23, "y" =>379.56]);
				}
				if ($payment_type == 2) { // BASKET
				  $images[] = array_merge($imageArr, ["x" =>498.8, "y" =>379.56]);
				}
			}
			
			// BUILD DATA for PRODUCTS
			// DEFAULT VALUES
			$X1=186.27; $X2=257.33; $X3=315.84; $X4=393.11; 
			$Y1=0; // for Product's other Params 
			$Y2=0; // for Product Tick IMage 
			
			if(strtolower($sec_name) == 'sbi nifty 50 etf'){ $Y1 = 427.3; $Y2=426.75;}
			if(strtolower($sec_name) == 'sbi s&p bse sensex etf'){ $Y1 = 442.83; $Y2=440.59;}
			if(strtolower($sec_name) == 'sbi nifty next 50 etf'){ $Y1 = 455.67; $Y2=455.81; }
			if(strtolower($sec_name) == 'sbi s&p bse 100 etf'){ $Y1 = 470.89; $Y2=469.65;}
			if(strtolower($sec_name) == 'sbi nifty bank etf'){ $Y1 = 485.42; $Y2=484.18;}
			if(strtolower($sec_name) == 'sbi nifty 10 yr benchmark g-sec etf'){ $Y1 = 501.33; $Y2=500.09; }
			if(strtolower($sec_name) == 'sbi s&p bse sensex next 50 etf'){ $Y1 = 516.55; $Y2=516;}
			if(strtolower($sec_name) == 'sbi nifty it etf'){ $Y1 = 531.77; $Y2= 529.83; }
			if(strtolower($sec_name) == 'sbi nifty private bank etf'){ $Y1= 546.3; $Y2=546.43; }
			if(strtolower($sec_name) == 'sbi nifty 200 quality 30 etf'){ $Y1= 560.14; $Y2=560.27; }
			if(strtolower($sec_name) == 'sbi nifty consumption etf'){ $Y1 = 574.67; $Y2=576.87; }
			if(strtolower($sec_name) == 'sbi gold etf'){ $Y1 = 590.58; $Y2=591.4; }
			
			// Product Tick Mark
			$images[] = array_merge($imageArr, ["x"=> $X1, "y"=>$Y2]);
			
			// No. of Basket 
			$textannotations[] = array_merge($config, ["text"=>"$ticket_basket", "x"=>$X2, "y"=>$Y1, "width"=> 51.69]);
			
			// Total UNits In Figures
			$textannotations[] = array_merge($config, ["text"=>"$total_units_in_float", "x"=>$X3, "y"=>$Y1, "width"=>71.68]);
			
			// Total UNits In Words
			$textannotations[] = array_merge($config, ["text"=>"$total_units_in_words", "x"=>$X4, "y"=>$Y1, "width"=>228.14]);
			
			
			if ($ticket->type == 1) 
			{
				// SHOW total amount for BUY-CASH cases
				if($ticket->payment_type == 1)
				{
					// total AMount in figures
					$textannotations[] = array_merge($config, ["text"=>"$total_amt", "x"=>126.75, "y"=>640.98, "width"=> 102.7]);

					// total AMount in WORDS
					$textannotations[] = array_merge($config, ["text"=>"$word_text", "x"=>303.51, "y"=>640.67, "width"=> 314.29]);
				}
				
				// UTR NUMBER
				$len = strlen($utr_no);
				if($len > 115) $size = 5;
				else if	($len > 45) $size = 6;
				else $size = 7;
			    $textannotations[] = array_merge($config, ["text"=>"$utr_no", "x"=>445.04, "y"=>616.99, "width"=>179.2, "height"=>17.29, "size"=>$size]);
			}				
			
		    $urlToken = "filetoken://09bbf623bed38062e3ef7b404200327665c19f92280bfeffec";
			
			// call API 
			self::callAPIandSaveFile($urlToken, $images, $textannotations, $ticket->id);
		}
		catch (\Exception $e) 
		{
			dd($e->getMessage());
		}
	  
	}
	
	// Handle QUANTUM FORM
	private static function handleQUANTUMForm($ticket) 
	{

		try 
		{	
		    // VARIABLES
			$marker_json = [];
			$textannotations = [];
			$images = [];
			$urlToken = "";
			
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
			
			$date = date("d-m-Y", time());
			
		    $config = ["size"=>7,"height"=> 10, "pages"=> "0", "type"=> "text"];
			
			$imageArr = ["url" => $checkboxImageData, "width" => 10, "height" => 10.25, "pages" => "1", "keepAspectRatio" => true ];
			
			// Total UNITS 
			$str = "$total_units_in_float ($total_units_in_words)";
			$textannotations[] = array_merge($config, ["text"=>"$str", "x"=>175.8, "y"=>488.45, "width"=>338.7, "pages"=>"1"]);
			 
			// SHOW total amount for BUY-CASH cases 
			if($ticket->type == 1 && $ticket->payment_type == 1) 
			{
				$str = "$total_amt ($word_text)";
				$textannotations[] = array_merge($config, ["text"=>"$str", "x"=>132.76, "y"=>506, "width"=>380.98, "pages"=>"1"]);
			}
				
			// BUY CASES
			if ($ticket->type == 1) {
				
				// Subscription Tick 
				$images[] = array_merge($imageArr, ["x" => 99.29, "y" => 309.27, "pages"=>"0"]);
				
				// Product Tick 
				$images[] = array_merge($imageArr, ["x" => 42.57, "y" => 573.03]);
				
				if ($payment_type == 1) {  // CASH
					$images[] = array_merge($imageArr, ["x" => 255.43, "y" => 593.21]);
				}
				if ($payment_type == 2) { // BASKET
				  $images[] = array_merge($imageArr, ["x" => 107.51, "y" => 593.21]);
				}
				
				// Total Units in Figures
				$textannotations[] = array_merge($config, ["text"=>"$total_units_in_float", "x"=>119.05, "y"=>610.51, "width"=>45.46, "pages"=>"1"]);
				
				// Total Units in WORDS
				$textannotations[] = array_merge($config, ["text"=>"$total_units_in_words", "x"=>209.97, "y"=>610.51, "width"=>378.09, "pages"=>"1"]);
				
				// UTR
				$textannotations[] = array_merge($config, ["text"=>"$utr_no", "x"=>199.87, "y"=>74.74, "width"=>217.91, "pages"=>"2", "height" => 18.74, "size"=>5]);
				
				// SHOW total amount for BUY-CASH cases 
				if($ticket->type == 1 && $ticket->payment_type == 1) 
				{
					// Total AMount in Figures
					$textannotations[] = array_merge($config, ["text"=>"$total_amt", "x"=>108.07, "y"=>91.54, "width"=>114, "pages"=>"2", "height" => 13.74, "size"=>7]);
				}
				
			}
			
			// SELL CASES
			if ($ticket->type == 2) {
				
				// SELL/REDEMPTION Tick 
				$images[] = array_merge($imageArr, ["x" => 162.78, "y" => 309.27, "pages"=>"0"]);
				
				// Product Tick 
				$images[] = array_merge($imageArr, ["x" => 177.5, "y" => 653.77]);
				
				if ($payment_type == 1) {  // CASH
					$images[] = array_merge($imageArr, ["x" => 254.71, "y" => 670.35]);
				}
				if ($payment_type == 2) { // BASKET
				  $images[] = array_merge($imageArr, ["x" => 108.24, "y" => 670.35]);
				}
				
				$textannotations[] = array_merge($config, ["text"=>"$total_units_in_float", "x"=>122.66, "y"=>707.1, "width"=>112.56, "pages"=>"1"]);
				
				$textannotations[] = array_merge($config, ["text"=>"$total_units_in_words", "x"=>76.48, "y"=>725.12, "width"=>455, "pages"=>"1"]);
			}
			
			// DATE
			$textannotations[] = array_merge($config, ["text"=>"$date", "x"=>461.79, "y"=>75.96, "width"=>102.46, "pages"=>"2", "height" => 13.74, "size"=>8]);
			
			// DATE 
			$textannotations[] = array_merge($config, ["text"=>"$date", "x"=>328.31, "y"=>527.18, "width"=>102.46, "pages"=>"2", "height" => 13.74, "size"=>8]);	

			// DATE 
			$textannotations[] = array_merge($config, ["text"=>"$date", "x"=>63.33, "y"=>208.15, "width"=>102.46, "pages"=>"4", "height" => 13.74, "size"=>8]);				
				
		    $urlToken = "filetoken://cf26aaeadffe97a5c448432fb26708cd60cf961d55788ba9e7";
			
			// call API 
			self::callAPIandSaveFile($urlToken, $images, $textannotations, $ticket->id);
		}
		catch (\Exception $e) 
		{
			dd($e->getMessage());
		}
	  
	}
	
	
	// Handle NAVI FORM
	private static function handleNAVIForm($ticket) 
	{

		try 
		{	
		    // VARIABLES
			$marker_json = [];
			$textannotations = [];
			$images = [];
			$urlToken = "";
			
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
			
			$date = date("d-m-Y", time());
			
		    $config = ["size"=>7,"height"=> 10, "pages"=> "0", "type"=> "text"];
			
			$imageArr = ["url" => $checkboxImageData, "width" => 10, "height" => 10.25, "pages" => "0", "keepAspectRatio" => true ];
			
			
			
			// BUY CASES
			if ($ticket->type == 1) {
				
				if ($payment_type == 1) {  // CASH
					$images[] = array_merge($imageArr, ["x" => 204.54, "y" => 504.2]);
				}
				
				if ($payment_type == 2) { // BASKET
				  $images[] = array_merge($imageArr, ["x" => 255.72, "y" =>  504.2]);
				}
				
				$textannotations[] = array_merge($config, ["text"=>"$total_units_in_float", "x"=>191.73, "y"=>583.01, "width"=>102.65]);
				$textannotations[] = array_merge($config, ["text"=>"$total_units_in_words", "x"=>333.01, "y"=>584.46, "width"=>258.05]);
				
				// SHOW total amount for BUY-CASH cases 
				if($ticket->type == 1 && $ticket->payment_type == 1) 
				{
					$textannotations[] = array_merge($config, ["text"=>"$total_amt", "x"=>173.71, "y"=>596, "width"=>118.21]);
					$textannotations[] = array_merge($config, ["text"=>"$word_text", "x"=>325.08, "y"=>595.28, "width"=>267.42, "size"=>6, "height" => 16.6]);
				}
				
				$textannotations[] = array_merge($config, ["text"=>"$utr_no", "x"=>259.49, "y"=>675.38, "width"=>116.77,"size"=>4,"height"=>13]);
			}
			
			// SELL CASES
			if ($ticket->type == 2) {
				if ($payment_type == 1) {  // CASH
					$images[] = array_merge($imageArr, ["x" => 384.74, "y" => 504.2]);
				}
				if ($payment_type == 2) { // BASKET
				  $images[] = array_merge($imageArr, ["x" => 440.24, "y" =>  504.2]);
				}
				$textannotations[] = array_merge($config, ["text"=>"$total_units_in_float", "x"=>180.2, "y"=>555.59, "width"=>108.84]);
				$textannotations[] = array_merge($config, ["text"=>"$total_units_in_words", "x"=>322.2, "y"=>555.59, "width"=>268.14]);
			}
			
		    $urlToken = "filetoken://fbc0f380b58a21e2594c0bd5f3e78cd23dd837c0b9171152eb";
			
			// call API 
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
        
		Log::info("callAPIandSaveFile Function called - [$urlToken], [Ticket ID : $ticketId]");
        
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
            Log::debug("PDF GEneration CURL Error : " . curl_error($curl));
        } else {
			
			if(is_array($response))
			{
			   Log::info("callAPIandSaveFile CURL SUCCESS");	
			}
			else 
			{
				Log::info("callAPIandSaveFile CURL SUCCESS .. Response can not be converted into an ARRAY");
			}
            if (isset($response["url"]) && $response["url"] != "") 
			{
                $filecontent = file_get_contents($response["url"]);
                
				Log::info("callAPIandSaveFile [url parameter found and ran file_get_contents() on response ]");
				
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
                Log::debug("PDF GEneration Error : Response missing URL KEY ." . print_r($response, true));
            }
        }
      } catch (\Exception $e) {
		  Log::info("PDF GENERATION Exception :: Ticket ID --> " . $ticketId);
          dd($e->getMessage());
      }
    }

    public static function saveDocument($ticketid, $text)
    {
      try{
        Storage::put("public/ticketpdfs/ticket-" . $ticketid . ".html", $text);
        //$pdf_file_name = "../storage/app/public/ticketpdfs/ticket-" . $ticketid . ".pdf";
        //Pdf::loadHTML($text)->save($pdf_file_name);
		Log::info("PDF GENERATION : Saving Document :: Ticket ID --> " . $ticketid);
      } catch (\Exception $e) {
		  Log::info("saveDocument Function Exception :: Ticket ID --> " . $ticketid);
          dd($e->getMessage());
      }
    }

    public static function GenerateDocument($ticket)
    {
      try{
        $sec_name = $ticket->security->name;

        if ($sec_name) {
            // Aditya BIRLA Form
            if (stripos($sec_name, "ADITYA BIRLA") !== false) {
                Log::info("Generating PDF for ADITYA BIRLA");
                self::handleBirlaForm($ticket);
            } elseif (stripos($sec_name, "AXIS") !== false) {
                Log::info("Generating PDF for AXIS");
                self::handleAXISForm($ticket);
            } elseif (stripos($sec_name, "LIC") !== false) {
                Log::info("Generating PDF for LIC");
                self::handleLICForm($ticket);
            } elseif (stripos($sec_name, "MIRAE") !== false) {
                Log::info("Generating PDF for MIRAE");
                self::handleMIRAEForm($ticket);
			} elseif (stripos($sec_name, "UTI") !== false) {
                Log::info("Generating PDF for UTI");
                self::handleUTIForm($ticket);
            } elseif (stripos($sec_name, "MOTILAL") !== false) {
                Log::info("Generating PDF for MOTILAL");
                self::handleMOTILALForm($ticket);
            } elseif (stripos($sec_name, "KOTAK") !== false) {
                Log::info("Generating PDF for KOTAK");
                self::handleKOTAKForm($ticket);
            } elseif (stripos($sec_name, "TATA") !== false) {
                Log::info("Generating PDF for TATA");
                self::handleTATAForm($ticket);
            } elseif (stripos($sec_name, "NIPPON") !== false || strtolower($sec_name) == 'cpse etf') {
                Log::info("Generating PDF for NIPPON");
                self::handleNIPPONForm($ticket);
			} elseif (stripos($sec_name, "ICICI") !== false || strtolower($sec_name) == 'bharat 22 etf') {
                Log::info("Generating PDF for ICICI");
                self::handleICICIForm($ticket);	
			} elseif (stripos($sec_name, "BAJAJ") !== false) {
                Log::info("Generating PDF for BAJAJ");
                self::handleBAJAJForm($ticket);	
			} elseif (stripos($sec_name, "HDFC") !== false) {
                Log::info("Generating PDF for HDFC");
                self::handleHDFCForm($ticket);						
            } elseif (stripos($sec_name, "NAVI") !== false) {
                Log::info("Generating PDF for NAVI");
                self::handleNAVIForm($ticket);						
			} elseif (stripos($sec_name, "Quantum") !== false) {
                Log::info("Generating PDF for QUANTUM");
                self::handleQUANTUMForm($ticket);						
            } elseif (stripos($sec_name, "SBI") !== false) {
                Log::info("Generating PDF for SBI");
                self::handleSBIForm($ticket);		
			} elseif (stripos($sec_name, "INVESCO") !== false) {
                Log::info("Generating PDF for INVESCO");
                self::handleINVESCOForm($ticket);			
			} else { 
                Log::info("Generating PDF :: No Matching AMC Name Found");
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
