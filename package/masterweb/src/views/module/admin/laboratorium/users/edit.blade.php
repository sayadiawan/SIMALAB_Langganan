@extends('masterweb::template.admin.layout')
@section('title')
    User Management
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
        <h4 class="card-title">User Management</h4>
        <p class="card-description">
            edit data
        </p>
        <form enctype="multipart/form-data" class="forms-sample" action="{{route('elits-users.update', [$id])}}" method="POST">
            @csrf
            <input type="hidden" value="PUT" name="_method">
            <div class="form-group">
                <label for="name">Laboratorium</label>
                <select name="laboratory_users" id="laboratory_users" class="form-control">
                    <option value="">tidak memiliki laboratorium</option>
                    @foreach ($laboratories as $laboratory)
                        <option value="{{$laboratory->id_laboratorium}}" {{isSelected($laboratory->id_laboratorium,$users->laboratory_users)}}>{{$laboratory->nama_laboratorium}}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Nama lengkap" value="{{$users->name}}">
            </div>

            <div class="form-group">
                <label for="nip_users">NIP</label>
                <input type="text" class="form-control" id="nip_users" name="nip_users" value="{{$users->nip_users}}" placeholder="NIP" >
            </div>

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Username" value="{{$users->username}}">
            </div>

            <div class="form-group">
                <label for="email">Alamat email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="{{$users->email}}">
            </div>

            <div class="form-group">
                <label for="email">Hak Akses</label>
                <select name="level" id="level" class="form-control">
                    @foreach ($privileges as $privilege)
                        <option value="{{$privilege->id}}" {{($users->level==$privilege->id) ? "selected" : NULL}}>{{$privilege->name}}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="email">Photo</label>
                <br>
                <img src="{{asset('storage/photo/'.$users->photo)}}" alt="photo" width="5%">
                <input type="file" name="photo" id="photo" class="form-control">
                <span>*kosongkan gambar jika tidak ingin diubah</span>
            </div>

            <button type="submit" class="btn btn-primary mr-2">Simpan</button>
            <button class="btn btn-light" onclick="goBack()">Kembali</button>
        </form>
        </div>
    </div>

    <script>
    function goBack() {
        window.history.back();
    }
    </script>
@endsection