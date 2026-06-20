@php
    $subtotalString = Cart::instance('shopping')->subtotal();
    $subtotal = (float) preg_replace('/[^\d.]/', '', $subtotalString);
    $shipping = (float) preg_replace('/[^\d.]/', '', Session::get('shipping', 0));
    $discount = (float) preg_replace('/[^\d.]/', '', Session::get('discount', 0));
    $grand_total = $subtotal + $shipping - $discount;

    // Advance Logic
    $advance_amount = \App\Http\Controllers\Frontend\ShoppingController::getCartAdvanceAmount();
    $hasAdvance     = $advance_amount > 0;
    $due_amount     = $hasAdvance ? ($grand_total - $advance_amount) : 0;
@endphp

<input type="hidden" id="js_subtotal" value="{{ $subtotal }}">

<table class="cart_table table table-bordered table-striped text-center mb-0">
    <thead>
        <tr>
            <th style="width: 20%;">ডিলিট</th>
            <th style="width: 40%;">প্রোডাক্ট</th>
            <th style="width: 20%;">পরিমাণ</th>
            <th style="width: 20%;">মূল্য</th>
        </tr>
    </thead>

    <tbody>
        @foreach(Cart::instance('shopping')->content() as $value)
        <tr>
            <td>
                <a class="cart_remove" data-id="{{$value->rowId}}"><i class="fas fa-trash text-danger"></i></a>
            </td>
            <td class="text-left">
                <a href="{{route('product',$value->options->slug)}}"> 
                    <img src="{{asset($value->options->image)}}" style="height:30px;width:30px" /> 
                    {{Str::limit($value->name,20)}}
                </a>
                @if($value->options->product_size)
                    <p>Size: {{$value->options->product_size}}</p>
                @endif
                @if($value->options->product_color)
                    <p>Color: {{ $value->options->product_color }}</p>
                @endif
            </td>
            <td class="cart_qty">
                <div class="qty-cart vcart-qty">
                    <div class="quantity">
                        <button class="minus cart_decrement" data-id="{{$value->rowId}}">-</button>
                        <input type="text" value="{{$value->qty}}" readonly />
                        <button class="plus cart_increment" data-id="{{$value->rowId}}">+</button>
                    </div>
                </div>
            </td>
            <td><span class="alinur">৳ </span><strong>{{$value->price}}</strong></td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3" class="text-end px-4">মোট</th>
            <td class="px-4" id="subtotalAmount">
                ৳ {{ number_format($subtotal, 2) }}
            </td>
        </tr>
        <tr>
            <th colspan="3" class="text-end px-4">ডেলিভারি চার্জ</th>
            <td class="px-4" id="shippingAmount">
                ৳ {{ number_format($shipping, 2) }}
            </td>
        </tr>
        @if($discount > 0)
        <tr>
            <th colspan="3" class="text-end px-4">কুপন ছাড়</th>
            <td class="px-4" id="discountAmount">
                -৳ {{ number_format($discount, 2) }}
            </td>
        </tr>
        @endif
        <tr>
            <th colspan="3" class="text-end px-4">সর্বমোট</th>
            <td>
                <b id="grandTotalAmount">৳ {{ number_format($grand_total, 2) }}</b>
            </td>
        </tr>

        @if($hasAdvance)
            <tr>
                <th colspan="3" class="text-end px-4">এখন অগ্রিম পরিশোধ করবেন</th>
                <td class="px-4 text-success">
                    <b id="advanceAmountCell">৳ {{ number_format($advance_amount, 2) }}</b>
                </td>
            </tr>
            <tr>
                <th colspan="3" class="text-end px-4">ডেলিভারির সময় দিতে হবে</th>
                <td class="px-4 text-danger">
                    <b id="dueAmountCell">৳ {{ number_format($due_amount, 2) }}</b>
                </td>
            </tr>
        @endif
    </tfoot>
</table>

<script>
    // Event delegation for cart actions
    $(document).off('click', '.cart_remove').on('click', '.cart_remove', function(){
        var id = $(this).data('id');
        if(id){
            $.get("{{route('cart.remove')}}", {'id':id}, function(data){
                $(".cartlist").html(data);
                if(typeof cart_count === 'function') cart_count();
            });
        }
    });

    $(document).off('click', '.cart_increment').on('click', '.cart_increment', function(){
        var id = $(this).data('id');
        if(id){
            $.get("{{route('cart.increment')}}", {'id':id}, function(data){
                $(".cartlist").html(data);
                if(typeof cart_count === 'function') cart_count();
            });
        }
    });

    $(document).off('click', '.cart_decrement').on('click', '.cart_decrement', function(){
        var id = $(this).data('id');
        if(id){
            $.get("{{route('cart.decrement')}}", {'id':id}, function(data){
                $(".cartlist").html(data);
                if(typeof cart_count === 'function') cart_count();
            });
        }
    });

    function cart_count(){
        $.get("{{route('cart.count')}}", function(data){
            if(data){
                $("#cart-qty").html(data);
            }else{
                $("#cart-qty").empty();
            }
        });
    }
</script>