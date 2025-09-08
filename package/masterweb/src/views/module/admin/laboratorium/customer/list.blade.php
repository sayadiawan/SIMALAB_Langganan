@extends('masterweb::template.admin.layout')

@section('title')
  Customer Management
@endsection

@section('content')

  <script>
    var role = "admin"
  </script>

  <script src="https://www.gstatic.com/firebasejs/3.2.1/firebase.js"></script>
  <script src="{{ asset('assets/admin/js/firebase-js/firebase/config.js') }}"></script>
  <script src="{{ asset('assets/admin/js/firebase-js/firebase/database.js') }}"></script>


  <div class="row">
    <div class="col-12 grid-margin stretch-card">
      <div class="card">
        <div class="">
          <div class="template-demo">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/home') }}"><i class="fa fa-home menu-icon mr-1"></i>
                    Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ url('/elits-rates') }}">Laboraturium</a></li>
                <li class="breadcrumb-item active" aria-current="page"><span>Customer Management</span></li>
              </ol>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="card">


    <div class="card-body">
      <div class="d-flex">
        <div class="mr-auto p-2">
          {{-- <div id="datepicker-popup" class="input-group date datepicker">
                <input type="text" class="form-control">
                <span class="input-group-addon input-group-append border-left">
                    <span class="far fa-calendar input-group-text"></span>
                </span>
            </div> --}}
        </div>

        <div class="p-2">
          <a href="{{ route('elits-customers.create') }}">

            <button type="button" class="btn btn-info btn-icon-text">
              Tambah Data
              <i class="fa fa-plus btn-icon-append"></i>
            </button>
          </a>
        </div>


      </div>

      <div class="row">

        @if (session('status'))
          <div class="alert alert-success">
            {{ session('status') }}

          </div>
        @endif
        <div class="col-12">
          <div class="table-responsive">
            <table id="order-listing" class="table">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama</th>
                  <th>Alamat</th>
                  <th>Email</th>
                  <th>Cp</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @php
                  $no = 1;
                @endphp
                @foreach ($customers as $customer)
                  <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $customer->name_customer }}</td>
                    <td>{{ $customer->address_customer }}</td>
                    <td>{{ $customer->email_customer }}</td>
                    <td>{{ $customer->cp_customer }}</td>
                    <td>

                      <a href="{{ route('elits-customers.edit', [$customer->id_customer]) }}">
                        <button type="button" class="btn btn-outline-success btn-rounded btn-icon">
                          <i class="fas fa-pencil-alt"></i>
                        </button>
                      </a>
                      <form onsubmit="return confirm('Delete this user permanently?')" class="d-inline"
                        action="{{ route('elits-customers.destroy', [$customer->id_customer]) }}" method="POST">

                        @csrf

                        <input type="hidden" name="_method" value="DELETE">

                        <button type="submit" class="btn btn-outline-danger btn-rounded btn-icon">
                          <i class="fas fa-trash"></i>
                        </button>
                      </form>
                    </td>

                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
