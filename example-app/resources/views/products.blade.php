<x-app-layout>

<div class="container mx-auto mt-4">
  <div class="row">
  	<div class="col-md-12 psection1 pb-4">
  		<a href="/dashboard/products/0" class="btn bg-primary text-white ">Add Product</a>		
  	</div>			
  </div>
  <div class="row psection2">
@foreach( $prodata as $product)
    <div class="col-md-4">
      <div class="card" style="width: 18rem;">
  		<img src="{{ $product->product }}" class="card-img-top" alt="...">
  		<div class="card-body">
    		<h5 class="card-title">{{ $product->name }}</h5>
        	<h6 class="d-flex card-subtitle mb-2 text-muted justify-content-between"><span>Brand</span><span>{{ $product->store }}</span></h6>
    		<p class="d-flex card-text bg-red justify-content-between"><span>Price</span><span>{{ $product->price }}</span></p>
       		<a href="#" class="btn bg-info w-100 text-white">Buy</a>
  		</div>
  	  </div>
    </div> 
@endforeach
	</div>
</div>

</x-app-layout>



