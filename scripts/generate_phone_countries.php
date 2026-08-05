<?php

/**
 * Generates config/phone_countries.php with worldwide dial codes.
 * Run: php scripts/generate_phone_countries.php
 */

$priority = [
    'SA' => ['+966', 'السعودية', 'Saudi Arabia', ['regex' => '/^[15]\d{8}$/'], '5xxxxxxxx', '501234567'],
    'EG' => ['+20', 'مصر', 'Egypt', ['regex' => '/^1[0125]\d{8}$/'], '1xxxxxxxxx', '1012345678'],
    'AE' => ['+971', 'الإمارات', 'UAE', ['regex' => '/^5[0-9]\d{7}$/'], '5xxxxxxxx', '501234567'],
    'KW' => ['+965', 'الكويت', 'Kuwait', ['regex' => '/^[569]\d{7}$/'], '5xxxxxxxx', '50123456'],
    'BH' => ['+973', 'البحرين', 'Bahrain', ['regex' => '/^[36]\d{7}$/'], '3xxxxxxxx', '36123456'],
    'QA' => ['+974', 'قطر', 'Qatar', ['regex' => '/^[3-7]\d{7}$/'], '3xxxxxxxx', '33123456'],
    'OM' => ['+968', 'عُمان', 'Oman', ['regex' => '/^[79]\d{7}$/'], '9xxxxxxxx', '91234567'],
    'JO' => ['+962', 'الأردن', 'Jordan', ['regex' => '/^[789]\d{7}$/'], '7xxxxxxxx', '791234567'],
    'IQ' => ['+964', 'العراق', 'Iraq', ['regex' => '/^7[0-9]\d{8}$/'], '7xxxxxxxxx', '7912345678'],
    'SY' => ['+963', 'سوريا', 'Syria', ['regex' => '/^9\d{8}$/'], '9xxxxxxxx', '912345678'],
    'LB' => ['+961', 'لبنان', 'Lebanon', ['regex' => '/^[37]\d{7}$/'], '3xxxxxxxx', '71123456'],
    'YE' => ['+967', 'اليمن', 'Yemen', ['regex' => '/^7[0-9]\d{7}$/'], '7xxxxxxxx', '712345678'],
    'PS' => ['+970', 'فلسطين', 'Palestine', ['regex' => '/^5[0-9]\d{7}$/'], '5xxxxxxxx', '591234567'],
    'SD' => ['+249', 'السودان', 'Sudan', ['regex' => '/^9[0-9]\d{7}$/'], '9xxxxxxxx', '912345678'],
    'MA' => ['+212', 'المغرب', 'Morocco', ['regex' => '/^[5-7]\d{8}$/'], '6xxxxxxxx', '612345678'],
    'DZ' => ['+213', 'الجزائر', 'Algeria', ['regex' => '/^[5-7]\d{8}$/'], '5xxxxxxxx', '551234567'],
    'TN' => ['+216', 'تونس', 'Tunisia', ['regex' => '/^[2-9]\d{7}$/'], '2xxxxxxxx', '20123456'],
    'LY' => ['+218', 'ليبيا', 'Libya', ['regex' => '/^9[0-9]\d{7}$/'], '9xxxxxxxx', '912345678'],
    'US' => ['+1', 'الولايات المتحدة', 'United States', ['regex' => '/^\d{10}$/'], '2015550123', '2015550123'],
    'GB' => ['+44', 'المملكة المتحدة', 'United Kingdom', ['regex' => '/^7\d{9}$/'], '7xxxxxxxxx', '7123456789'],
    'CA' => ['+1', 'كندا', 'Canada', ['regex' => '/^\d{10}$/'], '4165550123', '4165550123'],
    'TR' => ['+90', 'تركيا', 'Turkey', ['regex' => '/^5\d{9}$/'], '5xxxxxxxxx', '5012345678'],
    'DE' => ['+49', 'ألمانيا', 'Germany', ['regex' => '/^\d{6,13}$/'], '15123456789', '15123456789'],
    'FR' => ['+33', 'فرنسا', 'France', ['regex' => '/^[67]\d{8}$/'], '6xxxxxxxx', '612345678'],
    'MY' => ['+60', 'ماليزيا', 'Malaysia', ['regex' => '/^1\d{8,9}$/'], '1xxxxxxxx', '123456789'],
    'ID' => ['+62', 'إندونيسيا', 'Indonesia', ['regex' => '/^8\d{8,11}$/'], '8xxxxxxxxx', '81234567890'],
    'PK' => ['+92', 'باكستان', 'Pakistan', ['regex' => '/^3\d{9}$/'], '3xxxxxxxxx', '3012345678'],
    'IN' => ['+91', 'الهند', 'India', ['regex' => '/^[6-9]\d{9}$/'], '9xxxxxxxxx', '9123456789'],
    'BD' => ['+880', 'بنغلاديش', 'Bangladesh', ['regex' => '/^1\d{9}$/'], '1xxxxxxxxx', '1712345678'],
    'NG' => ['+234', 'نيجيريا', 'Nigeria', ['regex' => '/^[789]\d{9}$/'], '8012345678', '8012345678'],
    'ZA' => ['+27', 'جنوب أفريقيا', 'South Africa', ['regex' => '/^[6-8]\d{8}$/'], '8xxxxxxxx', '821234567'],
    'AU' => ['+61', 'أستراليا', 'Australia', ['regex' => '/^4\d{8}$/'], '4xxxxxxxx', '412345678'],
];

