<?php

use Illuminate\Support\Facades\DB;
use Smt\Masterweb\Models\StartNum;

function set_input_crud($val_type, $val_name, $value = null, $relation = null)
{
  //check kalo upload
  if ($val_type == "upload") {
    $ar =  '<input type="file" class="form-control dropify" name="' . $val_name . '" id="' . $val_name . '">';
    return $ar;
  }

  if (isset($relation[$val_name])) {
    $get_relation = $relation[$val_name];
    $data_relation = DB::table($get_relation[0])->whereNull('deleted_at')->get(); //get tabel


    $ar =   '<select name="' . $val_name . '" id="' . $val_name . '" class="form-control selected2">
                        <option value="">Pilih</option>';
    foreach ($data_relation as $item) {
      $id_relation = $get_relation[1];
      $val_relation = $get_relation[2];

      if ($item->$id_relation == $value) {
        $is_selected = "selected";
      } else {
        $is_selected = null;
      }


      $ar .= '<option value="' . $item->$id_relation . '" ' . $is_selected . '>' . $item->$val_relation . '</option>';
    }
    $ar .=  '</select>';
  } elseif ($val_type == "\BigInt") {
    $ar =  '<input type="number" class="form-control" name="' . $val_name . '" value="' . $value . '" id="' . $val_name . '" required>';
  } elseif ($val_type == "\String") {
    $ar = '<input type="text" class="form-control" name="' . $val_name . '" value="' . $value . '" id="' . $val_name . '" required>';
  } elseif ($val_type == "\Text") {
    $ar = '<textarea class="form-control" name="' . $val_name . '" id="' . $val_name . '" cols="30" rows="10" required>' . $value . '</textarea>';
  } elseif ($val_type == "\Date") {
    $ar = '<input type="date" class="form-control" name="' . $val_name . '" id="' . $val_name . '" value="' . $value . '" required>';
  } elseif ($val_type == "\Boolean") {
    //$cekhed = null;
    if ($value == "1") {
      $cekhed = "checked";
    } else {
      $cekhed = null;
    }


    $ar = '<div class="form-check">
                        <label class="form-check-label">
                            <input type="checkbox" name="' . $val_name . '" id="' . $val_name . '" value="1" class="form-check-input" ' . $cekhed . '>
                            Aktif
                            <i class="input-helper"></i>
                        </label>
                    </div>';
  } else {
    $ar = "belum terdefinisi " . $val_type;
  }

  return $ar;
}

function rupiah($angka)
{

  $hasil_rupiah = "Rp. " . number_format($angka, 0, ',', '.');
  return $hasil_rupiah;
}



function rubahNilaikeForm($value)
{
  //rubah pangkat




  // foreach ($value as $key => $value_one) {
  # code...
  // print_r($value_one);
  // }

  // print_r($value[9]);
  $value =str_replace("<br>","\n", $value);
  $value = str_replace("<sup>", "^(", $value);
  $value = str_replace("</sup>", ")", $value);
  $value = str_replace("&#60;", "<", $value);
  $value = str_replace("&#62;", ">", $value);
  $value = str_replace("&#8804;","≤", $value);
  $value = str_replace("&#8805;", "≥", $value);
  $value = str_replace("&nbsp;", " ", $value);


  return $value;
  # code...
}

function getRomawi($bln){
  switch ($bln){
      case 1:
          return "I";
          break;
      case 2:
          return "II";
          break;
      case 3:
          return "III";
          break;
      case 4:
          return "IV";
          break;
      case 5:
          return "V";
          break;
      case 6:
          return "VI";
          break;
      case 7:
          return "VII";
          break;
      case 8:
          return "VIII";
          break;
      case 9:
          return "IX";
          break;
      case 10:
          return "X";
          break;
      case 11:
          return "XI";
          break;
      case 12:
          return "XII";
          break;
  }
}

function rubahNilaikeHtml($value)
{
  //rubah pangkat


  // dd($value);

  $value = str_replace("<", "&#60;", $value);
  $value = str_replace(">", "&#62;", $value);
  $value = str_replace("≥","&#8805;", $value);
  $value = str_replace("≤","&#8804;", $value);
  $value = str_replace("^(", "<sup>", $value);
  $value = str_replace(")", "</sup>", $value);
  $value =str_replace("\n", '<br>', $value);
  $value = str_replace( " ","&nbsp;", $value);
  return $value;
  # code...
}

function cek_hasil_color($hasil, $min, $max, $equal, $id, $offset_baku_mutu = "default")
{


  $delete_space=str_replace(" ", "", $hasil);



  if (isset($delete_space) && $delete_space != "" && $delete_space != "-") {
    if ($offset_baku_mutu == "false") {
      $hasil_last = "<span  id='" . $id . "'>" . $hasil . "</span>";
    } elseif ($offset_baku_mutu == "true") {
      $hasil_last = "<span id='" . $id . "' style='color:red'>" . $hasil . '*</span> ';
    } else {
      if ($hasil != "-") {
        $hasil_last = "<span  id='" . $id . "'>" . $hasil . "</span>";
        if (isset($min)) {
          if ((float) $hasil < (float) $min) {

            $hasil_last = "<span id='" . $id . "' style='color:red'>" . $hasil . '*</span> ';
          }
        }

        if (isset($max)) {
          $result = rubahNilaikeForm($hasil);

          if (preg_match('/^(>)\s*\d+$/', $result)){
            $number = (int) preg_replace('/[^\d]/', '', $result);
            if ($max >= $number){
              $hasil_last = "<span id='" . $id . "' style='color:red'>" . $hasil . '*</span> ';
            }
          }

          if ((float) $hasil > (float) $max) {
            $hasil_last = "<span id='" . $id . "' style='color:red'>" . $hasil . '*</span> ';
          }
        }

        if (isset($equal)) {


          // $hasil=html_entity_decode($hasil);
          // $equal=html_entity_decode($equal);
          $hasil_last = $hasil;
          $hasil = str_replace('&nbsp;', ' ', $hasil);
          $hasil = str_replace(' ', '', $hasil);
          $equal = str_replace(' ', '', $equal);
          $equal = str_replace('&nbsp;', ' ', $equal);
          $equal = str_replace(' ', '', $equal);
          $hasil = str_replace(' ', '', $hasil);
          // dd($id );

          // $hasil = preg_replace('/\s+/', '', strtoupper(trim($hasil)));
          // $equal = preg_replace('/\s+/', '', strtoupper(trim($equal)));

          if ($hasil != $equal) {
            $hasil = $hasil_last;
            $hasil_last = "<span  id='" . $id . "' style='color:red'>" . $hasil . '*</span> ';

          }

        }
      } else {
        $hasil_last = "<span  id='" . $id . "'>-</span>";
      }
    }
  } else {
    $hasil_last = "<span  id='" . $id . "'>-</span>";
  }
  return $hasil_last;
}

function cek_hasil_color_mikro($hasil, $min, $max, $equal, $id, $offset_baku_mutu = "default")
{


  $delete_space=str_replace(" ", "", $hasil);



  if (isset($delete_space) && $delete_space != "" && $delete_space != "-") {
    if ($offset_baku_mutu == "false") {
      $hasil_last = "<span  id='" . $id . "'>" . $hasil . "</span>";
    } elseif ($offset_baku_mutu == "true") {
      $hasil_last = "<span id='" . $id . "' style='display: block; color:red; background-color: yellow;'>" . $hasil . '*</span> ';
    } else {
      if ($hasil != "-") {
        $hasil_last = "<span  id='" . $id . "'>" . $hasil . "</span>";
        if (isset($min)) {
          if ((float) $hasil < (float) $min) {

            $hasil_last = "<span id='" . $id . "' style='display: block; color:red; background-color: yellow;'>" . $hasil . '*</span> ';
          }
        }

        if (isset($max)) {
          if ((float) $hasil > (float) $max) {
            $hasil_last = "<span id='" . $id . "' style='display: block; color:red; background-color: yellow;'>" . $hasil . '*</span> ';
          }
        }

        if (isset($equal)) {


          // $hasil=html_entity_decode($hasil);
          // $equal=html_entity_decode($equal);
          $hasil_last = $hasil;
          $hasil = str_replace('&nbsp;', ' ', $hasil);
          $hasil = str_replace(' ', '', $hasil);
          $equal = str_replace(' ', '', $equal);
          $equal = str_replace('&nbsp;', ' ', $equal);
          $equal = str_replace(' ', '', $equal);
          $hasil = str_replace(' ', '', $hasil);
          // dd($id );

          // $hasil = preg_replace('/\s+/', '', strtoupper(trim($hasil)));
          // $equal = preg_replace('/\s+/', '', strtoupper(trim($equal)));

          if ($hasil != $equal) {
            $hasil = $hasil_last;
            $hasil_last = "<span  id='" . $id . "' style='display: block; color:red; background-color: yellow;'>" . $hasil . '*</span> ';

          }

        }
      } else {
        $hasil_last = "<span  id='" . $id . "'>-</span>";
      }
    }
  } else {
    $hasil_last = "<span  id='" . $id . "'>-</span>";
  }
  return $hasil_last;
}

function penyebut($nilai)
{
  $nilai = abs($nilai);
  $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
  $temp = "";
  if ($nilai < 12) {
    $temp = " " . $huruf[$nilai];
  } else if ($nilai < 20) {
    $temp = penyebut($nilai - 10) . " belas";
  } else if ($nilai < 100) {
    $temp = penyebut($nilai / 10) . " puluh" . penyebut($nilai % 10);
  } else if ($nilai < 200) {
    $temp = " seratus" . penyebut($nilai - 100);
  } else if ($nilai < 1000) {
    $temp = penyebut($nilai / 100) . " ratus" . penyebut($nilai % 100);
  } else if ($nilai < 2000) {
    $temp = " seribu" . penyebut($nilai - 1000);
  } else if ($nilai < 1000000) {
    $temp = penyebut($nilai / 1000) . " ribu" . penyebut($nilai % 1000);
  } else if ($nilai < 1000000000) {
    $temp = penyebut($nilai / 1000000) . " juta" . penyebut($nilai % 1000000);
  } else if ($nilai < 1000000000000) {
    $temp = penyebut($nilai / 1000000000) . " milyar" . penyebut(fmod($nilai, 1000000000));
  } else if ($nilai < 1000000000000000) {
    $temp = penyebut($nilai / 1000000000000) . " trilyun" . penyebut(fmod($nilai, 1000000000000));
  }
  return $temp;
}

function terbilang($nilai)
{
  if ($nilai < 0) {
    $hasil = "minus " . trim(penyebut($nilai));
  } else {
    $hasil = trim(penyebut($nilai));
  }
  return ucwords($hasil . " rupiah");
}
//end generator helper

function create_link($url)
{
  $url = strip_tags($url);
  $url = str_replace(" ", "-", $url);
  $url = str_replace("!", "", $url);
  $url = str_replace("@", "", $url);
  $url = str_replace("#", "", $url);
  $url = str_replace("$", "", $url);
  $url = str_replace("%", "", $url);
  $url = str_replace("^", "", $url);
  $url = str_replace("&", "", $url);
  $url = str_replace("*", "", $url);
  $url = str_replace("(", "", $url);
  $url = str_replace(")", "", $url);
  $url = str_replace("_", "", $url);
  $url = str_replace("+", "", $url);
  $url = str_replace("=", "", $url);
  $url = str_replace("{", "", $url);
  $url = str_replace("}", "", $url);
  $url = str_replace("[", "", $url);
  $url = str_replace("]", "", $url);
  $url = str_replace("|", "", $url);
  $url = str_replace('"', "", $url);
  $url = str_replace(";", "", $url);
  $url = str_replace(">", "", $url);
  $url = str_replace('<', "", $url);
  $url = str_replace("?", "", $url);
  $url = str_replace("/", "", $url);
  $url = str_replace('~', "", $url);
  $url = str_replace("`", "", $url);
  $url = str_replace(".", "", $url);
  $url = str_replace(",", "", $url);
  $url = str_replace(":", "", $url);
  $url = str_replace("'", "", $url);
  $url = addslashes($url);
  $url = strtolower($url);

  return $url;
}

//function fbulan
function fbulan($bulan)
{
  if ($bulan == "01") {
    $bln = "Januari";
  } else if ($bulan == "02") {
    $bln = "Februari";
  } else if ($bulan == "03") {
    $bln = "Maret";
  } else if ($bulan == "04") {
    $bln = "April";
  } else if ($bulan == "05") {
    $bln = "Mei";
  } else if ($bulan == "06") {
    $bln = "Juni";
  } else if ($bulan == "07") {
    $bln = "Juli";
  } else if ($bulan == "08") {
    $bln = "Agustus";
  } else if ($bulan == "09") {
    $bln = "September";
  } else if ($bulan == "10") {
    $bln = "Oktober";
  } else if ($bulan == "11") {
    $bln = "November";
  } else if ($bulan == "12") {
    $bln = "Desember";
  } else {
    $bln = "";
  }
  return $bln;
}

