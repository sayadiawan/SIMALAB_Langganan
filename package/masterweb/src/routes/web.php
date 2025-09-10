<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;



Route::group(['namespace' => 'Smt\Masterweb\Http\Controllers'], function () {
  Route::get('/scan/verification/{id}', ['as' => 'scan.verification', 'uses' => 'ScanController@verification']);

  // scan permohonan uji klinik
  Route::get('/qrcode/permohonan-uji-klinik/{text}/{size?}/{margin?}', [
    'uses' => 'ScanController@makeQrCodePermohonanUjiKlinik',
    'as'   => 'qrcode-permohonan-uji-klinik'
  ]);

  Route::get('/scan/permohonan-uji-klinik/{id}', ['as' => 'scan.permohonan-uji-klinik', 'uses' => 'ScanController@scanPermohonanUjiKlinik']);

  Route::get('/scan/print-permohonan-uji-klinik/{id}', ['as' => 'scan.print-permohonan-uji-klinik', 'uses' => 'ScanController@printPermohonanUjiKlinik']);

  Route::get('/scan/print/{id}/{idlab}', ['as' => 'scan.print', 'uses' => 'ScanController@printLHU']);
  Route::get('/scan/{id}', ['as' => 'scan.index', 'uses' => 'ScanController@index']);

  Route::get('/sm-master', 'AdmHomeController@index')->name('home');
  Route::get('/panel', 'AdmHomeController@index')->name('home');

  Route::get('/cronjob', 'AdmHomeController@index')->name('home');

  Route::get('/image/qrcode/{text}/{size?}/{margin?}', [
    'uses' => 'ImageController@makeQrCode',
    'as'   => 'qrcode'
  ]);




  // administrator route
  Route::group(['middleware' => ['web']], function () {
    Auth::routes(['verify' => true]);
    Route::get('/home', 'AdmHomeController@index')->name('home');
    Route::resource('biodata', 'AdmBiodataController');

    Route::get('adm-password', 'AdmPasswordController@edit')
      ->name('user.adm-password.edit');

    Route::put('adm-password', 'AdmPasswordController@update')
      ->name('user.adm-password.update');

    // Layout
    Route::get('adm-layout/type/{id}', 'AdmLayoutController@index');
    Route::post('adm-layout/store', 'AdmLayoutController@store');
    Route::get('adm-layout/getColumn', 'AdmLayoutController@columnData');
    Route::get('adm-layout/get_option_view', 'AdmLayoutController@getOption');

    // Users
    Route::resource('adm-users', 'UserController');
    Route::get('adm-users/reset/{param}', 'UserController@reset_password');
    // Privileges
    Route::resource('adm-privileges', 'AdmPrivilegesController');

    // Privileges
    Route::resource('privileges-elits', 'AdmPrivilegesElitsController');

    Route::post('privileges-features/store', 'AdmPrivilegesFeaturesController@store');
    Route::post('privileges-features/data', 'AdmPrivilegesFeaturesController@data');

    Route::post('privileges-features/update', ['as' => 'privileges-features.update', 'uses' => 'AdmPrivilegesFeaturesController@update']);
    Route::resource('privileges-features', 'AdmPrivilegesFeaturesController', ['except' => ['store', 'update']]);

    // Client
    Route::resource('adm-client', 'AdmClientController');
    Route::get('adm-client/publish/{id}', 'AdmClientController@publish');

    //Testimoni
    // Route::resource('adm-testimoni', 'AdmTestimoniController');
    // Route::get('adm-testimoni/publish/{id}', 'AdmTesimoniController@publish');

    //Category Portofolio
    Route::resource('adm-categoryportofolio', 'AdmCategoryPortofolioController');

    //Portofolio
    Route::resource('adm-portofolio', 'AdmPortofolioController');
    Route::get('adm-portofolio/publish/{id}', 'AdmPortofolioController@publish');



    Route::get('users/export/', 'UserController@export');
    Route::get('users/previlage/', 'UserController@previlage');



    //Route Admin Menu
    Route::resource('menuadm', 'AdmMenuController');
    Route::get('menuadm/index', 'AdmMenuController@index');
    Route::post('menuadm/sort', 'AdmMenuController@sort');
    Route::post('menuadm/store', 'AdmMenuController@store');
    Route::post('menuadm/data', 'AdmMenuController@data');
    Route::post('menuadm/update', 'AdmMenuController@update');
    Route::get('menuadm/change', 'AdmMenuController@change');
    Route::delete('menuadm/destroy/{id}', 'AdmMenuController@destroy');

    //Route Admin Diawan Menu
    Route::resource('menu-elits', 'AdmMenuElitsController');
    Route::get('menu-elits/index', 'AdmMenuElitsController@index');
    Route::post('menu-elits/sort', 'AdmMenuElitsController@sort');
    Route::post('menu-elits/store', 'AdmMenuElitsController@store');
    Route::post('menu-elits/data', 'AdmMenuElitsController@data');
    Route::post('menu-elits/update', 'AdmMenuElitsController@update');
    Route::get('menu-elits/change', 'AdmMenuElitsController@change');
    Route::delete('menu-elits/destroy/{id}', 'AdmMenuElitsController@destroy');


    //ROUTE MENU PUBLIC
    Route::resource('menu', 'AdmMenuPublicController');
    Route::get('menu/index', 'AdmMenuPublicController@index');
    Route::post('menu/sort', 'AdmMenuPublicController@sort');
    Route::post('menu/store', 'AdmMenuPublicController@store');
    Route::post('menu/data', 'AdmMenuPublicController@data');
    Route::post('menu/update', 'AdmMenuPublicController@update');
    Route::get('menu/change', 'AdmMenuPublicController@change');
    Route::delete('menu/destroy/{id}', 'AdmMenuPublicController@destroy');

    Route::resource('logo', 'AdmOptionsController');
    Route::get('favicon', 'AdmOptionsController@index_favicon');
    Route::put('favicon/{id}', 'AdmOptionsController@update_favicon');
    Route::get('metadata', 'AdmOptionsController@index_metadata');
    Route::put('metadata/{id}', 'AdmOptionsController@update_metadata');

    Route::get('adm-maps', 'AdmOptionsController@index_maps');
    Route::put('adm-maps/{id}', 'AdmOptionsController@update_maps');

    Route::resource('admsosmed', 'AdmSosmedController');

    // Admin
    Route::resource('admslideshow', 'AdmSlideshowController');
    Route::get('admslideshow/publish/{id}', 'AdmSlideshowController@publish');

    //ADMIN CONTENT
    Route::resource('admcontent', 'AdmContentController');

    //Admin Contact
    Route::resource('admcontact', 'AdmContactController');

    //Admin Contact
    Route::resource('admfeedback', 'AdmFeedbackController');

    //Admin Offer (Penawaran)
    Route::resource('admoffer', 'AdmOfferController');
    Route::get('admoffer/publish/{id}', 'AdmOfferController@publish');

    //Admin Contact
    Route::resource('adm-faq', 'AdmFaqController');
    Route::get('adm-faq/publish/{id}', 'AdmFaqController@publish');

    //Admin Layanan
    Route::resource('admlayanan', 'AdmLayananController');
    Route::resource('adm-categorylayanan', 'AdmCategoryLayananController');

    //generator
    // ubah
    Route::get('master/slideshow/{action?}/{id?}', 'MasterController@slideshow');
    Route::get('master/type/{action?}/{id?}', 'MasterController@type');
    //dilarang
    Route::post('master/SMStore', 'CrudController@store');
    Route::post('master/SMUpdate/{id}', 'CrudController@update');
    Route::get('master/SMDelete/{id}/{table}', 'CrudController@destroy');
    //end generator

    //Laboratorium





    Route::get('elits-release/getSamplePagination', ['as' => 'elits-release.getSamplePagination', 'uses' => 'LaboratoriumReleaseManagement@getSamplePagination']);

    // CETAK LAPORAN HARIAN, MINGGUAN, BULANAN
    Route::get('report-daily', [
      'as' => 'report-daily.index',
      'uses' => 'LaboratoriumReportManagement@report_daily'
    ]);

    Route::get('report-daily/data-report-daily', [
      'as' => 'report-daily.data-report-daily',
      'uses' => 'LaboratoriumReportManagement@data_report_daily'
    ]);

    Route::get('report-daily/print-report-daily', [
      'as' => 'report-daily.print-report-daily',
      'uses' => 'LaboratoriumReportManagement@printReportDaily'
    ]);

    Route::get('report/daily/{date_from?}/{date_to?}/', ['as' => 'report.daily', 'uses' => 'LaboratoriumReportManagement@daily']);

    Route::get('report-weekly', ['as' => 'report-weekly.index', 'uses' => 'LaboratoriumReportManagement@report_weekly']);
    Route::get('report-weekly/data-report-weekly', ['as' => 'report-weekly.data-report-weekly', 'uses' => 'LaboratoriumReportManagement@data_report_weekly']);
    Route::get('report-weekly/print-report-weekly', ['as' => 'report-weekly.print-report-weekly', 'uses' => 'LaboratoriumReportManagement@printReportWeekly']);
    Route::get('report/weekly/{date_from?}/{date_to?}/', ['as' => 'report.weekly', 'uses' => 'LaboratoriumReportManagement@weekly']);

    Route::get('report-monthly', ['as' => 'report-monthly.index', 'uses' => 'LaboratoriumReportManagement@report_monthly']);
    Route::get('report-monthly/data-report-monthly', ['as' => 'report-monthly.data-report-monthly', 'uses' => 'LaboratoriumReportManagement@data_report_monthly']);
    Route::get('report-monthly/print-report-monthly', ['as' => 'report-monthly.print-report-monthly', 'uses' => 'LaboratoriumReportManagement@printReportMonthly']);
    Route::get('report-monthly/print-report-monthly-excel', ['as' => 'report-monthly.print-report-monthly-excel', 'uses' => 'LaboratoriumReportManagement@printReportMonthly_to_excel']);
    Route::get('report-monthly/print-report-monthly-maatweb', ['as' => 'report-monthly.print-report-monthly-maatweb', 'uses' => 'LaboratoriumReportManagement@printReportMonthly_to_maatweb']);

    Route::get('report-date-verification-monthly', ['as' => 'report-date-verification-monthly.index', 'uses' => 'LaboratoriumReportManagement@report_date_verification_monthly']);
    Route::get('report-date-verification-monthly/data-date-verification-report-monthly', ['as' => 'report-date-verification-monthly.data-report-date-verification-monthly', 'uses' => 'LaboratoriumReportManagement@data_report_date_verification_monthly']);
    Route::get('report-date-verification-monthly/print-date-verification-report-monthly', ['as' => 'report-date-verification-monthly.print-report-date-verification-monthly', 'uses' => 'LaboratoriumReportManagement@printReportDateVerificationMonthly']);
    Route::get('report-date-verification-monthly/print-date-verification-report-monthly-excel', ['as' => 'report-date-verification-monthly.print-report-date-verification-monthly-excel', 'uses' => 'LaboratoriumReportManagement@printReportDateVerificationMonthly_to_excel']);
    Route::get('report-date-verification-monthly/print-date-verification-report-monthly-maatweb', ['as' => 'report-date-verification-monthly.print-report-date-verification-monthly-maatweb', 'uses' => 'LaboratoriumReportManagement@printReportDateVerificationMonthly_to_maatweb']);


    Route::get('report-daily/print-report-daily-maatweb', ['as' => 'report-daily.print-report-daily-maatweb', 'uses' => 'LaboratoriumReportManagement@printReportDaily_to_maatweb']);

    Route::get('report-daily/get-total-harga-sample-daily', [
      'as' => 'get-total-harga-sample-daily',
      'uses' => 'LaboratoriumReportManagement@getTotalHargaSampleDaily'
    ]);

    Route::get('report-annual', ['as' => 'report-annual.index', 'uses' => 'LaboratoriumReportManagement@report_annual']);
    Route::get('report-annual/data-report-annual', ['as' => 'report-annual.data-report-annual', 'uses' => 'LaboratoriumReportManagement@data_report_annual']);
    Route::get('report-annual/print-report-annual', ['as' => 'report-annual.print-report-annual', 'uses' => 'LaboratoriumReportManagement@printReportAnnual']);
    Route::get('report-annual/print-report-annual-maatweb', ['as' => 'report-annual.print-report-annual-maatweb', 'uses' => 'LaboratoriumReportManagement@printReportAnually_to_maatweb']);

    Route::get('report/yearly/{date_from?}/{date_to?}/', ['as' => 'report.yearly', 'uses' => 'LaboratoriumReportManagement@yearly']);
    // CETAK LAPORAN HARIAN, MINGGUAN, BULANAN

    // ROUTE MASTER
    Route::resource('elits-release', 'LaboratoriumReleaseManagement');
    Route::resource('elits-containers', 'LaboratoriumContainerManagement');



    // ROUTE MASTER PARAMETER JENIS KLINIK
    Route::resource('elits-parameter-jenis-klinik', 'LaboratoriumParameterJenisKlinikManagement');
    Route::post('elits-parameter-jenis-klinik/getParameterJenisKlinik', 'LaboratoriumParameterJenisKlinikManagement@getParameterJenisKlinik')->name('getParameterJenisKlinik');
    Route::get('elits-parameter-jenis-klinik-destroy/{id}', 'LaboratoriumParameterJenisKlinikManagement@destroy');

    // ROUTE MASTER PARAMETER SATUAN KLINIK
    Route::resource('elits-parameter-satuan-klinik', 'LaboratoriumParameterSatuanKlinikManagement');
    Route::post('elits-parameter-satuan-klinik/getParameterSatuanKlinik', 'LaboratoriumParameterSatuanKlinikManagement@getParameterSatuanKlinik')->name('getParameterSatuanKlinik');
    Route::get('elits-parameter-satuan-klinik-destroy/{id}', 'LaboratoriumParameterSatuanKlinikManagement@destroy');

    // ROUTE MASTER PARAMETER PAKET KLINIK
    Route::resource('elits-parameter-paket-klinik', 'LaboratoriumParameterPaketKlinikManagement');
    Route::post('elits-parameter-paket-klinik/getParameterPaketKlinik', 'LaboratoriumParameterPaketKlinikManagement@getParameterPaketKlinik')->name('getParameterPaketKlinik');
    Route::get('elits-parameter-paket-klinik-destroy/{id}', 'LaboratoriumParameterPaketKlinikManagement@destroy');

    // ROUTE MASTER PARAMETER EXTRA PAKET KLINIK
    Route::resource('elits-parameter-paket-extra', 'LaboratoriumParameterPaketExtraKlinikManagement');
    Route::get('elits-parameter-paket-extra-destroy/{id}', 'LaboratoriumParameterPaketExtraKlinikManagement@destroy');

    // ROUTE MASTER PROGRAM
    Route::get('data-program', ['as' => 'elits-program.data-program', 'uses' => 'LaboratoriumProgramManagement@data_program']);
    Route::post('elits-program/get-program', 'LaboratoriumProgramManagement@getProgram')->name('getProgram');
    Route::get('elits-program-destroy/{id}', 'LaboratoriumProgramManagement@destroy');
    Route::resource('elits-program', 'LaboratoriumProgramManagement');


    //permohonan uji klinik parameter 2
    Route::post('elits-permohonan-uji-klinik-2/get-parameter-custom-permohonan-uji-klinik-parameter', ['as' => 'elits-permohonan-uji-klinik-2.get-parameter-custom-permohonan-uji-klinik-parameter', 'uses' => 'LaboratoriumPermohonanUjiKlinikParameterManagement2@getDataParameterCustom']);
    Route::get('elits-permohonan-uji-klinik-2/create-permohonan-uji-klinik-parameter/{id}', ['as' => 'elits-permohonan-uji-klinik-2.create-permohonan-uji-klinik-parameter', 'uses' => 'LaboratoriumPermohonanUjiKlinikParameterManagement2@create']);
    Route::post('elits-permohonan-uji-klinik-2/store-permohonan-uji-klinik-parameter/{id}', ['as' => 'elits-permohonan-uji-klinik-2.store-permohonan-uji-klinik-parameter', 'uses' => 'LaboratoriumPermohonanUjiKlinikParameterManagement2@store']);
    Route::get('elits-permohonan-uji-klinik-2/permohonan-uji-klinik-parameter/{id}', ['as' => 'elits-permohonan-uji-klinik-2.permohonan-uji-klinik-parameter', 'uses' => 'LaboratoriumPermohonanUjiKlinikParameterManagement2@index']);
    Route::get('elits-permohonan-uji-klinik-2/test-bpjs', ['as' => 'elits-permohonan-uji-klinik-2.testBpjs', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement2@testBpjs']);
    Route::get('elits-permohonan-uji-klinik-2/sorting-number-klinik', ['as' => 'elits-permohonan-uji-klinik-2.sortingNumberKlinikAll', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement2@sortingNumberKlinikAll']);



    Route::get('elits-permohonan-uji-klinik-destroy-2/{id}', 'LaboratoriumPermohonanUjiKlinikManagement2@destroy');



    // Route::get('test-bpjs', ['as' => 'elits-permohonan-uji-klinik-2.test-bpjs', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement2@testBpjs']);

    //verifikasi klinik
    Route::get('elits-permohonan-uji-klinik-2/verification/{id}', ['as' => 'elits-permohonan-uji-klinik-2.verification', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement2@verification']);
    Route::post('elits-permohonan-uji-klinik-2/verification/analytic/{id}', ['as' => 'elits-permohonan-uji-klinik-2.verification-analytic', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement2@verificationAnalytic']);
    Route::get('elits-permohonan-uji-klinik-2/print_verifikasi/{id}', ['as' => 'elits-permohonan-uji-klinik-2.print_verifikasi', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement2@print_verifikasi']);

    //prolanis klinik
    Route::get('elits-permohonan-uji-klinik-2/prolanis', ['as' => 'elits-permohonan-uji-klinik-2.prolanis', 'uses' => 'LaboratoriumPermohonanUjiKlinikProlanisManagement@index']);
    Route::get('elits-permohonan-uji-klinik-2/create-prolanis', ['as' => 'elits-permohonan-uji-klinik-2.create-prolanis', 'uses' => 'LaboratoriumPermohonanUjiKlinikProlanisManagement@create']);
    Route::post('elits-permohonan-uji-klinik-2/store-prolanis', ['as' => 'elits-permohonan-uji-klinik-2.store-prolanis', 'uses' => 'LaboratoriumPermohonanUjiKlinikProlanisManagement@store']);
    Route::get('elits-permohonan-uji-klinik-2/download-format-prolanis/gula/{id}', ['as' => 'elits-permohonan-uji-klinik-2.format-prolanis-gula', 'uses' => 'LaboratoriumPermohonanUjiKlinikProlanisManagement@downloadFormatProlanisGula']);
    Route::get('elits-permohonan-uji-klinik-2/download-format-prolanis/urine/{id}', ['as' => 'elits-permohonan-uji-klinik-2.format-prolanis-urine', 'uses' => 'LaboratoriumPermohonanUjiKlinikProlanisManagement@downloadFormatProlanisUrine']);
    Route::post('elits-permohonan-uji-klinik-2/import-prolanis/gula/{id}', ['as' => 'elits-permohonan-uji-klinik-2.importProlanisGula', 'uses' => 'LaboratoriumPermohonanUjiKlinikProlanisManagement@importProlanisGula']);
    Route::post('elits-permohonan-uji-klinik-2/import-prolanis/urine/{id}', ['as' => 'elits-permohonan-uji-klinik-2.importProlanisUrine', 'uses' => 'LaboratoriumPermohonanUjiKlinikProlanisManagement@importProlanisUrine']);
    Route::get('elits-permohonan-uji-klinik-2/get-prolanis-gula', ['as' => 'elits-permohonan-uji-klinik-2.get-prolanis-gula', 'uses' => 'LaboratoriumPermohonanUjiKlinikProlanisManagement@getProlanisGula']);
    Route::get('elits-permohonan-uji-klinik-2/get-prolanis-gula', ['as' => 'elits-permohonan-uji-klinik-2.get-prolanis-gula', 'uses' => 'LaboratoriumPermohonanUjiKlinikProlanisManagement@getProlanisGula']);
    Route::get('elits-permohonan-uji-klinik-2/get-prolanis-urine', ['as' => 'elits-permohonan-uji-klinik-2.get-prolanis-urine', 'uses' => 'LaboratoriumPermohonanUjiKlinikProlanisManagement@getProlanisUrine']);
    Route::get('elits-permohonan-uji-klinik-2/get-prolanis', ['as' => 'elits-permohonan-uji-klinik-2.get-prolanis', 'uses' => 'LaboratoriumPermohonanUjiKlinikProlanisManagement@getAllProlanis']);
    Route::get('elits-permohonan-uji-klinik-2/destroy-prolanis/{id}', ['as' => 'elits-permohonan-uji-klinik-2.destroy-prolanis', 'uses' => 'LaboratoriumPermohonanUjiKlinikProlanisManagement@destroy']);


    //prolanis gula klinik
    Route::get('elits-permohonan-uji-klinik-2/prolanis-gula', ['as' => 'elits-permohonan-uji-klinik-2.prolanis-gula', 'uses' => 'LaboratoriumPermohonanUjiKlinikProlanisGulaManagement@index']);
    Route::get('elits-permohonan-uji-klinik-2/create-prolanis-gula', ['as' => 'elits-permohonan-uji-klinik-2.create-prolanis-gula', 'uses' => 'LaboratoriumPermohonanUjiKlinikProlanisGulaManagement@create']);
    Route::post('elits-permohonan-uji-klinik-2/store-prolanis-gula', ['as' => 'elits-permohonan-uji-klinik-2.store-prolanis-gula', 'uses' => 'LaboratoriumPermohonanUjiKlinikProlanisGulaManagement@store']);
    Route::get('elits-permohonan-uji-klinik-2/download-format-prolanis-gula/{id}', ['as' => 'elits-permohonan-uji-klinik-2.download-format-prolanis-gula', 'uses' => 'LaboratoriumPermohonanUjiKlinikProlanisGulaManagement@downloadFormatProlanisGula']);
    Route::post('elits-permohonan-uji-klinik-2/import-prolanis-gula/{id}', ['as' => 'elits-permohonan-uji-klinik-2.import-prolanis-gula', 'uses' => 'LaboratoriumPermohonanUjiKlinikProlanisGulaManagement@importProlanisGula']);
    // Route::get('elits-permohonan-uji-klinik-2/get-prolanis-gula', ['as' => 'elits-permohonan-uji-klinik-2.get-prolanis-gula', 'uses' => 'LaboratoriumPermohonanUjiKlinikProlanisGulaManagement@getProlanisGula']);

    //prolanis urine klinik
    Route::get('elits-permohonan-uji-klinik-2/prolanis-urine', ['as' => 'elits-permohonan-uji-klinik-2.prolanis-urine', 'uses' => 'LaboratoriumPermohonanUjiKlinikProlanisUrineManagement@index']);
    Route::get('elits-permohonan-uji-klinik-2/create-prolanis-urine', ['as' => 'elits-permohonan-uji-klinik-2.create-prolanis-urine', 'uses' => 'LaboratoriumPermohonanUjiKlinikProlanisUrineManagement@create']);
    Route::post('elits-permohonan-uji-klinik-2/store-prolanis-urine', ['as' => 'elits-permohonan-uji-klinik-2.store-prolanis-urine', 'uses' => 'LaboratoriumPermohonanUjiKlinikProlanisUrineManagement@store']);
    Route::get('elits-permohonan-uji-klinik-2/download-format-prolanis-urine/{id}', ['as' => 'elits-permohonan-uji-klinik-2.download-format-prolanis-urine', 'uses' => 'LaboratoriumPermohonanUjiKlinikProlanisUrineManagement@downloadFormatProlanisUrine']);
    Route::post('elits-permohonan-uji-klinik-2/import-prolanis-urine/{id}', ['as' => 'elits-permohonan-uji-klinik-2.import-prolanis-urine', 'uses' => 'LaboratoriumPermohonanUjiKlinikProlanisUrineManagement@importProlanisUrine']);
    // Route::get('elits-permohonan-uji-klinik-2/get-prolanis-urine', ['as' => 'elits-permohonan-uji-klinik-2.get-prolanis-urine', 'uses' => 'LaboratoriumPermohonanUjiKlinikProlanisUrineManagement@getProlanisUrine']);

    //haji klinik
    Route::get('elits-permohonan-uji-klinik-2/haji', ['as' => 'elits-permohonan-uji-klinik-2.haji', 'uses' => 'LaboratoriumPermohonanUjiKlinikHajiManagement@index']);
    Route::get('elits-permohonan-uji-klinik-2/create-haji', ['as' => 'elits-permohonan-uji-klinik-2.create-haji', 'uses' => 'LaboratoriumPermohonanUjiKlinikHajiManagement@create']);
    Route::post('elits-permohonan-uji-klinik-2/store-haji', ['as' => 'elits-permohonan-uji-klinik-2.store-haji', 'uses' => 'LaboratoriumPermohonanUjiKlinikHajiManagement@store']);
    Route::get('elits-permohonan-uji-klinik-2/download-format-haji/{id}', ['as' => 'elits-permohonan-uji-klinik-2.download-format-haji', 'uses' => 'LaboratoriumPermohonanUjiKlinikHajiManagement@downloadFormatHaji']);
    Route::post('elits-permohonan-uji-klinik-2/import-haji/{id}', ['as' => 'elits-permohonan-uji-klinik-2.import-haji', 'uses' => 'LaboratoriumPermohonanUjiKlinikHajiManagement@importHaji']);
    Route::get('elits-permohonan-uji-klinik-2/get-haji', ['as' => 'elits-permohonan-uji-klinik-2.get-haji', 'uses' => 'LaboratoriumPermohonanUjiKlinikHajiManagement@getHaji']);
    Route::get('elits-permohonan-uji-klinik-2/print-amplop/{id}', ['as' => 'elits-permohonan-uji-klinik-2.print-amplop', 'uses' => 'LaboratoriumPermohonanUjiKlinikHajiManagement@printAmplop']);
    Route::get('elits-permohonan-uji-klinik-2/print-amplop-prolanis/{id}', ['as' => 'elits-permohonan-uji-klinik-2.print-amplop-prolanis', 'uses' => 'LaboratoriumPermohonanUjiKlinikProlanisManagement@printAmplopProlanis']);
    Route::get('elits-permohonan-uji-klinik-2/destroy-haji/{id}', ['as' => 'elits-permohonan-uji-klinik-2.destroy-haji', 'uses' => 'LaboratoriumPermohonanUjiKlinikHajiManagement@destroy']);


    //permohonan uji klinik 2
    Route::get('elits-permohonan-uji-klinik-2/bukti-daftar-permohonan-uji-parameter/{id}', ['as' => 'elits-permohonan-uji-klinik-2.bukti-daftar-permohonan-uji-parameter', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement2@buktiDaftarPermohonanUjiParameter']);
    Route::get('data-permohonan-uji-klinik-2', ['as' => 'elits-permohonan-uji-klinik-2.data-permohonan-uji-klinik', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement2@data_permohonan_uji_klinik']);
    Route::get('elits-permohonan-uji-klinik-2/bukti-daftar-permohonan-uji-parameter/{id}', ['as' => 'elits-permohonan-uji-klinik-2.bukti-daftar-permohonan-uji-parameter', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement2@buktiDaftarPermohonanUjiParameter']);
    Route::post('elits-permohonan-uji-klinik-2/storeDataPermohonanUjiKlinikPayment/{id}', ['as' => 'elits-permohonan-uji-klinik-2.storeDataPermohonanUjiKlinikPayment', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement2@storeDataPermohonanUjiKlinikPayment']);
    Route::post('elits-permohonan-uji-klinik-2/updateDataPermohonanUjiKlinikPayment/{id}', ['as' => 'elits-permohonan-uji-klinik-2.updateDataPermohonanUjiKlinikPayment', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement2@updateDataPermohonanUjiKlinikPayment']);
    Route::get('elits-permohonan-uji-klinik-2/add-parameter/', ['as' => 'elits-permohonan-uji-klinik-2.add-parameter', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement2@addParameter']);
    Route::post('elits-permohonan-uji-klinik-2/store-parameter', ['as' => 'elits-permohonan-uji-klinik-2.store-parameter', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement2@storeParameter']);
    Route::get('data-permohonan-uji-klinik-2', ['as' => 'elits-permohonan-uji-klinik-2.data-permohonan-uji-klinik', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement2@data_permohonan_uji_klinik']);
    Route::resource('elits-permohonan-uji-klinik-2', 'LaboratoriumPermohonanUjiKlinikManagement2');
    Route::get('list-pasien-satu-sehat', ['as' => 'get-list-pasien-satu-sehat', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement2@getPatients']);
    Route::get('list-pasien-silaboy', ['as' => 'get-list-pasien-silaboy', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement2@getPatientsSilaboy']);

    // UNTUK SAMPLEPERMOHONAN UJI KLINIK
    Route::get('elits-permohonan-uji-klinik-2/create-permohonan-uji-sample/{id_puk}', ['as' => 'elits-permohonan-uji-klinik-2.create-permohonan-uji-sample', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement2@createPermohonanUjiSample']);
    Route::put('elits-permohonan-uji-klinik-2/store-permohonan-uji-sample/{id_puk}', ['as' => 'elits-permohonan-uji-klinik-2.store-permohonan-uji-sample', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement2@storePermohonanUjiSample']);

    // UNTUK ANALIS PERMOHONAN UJI KLINIK
    Route::get('elits-permohonan-uji-klinik-2/create-permohonan-uji-analis2/{id_puk}', ['as' => 'elits-permohonan-uji-klinik-2.create-permohonan-uji-analis2', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement2@createPermohonanUjiAnalis']);
    Route::get('elits-permohonan-uji-klinik-2/disabled-permohonan-uji-analis2/{id_puk}', ['as' => 'elits-permohonan-uji-klinik-2.disabled-permohonan-uji-analis2', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement2@disabledPermohonanUjiAnalis']);
    Route::put('elits-permohonan-uji-klinik-2/store-permohonan-uji-analis2/{id_puk}', ['as' => 'elits-permohonan-uji-klinik-2.store-permohonan-uji-analis2', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement2@storePermohonanUjiAnalis']);


    // UNTUK DOKTER PERMOHONAN UJI KLINIK
    Route::get('elits-permohonan-uji-klinik-2/create-permohonan-uji-rekomendasi-dokter/{id}', ['as' => 'elits-permohonan-uji-klinik-2.create-permohonan-uji-rekomendasi-dokter', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement2@createPermohonanUjiRekomendasiDokter']);
    Route::post('elits-permohonan-uji-klinik-2/store-permohonan-uji-rekomendasi-dokter/{id}', ['as' => 'elits-permohonan-uji-klinik-2.store-permohonan-uji-rekomendasi-dokter', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement2@storePermohonanUjiRekomendasiDokter']);


    // PRINT KARTU MEDIS
    Route::get('print-permohonan-uji-klinik-kartu-medis-2/{id}', ['as' => 'elits-permohonan-uji-klinik-2.print-permohonan-uji-klinik-kartu-medis', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement2@printPermohonanUjiKlinikKartuMedis']);

    // PRINT OUT PERMOHONAN UJI KLINIK (NOTA)
    Route::get('print-permohonan-uji-klinik-nota-2/{id}', ['as' => 'elits-permohonan-uji-klinik-2.print-permohonan-uji-klinik-nota', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement2@printPermohonanUjiKlinikNota']);

    // PELUNASAN PEMBAYARAN PERMOHONAN UJI KLINIK
    Route::post('permohonan-uji-klinik-get-payment', ['as' => 'permohonan-uji-klinik-get-payment', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement2@getDataPermohonanUjiKlinikPayment']);
    Route::post('permohonan-uji-klinik-store-payment', ['as' => 'permohonan-uji-klinik-store-payment', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement2@storeDataPermohonanUjiKlinikPayment']);

    // PRINT OUT PERMOHONAN UJI KLINIK (HASIL KLINIK)
    Route::get('print-permohonan-uji-klinik-hasil-2/{id}', ['as' => 'elits-permohonan-uji-klinik-2.print-permohonan-uji-klinik-hasil', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement2@printPermohonanUjiKlinikHasil']);


    // PRINT OUT PERMOHONAN UJI KLINIK (HASIL RAPID ANTIBODY)
    Route::get('print-permohonan-uji-klinik-hasil-rapid-antibody-2/{id}', ['as' => 'elits-permohonan-uji-klinik-2.print-permohonan-uji-klinik-hasil-rapid-antibody', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement2@printPermohonanUjiKlinikHasilRapidAntibody']);

    // PRINT OUT PERMOHONAN UJI KLINIK (HASIL RAPID ANTIGEN)
    Route::get('print-permohonan-uji-klinik-hasil-rapid-antigen-2/{id}', ['as' => 'elits-permohonan-uji-klinik-2.print-permohonan-uji-klinik-hasil-rapid-antigen', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement2@printPermohonanUjiKlinikHasilRapidAntigen']);

    // PRINT OUT PERMOHONAN UJI KLINIK (HASIL PCR)
    Route::get('print-permohonan-uji-klinik-hasil-pcr-2/{id}', ['as' => 'elits-permohonan-uji-klinik-2.print-permohonan-uji-klinik-hasil-pcr', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement2@printPermohonanUjiKlinikHasilPcr']);

    // PRINT OUT PERMOHONAN UJI KLINIK (QRCODE)
    Route::get('print-permohonan-uji-klinik-qrcode-2/{id}', ['as' => 'elits-permohonan-uji-klinik-2.print-permohonan-uji-klinik-qrcode', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement2@printPermohonanUjiKlinikQrcode']);

    // PRINT LABEL PERMOHONAN UJI KLINIK
    Route::get('elits-label-permohonan-uji-klinik-2', 'LaboratoriumPermohonanUjiKlinikManagement2@label');
    Route::get('elits-label-permohonan-uji-klinik-2/print', 'LaboratoriumPermohonanUjiKlinikManagement2@printLabel')->name('elits-permohonan-uji-klinik-2.print-label');


    Route::get('elits-permohonan-uji-klinik/', ['as' => 'elits-permohonan-uji-klinik.index', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement@index']);

    Route::get('elits-permohonan-uji-klinik/analys/{id}/{id_method?}', ['as' => 'elits-permohonan-uji-klinik.analys', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement@analys']);

    Route::get('elits-permohonan-uji-klinik/getSamplePagination', ['as' => 'elits-permohonan-uji-klinik.getSamplePagination', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement@getSamplePagination']);
    Route::post('elits-permohonan-uji-klinik/getSamplePagination', ['as' => 'elits-permohonan-uji-klinik.getSamplePagination', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement@getSamplePagination']);

    Route::get('elits-permohonan-uji-klinik/getIdSample/{id}', ['as' => 'elits-permohonan-uji-klinik.getIdSample', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement@getIdSample']);
    Route::get('elits-permohonan-uji-klinik/getPacketDetail/{id}', ['as' => 'elits-permohonan-uji-klinik.getPacketDetail', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement@getPacketDetail']);
    Route::post('elits-permohonan-uji-klinik/setPersiapanSample/{id}', ['as' => 'elits-permohonan-uji-klinik.setPersiapanSample', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement@setPersiapanSample']);
    Route::post('elits-permohonan-uji-klinik/setSampling/{id}', ['as' => 'elits-permohonan-uji-klinik.setSampling', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement@setSampling']);
    Route::get('elits-permohonan-uji-klinik/daftarPengujian/{id}', ['as' => 'elits-permohonan-uji-klinik.daftarPengujian', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement@daftarPengujian']);
    Route::get('elits-permohonan-uji-klinik/print/{id}', ['as' => 'elits-permohonan-uji-klinik.print', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement@print']);
    Route::post('elits-permohonan-uji-klinik/payment/{id}', ['as' => 'elits-permohonan-uji-klinik.payment', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement@payment']);

    // GET URL KETIKA USER TAMBAH/MENGUBAH PARAMETER DARI PERMOHONAN UJI KLINIK
    // #1 attempt
    /* Route::get('elits-permohonan-uji-klinik/create-permohonan-uji-parameter/{id}', ['as' => 'elits-permohonan-uji-klinik.create-permohonan-uji-parameter', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement@createPermohonanUjiParameter']);

    Route::get('elits-permohonan-uji-klinik/bukti-daftar-permohonan-uji-parameter/{id}', ['as' => 'elits-permohonan-uji-klinik.bukti-daftar-permohonan-uji-parameter', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement@buktiDaftarPermohonanUjiParameter']);

    Route::post('elits-permohonan-uji-klinik/get-parameter-dan-harga', ['as' => 'elits-permohonan-uji-klinik.get-parameter-dan-harga', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement@getParameterDanHarga']);
    Route::post('elits-permohonan-uji-klinik/count-parameter-dan-harga', ['as' => 'elits-permohonan-uji-klinik.count-parameter-dan-harga', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement@countParameterDanHarga']);

    Route::post('elits-permohonan-uji-klinik/store-permohonan-uji-parameter', ['as' => 'elits-permohonan-uji-klinik.store-permohonan-uji-parameter', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement@storePermohonanUjiParameter']);
    Route::put('elits-permohonan-uji-klinik/update-permohonan-uji-parameter/{id}', ['as' => 'elits-permohonan-uji-klinik.update-permohonan-uji-parameter', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement@updatePermohonanUjiParameter']); */

    #2 attempt
    Route::get('elits-permohonan-uji-klinik/create-permohonan-uji-parameter/{id}', ['as' => 'elits-permohonan-uji-klinik.create-permohonan-uji-parameter', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement@createPermohonanUjiParameter']);

    Route::get('elits-permohonan-uji-klinik/bukti-daftar-permohonan-uji-parameter/{id}', ['as' => 'elits-permohonan-uji-klinik.bukti-daftar-permohonan-uji-parameter', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement@buktiDaftarPermohonanUjiParameter']);

    Route::post('elits-permohonan-uji-klinik/get-parameter-dan-harga', ['as' => 'elits-permohonan-uji-klinik.get-parameter-dan-harga', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement@getParameterDanHarga']);
    Route::post('elits-permohonan-uji-klinik/count-parameter-dan-harga', ['as' => 'elits-permohonan-uji-klinik.count-parameter-dan-harga', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement@countParameterDanHarga']);

    Route::post('elits-permohonan-uji-klinik/store-permohonan-uji-parameter', ['as' => 'elits-permohonan-uji-klinik.store-permohonan-uji-parameter', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement@storePermohonanUjiParameter']);

    Route::get('elits-permohonan-uji-klinik/permohonan-uji-klinik-parameter/{id}', ['as' => 'elits-permohonan-uji-klinik.permohonan-uji-klinik-parameter', 'uses' => 'LaboratoriumPermohonanUjiKlinikParameterManagement@index']);

    Route::get('elits-permohonan-uji-klinik/create-permohonan-uji-klinik-parameter/{id}', ['as' => 'elits-permohonan-uji-klinik.create-permohonan-uji-klinik-parameter', 'uses' => 'LaboratoriumPermohonanUjiKlinikParameterManagement@create']);
    Route::post('elits-permohonan-uji-klinik/store-permohonan-uji-klinik-parameter/{id}', ['as' => 'elits-permohonan-uji-klinik.store-permohonan-uji-klinik-parameter', 'uses' => 'LaboratoriumPermohonanUjiKlinikParameterManagement@store']);

    Route::get('elits-permohonan-uji-klinik/show-permohonan-uji-klinik-parameter/{id}/{id_paket}', ['as' => 'elits-permohonan-uji-klinik.show-permohonan-uji-klinik-parameter', 'uses' => 'LaboratoriumPermohonanUjiKlinikParameterManagement@show']);

    Route::get('elits-permohonan-uji-klinik/edit-permohonan-uji-klinik-parameter/{id}/{id_paket}', ['as' => 'elits-permohonan-uji-klinik.edit-permohonan-uji-klinik-parameter', 'uses' => 'LaboratoriumPermohonanUjiKlinikParameterManagement@edit']);
    Route::post('elits-permohonan-uji-klinik/update-permohonan-uji-klinik-parameter/{id}/{id_paket}', ['as' => 'elits-permohonan-uji-klinik.update-permohonan-uji-klinik-parameter', 'uses' => 'LaboratoriumPermohonanUjiKlinikParameterManagement@update']);

    Route::get('elits-permohonan-uji/destroy-permohonan-uji-klinik-parameter/{id}', ['as' => 'elits-permohonan-uji.destroy-permohonan-uji-klinik-parameter', 'uses' => 'LaboratoriumPermohonanUjiKlinikParameterManagement@destroy']);

    Route::get('elits-permohonan-uji-2/destroy-permohonan-uji-klinik-parameter/{id}', ['as' => 'elits-permohonan-uji-2.destroy-permohonan-uji-klinik-parameter', 'uses' => 'LaboratoriumPermohonanUjiKlinikParameterManagement2@destroy']);



    // get parameter by jenis parameter dan jenis paket
    Route::post('elits-permohonan-uji-klinik/get-parameter-custom-permohonan-uji-klinik-parameter', ['as' => 'elits-permohonan-uji-klinik.get-parameter-custom-permohonan-uji-klinik-parameter', 'uses' => 'LaboratoriumPermohonanUjiKlinikParameterManagement@getDataParameterCustom']);
    Route::post('elits-permohonan-uji-klinik/get-parameter-paket-permohonan-uji-klinik-parameter', ['as' => 'elits-permohonan-uji-klinik.get-parameter-paket-permohonan-uji-klinik-parameter', 'uses' => 'LaboratoriumPermohonanUjiKlinikParameterManagement@getDataParameterPaket']);
    Route::post('elits-permohonan-uji-klinik/get-harga-total-permohonan-uji-klinik-parameter', ['as' => 'elits-permohonan-uji-klinik.get-harga-total-permohonan-uji-klinik-parameter', 'uses' => 'LaboratoriumPermohonanUjiKlinikParameterManagement@getCountHargaTotal']);
    Route::post('elits-permohonan-uji-klinik-2/get-harga-total-permohonan-uji-klinik-parameter', ['as' => 'elits-permohonan-uji-klinik-2.get-harga-total-permohonan-uji-klinik-parameter', 'uses' => 'LaboratoriumPermohonanUjiKlinikParameterManagement2@getCountHargaTotal']);

    // UNTUK ANALIS PERMOHONAN UJI KLINIK
    Route::get('elits-permohonan-uji-klinik/create-permohonan-uji-analis/{id_puk}', ['as' => 'elits-permohonan-uji-klinik.create-permohonan-uji-analis', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement@createPermohonanUjiAnalis']);
    Route::put('elits-permohonan-uji-klinik/store-permohonan-uji-analis/{id_puk}', ['as' => 'elits-permohonan-uji-klinik.store-permohonan-uji-analis', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement@storePermohonanUjiAnalis']);

    // UNTUK DOKTER PERMOHONAN UJI KLINIK
    Route::get('elits-permohonan-uji-klinik/create-permohonan-uji-rekomendasi-dokter/{id}', ['as' => 'elits-permohonan-uji-klinik.create-permohonan-uji-rekomendasi-dokter', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement@createPermohonanUjiRekomendasiDokter']);
    Route::post('elits-permohonan-uji-klinik/store-permohonan-uji-rekomendasi-dokter/{id}', ['as' => 'elits-permohonan-uji-klinik.store-permohonan-uji-rekomendasi-dokter', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement@storePermohonanUjiRekomendasiDokter']);

    // URL PADA PERMOHONAN UJI KLINIK DARI MENGGUNAKAN RESOURCE SAMPAI CUSTOM ROUTES
    Route::get('data-permohonan-uji-klinik', ['as' => 'elits-permohonan-uji-klinik.data-permohonan-uji-klinik', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement@data_permohonan_uji_klinik']);
    Route::get('elits-permohonan-uji-klinik-destroy/{id}', 'LaboratoriumPermohonanUjiKlinikManagement@destroy');


    Route::resource('elits-permohonan-uji-klinik', 'LaboratoriumPermohonanUjiKlinikManagement');




    // PRINT FORMULIR PENDAFTARAN
    Route::get('print-permohonan-uji-klinik-formulir/{id}', ['as' => 'elits-permohonan-uji-klinik.print-permohonan-uji-klinik-formulir', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement@printPermohonanUjiKlinikFormulir']);

    // PRINT FORMULIR PENDAFTARAN2
    Route::get('print-permohonan-uji-klinik-formulir-2/{id}', ['as' => 'elits-permohonan-uji-klinik-2.print-permohonan-uji-klinik-formulir', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement2@printPermohonanUjiKlinikFormulir']);

    // PRINT KARTU MEDIS
    Route::get('print-permohonan-uji-klinik-kartu-medis/{id}', ['as' => 'elits-permohonan-uji-klinik.print-permohonan-uji-klinik-kartu-medis', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement@printPermohonanUjiKlinikKartuMedis']);

    // PRINT OUT PERMOHONAN UJI KLINIK (NOTA)
    Route::get('print-permohonan-uji-klinik-nota/{id}', ['as' => 'elits-permohonan-uji-klinik.print-permohonan-uji-klinik-nota', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement@printPermohonanUjiKlinikNota']);

    // PELUNASAN PEMBAYARAN PERMOHONAN UJI KLINIK
    Route::post('permohonan-uji-klinik-get-payment', ['as' => 'permohonan-uji-klinik-get-payment', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement@getDataPermohonanUjiKlinikPayment']);
    Route::post('permohonan-uji-klinik-store-payment', ['as' => 'permohonan-uji-klinik-store-payment', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement@storeDataPermohonanUjiKlinikPayment']);


    Route::post('permohonan-uji-klinik-get-payment2', ['as' => 'permohonan-uji-klinik-get-payment2', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement2@getDataPermohonanUjiKlinikPayment']);
    Route::post('permohonan-uji-klinik-store-payment2', ['as' => 'permohonan-uji-klinik-store-payment2', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement2@storeDataPermohonanUjiKlinikPayment']);


    // PRINT OUT PERMOHONAN UJI KLINIK (HASIL KLINIK)
    Route::get('print-permohonan-uji-klinik-hasil/{id}', ['as' => 'elits-permohonan-uji-klinik.print-permohonan-uji-klinik-hasil', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement@printPermohonanUjiKlinikHasil']);

    // PRINT OUT PERMOHONAN UJI KLINIK (HASIL RAPID ANTIBODY)
    Route::get('print-permohonan-uji-klinik-hasil-rapid-antibody/{id}', ['as' => 'elits-permohonan-uji-klinik.print-permohonan-uji-klinik-hasil-rapid-antibody', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement@printPermohonanUjiKlinikHasilRapidAntibody']);

    // PRINT OUT PERMOHONAN UJI KLINIK (HASIL RAPID ANTIGEN)
    Route::get('print-permohonan-uji-klinik-hasil-rapid-antigen/{id}', ['as' => 'elits-permohonan-uji-klinik.print-permohonan-uji-klinik-hasil-rapid-antigen', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement@printPermohonanUjiKlinikHasilRapidAntigen']);

    // PRINT OUT PERMOHONAN UJI KLINIK (HASIL PCR)
    Route::get('print-permohonan-uji-klinik-hasil-pcr/{id}', ['as' => 'elits-permohonan-uji-klinik.print-permohonan-uji-klinik-hasil-pcr', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement@printPermohonanUjiKlinikHasilPcr']);

    // PRINT OUT PERMOHONAN UJI KLINIK (QRCODE)
    Route::get('print-permohonan-uji-klinik-qrcode/{id}', ['as' => 'elits-permohonan-uji-klinik.print-permohonan-uji-klinik-qrcode', 'uses' => 'LaboratoriumPermohonanUjiKlinikManagement@printPermohonanUjiKlinikQrcode']);

    // PRINT LABEL PERMOHONAN UJI KLINIK
    Route::get('elits-label-permohonan-uji-klinik', 'LaboratoriumPermohonanUjiKlinikManagement@label');
    Route::get('elits-label-permohonan-uji-klinik/print', 'LaboratoriumPermohonanUjiKlinikManagement@printLabel')->name('elits-permohonan-uji-klinik.print-label');


    //Pencahayaan Smapling
    Route::get('elits-pencahayaan/{id}', ['as' => 'elits-pencahayaan', 'uses' => 'LaboratoriumPencahayaanManagement@index']);
    Route::post('elits-pencahayaan/save/{id}', ['as' => 'elits-pencahayaan.save', 'uses' => 'LaboratoriumPencahayaanManagement@save']);
    Route::post('elits-pencahayaan/start/{id}', ['as' => 'elits-pencahayaan.start', 'uses' => 'LaboratoriumPencahayaanManagement@start']);


    Route::get('elits-pengetikan-hasil/{id}/{idlabs}', ['as' => 'elits-pengetikan-hasil.index', 'uses' => 'LaboratoriumPengetikanHasilManagement@create']);
    Route::post('elits-pengetikan-hasil/{id}/{idlabs}/', ['as' => 'elits-pengetikan-hasil.store', 'uses' => 'LaboratoriumPengetikanHasilManagement@store']);

    Route::get('elits-verifikasi-hasil/{id}/{idlabs}', ['as' => 'elits-verifikasi-hasil.index', 'uses' => 'LaboratoriumVerifikasiHasilManagement@create']);
    Route::post('elits-verifikasi-hasil/{id}/{idlabs}/', ['as' => 'elits-verifikasi-hasil.store', 'uses' => 'LaboratoriumVerifikasiHasilManagement@store']);

    Route::get('elits-pengesahan-hasil/{id}/{idlabs}', ['as' => 'elits-pengesahan-hasil.index', 'uses' => 'LaboratoriumPengesahanHasilManagement@create']);
    Route::post('elits-pengesahan-hasil/{id}/{idlabs}/', ['as' => 'elits-pengesahan-hasil.store', 'uses' => 'LaboratoriumPengesahanHasilManagement@store']);


    Route::resource('elits-rates', 'LaboratoriumTarifManagement');
    Route::resource('elits-input-laboratorium', 'LaboratoriumInputLaboratoriumManagement');

    Route::post('elits-input-laboratorium/get-laboratorium', 'LaboratoriumInputLaboratoriumManagement@getLaboratorium')
      ->name('get-laboratorium');
    Route::post('elits-input-laboratorium/get-laboratorium-non-klinik', 'LaboratoriumInputLaboratoriumManagement@getLaboratoriumNonKlinik')
      ->name('get-laboratorium-non-klinik');

    Route::resource('elits-majors', 'LaboratoriumMajorManagement');
    Route::get('elits-sampletypes', 'LaboratoriumSampleTypeManagement@index')->name('elits-sampletypes.index');
    Route::resource('elits-sampletypes', 'LaboratoriumSampleTypeManagement');


    Route::get('elits-sampletypes-destroy/{id}', 'LaboratoriumSampleTypeManagement@destroy')->name('elits-sampletypes-destroy');
    Route::get('elits-sampletypes/getdetail_sample_type/{id}', ['as' => 'elits-sampletypes.getdetail_sample_type', 'uses' => 'LaboratoriumSampleTypeManagement@getdetail_sample_type']);
    Route::get('elits-sampletypes/getbaku_mutu/{id}', ['as' => 'elits-sampletypes.getbaku_mutu', 'uses' => 'LaboratoriumSampleTypeManagement@getbaku_mutu']);



    // END ROUTE CUSTOM

    Route::get('elits-baku-mutu-klinik/data-baku-mutu-klinik', ['as' => 'elits-baku-mutu-klinik.data-baku-mutu-klinik', 'uses' => 'LaboratoriumBakuMutuKlinikManagement@data_baku_mutu_klinik']);
    Route::get('elits-baku-mutu-klinik-destroy/{id}', 'LaboratoriumBakuMutuKlinikManagement@destroy');
    Route::post('elits-baku-mutu-klinik/getBakuMutuKlinik', 'LaboratoriumBakuMutuKlinikManagement@getBakuMutuKlinik')->name('getBakuMutuKlinik');
    Route::post('elits-baku-mutu-klinik/checkBakuMutuParameterKlinik', 'LaboratoriumBakuMutuKlinikManagement@checkBakuMutuParameterKlinik')->name('checkBakuMutuParameterKlinik');
    Route::post('elits-baku-mutu-klinik/checkBakuMutuSubParameterSatuan', 'LaboratoriumBakuMutuKlinikManagement@checkBakuMutuSubParameterSatuan')->name('checkBakuMutuSubParameterSatuan');
    Route::resource('elits-baku-mutu-klinik', 'LaboratoriumBakuMutuKlinikManagement');
    //Module


    Route::get('elits-analys/klinik', ['as' => 'elits-analys.klinik', 'uses' => 'LaboratoriumAnalysManagement@index_klinik']);

    Route::get('elits-analys/analys/{id}/{id_method?}', ['as' => 'elits-analys.analys', 'uses' => 'LaboratoriumAnalysManagement@analys']);

    Route::get('elits-analys/analys/{id}/{id_method?}', ['as' => 'elits-analys.analys', 'uses' => 'LaboratoriumAnalysManagement@analys']);

    // Route::get('elits-analys/getSamplePagination', ['as' => 'elits-analys.getSamplePagination', 'uses' => 'LaboratoriumAnalysManagement@getSamplePagination']);
    Route::get('elits-analys/getSamplePagination', ['as' => 'elits-analys.getSamplePagination', 'uses' => 'LaboratoriumAnalysManagement@getSamplePagination']);




    Route::post('elits-excel/formImports', ['as' => 'elits-excel.formImports', 'uses' => 'LaboratoriumExcelManagement@formImports']);

    Route::get('elits-excel/downloadFormImports/{id_method}', ['as' => 'elits-excel.downloadFormImports', 'uses' => 'LaboratoriumExcelManagement@downloadFormImports']);

    Route::resource('elits-excel', 'LaboratoriumExcelManagement');

    Route::resource('elits-analys', 'LaboratoriumAnalysManagement');

    Route::post('elits-libraries/getLibrary', 'LaboratoriumLibraryManagement@getLibrary')->name('getLibrary');
    Route::resource('elits-libraries', 'LaboratoriumLibraryManagement');

    Route::post('elits-units/getDataUnitBySelect', 'LaboratoriumUnitManagement@getDataUnitBySelect')->name('getDataUnitBySelect');
    Route::resource('elits-units', 'LaboratoriumUnitManagement');

    Route::resource('elits-products', 'LaboratoriumProductManagement');
    Route::resource('elits-users', 'LaboratoriumUserManagement');
    Route::get('elits-users/reset/{param}', 'LaboratoriumUserManagement@reset_password');
    Route::post('elits-users/get-users-by-select', 'LaboratoriumUserManagement@getUsersBySelect2')->name('get-users-by-select');
    Route::post('elits-users/get-dokter-by-select', 'LaboratoriumUserManagement@getDokterBySelect2')->name('get-dokter-by-select');

    Route::resource('elits-inventories', 'LaboratoriumInventoryManagement');


    //stockopname
    Route::resource('stock-opname', 'StockOpnameController');

    // route adm pasien
    Route::resource('elits-pasien', 'AdmPasienController');
    Route::get('elits-pasien-datatables', ['as' => 'elits-pasien-datatables', 'uses' => 'AdmPasienController@dataPasienDatatables']);
    Route::get('elits-pasien/publish/{id}', 'AdmPasienController@publish');
    Route::get('elits-pasien-destroy/{id}', 'AdmPasienController@destroy');
    Route::post('elits-pasien/get-pasien-by-select', 'AdmPasienController@getPasienBySelect')->name('get-pasien-by-select');
    Route::post('elits-pasien/get-pasien-by-id', 'AdmPasienController@getPasienByID')->name('get-pasien-by-id');

    // route rekam medis klinik
    Route::get('elits-rekam-medis', ['as' => 'elits-rekam-medis', 'uses' => 'LaboratoriumRekamMedisKlinikController@index']);
    Route::get('elits-rekam-medis-show/{id}', ['as' => 'elits-rekam-medis-show', 'uses' => 'LaboratoriumRekamMedisKlinikController@show']);
    Route::get('elits-rekam-medis-destroy/{id}', 'LaboratoriumRekamMedisKlinikController@destroy')->name('elits-rekam-medis-destroy');

    // route rekam medis klinik detail
    Route::get('elits-rekam-medis-detail-hasil/{id}', ['as' => 'elits-rekam-medis-detail-hasil', 'uses' => 'LaboratoriumRekamMedisKlinikController@show_detail_hasil']);
    /* Route::get('elits-rekam-medis-detail-show/{id}', ['as' => 'elits-rekam-medis-show', 'uses' => 'LaboratoriumRekamMedisKlinikController@show']); */
    Route::get('elits-rekam-medis-detail-destroy/{id}', 'LaboratoriumRekamMedisKlinikController@destroy_detail')->name('elits-rekam-medis-detail-destroy');

    Route::get('elits-rekam-medis-detail-destroy/{id}', 'LaboratoriumRekamMedisKlinikController@destroy_detail')->name('elits-rekam-medis-detail-destroy');

    Route::get('elits-pendapatan-klinik', ['as' => 'elits-pendapatan-klinik', 'uses' => 'LaboratoriumPendapatanKlinikController@index']);
    Route::post('elits-pendapatan-klinik-count', ['as' => 'elits-pendapatan-klinik-count', 'uses' => 'LaboratoriumPendapatanKlinikController@getCountTotalPendapatan']);
    Route::get('elits-pendapatan-klinik-set-print', ['as' => 'elits-pendapatan-klinik-set-print', 'uses' => 'LaboratoriumPendapatanKlinikController@setPrintDataPeriodikKlinik']);

    /* Proses awal login akan mengarahkan ke default auth controller sebelum dialihkan ke login controller */
    // Route::get('/', 'Auth\AuthController@showLoginForm');
    Route::get('/', 'Auth\LoginController@showLoginForm');
    Route::post('/', 'Auth\LoginController@login')->name('login');
    Route::post('logout', 'Auth\LoginController@logout')->name('logout');

    Route::get('elits-signature/progress/{id}/{isClinic}', ['as' => 'signature-progress', 'uses' => 'SignatureController@signatureProgress']);
    Route::get('elits-signature-dummy/progress/{id}/{isClinic}', ['as' => 'signature-progress', 'uses' => 'SignatureController@signatureProgressDummy']);

    Route::get('elits-signature/verify', ['as' => 'signature-verify', 'uses' => 'SignatureController@verifySignatureView']);
    Route::post('elits-signature/verify', ['as' => 'signature-verify-post', 'uses' => 'SignatureController@verifySignature']);

    Route::get('elits-petugas/add', ['as' => 'adm-petugas-add', 'uses' => 'AdmPetugasController@create']);
    Route::post('elits-petugas', ['as' => 'adm-petugas-store', 'uses' => 'AdmPetugasController@store']);
    Route::get('elits-petugas', ['as' => 'adm-petugas', 'uses' => 'AdmPetugasController@index']);
    Route::get('elits-petugas/{id}', ['as' => 'adm-petugas-edit', 'uses' => 'AdmPetugasController@edit']);
    Route::put('elits-petugas/{id}', ['as' => 'adm-petugas-update', 'uses' => 'AdmPetugasController@update']);
    Route::delete('elits-petugas/{id}', ['as' => 'adm-petugas-delete', 'uses' => 'AdmPetugasController@destroy']);
  });



  //proses contact us
  Route::post('create_contact', 'ProcessController@contact');

  //proses penawaran
  Route::post('create_offer', 'ProcessController@offer');

  //proses penawaran
  Route::post('register_process', 'ProcessController@register');

  //proses penawaran
  Route::get('cronjob/email', 'CronController@email');

  //dinamic
  // Home page

  Route::get('export/{id}/{mount}/{year}', ['as' => 'export.report', 'uses' => 'ExportController@report']);

  // Route::get('/', [
  //     'as'      => 'home',
  //     'uses'    => 'PageController@index'
  // ]);

  // Route::get('/', function () {
  //   return redirect('/login');
  // });

  // Route::get('/register', function () {
  //   return view('masterweb::register');
  // });

  // Catch all page controller (place at the very bottom)
  Route::get('{slug}', [
    'uses' => 'PageController@getPage'
  ])->where('slug', '([A-Za-z0-9\-\/]+)');

  Route::get('{slug}/view/{link}', [
    'uses' => 'PageController@getPage'
  ])->where('slug', '([A-Za-z0-9\-\/]+)');

  //if have category in page
  Route::get('{slug}/cat/{link}', [
    'uses' => 'PageController@getPage'
  ])->where('slug', '([A-Za-z0-9\-\/]+)');
});
