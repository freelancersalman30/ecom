@extends('backEnd.layouts.master')
@section('title',$order_status->name.' Order')
@section('content')
<div class="container-fluid">
    
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{route('admin.order.create')}}" class="btn btn-danger rounded-pill"><i class="fe-shopping-cart"></i> POS Create</a>
                </div>
                <h4 class="page-title">{{$order_status->name}} Order ({{$order_status->orders_count}})</h4>
            </div>
        </div>
    </div>       
    <!-- end page title --> 
   <div class="row order_page">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-8">
                        <ul class="action2-btn">
                            <li><a data-bs-toggle="modal" data-bs-target="#asignUser" class="btn rounded-pill btn-success"><i class="fe-plus"></i> Assign User</a></li>
                            <li><a data-bs-toggle="modal" data-bs-target="#changeStatus" class="btn rounded-pill btn-primary"><i class="fe-plus"></i> Change Status</a></li>
                            <li><a href="{{route('admin.order.bulk_destroy')}}" class="btn rounded-pill btn-danger order_delete"><i class="fe-plus"></i> Delete All</a></li>
                            <li><a href="{{route('admin.order.order_print')}}" class="btn rounded-pill btn-info multi_order_print"><i class="fe-printer"></i> Print</a></li>
                            @if($steadfast)
                            <li><a href="{{route('admin.bulk_courier', 'steadfast')}}?status=5" class="btn rounded-pill btn-info multi_order_courier"><i class="fe-truck"></i> Steadfast</a></li>
                            @endif
                            


                        </ul>
                    </div>
                    <div class="col-sm-4">
                        <form class="custom_form">
                            <div class="form-group">
                                <input type="text" name="keyword" placeholder="Search">
                                <button class="btn  rounded-pill btn-info">Search</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="table-responsive ">
                <table id="datatable-buttons" class="table table-striped   w-100">
                    <thead>
                        <tr>
                            <th style="width:2%"><div class="form-check"><label class="form-check-label"><input type="checkbox" class="form-check-input checkall" value=""></label>
                            <th style="width:2%">SL</th>
                                    </div></th>
                            <th style="width:8%">Action</th>
                            <th style="width:8%">Invoice</th>
                            <th style="width:10%">Date</th>
                            <th style="width:10%">Name</th>
                            <th style="width:10%">Type</th>
							<th style="width:10%">Order Note</th>
<th style="width:10%">Admin Note</th>

                            <th style="width:10%">Courier Rate</th>
                            <th style="width:10%">Amount</th>
                            <th style="width:10%">Status</th>
                             <th>Track</th>
							  <th>Fraud Check</th>
                        </tr>
                    </thead>
                
                
                    <tbody>
                        @foreach($show_data as $key=>$value)
                        <tr>
                            <td><input type="checkbox" class="checkbox" value="{{$value->id}}"></td>
                            <td>{{$loop->iteration}}</td>
                            <td>
                                <div class="button-list custom-btn-list">   
                                    <a href="{{route('admin.order.invoice',['invoice_id'=>$value->invoice_id])}}" title="Invoice"><i class="fe-eye"></i></a>
                                    <a href="{{route('admin.order.process',['invoice_id'=>$value->invoice_id])}}" title="Process"><i class="fe-settings"></i></a>
                                    <a href="{{route('admin.order.edit',['invoice_id'=>$value->invoice_id])}}" title="Edit"><i class="fe-edit"></i></a>
                                    <form method="post" action="{{route('admin.order.destroy')}}" class="d-inline">        
                                        @csrf
                                    <input type="hidden" value="{{$value->id}}" name="id">
                                    <button type="submit" title="Delete" class="delete-confirm"><i class="fe-trash-2"></i></button>

                                    
                                    </form>
                                </div>
                            </td>
                            <td>{{$value->invoice_id}}</td>
                            <td>{{date('d-m-Y', strtotime($value->updated_at))}}<br> {{date('h:i:s a', strtotime($value->updated_at))}}</td>
                            <td><strong>{{$value->shipping?$value->shipping->name:''}}</strong><p>{{$value->shipping?$value->shipping->address:''}}</p>{{$value->shipping?$value->shipping->phone:''}}</td>
                            <td>  @php
        // অর্ডারে থাকা সব প্রোডাক্ট সংগ্রহ করছি
        $items = $value->orderDetails;

        $types = [];

        foreach ($items as $item) {
            if ($item->product && $item->product->is_digital == 1) {
                $types[] = 'Digital';
            } else {
                $types[] = 'Physical';
            }
        }

        $types = array_unique($types);

        if (count($types) === 1) {
            echo $types[0]; // শুধু Digital বা শুধু Physical
        } else {
            echo "Mixed"; // দুইটাই থাকলে
        }
    @endphp</td>
							{{-- ⭐ Order Note (Client Note) --}}
