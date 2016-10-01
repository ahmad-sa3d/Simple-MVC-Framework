<?php
	namespace App\Controllers\Admin;

	use App\Controllers\Controller;
	use App\Library\Response;
	use App\Model\User;

	class DashboardController extends Controller
	{
		public function index()
		{	
			return Response::view( 'admin.dashboard' );
		}
	
	}