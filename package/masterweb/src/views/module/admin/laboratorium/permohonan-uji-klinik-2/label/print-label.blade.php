<html lang="">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>Print-Label-Pasien.BYLALI-2002006-AP</title>
  <link rel="shortcut icon" href="">
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <style>
    .starter-template {
      padding: 0px 0px;
      text-align: center;
    }

    .label-container {
      display: inline-block;
      width: 38mm; /* Default width */
      height: 38mm; /* Default height */
      border: 1px solid #000000;
      padding: 2mm; /* Optional padding */
      margin: 0; /* Default margin set to 0 */
      cursor: move; /* Mengubah kursor saat di atas label */
      position: relative; /* Required for draggable */
      font-size: 8px; /* Set font size */
      vertical-align: top; /* Align items to the top */
      margin-bottom: 2mm; /* Add margin bottom */
    }

    .label-row {
      display: flex; /* Use flexbox to arrange labels */
      flex-wrap: wrap; /* Allow wrapping to the next line */
    }

    /* Specific margins for label placement */
    .label-container:nth-child(1) {
      margin-right: 0mm; /* No margin for the first label */
    }

    .label-container:nth-child(2) {
      margin-right: 2mm; /* 2mm margin for the second label */
    }

    .label-container:nth-child(3) {
      margin-right: 0mm; /* No margin for the third label */
    }

    /* Settings Panel */
    .settings-panel {
      position: fixed;
      bottom: 80px; /* Positioned above the buttons */
      right: 20px;
      background: white;
      border: 2px solid #28a745;
      border-radius: 8px;
      padding: 15px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      z-index: 1000;
      font-family: Arial, sans-serif;
      width: 280px;
    }

    .settings-panel h3 {
      margin: 0 0 10px 0;
      color: #28a745;
      font-size: 16px;
    }

    .setting-group {
      margin-bottom: 10px;
    }

    .setting-group label {
      display: inline-block;
      width: 60px;
      font-size: 12px;
      font-weight: bold;
    }

    .setting-group input {
      width: 60px;
      padding: 4px;
      border: 1px solid #ddd;
      border-radius: 3px;
      margin-right: 5px;
    }

    .setting-group select {
      width: 50px;
      padding: 2px;
      border: 1px solid #ddd;
      border-radius: 3px;
    }

    .apply-btn {
      background-color: #28a745;
      color: white;
      border: none;
      padding: 6px 12px;
      border-radius: 4px;
      cursor: pointer;
      font-size: 12px;
      margin-top: 10px;
    }

    .apply-btn:hover {
      background-color: #218838;
    }

    .toggle-settings {
      position: fixed;
      bottom: 18px;
      right: 100px;
      background-color: #28a745;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      cursor: pointer;
      font-size: 14px;
      z-index: 1001;
      transition: background-color 0.3s;
    }

    .toggle-settings:hover {
      background-color: #218838;
    }

    @media print {
      #print-button, .settings-panel, .toggle-settings {
        display: none;
      }

      #cetak {
        display: none;
      }
    }

    @page {
      margin: 20px 50px;
    }

    #print-button {
      position: fixed;
      bottom: 20px;
      right: 20px;
      padding: 10px 20px;
      font-size: 14px;
      cursor: pointer;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 5px;
      transition: background-color 0.3s;
    }

    #print-button:hover {
      background-color: #0056b3;
    }
  </style>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
</head>

<body>
<!-- Settings Button -->
<button class="toggle-settings" id="toggle-settings">⚙️ Settings</button>

<button id="print-button">Print</button>

<!-- Settings Panel -->
<div class="settings-panel" id="settings-panel" style="display: none;">
  <h3>Label Settings</h3>

  <div class="setting-group">
    <label for="label-width">Lebar:</label>
    <input type="number" id="label-width" value="38" min="10" max="200">
    <select id="width-unit">
      <option value="mm">mm</option>
      <option value="cm">cm</option>
      <option value="px">px</option>
    </select>
  </div>

  <div class="setting-group">
    <label for="label-height">Tinggi:</label>
    <input type="number" id="label-height" value="38" min="10" max="200">
    <select id="height-unit">
      <option value="mm">mm</option>
      <option value="cm">cm</option>
      <option value="px">px</option>
    </select>
  </div>

  <div class="setting-group">
    <label for="font-size">Font:</label>
    <input type="number" id="font-size" value="8" min="6" max="20">
    <select id="font-unit">
      <option value="px">px</option>
      <option value="pt">pt</option>
    </select>
  </div>

  <div class="setting-group">
    <label for="padding">Padding:</label>
    <input type="number" id="padding" value="2" min="0" max="10">
    <select id="padding-unit">
      <option value="mm">mm</option>
      <option value="px">px</option>
    </select>
  </div>

{{--  <div class="setting-group">--}}
{{--    <label for="labels-per-row">Per Baris:</label>--}}
{{--    <input type="number" id="labels-per-row" value="4" min="1" max="10">--}}
{{--  </div>--}}

  <button class="apply-btn" onclick="applySettings()">Terapkan</button>
  <button class="apply-btn" onclick="resetSettings()" style="background-color: #dc3545;">Reset</button>
</div>