<td>
    @php
        $orderNote = $value->order_note ?? $value->note ?? null;
    @endphp

    <button
        type="button"
        class="btn btn-sm btn-outline-info note-modal-btn"
        data-type="order"
        data-id="{{ $value->id }}"
        data-note="{{ $orderNote ?? '' }}"
    >
        {{ $orderNote ? 'View' : 'Add' }}
    </button>
</td>


<td>
    <button
        type="button"
        class="btn btn-sm btn-outline-warning note-modal-btn"
        data-type="admin"
        data-id="{{ $value->id }}"
        data-note="{{ $value->admin_note ?? '' }}"
    >
        {{ $value->admin_note ? 'View' : 'Add' }}
    </button>
</td>

							
                                                <td>
                                        Pathao: Success {{$value->pathao_success ?? 0}}, Cancel {{$value->pathao_cancel ?? 0}}, Rate {{$value->pathao_rate ?? 0}}% <br>
                                        RedX: Success {{$value->redx_success ?? 0}}, Cancel {{$value->redx_cancel ?? 0}}, Rate {{$value->redx_rate ?? 0}}% <br>
                                        Steadfast: Success {{$value->steadfast_success ?? 0}}, Cancel {{$value->steadfast_cancel ?? 0}}, Rate {{$value->steadfast_rate ?? 0}}%
                                    </td>
							
							<td>
    @php
        // এই অর্ডারের পেমেন্ট বের করছি
        $payment = \App\Models\Payment::where('order_id', $value->id)->first();

        $paid = $payment ? floatval($payment->amount) : 0;
        $total = floatval($value->amount);

        // ডিফল্ট ধরি: কোনো advance নাই / ফুল পেমেন্ট
        $showAmount = $total;

        // যদি payment amount > 0 এবং total থেকে কম হয় → মানে advance দিয়েছে
        if ($paid > 0 && $paid < $total) {
            $showAmount = $total - $paid;   // 👉 শুধু বাকি টাকা দেখাব
        }
    @endphp

    ৳{{ number_format($showAmount, 2) }}
</td>

                            <td>{{$value->status?$value->status->name:''}}</td>
                            
                            
                            
                            
                            
          <td>
    @if(!empty($value->consignment_id))
<a href="https://steadfast.com.bd/t/{{ $value->consignment_id }}"
   target="_blank"
   rel="noopener noreferrer"
   class="btn btn-sm btn-primary">
  ট্র্যাকিং
</a>

    @else
        <span class="text-muted">কোন রেকর্ড নেই</span>
    @endif
</td>
                            
                            
                            
                            
                            
							                               <td>
        <a href="javascript:void(0);" 
   class="btn btn-sm fraud-check"
   data-mobile="{{ $value->shipping ? $value->shipping->phone : '' }}"
   style="background:#fb8709; color:#fff; padding:5px 12px; border-radius:6px; font-size:13px;">
   চেকিং
</a>

                                    </td>
                            
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
                <div class="custom-paginate">
                    {{$show_data->links('pagination::bootstrap-4')}}
                </div>
            </div> <!-- end card body-->
           
        </div> <!-- end card -->
    </div><!-- end col-->
   </div>
   
</div>
<!-- Assign User End -->
<div class="modal fade" id="asignUser" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Assign User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{route('admin.order.assign')}}" id="order_assign">
      <div class="modal-body">
        <div class="form-group">
            <select name="user_id" id="user_id" class="form-control">
                <option value="">Select..</option>
                @foreach($users as $key=>$value)
                <option value="{{$value->id}}">{{$value->name}}</option>
                @endforeach
            </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success">Submit</button>
      </div>
      </form>
    </div>
  </div>
</div>
<!-- Assign User End-->

<!-- Assign User End -->
<div class="modal fade" id="changeStatus" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Assign User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{route('admin.order.status')}}" id="order_status_form">
      <div class="modal-body">
        <div class="form-group">
            <select name="order_status" id="order_status" class="form-control">
                <option value="">Select..</option>
                @foreach($orderstatus as $key=>$value)
                <option value="{{$value->id}}">{{$value->name}}</option>
                @endforeach
            </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success">Submit</button>
      </div>
      </form>
    </div>
  </div>
