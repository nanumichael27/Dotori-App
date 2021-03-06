@extends('layouts.app')

@section('meta-content')
	<title> {{ __('Dashboard')}} | {{ __('Dotori')}} </title>
	<script src="https://rawgit.com/kottenator/jquery-circle-progress/1.2.2/dist/circle-progress.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/react@16.12/umd/react.production.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/react-dom@16.12/umd/react-dom.production.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/prop-types@15.7.2/prop-types.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/babel-core/5.8.34/browser.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
	<script src="https://cdn.jsdelivr.net/npm/react-apexcharts@1.3.6/dist/react-apexcharts.iife.min.js"></script> 
	<style>
		.title01{
			line-height: 1.4em !important
		}
	</style>
@endsection
 
@section('content')
	<div class="main_notice">
		<p class="sub_title" style="padding-top: 0px !important"> 
			<i class="fas fa-fw fa-tachometer-alt"></i>
			{{ __('Dashboard')}}
		</p>
		<div class="notice_more purple-bg text-white" onclick="locaion.href='#'">
			{{ __('Admin')}}
		</div>
	</div>
	<div class="section_right_inner">
		<!--main_section01--> 
		<div class="main_section01">       
			<div class="main_balance_box main_balance_box01 mb-3">
				<p class="title01"> {{ __('Number of Members')}} </p>
				<p class="s_title01"></p>
				<div class="total_sum total_sum01">
					<p> {{number_format($registered_users)}} </p>	
				</div>
			</div>					
			<div class="main_balance_box main_balance_box02 mb-3">
				<p class="title01"> {{ __('Active Subscriptions')}} </p>
				<p class="s_title01"></p>
				<div class="total_sum total_sum02">
					<p> {{number_format($active_subscriptions)}} </p>
				</div>
			</div>
			<div class="main_balance_box main_balance_box03 mb-3">
				<p class="title01"> {{ __('Pending Subscriptions')}} </p>
				<p class="s_title01"></p>
				<div class="total_sum total_sum03">
					<p> {{number_format($pending_subscriptions)}} </p>
				</div>
			</div>
			<div class="main_balance_box main_balance_box04 mb-3">
				<p class="title01"> {{ __('Withdrawal Requests')}} </p>
				<p class="s_title01"> (pending) </p>
				<div class="total_sum total_sum04">
					<p> {{number_format($withdrawal_requests)}} </p>
				</div>
			</div>
			<div class="main_balance_box main_balance_box04 mb-3">
				<p class="title01"> {{ __('Deposit Requests')}} </p>
				<p class="s_title01"> ({{ __('pending')}}) &nbsp;</p>
				<div class="total_sum total_sum04">
					<p> {{number_format($deposit_requests)}} </p>
				</div>
			</div>					
		</div>
		<!--main_section01 end-->

		{{-- <!--main_section02-->
		<div class="main_section02">
			<div class="bonus_div">
				<p class="title">
					<i class="fas fa-chart-pie"></i>
					Bonus Chart
				</p>
				<div id="app"></div>
				<div class="bonus_total">
					<ul>
						<li>
							<span class="span01"></span>
							<span> inducement </span>
							<span class="total"> </span>
						</li>
						<li>
							<span class="span01"></span>
							<span>accumulate</span>
							<span class="total"> </span>
						</li>
						<li>
							<span class="span01"></span>
							<span> rank </span>
							<span class="total"> </span>
						</li>
					</ul>
				</div>
			</div>

			<div class="bonus_div referral-block">
				<div class="title">
					<i class="fas fa-link"></i>
					Referral Link
				</div><br/>
				<p class="referral-link">
					<span id="linkBar"> 
						{{"http://127.0.0.1:8000/register?refer=" . Auth::user()->memberId}}
					</span>
				</p>
				<p>
					<button class="btn btn-purple-bd"> Copy link </button>
				</p>
			</div>
		</div>
		<!--main_section02 end--> --}}

		<!--main_section03-->
		{{-- <div class="main_section03">
			<!--history_box01-->
			<div class="history_box01">
				<p class="title">
					<i class="fas fa-gift"></i> 
					Bonus History	
				</p>					

				<!--Daily history-->
				<div id ="tab-1" class="tab-content current table_a">
					<table>
						<tr>
							<th> Bonus </th>
							<th> Amount (PTS) </th>
							<th> Date </th>
							<th> Status </th>
						</tr>
						<tr>
							<td> Inducement </td>
							<td> 120,000 </td>
							<td>2022-02-07</td>
							<td> Paid </td>
						</tr>
						<tr>
							<td> Accumulate </td>
							<td> 240,000 </td>
							<td>2022-02-07</td>
							<td> Pending </td>
						</tr>
						<tr>
							<td> Rank </td>
							<td> 45,000,000 </td>
							<td>2022-02-07</td>
							<td> Paid </td>
						</tr>
					</table>
				</div>
				<!--Daily history end-->
			</div>
			<!--history_box01 end-->

			<!--history_box02-->
			<div class="history_box02">	
				<p class="title">
					<i class="fas fa-history"></i>
					Withdrawal History
				</p>
				<div class="table_a">
					<table>
						<tr>
							<th> Amount (KRW) </th>
							<th> Date </th>
							<th> Status </th>
						</tr>
						<tr>
							<td> 45,000,000 </td>
							<td> 2022-02-07 </td>
							<td> Pending </td>
						</tr>
					</table>
				</div>
			</div>
			<!--history_box02 end-->
		</div> --}}
		<!--main_section03 end-->
	</div><!--section_right inner end-->

	<script type="text/babel">
      	class ApexChart extends React.Component {
        	constructor(props) {
          		super(props);

				this.state = {
					series: [44, 55, 13, 33, 12],
					options: {
						chart: {
							width: 240,
							type: 'donut',
						},
						dataLabels: {
							enabled: false
						},
						responsive: [{
							breakpoint: 480,
							options: {
								chart: {
									width: 200
								},
								legend: {
									show: false
								}
							}
						}],
						legend: {
							position: 'right',
							offsetY: 0,
							height: 230,
						}
					},
				};
        	}
      
			appendData() {
				var arr = this.state.series.slice()
				arr.push(Math.floor(Math.random() * (100 - 1 + 1)) + 1)
				this.setState({
					series: arr
				})
			}
        
			removeData() {
				if(this.state.series.length === 1) return
				
				var arr = this.state.series.slice()
				arr.pop()
				
				this.setState({
					series: arr
				})
			}
        
			randomize() {
				this.setState({
					series: this.state.series.map(function() {
					return Math.floor(Math.random() * (100 - 1 + 1)) + 1
					})
				})
			}
			
			reset() {
				this.setState({
					series: [44, 55, 13, 33]
				})
			}
      
			render() {
				return (
					<div>
						<div>
							<div class="chart-wrap">
								<div id="chart">
									<ReactApexChart 
									  options={this.state.options} 
									  series={this.state.series} 
									  type="donut" 
									  width={240} 
									/>
								</div>
							</div>
						</div>
					</div>
				);
			}
        }
		
		const domContainer = document.querySelector('#app');
      	ReactDOM.render(React.createElement(ApexChart), domContainer);
    </script>

	<script>
		$(document).ready(function(){
			$('ul.tab_ul li').click(function(){
				var tab_id = $(this).attr('data-tab');

				$('ul.tab_ul li').removeClass('current');
				$('ul.tab_ul li').removeClass('tab_active');
				$('.tab-content').removeClass('current');

				$(this).addClass('current');
				$(this).addClass('tab_active');
				$("#"+tab_id).addClass('current');
			})
		})
	</script>
@endsection