//function fdate
function fdate($value, $format)
{
  if ($value != "") {
    list($thn, $bln, $tgl) = explode("-", $value);

    switch ($format) {
      case "DDMMYYYY":
        $return = $tgl . " " . Smt::fbulan($bln) . " " . $thn;
        break;
        //new case
      case "DDMM":
        $return = $tgl . " " . Smt::fbulan($bln);
        break;
      case "DD":
        $return = $tgl;
        break;
      case "MM":
        $return = $bln;
        break;
      case "YYYYY":
        $return = $thn;
        break;
      case "mm":
        $return = Smt::fbulan($bln);
        break;
      case "HHDDMMYYYY":
        $jam = explode(" ", $value)[1];
        $tgl = explode(" ", $tgl)[0];
        list($H, $M, $S) = explode(":", $jam);
        $return = $tgl . " " . Smt::fbulan($bln) . " " . $thn . " | " . $H . ":" . $M;
        break;
    }
  } else {
    $return = "";
  }
  return $return;
}

function get_num_phone($nohp)
{
  // kadang ada penulisan no hp 0811 239 345
  $nohp = str_replace(" ", "", $nohp);
  // kadang ada penulisan no hp (0274) 778787
  $nohp = str_replace("(", "", $nohp);
  // kadang ada penulisan no hp (0274) 778787
  $nohp = str_replace(")", "", $nohp);
  // kadang ada penulisan no hp 0811.239.345
  $nohp = str_replace(".", "", $nohp);

  // cek apakah no hp mengandung karakter + dan 0-9
  if (!preg_match('/[^+0-9]/', trim($nohp))) {
    // cek apakah no hp karakter 1-3 adalah +62
    if (substr(trim($nohp), 0, 3) == '+62') {
      $hp = trim($nohp);
    }
    // cek apakah no hp karakter 1 adalah 0
    elseif (substr(trim($nohp), 0, 1) == '0') {
      $hp = '+62' . substr(trim($nohp), 1);
    }
  }
  return $hp;
}

function get_img($news_content = NULL)
{


  $dom = new \DOMDocument();





  if ($news_content == "") {


    return NULL;
  }





  libxml_use_internal_errors(true);


  $dom->loadHTML($news_content);


  libxml_use_internal_errors(false);


  $img_nodes = $dom->getElementsByTagName('img');





  $img_link = NULL;


  foreach ($img_nodes as $link) {


    $img_link = $link->getAttribute('src');
    break;
  }





  return $img_link;
}

function img_empty($asset, $value)
{
  if (empty($value)) {
    $img = asset('assets/public/images/intro/blank.png');
  } else {
    $img = asset($asset . $value);
  }
  return $img;
}
//get title
function name_url($url_segment)
{
  $get_name_control = DB::table('ms_menus')->where('link', '=', $url_segment)->first();
  $url = $get_name_control->name;
  return $url;
}

//get link controller
function name_link($url_segment)
{
  if ($url_segment == NULL) {
    $url_segment = '/';
  }
  $get_name_control = DB::table('ms_menus')->where('link', '=', $url_segment)->first();
  $url = $get_name_control->link;
  return $url;
}

function name_controller($value = null)
{
  $get_name = DB::table('ms_menus')->where('link', '=', Request::segment(1))->where('deleted_at', NULL)->first();
  $name = $get_name->type;
  return $name;
}

function get_type($value = null)
{
  if (Request::segment(1) == NULL) {
    return '1';
  }
  $get_name = DB::table('ms_menus')->where('link', '=', Request::segment(1))->where('deleted_at', NULL)->first();
  $name = $get_name->type;
  return $name;
}

function get_menuid($type = null)
{
  if ($type == NULL) {
    $get_name = DB::table('ms_menus')->where('link', '=', Request::segment(1))->where('deleted_at', NULL)->first();
  } else {
    $get_name = DB::table('ms_menus')->where('type', '=', $type)->where('deleted_at', NULL)->first();
  }
  $name = $get_name->id;
  return $name;
}

function getPaketKlinik($namePaket)
{
   $paketKlinik = \Illuminate\Support\Facades\DB::table('ms_parameter_paket_klinik')->where('name_parameter_paket_klinik', 'like', '%'.$namePaket.'%')->whereNull('deleted_at')->first();
   return $paketKlinik;
}


function cekValue($form_result, $params, $num)
{
  if (isset($form_result[$num][$params])) {
    if ($params == "date_test") {
      return Carbon\Carbon::createFromFormat('Y-m-d H:i:s',  $form_result[$num][$params])->format('d/m/Y');
    } else {
      return $form_result[$num][$params];
    }
  } else {
    if ($params == "date_test") {
      return Carbon\Carbon::now()->format('d/m/Y');
    } else {
      return '';
    }
  }
}

function get_linkmenu($type = null)
{
  if ($type == NULL) {
    $get_name_control = DB::table('ms_menus')->where('link', '=', Request::segment(1))->first();
  } else {
    $get_name_control = DB::table('ms_menus')->where('type', '=', $type)->first();
  }
  $url = $get_name_control->link;
  return $url;
}

function get_linkname()
{
  $url_segment = Request::segment(1);
  if ($url_segment == NULL) {
    $url_segment = '';
  }
  $get_name_control = DB::table('ms_menus')->where('link', '=', $url_segment)->where('deleted_at', NULL)->first();
  $url = $get_name_control->name;
  return $url;
}

function getLayout($type, $module)
{
  if ($type == "1") {
?>
Lorem, ipsum dolor sit amet consectetur adipisicing elit. Temporibus, commodi aut! Aperiam, alias? Cumque omnis
quibusdam nostrum maiores ipsum, quasi officia inventore doloremque accusamus quis doloribus sit quos quae dolorem?
<?php
  }
}

function GetLayoutModule($column, $modules)
{
  return view('masterweb::module.admin.layoutmodule.columns', compact('column', 'modules'));
}

function GetLayoutModulePublic($column, $modules)
{
  return view('masterweb::module.admin.layoutmodule.column_modules', compact('column', 'modules'));
}

function getModule($module)
{
  $getModule = DB::table('ms_module')->where('id', '=', $module)->first();
  ?>
<div class="card rounded border mb-2">
    <div class="card-body p-3 moduleId" data-id="<?= $module ?>">
        <div class="media">
            <i class="fa fa-news icon-sm text-primary align-self-center mr-3"></i>
            <div class="media-body">
                <h6 class="mb-1"><?= $getModule->name ?></h6>
                <p class="mb-0 text-muted">
                    <?= $getModule->module ?>
                </p>
            </div>
        </div>
    </div>
</div>
<?php
}

function smt_reference($kode, $value = null)
{

  switch ($kode) {
    case 'PUBLISH':
      $data = array(
        '1' => 'Aktif',
        '0' => 'Tidak Aktif'
      );
      break;

    case 'CONTENTREF':
      $data = array(
        '1' => 'Full',
        '0' => 'List'
      );
      break;

    case 'SEKS':
      $data = array(
        'L' => 'Laki-laki',
        'P' => 'Perempuan',
        'l' => 'Laki-laki',
        'p' => 'Perempuan'
      );
      break;

    case 'AGAMA':
      $data = array(
        '1' => 'Islam',
        '2' => 'Kristen Protestan',
        '3' => 'Katolik',
        '4' => 'Hindu',
        '5' => 'Budha',
        '6' => 'Konghuchu'
      );
      break;

    case 'JENJANG':
      $data = array(
        'SD' => 'SD/MI Sederajat',
        'SMP' => 'SMP/MTS Sederajat',
        'SMA' => 'SMA/SMK/MK Sederajat',
      );
    case 'TYPE_PRODUCT':
      $data = array(
        'A' => 'Alat',
        'B' => 'Bahan Habis Pakai'
      );

    case 'STATUS_SAMPLE':
      $data = array(
        '0' => 'Menunggu',
        'A' => 'Permintaan Pemeriksaan',
        'B' => 'Persiapan Sampel',
        'C' => 'Pengambilan Sampel	',
        'D' => 'Penerimaan Sampel',
        'E' => 'Penanganan Sampel',
        'F' => 'Persiapan Reagen',
        'G' => 'Pipetase / Inokulasi',
        'H' => 'Preparasi',
        'I' => 'Inkubasi',
        'J' => 'Pemeriksaan Alat',
        'K' => 'Baca Hasil',
        'L' => 'Pelaporan Hasil',
        'M' => 'Pengetikan Hasil',
        'N' => 'Verifikasi Hasil',
        'O' => 'Pengesahan Hasil',
        '1' => 'Pengesahan Hasil',
      );
  }

  if ($value == null) {
    return $data;
  } else {
    return $data[$value];
  }
}

function isSelected($a, $b)
{
  if ($a == $b) {
    return "selected";
  }
}

function Info_umum($value)
{
  # code...
  if ($value == 1) {
    return "Search Engine";
  } elseif ($value == 2) {
    return "Mailing Partner";
  } elseif ($value == 3) {
    return "News Letter";
  } elseif ($value == 4) {
    return "Facebook";
  } else {
    return "Twitter";
  }
}

