@extends('masterweb::template.admin.layout')
@section('title')
  Petugas Management
@endsection

@section('content')
  <div class="card">
    <div class="card-body">
      <h4 class="card-title">Petugas Management</h4>
      <p class="card-description">
        Tambah data
      </p>
      <form enctype="multipart/form-data" class="forms-sample" action="{{ route("adm-petugas-store") }}" method="POST">
        @csrf

        <div class="form-group">
          <label for="nama">Nama Lengkap</label>
          <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama lengkap" value="{{ old('nama') }}" required>
          @error('nama')
          <div class="error text-danger mt-1 text-small">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-group">
          <label for="nik">NIK</label>
          <input type="text" class="form-control" id="nik" name="nik" placeholder="NIK" value="{{ old('nik') }}" required>
          @error('nik')
          <div class="error text-danger mt-1 text-small">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-group">
          <label for="password">Password BSRE</label>
          <input type="text" class="form-control" id="password" name="password" placeholder="Password" value="{{ old('password') }}" required>
          @error('password')
          <div class="error text-danger mt-1 text-small">{{ $message }}</div>
          @enderror
        </div>


        <button type="submit" class="btn btn-primary mr-2">Simpan</button>
        <button type="button" class="btn btn-light" onclick="window.location.href='{{ route("adm-petugas") }}'">Kembali</button>
      </form>
    </div>
  </div>
@endsection
