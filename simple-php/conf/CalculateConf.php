<?php 
	define('QUOTESHOST', 'https://forex.1forge.com/1.0.2/quotes?');
	define('APIKEY', 'Q6quscv3HNf89vlET3bgqBQBrSBFLrAN');
	define('SYMBOLS', 'https://forex.1forge.com/1.0.2/symbols?');

	//
	/*API Calls
		/quotes - Get quotes for specific currency pair(s)
		/symbols - Get a list of symbols
		/convert - Convert from one currency to another
		/market_status - Check if the market is open
		/quota - Check your current usage and remaining quota
	*/
	//
	//https://forex.1forge.com/1.0.2/quotes?pairs=USDCNH,GBPJPY,AUDUSD&api_key=Q6quscv3HNf89vlET3bgqBQBrSBFLrAN
	//
	//https://forex.1forge.com/1.0.2/symbols?api_key=Q6quscv3HNf89vlET3bgqBQBrSBFLrAN
	//
	///convert - Convert from one currency to another:
	//https://forex.1forge.com/1.0.2/convert?from=USD&to=EUR&quantity=100&api_key=Q6quscv3HNf89vlET3bgqBQBrSBFLrAN
	//
	///quota - Check your current usage and remaining quota:
	///GET https://forex.1forge.com/1.0.2/quota?api_key=Q6quscv3HNf89vlET3bgqBQBrSBFLrAN
 ?>