{{-- @dd($verificationActivity) --}}

<table class="table table-bordered">
    <thead>
        <tr>
            <th scope="col" class="border border-primary">Jenis Kegiatan Lab Kesmas</th>
            <th scope="col" class="border border-primary">Tanggal Mulai / Jam</th>
            <th scope="col" class="border border-primary">Tanggal Selesai / Jam</th>
            <th scope="col" class="border border-primary">Nama Petugas</th>
            <th scope="col" class="text-center border border-primary">Action</th>
        </tr>
    </thead>
    <tbody>
        <tr id="registrasi">
            <th scope="row">Pendaftaran / Registrasi</th>
            @if (isset($listVerifications[1]))
                <td>{{ $listVerifications[1]->start_date }}</td>
                <td>{{ $listVerifications[1]->stop_date }}</td>
                <td>{{ $listVerifications[1]->nama_petugas }}</td>
                <td class="text-center">
                    <svg id="toggle-registrasi" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="48"
                        height="48" viewBox="0 0 48 48" style="cursor: pointer">
                        <path fill="#c8e6c9"
                            d="M44,24c0,11.045-8.955,20-20,20S4,35.045,4,24S12.955,4,24,4S44,12.955,44,24z">
                        </path>
                        <path fill="#4caf50"
                            d="M34.586,14.586l-13.57,13.586l-5.602-5.586l-2.828,2.828l8.434,8.414l16.395-16.414L34.586,14.586z">
                        </path>
                    </svg>
                </td>
            @else
                <form action="{{ route('elits-samples.verification-analytic-2', [$sample->id_samples]) }}"
                    method="post" class="formPendaftaran">
                    @csrf
                    <input type="number" value="{{ 1 }}" name="verification_step" hidden>
                    <td><input type="datetime-local" class="form-control" name="start_date" required></td>
                    <td><input type="datetime-local" class="form-control" name="stop_date" required></td>
                    <td>
                        <select name="nama_petugas" id="namaPetugasPendaftaran" required>
                            @php
                                $list_name_petugas = explode(', ', $verificationActivity[0]->register);

                            @endphp
                            @foreach ($list_name_petugas as $nama_petugas)
                                <option value="{{ $nama_petugas }}">{{ $nama_petugas }}
                                </option>
                            @endforeach

                        </select>


                    <td class="text-center"><button type="submit" class="btn btn-success" onclick="checkNikAndPassword(document.getElementById('namaPetugasPendaftaran').value, 'formPendaftaran')">Verifikasi</button></td>
                </form>
            @endif
        </tr>
        <tr id="registrasi-update" style="display: none">
            <th scope="row">Pendaftaran / Registrasi</th>
            <form action="{{ route('elits-samples.verification-analytic-2', [$sample->id_samples]) }}" method="post" class="formPendaftaranUpdate">
                @csrf
                <input type="number" value="{{ 1 }}" name="verification_step" hidden>
                <td><input type="datetime-local" class="form-control" name="start_date" id="start_date_registrasi"
                        value="{{ isset($listVerifications[1]->start_date) ? $listVerifications[1]->start_date : '' }}"
                        required></td>
                <td><input type="datetime-local" class="form-control" name="stop_date" id="stop_date_registrasi"
                        value="{{ isset($listVerifications[1]->stop_date) ? $listVerifications[1]->stop_date : '' }}"
                        required></td>
                <td>
                    <select name="nama_petugas" id="namaPetugasPendaftaranUpdate" required>
                        @php
                            $list_name_petugas = explode(', ', $verificationActivity[0]->register);

                        @endphp
                        @foreach ($list_name_petugas as $nama_petugas)
                            <option value="{{ $nama_petugas }}">{{ $nama_petugas }}
                            </option>
                        @endforeach

                    </select>


                <td class="text-center"><button type="submit" class="btn btn-success" onclick="checkNikAndPassword(document.getElementById('namaPetugasPendaftaranUpdate').value, 'formPendaftaranUpdate')">Verifikasi</button></td>
            </form>
        </tr>

        <tr id="analitik">
            <th scope="row">Pemeriksaan / Analitik</th>
            @if (isset($listVerifications[2]))
                <td>{{ $listVerifications[2]->start_date }}</td>
                <td>{{ $listVerifications[2]->stop_date }}</td>
                <td>{{ $listVerifications[2]->nama_petugas }}</td>
                <td class="text-center">
                    <svg id="toggle-analitik" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="48"
                        height="48" viewBox="0 0 48 48" style="cursor: pointer">
                        <path fill="#c8e6c9"
                            d="M44,24c0,11.045-8.955,20-20,20S4,35.045,4,24S12.955,4,24,4S44,12.955,44,24z">
                        </path>
                        <path fill="#4caf50"
                            d="M34.586,14.586l-13.57,13.586l-5.602-5.586l-2.828,2.828l8.434,8.414l16.395-16.414L34.586,14.586z">
                        </path>
                    </svg>
                </td>
            @else
                <form action="{{ route('elits-samples.verification-analytic-2', [$sample->id_samples]) }}"
                    method="post" class="formPemeriksaan">
                    @csrf
                    <input type="number" value="{{ 2 }}" name="verification_step" hidden>
                    <td><input type="datetime-local" class="form-control" name="start_date" id="start_date_pemeriksaan"
                            required>
                    </td>
                    <td><input type="datetime-local" class="form-control" name="stop_date" id="stop_date_pemeriksaan"
                            required></td>
                    <td>
                        <select name="nama_petugas" id="namaPetugasPemeriksaan" required>
                            @php
                                if ($sample->kode_laboratorium == 'MBI') {
                                    # code...
                                    $list_name_petugas = explode(', ', $verificationActivity[1]->mikro);
                                } elseif ($sample->kode_laboratorium == 'KIM') {
                                    # code...
                                    $list_name_petugas = explode(', ', $verificationActivity[1]->kimia);
                                } else {
                                    $list_name_petugas = explode(', ', $verificationActivity[1]->klnik);
                                }
                            @endphp
                            @foreach ($list_name_petugas as $nama_petugas)
                                <option value="{{ $nama_petugas }}">{{ $nama_petugas }}
                                </option>
                            @endforeach

                        </select>
                        {{-- <input type="text" class="form-control" placeholder="Nama Petugas" value="{{ Auth()->user()->name }}"
            name="nama_petugas" required> --}}
                    </td>
                    <td class="text-center"><button type="submit" class="btn btn-success"
                            @if (!isset($listVerifications[1])) disabled @endif onclick="checkNikAndPassword(document.getElementById('namaPetugasPemeriksaan').value, 'formPemeriksaan')">Verifikasi</button>
                    </td>
                </form>
            @endif
        </tr>
        <tr id="analitik-update" style="display:none;">
            <th scope="row">Pemeriksaan / Analitik</th>
            <form action="{{ route('elits-samples.verification-analytic-2', [$sample->id_samples]) }}" method="post" class="formPemeriksaanUpdate">
                @csrf
                <input type="number" value="{{ 2 }}" name="verification_step" hidden>
                <td><input type="datetime-local" class="form-control" name="start_date"
                        value="{{ isset($listVerifications[2]->start_date) ? $listVerifications[2]->start_date : '' }}"
                        required>
                </td>
                <td><input type="datetime-local" class="form-control" name="stop_date"
                        value="{{ isset($listVerifications[2]->stop_date) ? $listVerifications[2]->stop_date : '' }}"
                        required></td>
                <td>
                    <select name="nama_petugas" id="namaPetugasPemeriksaanUpdate" required>
                        @php
                            if ($sample->kode_laboratorium == 'MBI') {
                                $list_name_petugas = explode(', ', $verificationActivity[1]->mikro);
                            } elseif ($sample->kode_laboratorium == 'KIM') {
                                $list_name_petugas = explode(', ', $verificationActivity[1]->kimia);
                            } else {
                                $list_name_petugas = explode(', ', $verificationActivity[1]->klnik);
                            }
                        @endphp
                        @foreach ($list_name_petugas as $nama_petugas)
                            <option value="{{ $nama_petugas }}"
                                {{ isset($listVerifications[2]->nama_petugas) && $listVerifications[2]->nama_petugas == $nama_petugas
                                    ? 'selected'
                                    : '' }}>
                                {{ $nama_petugas }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td class="text-center">
                    <button type="submit" class="btn btn-success"
                        @if (!isset($listVerifications[1])) disabled @endif onclick="checkNikAndPassword(document.getElementById('namaPetugasPemeriksaanUpdate').value, 'formPemeriksaanUpdate')">Verifikasi</button>
            </form>
            </td>
        </tr>

        <tr id="baca-hasil">
            <th scope="row">Input / Output Hasil Px</th>
            @if (isset($listVerifications[3]))
                <td>{{ $listVerifications[3]->start_date }}</td>
                <td>{{ $listVerifications[3]->stop_date }}</td>
                <td>{{ $listVerifications[3]->nama_petugas }}</td>
                <td class="text-center">
                    <svg id="toggle-baca-hasil" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="48"
                        height="48" viewBox="0 0 48 48" style="cursor: pointer">
                        <title>Edit Baca Hasil</title>
                        <path fill="#c8e6c9"
                            d="M44,24c0,11.045-8.955,20-20,20S4,35.045,4,24S12.955,4,24,4S44,12.955,44,24z">
                        </path>
                        <path fill="#4caf50"
                            d="M34.586,14.586l-13.57,13.586l-5.602-5.586l-2.828,2.828l8.434,8.414l16.395-16.414L34.586,14.586z">
                        </path>
                    </svg>
                </td>
            @else
                <form action="{{ route('elits-samples.verification-analytic-2', [$sample->id_samples]) }}"
                    method="post" class="formInputHasil">
                    @csrf
                    <input type="number" value="{{ 3 }}" name="verification_step" hidden>
                    <td><input type="datetime-local" class="form-control" name="start_date" id="start_date_input"
                            required></td>
                    <td><input type="datetime-local" class="form-control" name="stop_date" id="stop_date_input"
                            required></td>

                    <td>
                        @php
                            if ($sample->kode_laboratorium == 'MBI') {
                                # code...
                                $list_name_petugas = explode(', ', $verificationActivity[2]->mikro);
                            } elseif ($sample->kode_laboratorium == 'KIM') {
                                # code...
                                $list_name_petugas = explode(', ', $verificationActivity[2]->kimia);
                            } else {
                                $list_name_petugas = explode(', ', $verificationActivity[2]->klnik);
                            }
                        @endphp
                        <select name="nama_petugas" id="namaPetugasInputHasil" required>
                            @foreach ($list_name_petugas as $nama_petugas)
                                <option value="{{ $nama_petugas }}">{{ $nama_petugas }}
                                </option>
                            @endforeach

                        </select>
                        {{-- <input type="text" class="form-control" placeholder="Nama Petugas" value="{{ Auth()->user()->name }}"
            name="nama_petugas" required> --}}
                    </td>
                    <td class="text-center">
                        <button type="submit" class="btn btn-success"
                            @if (!isset($listVerifications[2]) or !isset($labAnalitikProgres)) disabled @endif onclick="checkNikAndPassword(document.getElementById('namaPetugasInputHasil').value, 'formInputHasil')">Verifikasi</button>
                </form>
                <a
                    href="{{ route('elits-baca-hasil.index', [$sample->id_samples, $lab_num->lab_id, 'bfecda4a-73f2-47d6-9fc3-01f65e0f02a1']) }}"><button
                        type="button" class="btn btn-primary" @if (!isset($listVerifications[2])) disabled @endif>Input
                        Hasil</button></a>
                </td>
          @endif
        </tr>
        <tr id="baca-hasil-update" style="display: none;">
            <th scope="row">Input / Output Hasil Px</th>
            <form action="{{ route('elits-samples.verification-analytic-2', [$sample->id_samples]) }}"
                method="post" class="formInputHasilUpdate">
                @csrf
                <input type="number" value="{{ 3 }}" name="verification_step" hidden>
                <td><input type="datetime-local" class="form-control" name="start_date"
                        value="{{ isset($listVerifications[3]->start_date) ? $listVerifications[3]->start_date : '' }}"
                        required>
                </td>
                <td><input type="datetime-local" class="form-control" name="stop_date"
                        value="{{ isset($listVerifications[3]->stop_date) ? $listVerifications[3]->stop_date : '' }}"
                        required></td>

                <td>
                    @php
                        if ($sample->kode_laboratorium == 'MBI') {
                            # code...
                            $list_name_petugas = explode(', ', $verificationActivity[2]->mikro);
                        } elseif ($sample->kode_laboratorium == 'KIM') {
                            # code...
                            $list_name_petugas = explode(', ', $verificationActivity[2]->kimia);
                        } else {
                            $list_name_petugas = explode(', ', $verificationActivity[2]->klnik);
                        }
                    @endphp
                    <select name="nama_petugas" id="namaPetugasInputHasilUpdate" required>
                        @foreach ($list_name_petugas as $nama_petugas)
                            <option value="{{ $nama_petugas }}">{{ $nama_petugas }}
                            </option>
                        @endforeach

                    </select>
                    {{-- <input type="text" class="form-control" placeholder="Nama Petugas" value="{{ Auth()->user()->name }}"
            name="nama_petugas" required> --}}
                </td>
                <td class="text-center">
                    <button type="submit" class="btn btn-success"
                        @if (!isset($listVerifications[2]) or !isset($labAnalitikProgres)) disabled @endif onclick="checkNikAndPassword(document.getElementById('namaPetugasInputHasilUpdate').value, 'formInputHasilUpdate')">Verifikasi</button>
            </form>
            <a
                href="{{ route('elits-baca-hasil.index', [$sample->id_samples, $lab_num->lab_id, 'bfecda4a-73f2-47d6-9fc3-01f65e0f02a1']) }}"><button
                    type="button" class="btn btn-primary" @if (!isset($listVerifications[2])) disabled @endif>Input
                    Hasil</button></a>
            </td>
        </tr>
        <tr id="verifikasi">
            <th scope="row">Verifikasi</th>
            @if (isset($listVerifications[4]))
                <td>{{ $listVerifications[4]->start_date }}</td>
                <td>{{ $listVerifications[4]->stop_date }}</td>
                <td>{{ $listVerifications[4]->nama_petugas }}</td>
                <td class="text-center">
                    <svg id="toggle-verifikasi" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="48"
                        height="48" viewBox="0 0 48 48" style="cursor: pointer;">
                        <title>Edit Verifikasi</title>
                        <path fill="#c8e6c9"
                            d="M44,24c0,11.045-8.955,20-20,20S4,35.045,4,24S12.955,4,24,4S44,12.955,44,24z"></path>
                        <path fill="#4caf50"
                            d="M34.586,14.586l-13.57,13.586l-5.602-5.586l-2.828,2.828l8.434,8.414l16.395-16.414L34.586,14.586z">
                        </path>
                    </svg>
                </td>
            @else
                <form action="{{ route('elits-samples.verification-analytic-2', [$sample->id_samples]) }}"
                    method="post" class="formVerifikasi">
                    @csrf
                    <input type="number" value="{{ 4 }}" name="verification_step" hidden>
                    <td><input type="datetime-local" class="form-control" name="start_date"
                            id="start_date_verifikasi" required>
                    </td>
                    <td><input type="datetime-local" class="form-control" name="stop_date" id="stop_date_verifikasi"
                            required></td>
                    <td>
                        <select name="nama_petugas" id="namaPetugasVerifikasi" required>
                            @php
                                if ($sample->kode_laboratorium == 'MBI') {
                                    # code...
                                    $list_name_petugas = explode(', ', $verificationActivity[3]->mikro);
                                } elseif ($sample->kode_laboratorium == 'KIM') {
                                    # code...
                                    $list_name_petugas = explode(', ', $verificationActivity[3]->kimia);
                                } else {
                                    $list_name_petugas = explode(', ', $verificationActivity[3]->klnik);
                                }
                            @endphp
                            @foreach ($list_name_petugas as $nama_petugas)
                                <option value="{{ $nama_petugas }}">{{ $nama_petugas }}
                                </option>
                            @endforeach

                        </select>
                    </td>
                    <td class="text-center">
                        <button type="submit" class="btn btn-success"
                            @if (!isset($listVerifications[3]) or !isset($verifikasiHasil) or !isset($verifikasiHasil->verifikasi_hasil_date)) disabled @endif onclick="checkNikAndPassword(document.getElementById('namaPetugasVerifikasi').value, 'formVerifikasi')">Verifikasi</button>
                </form>
                <a href="{{ route('elits-verifikasi-hasil.index', [$sample->id_samples, $lab_num->lab_id]) }}"><button
                        type="button" class="btn btn-primary"
                        @if (!isset($listVerifications[3])) disabled @endif>Verifikasi
                        Hasil</button></a>
                </td>
            @endif
        </tr>
        <tr id="verifikasi-update" style="display:none;">
            <th scope="row">Verifikasi</th>
            <form action="{{ route('elits-samples.verification-analytic-2', [$sample->id_samples]) }}"
                method="post" class="formVerifikasiUpdate">
                @csrf
                <input type="number" value="{{ 4 }}" name="verification_step" hidden>
                <td><input type="datetime-local" class="form-control" name="start_date"
                        value="{{ isset($listVerifications[4]->start_date) ? $listVerifications[4]->start_date : '' }}"
                        required>
                </td>
                <td><input type="datetime-local" class="form-control" name="stop_date"
                        value="{{ isset($listVerifications[4]->stop_date) ? $listVerifications[4]->stop_date : '' }}"
                        required></td>
                <td>
                    <select name="nama_petugas" id="namaPetugasVerifikasiUpdate" required>
                        @php
                            if ($sample->kode_laboratorium == 'MBI') {
                                # code...
                                $list_name_petugas = explode(', ', $verificationActivity[3]->mikro);
                            } elseif ($sample->kode_laboratorium == 'KIM') {
                                # code...
                                $list_name_petugas = explode(', ', $verificationActivity[3]->kimia);
                            } else {
                                $list_name_petugas = explode(', ', $verificationActivity[3]->klnik);
                            }
                        @endphp
                        @foreach ($list_name_petugas as $nama_petugas)
                            <option value="{{ $nama_petugas }}">{{ $nama_petugas }}
                            </option>
                        @endforeach

                    </select>

                </td>
                <td class="text-center">
                    <button type="submit" class="btn btn-success"
                        @if (!isset($listVerifications[3]) or !isset($verifikasiHasil) or !isset($verifikasiHasil->verifikasi_hasil_date)) disabled @endif onclick="checkNikAndPassword(document.getElementById('namaPetugasVerifikasiUpdate').value, 'formVerifikasiUpdate')">Verifikasi</button>
            </form>
            <a href="{{ route('elits-verifikasi-hasil.index', [$sample->id_samples, $lab_num->lab_id]) }}"><button
                    type="button" class="btn btn-primary"
                    @if (!isset($listVerifications[3])) disabled @endif>Verifikasi
                    Hasil</button></a>
            </td>
        </tr>
        <tr id="validasi">
            <th scope="row">Validasi</th>
            @if (isset($listVerifications[5]))
                <td>{{ $listVerifications[5]->start_date }}</td>
                <td>{{ $listVerifications[5]->stop_date }}</td>
                <td>{{ $listVerifications[5]->nama_petugas }}</td>
                <td class="text-center">
                    <svg id="toggle-validasi" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="48"
                        height="48" viewBox="0 0 48 48" style="cursor: pointer;">
                        <path fill="#c8e6c9"
                            d="M44,24c0,11.045-8.955,20-20,20S4,35.045,4,24S12.955,4,24,4S44,12.955,44,24z">
                        </path>
                        <path fill="#4caf50"
                            d="M34.586,14.586l-13.57,13.586l-5.602-5.586l-2.828,2.828l8.434,8.414l16.395-16.414L34.586,14.586z">
                        </path>
                    </svg>
                </td>
            @else
                <form action="{{ route('elits-samples.verification-analytic-2', [$sample->id_samples]) }}"
                    method="post" class="formValidasi">
                    @csrf
                    <input type="number" value="{{ 5 }}" name="verification_step" hidden>
                    <td><input type="datetime-local" class="form-control" name="start_date" id="start_date_validasi"
                            required></td>
                    <td><input type="datetime-local" class="form-control" name="stop_date" id="stop_date_validasi"
                            required></td>
                    <td>
                        <select name="nama_petugas" id="namaPetugasValidasi" required>
                            @php
                                if ($sample->kode_laboratorium == 'MBI') {
                                    # code...
                                    $list_name_petugas = explode(', ', $verificationActivity[4]->mikro);
                                } elseif ($sample->kode_laboratorium == 'KIM') {
                                    # code...
                                    $list_name_petugas = explode(', ', $verificationActivity[4]->kimia);
                                } else {
                                    $list_name_petugas = explode(', ', $verificationActivity[4]->klnik);
                                }
                            @endphp
                            @foreach ($list_name_petugas as $nama_petugas)
                                <option value="{{ $nama_petugas }}">{{ $nama_petugas }}
                                </option>
                            @endforeach

                        </select>

                    </td>
                    <td class="text-center">
                        <button type="submit" class="btn btn-success"
                            @if (!isset($listVerifications[4]) or !isset($pengesahanHasil) or !isset($pengesahanHasil->pengesahan_hasil_date)) disabled @endif onclick="checkNikAndPassword(document.getElementById('namaPetugasValidasi').value, 'formValidasi')">Verifikasi</button>
                </form>
                <a href="{{ route('elits-pengesahan-hasil.index', [$sample->id_samples, $lab_num->lab_id ?? '0']) }}"><button
                        type="button" class="btn btn-primary"
                        @if (!isset($listVerifications[4])) disabled @endif>Validasi
                        Hasil</button></a>
                </td>
            @endif
        </tr>
        <tr id="validasi-update" style="display:none;">
            <th scope="row">Validasi</th>
            <form action="{{ route('elits-samples.verification-analytic-2', [$sample->id_samples]) }}"
                method="post" class="formValidasiUpdate">
                @csrf
                <input type="number" value="{{ 5 }}" name="verification_step" hidden>
                <td><input type="datetime-local" class="form-control" name="start_date"
                        value="{{ isset($listVerifications[5]->start_date) ? $listVerifications[5]->start_date : '' }}"
                        required>
                </td>
                <td><input type="datetime-local" class="form-control" name="stop_date"
                        value="{{ isset($listVerifications[5]->stop_date) ? $listVerifications[5]->stop_date : '' }}"
                        required></td>
                <td>
                    <select name="nama_petugas" id="namaPetugasValidasiUpdate" required>
                        @php
                            if ($sample->kode_laboratorium == 'MBI') {
                                $list_name_petugas = explode(', ', $verificationActivity[4]->mikro);
                            } elseif ($sample->kode_laboratorium == 'KIM') {
                                $list_name_petugas = explode(', ', $verificationActivity[4]->kimia);
                            } else {
                                $list_name_petugas = explode(', ', $verificationActivity[4]->klnik);
                            }
                        @endphp
                        @foreach ($list_name_petugas as $nama_petugas)
                            <option value="{{ $nama_petugas }}"
                                {{ isset($listVerifications[5]->nama_petugas) && $listVerifications[5]->nama_petugas == $nama_petugas
                                    ? 'selected'
                                    : '' }}>
                                {{ $nama_petugas }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td class="text-center">
                    <button type="submit" class="btn btn-success"
                        @if (!isset($listVerifications[4]) or !isset($pengesahanHasil) or !isset($pengesahanHasil->pengesahan_hasil_date)) disabled @endif onclick="checkNikAndPassword(document.getElementById('namaPetugasValidasiUpdate').value, 'formValidasiUpdate')">Validasi</button>
            </form>
            <a href="{{ route('elits-pengesahan-hasil.index', [$sample->id_samples, $lab_num->lab_id ?? '0']) }}">
                <button type="button" class="btn btn-primary"
                    @if (!isset($listVerifications[4])) disabled @endif>Validasi
                    Hasil</button>
            </a>
            </td>
        </tr>

    </tbody>
</table>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        let pendaftaranStop, pemeriksaanStart, pemeriksaanStop, inputStart, inputStop, verifikasiStart,
            verifikasiStop, validasiStart, validasiStop;

        // 1. Pendaftaran
        @if (isset($listVerifications[1]))
            pendaftaranStop = new Date("{{ $listVerifications[1]->stop_date }}");
        @else
            pendaftaranStop = new Date();
        @endif

        // 2. Pemeriksaan
        @if (isset($listVerifications[2]))
            pemeriksaanStart = new Date("{{ $listVerifications[2]->start_date }}");
            pemeriksaanStop = new Date("{{ $listVerifications[2]->stop_date }}");
        @else
            pemeriksaanStart = new Date(pendaftaranStop);
            pemeriksaanStop = new Date(pemeriksaanStart);
            pemeriksaanStop.setDate(pemeriksaanStop.getDate() + 3); // [Mikro] +3 hari dari Pemeriksaan Start
        @endif

        // 3. Input / Output
        @if (isset($listVerifications[3]))
            inputStart = new Date("{{ $listVerifications[3]->start_date }}");
            inputStop = new Date("{{ $listVerifications[3]->stop_date }}");
        @else
            inputStart = new Date(pemeriksaanStop);
            inputStop = new Date(inputStart.getTime() + 10 * 60000); // [Mikro] +10 menit dari Input Start
        @endif

        // 4. Verifikasi
        @if (isset($listVerifications[4]))
            verifikasiStart = new Date("{{ $listVerifications[4]->start_date }}");
            verifikasiStop = new Date("{{ $listVerifications[4]->stop_date }}");
        @else
            verifikasiStart = new Date(inputStop);
            verifikasiStop = new Date(verifikasiStart.getTime() + 1 *
                3600000); // [Mikro] +1 jam dari Verifikasi Start
        @endif

        // 5. Validasi
        @if (isset($listVerifications[5]))
            validasiStart = new Date("{{ $listVerifications[5]->start_date }}");
            validasiStop = new Date("{{ $listVerifications[5]->stop_date }}");
        @else
            validasiStart = new Date(verifikasiStop);
            validasiStop = new Date(validasiStart.getTime() + 1 *
                3600000); // [Mikro] +1 jam dari Validasi Start
        @endif

        // if (document.querySelector('#start_date_pemeriksaan')) {
        //   document.querySelector('#start_date_pemeriksaan').value = formatDate(pemeriksaanStart);
        // }
        // if (document.querySelector('#stop_date_pemeriksaan')) {
        //   document.querySelector('#stop_date_pemeriksaan').value = formatDate(pemeriksaanStop);
        // }

        // if (document.querySelector('#start_date_input')) {
        //   document.querySelector('#start_date_input').value = formatDate(inputStart);
        // }
        // if (document.querySelector('#stop_date_input')) {
        //   document.querySelector('#stop_date_input').value = formatDate(inputStop);
        // }

        // if (document.querySelector('#start_date_verifikasi')) {
        //   document.querySelector('#start_date_verifikasi').value = formatDate(verifikasiStart);
        // }
        // if (document.querySelector('#stop_date_verifikasi')) {
        //   document.querySelector('#stop_date_verifikasi').value = formatDate(verifikasiStop);
        // }

        // if (document.querySelector('#start_date_validasi')) {
        //   document.querySelector('#start_date_validasi').value = formatDate(validasiStart);
        // }
        // if (document.querySelector('#stop_date_validasi')) {
        //   document.querySelector('#stop_date_validasi').value = formatDate(validasiStop);
        // }

        // Populate the date fields in the HTML

        if (document.querySelector('#start_date_registrasi')) {
            // document.querySelector('#start_date_pemeriksaan').value = formatDate(pemeriksaanStart);

            // flatpickr(".datetime", {
            //     enableTime: true,
            //     dateFormat: "d/m/Y H:i", // 24-hour format
            //     time_24hr: true
            // });

            var pendaftaranStart = new Date("{{ $listVerifications[1]->start_date }}");
            const start_date_registrasi = flatpickr("#start_date_registrasi", {
                // Opsi lain jika diperlukan
                enableTime: true,
                locale: "id", // Setting locale to Indonesian
                allowInput: true,
                dateFormat: "d/m/Y H:i", // 24-hour format
                time_24hr: true
            });
            start_date_registrasi.setDate(formatDate(pendaftaranStart), true); //

            $('#start_date_registrasi').inputmask("datetime", {
                placeholder: "dd/mm/yyyy hh:mm",

            });
        }

        if (document.querySelector('#stop_date_registrasi')) {
            // document.querySelector('#start_date_pemeriksaan').value = formatDate(pemeriksaanStart);

            // flatpickr(".datetime", {
            //     enableTime: true,
            //     dateFormat: "d/m/Y H:i", // 24-hour format
            //     time_24hr: true
            // });

            // var pendaftaranStop = new Date("{{ $listVerifications[1]->stop_date }}");
            const stop_date_registrasi = flatpickr("#stop_date_registrasi", {
                // Opsi lain jika diperlukan
                enableTime: true,
                locale: "id", // Setting locale to Indonesian
                allowInput: true,
                dateFormat: "d/m/Y H:i", // 24-hour format
                time_24hr: true
            });
            stop_date_registrasi.setDate(formatDate(pendaftaranStop), true); //

            $('#stop_date_registrasi').inputmask("datetime", {
                placeholder: "dd/mm/yyyy hh:mm",

            });
        }



        if (document.querySelector('#start_date_pemeriksaan')) {
            // document.querySelector('#start_date_pemeriksaan').value = formatDate(pemeriksaanStart);

            // flatpickr(".datetime", {
            //     enableTime: true,
            //     dateFormat: "d/m/Y H:i", // 24-hour format
            //     time_24hr: true
            // });
            const start_date_pemeriksaan = flatpickr("#start_date_pemeriksaan", {
                // Opsi lain jika diperlukan
                enableTime: true,
                locale: "id", // Setting locale to Indonesian
                allowInput: true,
                dateFormat: "d/m/Y H:i", // 24-hour format
                time_24hr: true
            });

            start_date_pemeriksaan.setDate(formatDate(pemeriksaanStart), true); //

            $('#start_date_pemeriksaan').inputmask("datetime", {
                placeholder: "dd/mm/yyyy hh:mm",

            });
        }

        if (document.querySelector('#stop_date_pemeriksaan')) {
            // document.querySelector('#stop_date_pemeriksaan').value = formatDate(pemeriksaanStart);

            // flatpickr(".datetime", {
            //     enableTime: true,
            //     dateFormat: "d/m/Y H:i", // 24-hour format
            //     time_24hr: true
            // });
            const stop_date_pemeriksaan = flatpickr("#stop_date_pemeriksaan", {
                // Opsi lain jika diperlukan
                enableTime: true,
                locale: "id", // Setting locale to Indonesian
                allowInput: true,
                dateFormat: "d/m/Y H:i", // 24-hour format
                time_24hr: true
            });

            stop_date_pemeriksaan.setDate(formatDate(pemeriksaanStop), true); //

            $('#stop_date_pemeriksaan').inputmask("datetime", {
                placeholder: "dd/mm/yyyy hh:mm",

            });
        }
        // if (document.querySelector('#stop_date_pemeriksaan')) {
        //     document.querySelector('#stop_date_pemeriksaan').value = formatDate(pemeriksaanStop);
        // }

        if (document.querySelector('#start_date_input')) {
            // document.querySelector('#stop_date_pemeriksaan').value = formatDate(pemeriksaanStart);

            // flatpickr(".datetime", {
            //     enableTime: true,
            //     dateFormat: "d/m/Y H:i", // 24-hour format
            //     time_24hr: true
            // });
            const start_date_input = flatpickr("#start_date_input", {
                // Opsi lain jika diperlukan
                enableTime: true,
                allowInput: true,
                locale: "id", // Setting locale to Indonesian
                dateFormat: "d/m/Y H:i", // 24-hour format
                time_24hr: true
            });

            start_date_input.setDate(formatDate(inputStart), true); //

            $('#start_date_input').inputmask("datetime", {
                placeholder: "dd/mm/yyyy hh:mm",

            });
        }

        // if (document.querySelector('#start_date_input')) {
        //     document.querySelector('#start_date_input').value = formatDate(inputStart);
        // }

        if (document.querySelector('#stop_date_input')) {
            // document.querySelector('#stop_date_input').value = formatDate(pemeriksaanStart);

            // flatpickr(".datetime", {
            //     enableTime: true,
            //     dateFormat: "d/m/Y H:i", // 24-hour format
            //     time_24hr: true
            // });
            const stop_date_input = flatpickr("#stop_date_input", {
                // Opsi lain jika diperlukan
                enableTime: true,
                allowInput: true,
                locale: "id", // Setting locale to Indonesian
                dateFormat: "d/m/Y H:i", // 24-hour format
                time_24hr: true
            });

            stop_date_input.setDate(formatDate(inputStop), true); //

            $('#stop_date_input').inputmask("datetime", {
                placeholder: "dd/mm/yyyy hh:mm",

            });
        }
        // if (document.querySelector('#stop_date_input')) {
        //     document.querySelector('#stop_date_input').value = formatDate(inputStop);
        // }

        if (document.querySelector('#start_date_verifikasi')) {
            // document.querySelector('#stop_date_input').value = formatDate(pemeriksaanStart);

            // flatpickr(".datetime", {
            //     enableTime: true,
            //     dateFormat: "d/m/Y H:i", // 24-hour format
            //     time_24hr: true
            // });
            const start_date_verifikasi = flatpickr("#start_date_verifikasi", {
                // Opsi lain jika diperlukan
                enableTime: true,
                allowInput: true,
                locale: "id", // Setting locale to Indonesian
                dateFormat: "d/m/Y H:i", // 24-hour format
                time_24hr: true
            });

            start_date_verifikasi.setDate(formatDate(verifikasiStart), true); //

            $('#start_date_verifikasi').inputmask("datetime", {
                placeholder: "dd/mm/yyyy hh:mm",

            });
        }

        // if (document.querySelector('#start_date_verifikasi')) {
        //     document.querySelector('#start_date_verifikasi').value = formatDate(verifikasiStart);
        // }

        if (document.querySelector('#stop_date_verifikasi')) {
            // document.querySelector('#stop_date_input').value = formatDate(pemeriksaanStart);

            // flatpickr(".datetime", {
            //     enableTime: true,
            //     dateFormat: "d/m/Y H:i", // 24-hour format
            //     time_24hr: true
            // });
            const stop_date_verifikasi = flatpickr("#stop_date_verifikasi", {
                // Opsi lain jika diperlukan

                allowInput: true,
                enableTime: true,
                locale: "id", // Setting locale to Indonesian
                dateFormat: "d/m/Y H:i", // 24-hour format
                time_24hr: true
            });

            stop_date_verifikasi.setDate(formatDate(verifikasiStop), true); //

            $('#stop_date_verifikasi').inputmask("datetime", {
                placeholder: "dd/mm/yyyy hh:mm",

            });
        }
        // if (document.querySelector('#stop_date_verifikasi')) {
        //     document.querySelector('#stop_date_verifikasi').value = formatDate(verifikasiStop);
        // }

        if (document.querySelector('#start_date_validasi')) {
            // document.querySelector('#stop_date_input').value = formatDate(pemeriksaanStart);

            // flatpickr(".datetime", {
            //     enableTime: true,
            //     dateFormat: "d/m/Y H:i", // 24-hour format
            //     time_24hr: true
            // });
            const start_date_validasi = flatpickr("#start_date_validasi", {
                // Opsi lain jika diperlukan

                allowInput: true,
                locale: "id", // Setting locale to Indonesian
                enableTime: true,
                dateFormat: "d/m/Y H:i", // 24-hour format
                time_24hr: true
            });

            start_date_validasi.setDate(formatDate(validasiStart), true); //

            $('#start_date_validasi').inputmask("datetime", {
                placeholder: "dd/mm/yyyy hh:mm",

            });
        }

        // if (document.querySelector('#start_date_validasi')) {
        //     document.querySelector('#start_date_validasi').value = formatDate(validasiStart);
        // }

        if (document.querySelector('#stop_date_validasi')) {
            // document.querySelector('#stop_date_input').value = formatDate(pemeriksaanStart);

            // flatpickr(".datetime", {
            //     enableTime: true,
            //     dateFormat: "d/m/Y H:i", // 24-hour format
            //     time_24hr: true
            // });
            const stop_date_validasi = flatpickr("#stop_date_validasi", {
                // Opsi lain jika diperlukan

                allowInput: true,
                locale: "id", // Setting locale to Indonesian
                enableTime: true,
                dateFormat: "d/m/Y H:i", // 24-hour format
                time_24hr: true
            });



            stop_date_validasi.setDate(formatDate(validasiStop), true); //

            $('#stop_date_validasi').inputmask("datetime", {
                placeholder: "dd/mm/yyyy hh:mm",

            });
        }

        function formatDate(date) {
            let year = date.getFullYear();
            let month = String(date.getMonth() + 1).padStart(2, '0');
            let day = String(date.getDate()).padStart(2, '0');
            let hours = String(date.getHours()).padStart(2, '0');
            let minutes = String(date.getMinutes()).padStart(2, '0');
            return `${day}/${month}/${year} ${hours}:${minutes}`;
        }
    });