// ISO2 => [dial, ar, en]
$all = [
    'AF' => ['+93', 'أفغانستان', 'Afghanistan'],
    'AL' => ['+355', 'ألبانيا', 'Albania'],
    'DZ' => ['+213', 'الجزائر', 'Algeria'],
    'AS' => ['+1684', 'ساموا الأمريكية', 'American Samoa'],
    'AD' => ['+376', 'أندورا', 'Andorra'],
    'AO' => ['+244', 'أنغولا', 'Angola'],
    'AI' => ['+1264', 'أنغويلا', 'Anguilla'],
    'AG' => ['+1268', 'أنتيغوا وباربودا', 'Antigua and Barbuda'],
    'AR' => ['+54', 'الأرجنتين', 'Argentina'],
    'AM' => ['+374', 'أرمينيا', 'Armenia'],
    'AW' => ['+297', 'أروبا', 'Aruba'],
    'AU' => ['+61', 'أستراليا', 'Australia'],
    'AT' => ['+43', 'النمسا', 'Austria'],
    'AZ' => ['+994', 'أذربيجان', 'Azerbaijan'],
    'BS' => ['+1242', 'البهاما', 'Bahamas'],
    'BH' => ['+973', 'البحرين', 'Bahrain'],
    'BD' => ['+880', 'بنغلاديش', 'Bangladesh'],
    'BB' => ['+1246', 'بربادوس', 'Barbados'],
    'BY' => ['+375', 'بيلاروس', 'Belarus'],
    'BE' => ['+32', 'بلجيكا', 'Belgium'],
    'BZ' => ['+501', 'بليز', 'Belize'],
    'BJ' => ['+229', 'بنين', 'Benin'],
    'BM' => ['+1441', 'برمودا', 'Bermuda'],
    'BT' => ['+975', 'بوتان', 'Bhutan'],
    'BO' => ['+591', 'بوليفيا', 'Bolivia'],
    'BA' => ['+387', 'البوسنة والهرسك', 'Bosnia and Herzegovina'],
    'BW' => ['+267', 'بوتسوانا', 'Botswana'],
    'BR' => ['+55', 'البرازيل', 'Brazil'],
    'IO' => ['+246', 'إقليم المحيط الهندي البريطاني', 'British Indian Ocean Territory'],
    'VG' => ['+1284', 'جزر العذراء البريطانية', 'British Virgin Islands'],
    'BN' => ['+673', 'بروناي', 'Brunei'],
    'BG' => ['+359', 'بلغاريا', 'Bulgaria'],
    'BF' => ['+226', 'بوركينا فاسو', 'Burkina Faso'],
    'BI' => ['+257', 'بوروندي', 'Burundi'],
    'KH' => ['+855', 'كمبوديا', 'Cambodia'],
    'CM' => ['+237', 'الكاميرون', 'Cameroon'],
    'CA' => ['+1', 'كندا', 'Canada'],
    'CV' => ['+238', 'الرأس الأخضر', 'Cape Verde'],
    'KY' => ['+1345', 'جزر كايمان', 'Cayman Islands'],
    'CF' => ['+236', 'جمهورية أفريقيا الوسطى', 'Central African Republic'],
    'TD' => ['+235', 'تشاد', 'Chad'],
    'CL' => ['+56', 'تشيلي', 'Chile'],
    'CN' => ['+86', 'الصين', 'China'],
    'CX' => ['+61', 'جزيرة كريسماس', 'Christmas Island'],
    'CC' => ['+61', 'جزر كوكوس', 'Cocos Islands'],
    'CO' => ['+57', 'كولومبيا', 'Colombia'],
    'KM' => ['+269', 'جزر القمر', 'Comoros'],
    'CG' => ['+242', 'الكونغو', 'Congo'],
    'CD' => ['+243', 'الكونغو الديمقراطية', 'Congo (DRC)'],
    'CK' => ['+682', 'جزر كوك', 'Cook Islands'],
    'CR' => ['+506', 'كوستاريكا', 'Costa Rica'],
    'CI' => ['+225', 'ساحل العاج', "Côte d'Ivoire"],
    'HR' => ['+385', 'كرواتيا', 'Croatia'],
    'CU' => ['+53', 'كوبا', 'Cuba'],
    'CW' => ['+599', 'كوراساو', 'Curaçao'],
    'CY' => ['+357', 'قبرص', 'Cyprus'],
    'CZ' => ['+420', 'التشيك', 'Czech Republic'],
    'DK' => ['+45', 'الدنمارك', 'Denmark'],
    'DJ' => ['+253', 'جيبوتي', 'Djibouti'],
    'DM' => ['+1767', 'دومينيكا', 'Dominica'],
    'DO' => ['+1809', 'جمهورية الدومينيكان', 'Dominican Republic'],
    'EC' => ['+593', 'الإكوادور', 'Ecuador'],
    'EG' => ['+20', 'مصر', 'Egypt'],
    'SV' => ['+503', 'السلفادور', 'El Salvador'],
    'GQ' => ['+240', 'غينيا الاستوائية', 'Equatorial Guinea'],
    'ER' => ['+291', 'إريتريا', 'Eritrea'],
    'EE' => ['+372', 'إستونيا', 'Estonia'],
    'SZ' => ['+268', 'إسواتيني', 'Eswatini'],
    'ET' => ['+251', 'إثيوبيا', 'Ethiopia'],
    'FK' => ['+500', 'جزر فوكلاند', 'Falkland Islands'],
    'FO' => ['+298', 'جزر فارو', 'Faroe Islands'],
    'FJ' => ['+679', 'فيجي', 'Fiji'],
    'FI' => ['+358', 'فنلندا', 'Finland'],
    'FR' => ['+33', 'فرنسا', 'France'],
    'GF' => ['+594', 'غويانا الفرنسية', 'French Guiana'],
    'PF' => ['+689', 'بولينيزيا الفرنسية', 'French Polynesia'],
    'GA' => ['+241', 'الغابون', 'Gabon'],
    'GM' => ['+220', 'غامبيا', 'Gambia'],
    'GE' => ['+995', 'جورجيا', 'Georgia'],
    'DE' => ['+49', 'ألمانيا', 'Germany'],
    'GH' => ['+233', 'غانا', 'Ghana'],
    'GI' => ['+350', 'جبل طارق', 'Gibraltar'],
    'GR' => ['+30', 'اليونان', 'Greece'],
    'GL' => ['+299', 'غرينلاند', 'Greenland'],
    'GD' => ['+1473', 'غرينادا', 'Grenada'],
    'GP' => ['+590', 'غوادلوب', 'Guadeloupe'],
    'GU' => ['+1671', 'غوام', 'Guam'],
    'GT' => ['+502', 'غواتيمالا', 'Guatemala'],
    'GG' => ['+44', 'غيرنزي', 'Guernsey'],
    'GN' => ['+224', 'غينيا', 'Guinea'],
    'GW' => ['+245', 'غينيا بيساو', 'Guinea-Bissau'],
    'GY' => ['+592', 'غيانا', 'Guyana'],
    'HT' => ['+509', 'هايتي', 'Haiti'],
    'HN' => ['+504', 'هندوراس', 'Honduras'],
    'HK' => ['+852', 'هونغ كونغ', 'Hong Kong'],
    'HU' => ['+36', 'المجر', 'Hungary'],
    'IS' => ['+354', 'آيسلندا', 'Iceland'],
    'IN' => ['+91', 'الهند', 'India'],
    'ID' => ['+62', 'إندونيسيا', 'Indonesia'],
    'IR' => ['+98', 'إيران', 'Iran'],
    'IQ' => ['+964', 'العراق', 'Iraq'],
    'IE' => ['+353', 'أيرلندا', 'Ireland'],
    'IM' => ['+44', 'جزيرة مان', 'Isle of Man'],
    'IL' => ['+972', 'إسرائيل', 'Israel'],
    'IT' => ['+39', 'إيطاليا', 'Italy'],
    'JM' => ['+1876', 'جامايكا', 'Jamaica'],
    'JP' => ['+81', 'اليابان', 'Japan'],
    'JE' => ['+44', 'جيرسي', 'Jersey'],
    'JO' => ['+962', 'الأردن', 'Jordan'],
    'KZ' => ['+7', 'كازاخستان', 'Kazakhstan'],
    'KE' => ['+254', 'كينيا', 'Kenya'],
    'KI' => ['+686', 'كيريباتي', 'Kiribati'],
    'XK' => ['+383', 'كوسوفو', 'Kosovo'],
    'KW' => ['+965', 'الكويت', 'Kuwait'],
    'KG' => ['+996', 'قيرغيزستان', 'Kyrgyzstan'],
    'LA' => ['+856', 'لاوس', 'Laos'],
    'LV' => ['+371', 'لاتفيا', 'Latvia'],
    'LB' => ['+961', 'لبنان', 'Lebanon'],
    'LS' => ['+266', 'ليسوتو', 'Lesotho'],
    'LR' => ['+231', 'ليبيريا', 'Liberia'],
    'LY' => ['+218', 'ليبيا', 'Libya'],
    'LI' => ['+423', 'ليختنشتاين', 'Liechtenstein'],
    'LT' => ['+370', 'ليتوانيا', 'Lithuania'],
    'LU' => ['+352', 'لوكسمبورغ', 'Luxembourg'],
    'MO' => ['+853', 'ماكاو', 'Macau'],
    'MG' => ['+261', 'مدغشقر', 'Madagascar'],
    'MW' => ['+265', 'ملاوي', 'Malawi'],
    'MY' => ['+60', 'ماليزيا', 'Malaysia'],
    'MV' => ['+960', 'المالديف', 'Maldives'],
    'ML' => ['+223', 'مالي', 'Mali'],
    'MT' => ['+356', 'مالطا', 'Malta'],
    'MH' => ['+692', 'جزر مارشال', 'Marshall Islands'],
    'MQ' => ['+596', 'مارتينيك', 'Martinique'],
    'MR' => ['+222', 'موريتانيا', 'Mauritania'],
    'MU' => ['+230', 'موريشيوس', 'Mauritius'],
    'YT' => ['+262', 'مايوت', 'Mayotte'],
    'MX' => ['+52', 'المكسيك', 'Mexico'],
    'FM' => ['+691', 'ميكرونيزيا', 'Micronesia'],
    'MD' => ['+373', 'مولدوفا', 'Moldova'],
    'MC' => ['+377', 'موناكو', 'Monaco'],
    'MN' => ['+976', 'منغوليا', 'Mongolia'],
    'ME' => ['+382', 'الجبل الأسود', 'Montenegro'],
    'MS' => ['+1664', 'مونتسيرات', 'Montserrat'],
    'MA' => ['+212', 'المغرب', 'Morocco'],
    'MZ' => ['+258', 'موزمبيق', 'Mozambique'],
    'MM' => ['+95', 'ميانمار', 'Myanmar'],
    'NA' => ['+264', 'ناميبيا', 'Namibia'],
    'NR' => ['+674', 'ناورو', 'Nauru'],
    'NP' => ['+977', 'نيبال', 'Nepal'],
    'NL' => ['+31', 'هولندا', 'Netherlands'],
    'NC' => ['+687', 'كاليدونيا الجديدة', 'New Caledonia'],
    'NZ' => ['+64', 'نيوزيلندا', 'New Zealand'],
    'NI' => ['+505', 'نيكاراغوا', 'Nicaragua'],
    'NE' => ['+227', 'النيجر', 'Niger'],
    'NG' => ['+234', 'نيجيريا', 'Nigeria'],
    'NU' => ['+683', 'نيوي', 'Niue'],
    'NF' => ['+672', 'جزيرة نورفولك', 'Norfolk Island'],
    'KP' => ['+850', 'كوريا الشمالية', 'North Korea'],
    'MK' => ['+389', 'مقدونيا الشمالية', 'North Macedonia'],
    'MP' => ['+1670', 'جزر ماريانا الشمالية', 'Northern Mariana Islands'],
    'NO' => ['+47', 'النرويج', 'Norway'],
    'OM' => ['+968', 'عُمان', 'Oman'],
    'PK' => ['+92', 'باكستان', 'Pakistan'],
    'PW' => ['+680', 'بالاو', 'Palau'],
    'PS' => ['+970', 'فلسطين', 'Palestine'],
    'PA' => ['+507', 'بنما', 'Panama'],
    'PG' => ['+675', 'بابوا غينيا الجديدة', 'Papua New Guinea'],
    'PY' => ['+595', 'باراغواي', 'Paraguay'],
    'PE' => ['+51', 'بيرو', 'Peru'],
    'PH' => ['+63', 'الفلبين', 'Philippines'],
    'PL' => ['+48', 'بولندا', 'Poland'],
    'PT' => ['+351', 'البرتغال', 'Portugal'],
    'PR' => ['+1787', 'بورتوريكو', 'Puerto Rico'],
    'QA' => ['+974', 'قطر', 'Qatar'],
    'RE' => ['+262', 'ريونيون', 'Réunion'],
    'RO' => ['+40', 'رومانيا', 'Romania'],
    'RU' => ['+7', 'روسيا', 'Russia'],
    'RW' => ['+250', 'رواندا', 'Rwanda'],
    'BL' => ['+590', 'سان بارتليمي', 'Saint Barthélemy'],
    'SH' => ['+290', 'سانت هيلينا', 'Saint Helena'],
    'KN' => ['+1869', 'سانت كيتس ونيفيس', 'Saint Kitts and Nevis'],
    'LC' => ['+1758', 'سانت لوسيا', 'Saint Lucia'],
    'MF' => ['+590', 'سانت مارتن', 'Saint Martin'],
    'PM' => ['+508', 'سان بيير وميكلون', 'Saint Pierre and Miquelon'],
    'VC' => ['+1784', 'سانت فنسنت', 'Saint Vincent and the Grenadines'],
    'WS' => ['+685', 'ساموا', 'Samoa'],
    'SM' => ['+378', 'سان مارينو', 'San Marino'],
    'ST' => ['+239', 'ساو تومي وبرينسيبي', 'Sao Tome and Principe'],
    'SA' => ['+966', 'السعودية', 'Saudi Arabia'],
    'SN' => ['+221', 'السنغال', 'Senegal'],
    'RS' => ['+381', 'صربيا', 'Serbia'],
    'SC' => ['+248', 'سيشل', 'Seychelles'],
    'SL' => ['+232', 'سيراليون', 'Sierra Leone'],
    'SG' => ['+65', 'سنغافورة', 'Singapore'],
    'SX' => ['+1721', 'سينت مارتن', 'Sint Maarten'],
    'SK' => ['+421', 'سلوفاكيا', 'Slovakia'],
    'SI' => ['+386', 'سلوفينيا', 'Slovenia'],
    'SB' => ['+677', 'جزر سليمان', 'Solomon Islands'],
    'SO' => ['+252', 'الصومال', 'Somalia'],
    'ZA' => ['+27', 'جنوب أفريقيا', 'South Africa'],
    'KR' => ['+82', 'كوريا الجنوبية', 'South Korea'],
    'SS' => ['+211', 'جنوب السودان', 'South Sudan'],
    'ES' => ['+34', 'إسبانيا', 'Spain'],
    'LK' => ['+94', 'سريلانكا', 'Sri Lanka'],
    'SD' => ['+249', 'السودان', 'Sudan'],
    'SR' => ['+597', 'سورينام', 'Suriname'],
    'SE' => ['+46', 'السويد', 'Sweden'],
    'CH' => ['+41', 'سويسرا', 'Switzerland'],
    'SY' => ['+963', 'سوريا', 'Syria'],
    'TW' => ['+886', 'تايوان', 'Taiwan'],
    'TJ' => ['+992', 'طاجيكستان', 'Tajikistan'],
    'TZ' => ['+255', 'تنزانيا', 'Tanzania'],
    'TH' => ['+66', 'تايلاند', 'Thailand'],
    'TL' => ['+670', 'تيمور الشرقية', 'Timor-Leste'],
    'TG' => ['+228', 'توغو', 'Togo'],
    'TK' => ['+690', 'توكيلاو', 'Tokelau'],
    'TO' => ['+676', 'تونغا', 'Tonga'],
    'TT' => ['+1868', 'ترينيداد وتوباغو', 'Trinidad and Tobago'],
    'TN' => ['+216', 'تونس', 'Tunisia'],
    'TR' => ['+90', 'تركيا', 'Turkey'],
    'TM' => ['+993', 'تركمانستان', 'Turkmenistan'],
    'TC' => ['+1649', 'جزر توركس وكايكوس', 'Turks and Caicos Islands'],
    'TV' => ['+688', 'توفالو', 'Tuvalu'],
    'VI' => ['+1340', 'جزر العذراء الأمريكية', 'U.S. Virgin Islands'],
    'UG' => ['+256', 'أوغندا', 'Uganda'],
    'UA' => ['+380', 'أوكرانيا', 'Ukraine'],
    'AE' => ['+971', 'الإمارات', 'United Arab Emirates'],
    'GB' => ['+44', 'المملكة المتحدة', 'United Kingdom'],
    'US' => ['+1', 'الولايات المتحدة', 'United States'],
    'UY' => ['+598', 'الأوروغواي', 'Uruguay'],
    'UZ' => ['+998', 'أوزبكستان', 'Uzbekistan'],
    'VU' => ['+678', 'فانواتو', 'Vanuatu'],
    'VA' => ['+39', 'الفاتيكان', 'Vatican City'],
    'VE' => ['+58', 'فنزويلا', 'Venezuela'],
    'VN' => ['+84', 'فيتنام', 'Vietnam'],
    'WF' => ['+681', 'واليس وفوتونا', 'Wallis and Futuna'],
    'EH' => ['+212', 'الصحراء الغربية', 'Western Sahara'],
    'YE' => ['+967', 'اليمن', 'Yemen'],
    'ZM' => ['+260', 'زامبيا', 'Zambia'],
    'ZW' => ['+263', 'زيمبابوي', 'Zimbabwe'],
];

