@extends('backEnd.layouts.master')
@section('title','Fraud API Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('settings.index') }}" class="btn btn-secondary btn-sm">← Back</a>
                </div>
                <h4 class="page-title">🛡️ Fraud API Settings</h4>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card border border-danger rounded shadow-sm">
                <div class="card-header bg-light border-bottom border-danger d-flex align-items-center">
                    <img src="https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEiaLGIwvLMp5DEpwqRb4hyphenhyphenw-EQlGQNCkcHxFY-H0NbTIE_SqaBkTkwSv3MbrW1aQSliwE4Y1HM8UcLjsLhFfeasIlQvExvML1jqhCe6WechQ-YKQjTtMmAcVBLilbkQQFe03vF7-logzZxrPcUE52VVHHI090sedjV7IyJALrw3m04AoxitdTS0LgujNWel/s16000/logo.png" alt="Fraud Icon" width="35" class="me-2">
                    <h5 class="mb-0 text-danger fw-semibold">Zachaikori API Configuration</h5>
                </div>

                <div class="card-body">
                    <form action="{{ route('admin.fraud.update') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="fraud_api_key" class="form-label fw-semibold">
                                🔑 Fraud API Key <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="fraud_api_key"
                                   id="fraud_api_key"
                                   class="form-control"
                                   placeholder="Enter Fraud API Key"
                                   value="{{ old('fraud_api_key', $data->fraud_api_key ?? '') }}"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="fraud_secret_key" class="form-label fw-semibold">
                                🔒 Fraud Secret Key <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="fraud_secret_key"
                                   id="fraud_secret_key"
                                   class="form-control"
                                   placeholder="Enter Fraud Secret Key"
                                   value="{{ old('fraud_secret_key', $data->fraud_secret_key ?? '') }}"
                                   required>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-danger w-100 fw-bold py-2">
                                💾 Update Fraud API Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
