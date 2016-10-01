<?php
namespace App\Controllers;

use App\Controllers\Controller;
use App\Library\Response;
use App\Model\Product;
use App\Library\App;

class IndexController extends Controller
{
	public function index()
	{
		return Response::view( 'home' );
	}

}