function getAction($action)
{
  $user = Auth()->user();
  $level = $user->level;

  $role = \Smt\Masterweb\Models\AdminMenu::where('ms_menuadm.link', '=',  "/" . Request::segment(1))
    ->Orwhere('ms_menuadm.link', '=',  Request::segment(1))
    ->join('tb_role', function ($join) use ($level) {
      $join->on('tb_role.menu_id', '=', 'ms_menuadm.id')
        ->where('privilege_id', '=', $level)
        ->whereNull('ms_menuadm.deleted_at')
        ->whereNull('tb_role.deleted_at');
    })
    ->first();

  $UUIDv4 = '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i';
  //  ;

  if ($action != "create" && $action != "read" && $action != "update" && $action != "delete") {
    if (preg_match($UUIDv4, $action) || !isset($action)) {

      $access = \Smt\Masterweb\Models\PrivilegeMenuRole::where(['tb_privilege_menu_role.privilege_id' => $level])
        ->join('tb_privilege_menu', function ($join) {
          $join->on('tb_privilege_menu.id_privilege_features', '=', 'tb_privilege_menu_role.id_privilege_menu')
            // ->where('tb_privilege_menu.sub_link', 'like', "%" . substr(Request::segment(1),0,strlen(Request::segment(1))))
            // ->orWhere('tb_privilege_menu.sub_link', 'like', "%" . substr(Request::segment(1),0,strlen(Request::segment(1))))
            ->whereNull('tb_privilege_menu.deleted_at')
            ->whereNull('tb_privilege_menu_role.deleted_at');
        })
        ->join('ms_menuadm', function ($join) {
          $join->on('tb_privilege_menu.menu_id', '=', 'ms_menuadm.id')
            ->whereNull('tb_privilege_menu.deleted_at')
            ->where('ms_menuadm.link', 'like', "%" . substr(Request::segment(1), 0, strlen(Request::segment(1)) - 1))
            ->orWhere('ms_menuadm.link', 'like', "%" . substr(Request::segment(1), 0, strlen(Request::segment(1))))
            ->whereNull('ms_menuadm.deleted_at');
        })

        // ->where('tb_privilege_menu.sub_link', 'like', "%".$action."%")
        // ->where('tb_privilege_menu.privilege_id', '=', $level)
        ->get();

      if (count($access) == 0) {

        $access = \Smt\Masterweb\Models\PrivilegeMenuRole::where(['tb_privilege_menu_role.privilege_id' => $level])
          ->join('tb_privilege_menu', function ($join) {
            $join->on('tb_privilege_menu.id_privilege_features', '=', 'tb_privilege_menu_role.id_privilege_menu')
              ->where('tb_privilege_menu.sub_link', 'like', "%" . substr(Request::segment(1), 0, strlen(Request::segment(1))))
              // ->orWhere('tb_privilege_menu.sub_link', 'like', "%" . substr(Request::segment(1),0,strlen(Request::segment(1))))
              ->whereNull('tb_privilege_menu.deleted_at')
              ->whereNull('tb_privilege_menu_role.deleted_at');
          })
          ->join('ms_menuadm', function ($join) {
            $join->on('tb_privilege_menu.menu_id', '=', 'ms_menuadm.id')
              ->whereNull('tb_privilege_menu.deleted_at')
              // ->where('ms_menuadm.link', 'like', "%" . substr(Request::segment(1),0,strlen(Request::segment(1))-1))
              // ->orWhere('ms_menuadm.link', 'like', "%" . substr(Request::segment(1),0,strlen(Request::segment(1))))
              ->whereNull('ms_menuadm.deleted_at');
          })

          // ->where('tb_privilege_menu.sub_link', 'like', "%".$action."%")
          // ->where('tb_privilege_menu.privilege_id', '=', $level)
          ->get();
      }
    } else {
      $access = \Smt\Masterweb\Models\PrivilegeMenuRole::where(['tb_privilege_menu_role.privilege_id' => $level])
        ->join('tb_privilege_menu', function ($join) {
          $join->on('tb_privilege_menu.id_privilege_features', '=', 'tb_privilege_menu_role.id_privilege_menu')

            ->whereNull('tb_privilege_menu.deleted_at')
            ->whereNull('tb_privilege_menu_role.deleted_at');
        })
        ->join('ms_menuadm', function ($join) {
          $join->on('tb_privilege_menu.menu_id', '=', 'ms_menuadm.id')
            ->whereNull('tb_privilege_menu.deleted_at')
            ->where('ms_menuadm.link', 'like', "%" . substr(Request::segment(1), 0, strlen(Request::segment(1)) - 1))
            ->orWhere('ms_menuadm.link', 'like', "%" . substr(Request::segment(1), 0, strlen(Request::segment(1))))
            ->whereNull('ms_menuadm.deleted_at');
        })

        ->where('tb_privilege_menu.sub_link', 'like', "%" . $action . "%")
        // ->where('tb_privilege_menu.privilege_id', '=', $level)
        ->get();
    }

    $user  = Auth()->user()->getlevel()->first();
    $level = $user->level;

    if (!isset($role)) {
      return true;
    } else {

      if (!isset($role[$action])) {
        if (($role['link'] == "/device-management" &&
            ($action == 'live' || $action == 'live2' || $action == 'activation' || $action == 'connect' || $action == 'report' || $action == 'edit_offset' ||  (($level == "ADMD" || $level == "DEV" || $level == "MANU" || $level == "MM") && $action == 'setting-regresi')))
          || ($role['link'] == "/report-management" && $level != "DSB")
          || $role['link'] == "/biodata"
          || $role['link'] == "adm-password"

        ) {
          return true;
        } elseif (($level == "ADMD" || $level == "DEV" || $level == "MANU" || $level == "MM") && $role['link'] == "/distributor-management") {
          return true;
        } elseif (($level == "ADMD" || $level == "DEV" || $level == "MANU" || $level == "MM" || $level == "DSB") && $role['link'] == "/client-management") {
          return true;
        } elseif (($level == "ADMD" || $level == "DEV" || $level == "MANU" || $level == "MM" || $level == "DSB" || $level == "CLI") && $role['link'] == "/user-client-management") {
          return true;
        } else {

          return false;
        }
      } else {

        if (($role['link'] == "/device-management" &&
            ($action == 'live' || $action == 'live2' || $action == 'activation' || (($level == "ADMD" || $level == "DEV" || $level == "MANU" || $level == "MM") && $action == 'connect') || $action == 'report' || $action == 'edit_offset' || (($level == "ADMD" || $level == "DEV" || $level == "MANU" || $level == "MM") && $action == 'setting-regresi')))
          || ($role['link'] == "/report-management" && $level != "DSB")
          || $role['link'] == "/biodata"
          || $role['link'] == "/adm-password"

        ) {


          return true;
        } elseif (($level == "ADMD" || $level == "DEV" || $level == "MANU" || $level == "MM") && $role['link'] == "/distributor-management") {
          if ($action == "read") {
            return true;
          } else {
            if ($role[$action] == "1") {
              return true;
            } else {
              return false;
            }
          }
        } elseif (($level == "ADMD" || $level == "DEV" || $level == "MANU" || $level == "MM" || $level == "DSB") && $role['link'] == "/client-management") {
          if ($action == "read") {
            return true;
          } else {
            if ($role[$action] == "1") {
              return true;
            } else {
              return false;
            }
          }
        } elseif (($level == "ADMD" || $level == "DEV" || $level == "MANU" || $level == "MM" || $level == "DSB" || $level == "CLI") && $role['link'] == "/user-client-management") {
          if ($action == "read") {
            return true;
          } else {
            if ($role[$action] == "1") {
              return true;
            } else {
              return false;
            }
          }
        } else {

          if ($role[$action] == "1") {
            return true;
          } else {
            return false;
          }
        }
      }
    }
  } else {
    $access = \Smt\Masterweb\Models\PrivilegeMenuRole::where(['tb_privilege_menu_role.privilege_id' => $level])
      ->join('tb_privilege_menu', function ($join) {
        $join->on('tb_privilege_menu.id_privilege_features', '=', 'tb_privilege_menu_role.id_privilege_menu')

          ->whereNull('tb_privilege_menu.deleted_at')
          ->whereNull('tb_privilege_menu_role.deleted_at');
      })
      ->join('ms_menuadm', function ($join) {
        $join->on('tb_privilege_menu.menu_id', '=', 'ms_menuadm.id')
          ->whereNull('tb_privilege_menu.deleted_at')
          ->where('ms_menuadm.link', 'like', "%" . substr(Request::segment(1), 0, strlen(Request::segment(1)) - 1))
          ->orWhere('ms_menuadm.link', 'like', "%" . substr(Request::segment(1), 0, strlen(Request::segment(1))))
          ->whereNull('ms_menuadm.deleted_at');
      })

      ->where('tb_privilege_menu.sub_link', 'like', "%" . $action . "%")
      // ->where('tb_privilege_menu.privilege_id', '=', $level)
      ->get();



    if (!isset($role)) {
      return true;
    } else {

      if (!isset($role[$action])) {
        if (($role['link'] == "/device-management" &&
            ($action == 'live' || $action == 'live2' || $action == 'activation' || $action == 'connect' || $action == 'report' || $action == 'edit_offset' ||  (($level == "ADMD" || $level == "DEV" || $level == "MANU" || $level == "MM") && $action == 'setting-regresi')))
          || ($role['link'] == "/report-management" && $level != "DSB")
          || $role['link'] == "/biodata"
          || $role['link'] == "adm-password"

        ) {
          return true;
        } elseif (($level == "ADMD" || $level == "DEV" || $level == "MANU" || $level == "MM") && $role['link'] == "/distributor-management") {
          return true;
        } elseif (($level == "ADMD" || $level == "DEV" || $level == "MANU" || $level == "MM" || $level == "DSB") && $role['link'] == "/client-management") {
          return true;
        } elseif (($level == "ADMD" || $level == "DEV" || $level == "MANU" || $level == "MM" || $level == "DSB" || $level == "CLI") && $role['link'] == "/user-client-management") {
          return true;
        } else {

          return false;
        }
      } else {

        if (($role['link'] == "/device-management" &&
            ($action == 'live' || $action == 'live2' || $action == 'activation' || (($level == "ADMD" || $level == "DEV" || $level == "MANU" || $level == "MM") && $action == 'connect') || $action == 'report' || $action == 'edit_offset' || (($level == "ADMD" || $level == "DEV" || $level == "MANU" || $level == "MM") && $action == 'setting-regresi')))
          || ($role['link'] == "/report-management" && $level != "DSB")
          || $role['link'] == "/biodata"
          || $role['link'] == "/adm-password"

        ) {


          return true;
        } elseif (($level == "ADMD" || $level == "DEV" || $level == "MANU" || $level == "MM") && $role['link'] == "/distributor-management") {
          if ($action == "read") {
            return true;
          } else {
            if ($role[$action] == "1") {
              return true;
            } else {
              return false;
            }
          }
        } elseif (($level == "ADMD" || $level == "DEV" || $level == "MANU" || $level == "MM" || $level == "DSB") && $role['link'] == "/client-management") {
          if ($action == "read") {
            return true;
          } else {
            if ($role[$action] == "1") {
              return true;
            } else {
              return false;
            }
          }
        } elseif (($level == "ADMD" || $level == "DEV" || $level == "MANU" || $level == "MM" || $level == "DSB" || $level == "CLI") && $role['link'] == "/user-client-management") {
          if ($action == "read") {
            return true;
          } else {
            if ($role[$action] == "1") {
              return true;
            } else {
              return false;
            }
          }
        } else {

          if ($role[$action] == "1") {
            return true;
          } else {
            return false;
          }
        }
      }
    }
  }



  // return view('masterweb::module.admin.laboratorium.coba',compact('role'));
  // return Excel::download(new UsersExport, 'users.xlsx');
}

function arrayToKomma($array,$name=null,$name2=null)
{

  if(count($array)>1){
    $string="";
    $i=0;

    // foreach ($array as $arrayName) {
    //   # code...
    // }
    $array_temp =[];


    // array_unique($array);
    foreach ($array as $arrayName) {
      # code...
      // if($i<(count($array)-1)){

        if($i<(count($array)-2)){
          if(isset($name)){

            if(isset($name2)){
              array_push($array_temp,$arrayName[$name][$name2]);
              // $string=$string.$arrayName[$name][$name2].", ";
            }else{
              array_push($array_temp,$arrayName[$name]);
              // $string=$string.$arrayName[$name].", ";
            }

          }else{
            array_push($array_temp,$arrayName);
            // $string=$string.$arrayName.", ";
          }
        }else{
          if(isset($name)){

            if(isset($name2)){

              array_push($array_temp,$arrayName[$name][$name2]);
              // $string=$string.$arrayName[$name][$name2]." ";
            }else{
              array_push($array_temp,$arrayName[$name]);
              // $string=$string.$arrayName[$name]." ";
            }
          }else{
            array_push($array_temp,$arrayName);
            // $string=$string.$arrayName." ";
          }
        }

      // }else{
      //   if(isset($name)){
      //     if(isset($name2)){
      //       $string=$string."dan ".$arrayName[$name][$name2];
      //     }else{
      //       $string=$string."dan ".$arrayName[$name];
      //     }
      //   }else{
      //     $string=$string."dan ".$arrayName;
      //   }
      // }
      $i++;
    }


    // dd($array_temp);

    $array_temp=array_filter($array_temp, fn($value) => !is_null($value) && $value !== '');

    $array_temp=array_unique($array_temp);
    $i=0;
    if(count($array_temp)>1){
      # code...
      foreach ($array_temp as $arrayName) {
        # code...
        if($i<(count($array)-2)){
          if($i<(count($array_temp)-1)){
            $string=$string.$arrayName.", ";
          }else{
            $string=$string.$arrayName." ";
          }
        }else{
          $string=$string." dan ".$arrayName;
        }

        $i++;

      }

    }else{
      $string=$array_temp[0];
    }
    return $string;
  }elseif(count($array)==1){
    if(isset($name)){
      if(isset($name2)){
        return $array[0][$name][$name2];
      }else{
        return $array[0][$name];
      }
    }else{
      return $array[0];
    }
  }else{
    return "";
  }
}


function getSpesialAction($parent, $action, $id_device = null)
{
  $user = Auth()->user();
  $level = $user->level;

  $privilage = \Smt\Masterweb\Models\Privileges::where(['id' => $level])->first();

  $access = \Smt\Masterweb\Models\PrivilegeMenuRole::where(['privilege_id' => $privilage->id])
    ->join('tb_privilege_menu', function ($join) {
      $join->on('tb_privilege_menu.id_privilege_features', '=', 'tb_privilege_menu_role.id_privilege_menu')

        ->whereNull('tb_privilege_menu.deleted_at')
        ->whereNull('tb_privilege_menu_role.deleted_at');
    })
    ->where('sub_link', '=', $action)
    ->first();


  if ($access != null) {
    if ($access->value) {
      return true;
    } else {
      return false;
    }
  } else {
    return false;
  }





  // $role = \Smt\Masterweb\Models\Role::where(['privilege_id'=>$level])
  // ->join('ms_menuadm', function ($join) {
  //     $join->on('tb_role.menu_id', '=', 'ms_menuadm.id')
  //     ->where('ms_menuadm.link', 'like', "%".Request::segment(1))
  //     ->whereNull('ms_menuadm.deleted_at')
  //     ->whereNull('tb_role.deleted_at');
  // })
  // ->first();


  // if($role[$action]){
  //     return true;
  // }else{
  //     return false;
  // }


  // return view('masterweb::module.admin.laboratorium.coba',compact('role'));
  // return Excel::download(new UsersExport, 'users.xlsx');
}


