@extends('layouts.app')

@section('meta-content')
	<title> {{ __('Purchase Package')}} | {{ __('Dotori')}} </title>
@endsection

@section('content')
	@if($purchase_active === false)
		<div class="alert-boxes">
			<div class="alert alert-danger fade show" role="alert">
				{{$inactive_message}}
			</div>
		</div>
	@endif
	<div class="sub_top"><!--sub_top-->
		<div class="sub_title">
			<i class="fas fa-fw fa-cube"></i>
			{{ __('Purchase Package')}}
		</div>
	</div><!--sub_top end-->

	<div class="section_right_inner"><!--section_right_inner-->
		<div class="col-md-12 col-sm-12 col-12">
			<div class="mt-4 col-md-12 col-sm-12 col-12">
				<h3 class="subheader text-grey"> 
					{{$purchase_active ? "Choose a product to purchase" : "Available Packages"}}
				</h3>
				@foreach($packages as $package)
					<div class="buy_package col-lg-3 col-md-4 col-sm-6 col-12 {{'buy_package0' . $package->id}}" 
						onclick="select_package('{{$package->id}}', '{{$package->staking_amount + $package->reward}}', `{{$purchase_active}}`)">
						<img src="{{URL::asset('packages/' . $package->filename)}}" class="package_img"/>
						<p class="text-white subheader mt-3">{{$package->name}}</p>
						<h6 class="text-white">Reward - {{number_format($package->reward)}} SPOINT</h6>
						<div class="total_sum {{'total_sum0' . $package->id}}">
							{{number_format($package->staking_amount + $package->reward)}} {{ __('KRW')}}
						</div>
					</div>		
				@endforeach
			</div>
		</div>
		{{-- @if($purchase_active)
			<div class="col-md-12 col-sm-12 col-12">	
				<div class="alert alert-info fade-show" role="alert">
					<i class="fas fa-exclamation-circle"></i>
					Package purchase is only available from Monday to Friday, from 10:00am to 6:00pm (KST).
				</div>
			</div>
		@endif --}}
	</div>

	<div class="ctrl-btn col-md-3 col-sm-6 col-12"> 
		<a href="/packages/subscribed"> 
			<button class="btn btn-purple-bd"> {{ __('Back to my packages')}} </button>
		</a>
	</div><br/>

	<!-- Modal to show form to enable user purchase a package -->
	<div class="modal fade" id="modal-package-purchase" tabindex="-1" role="dialog" aria-labelledby="request-withdrawal-label">
		<div class="modal-dialog modal-md modal-dialog-scrollable modal-dialog-centered" role="document">
			<div class="modal-content">
				<form action="/package/purchase" method='POST' id="purchase-package-form">
					@csrf
					<div class="modal-header">
						@if($purchase_active)
							<h4 class="text-blue modal-title" id="request-withdrawal-label">
								{{ __('Subscribe Package')}}
							</h4>
						@else
							<p class="text-red">
								{{ __('Sorry, package subscription is inactive for now.')}}
								{{ __('Package purchase is only available from Monday to Friday, from 10:00am to 6:00pm (KST).')}}
							</p>
						@endif
						<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">
						@if($purchase_active)
							<div class="form01">
								<input type="hidden" id="selected-package-id" name="package_id"/>	
								
								<!--withdrawal_input_box-->
								<div style="margin-top: 15px;">
									<span class="text-red" id="insufficientErrorMessage"></span>
								</div>
								<div class="withdrawal_input_box">
									<table style="width:100%;">
										<tbody>
											<tr>
												<td> {{ __('Available Amount')}} </td>
												<td>
													<input type="text" 
														class="withdrawal_input01" 
														disabled 
														value="{{Auth::user()->available_points}}"
													/>
													<input type="hidden" id="available_amount" value="{{Auth::user()->available_points}}"/>
												</td>
											</tr>
											<tr>
												<td>{{ __('Package Price')}}</td>
												<td>
													<input type="text" 
														class="withdrawal_input01" 
														disabled 
														value="{{old('package_price')}}" 
														placeholder="" 
														id="package-price-input"
													/>
													<input type="hidden" name='package_price' id="form-package-price" value="{{old('package_price')}}" />
												</td>
											</tr>
											<tr>
												<td>{{ __('Quantity')}} (PTS) </td>
												<td>
													<input type="number" 
														class="withdrawal_input01" 
														name='quantity' 
														id="package_qty"
														value="{{old('quantity')}}"
														min="0"
														oninput="calculateAmount(this.value)"
													/>
												</td>
											</tr>
											<tr>
												<td> {{ __('Purchase Amount')}} </td>
												<td>
													<input type="text" 
														class="withdrawal_input01" 
														name='total' 
														id="total_amount"
														disabled
														value="{{old('total_amount')}}"
													/>
													<input type="hidden" name='total_amount' id="form-package-amount" value="{{old('total_amount')}}"/>
		
												</td>
											</tr>
											<tr>
												<td> {{ __('PIN')}} </td>
												<td>
													<input type="password" 
														id="pin" 
														class="withdrawal_input01" 
														name="pin" required 
														maxlength="6"
													/>
												</td>
											</tr>
										</tbody>
									</table>
									<p class="text-red" id="pin-error"></p>
								</div><br/>
							</div>
						@endif
					</div>
					@if($purchase_active)
						<div class="modal-footer">
							<!--withdrawal_input_box end-->
							<input type="button" class="btn btn-light-blue-bg" value="Purchase" onclick="validatePurchase('purchase', 'purchase-package-form')">
						</div>
					@endif
				</form>
			</div>
		</div>
	</div>
	<script src="{{URL::asset('/js/subscription.js')}}"></script>

	<script>	
		function select_package(id, price, purchase_active){
			if(purchase_active == true){
				document.getElementById('package-price-input').value = price;
				document.getElementById('form-package-price').value = price;
				document.getElementById('selected-package-id').value = id;
				//give package quanitity of one(1)
				document.getElementById('package_qty').value = 1;
				//get total purchase amount
				calculateAmount(1);
				balanceStatus();			
			}
			$modal = $('#modal-package-purchase');
			$modal.modal('show');
		}

		function calculateAmount(qty){
			var price = document.getElementById('form-package-price').value 
			var total = Number(price) * qty;
			document.getElementById('total_amount').value = total;
			document.getElementById('form-package-amount').value = total;
			balanceStatus();
		}
	</script>
@endsection