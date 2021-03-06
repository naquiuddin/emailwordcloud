<?php

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function showWelcome()
	{
		return View::make('hello');
	}

	public function saveSendGridEmail()
	{
		return View::make('hello');
	}

	public function showWordCloud()
	{
		return View::make('showcloud');
	}

	public function getWords() {
		$neoModel = new Neo4jModel();
		if(!isset($_GET['data'])){
			//TODO: Take a word as an input and use that to filter the word count as well.
			$result = $neoModel->getWordCount();
			return Response::json($result);
		}
		else {
			$wordArr = Input::all();
			$result = $neoModel->getWordCount($wordArr['data']);
			return Response::json($result);
		}
	}

}