$generic = ['regex' => '/^\d{6,15}$/'];
$countries = [];

// Priority first
foreach ($priority as $code => $row) {
    [$dial, $ar, $en, $validation, $placeholder, $example] = $row;
    $countries[$code] = [
        'code' => $code,
        'dial_code' => $dial,
        'name_ar' => $ar,
        'name_en' => $en,
        'validation' => $validation,
        'placeholder' => $placeholder,
        'example' => $example,
    ];
}

// Then remaining, sorted by English name
$rest = [];
foreach ($all as $code => $row) {
    if (isset($countries[$code])) {
        continue;
    }
    [$dial, $ar, $en] = $row;
    $rest[] = [
        'code' => $code,
        'dial_code' => $dial,
        'name_ar' => $ar,
        'name_en' => $en,
        'validation' => $generic,
        'placeholder' => 'رقم الهاتف',
        'example' => '',
    ];
}

usort($rest, fn ($a, $b) => strcasecmp($a['name_en'], $b['name_en']));

$final = array_values(array_merge(array_values($countries), $rest));

$export = var_export($final, true);
// pretty-ish: convert array() to []
$export = preg_replace('/\barray \(/', '[', $export);
$export = preg_replace('/\)$/', ']', $export);
$export = str_replace(')', ']', $export);

$php = <<<PHP
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | أكواد الدول والتحقق من أرقام الهواتف (عالمي)
    |--------------------------------------------------------------------------
    */
    'countries' => {$export},

    'default_country' => 'SA',
];

PHP;

// Fix nested array syntax more carefully with json roundtrip for clean PHP 8 arrays
$json = json_encode($final, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
$phpArray = var_export(json_decode($json, true), true);

$out = "<?php\n\nreturn [\n"
    . "    /*\n"
    . "    |--------------------------------------------------------------------------\n"
    . "    | أكواد الدول والتحقق من أرقام الهواتف (عالمي + بحث في فورم التسجيل)\n"
    . "    |--------------------------------------------------------------------------\n"
    . "    */\n"
    . "    'countries' => " . $phpArray . ",\n\n"
    . "    'default_country' => 'SA',\n"
    . "];\n";

$path = dirname(__DIR__) . '/config/phone_countries.php';
file_put_contents($path, $out);
echo 'Wrote ' . count($final) . " countries to {$path}\n";
