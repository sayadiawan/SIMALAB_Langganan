<?php

namespace Smt\Masterweb\Helpers;

use Carbon\Carbon;
use DateTime;

class DateHelper
{
    public static function formatDateIndo($date)
    {
        $months = [
          1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
          'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $carbonDate = \Carbon\Carbon::parse($date);
        return $carbonDate->day . ' ' . $months[$carbonDate->month] . ' ' . $carbonDate->year;
    }

    public static function formatDate($date)
    {
      return Carbon::parse($date)->format('d-m-Y H:i');
    }

    public static function formatOnlyDate($date)
    {
      return Carbon::parse($date)->format('d-m-Y');
    }

    // Format $dob = "YYYY-MM-DD"
    public static function calcAge($dob): int
    {
      if (!DateTime::createFromFormat('Y-m-d', $dob)) {
        return "Tanggal lahir tidak valid. Harus dalam format YYYY-MM-DD.";
      }

      try {
        $dob = new DateTime($dob);
        $today = new DateTime();
        $age = $today->diff($dob);

        return $age->y;
      } catch (\Exception $e){
        return "Terjadi kesalahan: " . $e->getMessage();
      }
    }
}