<div id="printable" class="container">
  @php
    $label_count = count($get_data);
  @endphp

  @if ($label_count > 0)
    <div class="label-row">
      @for ($n = 0; $n < $label_count; $n++)
        <div class="label-container" id="label-{{ $n }}">
          <table style="width: 100%; height: 100%;">
            <tr>
              <td>
                <table style="width: 100%; height: 100%; border-collapse: collapse;">
                  <tr>
                    <td id="no_rekamedis" style="text-align: center;">
                      <b style="font-size: 12px;">{{ $get_data[$n]->noregister_permohonan_uji_klinik }}</b>
                      <hr style="border: 1px solid black; margin: 2px 0;"> <!-- Garis horizontal -->
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                          <td id="nama_pasien" style="text-align: center;">
                            <b style="font-size: 10px;">{{ $get_data[$n]->pasien->nama_pasien }}</b>
                          </td>
                        </tr>
                        <tr>
                          <td id="tgllahir_pasien" style="text-align: center;">
                            <b style="font-size: 10px;">{{ date('d/m/Y', strtotime($get_data[$n]->pasien->tgllahir_pasien)) }}</b>
                          </td>
                        </tr>
                        <tr>
                          <td id="jenis_pemeriksaan" style="text-align: center;">
                            <b style="font-size: 10px;">{{ $get_data[$n]->pasien->alamat_pasien }}</b>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </div>
        @if (($n + 1) % 4 === 0) <!-- Check for every 4 labels -->
    </div><div class="label-row"> <!-- Close and open new row -->
      @endif
      @endfor
    </div>
  @endif
</div>

<script>
  $(function() {
    // Initialize draggable feature for all labels
    initializeDraggable();

    // Print button functionality
    $('#print-button').on('click', function() {
      window.print();
    });

    // Toggle settings panel
    $('#toggle-settings').on('click', function() {
      $('#settings-panel').toggle();
    });
  });

  function applySettings() {
    const width = $('#label-width').val();
    const widthUnit = $('#width-unit').val();
    const height = $('#label-height').val();
    const heightUnit = $('#height-unit').val();
    const fontSize = $('#font-size').val();
    const fontUnit = $('#font-unit').val();
    const padding = $('#padding').val();
    const paddingUnit = $('#padding-unit').val();
    // const labelsPerRow = $('#labels-per-row').val();

    // Apply new dimensions to all labels
    $('.label-container').css({
      'width': width + widthUnit,
      'height': height + heightUnit,
      'font-size': fontSize + fontUnit,
      'padding': padding + paddingUnit
    });

    // Update labels per row
    updateLabelsPerRow(labelsPerRow);

    // Re-initialize draggable after layout change
    reinitializeDraggable();

    // Save settings to localStorage (if supported)
    if (typeof(Storage) !== "undefined") {
      localStorage.setItem('labelSettings', JSON.stringify({
        width: width,
        widthUnit: widthUnit,
        height: height,
        heightUnit: heightUnit,
        fontSize: fontSize,
        fontUnit: fontUnit,
        padding: padding,
        paddingUnit: paddingUnit,
        labelsPerRow: labelsPerRow
      }));
    }

    alert('Pengaturan berhasil diterapkan!');
  }

  function resetSettings() {
    $('#label-width').val(38);
    $('#width-unit').val('mm');
    $('#label-height').val(38);
    $('#height-unit').val('mm');
    $('#font-size').val(8);
    $('#font-unit').val('px');
    $('#padding').val(2);
    $('#padding-unit').val('mm');
    // $('#labels-per-row').val(4);

    applySettings();
  }

  function updateLabelsPerRow(labelsPerRow) {
    // Remove existing CSS rules for labels per row
    $('style[id="dynamic-label-rules"]').remove();

    // Create new CSS rules for dynamic labels per row
    let css = `
                .label-container:nth-child(${labelsPerRow}n) {
                    margin-right: 0mm !important;
                }
                .label-container:not(:nth-child(${labelsPerRow}n)) {
                    margin-right: 2mm !important;
                }
            `;

    $('<style id="dynamic-label-rules">').html(css).appendTo('head');
  }

  // Enhanced draggable initialization for dynamic labels
  function initializeDraggable() {
    $(".label-container").draggable({
      containment: "#printable",
      cursor: "move",
      opacity: 0.7,
      revert: false
    });
  }

  // Re-initialize draggable after settings change
  function reinitializeDraggable() {
    $(".label-container").draggable("destroy");
    initializeDraggable();
  }

  // Load saved settings on page load
  $(document).ready(function() {
    if (typeof(Storage) !== "undefined") {
      const savedSettings = localStorage.getItem('labelSettings');
      if (savedSettings) {
        const settings = JSON.parse(savedSettings);
        $('#label-width').val(settings.width);
        $('#width-unit').val(settings.widthUnit);
        $('#label-height').val(settings.height);
        $('#height-unit').val(settings.heightUnit);
        $('#font-size').val(settings.fontSize);
        $('#font-unit').val(settings.fontUnit);
        $('#padding').val(settings.padding);
        $('#padding-unit').val(settings.paddingUnit);
        // $('#labels-per-row').val(settings.labelsPerRow);

        // Apply the loaded settings
        applySettings();
      }
    }
  });
</script>

</body>

</html>
