@extends('layouts.master')

@section( 'title' )
	{{ $this->title ? $this->title : 'Error' }}
@stop

@section( 'content' )
	
	<div class="panel panel-danger">
		<div class="panel-body text-center">
			<h3> {{ $this->message }} <small> {{ isset($this->code) ? $this->code : '' }} </small></h3>
		</div>
	</div>
@stop