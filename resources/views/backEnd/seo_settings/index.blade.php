@extends('backEnd.layouts.master')
@section('title','SEO Settings')

@section('content')
<div class="container-fluid mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                {{-- আপনি চেয়েছিলেন header-এ "Email Configuration" image হলো, এখানে "SEO Configuration" --}}
                <img src="https://blogger.googleusercontent.com/img/a/AVvXsEiD4KWbeS0TmkD8ViCKL7qJo69_R3QJsQmKyegmEbSR8SgNPobOSNs5YMD_aGAT4u8gLrVRArU_LoSKlH_bvNWFJ4ShjWKS_3Ljg09Mr8sg2gEdc-lPqNO_7qLC-aIao1MMTb8OAVWxgvu6FL1DXNSC_9q8bqyhKzgXzYXXIjlsowybTqjbOC3kFO5ZfoOl" alt="SEO" style="width:40px;height:40px;margin-right:10px;">
               <h5 class="mb-0 text-white">SEO Configuration</h5>

            </div>
            <small class="text-white-50">Manage meta tags & verification</small>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('admin.seo_settings.update') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Meta Title</label>
                        <input type="text" name="meta_title" class="form-control @error('meta_title') is-invalid @enderror"
                               value="{{ old('meta_title', $seo->meta_title ?? '') }}" maxlength="255">
                        @error('meta_title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Meta Tags (comma separated)</label>
                        <input type="text" name="meta_tags" class="form-control @error('meta_tags') is-invalid @enderror"
                               value="{{ old('meta_tags', $seo->meta_tags ?? '') }}" maxlength="255">
                        @error('meta_tags') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">Meta Description</label>
                        <textarea name="meta_description" rows="4" class="form-control @error('meta_description') is-invalid @enderror">{{ old('meta_description', $seo->meta_description ?? '') }}</textarea>
                        @error('meta_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Search Console Verification</label>
                        <input type="text" name="search_console_verification" class="form-control @error('search_console_verification') is-invalid @enderror"
                               value="{{ old('search_console_verification', $seo->search_console_verification ?? '') }}" maxlength="255" placeholder="google-site-verification=xxxxx">
                        @error('search_console_verification') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 d-flex align-items-end">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fa fa-save"></i> Save SEO Settings
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
