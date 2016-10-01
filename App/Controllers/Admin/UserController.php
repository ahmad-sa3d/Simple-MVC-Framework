<?php
	namespace App\Controllers\Admin;

	use App\Controllers\Controller;
	use App\Library\Response;
	use App\Library\Request;
	use App\Library\App;
	use App\Library\Redirect;
	use App\Library\SimpleDateTime;
	use App\Library\Validator;
	use App\Model\User;

	class UserController extends Controller
	{
		public function index()
		{
			$users = User::all([ 'order_by' => 'username', 'order' => 'ASC' ]);
			// return User::all( [ 'order_by' => 'email', 'order' => 'DESC' ] );

			return Response::view( 'admin.user.index', compact('users') );
		}

		/**
		 * Form Page To Insert New Record data
		 * @return View Response 	Form Page
		 */	
		public function create()
		{
			return Response::view( 'admin.user.create' );
		}

		/**
		 * Store New Record
		 * @param  Request $request App Request Instance
		 * @return Redirect           Redirect To New Created Record Page
		 */
		public function store( Request $request )
		{
			$validator = Validator::make( $request->all(), [
				'username' => 'required|string|between:5,20|unique:users,username',
				'email' => 'required|email|unique:users,email',
				'password' => 'required|min:6',
			],
			[
				'username.min' => 'Custom Error Message For Username Min Rule Error',
				// 'username.*' => 'Custom Error Message For Any Username Rule',
				// 'email.email' => 'Custom Error Message For Email Email Rule Error',
			] );

			if( $validator->fails() )
				return Redirect::to( '/admin/user/create' );


			$user = new User();

			$user->username = $request->input( 'username' );
			$user->email = $request->input( 'email' );
			$user->password = $this->hashMake( $request->input( 'password' ) );
			$user->admin = $request->has( 'admin' );

			if( $user->save() )
			{
				$this->makeSuccessNotification( 'User <strong>' . $user->username . '</strong> has successfully Created.' );
				return Redirect::to( '/admin/user/' . $user->id );
			}
			else
			{
				$this->makeErrorNotification( 'Error On Saving User <strong>' . $user->username . '</strong> Try Again Later.' );
				return Redirect::to( '/admin/user' );
			}

		}

		/**
		 * Edit Record View
		 * @param  User  $user Record To be Edit
		 * @param  Request $request App Request Instance
		 * @return View Responce          
		 */
		public function edit( User $user )
		{
			return Response::view( 'admin.user.edit', compact('user') );
		}

		/**
		 * Display Single Record View
		 * @param  Record $user Record To Display
		 * @return Response View
		 */
		public function show( User $user )
		{
			return Response::view( 'admin.user.show', compact('user') );
		}

		/**
		 * Update Record
		 * @param  User $user User Instance
		 * @param  Request $request App Request
		 * @return Redirect
		 */
		public function update( User $user, Request $request )
		{
			// Validate
			$validator = Validator::make( $request->all(), [
				'username' => 'required|string|between:5,20|unique:users,username,'.$user->id,
				'email' => 'required|email|unique:users,email,'.$user->id,
				'password' => 'required|min:6',
				] );

			if( $validator->fails() )
				return Redirect::to( '/admin/user/create' );

			$user->username = $request->input( 'username' );
			$user->email = $request->input( 'email' );
			$user->password = $this->hashMake( $request->input( 'password' ) );
			
			$user->admin = $request->has( 'admin' );

			$user->updated_at = new SimpleDateTime();

			if( $user->save() )
			{
				$this->makeSuccessNotification( 'User <strong>' . $user->username . '</strong> has successfully Updated.' );
				return Redirect::to( '/admin/user/' . $user->id );
			}
			else
			{
				$this->makeErrorNotification( 'Error On Saving User <strong>' . $user->username . '</strong> Try Again Later.' );
				return Redirect::to( '/admin/user/' . $user->id );
			}
		}

		/**
		 * Delete Single Record View
		 * @param  Record $user Record To Delete
		 * @return Response View
		 */
		public function delete( User $user )
		{
			return Response::view('admin.user.delete', compact('user'));
		}

		/**
		 * Destroy Single Record
		 * @param  Record $user Record To Destroy
		 * @return Redirect
		 */
		public function destroy( User $user )
		{
			if( $user->id == $this->session->user->id )
			{
				// User cannot Delete himself
				$this->makeErrorNotification( 'Sorry You cannot Delete Yourself!.' );
				return Redirect::to( '/admin/user/' . $user->id );
			}

			if( $user->delete() )
			{
				$this->makeSuccessNotification( 'User <strong>' . $user->username . '</strong> has successfully Deleted.' );
				return Redirect::to( '/admin/user' );
			}
			else
			{
				$this->makeErrorNotification( 'User <strong>' . $user->username . '</strong> Couldnot be Deleted.' );
				return Redirect::to( '/admin/user/' . $user->id );
			}
		}

		
	}