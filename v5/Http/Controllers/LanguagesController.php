<?php

/**
 * Ushahidi Languages controller
 *
 * @author    Ushahidi Team <technologyteam@ushahidi.com>
 * @package   Ushahidi\Application
 * @license   https://www.gnu.org/licenses/agpl-3.0.html (AGPL3)
 * @copyright 2020 Ushahidi
 */

namespace v5\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

class LanguagesController extends V5Controller
{


    /**
     * An silly endpoint that returns an array of languages
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $languages = [
            [
                'code' => 'ach',
                'name' => 'Acoli',
            ],
            [
                'code' => 'ady',
                'name' => 'Adyghe',
            ],
            [
                'code' => 'af',
                'name' => 'Afrikaans',
            ],
            [
                'code' => 'af-ZA',
                'name' => 'Afrikaans (South Africa)',
            ],
            [
                'code' => 'ak',
                'name' => 'Akan',
            ],
            [
                'code' => 'sq',
                'name' => 'Albanian',
            ],
            [
                'code' => 'sq-AL',
                'name' => 'Albanian (Albania)',
            ],
            [
                'code' => 'aln',
                'name' => 'Albanian Gheg',
            ],
            [
                'code' => 'am',
                'name' => 'Amharic',
            ],
            [
                'code' => 'am-ET',
                'name' => 'Amharic (Ethiopia)',
            ],
            [
                'code' => 'ar',
                'name' => 'Arabic',
            ],
            [
                'code' => 'ar-EG',
                'name' => 'Arabic (Egypt)',
            ],
            [
                'code' => 'ar-SA',
                'name' => 'Arabic (Saudi Arabia)',
            ],
            [
                'code' => 'ar-SD',
                'name' => 'Arabic (Sudan)',
            ],
            [
                'code' => 'ar-SY',
                'name' => 'Arabic (Syria)',
            ],
            [
                'code' => 'ar-AA',
                'name' => 'Arabic (Unitag)',
            ],
            [
                'code' => 'an',
                'name' => 'Aragonese',
            ],
            [
                'code' => 'hy',
                'name' => 'Armenian',
            ],
            [
                'code' => 'hy-AM',
                'name' => 'Armenian (Armenia)',
            ],
            [
                'code' => 'as',
                'name' => 'Assamese',
            ],
            [
                'code' => 'as-IN',
                'name' => 'Assamese (India)',
            ],
            [
                'code' => 'ast',
                'name' => 'Asturian',
            ],
            [
                'code' => 'ast-ES',
                'name' => 'Asturian (Spain)',
            ],
            [
                'code' => 'az',
                'name' => 'Azerbaijani',
            ],
            [
                'code' => 'az@Arab',
                'name' => 'Azerbaijani (Arabic)',
            ],
            [
                'code' => 'az-AZ',
                'name' => 'Azerbaijani (Azerbaijan)',
            ],
            [
                'code' => 'az-IR',
                'name' => 'Azerbaijani (Iran)',
            ],
            [
                'code' => 'az@latin',
                'name' => 'Azerbaijani (Latin)',
            ],
            [
                'code' => 'bal',
                'name' => 'Balochi',
            ],
            [
                'code' => 'ba',
                'name' => 'Bashkir',
            ],
            [
                'code' => 'eu',
                'name' => 'Basque',
            ],
            [
                'code' => 'eu-ES',
                'name' => 'Basque (Spain)',
            ],
            [
                'code' => 'bar',
                'name' => 'Bavarian',
            ],
            [
                'code' => 'be',
                'name' => 'Belarusian',
            ],
            [
                'code' => 'be-BY',
                'name' => 'Belarusian (Belarus)',
            ],
            [
                'code' => 'be@tarask',
                'name' => 'Belarusian (Tarask)',
            ],
            [
                'code' => 'bn',
                'name' => 'Bengali',
            ],
            [
                'code' => 'bn-BD',
                'name' => 'Bengali (Bangladesh)',
            ],
            [
                'code' => 'bn-IN',
                'name' => 'Bengali (India)',
            ],
            [
                'code' => 'brx',
                'name' => 'Bodo',
            ],
            [
                'code' => 'bs',
                'name' => 'Bosnian',
            ],
            [
                'code' => 'bs-BA',
                'name' => 'Bosnian (Bosnia and Herzegovina)',
            ],
            [
                'code' => 'br',
                'name' => 'Breton',
            ],
            [
                'code' => 'bg',
                'name' => 'Bulgarian',
            ],
            [
                'code' => 'bg-BG',
                'name' => 'Bulgarian (Bulgaria)',
            ],
            [
                'code' => 'my',
                'name' => 'Burmese',
            ],
            [
                'code' => 'my-MM',
                'name' => 'Burmese (Myanmar)',
            ],
            [
                'code' => 'ca',
                'name' => 'Catalan',
            ],
            [
                'code' => 'ca-ES',
                'name' => 'Catalan (Spain)',
            ],
            [
                'code' => 'ca@valencia',
                'name' => 'Catalan (Valencian)',
            ],
            [
                'code' => 'ceb',
                'name' => 'Cebuano',
            ],
            [
                'code' => 'tzm',
                'name' => 'Central Atlas Tamazight',
            ],
            [
                'code' => 'hne',
                'name' => 'Chhattisgarhi',
            ],
            [
                'code' => 'cgg',
                'name' => 'Chiga',
            ],
            [
                'code' => 'zh',
                'name' => 'Chinese',
            ],
            [
                'code' => 'zh-CN',
                'name' => 'Chinese (China)',
            ],
            [
                'code' => 'zh-CN.GB2312',
                'name' => 'Chinese (China) (GB2312)',
            ],
            [
                'code' => 'gan',
                'name' => 'Chinese (Gan)',
            ],
            [
                'code' => 'hak',
                'name' => 'Chinese (Hakka)',
            ],
            [
                'code' => 'zh-HK',
                'name' => 'Chinese (Hong Kong)',
            ],
            [
                'code' => 'czh',
                'name' => 'Chinese (Huizhou)',
            ],
            [
                'code' => 'cjy',
                'name' => 'Chinese (Jinyu)',
            ],
            [
                'code' => 'lzh',
                'name' => 'Chinese (Literary)',
            ],
            [
                'code' => 'cmn',
                'name' => 'Chinese (Mandarin)',
            ],
            [
                'code' => 'mnp',
                'name' => 'Chinese (Min Bei)',
            ],
            [
                'code' => 'cdo',
                'name' => 'Chinese (Min Dong)',
            ],
            [
                'code' => 'nan',
                'name' => 'Chinese (Min Nan)',
            ],
            [
                'code' => 'czo',
                'name' => 'Chinese (Min Zhong)',
            ],
            [
                'code' => 'cpx',
                'name' => 'Chinese (Pu-Xian)',
            ],
            [
                'code' => 'zh-Hans',
                'name' => 'Chinese Simplified',
            ],
            [
                'code' => 'zh-TW',
                'name' => 'Chinese (Taiwan)',
            ],
            [
                'code' => 'zh-TW.Big5',
                'name' => 'Chinese (Taiwan) (Big5) ',
            ],
            [
                'code' => 'zh-Hant',
                'name' => 'Chinese Traditional',
            ],
            [
                'code' => 'wuu',
                'name' => 'Chinese (Wu)',
            ],
            [
                'code' => 'hsn',
                'name' => 'Chinese (Xiang)',
            ],
            [
                'code' => 'yue',
                'name' => 'Chinese (Yue)',
            ],
            [
                'code' => 'cv',
                'name' => 'Chuvash',
            ],
            [
                'code' => 'ksh',
                'name' => 'Colognian',
            ],
            [
                'code' => 'kw',
                'name' => 'Cornish',
            ],
            [
                'code' => 'co',
                'name' => 'Corsican',
            ],
            [
                'code' => 'crh',
                'name' => 'Crimean Turkish',
            ],
            [
                'code' => 'hr',
                'name' => 'Croatian',
            ],
            [
                'code' => 'hr-HR',
                'name' => 'Croatian (Croatia)',
            ],
            [
                'code' => 'cs',
                'name' => 'Czech',
            ],
            [
                'code' => 'cs-CZ',
                'name' => 'Czech (Czech Republic)',
            ],
            [
                'code' => 'da',
                'name' => 'Danish',
            ],
            [
                'code' => 'da-DK',
                'name' => 'Danish (Denmark)',
            ],
            [
                'code' => 'dv',
                'name' => 'Divehi',
            ],
            [
                'code' => 'doi',
                'name' => 'Dogri',
            ],
            [
                'code' => 'nl',
                'name' => 'Dutch',
            ],
            [
                'code' => 'nl-BE',
                'name' => 'Dutch (Belgium)',
            ],
            [
                'code' => 'nl-NL',
                'name' => 'Dutch (Netherlands)',
            ],
            [
                'code' => 'dz',
                'name' => 'Dzongkha',
            ],
            [
                'code' => 'dz-BT',
                'name' => 'Dzongkha (Bhutan)',
            ],
            [
                'code' => 'en',
                'name' => 'English',
            ],
            [
                'code' => 'en-AU',
                'name' => 'English (Australia)',
            ],
            [
                'code' => 'en-AT',
                'name' => 'English (Austria)',
            ],
            [
                'code' => 'en-BD',
                'name' => 'English (Bangladesh)',
            ],
            [
                'code' => 'en-BE',
                'name' => 'English (Belgium)',
            ],
            [
                'code' => 'en-CA',
                'name' => 'English (Canada)',
            ],
            [
                'code' => 'en-CL',
                'name' => 'English (Chile)',
            ],
            [
                'code' => 'en-CZ',
                'name' => 'English (Czech Republic)',
            ],
            [
                'code' => 'en-ee',
                'name' => 'English (Estonia)',
            ],
            [
                'code' => 'en-FI',
                'name' => 'English (Finland)',
            ],
            [
                'code' => 'en-DE',
                'name' => 'English (Germany)',
            ],
            [
                'code' => 'en-GH',
                'name' => 'English (Ghana)',
            ],
            [
                'code' => 'en-HK',
                'name' => 'English (Hong Kong)',
            ],
            [
                'code' => 'en-HU',
                'name' => 'English (Hungary)',
            ],
            [
                'code' => 'en-IN',
                'name' => 'English (India)',
            ],
            [
                'code' => 'en-IE',
                'name' => 'English (Ireland)',
            ],
            [
                'code' => 'en-lv',
                'name' => 'English (Latvia)',
            ],
            [
                'code' => 'en-lt',
                'name' => 'English (Lithuania)',
            ],
            [
                'code' => 'en-NL',
                'name' => 'English (Netherlands)',
            ],
            [
                'code' => 'en-NZ',
                'name' => 'English (New Zealand)',
            ],
            [
                'code' => 'en-NG',
                'name' => 'English (Nigeria)',
            ],
            [
                'code' => 'en-PK',
                'name' => 'English (Pakistan)',
            ],
            [
                'code' => 'en-PL',
                'name' => 'English (Poland)',
            ],
            [
                'code' => 'en-RO',
                'name' => 'English (Romania)',
            ],
            [
                'code' => 'en-SK',
                'name' => 'English (Slovakia)',
            ],
            [
                'code' => 'en-ZA',
                'name' => 'English (South Africa)',
            ],
            [
                'code' => 'en-LK',
                'name' => 'English (Sri Lanka)',
            ],
            [
                'code' => 'en-SE',
                'name' => 'English (Sweden)',
            ],
            [
                'code' => 'en-CH',
                'name' => 'English (Switzerland)',
            ],
            [
                'code' => 'en-GB',
                'name' => 'English (United Kingdom)',
            ],
            [
                'code' => 'en-US',
                'name' => 'English (United States)',
            ],
            [
                'code' => 'en-EN',
                'name' => 'English',
            ],
            [
                'code' => 'myv',
                'name' => 'Erzya',
            ],
            [
                'code' => 'eo',
                'name' => 'Esperanto',
            ],
            [
                'code' => 'et',
                'name' => 'Estonian',
            ],
            [
                'code' => 'et-EE',
                'name' => 'Estonian (Estonia)',
            ],
            [
                'code' => 'fo',
                'name' => 'Faroese',
            ],
            [
                'code' => 'fo-FO',
                'name' => 'Faroese (Faroe Islands)',
            ],
            [
                'code' => 'fil',
                'name' => 'Filipino',
            ],
            [
                'code' => 'fi',
                'name' => 'Finnish',
            ],
            [
                'code' => 'fi-FI',
                'name' => 'Finnish (Finland)',
            ],
            [
                'code' => 'frp',
                'name' => 'Franco-Provençal (Arpitan)',
            ],
            [
                'code' => 'fr',
                'name' => 'French',
            ],
            [
                'code' => 'fr-BE',
                'name' => 'French (Belgium)',
            ],
            [
                'code' => 'fr-CA',
                'name' => 'French (Canada)',
            ],
            [
                'code' => 'fr-FR',
                'name' => 'French (France)',
            ],
            [
                'code' => 'fr-CH',
                'name' => 'French (Switzerland)',
            ],
            [
                'code' => 'fur',
                'name' => 'Friulian',
            ],
            [
                'code' => 'ff',
                'name' => 'Fulah',
            ],
            [
                'code' => 'ff-SN',
                'name' => 'Fulah (Senegal)',
            ],
            [
                'code' => 'gd',
                'name' => 'Gaelic, Scottish',
            ],
            [
                'code' => 'gl',
                'name' => 'Galician',
            ],
            [
                'code' => 'gl-ES',
                'name' => 'Galician (Spain)',
            ],
            [
                'code' => 'lg',
                'name' => 'Ganda',
            ],
            [
                'code' => 'ka',
                'name' => 'Georgian',
            ],
            [
                'code' => 'ka-GE',
                'name' => 'Georgian (Georgia)',
            ],
            [
                'code' => 'de',
                'name' => 'German',
            ],
            [
                'code' => 'de-AT',
                'name' => 'German (Austria)',
            ],
            [
                'code' => 'de-DE',
                'name' => 'German (Germany)',
            ],
            [
                'code' => 'de-CH',
                'name' => 'German (Switzerland)',
            ],
            [
                'code' => 'el',
                'name' => 'Greek',
            ],
            [
                'code' => 'el-GR',
                'name' => 'Greek (Greece)',
            ],
            [
                'code' => 'kl',
                'name' => 'Greenlandic',
            ],
            [
                'code' => 'gu',
                'name' => 'Gujarati',
            ],
            [
                'code' => 'gu-IN',
                'name' => 'Gujarati (India)',
            ],
            [
                'code' => 'gun',
                'name' => 'Gun',
            ],
            [
                'code' => 'ht',
                'name' => 'Haitian (Haitian Creole)',
            ],
            [
                'code' => 'ht-HT',
                'name' => 'Haitian (Haitian Creole) (Haiti)',
            ],
            [
                'code' => 'ha',
                'name' => 'Hausa',
            ],
            [
                'code' => 'haw',
                'name' => 'Hawaiian',
            ],
            [
                'code' => 'he',
                'name' => 'Hebrew',
            ],
            [
                'code' => 'he-IL',
                'name' => 'Hebrew (Israel)',
            ],
            [
                'code' => 'hi',
                'name' => 'Hindi',
            ],
            [
                'code' => 'hi-IN',
                'name' => 'Hindi (India)',
            ],
            [
                'code' => 'hu',
                'name' => 'Hungarian',
            ],
            [
                'code' => 'hu-HU',
                'name' => 'Hungarian (Hungary)',
            ],
            [
                'code' => 'is',
                'name' => 'Icelandic',
            ],
            [
                'code' => 'is-IS',
                'name' => 'Icelandic (Iceland)',
            ],
            [
                'code' => 'io',
                'name' => 'Ido',
            ],
            [
                'code' => 'ig',
                'name' => 'Igbo',
            ],
            [
                'code' => 'ilo',
                'name' => 'Iloko',
            ],
            [
                'code' => 'id',
                'name' => 'Indonesian',
            ],
            [
                'code' => 'id-ID',
                'name' => 'Indonesian (Indonesia)',
            ],
            [
                'code' => 'ia',
                'name' => 'Interlingua',
            ],
            [
                'code' => 'iu',
                'name' => 'Inuktitut',
            ],
            [
                'code' => 'ga',
                'name' => 'Irish',
            ],
            [
                'code' => 'ga-IE',
                'name' => 'Irish (Ireland)',
            ],
            [
                'code' => 'it',
                'name' => 'Italian',
            ],
            [
                'code' => 'it-IT',
                'name' => 'Italian (Italy)',
            ],
            [
                'code' => 'it-CH',
                'name' => 'Italian (Switzerland)',
            ],
            [
                'code' => 'ja',
                'name' => 'Japanese',
            ],
            [
                'code' => 'ja-JP',
                'name' => 'Japanese (Japan)',
            ],
            [
                'code' => 'jv',
                'name' => 'Javanese',
            ],
            [
                'code' => 'kab',
                'name' => 'Kabyle',
            ],
            [
                'code' => 'kn',
                'name' => 'Kannada',
            ],
            [
                'code' => 'kn-IN',
                'name' => 'Kannada (India)',
            ],
            [
                'code' => 'pam',
                'name' => 'Kapampangan',
            ],
            [
                'code' => 'ks',
                'name' => 'Kashmiri',
            ],
            [
                'code' => 'ks-IN',
                'name' => 'Kashmiri (India)',
            ],
            [
                'code' => 'csb',
                'name' => 'Kashubian',
            ],
            [
                'code' => 'kk',
                'name' => 'Kazakh',
            ],
            [
                'code' => 'kk@Arab',
                'name' => 'Kazakh (Arabic)',
            ],
            [
                'code' => 'kk@Cyrl',
                'name' => 'Kazakh (Cyrillic)',
            ],
            [
                'code' => 'kk-KZ',
                'name' => 'Kazakh (Kazakhstan)',
            ],
            [
                'code' => 'kk@latin',
                'name' => 'Kazakh (Latin)',
            ],
            [
                'code' => 'km',
                'name' => 'Khmer',
            ],
            [
                'code' => 'km-KH',
                'name' => 'Khmer (Cambodia)',
            ],
            [
                'code' => 'rw',
                'name' => 'Kinyarwanda',
            ],
            [
                'code' => 'ky',
                'name' => 'Kirgyz',
            ],
            [
                'code' => 'tlh',
                'name' => 'Klingon',
            ],
            [
                'code' => 'kok',
                'name' => 'Konkani',
            ],
            [
                'code' => 'ko',
                'name' => 'Korean',
            ],
            [
                'code' => 'ko-KR',
                'name' => 'Korean (Korea)',
            ],
            [
                'code' => 'ku',
                'name' => 'Kurdish',
            ],
            [
                'code' => 'ku-IQ',
                'name' => 'Kurdish (Iraq)',
            ],
            [
                'code' => 'lad',
                'name' => 'Ladino',
            ],
            [
                'code' => 'lo',
                'name' => 'Lao',
            ],
            [
                'code' => 'lo-LA',
                'name' => 'Lao (Laos)',
            ],
            [
                'code' => 'ltg',
                'name' => 'Latgalian',
            ],
            [
                'code' => 'la',
                'name' => 'Latin',
            ],
            [
                'code' => 'lv',
                'name' => 'Latvian',
            ],
            [
                'code' => 'lv-LV',
                'name' => 'Latvian (Latvia)',
            ],
            [
                'code' => 'lez',
                'name' => 'Lezghian',
            ],
            [
                'code' => 'lij',
                'name' => 'Ligurian',
            ],
            [
                'code' => 'li',
                'name' => 'Limburgian',
            ],
            [
                'code' => 'ln',
                'name' => 'Lingala',
            ],
            [
                'code' => 'lt',
                'name' => 'Lithuanian',
            ],
            [
                'code' => 'lt-LT',
                'name' => 'Lithuanian (Lithuania)',
            ],
            [
                'code' => 'jbo',
                'name' => 'Lojban',
            ],
            [
                'code' => 'en@lolcat',
                'name' => 'LOLCAT English',
            ],
            [
                'code' => 'lmo',
                'name' => 'Lombard',
            ],
            [
                'code' => 'dsb',
                'name' => 'Lower Sorbian',
            ],
            [
                'code' => 'nds',
                'name' => 'Low German',
            ],
            [
                'code' => 'lb',
                'name' => 'Luxembourgish',
            ],
            [
                'code' => 'mk',
                'name' => 'Macedonian',
            ],
            [
                'code' => 'mk-MK',
                'name' => 'Macedonian (Macedonia)',
            ],
            [
                'code' => 'mai',
                'name' => 'Maithili',
            ],
            [
                'code' => 'mg',
                'name' => 'Malagasy',
            ],
            [
                'code' => 'ms',
                'name' => 'Malay',
            ],
            [
                'code' => 'ml',
                'name' => 'Malayalam',
            ],
            [
                'code' => 'ml-IN',
                'name' => 'Malayalam (India)',
            ],
            [
                'code' => 'ms-MY',
                'name' => 'Malay (Malaysia)',
            ],
            [
                'code' => 'mt',
                'name' => 'Maltese',
            ],
            [
                'code' => 'mt-MT',
                'name' => 'Maltese (Malta)',
            ],
            [
                'code' => 'mni',
                'name' => 'Manipuri',
            ],
            [
                'code' => 'mi',
                'name' => 'Maori',
            ],
            [
                'code' => 'arn',
                'name' => 'Mapudungun',
            ],
            [
                'code' => 'mr',
                'name' => 'Marathi',
            ],
            [
                'code' => 'mr-IN',
                'name' => 'Marathi (India)',
            ],
            [
                'code' => 'mh',
                'name' => 'Marshallese',
            ],
            [
                'code' => 'mw1',
                'name' => 'Mirandese',
            ],
            [
                'code' => 'mn',
                'name' => 'Mongolian',
            ],
            [
                'code' => 'mn-MN',
                'name' => 'Mongolian (Mongolia)',
            ],
            [
                'code' => 'nah',
                'name' => 'Nahuatl',
            ],
            [
                'code' => 'nv',
                'name' => 'Navajo',
            ],
            [
                'code' => 'nr',
                'name' => 'Ndebele, South',
            ],
            [
                'code' => 'nap',
                'name' => 'Neapolitan',
            ],
            [
                'code' => 'ne',
                'name' => 'Nepali',
            ],
            [
                'code' => 'ne-NP',
                'name' => 'Nepali (Nepal)',
            ],
            [
                'code' => 'nia',
                'name' => 'Nias',
            ],
            [
                'code' => 'nqo',
                'name' => "N'ko",
            ],
            [
                'code' => 'se',
                'name' => 'Northern Sami',
            ],
            [
                'code' => 'nso',
                'name' => 'Northern Sotho',
            ],
            [
                'code' => 'no',
                'name' => 'Norwegian',
            ],
            [
                'code' => 'nb',
                'name' => 'Norwegian Bokmål',
            ],
            [
                'code' => 'nb-NO',
                'name' => 'Norwegian Bokmål (Norway)',
            ],
            [
                'code' => 'no-NO',
                'name' => 'Norwegian (Norway)',
            ],
            [
                'code' => 'nn',
                'name' => 'Norwegian Nynorsk',
            ],
            [
                'code' => 'nn-NO',
                'name' => 'Norwegian Nynorsk (Norway)',
            ],
            [
                'code' => 'ny',
                'name' => 'Nyanja',
            ],
            [
                'code' => 'oc',
                'name' => 'Occitan (post 1500)',
            ],
            [
                'code' => 'or',
                'name' => 'Oriya',
            ],
            [
                'code' => 'or-IN',
                'name' => 'Oriya (India)',
            ],
            [
                'code' => 'om',
                'name' => 'Oromo',
            ],
            [
                'code' => 'os',
                'name' => 'Ossetic',
            ],
            [
                'code' => 'pfl',
                'name' => 'Palatinate German',
            ],
            [
                'code' => 'pa',
                'name' => 'Panjabi (Punjabi)',
            ],
            [
                'code' => 'pa-IN',
                'name' => 'Panjabi (Punjabi) (India)',
            ],
            [
                'code' => 'pap',
                'name' => 'Papiamento',
            ],
            [
                'code' => 'fa',
                'name' => 'Persian',
            ],
            [
                'code' => 'fa-AF',
                'name' => 'Persian (Afghanistan)',
            ],
            [
                'code' => 'fa-IR',
                'name' => 'Persian (Iran)',
            ],
            [
                'code' => 'pms',
                'name' => 'Piemontese',
            ],
            [
                'code' => 'en@pirate',
                'name' => 'Pirate English',
            ],
            [
                'code' => 'pl',
                'name' => 'Polish',
            ],
            [
                'code' => 'pl-PL',
                'name' => 'Polish (Poland)',
            ],
            [
                'code' => 'pt',
                'name' => 'Portuguese',
            ],
            [
                'code' => 'pt-BR',
                'name' => 'Portuguese (Brazil)',
            ],
            [
                'code' => 'pt-PT',
                'name' => 'Portuguese (Portugal)',
            ],
            [
                'code' => 'ps',
                'name' => 'Pushto',
            ],
            [
                'code' => 'ro',
                'name' => 'Romanian',
            ],
            [
                'code' => 'ro-RO',
                'name' => 'Romanian (Romania)',
            ],
            [
                'code' => 'rm',
                'name' => 'Romansh',
            ],
            [
                'code' => 'ru',
                'name' => 'Russian',
            ],
            [
                'code' => 'ru-ee',
                'name' => 'Russian (Estonia)',
            ],
            [
                'code' => 'ru-lv',
                'name' => 'Russian (Latvia)',
            ],
            [
                'code' => 'ru-lt',
                'name' => 'Russian (Lithuania)',
            ],
            [
                'code' => 'ru@petr1708',
                'name' => 'Russian Petrine orthography',
            ],
            [
                'code' => 'ru-RU',
                'name' => 'Russian (Russia)',
            ],
            [
                'code' => 'sah',
                'name' => 'Sakha (Yakut)',
            ],
            [
                'code' => 'sm',
                'name' => 'Samoan',
            ],
            [
                'code' => 'sa',
                'name' => 'Sanskrit',
            ],
            [
                'code' => 'sat',
                'name' => 'Santali',
            ],
            [
                'code' => 'sc',
                'name' => 'Sardinian',
            ],
            [
                'code' => 'sco',
                'name' => 'Scots',
            ],
            [
                'code' => 'sr',
                'name' => 'Serbian',
            ],
            [
                'code' => 'sr@Ijekavian',
                'name' => 'Serbian (Ijekavian)',
            ],
            [
                'code' => 'sr@ijekavianlatin',
                'name' => 'Serbian (Ijekavian Latin)',
            ],
            [
                'code' => 'sr@latin',
                'name' => 'Serbian (Latin)',
            ],
            [
                'code' => 'sr-RS@latin',
                'name' => 'Serbian (Latin) (Serbia)',
            ],
            [
                'code' => 'sr-RS',
                'name' => 'Serbian (Serbia)',
            ],
            [
                'code' => 'sn',
                'name' => 'Shona',
            ],
            [
                'code' => 'scn',
                'name' => 'Sicilian',
            ],
            [
                'code' => 'szl',
                'name' => 'Silesian',
            ],
            [
                'code' => 'sd',
                'name' => 'Sindhi',
            ],
            [
                'code' => 'si',
                'name' => 'Sinhala',
            ],
            [
                'code' => 'si-LK',
                'name' => 'Sinhala (Sri Lanka)',
            ],
            [
                'code' => 'sk',
                'name' => 'Slovak',
            ],
            [
                'code' => 'sk-SK',
                'name' => 'Slovak (Slovakia)',
            ],
            [
                'code' => 'sl',
                'name' => 'Slovenian',
            ],
            [
                'code' => 'sl-SI',
                'name' => 'Slovenian (Slovenia)',
            ],
            [
                'code' => 'so',
                'name' => 'Somali',
            ],
            [
                'code' => 'son',
                'name' => 'Songhay',
            ],
            [
                'code' => 'st',
                'name' => 'Sotho, Southern',
            ],
            [
                'code' => 'st-ZA',
                'name' => 'Sotho, Southern (South Africa)',
            ],
            [
                'code' => 'sma',
                'name' => 'Southern Sami',
            ],
            [
                'code' => 'es',
                'name' => 'Spanish',
            ],
            [
                'code' => 'es-AR',
                'name' => 'Spanish (Argentina)',
            ],
            [
                'code' => 'es-BO',
                'name' => 'Spanish (Bolivia)',
            ],
            [
                'code' => 'es-CL',
                'name' => 'Spanish (Chile)',
            ],
            [
                'code' => 'es-CO',
                'name' => 'Spanish (Colombia)',
            ],
            [
                'code' => 'es-CR',
                'name' => 'Spanish (Costa Rica)',
            ],
            [
                'code' => 'es-DO',
                'name' => 'Spanish (Dominican Republic)',
            ],
            [
                'code' => 'es-EC',
                'name' => 'Spanish (Ecuador)',
            ],
            [
                'code' => 'es-SV',
                'name' => 'Spanish (El Salvador)',
            ],
            [
                'code' => 'es-GT',
                'name' => 'Spanish (Guatemala)',
            ],
            [
                'code' => 'es-419',
                'name' => 'Spanish (Latin America)',
            ],
            [
                'code' => 'es-MX',
                'name' => 'Spanish (Mexico)',
            ],
            [
                'code' => 'es-NI',
                'name' => 'Spanish (Nicaragua)',
            ],
            [
                'code' => 'es-PA',
                'name' => 'Spanish (Panama)',
            ],
            [
                'code' => 'es-PY',
                'name' => 'Spanish (Paraguay)',
            ],
            [
                'code' => 'es-PE',
                'name' => 'Spanish (Peru)',
            ],
            [
                'code' => 'es-PR',
                'name' => 'Spanish (Puerto Rico)',
            ],
            [
                'code' => 'es-ES',
                'name' => 'Spanish (Spain)',
            ],
            [
                'code' => 'es-US',
                'name' => 'Spanish (United States)',
            ],
            [
                'code' => 'es-UY',
                'name' => 'Spanish (Uruguay)',
            ],
            [
                'code' => 'es-VE',
                'name' => 'Spanish (Venezuela)',
            ],
            [
                'code' => 'su',
                'name' => 'Sundanese',
            ],
            [
                'code' => 'sw',
                'name' => 'Swahili',
            ],
            [
                'code' => 'sw-KE',
                'name' => 'Swahili (Kenya)',
            ],
            [
                'code' => 'ss',
                'name' => 'Swati',
            ],
            [
                'code' => 'sv',
                'name' => 'Swedish',
            ],
            [
                'code' => 'sv-FI',
                'name' => 'Swedish (Finland)',
            ],
            [
                'code' => 'sv-SE',
                'name' => 'Swedish (Sweden)',
            ],
            [
                'code' => 'tl',
                'name' => 'Tagalog',
            ],
            [
                'code' => 'tl-PH',
                'name' => 'Tagalog (Philippines)',
            ],
            [
                'code' => 'tg',
                'name' => 'Tajik',
            ],
            [
                'code' => 'tg-TJ',
                'name' => 'Tajik (Tajikistan)',
            ],
            [
                'code' => 'tzl',
                'name' => 'Talossan',
            ],
            [
                'code' => 'ta',
                'name' => 'Tamil',
            ],
            [
                'code' => 'ta-IN',
                'name' => 'Tamil (India)',
            ],
            [
                'code' => 'ta-LK',
                'name' => 'Tamil (Sri-Lanka)',
            ],
            [
                'code' => 'tt',
                'name' => 'Tatar',
            ],
            [
                'code' => 'te',
                'name' => 'Telugu',
            ],
            [
                'code' => 'te-IN',
                'name' => 'Telugu (India)',
            ],
            [
                'code' => 'tet',
                'name' => 'Tetum (Tetun)',
            ],
            [
                'code' => 'th',
                'name' => 'Thai',
            ],
            [
                'code' => 'th-TH',
                'name' => 'Thai (Thailand)',
            ],
            [
                'code' => 'bo',
                'name' => 'Tibetan',
            ],
            [
                'code' => 'bo-CN',
                'name' => 'Tibetan (China)',
            ],
            [
                'code' => 'ti',
                'name' => 'Tigrinya',
            ],
            [
                'code' => 'to',
                'name' => 'Tongan',
            ],
            [
                'code' => 'ts',
                'name' => 'Tsonga',
            ],
            [
                'code' => 'tn',
                'name' => 'Tswana',
            ],
            [
                'code' => 'tr',
                'name' => 'Turkish',
            ],
            [
                'code' => 'tr-TR',
                'name' => 'Turkish (Turkey)',
            ],
            [
                'code' => 'tk',
                'name' => 'Turkmen',
            ],
            [
                'code' => 'tk-TM',
                'name' => 'Turkmen (Turkmenistan)',
            ],
            [
                'code' => 'udm',
                'name' => 'Udmurt',
            ],
            [
                'code' => 'ug',
                'name' => 'Uighur',
            ],
            [
                'code' => 'ug@Arab',
                'name' => 'Uighur (Arabic)',
            ],
            [
                'code' => 'ug@Cyrl',
                'name' => 'Uighur (Cyrillic)',
            ],
            [
                'code' => 'ug@Latin',
                'name' => 'Uighur (Latin)',
            ],
            [
                'code' => 'uk',
                'name' => 'Ukrainian',
            ],
            [
                'code' => 'uk-UA',
                'name' => 'Ukrainian (Ukraine)',
            ],
            [
                'code' => 'vmf',
                'name' => 'Upper Franconian',
            ],
            [
                'code' => 'hsb',
                'name' => 'Upper Sorbian',
            ],
            [
                'code' => 'ur',
                'name' => 'Urdu',
            ],
            [
                'code' => 'ur-PK',
                'name' => 'Urdu (Pakistan)',
            ],
            [
                'code' => 'uz',
                'name' => 'Uzbek',
            ],
            [
                'code' => 'uz@Arab',
                'name' => 'Uzbek (Arabic)',
            ],
            [
                'code' => 'uz@Cyrl',
                'name' => 'Uzbek (Cyrillic)',
            ],
            [
                'code' => 'uz@Latn',
                'name' => 'Uzbek (Latin)',
            ],
            [
                'code' => 'uz-UZ',
                'name' => 'Uzbek (Uzbekistan)',
            ],
            [
                'code' => 've',
                'name' => 'Venda',
            ],
            [
                'code' => 'vec',
                'name' => 'Venetian',
            ],
            [
                'code' => 'vi',
                'name' => 'Vietnamese',
            ],
            [
                'code' => 'vi-VN',
                'name' => 'Vietnamese (Viet Nam)',
            ],
            [
                'code' => 'vls',
                'name' => 'Vlaams',
            ],
            [
                'code' => 'wa',
                'name' => 'Walloon',
            ],
            [
                'code' => 'war',
                'name' => 'Wáray-Wáray',
            ],
            [
                'code' => 'cy',
                'name' => 'Welsh',
            ],
            [
                'code' => 'cy-GB',
                'name' => 'Welsh (United Kingdom)',
            ],
            [
                'code' => 'fy',
                'name' => 'Western Frisian',
            ],
            [
                'code' => 'fy-NL',
                'name' => 'Western Frisian (Netherlands)',
            ],
            [
                'code' => 'wo',
                'name' => 'Wolof',
            ],
            [
                'code' => 'wo-SN',
                'name' => 'Wolof (Senegal)',
            ],
            [
                'code' => 'xh',
                'name' => 'Xhosa',
            ],
            [
                'code' => 'yi',
                'name' => 'Yiddish',
            ],
            [
                'code' => 'yo',
                'name' => 'Yoruba',
            ],
            [
                'code' => 'zu',
                'name' => 'Zulu',
            ],
            [
                'code' => 'zu-ZA',
                'name' => 'Zulu (South Africa)',
            ],
        ];
        return response()->json(['results' => $languages]);
    }//end index()
}//end class
