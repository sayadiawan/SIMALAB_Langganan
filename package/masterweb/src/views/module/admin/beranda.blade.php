@extends('masterweb::template.admin.layout')

@section('title')
  Beranda
@endsection

@php
  use Carbon\Carbon;

  $user = Auth()->user();

  $hour = Carbon::now()->format('H'); // Get current hour in 24-hour format
  if ($hour < 10) {
      $greeting = 'Selamat Pagi';
  } elseif ($hour < 14){
    $greeting = 'Selamat Siang';
  }elseif ($hour < 18) {
      $greeting = 'Selamat Sore';
  } else {
      $greeting = 'Selamat Malam';
  }
@endphp

@section('content')
  <style>
    .banner {
      background: rgb(18,5,254);
      background: linear-gradient(90deg, rgba(18,5,254,1) 0%, rgba(60,168,168,1) 0%, rgba(169,241,241,1) 100%);
      border-radius: 10px;
    }
    .text-banner {
      padding: 30px;
    }
    .text-banner h4{
      font-size: 28pt;
      color: white;
    }

    .btn-permohonan-uji {
      margin-top: 90px;
    }

    .text-banner p {
      font-size: 14pt;
      color: white;
    }

    .image-banner {
      width: 430px;
    }

    .total-analisa, .total-sample, .analisa-berjalan, .analisa-selesai, .total-uji-klinik, .total-pasien {
      height: 150px;
      width: 250px;
      border-left: 8px solid #3CA8A8;
      border-radius: 4px;
      background-color: white;
      transition: background-color 0.3s ease, border-left-color 0.3s ease, color 0.3s ease;
      color: black;
    }

    .total-analisa:hover, .total-sample:hover, .analisa-berjalan:hover, .analisa-selesai:hover, .total-uji-klinik:hover, .total-pasien:hover {
      background-color: #3CA8A8;
      color: white;
    }

    .counter {
      font-size: 55px;
    }

    .chart {
      display: flex;
      margin-top: 30px;
      gap: 10px;
    }

    .income-chart {
      width: 50%;
      height: 500px;
      background-color: white;
    }

    .sample-chart {
      width: 50%;
      background-color: white;
    }

    @media only screen and (max-width: 1023px) {
      .total-analisa, .total-sample, .analisa-berjalan, .analisa-selesai, .total-uji-klinik, .total-pasien {
        width: 100%;
      }

      .text-banner h4{
        font-size: 20pt;
        color: white;
      }

      .text-banner p {
        font-size: 12pt;
        color: white;
      }

      .btn-permohonan-uji {
        margin-top: 2px;
      }

      .image-banner {
        width: 200px;
      }

      .chart {
        display: block;
      }

      .income-chart {
        width: 100%;
        background-color: white;
        padding: 20px;
      }

      .sample-chart {
        width: 100%;
        margin-top: 10px;
        background-color: white;
      }

    }
  </style>

  <div class="d-flex justify-content-between mx-auto banner shadow">
    <div class="text-banner">
      <h4>{{ $greeting }} {{ $user->name }}</h4>
      <p class="mt-2">Hard work done with heart always pays off. <br> Every effort you make is a step towards great achievement.</p>
      @if($user->level == '3382abf2-8518-42f9-91e1-096f25da8ae8')
        @if($user->laboratory_users == 'bbed2259-2826-4711-b0fc-abdad5aace22')
          <a href="{{ url('elits-analys/klinik') }}">
            <button type="button" class="btn btn-default btn-fw btn-sm btn-permohonan-uji">Data Analisa Klinik</button>
          </a>
        @else
          <a href="{{ url('elits-analys') }}">
            <button type="button" class="btn btn-default btn-fw btn-sm btn-permohonan-uji">Data Analisa</button>
          </a>
        @endif
      @else
        <a href="{{ url('elits-permohonan-uji') }}">
          <button type="button" class="btn btn-default btn-fw btn-sm btn-permohonan-uji">Permohonan Uji</button>
        </a>
      @endif
    </div>

    <img class="image-banner" src="{{ asset('assets/admin/images/scientist-looking-test-tube.png') }}">
  </div>

  <div class="d-flex flex-wrap justify-content-between mx-auto mt-5">
    <div class="total-analisa p-3 mb-2 shadow d-flex justify-content-center align-items-center" style="height: 150px;">
      <div class="text-center">
        <h3 class="counter" data-target="{{ $total_permohonan_uji }}">0</h3>
        <h5 class="text-title">Total Permohonan Uji Kesmas</h5>
      </div>
    </div>
    <div class="total-sample p-3 mb-2 shadow d-flex justify-content-center align-items-center" style="height: 150px;">
      <div class="text-center">
        <h3 class="counter" data-target="{{ $total_sample }}">0</h3>
        <h5 class="text-title">Total Sampel Kesmas</h5>
      </div>
    </div>
    <div class="analisa-berjalan p-3 mb-2 shadow d-flex justify-content-center align-items-center" style="height: 150px;">
      <div class="text-center">
        <h3 class="counter" data-target="{{ $total_berjalan }}">0</h3>
        <h5 class="text-title">Analisa Kesmas Berjalan</h5>
      </div>
    </div>
    <div class="analisa-selesai p-3 mb-2 shadow d-flex justify-content-center align-items-center" style="height: 150px;">
      <div class="text-center">
        <h3 class="counter" data-target="{{ $total_selesai }}">0</h3>
        <h5 class="text-title">Analisa Kesmas Selesai</h5>
      </div>
    </div>

    <div class="total-uji-klinik p-3 mb-2 shadow d-flex justify-content-center align-items-center" style="height: 150px;">
      <div class="text-center">
        <h3 class="counter" data-target="{{ $total_permohonan_uji_klinik }}">0</h3>
        <h5 class="text-title">Total Permohonan Uji Klinik</h5>
      </div>
    </div>
    <div class="total-pasien p-3 mb-2 shadow d-flex justify-content-center align-items-center" style="height: 150px;">
      <div class="text-center">
        <h3 class="counter" data-target="{{ $pasien_klinik }}">0</h3>
        <h5 class="text-title">Pasien Klinik</h5>
      </div>
    </div>
  </div>

  <div class="chart mx-auto">
    <div class="income-chart shadow text-center" style="padding: 30px">
      <span><b>Pendapatan per Bulan</b></span>
      <canvas id="chartPendapatan"></canvas>
    </div>
    <div class="sample-chart shadow text-center" style="padding: 30px">
      <span><b>Total Sampel berdasarkan Jenis Sampel</b></span>
      <canvas id="chartSample"></canvas>
    </div>
  </div>

  <div class="row grid-margin mt-3">
    <div class="col-12">
      <div class="card card-statistics shadow">
        <div class="card-body">
          <div class="d-flex flex-column flex-md-row align-items-center justify-content-between">
            <div class="statistics-item">
              <h5 class="font-white card-title">
                <i class="icon-sm ti-agenda mr-2"></i> Permohonan Uji
              </h5>
              <a href="{{ url('elits-permohonan-uji') }}">
                <button type="button" class="btn btn-default btn-fw btn-sm">Selengkapnya</button>
              </a>
            </div>
            <div class="statistics-item">
              <h5 class="font-white card-title">
                <i class="icon-sm fas fa-users mr-2"></i> Data Inventori
              </h5>
              <a href="{{ url('elits-inventories') }}">
                <button type="button" class="btn btn-default btn-fw btn-sm">Selengkapnya</button>
              </a>
            </div>

            <div class="statistics-item">
              <h5 class="font-white card-title">
                <i class="icon-sm fas fa-users mr-2"></i> Stok Opname
              </h5>
              <a href="{{ url('stock-opname') }}">
                <button type="button" class="btn btn-default btn-fw btn-sm">Selengkapnya</button>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function() {
      $('.counter').each(function() {
        let $this = $(this);
        let total = parseInt($this.data('target'));
        let currentCount = 0;
        let increment = Math.ceil(total / 100);

        function updateCounter() {
          if (currentCount < total) {
            currentCount += increment;
            if (currentCount > total) {
              currentCount = total;
            }
            $this.text(currentCount).addClass('counter');
            setTimeout(updateCounter, 10);
          }
        }

        updateCounter();
      });
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    const ctx = document.getElementById('chartPendapatan').getContext('2d');

    new Chart(ctx, {
      type: 'line',
      data: {
        labels: {!! json_encode($bulans) !!},
        datasets: [{
          label: 'Pendapatan',
          data: {!! json_encode($pendapatans) !!},
          borderColor: 'rgba(75, 192, 192, 1)',
          borderWidth: 2,
          fill: false
        }]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });

    const ctx2 = document.getElementById('chartSample').getContext('2d');

    function getRandomColor() {
      const r = Math.floor(Math.random() * 255);
      const g = Math.floor(Math.random() * 255);
      const b = Math.floor(Math.random() * 255);
      return `rgb(${r}, ${g}, ${b})`;
    }

    const backgroundColors = {!! json_encode($sampleTypes) !!}.map(() => getRandomColor());

    new Chart(ctx2, {
      type: 'bar',
      data: {
        labels: {!! json_encode($sampleTypes) !!},
        datasets: [{
          label: 'Total',
          data: {!! json_encode($countSample) !!},
          backgroundColor: backgroundColors,
          borderColor: null,
          borderWidth: 0,
          fill: false
        }]
      },
      options: {
        plugins: {
          legend: {
            position: 'top'
          }
        }
      }
    });
  </script>
@endsection
