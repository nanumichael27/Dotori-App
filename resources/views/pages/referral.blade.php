@extends('layouts.app')

@section('meta-content')
	<title> {{ __('Referrals')}} | {{ __('Dotori')}} </title>
	<style>
		.sub_title img {
			float: left;
			margin-top: 13px;
			margin-right: 5px;
		}
		p{
			line-height: 2em;
			margin-bottom: 1.2em
		}
		.card{
			padding: 20px 30px
		}
	</style>
@endsection

@section('content')
	<div class="sub_top"><!--sub_top-->
		<div class="sub_title">
			<i class="fas fa-fw fa-sitemap"></i> 
			{{ __('Recommendation Chart')}}
		</div>
	</div><!--sub_top end-->

	<div class="section_right_inner"><!--section_right_inner-->				
		<div class="col-md-6 col-sm-6 col-12 card">
			<h4 class="text-purple"> {{ __('Referral Link')}}: </h4>
			<p class="referral-link" id="linkBar">
				{{route('register')."?refer=" . Auth::user()->memberId}}
			</p>
			<p> 
				<input type="button" value="{{ __('Copy Link')}}" class="btn btn-purple-bd" onclick="copyToClipBoard()"/>
			</p>
		</div><br/>
		
		<!--Withdrawal_right-->
		<div class="deposit_right col-md-12 col-sm-12 col-12 mt-4">
			<p class="title">
				<i class="fas fa-fw fa-history"></i>
				{{ __('Referral History')}} 
			</p>
			<div class="history_table">
				<table>
					<tbody>
						<tr>
							<th> {{ __('Member ID')}} </th>
							<th> {{ __('Member Rank')}} </th>
							<th> {{ __('Subscription Status')}} </th>
							<th> {{ __('Join Date')}} </th>
						</tr>

						@if($referrals->count() > 0)
							@foreach($referrals as $referral)
							<tr>
								<td> {{$referral->memberId}} </td>
								<td>
									{{$referral->subscribed_user !== null ? $referral->rank->name : "Level 1"}}
								</td>
								<td> 
									{{$referral->subscribed_user !== null ? $referral->subscribed_user->status : "inactive" }}
								</td>
								<td> {{$referral->created_at}} </td>
							</tr>
							@endforeach
						@else
							<tr>
								<td colspan="4"> {{ __('You have not recommended anyone yet.')}} </td>
							</tr>
						@endif
					</tbody>
				</table>
			</div>
		</div>
	</div><!--section_right_inner end-->

@endsection