</script>
<script>
    $(document).ready(function() {

        if (document.getElementById('toggle-registrasi')) {
            document.getElementById('toggle-registrasi').addEventListener('click', function() {
                var registrasiRow = document.getElementById('registrasi');
                var registrasiUpdateRow = document.getElementById('registrasi-update');

                if (registrasiRow.style.display === 'none') {
                    registrasiRow.style.display = '';
                    registrasiUpdateRow.style.display = 'none';
                } else {
                    registrasiRow.style.display = 'none';
                    registrasiUpdateRow.style.display = '';
                }


                var registrasiUpdateRowStart = $('#registrasi-update [name="start_date"]').val();

                flatpickr('#registrasi-update [name="start_date"]', {
                    enableTime: true,
                    dateFormat: "d/m/Y H:i", // 24-hour format
                    time_24hr: true
                });
                const register_update_start = flatpickr('#registrasi-update [name="start_date"]', {
                    // Opsi lain jika diperlukan

                    allowInput: true,
                    locale: "id", // Setting locale to Indonesian
                    enableTime: true,
                    dateFormat: "d/m/Y H:i", // 24-hour format
                    time_24hr: true
                });


                register_update_start.setDate(formatDate(new Date(registrasiUpdateRowStart)), true); //

                $('#registrasi-update [name="start_date"]').inputmask("datetime", {
                    placeholder: "dd/mm/yyyy hh:mm",

                });

                var registrasiUpdateRowStop = $('#registrasi-update [name="stop_date"]').val();

                flatpickr('#registrasi-update [name="stop_date"]', {
                    enableTime: true,
                    dateFormat: "d/m/Y H:i", // 24-hour format
                    time_24hr: true
                });
                const register_update_stop = flatpickr('#registrasi-update [name="stop_date"]', {
                    // Opsi lain jika diperlukan

                    allowInput: true,
                    locale: "id", // Setting locale to Indonesian
                    enableTime: true,
                    dateFormat: "d/m/Y H:i", // 24-hour format
                    time_24hr: true
                });


                register_update_stop.setDate(formatDate(new Date(registrasiUpdateRowStop)), true); //

                $('#registrasi-update [name="stop_date"]').inputmask("datetime", {
                    placeholder: "dd/mm/yyyy hh:mm",

                });
            });
        }


        if (document.getElementById('toggle-analitik')) {

            document.getElementById('toggle-analitik').addEventListener('click', function() {
                var analitikRow = document.getElementById('analitik');
                var analitikUpdateRow = document.getElementById('analitik-update');

                if (analitikRow.style.display === 'none') {
                    analitikRow.style.display = '';
                    analitikUpdateRow.style.display = 'none';
                } else {
                    analitikRow.style.display = 'none';
                    analitikUpdateRow.style.display = '';
                }

                var analitikUpdateRowStart = $('#analitik-update [name="start_date"]').val();

                flatpickr('#analitik-update [name="start_date"]', {
                    enableTime: true,
                    dateFormat: "d/m/Y H:i", // 24-hour format
                    time_24hr: true
                });
                const analitik_update_start = flatpickr('#analitik-update [name="start_date"]', {
                    // Opsi lain jika diperlukan

                    allowInput: true,
                    locale: "id", // Setting locale to Indonesian
                    enableTime: true,
                    dateFormat: "d/m/Y H:i", // 24-hour format
                    time_24hr: true
                });


                analitik_update_start.setDate(formatDate(new Date(analitikUpdateRowStart)), true); //

                $('#analitik-update [name="start_date"]').inputmask("datetime", {
                    placeholder: "dd/mm/yyyy hh:mm",

                });

                var analitikUpdateRowStop = $('#analitik-update [name="stop_date"]').val();

                flatpickr('#analitik-update [name="stop_date"]', {
                    enableTime: true,
                    dateFormat: "d/m/Y H:i", // 24-hour format
                    time_24hr: true
                });
                const analitik_update_stop = flatpickr('#analitik-update [name="stop_date"]', {
                    // Opsi lain jika diperlukan

                    allowInput: true,
                    locale: "id", // Setting locale to Indonesian
                    enableTime: true,
                    dateFormat: "d/m/Y H:i", // 24-hour format
                    time_24hr: true
                });


                analitik_update_stop.setDate(formatDate(new Date(analitikUpdateRowStop)), true); //

                $('#analitik-update [name="stop_date"]').inputmask("datetime", {
                    placeholder: "dd/mm/yyyy hh:mm",

                });
            });
        }

        if (document.getElementById('toggle-baca-hasil')) {

            document.getElementById('toggle-baca-hasil').addEventListener('click', function() {
                var bacaHasilRow = document.getElementById('baca-hasil');
                var bacaHasilUpdateRow = document.getElementById('baca-hasil-update');

                if (bacaHasilRow.style.display === 'none') {
                    bacaHasilRow.style.display = '';
                    bacaHasilUpdateRow.style.display = 'none';
                } else {
                    bacaHasilRow.style.display = 'none';
                    bacaHasilUpdateRow.style.display = '';
                }
                var bacaHasilUpdateRowStart = $('#baca-hasil-update [name="start_date"]').val();

                flatpickr('#baca-hasil-update [name="start_date"]', {
                    enableTime: true,
                    dateFormat: "d/m/Y H:i", // 24-hour format
                    time_24hr: true
                });
                const bacaHasil_update_start = flatpickr('#baca-hasil-update [name="start_date"]', {
                    // Opsi lain jika diperlukan

                    allowInput: true,
                    locale: "id", // Setting locale to Indonesian
                    enableTime: true,
                    dateFormat: "d/m/Y H:i", // 24-hour format
                    time_24hr: true
                });


                bacaHasil_update_start.setDate(formatDate(new Date(bacaHasilUpdateRowStart)), true); //

                $('#baca-hasil-update [name="start_date"]').inputmask("datetime", {
                    placeholder: "dd/mm/yyyy hh:mm",

                });

                var bacaHasilUpdateRowStop = $('#baca-hasil-update [name="stop_date"]').val();

                flatpickr('#baca-hasil-update [name="stop_date"]', {
                    enableTime: true,
                    dateFormat: "d/m/Y H:i", // 24-hour format
                    time_24hr: true
                });
                const bacaHasil_update_stop = flatpickr('#baca-hasil-update [name="stop_date"]', {
                    // Opsi lain jika diperlukan

                    allowInput: true,
                    locale: "id", // Setting locale to Indonesian
                    enableTime: true,
                    dateFormat: "d/m/Y H:i", // 24-hour format
                    time_24hr: true
                });


                bacaHasil_update_stop.setDate(formatDate(new Date(bacaHasilUpdateRowStop)), true); //

                $('#baca-hasil-update [name="stop_date"]').inputmask("datetime", {
                    placeholder: "dd/mm/yyyy hh:mm",

                });
            });

        }


        if (document.getElementById('toggle-verifikasi')) {

            document.getElementById('toggle-verifikasi').addEventListener('click', function() {
                var verifikasiRow = document.getElementById('verifikasi');
                var verifikasiUpdateRow = document.getElementById('verifikasi-update');

                if (verifikasiRow.style.display === 'none') {
                    verifikasiRow.style.display = '';
                    verifikasiUpdateRow.style.display = 'none';
                } else {
                    verifikasiRow.style.display = 'none';
                    verifikasiUpdateRow.style.display = '';
                }

                var verifikasiUpdateRowStart = $('#verifikasi-update [name="start_date"]').val();

                flatpickr('#verifikasi-update [name="start_date"]', {
                    enableTime: true,
                    dateFormat: "d/m/Y H:i", // 24-hour format
                    time_24hr: true
                });
                const verifikasi_update_start = flatpickr('#verifikasi-update [name="start_date"]', {
                    // Opsi lain jika diperlukan

                    allowInput: true,
                    locale: "id", // Setting locale to Indonesian
                    enableTime: true,
                    dateFormat: "d/m/Y H:i", // 24-hour format
                    time_24hr: true
                });


                verifikasi_update_start.setDate(formatDate(new Date(verifikasiUpdateRowStart)),
                    true); //

                $('#verifikasi-update [name="start_date"]').inputmask("datetime", {
                    placeholder: "dd/mm/yyyy hh:mm",

                });

                var verifikasiUpdateRowStop = $('#verifikasi-update [name="stop_date"]').val();

                flatpickr('#verifikasi-update [name="stop_date"]', {
                    enableTime: true,
                    dateFormat: "d/m/Y H:i", // 24-hour format
                    time_24hr: true
                });
                const verifikasi_update_stop = flatpickr('#verifikasi-update [name="stop_date"]', {
                    // Opsi lain jika diperlukan

                    allowInput: true,
                    locale: "id", // Setting locale to Indonesian
                    enableTime: true,
                    dateFormat: "d/m/Y H:i", // 24-hour format
                    time_24hr: true
                });


                verifikasi_update_stop.setDate(formatDate(new Date(verifikasiUpdateRowStop)), true); //

                $('#verifikasi-update [name="stop_date"]').inputmask("datetime", {
                    placeholder: "dd/mm/yyyy hh:mm",

                });
            });

        }

        if (document.getElementById('toggle-validasi')) {

            document.getElementById('toggle-validasi').addEventListener('click', function() {
                var validasiRow = document.getElementById('validasi');
                var validasiUpdateRow = document.getElementById('validasi-update');

                if (validasiRow.style.display === 'none') {
                    validasiRow.style.display = '';
                    validasiUpdateRow.style.display = 'none';
                } else {
                    validasiRow.style.display = 'none';
                    validasiUpdateRow.style.display = '';
                }

                var validasiUpdateRowStart = $('#validasi-update [name="start_date"]').val();

                flatpickr('#validasi-update [name="start_date"]', {
                    enableTime: true,
                    dateFormat: "d/m/Y H:i", // 24-hour format
                    time_24hr: true
                });
                const validasi_update_start = flatpickr('#validasi-update [name="start_date"]', {
                    // Opsi lain jika diperlukan

                    allowInput: true,
                    locale: "id", // Setting locale to Indonesian
                    enableTime: true,
                    dateFormat: "d/m/Y H:i", // 24-hour format
                    time_24hr: true
                });


                validasi_update_start.setDate(formatDate(new Date(validasiUpdateRowStart)), true); //

                $('#validasi-update [name="start_date"]').inputmask("datetime", {
                    placeholder: "dd/mm/yyyy hh:mm",

                });

                var validasiUpdateRowStop = $('#validasi-update [name="stop_date"]').val();

                flatpickr('#validasi-update [name="stop_date"]', {
                    enableTime: true,
                    dateFormat: "d/m/Y H:i", // 24-hour format
                    time_24hr: true
                });
                const validasi_update_stop = flatpickr('#validasi-update [name="stop_date"]', {
                    // Opsi lain jika diperlukan

                    allowInput: true,
                    locale: "id", // Setting locale to Indonesian
                    enableTime: true,
                    dateFormat: "d/m/Y H:i", // 24-hour format
                    time_24hr: true
                });


                validasi_update_stop.setDate(formatDate(new Date(validasiUpdateRowStop)), true); //

                $('#validasi-update [name="stop_date"]').inputmask("datetime", {
                    placeholder: "dd/mm/yyyy hh:mm",

                });
            });
        }

        function formatDate(date) {
            let year = date.getFullYear();
            let month = String(date.getMonth() + 1).padStart(2, '0');
            let day = String(date.getDate()).padStart(2, '0');
            let hours = String(date.getHours()).padStart(2, '0');
            let minutes = String(date.getMinutes()).padStart(2, '0');
            return `${day}/${month}/${year} ${hours}:${minutes}`;
        }
    });
</script>
