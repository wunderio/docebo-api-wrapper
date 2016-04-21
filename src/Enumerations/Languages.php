<?php

namespace Suru\Docebo\DoceboApiWrapper\Enumerations;

/**
 * ISO 639-1 language codes: https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
 * also see: http://www.science.co.il/Language/Locale-codes.asp
 * 
 */
class Languages extends Enumeration {
    
    const AR = 'arabic';
    const BS = 'bosnian';
    const BG = 'bulgarian';
    const HR = 'croatian';
    const CS = 'czech';
    const DA = 'danish';
    const NL = 'dutch';
    const EN = 'english';
    const FA = 'farsi';
    const FI = 'finnish';
    const FR = 'french';
    const DE = 'german';
    const EL = 'greek';
    const HE = 'hebrew';
    const HU = 'hungarian';
    const ID = 'indonesian';
    const IT = 'italian';
    const JA = 'japanese';
    const KO = 'korean';
    const NO = 'norwegian';
    const PL = 'polish';
    const PT = 'portuguese';
    const RO = 'romanian';
    const RU = 'russian';
    const ZH = 'chinese';
    const SL = 'slovenian';
    const ES = 'spanish';
    const SV = 'swedish';
    const TH = 'thai';
    const TR = 'turkish';
    const UK = 'ukrainian';
    
    /**
     * Docebo contains some languages with locales which will need to be 
     * converted back to their parent language in order to provide the two
     * character ISO 639-1 language code.
     * 
     * @var array
     */
    private static $locales = [
        'english_uk' => 'english',
        'portuguese-br' => 'portuguese',
        'simplified_chinese' => 'chinese',
    ];
    
    /**
     * Checks whether a value contains a hyphen or underscore seperator 
     * indicating that it is a locale or variant of a parent language.
     * 
     * @param string $value
     * @return boolean
     */
    public static function valueContainsLocale($value)
    {
        if (in_array($value, array_keys(static::$locales)))
        {
            return true;
        }
        
        return false;
    }
    
    /**
     * Strips the locale part of the language string.
     * 
     * @param string $value
     * @return string
     */
    public static function stripLocaleFromValue($value)
    {
        return static::$locales[$value];
    }
}
