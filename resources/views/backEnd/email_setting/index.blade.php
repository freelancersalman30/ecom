@extends('backEnd.layouts.master')
@section('title','Email Settings')

@section('content')
<div class="container-fluid mt-4">
    <div class="card shadow-sm border-0">
<div class="card-header bg-dark d-flex align-items-center" style="color: #fff !important;">
    <i class="fa fa-envelope me-2" style="color: #fff !important;"></i>
    <h5 class="mb-0" style="color: #fff !important;">Email Configuration</h5>
</div>


        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('email_setting.update') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mailer *</label>
                        <input type="text" name="MAIL_MAILER" class="form-control"
                               value="{{ $mail['MAIL_MAILER'] ?? '' }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Host *</label>
                        <input type="text" name="MAIL_HOST" class="form-control"
                               value="{{ $mail['MAIL_HOST'] ?? '' }}" required>
                    </div>

                    <!-- 🔌 Port Dropdown -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Port *</label>
                        <select name="MAIL_PORT" class="form-control" required>
                            <option value="465" {{ ($mail['MAIL_PORT'] ?? '') == '465' ? 'selected' : '' }}>465</option>
                            <option value="587" {{ ($mail['MAIL_PORT'] ?? '') == '587' ? 'selected' : '' }}>587</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Username *</label>
                        <input type="text" name="MAIL_USERNAME" class="form-control"
                               value="{{ $mail['MAIL_USERNAME'] ?? '' }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password *</label>
                        <input type="text" name="MAIL_PASSWORD" class="form-control"
                               value="{{ $mail['MAIL_PASSWORD'] ?? '' }}" required>
                    </div>

                    <!-- 🔒 Encryption Dropdown -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Encryption *</label>
                        <select name="MAIL_ENCRYPTION" class="form-control" required>
                            <option value="ssl" {{ ($mail['MAIL_ENCRYPTION'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                            <option value="tls" {{ ($mail['MAIL_ENCRYPTION'] ?? '') == 'tls' ? 'selected' : '' }}>TLS</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">From Address *</label>
                        <input type="email" name="MAIL_FROM_ADDRESS" class="form-control"
                               value="{{ $mail['MAIL_FROM_ADDRESS'] ?? '' }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">From Name *</label>
                        <input type="text" name="MAIL_FROM_NAME" class="form-control"
                               value="{{ $mail['MAIL_FROM_NAME'] ?? '' }}" required>
                    </div>
                </div>

                <div class="mt-3 text-end">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fa fa-save"></i> Update Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