</div>
<!-- Assign User End-->
<!-- pathao coureir start -->
<div class="modal fade" id="pathao" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Pathao Courier</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{route('admin.order.pathao')}}" id="order_sendto_pathao">

      <div class="modal-body">
        <div class="form-group">
            <label for="pathaostore" class="form-label">Store</label>
           <select name="pathaostore" id="pathaostore" class="pathaostore form-control" >
             <option value="">Select Store...</option>
             @if(isset($pathaostore['data']['data']))
             @foreach($pathaostore['data']['data'] as $key=>$store)
             <option value="{{$store['store_id']}}">{{$store['store_name']}}</option>
             @endforeach
             @else
             @endif
           </select>
            @if ($errors->has('pathaostore'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('pathaostore') }}</strong>
              </span>
              @endif
        </div>
        <!-- form group end -->
        <div class="form-group mt-3">
          <label for="pathaocity" class="form-label">City</label>
           <select name="pathaocity" id="pathaocity" class="chosen-select pathaocity form-control" style="width:100%" >
             <option value="">Select City...</option>
             @if(isset($pathaocities['data']['data']))
             @foreach($pathaocities['data']['data'] as $key=>$city)
             <option value="{{$city['city_id']}}">{{$city['city_name']}}</option>
             @endforeach
             @else
             @endif
           </select>
            @if ($errors->has('pathaocity'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('pathaocity') }}</strong>
              </span>
              @endif
        </div>
        <!-- form group end -->
        <div class="form-group mt-3">
          <label for="" class="form-label">Zone</label>
             <select name="pathaozone" id="pathaozone" class="pathaozone chosen-select form-control  {{ $errors->has('pathaozone') ? ' is-invalid' : '' }}" value="{{ old('pathaozone') }}"  style="width:100%">
            </select>
             @if ($errors->has('pathaozone'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('pathaozone') }}</strong>
              </span>
              @endif
        </div>
        <!-- form group end -->
        <div class="form-group mt-3">
          <label for="" class="form-label">Area</label>
             <select name="pathaoarea" id="pathaoarea" class="pathaoarea chosen-select form-control  {{ $errors->has('pathaoarea') ? ' is-invalid' : '' }}" value="{{ old('pathaoarea') }}"  style="width:100%">
            </select>
             @if ($errors->has('pathaoarea'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('pathaoarea') }}</strong>
              </span>
              @endif
        </div>
        <!-- form group end -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success">Submit</button>
      </div>
      </form>
    </div>
  </div>
</div>
<!-- Note View/Edit Modal -->
<div class="modal fade" id="noteModal" tabindex="-1" aria-labelledby="noteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="noteModalLabel">Note</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        {{-- hidden ফিল্ড --}}
        <input type="hidden" id="note_order_id">
        <input type="hidden" id="note_type">

        <div class="form-group">
            <label id="note_label">Note</label>
            <textarea id="note_modal_text" class="form-control" rows="5"
                      placeholder="Write note here..."></textarea>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-success" id="saveNoteBtn">Save</button>
      </div>
    </div>
  </div>
</div>

<!-- pathao courier  End-->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // ⭐ Order Note / Admin Note popup open
    $(document).on('click', '.note-modal-btn', function (e) {
        e.preventDefault();

        let orderId = $(this).data('id');
        let type    = $(this).data('type');   // 'order' or 'admin'
        let note    = $(this).data('note') || '';

        $('#note_order_id').val(orderId);
        $('#note_type').val(type);
        $('#note_modal_text').val(note);

        // Modal title / label change
        if (type === 'admin') {
            $('#noteModalLabel').text('Admin Note');
            $('#note_label').text('Admin Note');
        } else {
            $('#noteModalLabel').text('Order Note (Customer)');
            $('#note_label').text('Order Note (Customer)');
        }

        $('#noteModal').modal('show');
    });

    // ⭐ Save Note (AJAX)
    $('#saveNoteBtn').on('click', function () {
        let orderId = $('#note_order_id').val();
        let type    = $('#note_type').val();
        let note    = $('#note_modal_text').val();

        $.ajax({
            url: "{{ route('admin.order.update_note') }}", // নিচে route বানাবো
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                order_id: orderId,
                note_type: type,   // 'order' / 'admin'
                note: note
            },
            success: function (res) {
                if (res.status === 'success') {
                    toastr.success('Note updated successfully');

                    // টেবিলের বাটনের টেক্সট ও data-note আপডেট
                    let selector = '.note-modal-btn[data-id="' + orderId + '"][data-type="' + type + '"]';
                    let $btn = $(selector);
                    $btn.data('note', note);
                    $btn.text(note ? 'View' : 'Add');

                    $('#noteModal').modal('hide');
                } else {
                    toastr.error(res.message || 'Update failed');
                }
            },
            error: function () {
                toastr.error('Something went wrong');
            }
        });
    });

