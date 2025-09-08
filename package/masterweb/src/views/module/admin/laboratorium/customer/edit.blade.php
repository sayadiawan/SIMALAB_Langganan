@extends('masterweb::template.admin.layout')
@section('title')
Customer Management
@endsection

@section('content')
<div class="row">
  <div class="col-12 grid-margin stretch-card">
    <div class="card">
      <div class="">
        <div class="template-demo">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ url('/home') }}"><i class="fa fa-home menu-icon mr-1"></i>
                  Beranda</a></li>
              <li class="breadcrumb-item"><a href="{{ url('/elits-customers') }}">Customer Management</a>
              </li>
              <li class="breadcrumb-item active" aria-current="page"><span>edit</span></li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="card">
  <div class="card-body">
    <h4 class="card-title">Customer Management</h4>

    <form enctype="multipart/form-data" class="forms-sample" action="{{ route('elits-customers.update', [$id]) }}" method="POST">
      @csrf
      <input type="hidden" value="PUT" name="_method">

      <div class="form-group">

        <label for="name_customer">Nama</label>
        <input type="text" class="form-control" id="name_customer" name="name_customer" value="{{ $customer->name_customer }}" placeholder="Isikan Nama" required>
      </div>

      <div class="form-group">

        <label for="address_customer">Alamat</label>
        <textarea class="form-control" id="address_customer" name="address_customer" placeholder="Isikan Alamat" required>{{ $customer->address_customer }}</textarea>
      </div>


      <div class="form-group">
        <label for="kecamatan">Kecamatan</label>
        @php
        $kecamatan = [
        'Andong',
        'Ampel',
        'Banyudono',
        'Boyolali',
        'Cepogo',
        'Gladagsari',
        'Juwangi',
        'Karanggede',
        'Kemusu',
        'Klego',
        'Mojosongo',
        'Musuk',
        'Ngemplak',
        'Nogosari',
        'Sambi',
        'Sawit',
        'Selo',
        'Simo',
        'Tamansari',
        'Teras',
        'Wonosegoro',
        'Wonosamodro',
        ];
        @endphp
        <select name="kecamatan" class="form-control" id="kecamatan" onchange="CheckKecamatan(this)">
          <option value="" selected disabled>Pilih Kecamatan</option>
          <option value="Andong" {{ $customer->kecamatan_customer == 'Andong' ? 'selected' : '' }}>
            Andong
          </option>
          <option value="Ampel" {{ $customer->kecamatan_customer == 'Ampel' ? 'selected' : '' }}>
            Ampel
          </option>
          <option value="Banyudono" {{ $customer->kecamatan_customer == 'Banyudono' ? 'selected' : '' }}>
            Banyudono
          </option>
          <option value="Boyolali" {{ $customer->kecamatan_customer == 'Boyolali' ? 'selected' : '' }}>
            Boyolali
          </option>
          <option value="Cepogo" {{ $customer->kecamatan_customer == 'Cepogo' ? 'selected' : '' }}>
            Cepogo
          </option>
          <option value="Gladagsari" {{ $customer->kecamatan_customer == 'Gladagsari' ? 'selected' : '' }}>
            Gladagsari
          </option>
          <option value="Juwangi" {{ $customer->kecamatan_customer == 'Juwangi' ? 'selected' : '' }}>
            Juwangi
          </option>
          <option value="Karanggede" {{ $customer->kecamatan_customer == 'Karanggede' ? 'selected' : '' }}>
            Karanggede
          </option>
          <option value="Kemusu" {{ $customer->kecamatan_customer == 'Kemusu' ? 'selected' : '' }}>
            Kemusu
          </option>
          <option value="Klego" {{ $customer->kecamatan_customer == 'Klego' ? 'selected' : '' }}>
            Klego
          </option>
          <option value="Mojosongo" {{ $customer->kecamatan_customer == 'Mojosongo' ? 'selected' : '' }}>
            Mojosongo
          </option>
          <option value="Musuk" {{ $customer->kecamatan_customer == 'Musuk' ? 'selected' : '' }}>
            Musuk
          </option>
          <option value="Ngemplak" {{ $customer->kecamatan_customer == 'Ngemplak' ? 'selected' : '' }}>
            Ngemplak
          </option>
          <option value="Nogosari" {{ $customer->kecamatan_customer == 'Nogosari' ? 'selected' : '' }}>
            Nogosari
          </option>
          <option value="Sambi" {{ $customer->kecamatan_customer == 'Sambi' ? 'selected' : '' }}>
            Sambi
          </option>
          <option value="Sawit" {{ $customer->kecamatan_customer == 'Sawit' ? 'selected' : '' }}>
            Sawit
          </option>
          <option value="Selo" {{ $customer->kecamatan_customer == 'Selo' ? 'selected' : '' }}>
            Selo
          </option>
          <option value="Simo" {{ $customer->kecamatan_customer == 'Simo' ? 'selected' : '' }}>
            Simo
          </option>
          <option value="Tamansari" {{ $customer->kecamatan_customer == 'Tamansari' ? 'selected' : '' }}>
            Tamansari
          </option>
          <option value="Teras" {{ $customer->kecamatan_customer == 'Teras' ? 'selected' : '' }}>
            Teras
          </option>
          <option value="Wonosegoro" {{ $customer->kecamatan_customer == 'Wonosegoro' ? 'selected' : '' }}>
            Wonosegoro
          </option>
          <option value="Wonosamodro" {{ $customer->kecamatan_customer == 'Wonosamodro' ? 'selected' : '' }}>
            Wonosamodro
          </option>
          <option value="0" {{ !in_array($customer->kecamatan_customer, $kecamatan) ? 'selected' : '' }}>Lainnya
          </option>
        </select>
      </div>

      <div class="form-group">
        <input type="text" class="form-control mt-10" id="kecamatan_other" style="{{ !in_array($customer->kecamatan_customer, $kecamatan) ? '' : 'display:none;' }}" value="{{ !in_array($customer->kecamatan_customer, $kecamatan) ? $customer->kecamatan_customer : '' }}" id="kecamatan_other" name="kecamatan_other" placeholder="Isikan Kecamatan Lain">
      </div>

      <div class="form-group">
        <label for="email_customer">Email</label>
        <input type="text" class="form-control" id="email_customer" name="email_customer" value="{{ $customer->email_customer }}" placeholder="Isikan Email" required>
      </div>


      <div class="form-group">
        <label for="cp_customer">Contact Person</label>
        <textarea class="form-control" id="cp_customer" name="cp_customer" placeholder="Isikan Contact Person">{{ $customer->cp_customer }}</textarea>
      </div>


      <button type="submit" class="btn btn-primary mr-2">Simpan</button>
      <button onclick="goBack()" class="btn btn-light">Kembali</button>
    </form>
  </div>
</div>



<script>
  $('#kecamatan').select2({
    allowclear: true,
    placeholder: 'Pilih Kecamatan'
  });

  function goBack() {
    window.history.back();
  }
</script>
@endsection