function template_email($url, $nama_member, $status_member, $opt)
{
  $html = '

        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><!--[if IE]><html xmlns="http://www.w3.org/1999/xhtml" class="ie"><![endif]--><!--[if !IE]><!--><html style="margin: 0;padding: 0;" xmlns="http://www.w3.org/1999/xhtml"><!--<![endif]--><head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <title></title>
            <!--[if !mso]><!--><meta http-equiv="X-UA-Compatible" content="IE=edge" /><!--<![endif]-->
            <meta name="viewport" content="width=device-width" /><style type="text/css">
            @media only screen and (min-width: 620px){.wrapper{min-width:600px !important}.wrapper h1{}.wrapper h1{font-size:26px !important;line-height:34px !important}.wrapper h2{}.wrapper h2{font-size:20px !important;line-height:28px !important}.wrapper h3{}.column{}.wrapper .size-8{font-size:8px !important;line-height:14px !important}.wrapper .size-9{font-size:9px !important;line-height:16px !important}.wrapper .size-10{font-size:10px !important;line-height:18px !important}.wrapper .size-11{font-size:11px !important;line-height:19px !important}.wrapper .size-12{font-size:12px !important;line-height:19px !important}.wrapper .size-13{font-size:13px !important;line-height:21px !important}.wrapper .size-14{font-size:14px !important;line-height:21px !important}.wrapper .size-15{font-size:15px !important;line-height:23px !important}.wrapper .size-16{font-size:16px !important;line-height:24px
            !important}.wrapper .size-17{font-size:17px !important;line-height:26px !important}.wrapper .size-18{font-size:18px !important;line-height:26px !important}.wrapper .size-20{font-size:20px !important;line-height:28px !important}.wrapper .size-22{font-size:22px !important;line-height:31px !important}.wrapper .size-24{font-size:24px !important;line-height:32px !important}.wrapper .size-26{font-size:26px !important;line-height:34px !important}.wrapper .size-28{font-size:28px !important;line-height:36px !important}.wrapper .size-30{font-size:30px !important;line-height:38px !important}.wrapper .size-32{font-size:32px !important;line-height:40px !important}.wrapper .size-34{font-size:34px !important;line-height:43px !important}.wrapper .size-36{font-size:36px !important;line-height:43px !important}.wrapper .size-40{font-size:40px !important;line-height:47px !important}.wrapper
            .size-44{font-size:44px !important;line-height:50px !important}.wrapper .size-48{font-size:48px !important;line-height:54px !important}.wrapper .size-56{font-size:56px !important;line-height:60px !important}.wrapper .size-64{font-size:64px !important;line-height:63px !important}}
            </style>
                <meta name="x-apple-disable-message-reformatting" />
                <style type="text/css">
            body {
            margin: 0;
            padding: 0;
            }
            table {
            border-collapse: collapse;
            table-layout: fixed;
            }
            * {
            line-height: inherit;
            }
            [x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
            }
            .wrapper .footer__share-button a:hover,
            .wrapper .footer__share-button a:focus {
            color: #ffffff !important;
            }
            .btn a:hover,
            .btn a:focus,
            .footer__share-button a:hover,
            .footer__share-button a:focus,
            .email-footer__links a:hover,
            .email-footer__links a:focus {
            opacity: 0.8;
            }
            .preheader,
            .header,
            .layout,
            .column {
            transition: width 0.25s ease-in-out, max-width 0.25s ease-in-out;
            }
            .preheader td {
            padding-bottom: 8px;
            }
            .layout,
            div.header {
            max-width: 400px !important;
            -fallback-width: 95% !important;
            width: calc(100% - 20px) !important;
            }
            div.preheader {
            max-width: 360px !important;
            -fallback-width: 90% !important;
            width: calc(100% - 60px) !important;
            }
            .snippet,
            .webversion {
            Float: none !important;
            }
            .stack .column {
            max-width: 400px !important;
            width: 100% !important;
            }
            .fixed-width.has-border {
            max-width: 402px !important;
            }
            .fixed-width.has-border .layout__inner {
            box-sizing: border-box;
            }
            .snippet,
            .webversion {
            width: 50% !important;
            }
            .ie .btn {
            width: 100%;
            }
            .ie .stack .column,
            .ie .stack .gutter {
            display: table-cell;
            float: none !important;
            }
            .ie div.preheader,
            .ie .email-footer {
            max-width: 560px !important;
            width: 560px !important;
            }
            .ie .snippet,
            .ie .webversion {
            width: 280px !important;
            }
            .ie div.header,
            .ie .layout {
            max-width: 600px !important;
            width: 600px !important;
            }
            .ie .two-col .column {
            max-width: 300px !important;
            width: 300px !important;
            }
            .ie .three-col .column,
            .ie .narrow {
            max-width: 200px !important;
            width: 200px !important;
            }
            .ie .wide {
            width: 400px !important;
            }
            .ie .stack.fixed-width.has-border,
            .ie .stack.has-gutter.has-border {
            max-width: 602px !important;
            width: 602px !important;
            }
            .ie .stack.two-col.has-gutter .column {
            max-width: 290px !important;
            width: 290px !important;
            }
            .ie .stack.three-col.has-gutter .column,
            .ie .stack.has-gutter .narrow {
            max-width: 188px !important;
            width: 188px !important;
            }
            .ie .stack.has-gutter .wide {
            max-width: 394px !important;
            width: 394px !important;
            }
            .ie .stack.two-col.has-gutter.has-border .column {
            max-width: 292px !important;
            width: 292px !important;
            }
            .ie .stack.three-col.has-gutter.has-border .column,
            .ie .stack.has-gutter.has-border .narrow {
            max-width: 190px !important;
            width: 190px !important;
            }
            .ie .stack.has-gutter.has-border .wide {
            max-width: 396px !important;
            width: 396px !important;
            }
            .ie .fixed-width .layout__inner {
            border-left: 0 none white !important;
            border-right: 0 none white !important;
            }
            .ie .layout__edges {
            display: none;
            }
            .mso .layout__edges {
            font-size: 0;
            }
            .layout-fixed-width,
            .mso .layout-full-width {
            background-color: #ffffff;
            }
            @media only screen and (min-width: 620px) {
            .column,
            .gutter {
                display: table-cell;
                Float: none !important;
                vertical-align: top;
            }
            div.preheader,
            .email-footer {
                max-width: 560px !important;
                width: 560px !important;
            }
            .snippet,
            .webversion {
                width: 280px !important;
            }
            div.header,
            .layout,
            .one-col .column {
                max-width: 600px !important;
                width: 600px !important;
            }
            .fixed-width.has-border,
            .fixed-width.x_has-border,
            .has-gutter.has-border,
            .has-gutter.x_has-border {
                max-width: 602px !important;
                width: 602px !important;
            }
            .two-col .column {
                max-width: 300px !important;
                width: 300px !important;
            }
            .three-col .column,
            .column.narrow,
            .column.x_narrow {
                max-width: 200px !important;
                width: 200px !important;
            }
            .column.wide,
            .column.x_wide {
                width: 400px !important;
            }
            .two-col.has-gutter .column,
            .two-col.x_has-gutter .column {
                max-width: 290px !important;
                width: 290px !important;
            }
            .three-col.has-gutter .column,
            .three-col.x_has-gutter .column,
            .has-gutter .narrow {
                max-width: 188px !important;
                width: 188px !important;
            }
            .has-gutter .wide {
                max-width: 394px !important;
                width: 394px !important;
            }
            .two-col.has-gutter.has-border .column,
            .two-col.x_has-gutter.x_has-border .column {
                max-width: 292px !important;
                width: 292px !important;
            }
            .three-col.has-gutter.has-border .column,
            .three-col.x_has-gutter.x_has-border .column,
            .has-gutter.has-border .narrow,
            .has-gutter.x_has-border .narrow {
                max-width: 190px !important;
                width: 190px !important;
            }
            .has-gutter.has-border .wide,
            .has-gutter.x_has-border .wide {
                max-width: 396px !important;
                width: 396px !important;
            }
            }
            @supports (display: flex) {
            @media only screen and (min-width: 620px) {
                .fixed-width.has-border .layout__inner {
                display: flex !important;
                }
            }
            }
            @media only screen and (-webkit-min-device-pixel-ratio: 2), only screen and (min--moz-device-pixel-ratio: 2), only screen and (-o-min-device-pixel-ratio: 2/1), only screen and (min-device-pixel-ratio: 2), only screen and (min-resolution: 192dpi), only screen and (min-resolution: 2dppx) {
            .fblike {
                background-image: url(https://i7.createsend1.com/static/eb/master/13-the-blueprint-3/images/fblike@2x.png) !important;
            }
            .tweet {
                background-image: url(https://i8.createsend1.com/static/eb/master/13-the-blueprint-3/images/tweet@2x.png) !important;
            }
            .linkedinshare {
                background-image: url(https://i9.createsend1.com/static/eb/master/13-the-blueprint-3/images/lishare@2x.png) !important;
            }
            .forwardtoafriend {
                background-image: url(https://i10.createsend1.com/static/eb/master/13-the-blueprint-3/images/forward@2x.png) !important;
            }
            }
            @media (max-width: 321px) {
            .fixed-width.has-border .layout__inner {
                border-width: 1px 0 !important;
            }
            .layout,
            .stack .column {
                min-width: 320px !important;
                width: 320px !important;
            }
            .border {
                display: none;
            }
            .has-gutter .border {
                display: table-cell;
            }
            }
            .mso div {
            border: 0 none white !important;
            }
            .mso .w560 .divider {
            Margin-left: 260px !important;
            Margin-right: 260px !important;
            }
            .mso .w360 .divider {
            Margin-left: 160px !important;
            Margin-right: 160px !important;
            }
            .mso .w260 .divider {
            Margin-left: 110px !important;
            Margin-right: 110px !important;
            }
            .mso .w160 .divider {
            Margin-left: 60px !important;
            Margin-right: 60px !important;
            }
            .mso .w354 .divider {
            Margin-left: 157px !important;
            Margin-right: 157px !important;
            }
            .mso .w250 .divider {
            Margin-left: 105px !important;
            Margin-right: 105px !important;
            }
            .mso .w148 .divider {
            Margin-left: 54px !important;
            Margin-right: 54px !important;
            }
            .mso .size-8,
            .ie .size-8 {
            font-size: 8px !important;
            line-height: 14px !important;
            }
            .mso .size-9,
            .ie .size-9 {
            font-size: 9px !important;
            line-height: 16px !important;
            }
            .mso .size-10,
            .ie .size-10 {
            font-size: 10px !important;
            line-height: 18px !important;
            }
            .mso .size-11,
            .ie .size-11 {
            font-size: 11px !important;
            line-height: 19px !important;
            }
            .mso .size-12,
            .ie .size-12 {
            font-size: 12px !important;
            line-height: 19px !important;
            }
            .mso .size-13,
            .ie .size-13 {
            font-size: 13px !important;
            line-height: 21px !important;
            }
            .mso .size-14,
            .ie .size-14 {
            font-size: 14px !important;
            line-height: 21px !important;
            }
            .mso .size-15,
            .ie .size-15 {
            font-size: 15px !important;
            line-height: 23px !important;
            }
            .mso .size-16,
            .ie .size-16 {
            font-size: 16px !important;
            line-height: 24px !important;
            }
            .mso .size-17,
            .ie .size-17 {
            font-size: 17px !important;
            line-height: 26px !important;
            }
            .mso .size-18,
            .ie .size-18 {
            font-size: 18px !important;
            line-height: 26px !important;
            }
            .mso .size-20,
            .ie .size-20 {
            font-size: 20px !important;
            line-height: 28px !important;
            }
            .mso .size-22,
            .ie .size-22 {
            font-size: 22px !important;
            line-height: 31px !important;
            }
            .mso .size-24,
            .ie .size-24 {
            font-size: 24px !important;
            line-height: 32px !important;
            }
            .mso .size-26,
            .ie .size-26 {
            font-size: 26px !important;
            line-height: 34px !important;
            }
            .mso .size-28,
            .ie .size-28 {
            font-size: 28px !important;
            line-height: 36px !important;
            }
            .mso .size-30,
            .ie .size-30 {
            font-size: 30px !important;
            line-height: 38px !important;
            }
            .mso .size-32,
            .ie .size-32 {
            font-size: 32px !important;
            line-height: 40px !important;
            }
            .mso .size-34,
            .ie .size-34 {
            font-size: 34px !important;
            line-height: 43px !important;
            }
            .mso .size-36,
            .ie .size-36 {
            font-size: 36px !important;
            line-height: 43px !important;
            }
            .mso .size-40,
            .ie .size-40 {
            font-size: 40px !important;
            line-height: 47px !important;
            }
            .mso .size-44,
            .ie .size-44 {
            font-size: 44px !important;
            line-height: 50px !important;
            }
            .mso .size-48,
            .ie .size-48 {
            font-size: 48px !important;
            line-height: 54px !important;
            }
            .mso .size-56,
            .ie .size-56 {
            font-size: 56px !important;
            line-height: 60px !important;
            }
            .mso .size-64,
            .ie .size-64 {
            font-size: 64px !important;
            line-height: 63px !important;
            }
            .btn {
                border-radius: 3px;
                box-shadow: 0 2px 3px rgba(0, 0, 0, 0.16);
                color: #fff;
                display: inline-block;
                text-decoration: none;
                -webkit-text-size-adjust: none;
            }
            .btn-red,
            .btn-error {
                background-color: #e3342f;
                border-top: 10px solid #e3342f;
                border-right: 18px solid #e3342f;
                border-bottom: 10px solid #e3342f;
                border-left: 18px solid #e3342f;
            }
            </style>

            <!--[if !mso]><!--><style type="text/css">
            @import url(https://fonts.googleapis.com/css?family=Roboto:400,700,400italic,700italic);
            </style><link href="https://fonts.googleapis.com/css?family=Roboto:400,700,400italic,700italic" rel="stylesheet" type="text/css" /><!--<![endif]--><style type="text/css">
            body{background-color:#fbfbfb}.logo a:hover,.logo a:focus{color:#1e2e3b !important}.mso .layout-has-border{border-top:1px solid #c8c8c8;border-bottom:1px solid #c8c8c8}.mso .layout-has-bottom-border{border-bottom:1px solid #c8c8c8}.mso .border,.ie .border{background-color:#c8c8c8}.mso h1,.ie h1{}.mso h1,.ie h1{font-size:26px !important;line-height:34px !important}.mso h2,.ie h2{}.mso h2,.ie h2{font-size:20px !important;line-height:28px !important}.mso h3,.ie h3{}.mso .layout__inner,.ie .layout__inner{}.mso .footer__share-button p{}.mso .footer__share-button p{font-family:Georgia,serif}
            </style><meta name="robots" content="noindex,nofollow" />
            <meta property="og:title" content="My First Campaign" />
            </head>
            <!--[if mso]>
            <body class="mso">
            <![endif]-->
            <!--[if !mso]><!-->
            <body class="full-padding" style="margin: 0;padding: 0;-webkit-text-size-adjust: 100%;">
            <!--<![endif]-->
                <table class="wrapper" style="border-collapse: collapse;table-layout: fixed;min-width: 320px;width: 100%;background-color: #fbfbfb;" cellpadding="0" cellspacing="0" role="presentation"><tbody><tr><td>
                <div role="banner">
                    <div class="preheader" style="Margin: 0 auto;max-width: 560px;min-width: 280px; width: 280px;width: calc(28000% - 167440px);">
                    <div style="border-collapse: collapse;display: table;width: 100%;">
                    <!--[if (mso)|(IE)]><table align="center" class="preheader" cellpadding="0" cellspacing="0" role="presentation"><tr><td style="width: 280px" valign="top"><![endif]-->
                        <div class="snippet" style="display: table-cell;Float: left;font-size: 12px;line-height: 19px;max-width: 280px;min-width: 140px; width: 140px;width: calc(14000% - 78120px);padding: 10px 0 5px 0;color: #999;font-family: Georgia,serif;">

                        </div>
                    <!--[if (mso)|(IE)]></td><td style="width: 280px" valign="top"><![endif]-->
                        <div class="webversion" style="display: table-cell;Float: left;font-size: 12px;line-height: 19px;max-width: 280px;min-width: 139px; width: 139px;width: calc(14100% - 78680px);padding: 10px 0 5px 0;text-align: right;color: #999;font-family: Georgia,serif;">
                        </div>
                    <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
                    </div>
                    </div>
                    <div class="header" style="Margin: 0 auto;max-width: 600px;min-width: 320px; width: 320px;width: calc(28000% - 167400px);" id="emb-email-header-container">
                    <!--[if (mso)|(IE)]><table align="center" class="header" cellpadding="0" cellspacing="0" role="presentation"><tr><td style="width: 600px"><![endif]-->
                    <div class="logo emb-logo-margin-box" style="font-size: 26px;line-height: 32px;Margin-top: 6px;Margin-bottom: 20px;color: #41637e;font-family: Avenir,sans-serif;Margin-left: 20px;Margin-right: 20px;" align="center">
                    <div class="logo-center" align="center" id="emb-email-header"><img style="display: block;height: auto;width: 100%;border: 0;max-width: 254px;" src="" alt="" width="254" /></div>
                    </div>
                    <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
                    </div>
                </div>
                <div>
                <div class="layout one-col fixed-width stack" style="Margin: 0 auto;max-width: 600px;min-width: 320px; width: 320px;width: calc(28000% - 167400px);overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;">
                    <div class="layout__inner" style="border-collapse: collapse;display: table;width: 100%;background-color: #ffffff;">
                    <!--[if (mso)|(IE)]><table align="center" cellpadding="0" cellspacing="0" role="presentation"><tr class="layout-fixed-width" style="background-color: #ffffff;"><td style="width: 600px" class="w560"><![endif]-->
                    <div class="column" style="text-align: left;color: #565656;font-size: 14px;line-height: 21px;font-family: Georgia,serif;">

                        <div style="Margin-left: 20px;Margin-right: 20px;Margin-top: 24px;Margin-bottom: 24px;">
                <div style="mso-line-height-rule: exactly;mso-text-raise: 11px;vertical-align: middle;">
                    <h1 class="size-30" style="Margin-top: 0;Margin-bottom: 0;font-style: normal;font-weight: normal;color: #565656;font-size: 26px;line-height: 34px;font-family: Avenir,sans-serif;" lang="x-size-30">Hai ' . $nama_member . ' !</h1>
                    <p class="size-20" style="Margin-top: 20px;Margin-bottom: 0;font-family: roboto,tahoma,sans-serif;font-size: 17px;line-height: 26px;" lang="x-size-20"><span class="font-roboto">Terimakasih telah mendaftarkan diri di ' . $opt->title . '.</span></p>
                    <p class="size-20" style="Margin-top: 20px;Margin-bottom: 0;font-family: roboto,tahoma,sans-serif;font-size: 17px;line-height: 26px;" lang="x-size-20"><span class="font-roboto">Silahkan Klik tombol dibawah ini untuk memverifikasi akun Anda.</span></p>

                    <a href="' . $url . '" style="Margin-top: 20px;Margin-bottom: 0;font-family: roboto,tahoma,sans-serif;font-size: 17px;line-height: 26px;color:#000;  border-radius: 3px;
                    box-shadow: 0 2px 3px rgba(0, 0, 0, 0.16);color: #fff;display: inline-block;text-decoration: none;-webkit-text-size-adjust: none;background-color: #e3342f;border-top: 10px solid #e3342f;border-right: 18px solid #e3342f;border-bottom: 10px solid #e3342f;border-left: 18px solid #e3342f;" target="_blank"><span class="font-roboto">  verifikasi </span></a>

                    <p class="size-20" style="Margin-top: 20px;Margin-bottom: 0;font-family: roboto,tahoma,sans-serif;font-size: 17px;line-height: 26px;" lang="x-size-20"><span class="font-roboto">Hormat kami,</span><br><br><span class="font-roboto">Tim ' . $opt->title . '.</span></p>
                </div>
                </div>

                    </div>
                    <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
                    </div>
                </div>

                <div style="mso-line-height-rule: exactly;line-height: 20px;font-size: 20px;">&nbsp;</div>


                <div role="contentinfo">
                    <div class="layout email-footer stack" style="Margin: 0 auto;max-width: 600px;min-width: 320px; width: 320px;width: calc(28000% - 167400px);overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;">
                    <div class="layout__inner" style="border-collapse: collapse;display: table;width: 100%;">
                    <!--[if (mso)|(IE)]><table align="center" cellpadding="0" cellspacing="0" role="presentation"><tr class="layout-email-footer"><td style="width: 400px;" valign="top" class="w360"><![endif]-->
                        <div class="column wide" style="text-align: left;font-size: 12px;line-height: 19px;color: #999;font-family: Georgia,serif;Float: left;max-width: 400px;min-width: 320px; width: 320px;width: calc(8000% - 47600px);">
                        <div style="Margin-left: 20px;Margin-right: 20px;Margin-top: 10px;Margin-bottom: 10px;">
                            <table class="email-footer__links" style="border-collapse: collapse;table-layout: fixed;" role="presentation" emb-web-links><tbody><tr role="navigation">

                            </tr></tbody></table>
                            <div style="font-size: 12px;line-height: 19px;Margin-top: 20px;font-family: roboto,tahoma,sans-serif;">
                            <div class="font-roboto">Copyright SIMKRAF &#169; 2020 All rights reserved.</div>
                            </div>
                            <div style="font-size: 12px;line-height: 19px;Margin-top: 18px;">

                            </div>
                            <!--[if mso]>&nbsp;<![endif]-->
                        </div>
                        </div>
                    <!--[if (mso)|(IE)]></td><td style="width: 200px;" valign="top" class="w160"><![endif]-->
                        <div class="column narrow" style="text-align: left;font-size: 12px;line-height: 19px;color: #999;font-family: Georgia,serif;Float: left;max-width: 320px;min-width: 200px; width: 320px;width: calc(72200px - 12000%);">
                        <div style="Margin-left: 20px;Margin-right: 20px;Margin-top: 10px;Margin-bottom: 10px;">

                        </div>
                        </div>
                    <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
                    </div>
                    </div>
                    <div class="layout one-col email-footer" style="Margin: 0 auto;max-width: 600px;min-width: 320px; width: 320px;width: calc(28000% - 167400px);overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;">
                    <div class="layout__inner" style="border-collapse: collapse;display: table;width: 100%;">
                    <!--[if (mso)|(IE)]><table align="center" cellpadding="0" cellspacing="0" role="presentation"><tr class="layout-email-footer"><td style="width: 600px;" class="w560"><![endif]-->
                        <div class="column" style="text-align: left;font-size: 12px;line-height: 19px;color: #999;font-family: Georgia,serif;">
                        <div style="Margin-left: 20px;Margin-right: 20px;Margin-top: 10px;Margin-bottom: 10px;">
                            <div style="font-size: 12px;line-height: 19px;">

                            </div>
                        </div>
                        </div>
                    <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
                    </div>
                    </div>
                </div>
                <div style="line-height:40px;font-size:40px;">&nbsp;</div>
                </div></td></tr></tbody></table>

        </body></html>

        ';

  return $html;
}

function takeIt($module)
{
  if (isset($module[0]) and $module[0] == "html") {
    echo $module[1];
  } else {
    $getModule = DB::table('ms_module')->where('id', '=', $module)->first();
    echo view('masterweb::' . $getModule->module);
  }
}

function integerToRoman($integer)
{
  // Convert the integer into an integer (just to make sure)
  $integer = intval($integer);
  $result = '';

  // Create a lookup array that contains all of the Roman numerals.
  $lookup = array(
    'M' => 1000,
    'CM' => 900,
    'D' => 500,
    'CD' => 400,
    'C' => 100,
    'XC' => 90,
    'L' => 50,
    'XL' => 40,
    'X' => 10,
    'IX' => 9,
    'V' => 5,
    'IV' => 4,
    'I' => 1
  );

  foreach ($lookup as $roman => $value) {
    // Determine the number of matches
    $matches = intval($integer / $value);

    // Add the same number of characters to the string
    $result .= str_repeat($roman, $matches);

    // Set the integer to be the remainder of the integer and the value
    $integer = $integer % $value;
  }

  // The Roman numeral should be built, return it
  return $result;
}

function number_to_alphabet($number)
{
  $number = intval($number);
  if ($number <= 0) {
    return '';
  }
  $alphabet = '';
  while ($number != 0) {
    $p = ($number - 1) % 26;
    $number = intval(($number - $p) / 26);
    $alphabet = chr(65 + $p) . $alphabet;
  }
  return $alphabet;
}

function alphabet_to_number($string)
{
  $string = strtoupper($string);
  $length = strlen($string);
  $number = 0;
  $level = 1;
  while ($length >= $level) {
    $char = $string[$length - $level];
    $c = ord($char) - 64;
    $number += $c * (26 ** ($level - 1));
    $level++;
  }
  return $number;
}

if (!function_exists('fdate_sas')) {

  function fdate_sas($value, $format)

  {

    $set_date = explode(" ", $value);

    $date = explode("-", $set_date[0]);

    $tgl = $date[2];

    $bln = $date[1];

    $thn = $date[0];

    $return = "";



    switch ($format) {

      case "DDMMYYYY":

        $return = $tgl . " " . fbulan($bln) . " " . $thn;

        break;

      case "DDMMMYYYY":

        $return = $tgl . " " . substr(fbulan($bln), 0, 3) . " " . $thn;

        break;

      case "DD":

        $return = $tgl;

        break;

      case "MM":

        $return = $bln;

        break;

      case "YYYYY":

        $return = $thn;

        break;

      case "MMYYYY":

        $return = fbulan($bln) . " " . $thn;

        break;

      case "mm":

        $return = fbulan($bln);

        break;

        // case "HHDDMMYYYY" :

        //     $eks = explode(" ", $tgl);

        //     $tgl = $eks[0];

        //     $jam = $eks[1];

        // list($H,$M,$S) = explode(":",$jam);

        //     $return = $tgl." ".fbulan($bln)." ".$thn." | ".$H.":".$M.":".$S;

        // break;

    }

    return $return;
  }
}

if (!function_exists('fdate_carbon_sas')) {
  function fdate_carbon_sas($value, $format)
  {
    if ($value != null) {
      $set_date = explode(" ", $value);
      $date = explode("-", $set_date[0]);
      $tgl = $date[2];
      $bln = $date[1];
      $thn = $date[0];
      $return = "";

      switch ($format) {
        case "DDMMYYYYHHMM":
          $return = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $value)->isoFormat('D MMMM Y HH:mm');
          break;

        case "DDMMYYYY-HHMM":
          $return = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $value)->isoFormat('D MMMM Y');
          break;

        case "DDMMYYYY":
          $return = \Carbon\Carbon::createFromFormat('Y-m-d', $value)->isoFormat('D MMMM Y');
          break;

          // OUTPUT 20 Mar 2018
        case "DDMMMYYYY":
          $return = \Carbon\Carbon::createFromFormat('Y-m-d', $value)->format('%d %b %Y');
          break;

        case "DD":
          $return = $return = \Carbon\Carbon::createFromFormat('Y-m-d', $value)->isoFormat('D');
          break;

        case "MM":
          $return = $return = \Carbon\Carbon::createFromFormat('Y-m-d', $value)->isoFormat('MMM');
          break;

        case "YYYYY":
          $return = $return = \Carbon\Carbon::createFromFormat('Y-m-d', $value)->isoFormat('Y');
          break;

        case "MMYYYY":
          $return = $return = \Carbon\Carbon::createFromFormat('Y-m-d', $value)->isoFormat('MMM Y');
          break;

        default:
          return "not found";
          break;
      }

      return $return;
    }
  }
}

if (!function_exists('reference_sas')) {

  function reference_sas($code, $value = null)
  {
    switch ($code) {
      case 'gender':
        (object) $data = [
          'L' => 'Laki-laki',
          'P' => 'Perempuan',
        ];
        return ($value == null) ? $data : $data[$value];
        break;

      case 'jenis_pemeriksaan_klinik':
        (object) $data = [
          '0' => 'Pemeriksaan Klinik',
          '1' => 'PCR',
          '2' => 'Rapid Antibody',
          '3' => 'Rapid Antigen',
        ];
        return ($value == null) ? $data : $data[$value];
        break;

      case 'jenis_program':
        (object) $data = [
          '0' => 'AB',
          '1' => 'AM',
          '2' => 'MAKMIN',
          '3' => 'KLB',
          '4' => 'SIDAK PASAR',
          '5' => 'TTU,tpm',
          '6' => 'SARKES',
          '7' => 'NON SARKES',
          '8' => 'PARASITOLOGI',
          '9' => 'KLINIK',
        ];
        return ($value == null) ? $data : $data[$value];
        break;

      default:
        return "not found";
        break;
    }
  }
}

function sortingNumberKlinik()
{

  $start_num=StartNum::where('code_lab_start_number','KLI')->first();

  if ( date('Y')==$start_num->year_start_number) {
    //UPDATE tb_number_klinik n
    DB::statement("
        SET @current_number = 2831;
    ");
    DB::statement("

        UPDATE tb_number_klinik n
        JOIN (
            SELECT
                id_number_klinik,
                @current_number AS new_number,
                CASE
                    WHEN id_permohonan_uji_klinik IS NOT NULL THEN @current_number
                    ELSE
                        CASE
                            WHEN id_prolanis IS NOT NULL THEN @current_number + v.kuota_prolanis - 1
                            WHEN id_haji IS NOT NULL THEN @current_number + h.kuota_haji - 1
                            WHEN id_prolanis_gula IS NOT NULL THEN @current_number + g.kuota_prolanis_gula - 1
                            WHEN id_prolanis_urine IS NOT NULL THEN @current_number + u.kuota_prolanis_urine - 1
                        END
                END AS last_number,
                @current_number :=
                    CASE
                      WHEN id_prolanis IS NOT NULL THEN @current_number + v.kuota_prolanis
                        WHEN id_haji IS NOT NULL THEN @current_number + h.kuota_haji
                        WHEN id_prolanis_gula IS NOT NULL THEN @current_number + g.kuota_prolanis_gula
                        WHEN id_prolanis_urine IS NOT NULL THEN @current_number + u.kuota_prolanis_urine
                        ELSE @current_number + 1
                    END AS updated_current_number

            FROM (
                SELECT *
                FROM tb_number_klinik
                WHERE deleted_at IS NULL
                ORDER BY created_at ASC
            ) sorted


            LEFT JOIN tb_permohonan_uji_klinik_haji h
                ON sorted.id_haji = h.id_permohonan_uji_klinik_haji AND h.deleted_at IS NULL
            LEFT JOIN tb_permohonan_uji_klinik_prolanis_gula g
                ON sorted.id_prolanis_gula = g.id_permohonan_uji_klinik_prolanis_gula AND g.deleted_at IS NULL
            LEFT JOIN tb_permohonan_uji_klinik_prolanis_urine u
                ON sorted.id_prolanis_urine = u.id_permohonan_uji_klinik_prolanis_urine AND u.deleted_at IS NULL
            LEFT JOIN tb_permohonan_uji_klinik_prolanis v
                ON sorted.id_prolanis = v.id_permohonan_uji_klinik_prolanis AND v.deleted_at IS NULL
        ) ordered
        ON n.id_number_klinik = ordered.id_number_klinik
        SET
            n.new_number = ordered.new_number,
            n.last_number = ordered.last_number;

    ");


    //NON PROLANIS DAN HAJI
    DB::statement("
        UPDATE
                    tb_permohonan_uji_klinik_2 p
                JOIN
                    tb_number_klinik n
                    ON p.id_permohonan_uji_klinik = n.id_permohonan_uji_klinik
                SET
                    p.nourut_permohonan_uji_klinik = n.new_number,
                    p.noregister_permohonan_uji_klinik = CONCAT(
                        n.new_number,
                        '/LK/',
                        CASE
                            WHEN MONTH(p.created_at) = 1 THEN 'I'
                            WHEN MONTH(p.created_at) = 2 THEN 'II'
                            WHEN MONTH(p.created_at) = 3 THEN 'III'
                            WHEN MONTH(p.created_at) = 4 THEN 'IV'
                            WHEN MONTH(p.created_at) = 5 THEN 'V'
                            WHEN MONTH(p.created_at) = 6 THEN 'VI'
                            WHEN MONTH(p.created_at) = 7 THEN 'VII'
                            WHEN MONTH(p.created_at) = 8 THEN 'VIII'
                            WHEN MONTH(p.created_at) = 9 THEN 'IX'
                            WHEN MONTH(p.created_at) = 10 THEN 'X'
                            WHEN MONTH(p.created_at) = 11 THEN 'XI'
                            WHEN MONTH(p.created_at) = 12 THEN 'XII'
                        END,
                        '/',
                        YEAR(p.created_at)
                    )
                WHERE
                    n.id_permohonan_uji_klinik IS NOT NULL
                    AND p.deleted_at IS NULL
                    AND n.deleted_at IS NULL;
        ");


    //UBAH PROLANIS
    DB::statement("

      UPDATE
            `tb_permohonan_uji_klinik_2` p
        JOIN (
          SELECT p.id_permohonan_uji_klinik, n.new_number, n.id_prolanis, ROW_NUMBER() OVER (PARTITION BY p.id_permohonan_uji_klinik_prolanis ORDER BY p.nourut_permohonan_uji_klinik) AS urutan FROM `tb_permohonan_uji_klinik_2` p LEFT JOIN tb_number_klinik n ON p.id_permohonan_uji_klinik_prolanis IN (n.id_prolanis, n.id_prolanis_gula, n.id_prolanis_urine) WHERE n.id_prolanis IS NOT NULL AND n.id_permohonan_uji_klinik IS NULL
        ) u
        ON
          p.id_permohonan_uji_klinik = u.id_permohonan_uji_klinik
        SET
            p.nourut_permohonan_uji_klinik = u.new_number + u.urutan - 1,
            p.noregister_permohonan_uji_klinik = CONCAT(
                        u.new_number + u.urutan - 1,
                        '/LK/',
                        CASE
                            WHEN MONTH(p.created_at) = 1 THEN 'I'
                            WHEN MONTH(p.created_at) = 2 THEN 'II'
                            WHEN MONTH(p.created_at) = 3 THEN 'III'
                            WHEN MONTH(p.created_at) = 4 THEN 'IV'
                            WHEN MONTH(p.created_at) = 5 THEN 'V'
                            WHEN MONTH(p.created_at) = 6 THEN 'VI'
                            WHEN MONTH(p.created_at) = 7 THEN 'VII'
                            WHEN MONTH(p.created_at) = 8 THEN 'VIII'
                            WHEN MONTH(p.created_at) = 9 THEN 'IX'
                            WHEN MONTH(p.created_at) = 10 THEN 'X'
                            WHEN MONTH(p.created_at) = 11 THEN 'XI'
                            WHEN MONTH(p.created_at) = 12 THEN 'XII'
                        END,
                        '/',
                        YEAR(p.created_at)
                    );

      ");

    // UBAH HAJI
    DB::statement("

              UPDATE
            `tb_permohonan_uji_klinik_2` p
        JOIN (
          SELECT p.id_permohonan_uji_klinik, n.new_number, n.id_haji, ROW_NUMBER() OVER (PARTITION BY p.id_permohonan_uji_klinik_haji ORDER BY p.nourut_permohonan_uji_klinik) AS urutan FROM `tb_permohonan_uji_klinik_2` p LEFT JOIN tb_number_klinik n ON p.id_permohonan_uji_klinik_haji = n.id_haji WHERE n.id_haji IS NOT NULL AND n.id_permohonan_uji_klinik IS NULL
        ) u
        ON p.id_permohonan_uji_klinik = u.id_permohonan_uji_klinik
        SET
            p.nourut_permohonan_uji_klinik = u.new_number + u.urutan - 1,
            p.noregister_permohonan_uji_klinik = CONCAT(
                        u.new_number + u.urutan - 1,
                        '/LK/',
                        CASE
                            WHEN MONTH(p.created_at) = 1 THEN 'I'
                            WHEN MONTH(p.created_at) = 2 THEN 'II'
                            WHEN MONTH(p.created_at) = 3 THEN 'III'
                            WHEN MONTH(p.created_at) = 4 THEN 'IV'
                            WHEN MONTH(p.created_at) = 5 THEN 'V'
                            WHEN MONTH(p.created_at) = 6 THEN 'VI'
                            WHEN MONTH(p.created_at) = 7 THEN 'VII'
                            WHEN MONTH(p.created_at) = 8 THEN 'VIII'
                            WHEN MONTH(p.created_at) = 9 THEN 'IX'
                            WHEN MONTH(p.created_at) = 10 THEN 'X'
                            WHEN MONTH(p.created_at) = 11 THEN 'XI'
                            WHEN MONTH(p.created_at) = 12 THEN 'XII'
                        END,
                        '/',
                        YEAR(p.created_at)
                    );
    ");

  }else{
       //UPDATE tb_number_klinik n
    DB::statement("
       SET @current_number = 1;
   ");
   DB::statement("

       UPDATE tb_number_klinik n
       JOIN (
           SELECT
               id_number_klinik,
               @current_number AS new_number,
               CASE
                   WHEN id_permohonan_uji_klinik IS NOT NULL THEN @current_number
                   ELSE
                       CASE
                           WHEN id_prolanis IS NOT NULL THEN @current_number + v.kuota_prolanis - 1
                           WHEN id_haji IS NOT NULL THEN @current_number + h.kuota_haji - 1
                           WHEN id_prolanis_gula IS NOT NULL THEN @current_number + g.kuota_prolanis_gula - 1
                           WHEN id_prolanis_urine IS NOT NULL THEN @current_number + u.kuota_prolanis_urine - 1
                       END
               END AS last_number,
               @current_number :=
                   CASE
                     WHEN id_prolanis IS NOT NULL THEN @current_number + v.kuota_prolanis
                       WHEN id_haji IS NOT NULL THEN @current_number + h.kuota_haji
                       WHEN id_prolanis_gula IS NOT NULL THEN @current_number + g.kuota_prolanis_gula
                       WHEN id_prolanis_urine IS NOT NULL THEN @current_number + u.kuota_prolanis_urine
                       ELSE @current_number + 1
                   END AS updated_current_number

           FROM (
               SELECT *
               FROM tb_number_klinik
               WHERE deleted_at IS NULL
               AND YEAR(tb_number_klinik.created_at) = ".date('Y')."
               ORDER BY created_at ASC
           ) sorted


           LEFT JOIN tb_permohonan_uji_klinik_haji h
               ON sorted.id_haji = h.id_permohonan_uji_klinik_haji AND h.deleted_at IS NULL
           LEFT JOIN tb_permohonan_uji_klinik_prolanis_gula g
               ON sorted.id_prolanis_gula = g.id_permohonan_uji_klinik_prolanis_gula AND g.deleted_at IS NULL
           LEFT JOIN tb_permohonan_uji_klinik_prolanis_urine u
               ON sorted.id_prolanis_urine = u.id_permohonan_uji_klinik_prolanis_urine AND u.deleted_at IS NULL
           LEFT JOIN tb_permohonan_uji_klinik_prolanis v
               ON sorted.id_prolanis = v.id_permohonan_uji_klinik_prolanis AND v.deleted_at IS NULL
       ) ordered
       ON n.id_number_klinik = ordered.id_number_klinik

       SET
           n.new_number = ordered.new_number,
           n.last_number = ordered.last_number
      WHERE YEAR(n.created_at) = ".date('Y').";
   ");


   //NON PROLANIS DAN HAJI
   DB::statement("
       UPDATE
                   tb_permohonan_uji_klinik_2 p
               JOIN
                   tb_number_klinik n
                   ON p.id_permohonan_uji_klinik = n.id_permohonan_uji_klinik
               SET
                   p.nourut_permohonan_uji_klinik = n.new_number,
                   p.noregister_permohonan_uji_klinik = CONCAT(
                        LPAD(n.new_number, 4, '0'),
                       '/LK/',
                       CASE
                           WHEN MONTH(p.created_at) = 1 THEN 'I'
                           WHEN MONTH(p.created_at) = 2 THEN 'II'
                           WHEN MONTH(p.created_at) = 3 THEN 'III'
                           WHEN MONTH(p.created_at) = 4 THEN 'IV'
                           WHEN MONTH(p.created_at) = 5 THEN 'V'
                           WHEN MONTH(p.created_at) = 6 THEN 'VI'
                           WHEN MONTH(p.created_at) = 7 THEN 'VII'
                           WHEN MONTH(p.created_at) = 8 THEN 'VIII'
                           WHEN MONTH(p.created_at) = 9 THEN 'IX'
                           WHEN MONTH(p.created_at) = 10 THEN 'X'
                           WHEN MONTH(p.created_at) = 11 THEN 'XI'
                           WHEN MONTH(p.created_at) = 12 THEN 'XII'
                       END,
                       '/',
                       YEAR(p.created_at)
                   )
               WHERE
                   n.id_permohonan_uji_klinik IS NOT NULL
                   AND p.deleted_at IS NULL
                    AND YEAR(p.created_at) = ".date('Y')."
                       AND YEAR( n.created_at) = ".date('Y')."
                   AND n.deleted_at IS NULL;
       ");


   //UBAH PROLANIS
   DB::statement("

     UPDATE
           `tb_permohonan_uji_klinik_2` p
       JOIN (
         SELECT p.id_permohonan_uji_klinik, n.new_number, n.id_prolanis, ROW_NUMBER() OVER (PARTITION BY p.id_permohonan_uji_klinik_prolanis ORDER BY p.nourut_permohonan_uji_klinik) AS urutan FROM `tb_permohonan_uji_klinik_2` p LEFT JOIN tb_number_klinik n ON p.id_permohonan_uji_klinik_prolanis IN (n.id_prolanis, n.id_prolanis_gula, n.id_prolanis_urine) WHERE n.id_prolanis IS NOT NULL AND n.id_permohonan_uji_klinik IS NULL AND YEAR(p.created_at) = ".date('Y')." AND YEAR(n.created_at) = ".date('Y')."
       ) u
       ON
         p.id_permohonan_uji_klinik = u.id_permohonan_uji_klinik
       SET
           p.nourut_permohonan_uji_klinik = u.new_number + u.urutan - 1,
           p.noregister_permohonan_uji_klinik = CONCAT(
                        LPAD(u.new_number + u.urutan - 1, 4, '0'),

                       '/LK/',
                       CASE
                           WHEN MONTH(p.created_at) = 1 THEN 'I'
                           WHEN MONTH(p.created_at) = 2 THEN 'II'
                           WHEN MONTH(p.created_at) = 3 THEN 'III'
                           WHEN MONTH(p.created_at) = 4 THEN 'IV'
                           WHEN MONTH(p.created_at) = 5 THEN 'V'
                           WHEN MONTH(p.created_at) = 6 THEN 'VI'
                           WHEN MONTH(p.created_at) = 7 THEN 'VII'
                           WHEN MONTH(p.created_at) = 8 THEN 'VIII'
                           WHEN MONTH(p.created_at) = 9 THEN 'IX'
                           WHEN MONTH(p.created_at) = 10 THEN 'X'
                           WHEN MONTH(p.created_at) = 11 THEN 'XI'
                           WHEN MONTH(p.created_at) = 12 THEN 'XII'
                       END,
                       '/',
                       YEAR(p.created_at)
                   )

               WHERE YEAR(p.created_at) = ".date('Y').";

     ");

   // UBAH HAJI
   DB::statement("

             UPDATE
           `tb_permohonan_uji_klinik_2` p
       JOIN (
         SELECT p.id_permohonan_uji_klinik, n.new_number, n.id_haji, ROW_NUMBER() OVER (PARTITION BY p.id_permohonan_uji_klinik_haji ORDER BY p.nourut_permohonan_uji_klinik) AS urutan FROM `tb_permohonan_uji_klinik_2` p LEFT JOIN tb_number_klinik n ON p.id_permohonan_uji_klinik_haji = n.id_haji WHERE n.id_haji IS NOT NULL AND n.id_permohonan_uji_klinik IS NULL
       ) u
       ON p.id_permohonan_uji_klinik = u.id_permohonan_uji_klinik
       SET
           p.nourut_permohonan_uji_klinik = u.new_number + u.urutan - 1,
           p.noregister_permohonan_uji_klinik = CONCAT(
                        LPAD(u.new_number + u.urutan - 1, 4, '0'),
                       '/LK/',
                       CASE
                           WHEN MONTH(p.created_at) = 1 THEN 'I'
                           WHEN MONTH(p.created_at) = 2 THEN 'II'
                           WHEN MONTH(p.created_at) = 3 THEN 'III'
                           WHEN MONTH(p.created_at) = 4 THEN 'IV'
                           WHEN MONTH(p.created_at) = 5 THEN 'V'
                           WHEN MONTH(p.created_at) = 6 THEN 'VI'
                           WHEN MONTH(p.created_at) = 7 THEN 'VII'
                           WHEN MONTH(p.created_at) = 8 THEN 'VIII'
                           WHEN MONTH(p.created_at) = 9 THEN 'IX'
                           WHEN MONTH(p.created_at) = 10 THEN 'X'
                           WHEN MONTH(p.created_at) = 11 THEN 'XI'
                           WHEN MONTH(p.created_at) = 12 THEN 'XII'
                       END,
                       '/',
                       YEAR(p.created_at)
                   )
                WHERE YEAR(p.created_at) = ".date('Y')."
                   ;


     ");
  }
}



function sortingNumber($lab_id, $plus_number=0){


  // $data = Sample::orderBy(DB::raw("CAST(SUBSTRING_INDEX(codesample_samples, '/', 1) AS UNSIGNED)"))
  // // ->where
  // ->leftjoin('tb_lab_num', function ($join) {
  //   $join->on('tb_lab_num.sample_id', '=', DB::raw('(SELECT sample_id FROM tb_lab_num WHERE tb_lab_num.sample_id = tb_samples.id_samples AND tb_lab_num.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

  //     // ->limit(1)
  //     ->whereNull('tb_lab_num.deleted_at')
  //     ->whereNull('tb_samples.deleted_at');
  // })
  // ->where('tb_lab_num.lab_id', $lab_id)
  // ->get();

  // $lab_id = 123; // Example lab_id

  DB::statement("
        DELETE tb_lab_num
      FROM tb_lab_num
      JOIN (
          SELECT id_lab_num
          FROM (
              SELECT id_lab_num,
                    ROW_NUMBER() OVER (PARTITION BY sample_id, permohonan_uji_id, deleted_at ORDER BY id_lab_num) AS rn
              FROM tb_lab_num
              WHERE YEAR(created_at) = YEAR(CURDATE())
          ) AS subquery
          WHERE rn > 1
      ) AS duplicates
      ON tb_lab_num.id_lab_num = duplicates.id_lab_num;
  ");


  if (isset($plus_number)) {
    # code...

      $start_num=$plus_number;
      DB::statement("DROP TEMPORARY TABLE IF EXISTS OrderedSamples");


      DB::statement("
            CREATE TEMPORARY TABLE OrderedSamples AS (
                SELECT DISTINCT tb_samples.id_samples,
                      ROW_NUMBER() OVER (ORDER BY CAST(SUBSTRING_INDEX(tb_samples.codesample_samples, '/', 1) AS UNSIGNED)) + " . (int)$start_num . " AS row_num,
                      SUBSTRING(tb_samples.codesample_samples, LOCATE('/', tb_samples.codesample_samples)

                      ) AS suffix

                FROM tb_samples
                LEFT JOIN tb_lab_num ON tb_lab_num.sample_id = tb_samples.id_samples
                WHERE tb_lab_num.lab_id = :lab_id
                  AND tb_lab_num.is_makanan = 0
                  AND tb_lab_num.deleted_at IS NULL
                  AND tb_samples.deleted_at IS NULL
                  AND tb_lab_num.lab_number >= " . (int)$start_num . "
                  AND YEAR(tb_lab_num.created_at) = ".date('Y')."
                  AND YEAR(tb_samples.created_at) = ".date('Y')."

                  AND tb_lab_num.sample_id = (
                      SELECT sample_id FROM tb_lab_num
                      WHERE tb_lab_num.sample_id = tb_samples.id_samples
                        AND tb_lab_num.is_makanan = 0
                        AND tb_lab_num.deleted_at IS NULL
                        AND tb_samples.deleted_at IS NULL

                      LIMIT 1
                  )
            )
        ", ['lab_id' => $lab_id]);

            // Step 2: Update the main table based on the OrderedSamples table
            DB::statement("
                UPDATE tb_samples
                JOIN OrderedSamples ON tb_samples.id_samples = OrderedSamples.id_samples
                SET tb_samples.codesample_samples = CONCAT(LPAD(OrderedSamples.row_num, 4, '0'), OrderedSamples.suffix),
                    tb_samples.count_id = LPAD(OrderedSamples.row_num, 4, '0')
                WHERE YEAR(tb_samples.created_at) = ".date('Y').";

            ");
            DB::statement("DROP TEMPORARY TABLE IF EXISTS OrderedSamples");

            DB::statement("
            CREATE TEMPORARY TABLE OrderedSamples AS (
                SELECT DISTINCT tb_samples.id_samples,
                      ROW_NUMBER() OVER (
                          ORDER BY CAST(SUBSTRING_INDEX(tb_samples.codesample_samples, '/', 1) AS UNSIGNED)
                      ) + " . (int)$start_num . " AS row_num,
                      SUBSTRING(tb_samples.codesample_samples, LOCATE('/', tb_samples.codesample_samples)



                      ) AS suffix
                FROM tb_samples
                LEFT JOIN tb_lab_num ON tb_lab_num.sample_id = tb_samples.id_samples
                WHERE tb_lab_num.lab_id = :lab_id
                  AND tb_lab_num.is_makanan = 0
                  AND tb_lab_num.deleted_at IS NULL
                  AND tb_samples.deleted_at IS NULL
                  AND tb_lab_num.lab_number >= " . (int)$start_num . "
                   AND YEAR(tb_lab_num.created_at) = ".date('Y')."
                  AND YEAR(tb_samples.created_at) = ".date('Y')."


                  AND tb_lab_num.sample_id = (
                      SELECT sample_id FROM tb_lab_num
                      WHERE tb_lab_num.sample_id = tb_samples.id_samples
                        AND tb_lab_num.is_makanan = 0
                        AND tb_lab_num.deleted_at IS NULL
                        AND tb_samples.deleted_at IS NULL


                      LIMIT 1
                  )
            )
        ", ['lab_id' => $lab_id]);
        DB::statement("
            UPDATE tb_lab_num
            JOIN OrderedSamples ON tb_lab_num.sample_id = OrderedSamples.id_samples
            SET tb_lab_num.lab_number = LPAD(OrderedSamples.row_num, 4, '0')
         WHERE YEAR(tb_lab_num.created_at) = ".date('Y').";
            ");


            //   $data=  DB::select("

            //         SELECT
            //       DISTINCT tb_samples.id_samples,
            //         tb_samples.codesample_samples,
            //               ROW_NUMBER() OVER (
            //                   ORDER BY CAST(SUBSTRING_INDEX(tb_samples.codesample_samples, '/', 1) AS UNSIGNED)
            //               ) AS row_num
            //         FROM tb_samples
            //         LEFT JOIN tb_lab_num ON tb_lab_num.sample_id = tb_samples.id_samples
            //         WHERE tb_lab_num.lab_id = :lab_id
            //           AND  tb_lab_num.lab_number >= '". $start_num."'
            //           AND tb_lab_num.deleted_at IS NULL
            //           AND tb_samples.deleted_at IS NULL
            //           AND tb_lab_num.sample_id = (
            //               SELECT sample_id FROM tb_lab_num
            //               WHERE tb_lab_num.sample_id = tb_samples.id_samples
            //                 AND tb_lab_num.deleted_at IS NULL
            //                 AND tb_samples.deleted_at IS NULL
            //               LIMIT 1
            //           )

            // ", ['lab_id' => $lab_id]);

            // dd($data);
  }else{

    $start_num= StartNum::join('ms_laboratorium', function ($join) {
      $join->on('ms_laboratorium.kode_laboratorium', '=', 'ms_start_number.code_lab_start_number')
        ->whereNull('ms_laboratorium.deleted_at')
        ->whereNull('ms_start_number.deleted_at');
    })->where('id_laboratorium',$lab_id)->first();





    if (date('Y')==$start_num->year_start_number) {
      # code...
        $start_num=$start_num->count_start_number;
            // Step 1: Drop the temporary table if it exists
        DB::statement("DROP TEMPORARY TABLE IF EXISTS OrderedSamples");

        // Step 2: Create the OrderedSamples temporary table
        DB::statement("
            CREATE TEMPORARY TABLE OrderedSamples AS (
                SELECT DISTINCT tb_samples.id_samples,
                      ROW_NUMBER() OVER (
                          ORDER BY CAST(SUBSTRING_INDEX(tb_samples.codesample_samples, '/', 1) AS UNSIGNED)
                      ) + " . (int)$start_num . " AS row_num,
                      SUBSTRING(tb_samples.codesample_samples, LOCATE('/', tb_samples.codesample_samples)

                      ) AS suffix
                FROM tb_samples
                LEFT JOIN tb_lab_num ON tb_lab_num.sample_id = tb_samples.id_samples
                WHERE tb_lab_num.lab_id = :lab_id
                  AND tb_lab_num.deleted_at IS NULL
                  AND tb_samples.deleted_at IS NULL
                  AND YEAR(tb_samples.created_at) = ".date('Y')."
                  AND YEAR(tb_lab_num.created_at) = ".date('Y')."
                    AND tb_lab_num.is_makanan = 0
                  AND tb_lab_num.sample_id = (
                      SELECT sample_id FROM tb_lab_num
                      WHERE tb_lab_num.sample_id = tb_samples.id_samples
                        AND tb_lab_num.deleted_at IS NULL
                          AND tb_lab_num.is_makanan = 0

                        AND tb_samples.deleted_at IS NULL
                      LIMIT 1
                  )
            )
        ", ['lab_id' => $lab_id]);



        DB::statement("
            UPDATE tb_samples
            JOIN OrderedSamples ON tb_samples.id_samples = OrderedSamples.id_samples
            SET tb_samples.codesample_samples = CONCAT(LPAD(OrderedSamples.row_num, 4, '0'), OrderedSamples.suffix)
            AND YEAR(tb_samples.created_at) = ".date('Y').";

        ");


        // Step 1: Drop the temporary table if it exists
        DB::statement("DROP TEMPORARY TABLE IF EXISTS OrderedSamples");

        // Step 2: Create the OrderedSamples temporary table
        DB::statement("
            CREATE TEMPORARY TABLE OrderedSamples AS (
                SELECT DISTINCT tb_samples.id_samples,
                      ROW_NUMBER() OVER (
                          ORDER BY CAST(SUBSTRING_INDEX(tb_samples.codesample_samples, '/', 1) AS UNSIGNED)
                      ) + " . (int)$start_num . " AS row_num,
                      SUBSTRING(tb_samples.codesample_samples, LOCATE('/', tb_samples.codesample_samples)

                      ) AS suffix
                FROM tb_samples
                LEFT JOIN tb_lab_num ON tb_lab_num.sample_id = tb_samples.id_samples
                WHERE tb_lab_num.lab_id = :lab_id
                  AND tb_lab_num.deleted_at IS NULL
                  AND tb_samples.deleted_at IS NULL
                    AND YEAR(tb_samples.created_at) = ".date('Y')."
                  AND YEAR(tb_lab_num.created_at) = ".date('Y')."
                    AND tb_lab_num.is_makanan = 0
                  AND tb_lab_num.sample_id = (
                      SELECT sample_id FROM tb_lab_num
                      WHERE tb_lab_num.sample_id = tb_samples.id_samples
                        AND tb_lab_num.is_makanan = 0

                        AND tb_lab_num.deleted_at IS NULL
                        AND tb_samples.deleted_at IS NULL
                      LIMIT 1
                  )
            )
        ", ['lab_id' => $lab_id]);

        // Step 3: Update tb_lab_num using OrderedSamples
        DB::statement("
            UPDATE tb_lab_num
            JOIN OrderedSamples ON tb_lab_num.sample_id = OrderedSamples.id_samples
            SET tb_lab_num.lab_number = LPAD(OrderedSamples.row_num, 4, '0')
                AND YEAR(tb_lab_num.created_at) = ".date('Y').";
        ");
    }else{

      DB::statement("DROP TEMPORARY TABLE IF EXISTS OrderedSamples");

        // Step 2: Create the OrderedSamples temporary table
        DB::statement("
            CREATE TEMPORARY TABLE OrderedSamples AS (
                SELECT DISTINCT tb_samples.id_samples,
                      ROW_NUMBER() OVER (
                          ORDER BY CAST(SUBSTRING_INDEX(tb_samples.codesample_samples, '/', 1) AS UNSIGNED)
                      )  AS row_num,
                      SUBSTRING(tb_samples.codesample_samples, LOCATE('/', tb_samples.codesample_samples)

                      ) AS suffix
                FROM tb_samples
                LEFT JOIN tb_lab_num ON tb_lab_num.sample_id = tb_samples.id_samples
                WHERE tb_lab_num.lab_id = :lab_id
                  AND tb_lab_num.deleted_at IS NULL
                  AND tb_samples.deleted_at IS NULL
                  AND YEAR(tb_samples.created_at) = ".date('Y')."
                  AND YEAR(tb_lab_num.created_at) = ".date('Y')."
                    AND tb_lab_num.is_makanan = 0
                  AND tb_lab_num.sample_id = (
                      SELECT sample_id FROM tb_lab_num
                      WHERE tb_lab_num.sample_id = tb_samples.id_samples
                        AND tb_lab_num.deleted_at IS NULL
                          AND tb_lab_num.is_makanan = 0

                        AND tb_samples.deleted_at IS NULL
                      LIMIT 1
                  )
            )
        ", ['lab_id' => $lab_id]);



        DB::statement("
            UPDATE tb_samples
            JOIN OrderedSamples ON tb_samples.id_samples = OrderedSamples.id_samples
            SET tb_samples.codesample_samples = CONCAT(LPAD(OrderedSamples.row_num, 4, '0'), OrderedSamples.suffix);

        ");


        // Step 1: Drop the temporary table if it exists
        DB::statement("DROP TEMPORARY TABLE IF EXISTS OrderedSamples");

        // Step 2: Create the OrderedSamples temporary table
        DB::statement("
            CREATE TEMPORARY TABLE OrderedSamples AS (
                SELECT DISTINCT tb_samples.id_samples,
                      ROW_NUMBER() OVER (
                          ORDER BY CAST(SUBSTRING_INDEX(tb_samples.codesample_samples, '/', 1) AS UNSIGNED)
                      )  AS row_num,
                      SUBSTRING(tb_samples.codesample_samples, LOCATE('/', tb_samples.codesample_samples)

                      ) AS suffix
                FROM tb_samples
                LEFT JOIN tb_lab_num ON tb_lab_num.sample_id = tb_samples.id_samples
                WHERE tb_lab_num.lab_id = :lab_id
                  AND tb_lab_num.deleted_at IS NULL
                  AND tb_samples.deleted_at IS NULL
                    AND YEAR(tb_samples.created_at) = ".date('Y')."
                  AND YEAR(tb_lab_num.created_at) = ".date('Y')."
                    AND tb_lab_num.is_makanan = 0
                  AND tb_lab_num.sample_id = (
                      SELECT sample_id FROM tb_lab_num
                      WHERE tb_lab_num.sample_id = tb_samples.id_samples
                        AND tb_lab_num.is_makanan = 0

                        AND tb_lab_num.deleted_at IS NULL
                        AND tb_samples.deleted_at IS NULL
                      LIMIT 1
                  )
            )
        ", ['lab_id' => $lab_id]);

        // Step 3: Update tb_lab_num using OrderedSamples
        DB::statement("
            UPDATE tb_lab_num
            JOIN OrderedSamples ON tb_lab_num.sample_id = OrderedSamples.id_samples
            SET tb_lab_num.lab_number = LPAD(OrderedSamples.row_num, 4, '0');
        ");
    }



  }

  $start_num= StartNum::where('code_lab_start_number',"MAK-MIN")->first();

  // dd($start_num->count_start_number);

  $start_num=$start_num->count_start_number;



  // Step 1: Drop the temporary table if it exists
  DB::statement("DROP TEMPORARY TABLE IF EXISTS OrderedSamples");

  // Step 2: Create the OrderedSamples temporary table
  DB::statement("
      CREATE TEMPORARY TABLE OrderedSamples AS (
          SELECT DISTINCT tb_samples.id_samples,
                ROW_NUMBER() OVER (
                    ORDER BY CAST(SUBSTRING_INDEX(tb_samples.codesample_samples, '/', 1) AS UNSIGNED)
                ) + " . (int)$start_num . " AS row_num,
                SUBSTRING(tb_samples.codesample_samples, LOCATE('/', tb_samples.codesample_samples)

                ) AS suffix
          FROM tb_samples
          LEFT JOIN tb_lab_num ON tb_lab_num.sample_id = tb_samples.id_samples
          WHERE tb_lab_num.lab_id = :lab_id
           AND  tb_lab_num.deleted_at IS NULL
            AND tb_samples.deleted_at IS NULL
                 AND YEAR(tb_samples.created_at) = ".date('Y')."
              AND YEAR(tb_lab_num.created_at) = ".date('Y')."
              AND tb_lab_num.is_makanan = 1
            AND tb_lab_num.sample_id = (
                SELECT sample_id FROM tb_lab_num
                WHERE tb_lab_num.sample_id = tb_samples.id_samples
                  AND tb_lab_num.deleted_at IS NULL
                    AND tb_lab_num.is_makanan = 1

                  AND tb_samples.deleted_at IS NULL
                LIMIT 1
            )
      )
  ", ['lab_id' => $lab_id]);



  DB::statement("
      UPDATE tb_samples
      JOIN OrderedSamples ON tb_samples.id_samples = OrderedSamples.id_samples
      SET tb_samples.codesample_samples = CONCAT(LPAD(OrderedSamples.row_num, 4, '0'), OrderedSamples.suffix);
  ");


  // Step 1: Drop the temporary table if it exists
  DB::statement("DROP TEMPORARY TABLE IF EXISTS OrderedSamples");

  // Step 2: Create the OrderedSamples temporary table
  DB::statement("
      CREATE TEMPORARY TABLE OrderedSamples AS (
          SELECT DISTINCT tb_samples.id_samples,
                ROW_NUMBER() OVER (
                    ORDER BY CAST(SUBSTRING_INDEX(tb_samples.codesample_samples, '/', 1) AS UNSIGNED)
                ) + " . (int)$start_num . " AS row_num,
                SUBSTRING(tb_samples.codesample_samples, LOCATE('/', tb_samples.codesample_samples)

                ) AS suffix
          FROM tb_samples

          LEFT JOIN tb_lab_num ON tb_lab_num.sample_id = tb_samples.id_samples
          WHERE tb_lab_num.lab_id = :lab_id
           AND tb_lab_num.deleted_at IS NULL
            AND tb_samples.deleted_at IS NULL
              AND YEAR(tb_samples.created_at) = ".date('Y')."
              AND YEAR(tb_lab_num.created_at) = ".date('Y')."
              AND tb_lab_num.is_makanan = 1
            AND tb_lab_num.sample_id = (
                SELECT sample_id FROM tb_lab_num
                WHERE tb_lab_num.sample_id = tb_samples.id_samples
                  AND tb_lab_num.is_makanan = 1
                  AND tb_lab_num.deleted_at IS NULL

                  AND tb_samples.deleted_at IS NULL
                LIMIT 1
            )
      )
   ", ['lab_id' => $lab_id]);


  // Step 3: Update tb_lab_num using OrderedSamples
  DB::statement("
      UPDATE tb_lab_num
      JOIN OrderedSamples ON tb_lab_num.sample_id = OrderedSamples.id_samples
      SET tb_lab_num.lab_number = LPAD(OrderedSamples.row_num, 4, '0');
  ");
}

?>
