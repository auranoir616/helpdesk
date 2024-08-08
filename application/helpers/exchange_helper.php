<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	if ( ! function_exists( 'exchange' ) ) {
		
		/**
		 * undocumented function
		 *
		 * @return void
		 * @author 
		 **/
		function exchange( $from = 'BTC', $to = COIN_EXT, $amount = 0 )
		{

			
			/*=========================================
			=            EXCHANGE PLATFORM            =
			=========================================*/
			
			$price  		= 0;

			
			/* exchange from USD */
			
			if ( ($from == 'USD') && ($to == 'BTC') ) {
				$price 		= number_format( blockchain_to_btc( $amount )  ,8,'.','');
			}

			if ( ($from == 'USD') && ($to == COIN_EXT) ) {
				$price 		= $amount / option('CoinToUSD');
			}

			if ( ($from == 'USD') && ($to == 'ETH') ) {
				$price 		= $amount * etherium_price( 'USD', 'ETH' )->ETH;
			}


			/* exchange from BTC */

			if ( ($from == 'BTC') && ($to == 'USD') ) {
				$price 		= blockchain_exchange( 'USD' ) * $amount;
			}
			
			if ( ($from == 'BTC') && ($to == COIN_EXT) ) {
				$price 		= (blockchain_exchange( 'USD' ) * option('CoinToUSD')) * $amount;
			}

			if ( ($from == 'BTC') && ($to == 'ETH') ) {
				$price 		= $amount * etherium_price( 'BTC', 'ETH' )->ETH;
			}


			/* exchange from COIN_EXT */

			if ( ($from == COIN_EXT) && ($to == 'USD') ) {
				$price 		= $amount * option('CoinToUSD');
			}
			
			if ( ($from == COIN_EXT) && ($to == 'BTC') ) {
				$price 		= number_format( (blockchain_to_btc( $amount ) * option('CoinToUSD')) ,8,'.','');
			}

			if ( ($from == COIN_EXT) && ($to == 'ETH') ) {
				$price 		= ($amount * option('CoinToUSD')) * etherium_price( 'USD', 'ETH' )->ETH;
			}

			if ( ($from == COIN_EXT) && ($to == 'IDR') ) {
				$price 		= $amount * option('price_per_coin_wd');
			}


			/* exchange from etherium */

			if ( ($from == 'ETH') && ($to == 'USD') ) {
				$price 		= $amount * etherium_price( 'ETH', 'USD' )->USD ;
			}
			
			if ( ($from == 'ETH') && ($to == 'BTC') ) {
				$price 		= $amount * etherium_price( 'ETH', 'BTC' )->BTC ;
			}

			if ( ($from == 'ETH') && ($to == COIN_EXT) ) {
				$price 		= etherium_price( 'ETH', 'USD' )->USD / ($amount * option('CoinToUSD'));
			}


			/* exchange from IDR */
			if ( ($from == 'IDR') && ($to == COIN_EXT ) ) {
				$price_ina 	= option('price_per_coin_deposit');
				$price 		= $amount / $price_ina;
			}
			
			
			
			/*=====  End of EXCHANGE PLATFORM  ======*/
				


			return $price;

		}

	}


	/**
	 * untuk parameter to bisa multiple dengan memformat dengan coma misal USD,BTC,LTC
	 *
	 * @return JSON data
	 * @author 
	 **/
	function etherium_price( $from = 'ETH', $to = 'USD' )
	{
		$curl 	= getUrlContent( 'https://min-api.cryptocompare.com/data/price?fsym='.$from.'&tsyms=' . $to );
		return json_decode($curl);
	}


	/**
	 * sak currency pirang BTC
	 *
	 * @return void
	 * @author 
	 **/
	function blockchain_to_btc( $amount = 0, $currency = 'USD' )
	{

		$curl 	= getUrlContent('https://blockchain.info/tobtc?currency='.$currency.'&value='.$amount.'');
		return $curl;
	}


	/**
	 * 1 BTC pirang currency
	 * daftar currency tersedia lihat di : https://blockchain.info/ticker
	 * @return void
	 * @author 
	 **/
	function blockchain_exchange( $get_currency = 'USD' )
	{
		$curl 		= getUrlContent('https://blockchain.info/ticker');
		$result 	= json_decode( $curl );

		if ( isset( $result->$get_currency ) ) {
			$output 	= $result->$get_currency->sell;
		}
		else {
			$output 	= false;
		}

		return $output;
	}


	/*==================================
	=            BTC FORMAT            =
	==================================*/
	/**
	 * echo formatBTC(convertToBTCFromSatoshi(5000));
	 *
	 * @return void
	 * @author 
	 **/
	function convertToBTCFromSatoshi($value) {
	    $BTC = $value / 100000000 ;
	    return $BTC;
	}

	function convertToSatoshi($value) {
	    $BTC = $value * 100000000 ;
	    return $BTC;
	}

	function formatBTC($value, $currency = true) {
	    $value = sprintf('%.8f', $value);

	    if ( $currency == true ) {
	    	$currency 	= ' BTC';
	    }else {
	    	$currency 	= '';
	    }

	    $value = convertToBTCFromSatoshi(rtrim($value, '0'))  .  $currency ;
	    return $value;
	}

	function formatCOIN($value) {
	    $value = sprintf('%.8f', $value);
	    $value = convertToBTCFromSatoshi(trim($value, '0')) . '';
	    return $value;
	}


	function getUrlContent($url){
		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		$data = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		return ($httpcode>=200 && $httpcode<300) ? $data : false;
	
	}
