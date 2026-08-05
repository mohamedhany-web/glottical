<?php

return [
    /*
    |--------------------------------------------------------------------------
    | أكواد الدول والتحقق من أرقام الهواتف (عالمي + بحث في فورم التسجيل)
    |--------------------------------------------------------------------------
    */
    'countries' => array (
  0 => 
  array (
    'code' => 'SA',
    'dial_code' => '+966',
    'name_ar' => 'السعودية',
    'name_en' => 'Saudi Arabia',
    'validation' => 
    array (
      'regex' => '/^[15]\\d{8}$/',
    ),
    'placeholder' => '5xxxxxxxx',
    'example' => '501234567',
  ),
  1 => 
  array (
    'code' => 'EG',
    'dial_code' => '+20',
    'name_ar' => 'مصر',
    'name_en' => 'Egypt',
    'validation' => 
    array (
      'regex' => '/^1[0125]\\d{8}$/',
    ),
    'placeholder' => '1xxxxxxxxx',
    'example' => '1012345678',
  ),
  2 => 
  array (
    'code' => 'AE',
    'dial_code' => '+971',
    'name_ar' => 'الإمارات',
    'name_en' => 'UAE',
    'validation' => 
    array (
      'regex' => '/^5[0-9]\\d{7}$/',
    ),
    'placeholder' => '5xxxxxxxx',
    'example' => '501234567',
  ),
  3 => 
  array (
    'code' => 'KW',
    'dial_code' => '+965',
    'name_ar' => 'الكويت',
    'name_en' => 'Kuwait',
    'validation' => 
    array (
      'regex' => '/^[569]\\d{7}$/',
    ),
    'placeholder' => '5xxxxxxxx',
    'example' => '50123456',
  ),
  4 => 
  array (
    'code' => 'BH',
    'dial_code' => '+973',
    'name_ar' => 'البحرين',
    'name_en' => 'Bahrain',
    'validation' => 
    array (
      'regex' => '/^[36]\\d{7}$/',
    ),
    'placeholder' => '3xxxxxxxx',
    'example' => '36123456',
  ),
  5 => 
  array (
    'code' => 'QA',
    'dial_code' => '+974',
    'name_ar' => 'قطر',
    'name_en' => 'Qatar',
    'validation' => 
    array (
      'regex' => '/^[3-7]\\d{7}$/',
    ),
    'placeholder' => '3xxxxxxxx',
    'example' => '33123456',
  ),
  6 => 
  array (
    'code' => 'OM',
    'dial_code' => '+968',
    'name_ar' => 'عُمان',
    'name_en' => 'Oman',
    'validation' => 
    array (
      'regex' => '/^[79]\\d{7}$/',
    ),
    'placeholder' => '9xxxxxxxx',
    'example' => '91234567',
  ),
  7 => 
  array (
    'code' => 'JO',
    'dial_code' => '+962',
    'name_ar' => 'الأردن',
    'name_en' => 'Jordan',
    'validation' => 
    array (
      'regex' => '/^[789]\\d{7}$/',
    ),
    'placeholder' => '7xxxxxxxx',
    'example' => '791234567',
  ),
  8 => 
  array (
    'code' => 'IQ',
    'dial_code' => '+964',
    'name_ar' => 'العراق',
    'name_en' => 'Iraq',
    'validation' => 
    array (
      'regex' => '/^7[0-9]\\d{8}$/',
    ),
    'placeholder' => '7xxxxxxxxx',
    'example' => '7912345678',
  ),
  9 => 
  array (
    'code' => 'SY',
    'dial_code' => '+963',
    'name_ar' => 'سوريا',
    'name_en' => 'Syria',
    'validation' => 
    array (
      'regex' => '/^9\\d{8}$/',
    ),
    'placeholder' => '9xxxxxxxx',
    'example' => '912345678',
  ),
  10 => 
  array (
    'code' => 'LB',
    'dial_code' => '+961',
    'name_ar' => 'لبنان',
    'name_en' => 'Lebanon',
    'validation' => 
    array (
      'regex' => '/^[37]\\d{7}$/',
    ),
    'placeholder' => '3xxxxxxxx',
    'example' => '71123456',
  ),
  11 => 
  array (
    'code' => 'YE',
    'dial_code' => '+967',
    'name_ar' => 'اليمن',
    'name_en' => 'Yemen',
    'validation' => 
    array (
      'regex' => '/^7[0-9]\\d{7}$/',
    ),
    'placeholder' => '7xxxxxxxx',
    'example' => '712345678',
  ),
  12 => 
  array (
    'code' => 'PS',
    'dial_code' => '+970',
    'name_ar' => 'فلسطين',
    'name_en' => 'Palestine',
    'validation' => 
    array (
      'regex' => '/^5[0-9]\\d{7}$/',
    ),
    'placeholder' => '5xxxxxxxx',
    'example' => '591234567',
  ),
  13 => 
  array (
    'code' => 'SD',
    'dial_code' => '+249',
    'name_ar' => 'السودان',
    'name_en' => 'Sudan',
    'validation' => 
    array (
      'regex' => '/^9[0-9]\\d{7}$/',
    ),
    'placeholder' => '9xxxxxxxx',
    'example' => '912345678',
  ),
  14 => 
  array (
    'code' => 'MA',
    'dial_code' => '+212',
    'name_ar' => 'المغرب',
    'name_en' => 'Morocco',
    'validation' => 
    array (
      'regex' => '/^[5-7]\\d{8}$/',
    ),
    'placeholder' => '6xxxxxxxx',
    'example' => '612345678',
  ),
  15 => 
  array (
    'code' => 'DZ',
    'dial_code' => '+213',
    'name_ar' => 'الجزائر',
    'name_en' => 'Algeria',
    'validation' => 
    array (
      'regex' => '/^[5-7]\\d{8}$/',
    ),
    'placeholder' => '5xxxxxxxx',
    'example' => '551234567',
  ),
  16 => 
  array (
    'code' => 'TN',
    'dial_code' => '+216',
    'name_ar' => 'تونس',
    'name_en' => 'Tunisia',
    'validation' => 
    array (
      'regex' => '/^[2-9]\\d{7}$/',
    ),
    'placeholder' => '2xxxxxxxx',
    'example' => '20123456',
  ),
  17 => 
  array (
    'code' => 'LY',
    'dial_code' => '+218',
    'name_ar' => 'ليبيا',
    'name_en' => 'Libya',
    'validation' => 
    array (
      'regex' => '/^9[0-9]\\d{7}$/',
    ),
    'placeholder' => '9xxxxxxxx',
    'example' => '912345678',
  ),
  18 => 
  array (
    'code' => 'US',
    'dial_code' => '+1',
    'name_ar' => 'الولايات المتحدة',
    'name_en' => 'United States',
    'validation' => 
    array (
      'regex' => '/^\\d{10}$/',
    ),
    'placeholder' => '2015550123',
    'example' => '2015550123',
  ),
  19 => 
  array (
    'code' => 'GB',
    'dial_code' => '+44',
    'name_ar' => 'المملكة المتحدة',
    'name_en' => 'United Kingdom',
    'validation' => 
    array (
      'regex' => '/^7\\d{9}$/',
    ),
    'placeholder' => '7xxxxxxxxx',
    'example' => '7123456789',
  ),
  20 => 
  array (
    'code' => 'CA',
    'dial_code' => '+1',
    'name_ar' => 'كندا',
    'name_en' => 'Canada',
    'validation' => 
    array (
      'regex' => '/^\\d{10}$/',
    ),
    'placeholder' => '4165550123',
    'example' => '4165550123',
  ),
  21 => 
  array (
    'code' => 'TR',
    'dial_code' => '+90',
    'name_ar' => 'تركيا',
    'name_en' => 'Turkey',
    'validation' => 
    array (
      'regex' => '/^5\\d{9}$/',
    ),
    'placeholder' => '5xxxxxxxxx',
    'example' => '5012345678',
  ),
  22 => 
  array (
    'code' => 'DE',
    'dial_code' => '+49',
    'name_ar' => 'ألمانيا',
    'name_en' => 'Germany',
    'validation' => 
    array (
      'regex' => '/^\\d{6,13}$/',
    ),
    'placeholder' => '15123456789',
    'example' => '15123456789',
  ),
  23 => 
  array (
    'code' => 'FR',
    'dial_code' => '+33',
    'name_ar' => 'فرنسا',
    'name_en' => 'France',
    'validation' => 
    array (
      'regex' => '/^[67]\\d{8}$/',
    ),
    'placeholder' => '6xxxxxxxx',
    'example' => '612345678',
  ),
  24 => 
  array (
    'code' => 'MY',
    'dial_code' => '+60',
    'name_ar' => 'ماليزيا',
    'name_en' => 'Malaysia',
    'validation' => 
    array (
      'regex' => '/^1\\d{8,9}$/',
    ),
    'placeholder' => '1xxxxxxxx',
    'example' => '123456789',
  ),
  25 => 
  array (
    'code' => 'ID',
    'dial_code' => '+62',
    'name_ar' => 'إندونيسيا',
    'name_en' => 'Indonesia',
    'validation' => 
    array (
      'regex' => '/^8\\d{8,11}$/',
    ),
    'placeholder' => '8xxxxxxxxx',
    'example' => '81234567890',
  ),
  26 => 
  array (
    'code' => 'PK',
    'dial_code' => '+92',
    'name_ar' => 'باكستان',
    'name_en' => 'Pakistan',
    'validation' => 
    array (
      'regex' => '/^3\\d{9}$/',
    ),
    'placeholder' => '3xxxxxxxxx',
    'example' => '3012345678',
  ),
  27 => 
  array (
    'code' => 'IN',
    'dial_code' => '+91',
    'name_ar' => 'الهند',
    'name_en' => 'India',
    'validation' => 
    array (
      'regex' => '/^[6-9]\\d{9}$/',
    ),
    'placeholder' => '9xxxxxxxxx',
    'example' => '9123456789',
  ),
  28 => 
  array (
    'code' => 'BD',
    'dial_code' => '+880',
    'name_ar' => 'بنغلاديش',
    'name_en' => 'Bangladesh',
    'validation' => 
    array (
      'regex' => '/^1\\d{9}$/',
    ),
    'placeholder' => '1xxxxxxxxx',
    'example' => '1712345678',
  ),
  29 => 
  array (
    'code' => 'NG',
    'dial_code' => '+234',
    'name_ar' => 'نيجيريا',
    'name_en' => 'Nigeria',
    'validation' => 
    array (
      'regex' => '/^[789]\\d{9}$/',
    ),
    'placeholder' => '8012345678',
    'example' => '8012345678',
  ),
  30 => 
  array (
    'code' => 'ZA',
    'dial_code' => '+27',
    'name_ar' => 'جنوب أفريقيا',
    'name_en' => 'South Africa',
    'validation' => 
    array (
      'regex' => '/^[6-8]\\d{8}$/',
    ),
    'placeholder' => '8xxxxxxxx',
    'example' => '821234567',
  ),
  31 => 
  array (
    'code' => 'AU',
    'dial_code' => '+61',
    'name_ar' => 'أستراليا',
    'name_en' => 'Australia',
    'validation' => 
    array (
      'regex' => '/^4\\d{8}$/',
    ),
    'placeholder' => '4xxxxxxxx',
    'example' => '412345678',
  ),
  32 => 
  array (
    'code' => 'AF',
    'dial_code' => '+93',
    'name_ar' => 'أفغانستان',
    'name_en' => 'Afghanistan',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  33 => 
  array (
    'code' => 'AL',
    'dial_code' => '+355',
    'name_ar' => 'ألبانيا',
    'name_en' => 'Albania',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  34 => 
  array (
    'code' => 'AS',
    'dial_code' => '+1684',
    'name_ar' => 'ساموا الأمريكية',
    'name_en' => 'American Samoa',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  35 => 
  array (
    'code' => 'AD',
    'dial_code' => '+376',
    'name_ar' => 'أندورا',
    'name_en' => 'Andorra',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  36 => 
  array (
    'code' => 'AO',
    'dial_code' => '+244',
    'name_ar' => 'أنغولا',
    'name_en' => 'Angola',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  37 => 
  array (
    'code' => 'AI',
    'dial_code' => '+1264',
    'name_ar' => 'أنغويلا',
    'name_en' => 'Anguilla',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  38 => 
  array (
    'code' => 'AG',
    'dial_code' => '+1268',
    'name_ar' => 'أنتيغوا وباربودا',
    'name_en' => 'Antigua and Barbuda',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  39 => 
  array (
    'code' => 'AR',
    'dial_code' => '+54',
    'name_ar' => 'الأرجنتين',
    'name_en' => 'Argentina',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  40 => 
  array (
    'code' => 'AM',
    'dial_code' => '+374',
    'name_ar' => 'أرمينيا',
    'name_en' => 'Armenia',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  41 => 
  array (
    'code' => 'AW',
    'dial_code' => '+297',
    'name_ar' => 'أروبا',
    'name_en' => 'Aruba',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  42 => 
  array (
    'code' => 'AT',
    'dial_code' => '+43',
    'name_ar' => 'النمسا',
    'name_en' => 'Austria',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  43 => 
  array (
    'code' => 'AZ',
    'dial_code' => '+994',
    'name_ar' => 'أذربيجان',
    'name_en' => 'Azerbaijan',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  44 => 
  array (
    'code' => 'BS',
    'dial_code' => '+1242',
    'name_ar' => 'البهاما',
    'name_en' => 'Bahamas',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  45 => 
  array (
    'code' => 'BB',
    'dial_code' => '+1246',
    'name_ar' => 'بربادوس',
    'name_en' => 'Barbados',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  46 => 
  array (
    'code' => 'BY',
    'dial_code' => '+375',
    'name_ar' => 'بيلاروس',
    'name_en' => 'Belarus',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  47 => 
  array (
    'code' => 'BE',
    'dial_code' => '+32',
    'name_ar' => 'بلجيكا',
    'name_en' => 'Belgium',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  48 => 
  array (
    'code' => 'BZ',
    'dial_code' => '+501',
    'name_ar' => 'بليز',
    'name_en' => 'Belize',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  49 => 
  array (
    'code' => 'BJ',
    'dial_code' => '+229',
    'name_ar' => 'بنين',
    'name_en' => 'Benin',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  50 => 
  array (
    'code' => 'BM',
    'dial_code' => '+1441',
    'name_ar' => 'برمودا',
    'name_en' => 'Bermuda',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  51 => 
  array (
    'code' => 'BT',
    'dial_code' => '+975',
    'name_ar' => 'بوتان',
    'name_en' => 'Bhutan',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  52 => 
  array (
    'code' => 'BO',
    'dial_code' => '+591',
    'name_ar' => 'بوليفيا',
    'name_en' => 'Bolivia',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  53 => 
  array (
    'code' => 'BA',
    'dial_code' => '+387',
    'name_ar' => 'البوسنة والهرسك',
    'name_en' => 'Bosnia and Herzegovina',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  54 => 
  array (
    'code' => 'BW',
    'dial_code' => '+267',
    'name_ar' => 'بوتسوانا',
    'name_en' => 'Botswana',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  55 => 
  array (
    'code' => 'BR',
    'dial_code' => '+55',
    'name_ar' => 'البرازيل',
    'name_en' => 'Brazil',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  56 => 
  array (
    'code' => 'IO',
    'dial_code' => '+246',
    'name_ar' => 'إقليم المحيط الهندي البريطاني',
    'name_en' => 'British Indian Ocean Territory',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  57 => 
  array (
    'code' => 'VG',
    'dial_code' => '+1284',
    'name_ar' => 'جزر العذراء البريطانية',
    'name_en' => 'British Virgin Islands',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  58 => 
  array (
    'code' => 'BN',
    'dial_code' => '+673',
    'name_ar' => 'بروناي',
    'name_en' => 'Brunei',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  59 => 
  array (
    'code' => 'BG',
    'dial_code' => '+359',
    'name_ar' => 'بلغاريا',
    'name_en' => 'Bulgaria',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  60 => 
  array (
    'code' => 'BF',
    'dial_code' => '+226',
    'name_ar' => 'بوركينا فاسو',
    'name_en' => 'Burkina Faso',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  61 => 
  array (
    'code' => 'BI',
    'dial_code' => '+257',
    'name_ar' => 'بوروندي',
    'name_en' => 'Burundi',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  62 => 
  array (
    'code' => 'KH',
    'dial_code' => '+855',
    'name_ar' => 'كمبوديا',
    'name_en' => 'Cambodia',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  63 => 
  array (
    'code' => 'CM',
    'dial_code' => '+237',
    'name_ar' => 'الكاميرون',
    'name_en' => 'Cameroon',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  64 => 
  array (
    'code' => 'CV',
    'dial_code' => '+238',
    'name_ar' => 'الرأس الأخضر',
    'name_en' => 'Cape Verde',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  65 => 
  array (
    'code' => 'KY',
    'dial_code' => '+1345',
    'name_ar' => 'جزر كايمان',
    'name_en' => 'Cayman Islands',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  66 => 
  array (
    'code' => 'CF',
    'dial_code' => '+236',
    'name_ar' => 'جمهورية أفريقيا الوسطى',
    'name_en' => 'Central African Republic',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  67 => 
  array (
    'code' => 'TD',
    'dial_code' => '+235',
    'name_ar' => 'تشاد',
    'name_en' => 'Chad',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  68 => 
  array (
    'code' => 'CL',
    'dial_code' => '+56',
    'name_ar' => 'تشيلي',
    'name_en' => 'Chile',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  69 => 
  array (
    'code' => 'CN',
    'dial_code' => '+86',
    'name_ar' => 'الصين',
    'name_en' => 'China',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  70 => 
  array (
    'code' => 'CX',
    'dial_code' => '+61',
    'name_ar' => 'جزيرة كريسماس',
    'name_en' => 'Christmas Island',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  71 => 
  array (
    'code' => 'CC',
    'dial_code' => '+61',
    'name_ar' => 'جزر كوكوس',
    'name_en' => 'Cocos Islands',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  72 => 
  array (
    'code' => 'CO',
    'dial_code' => '+57',
    'name_ar' => 'كولومبيا',
    'name_en' => 'Colombia',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  73 => 
  array (
    'code' => 'KM',
    'dial_code' => '+269',
    'name_ar' => 'جزر القمر',
    'name_en' => 'Comoros',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  74 => 
  array (
    'code' => 'CG',
    'dial_code' => '+242',
    'name_ar' => 'الكونغو',
    'name_en' => 'Congo',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  75 => 
  array (
    'code' => 'CD',
    'dial_code' => '+243',
    'name_ar' => 'الكونغو الديمقراطية',
    'name_en' => 'Congo (DRC)',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  76 => 
  array (
    'code' => 'CK',
    'dial_code' => '+682',
    'name_ar' => 'جزر كوك',
    'name_en' => 'Cook Islands',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  77 => 
  array (
    'code' => 'CR',
    'dial_code' => '+506',
    'name_ar' => 'كوستاريكا',
    'name_en' => 'Costa Rica',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  78 => 
  array (
    'code' => 'HR',
    'dial_code' => '+385',
    'name_ar' => 'كرواتيا',
    'name_en' => 'Croatia',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  79 => 
  array (
    'code' => 'CU',
    'dial_code' => '+53',
    'name_ar' => 'كوبا',
    'name_en' => 'Cuba',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  80 => 
  array (
    'code' => 'CW',
    'dial_code' => '+599',
    'name_ar' => 'كوراساو',
    'name_en' => 'Curaçao',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  81 => 
  array (
    'code' => 'CY',
    'dial_code' => '+357',
    'name_ar' => 'قبرص',
    'name_en' => 'Cyprus',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  82 => 
  array (
    'code' => 'CZ',
    'dial_code' => '+420',
    'name_ar' => 'التشيك',
    'name_en' => 'Czech Republic',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  83 => 
  array (
    'code' => 'CI',
    'dial_code' => '+225',
    'name_ar' => 'ساحل العاج',
    'name_en' => 'Côte d\'Ivoire',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  84 => 
  array (
    'code' => 'DK',
    'dial_code' => '+45',
    'name_ar' => 'الدنمارك',
    'name_en' => 'Denmark',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  85 => 
  array (
    'code' => 'DJ',
    'dial_code' => '+253',
    'name_ar' => 'جيبوتي',
    'name_en' => 'Djibouti',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  86 => 
  array (
    'code' => 'DM',
    'dial_code' => '+1767',
    'name_ar' => 'دومينيكا',
    'name_en' => 'Dominica',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  87 => 
  array (
    'code' => 'DO',
    'dial_code' => '+1809',
    'name_ar' => 'جمهورية الدومينيكان',
    'name_en' => 'Dominican Republic',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  88 => 
  array (
    'code' => 'EC',
    'dial_code' => '+593',
    'name_ar' => 'الإكوادور',
    'name_en' => 'Ecuador',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  89 => 
  array (
    'code' => 'SV',
    'dial_code' => '+503',
    'name_ar' => 'السلفادور',
    'name_en' => 'El Salvador',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  90 => 
  array (
    'code' => 'GQ',
    'dial_code' => '+240',
    'name_ar' => 'غينيا الاستوائية',
    'name_en' => 'Equatorial Guinea',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  91 => 
  array (
    'code' => 'ER',
    'dial_code' => '+291',
    'name_ar' => 'إريتريا',
    'name_en' => 'Eritrea',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  92 => 
  array (
    'code' => 'EE',
    'dial_code' => '+372',
    'name_ar' => 'إستونيا',
    'name_en' => 'Estonia',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  93 => 
  array (
    'code' => 'SZ',
    'dial_code' => '+268',
    'name_ar' => 'إسواتيني',
    'name_en' => 'Eswatini',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  94 => 
  array (
    'code' => 'ET',
    'dial_code' => '+251',
    'name_ar' => 'إثيوبيا',
    'name_en' => 'Ethiopia',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  95 => 
  array (
    'code' => 'FK',
    'dial_code' => '+500',
    'name_ar' => 'جزر فوكلاند',
    'name_en' => 'Falkland Islands',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  96 => 
  array (
    'code' => 'FO',
    'dial_code' => '+298',
    'name_ar' => 'جزر فارو',
    'name_en' => 'Faroe Islands',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  97 => 
  array (
    'code' => 'FJ',
    'dial_code' => '+679',
    'name_ar' => 'فيجي',
    'name_en' => 'Fiji',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  98 => 
  array (
    'code' => 'FI',
    'dial_code' => '+358',
    'name_ar' => 'فنلندا',
    'name_en' => 'Finland',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  99 => 
  array (
    'code' => 'GF',
    'dial_code' => '+594',
    'name_ar' => 'غويانا الفرنسية',
    'name_en' => 'French Guiana',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  100 => 
  array (
    'code' => 'PF',
    'dial_code' => '+689',
    'name_ar' => 'بولينيزيا الفرنسية',
    'name_en' => 'French Polynesia',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  101 => 
  array (
    'code' => 'GA',
    'dial_code' => '+241',
    'name_ar' => 'الغابون',
    'name_en' => 'Gabon',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  102 => 
  array (
    'code' => 'GM',
    'dial_code' => '+220',
    'name_ar' => 'غامبيا',
    'name_en' => 'Gambia',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  103 => 
  array (
    'code' => 'GE',
    'dial_code' => '+995',
    'name_ar' => 'جورجيا',
    'name_en' => 'Georgia',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  104 => 
  array (
    'code' => 'GH',
    'dial_code' => '+233',
    'name_ar' => 'غانا',
    'name_en' => 'Ghana',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  105 => 
  array (
    'code' => 'GI',
    'dial_code' => '+350',
    'name_ar' => 'جبل طارق',
    'name_en' => 'Gibraltar',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  106 => 
  array (
    'code' => 'GR',
    'dial_code' => '+30',
    'name_ar' => 'اليونان',
    'name_en' => 'Greece',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  107 => 
  array (
    'code' => 'GL',
    'dial_code' => '+299',
    'name_ar' => 'غرينلاند',
    'name_en' => 'Greenland',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  108 => 
  array (
    'code' => 'GD',
    'dial_code' => '+1473',
    'name_ar' => 'غرينادا',
    'name_en' => 'Grenada',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  109 => 
  array (
    'code' => 'GP',
    'dial_code' => '+590',
    'name_ar' => 'غوادلوب',
    'name_en' => 'Guadeloupe',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  110 => 
  array (
    'code' => 'GU',
    'dial_code' => '+1671',
    'name_ar' => 'غوام',
    'name_en' => 'Guam',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  111 => 
  array (
    'code' => 'GT',
    'dial_code' => '+502',
    'name_ar' => 'غواتيمالا',
    'name_en' => 'Guatemala',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  112 => 
  array (
    'code' => 'GG',
    'dial_code' => '+44',
    'name_ar' => 'غيرنزي',
    'name_en' => 'Guernsey',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  113 => 
  array (
    'code' => 'GN',
    'dial_code' => '+224',
    'name_ar' => 'غينيا',
    'name_en' => 'Guinea',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  114 => 
  array (
    'code' => 'GW',
    'dial_code' => '+245',
    'name_ar' => 'غينيا بيساو',
    'name_en' => 'Guinea-Bissau',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  115 => 
  array (
    'code' => 'GY',
    'dial_code' => '+592',
    'name_ar' => 'غيانا',
    'name_en' => 'Guyana',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  116 => 
  array (
    'code' => 'HT',
    'dial_code' => '+509',
    'name_ar' => 'هايتي',
    'name_en' => 'Haiti',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  117 => 
  array (
    'code' => 'HN',
    'dial_code' => '+504',
    'name_ar' => 'هندوراس',
    'name_en' => 'Honduras',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  118 => 
  array (
    'code' => 'HK',
    'dial_code' => '+852',
    'name_ar' => 'هونغ كونغ',
    'name_en' => 'Hong Kong',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  119 => 
  array (
    'code' => 'HU',
    'dial_code' => '+36',
    'name_ar' => 'المجر',
    'name_en' => 'Hungary',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  120 => 
  array (
    'code' => 'IS',
    'dial_code' => '+354',
    'name_ar' => 'آيسلندا',
    'name_en' => 'Iceland',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  121 => 
  array (
    'code' => 'IR',
    'dial_code' => '+98',
    'name_ar' => 'إيران',
    'name_en' => 'Iran',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  122 => 
  array (
    'code' => 'IE',
    'dial_code' => '+353',
    'name_ar' => 'أيرلندا',
    'name_en' => 'Ireland',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  123 => 
  array (
    'code' => 'IM',
    'dial_code' => '+44',
    'name_ar' => 'جزيرة مان',
    'name_en' => 'Isle of Man',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  124 => 
  array (
    'code' => 'IL',
    'dial_code' => '+972',
    'name_ar' => 'إسرائيل',
    'name_en' => 'Israel',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  125 => 
  array (
    'code' => 'IT',
    'dial_code' => '+39',
    'name_ar' => 'إيطاليا',
    'name_en' => 'Italy',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  126 => 
  array (
    'code' => 'JM',
    'dial_code' => '+1876',
    'name_ar' => 'جامايكا',
    'name_en' => 'Jamaica',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  127 => 
  array (
    'code' => 'JP',
    'dial_code' => '+81',
    'name_ar' => 'اليابان',
    'name_en' => 'Japan',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  128 => 
  array (
    'code' => 'JE',
    'dial_code' => '+44',
    'name_ar' => 'جيرسي',
    'name_en' => 'Jersey',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  129 => 
  array (
    'code' => 'KZ',
    'dial_code' => '+7',
    'name_ar' => 'كازاخستان',
    'name_en' => 'Kazakhstan',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  130 => 
  array (
    'code' => 'KE',
    'dial_code' => '+254',
    'name_ar' => 'كينيا',
    'name_en' => 'Kenya',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  131 => 
  array (
    'code' => 'KI',
    'dial_code' => '+686',
    'name_ar' => 'كيريباتي',
    'name_en' => 'Kiribati',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  132 => 
  array (
    'code' => 'XK',
    'dial_code' => '+383',
    'name_ar' => 'كوسوفو',
    'name_en' => 'Kosovo',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  133 => 
  array (
    'code' => 'KG',
    'dial_code' => '+996',
    'name_ar' => 'قيرغيزستان',
    'name_en' => 'Kyrgyzstan',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  134 => 
  array (
    'code' => 'LA',
    'dial_code' => '+856',
    'name_ar' => 'لاوس',
    'name_en' => 'Laos',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  135 => 
  array (
    'code' => 'LV',
    'dial_code' => '+371',
    'name_ar' => 'لاتفيا',
    'name_en' => 'Latvia',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  136 => 
  array (
    'code' => 'LS',
    'dial_code' => '+266',
    'name_ar' => 'ليسوتو',
    'name_en' => 'Lesotho',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  137 => 
  array (
    'code' => 'LR',
    'dial_code' => '+231',
    'name_ar' => 'ليبيريا',
    'name_en' => 'Liberia',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  138 => 
  array (
    'code' => 'LI',
    'dial_code' => '+423',
    'name_ar' => 'ليختنشتاين',
    'name_en' => 'Liechtenstein',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  139 => 
  array (
    'code' => 'LT',
    'dial_code' => '+370',
    'name_ar' => 'ليتوانيا',
    'name_en' => 'Lithuania',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  140 => 
  array (
    'code' => 'LU',
    'dial_code' => '+352',
    'name_ar' => 'لوكسمبورغ',
    'name_en' => 'Luxembourg',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  141 => 
  array (
    'code' => 'MO',
    'dial_code' => '+853',
    'name_ar' => 'ماكاو',
    'name_en' => 'Macau',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  142 => 
  array (
    'code' => 'MG',
    'dial_code' => '+261',
    'name_ar' => 'مدغشقر',
    'name_en' => 'Madagascar',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  143 => 
  array (
    'code' => 'MW',
    'dial_code' => '+265',
    'name_ar' => 'ملاوي',
    'name_en' => 'Malawi',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  144 => 
  array (
    'code' => 'MV',
    'dial_code' => '+960',
    'name_ar' => 'المالديف',
    'name_en' => 'Maldives',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  145 => 
  array (
    'code' => 'ML',
    'dial_code' => '+223',
    'name_ar' => 'مالي',
    'name_en' => 'Mali',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  146 => 
  array (
    'code' => 'MT',
    'dial_code' => '+356',
    'name_ar' => 'مالطا',
    'name_en' => 'Malta',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  147 => 
  array (
    'code' => 'MH',
    'dial_code' => '+692',
    'name_ar' => 'جزر مارشال',
    'name_en' => 'Marshall Islands',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  148 => 
  array (
    'code' => 'MQ',
    'dial_code' => '+596',
    'name_ar' => 'مارتينيك',
    'name_en' => 'Martinique',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  149 => 
  array (
    'code' => 'MR',
    'dial_code' => '+222',
    'name_ar' => 'موريتانيا',
    'name_en' => 'Mauritania',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  150 => 
  array (
    'code' => 'MU',
    'dial_code' => '+230',
    'name_ar' => 'موريشيوس',
    'name_en' => 'Mauritius',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  151 => 
  array (
    'code' => 'YT',
    'dial_code' => '+262',
    'name_ar' => 'مايوت',
    'name_en' => 'Mayotte',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  152 => 
  array (
    'code' => 'MX',
    'dial_code' => '+52',
    'name_ar' => 'المكسيك',
    'name_en' => 'Mexico',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  153 => 
  array (
    'code' => 'FM',
    'dial_code' => '+691',
    'name_ar' => 'ميكرونيزيا',
    'name_en' => 'Micronesia',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  154 => 
  array (
    'code' => 'MD',
    'dial_code' => '+373',
    'name_ar' => 'مولدوفا',
    'name_en' => 'Moldova',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  155 => 
  array (
    'code' => 'MC',
    'dial_code' => '+377',
    'name_ar' => 'موناكو',
    'name_en' => 'Monaco',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  156 => 
  array (
    'code' => 'MN',
    'dial_code' => '+976',
    'name_ar' => 'منغوليا',
    'name_en' => 'Mongolia',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  157 => 
  array (
    'code' => 'ME',
    'dial_code' => '+382',
    'name_ar' => 'الجبل الأسود',
    'name_en' => 'Montenegro',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  158 => 
  array (
    'code' => 'MS',
    'dial_code' => '+1664',
    'name_ar' => 'مونتسيرات',
    'name_en' => 'Montserrat',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  159 => 
  array (
    'code' => 'MZ',
    'dial_code' => '+258',
    'name_ar' => 'موزمبيق',
    'name_en' => 'Mozambique',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  160 => 
  array (
    'code' => 'MM',
    'dial_code' => '+95',
    'name_ar' => 'ميانمار',
    'name_en' => 'Myanmar',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  161 => 
  array (
    'code' => 'NA',
    'dial_code' => '+264',
    'name_ar' => 'ناميبيا',
    'name_en' => 'Namibia',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  162 => 
  array (
    'code' => 'NR',
    'dial_code' => '+674',
    'name_ar' => 'ناورو',
    'name_en' => 'Nauru',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  163 => 
  array (
    'code' => 'NP',
    'dial_code' => '+977',
    'name_ar' => 'نيبال',
    'name_en' => 'Nepal',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  164 => 
  array (
    'code' => 'NL',
    'dial_code' => '+31',
    'name_ar' => 'هولندا',
    'name_en' => 'Netherlands',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  165 => 
  array (
    'code' => 'NC',
    'dial_code' => '+687',
    'name_ar' => 'كاليدونيا الجديدة',
    'name_en' => 'New Caledonia',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  166 => 
  array (
    'code' => 'NZ',
    'dial_code' => '+64',
    'name_ar' => 'نيوزيلندا',
    'name_en' => 'New Zealand',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  167 => 
  array (
    'code' => 'NI',
    'dial_code' => '+505',
    'name_ar' => 'نيكاراغوا',
    'name_en' => 'Nicaragua',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  168 => 
  array (
    'code' => 'NE',
    'dial_code' => '+227',
    'name_ar' => 'النيجر',
    'name_en' => 'Niger',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  169 => 
  array (
    'code' => 'NU',
    'dial_code' => '+683',
    'name_ar' => 'نيوي',
    'name_en' => 'Niue',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  170 => 
  array (
    'code' => 'NF',
    'dial_code' => '+672',
    'name_ar' => 'جزيرة نورفولك',
    'name_en' => 'Norfolk Island',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  171 => 
  array (
    'code' => 'KP',
    'dial_code' => '+850',
    'name_ar' => 'كوريا الشمالية',
    'name_en' => 'North Korea',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  172 => 
  array (
    'code' => 'MK',
    'dial_code' => '+389',
    'name_ar' => 'مقدونيا الشمالية',
    'name_en' => 'North Macedonia',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  173 => 
  array (
    'code' => 'MP',
    'dial_code' => '+1670',
    'name_ar' => 'جزر ماريانا الشمالية',
    'name_en' => 'Northern Mariana Islands',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  174 => 
  array (
    'code' => 'NO',
    'dial_code' => '+47',
    'name_ar' => 'النرويج',
    'name_en' => 'Norway',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  175 => 
  array (
    'code' => 'PW',
    'dial_code' => '+680',
    'name_ar' => 'بالاو',
    'name_en' => 'Palau',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  176 => 
  array (
    'code' => 'PA',
    'dial_code' => '+507',
    'name_ar' => 'بنما',
    'name_en' => 'Panama',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  177 => 
  array (
    'code' => 'PG',
    'dial_code' => '+675',
    'name_ar' => 'بابوا غينيا الجديدة',
    'name_en' => 'Papua New Guinea',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  178 => 
  array (
    'code' => 'PY',
    'dial_code' => '+595',
    'name_ar' => 'باراغواي',
    'name_en' => 'Paraguay',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  179 => 
  array (
    'code' => 'PE',
    'dial_code' => '+51',
    'name_ar' => 'بيرو',
    'name_en' => 'Peru',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  180 => 
  array (
    'code' => 'PH',
    'dial_code' => '+63',
    'name_ar' => 'الفلبين',
    'name_en' => 'Philippines',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  181 => 
  array (
    'code' => 'PL',
    'dial_code' => '+48',
    'name_ar' => 'بولندا',
    'name_en' => 'Poland',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  182 => 
  array (
    'code' => 'PT',
    'dial_code' => '+351',
    'name_ar' => 'البرتغال',
    'name_en' => 'Portugal',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  183 => 
  array (
    'code' => 'PR',
    'dial_code' => '+1787',
    'name_ar' => 'بورتوريكو',
    'name_en' => 'Puerto Rico',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  184 => 
  array (
    'code' => 'RO',
    'dial_code' => '+40',
    'name_ar' => 'رومانيا',
    'name_en' => 'Romania',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  185 => 
  array (
    'code' => 'RU',
    'dial_code' => '+7',
    'name_ar' => 'روسيا',
    'name_en' => 'Russia',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  186 => 
  array (
    'code' => 'RW',
    'dial_code' => '+250',
    'name_ar' => 'رواندا',
    'name_en' => 'Rwanda',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  187 => 
  array (
    'code' => 'RE',
    'dial_code' => '+262',
    'name_ar' => 'ريونيون',
    'name_en' => 'Réunion',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  188 => 
  array (
    'code' => 'BL',
    'dial_code' => '+590',
    'name_ar' => 'سان بارتليمي',
    'name_en' => 'Saint Barthélemy',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  189 => 
  array (
    'code' => 'SH',
    'dial_code' => '+290',
    'name_ar' => 'سانت هيلينا',
    'name_en' => 'Saint Helena',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  190 => 
  array (
    'code' => 'KN',
    'dial_code' => '+1869',
    'name_ar' => 'سانت كيتس ونيفيس',
    'name_en' => 'Saint Kitts and Nevis',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  191 => 
  array (
    'code' => 'LC',
    'dial_code' => '+1758',
    'name_ar' => 'سانت لوسيا',
    'name_en' => 'Saint Lucia',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  192 => 
  array (
    'code' => 'MF',
    'dial_code' => '+590',
    'name_ar' => 'سانت مارتن',
    'name_en' => 'Saint Martin',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  193 => 
  array (
    'code' => 'PM',
    'dial_code' => '+508',
    'name_ar' => 'سان بيير وميكلون',
    'name_en' => 'Saint Pierre and Miquelon',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  194 => 
  array (
    'code' => 'VC',
    'dial_code' => '+1784',
    'name_ar' => 'سانت فنسنت',
    'name_en' => 'Saint Vincent and the Grenadines',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  195 => 
  array (
    'code' => 'WS',
    'dial_code' => '+685',
    'name_ar' => 'ساموا',
    'name_en' => 'Samoa',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  196 => 
  array (
    'code' => 'SM',
    'dial_code' => '+378',
    'name_ar' => 'سان مارينو',
    'name_en' => 'San Marino',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  197 => 
  array (
    'code' => 'ST',
    'dial_code' => '+239',
    'name_ar' => 'ساو تومي وبرينسيبي',
    'name_en' => 'Sao Tome and Principe',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  198 => 
  array (
    'code' => 'SN',
    'dial_code' => '+221',
    'name_ar' => 'السنغال',
    'name_en' => 'Senegal',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  199 => 
  array (
    'code' => 'RS',
    'dial_code' => '+381',
    'name_ar' => 'صربيا',
    'name_en' => 'Serbia',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  200 => 
  array (
    'code' => 'SC',
    'dial_code' => '+248',
    'name_ar' => 'سيشل',
    'name_en' => 'Seychelles',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  201 => 
  array (
    'code' => 'SL',
    'dial_code' => '+232',
    'name_ar' => 'سيراليون',
    'name_en' => 'Sierra Leone',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  202 => 
  array (
    'code' => 'SG',
    'dial_code' => '+65',
    'name_ar' => 'سنغافورة',
    'name_en' => 'Singapore',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  203 => 
  array (
    'code' => 'SX',
    'dial_code' => '+1721',
    'name_ar' => 'سينت مارتن',
    'name_en' => 'Sint Maarten',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  204 => 
  array (
    'code' => 'SK',
    'dial_code' => '+421',
    'name_ar' => 'سلوفاكيا',
    'name_en' => 'Slovakia',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  205 => 
  array (
    'code' => 'SI',
    'dial_code' => '+386',
    'name_ar' => 'سلوفينيا',
    'name_en' => 'Slovenia',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  206 => 
  array (
    'code' => 'SB',
    'dial_code' => '+677',
    'name_ar' => 'جزر سليمان',
    'name_en' => 'Solomon Islands',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  207 => 
  array (
    'code' => 'SO',
    'dial_code' => '+252',
    'name_ar' => 'الصومال',
    'name_en' => 'Somalia',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  208 => 
  array (
    'code' => 'KR',
    'dial_code' => '+82',
    'name_ar' => 'كوريا الجنوبية',
    'name_en' => 'South Korea',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  209 => 
  array (
    'code' => 'SS',
    'dial_code' => '+211',
    'name_ar' => 'جنوب السودان',
    'name_en' => 'South Sudan',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  210 => 
  array (
    'code' => 'ES',
    'dial_code' => '+34',
    'name_ar' => 'إسبانيا',
    'name_en' => 'Spain',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  211 => 
  array (
    'code' => 'LK',
    'dial_code' => '+94',
    'name_ar' => 'سريلانكا',
    'name_en' => 'Sri Lanka',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  212 => 
  array (
    'code' => 'SR',
    'dial_code' => '+597',
    'name_ar' => 'سورينام',
    'name_en' => 'Suriname',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  213 => 
  array (
    'code' => 'SE',
    'dial_code' => '+46',
    'name_ar' => 'السويد',
    'name_en' => 'Sweden',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  214 => 
  array (
    'code' => 'CH',
    'dial_code' => '+41',
    'name_ar' => 'سويسرا',
    'name_en' => 'Switzerland',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  215 => 
  array (
    'code' => 'TW',
    'dial_code' => '+886',
    'name_ar' => 'تايوان',
    'name_en' => 'Taiwan',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  216 => 
  array (
    'code' => 'TJ',
    'dial_code' => '+992',
    'name_ar' => 'طاجيكستان',
    'name_en' => 'Tajikistan',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  217 => 
  array (
    'code' => 'TZ',
    'dial_code' => '+255',
    'name_ar' => 'تنزانيا',
    'name_en' => 'Tanzania',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  218 => 
  array (
    'code' => 'TH',
    'dial_code' => '+66',
    'name_ar' => 'تايلاند',
    'name_en' => 'Thailand',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  219 => 
  array (
    'code' => 'TL',
    'dial_code' => '+670',
    'name_ar' => 'تيمور الشرقية',
    'name_en' => 'Timor-Leste',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  220 => 
  array (
    'code' => 'TG',
    'dial_code' => '+228',
    'name_ar' => 'توغو',
    'name_en' => 'Togo',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  221 => 
  array (
    'code' => 'TK',
    'dial_code' => '+690',
    'name_ar' => 'توكيلاو',
    'name_en' => 'Tokelau',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  222 => 
  array (
    'code' => 'TO',
    'dial_code' => '+676',
    'name_ar' => 'تونغا',
    'name_en' => 'Tonga',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  223 => 
  array (
    'code' => 'TT',
    'dial_code' => '+1868',
    'name_ar' => 'ترينيداد وتوباغو',
    'name_en' => 'Trinidad and Tobago',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  224 => 
  array (
    'code' => 'TM',
    'dial_code' => '+993',
    'name_ar' => 'تركمانستان',
    'name_en' => 'Turkmenistan',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  225 => 
  array (
    'code' => 'TC',
    'dial_code' => '+1649',
    'name_ar' => 'جزر توركس وكايكوس',
    'name_en' => 'Turks and Caicos Islands',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  226 => 
  array (
    'code' => 'TV',
    'dial_code' => '+688',
    'name_ar' => 'توفالو',
    'name_en' => 'Tuvalu',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  227 => 
  array (
    'code' => 'VI',
    'dial_code' => '+1340',
    'name_ar' => 'جزر العذراء الأمريكية',
    'name_en' => 'U.S. Virgin Islands',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  228 => 
  array (
    'code' => 'UG',
    'dial_code' => '+256',
    'name_ar' => 'أوغندا',
    'name_en' => 'Uganda',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  229 => 
  array (
    'code' => 'UA',
    'dial_code' => '+380',
    'name_ar' => 'أوكرانيا',
    'name_en' => 'Ukraine',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  230 => 
  array (
    'code' => 'UY',
    'dial_code' => '+598',
    'name_ar' => 'الأوروغواي',
    'name_en' => 'Uruguay',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  231 => 
  array (
    'code' => 'UZ',
    'dial_code' => '+998',
    'name_ar' => 'أوزبكستان',
    'name_en' => 'Uzbekistan',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  232 => 
  array (
    'code' => 'VU',
    'dial_code' => '+678',
    'name_ar' => 'فانواتو',
    'name_en' => 'Vanuatu',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  233 => 
  array (
    'code' => 'VA',
    'dial_code' => '+39',
    'name_ar' => 'الفاتيكان',
    'name_en' => 'Vatican City',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  234 => 
  array (
    'code' => 'VE',
    'dial_code' => '+58',
    'name_ar' => 'فنزويلا',
    'name_en' => 'Venezuela',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  235 => 
  array (
    'code' => 'VN',
    'dial_code' => '+84',
    'name_ar' => 'فيتنام',
    'name_en' => 'Vietnam',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  236 => 
  array (
    'code' => 'WF',
    'dial_code' => '+681',
    'name_ar' => 'واليس وفوتونا',
    'name_en' => 'Wallis and Futuna',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  237 => 
  array (
    'code' => 'EH',
    'dial_code' => '+212',
    'name_ar' => 'الصحراء الغربية',
    'name_en' => 'Western Sahara',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  238 => 
  array (
    'code' => 'ZM',
    'dial_code' => '+260',
    'name_ar' => 'زامبيا',
    'name_en' => 'Zambia',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
  239 => 
  array (
    'code' => 'ZW',
    'dial_code' => '+263',
    'name_ar' => 'زيمبابوي',
    'name_en' => 'Zimbabwe',
    'validation' => 
    array (
      'regex' => '/^\\d{6,15}$/',
    ),
    'placeholder' => 'رقم الهاتف',
    'example' => '',
  ),
),

    'default_country' => 'SA',
];
