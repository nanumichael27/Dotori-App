@extends('layouts.app')

@section('meta-content')
	<title> {{ __('Deposit Requests')}} | {{ __('Dotori')}} </title>
@endsection

@section('content')
	<div class="sub_top"><!--sub_top-->
		<div class="sub_title">
			<i class="fas fa-fw fa-history"></i>
	    	{{ __('Deposit Requests')}}
		</div>
	</div><!--sub_top end-->
			
	<div class="section_right_inner"><!--section_right_inner-->
		<div class="my-3">
			<a href="{{route('deposit.requests.export')}}" class="btn btn-sm btn-light-blue-bg pt-3">
				{{ __('Export')}}
			</a>
		</div>

        <div class="ctrl-btn col-md-3 col-sm-6 col-12"> 
            <a href="/admin/deposits"> 
                <button class="btn btn-purple-bd"> {{ __('View deposits history')}} </button>
            </a>
        </div><br/>

		<div class="deposit_right col-md-12 col-sm-12 col-12 mt-4">
			<div class="history_table">
				<table>
					<tbody>
						<tr>
							<th> {{ __('Name of Member')}} </th>
                            <th> {{ __('Deposit Amount')}} </th>
                            <th> {{ __('Status')}} </th>
							<th> {{ __('Date')}} </th>
							<th> {{ __('Action')}} </th>
						</tr>						
						@if($deposits->count() > 0)
							@foreach($deposits as $deposit)
							<tr>
                                <td> {{$deposit->user->name}} </td>
								<td> {{number_format($deposit->amount)}}</td>
								<td> {{$deposit->status}} </td>
								<td> {{$deposit->updated_at}} </td>
								<td> 
									<button 
										class="btn btn-light-blue-bg" 
										onclick="showDepositModal(`{{$deposit->id}}`, `{{$deposit->user->name}}`, 
											`{{number_format($deposit->amount)}}`, `{{$deposit->bank_name}}`, `{{$deposit->account_name}}`, 
											`{{$deposit->status}}`, `{{$deposit->updated_at}}`)"
									> {{ __('View')}} </button>
								</td>
							</tr>
							@endforeach
						@else
							<tr>
								<td colspan="8"> {{ __('There are no deposit requests yet.')}} </td>
							</tr>
						@endif
					</tbody>
				</table>
			</div>
			@if($deposits->count() > 0)
				<div class="mt-2">
					<hr/>
					{{$deposits->links("pagination::bootstrap-4")}}
				</div>
			@endif
		</div>
	</div><!--section_right_inner end-->

	<!-- Modal to display all the details of a deposit request -->
	<div class="modal fade" id="modal-request-deposit" tabindex="-1" role="dialog" aria-labelledby="request-deposit-label">
		<div class="modal-dialog modal-md modal-dialog-scrollable modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="text-blue modal-title" id="request-deposit-label">
						{{ __('Deposit Request')}}
					</h4>
					<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<div class="modal-body">
					<form id="form-validate-deposit" method="POST" action="/admin/deposit/validate">
						@csrf

						<div class="form-group">
							<span> 
								{{ __('Dotori Account Name')}}
							</span>
							<input type="text" 
								disabled
								class="form-control" 
								id='dotori_name'
							/>
						</div>
						
						<div class="form-group">
							<span> 
								{{ __('Deposit Amount')}} ({{__('KRW')}})
							</span>
							<input type="text" 
								disabled
								class="form-control" 
								id='deposit_amount'
							/>
						</div>

						<div class="form-group">
							<span> 
								{{ __('Depositor\'s Bank')}}
							</span>
							<input type="text"
								disabled
								class="form-control" 
								id='bank_name'
							/>
						</div>

						<div class="form-group">
							<span>
								{{ __('Depositor\'s Name')}}
							</span>
							<input type="text" 
								disabled
								class="form-control" 
								id="depositor_name" 
							/>
						</div>

						<div class="form-group">
							<span>
								{{ __('Deposit Status')}}
							</span>
							<input type="text" 
								disabled
								class="form-control" 
								id="deposit_status" 
							/>
						</div>

						<div class="form-group">
							<span>
								{{ __('Date of Request')}}
							</span>
							<input type="text" 
								disabled
								class="form-control" 
								id="date" 
							/>
						</div>

						<input type="hidden" id="deposit_transaction_id" name="deposit_id"/>
						<button type="button" class="btn btn-purple-bg" onclick="validateDeposit()">
							{{ __('Validate deposit')}}	
						</button>
					</form>
				</div>
			</div>
		</div>
	</div>

	<script>
		function showDepositModal(id, name, amount, bank, account, status, date){
			document.getElementById('dotori_name').value = name;
			document.getElementById('deposit_amount').value = amount;
			document.getElementById('bank_name').value = bank;
			document.getElementById('depositor_name').value = account;
			document.getElementById('deposit_status').value = status;
			document.getElementById('date').value = date;
			document.getElementById('deposit_transaction_id').value = id;
			var $modal;
			$modal = $('#modal-request-deposit');
			$modal.modal('show');
		}	

		function validateDeposit(){
			var confirmVal = confirm("Are you sure that the account holder has made payment?");
			if(confirmVal){
				document.getElementById('form-validate-deposit').submit();
			}
		}
	</script>
@endsection