@extends('layouts.app')

@section('meta-content')
	<title> {{ __('Edit product')}} | {{ __('Dotori')}} </title>
@endsection

@section('content')
	<div class="sub_top"><!--sub_top-->
		<div class="sub_title">
			<i class="fas fa-fw fa-money-check-alt"></i>
			{{ __('Edit Product')}}
		</div>
	</div><!--sub_top end-->
	<div class="section_right_inner"><!--section_right_inner-->
		<!--withdrawal_left-->
		<div class="withdrawal_left col-md-12 col-sm-12 col-12">
            <form action="/admin/shopping-products/{{$product->id}}/update" method="POST">
                @csrf
                <div class="form-group">
                    <span> 
                        {{ __('Product Name')}} <span class="text-red">*</span> 
                    </span>
                    <input type="text" 
                        class="form-control" 
                        name="product_name" 
                        value="{{$product->name}}" 
                        required
                    />
                </div>

                <div class="form-group">
                    <span> 
                        {{ __('Product Description')}} <em class="text-grey">({{ __('Optional')}})</em> 
                    </span>
                    <textarea
                        class="form-control" 
                        name="product_description" 
                        rows="5"
                    >{{$product->description}}</textarea>
                </div>

                <div class="form-group">
                    <span> 
                        {{ __('Price')}} (SPOINT) <span class="text-red">*</span> 
                    </span>
                    <input type="number" 
                        class="form-control" 
                        name="product_price" 
                        value="{{$product->price}}" 
                        required
                    />
                </div>

                <div class="form-group">
                    <div class="container" style="padding:0px">
                        <div class="row" style="padding:0px">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 mb-2" id="image_container" style="padding:0px; margin-left:12px">
                                <img id="output_img" width="100%" height="auto" src="{{asset('products/' . $product->filename)}}"/>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 file-input">
                                <input type="file" name="product_image" class="image" accept=".png, .jpg, .jpeg"/><br><br>
                                <input type="hidden" id="base64image" name="base64image">
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-purple-bg">
                    {{ __('Update product')}}
                </button> &nbsp;
                @if($product->is_active)
                    <button type="button" class="btn btn-grey-bg" onclick="updateProductStatus('disable')">
                        {{ __('Disable product')}}
                    </button>
                @else
                    <button type="button" class="btn btn-purple-bd" onclick="updateProductStatus('enable')">
                        {{ __('Enable product')}}
                    </button>
                @endif
            </form>
		</div>

        <form action="/admin/shopping-products/{{$product->id}}/update-status" method="POST" id="update-product-status-form">
            @csrf
            <input type="hidden" name="status-action" id="status-action"/>
        </form>
		<!--withdrawal_left end-->	
	</div><!--section_right_inner end-->
	@include('includes.productImageUpload')

    <script>
        function updateProductStatus(statusAction){
            var confirmDisable = confirm(`Are your sure you want to ${statusAction} this product?`);
            if(confirmDisable){
                document.getElementById('status-action').value = statusAction;
                document.getElementById('update-product-status-form').submit();
            }
        }
    </script>
@endsection