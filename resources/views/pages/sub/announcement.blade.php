@extends('layouts.app')

@section('meta-content')
	<title> Announcements | Dotori </title>
@endsection

@section('content')
	<div id="wrap">
		@include('includes.sidebar')	

		<div class="section_right"><!--section_right-->
            <div class="sub_top"><!--sub_top-->
				<div class="sub_title">
					<i class="fas fa-bullhorn fa-fw"></i> &nbsp;
                    Announcements
				</div>
			</div><!--sub_top end-->
        </div>
    </div>
@endsection