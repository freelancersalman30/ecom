@extends('backEnd.layouts.master')
@section('title', 'Sitemap Generator')

@section('content')
<div class="container-fluid mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">🗺 Sitemap Generator</h5>
            <a href="{{ url('sitemap.xml') }}" target="_blank" class="btn btn-light btn-sm">View Sitemap</a>
        </div>

        <div class="card-body text-center">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <p class="mb-3 text-muted">
                Click the button below to generate or update your <strong>sitemap.xml</strong> file.
            </p>

            <form action="{{ route('admin.sitemap.generate') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary btn-lg px-4">
                    <i class="fa fa-sync-alt"></i> Generate Sitemap
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Auto update প্রতি ১ ঘণ্টায় --}}
<script>
setInterval(() => {
    fetch('{{ route('admin.sitemap.generate') }}', { 
        method: 'POST', 
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } 
    })
    .then(() => console.log("🕒 Sitemap auto-updated!"));
}, 3600000); // প্রতি ১ ঘণ্টা = 1000*60*60
</script>
@endsection