$(document).ready(function(){
    $(".checkall").on('change',function(){
      $(".checkbox").prop('checked',$(this).is(":checked"));
    });
	
	
	// Fraud check
    $(document).on('click', '.fraud-check', function(e){
        e.preventDefault();
        var mobile = $(this).data('mobile');
        if(!mobile){ toastr.error('No mobile number found!'); return; }

        $.ajax({
            url: "{{ route('admin.fraud.check') }}",
            type: "POST",
            data: { mobile: mobile, _token: "{{ csrf_token() }}" },
            success: function(res){
                if(res.status === 'success'){
                    let data = res.data;
                    let msg = `
                        <b>Pathao</b>: Success ${data.pathao.success}, Cancel ${data.pathao.cancel}, Rate ${data.pathao.rate}%<br>
                        <b>RedX</b>: Success ${data.redx.success}, Cancel ${data.redx.cancel}, Rate ${data.redx.rate}%<br>
                        <b>Steadfast</b>: Success ${data.steadfast.success}, Cancel ${data.steadfast.cancel}, Rate ${data.steadfast.rate}%
                    `;
                    toastr.info(msg, 'Fraud Check Result', {timeOut: 8000});
                } else {
                    toastr.warning(res.message, 'Fraud Check Failed');
                }
            },
            error: function(xhr){ toastr.error('Something went wrong!'); }
        });
    });


    // order assign
    $(document).on('submit', 'form#order_assign', function(e){
        e.preventDefault();
        var url = $(this).attr('action');
        var method = $(this).attr('method');
        let user_id=$(document).find('select#user_id').val();
    
        var order = $('input.checkbox:checked').map(function(){
          return $(this).val();
        });
        var order_ids=order.get();
        
        if(order_ids.length ==0){
            toastr.error('Please Select An Order First !');
            return ;
        }
        
        $.ajax({
           type:'GET',
           url:url,
           data:{user_id,order_ids},
           success:function(res){
               if(res.status=='success'){
                toastr.success(res.message);
                window.location.reload();
                
            }else{
                toastr.error('Failed something wrong');
            }
           }
        });
    
    });

    // order status change 
    $(document).on('submit', 'form#order_status_form', function(e){
        e.preventDefault();
        var url = $(this).attr('action');
        var method = $(this).attr('method');
        let order_status=$(document).find('select#order_status').val();
    
        var order = $('input.checkbox:checked').map(function(){
          return $(this).val();
        });
        var order_ids=order.get();
        
        if(order_ids.length ==0){
            toastr.error('Please Select An Order First !');
            return ;
        }
        
        $.ajax({
           type:'GET',
           url:url,
           data:{order_status,order_ids},
           success:function(res){
               if(res.status=='success'){
                toastr.success(res.message);
                window.location.reload();
                
            }else{
                toastr.error('Failed something wrong');
            }
           }
        });
    
    });
    // order delete
    $(document).on('click', '.order_delete', function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        var order = $('input.checkbox:checked').map(function(){
          return $(this).val();
        });
        var order_ids=order.get();
        
        if(order_ids.length ==0){
            toastr.error('Please Select An Order First !');
            return ;
        }
        
        $.ajax({
           type:'GET',
           url:url,
           data:{order_ids},
           success:function(res){
               if(res.status=='success'){
                toastr.success(res.message);
                window.location.reload();
                
            }else{
                toastr.error('Failed something wrong');
            }
           }
        });
    
    });
    
    // multiple print
    $(document).on('click', '.multi_order_print', function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        var order = $('input.checkbox:checked').map(function(){
          return $(this).val();
        });
        var order_ids=order.get();
        
        if(order_ids.length ==0){
            toastr.error('Please Select Atleast One Order!');
            return ;
        }
        $.ajax({
           type:'GET',
           url,
           data:{order_ids},
           success:function(res){
               if(res.status=='success'){
                   console.log(res.items, res.info);                          
                   var myWindow = window.open("", "_blank");                   
                   myWindow.document.write(res.view);
            }else{
                toastr.error('Failed something wrong');
            }
           }
        });
    });
    // multiple courier
    $(document).on('click', '.multi_order_courier', function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        console.log(url);
        var order = $('input.checkbox:checked').map(function(){
          return $(this).val();
        });
        var order_ids=order.get();
        
        if(order_ids.length ==0){
            toastr.error('Please Select An Order First !');
            return ;
        }
        
        $.ajax({
           type:'GET',
           url:url,
           data:{order_ids},
           success:function(res){
               if(res.status=='success'){
                   console.log(res.message);
                   console.log(res.success);
                   console.log(res.failed);
                    toastr.success(res.message);
                    toastr.success(res.success);
                    toastr.error(res.failed);
                    window.location.reload();
                
            }else{
                console.log(res.message);
                toastr.error('Failed something wrong');
            }
           }
        });
    
    });
})
</script>
@endsection