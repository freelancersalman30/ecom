@extends('backEnd.layouts.master')
@section('title','Suppliers')

@section('content')
<div class="container-fluid">



    <div class="row">

        {{-- Add / Edit Supplier --}}
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <strong>
                        @isset($supplier)
                            Edit Supplier
                        @else
                            + Add Supplier
                        @endisset
                    </strong>
                </div>
                <div class="card-body">

                    <form
                        action="@isset($supplier)
                                    {{ route('suppliers.update', $supplier->id) }}
                                @else
                                    {{ route('suppliers.store') }}
                                @endisset"
                        method="POST"
                    >
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Name *</label>
                            <input type="text"
                                   name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $supplier->name ?? '') }}"
                                   required>
                            @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text"
                                   name="phone"
                                   class="form-control"
                                   value="{{ old('phone', $supplier->phone ?? '') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email"
                                   name="email"
                                   class="form-control"
                                   value="{{ old('email', $supplier->email ?? '') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="address"
                                      class="form-control"
                                      rows="2">{{ old('address', $supplier->address ?? '') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-success">
                            @isset($supplier)
                                Update Supplier
                            @else
                                Save Supplier
                            @endisset
                        </button>

                        @isset($supplier)
                            <a href="{{ route('suppliers.index') }}" class="btn btn-light ms-2">
                                Cancel
                            </a>
                        @endisset
                    </form>

                </div>
            </div>
        </div>

        {{-- Supplier List --}}
        <div class="col-md-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <strong>Supplier List</strong>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Current Due</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($suppliers as $s)
                            <tr>
                                <td>{{ $loop->iteration + ($suppliers->currentPage()-1)*$suppliers->perPage() }}</td>
                                <td>{{ $s->name }}</td>
                                <td>{{ $s->phone }}</td>
                                <td>{{ $s->email }}</td>
                                <td>{{ number_format($s->current_due,2) }} ৳</td>
                                <td>
                                    <a href="{{ route('suppliers.edit', $s->id) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    কোনো supplier পাওয়া যায়নি।
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>

                    <div class="mt-3">
                        {{ $suppliers->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
