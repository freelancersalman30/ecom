@extends('backEnd.layouts.master')
@section('title','Send Custom SMS')

@section('content')
<div class="container-fluid mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">📩 Send Custom SMS</h5>
        </div>

        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form action="{{ route('admin.sms.custom.send') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Phone Number (বাংলাদেশ ফরম্যাটে)</label>
                    <input type="text" name="phone" class="form-control" placeholder="017xxxxxxxx" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Message</label>
                    <textarea name="message" rows="4" class="form-control" placeholder="Write your custom message..." required></textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-paper-plane"></i> Send SMS
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
