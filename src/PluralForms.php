<?php

namespace Elixir\I18N;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class PluralForms 
{
    /**
     * @var array 
     */
    public static $override = [];
    
    /**
     * @see http://localization-guide.readthedocs.org/en/latest/l10n/pluralforms.html
     * @param string $locale
     * @return array|false
     */
    public static function get($locale)
    {
        // First pass
        if (isset(static::$override[$locale]))
        {
            return static::$override[$locale];
        }
        
        if ($locale === 'be_FR')
        {
            $locale = 'fr';
        }
        else if ($locale === 'be_NL')
        {
            $locale = 'nl';
        }
        
        if (!in_array($locale, ['es_AR', 'pt_BR']) && strlen($locale) > 3) 
        {
            $locale = substr($locale, 0, -strlen(strrchr($locale, '_')));
        }
        
        // Second pass
        if (isset(static::$override[$locale]))
        {
            return static::$override[$locale];
        }
        
        switch ($locale)
        {
            // A
            case 'ach':
            case 'ak':
            case 'am':
            case 'arn':
                return 
                [
                    'rule' => 'nplurals=2; plural=(n > 1);',
                    'nplurals' => 2,
                    'plural' => function($n)
                    {
                        return $n > 1;
                    }
                ];
            case 'af':
            case 'an':
            case 'anp':
            case 'as':
            case 'ast':
            case 'az':
                return 
                [
                    'rule' => 'nplurals=2; plural=(n != 1);',
                    'nplurals' => 2,
                    'plural' => function($n)
                    {
                        return $n != 1;
                    }
                ];
            case 'ar':
                return 
                [
                    'rule' => 'nplurals=6; plural=(n==0 ? 0 : n==1 ? 1 : n==2 ? 2 : n%100>=3 && n%100<=10 ? 3 : n%100>=11 ? 4 : 5);',
                    'nplurals' => 6,
                    'plural' => function($n)
                    {
                        return ($n == 0 ? 0 : ($n == 1 ? 1 : ($n == 2 ? 2 : ($n % 100 >= 3 && $n % 100 <= 10 ? 3 : ($n % 100 >= 11 ? 4 : 5)))));
                    }
                ];
            case 'ay':
                return 
                [
                    'rule' => 'nplurals=1; plural=0;',
                    'nplurals' => 1,
                    'plural' => function($n)
                    {
                        return 0;
                    }
                ];
            // B
            case 'be':
            case 'bs':
                return 
                [
                    'rule' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);',
                    'nplurals' => 3,
                    'plural' => function($n)
                    {
                        return ($n % 10 == 1 && $n % 100 != 11 ? 0 : ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20) ? 1 : 2));
                    }
                ];
            case 'bg':
            case 'bn':
            case 'brx':
                return 
                [
                    'rule' => 'nplurals=2; plural=(n != 1);',
                    'nplurals' => 2,
                    'plural' => function($n)
                    {
                        return $n != 1;
                    }
                ];
            case 'bo':
                return 
                [
                    'rule' => 'nplurals=1; plural=0;',
                    'nplurals' => 1,
                    'plural' => function($n)
                    {
                        return 0;
                    }
                ];
            case 'br':
                return 
                [
                    'rule' => 'nplurals=2; plural=(n > 1);',
                    'nplurals' => 2,
                    'plural' => function($n)
                    {
                        return $n > 1;
                    }
                ];
            // C
            case 'ca':
                return 
                [
                    'rule' => 'nplurals=2; plural=(n != 1);',
                    'nplurals' => 2,
                    'plural' => function($n)
                    {
                        return $n != 1;
                    }
                ];
            case 'cgg':
                return 
                [
                    'rule' => 'nplurals=1; plural=0;',
                    'nplurals' => 1,
                    'plural' => function($n)
                    {
                        return 0;
                    }
                ];
            case 'cs':
                return 
                [
                    'rule' => 'nplurals=3; plural=(n==1) ? 0 : (n>=2 && n<=4) ? 1 : 2;',
                    'nplurals' => 3,
                    'plural' => function($n)
                    {
                        return ($n == 1) ? 0 : (($n >= 2 && $n <= 4) ? 1 : 2);
                    }
                ];
            case 'csb':
                return 
                [
                    'rule' => 'nplurals=3; plural=(n==1) ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2;',
                    'nplurals' => 3,
                    'plural' => function($n)
                    {
                        return ($n == 1) ? 0 : ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20) ? 1 : 2);
                    }
                ];
            case 'cy':
                return 
                [
                    'rule' => 'nplurals=4; plural=(n==1) ? 0 : (n==2) ? 1 : (n != 8 && n != 11) ? 2 : 3;',
                    'nplurals' => 4,
                    'plural' => function($n)
                    {
                        return ($n == 1) ? 0 : (($n == 2) ? 1 : (($n != 8 && $n != 11) ? 2 : 3));
                    }
                ];
            // D
            case 'da':
            case 'de':
            case 'doi':
                return 
                [
                    'rule' => 'nplurals=2; plural=(n != 1);',
                    'nplurals' => 2,
                    'plural' => function($n)
                    {
                        return $n != 1;
                    }
                ];
            case 'dz':
                return 
                [
                    'rule' => 'nplurals=1; plural=0;',
                    'nplurals' => 1,
                    'plural' => function($n)
                    {
                        return 0;
                    }
                ];
            // E
            case 'el':
            case 'en':
            case 'eo':
            case 'es':
            case 'es_AR':
            case 'et':
            case 'eu':
                return 
                [
                    'rule' => 'nplurals=2; plural=(n != 1);',
                    'nplurals' => 2,
                    'plural' => function($n)
                    {
                        return $n != 1;
                    }
                ];
            // F
            case 'fa':
                return 
                [
                    'rule' => 'nplurals=1; plural=0;',
                    'nplurals' => 1,
                    'plural' => function($n)
                    {
                        return 0;
                    }
                ];
            case 'ff':
            case 'fi':
            case 'fo':
            case 'fur':
            case 'fy':
                return 
                [
                    'rule' => 'nplurals=2; plural=(n != 1);',
                    'nplurals' => 2,
                    'plural' => function($n)
                    {
                        return $n != 1;
                    }
                ];
            case 'fil':
            case 'fr':
                return 
                [
                    'rule' => 'nplurals=2; plural=(n > 1);',
                    'nplurals' => 2,
                    'plural' => function($n)
                    {
                        return $n > 1;
                    }
                ];
            // G
            case 'ga':
                return 
                [
                    'rule' => 'nplurals=5; plural=n==1 ? 0 : n==2 ? 1 : (n>2 && n<7) ? 2 :(n>6 && n<11) ? 3 : 4;',
                    'nplurals' => 5,
                    'plural' => function($n)
                    {
                        return $n == 1 ? 0 : ($n == 2 ? 1 : (($n > 2 && $n < 7) ? 2 : (($n > 6 && $n < 11) ? 3 : 4)));
                    }
                ];
            case 'gd':
                return 
                [
                    'rule' => 'nplurals=4; plural=(n==1 || n==11) ? 0 : (n==2 || n==12) ? 1 : (n > 2 && n < 20) ? 2 : 3;',
                    'nplurals' => 4,
                    'plural' => function($n)
                    {
                        return ($n == 1 || $n == 11) ? 0 : (($n == 2 || $n == 12) ? 1 : (($n > 2 && $n < 20) ? 2 : 3));
                    }
                ];
            case 'gl':
            case 'gu':
                return 
                [
                    'rule' => 'nplurals=2; plural=(n != 1);',
                    'nplurals' => 2,
                    'plural' => function($n)
                    {
                        return $n != 1;
                    }
                ];
            case 'gun':
                return 
                [
                    'rule' => 'nplurals=2; plural=(n > 1);',
                    'nplurals' => 2,
                    'plural' => function($n)
                    {
                        return $n > 1;
                    }
                ];
            // H
            case 'ha':
            case 'he':
            case 'hi':
            case 'hne':
            case 'hu':
            case 'hy':
                return 
                [
                    'rule' => 'nplurals=2; plural=(n != 1);',
                    'nplurals' => 2,
                    'plural' => function($n)
                    {
                        return $n != 1;
                    }
                ];
            case 'hr':
                return 
                [
                    'rule' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);',
                    'nplurals' => 3,
                    'plural' => function($n)
                    {
                        return ($n % 10 == 1 && $n % 100 != 11 ? 0 : ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20) ? 1 : 2));
                    }
                ];
            // I
            case 'ia':
            case 'it':
                return 
                [
                    'rule' => 'nplurals=2; plural=(n != 1);',
                    'nplurals' => 2,
                    'plural' => function($n)
                    {
                        return $n != 1;
                    }
                ];
            case 'id':
                return 
                [
                    'rule' => 'nplurals=1; plural=0;',
                    'nplurals' => 1,
                    'plural' => function($n)
                    {
                        return 0;
                    }
                ];
            case 'is':
                return 
                [
                    'rule' => 'nplurals=2; plural=(n%10!=1 || n%100==11);',
                    'nplurals' => 2,
                    'plural' => function($n)
                    {
                        return ($n % 10 != 1 || $n % 100 == 11);
                    }
                ];
            // J
            case 'ja':
            case 'jbo':
                return 
                [
                    'rule' => 'nplurals=1; plural=0;',
                    'nplurals' => 1,
                    'plural' => function($n)
                    {
                        return 0;
                    }
                ];
            case 'jv':
                return 
                [
                    'rule' => 'nplurals=2; plural=(n != 0);',
                    'nplurals' => 2,
                    'plural' => function($n)
                    {
                        return $n != 0;
                    }
                ];
            // K
            case 'ka':
            case 'kk':
            case 'km':
            case 'ko':
            case 'ky':
                return 
                [
                    'rule' => 'nplurals=1; plural=0;',
                    'nplurals' => 1,
                    'plural' => function($n)
                    {
                        return 0;
                    }
                ];
            case 'kl':
            case 'kn':
            case 'ku':
                return 
                [
                    'rule' => 'nplurals=2; plural=(n != 1);',
                    'nplurals' => 2,
                    'plural' => function($n)
                    {
                        return $n != 1;
                    }
                ];
            case 'kw':
                return 
                [
                    'rule' => 'nplurals=4; plural=(n==1) ? 0 : (n==2) ? 1 : (n == 3) ? 2 : 3;',
                    'nplurals' => 4,
                    'plural' => function($n)
                    {
                        return ($n == 1) ? 0 : (($n == 2) ? 1 : (($n == 3) ? 2 : 3));
                    }
                ];
            // L
            case 'lb':
                return 
                [
                    'rule' => 'nplurals=2; plural=(n != 1);',
                    'nplurals' => 2,
                    'plural' => function($n)
                    {
                        return $n != 1;
                    }
                ]; 
            case 'ln':
                return 
                [
                    'rule' => 'nplurals=2; plural=(n > 1);',
                    'nplurals' => 2,
                    'plural' => function($n)
                    {
                        return $n > 1;
                    }
                ];
            case 'lo':
                return 
                [
                    'rule' => 'nplurals=1; plural=0;',
                    'nplurals' => 1,
                    'plural' => function($n)
                    {
                        return 0;
                    }
                ];
            case 'lt':
                return 
                [
                    'rule' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && (n%100<10 || n%100>=20) ? 1 : 2);',
                    'nplurals' => 3,
                    'plural' => function($n)
                    {
                        return ($n % 10 == 1 && $n % 100 != 11 ? 0 : ($n % 10 >= 2 && ($n % 100 < 10 || $n % 100 >= 20) ? 1 : 2));
                    }
                ];
            case 'lv':
                return 
                [
                    'rule' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n != 0 ? 1 : 2);',
                    'nplurals' => 3,
                    'plural' => function($n)
                    {
                        return ($n % 10 == 1 && $n % 100 != 11 ? 0 : ($n != 0 ? 1 : 2));
                    }
                ];
            // M
            case 'mai':
            case 'ml':
            case 'mn':
            case 'mni':
            case 'mr':
                return 
                [
                    'rule' => 'nplurals=2; plural=(n != 1);',
                    'nplurals' => 2,
                    'plural' => function($n)
                    {
                        return $n != 1;
                    }
                ]; 
            case 'mfe':
            case 'mg':
            case 'mi':
                return 
                [
                    'rule' => 'nplurals=2; plural=(n > 1);',
                    'nplurals' => 2,
                    'plural' => function($n)
                    {
                        return $n > 1;
                    }
                ];
            case 'mk':
                return 
                [
                    'rule' => 'nplurals=2; plural= n==1 || n%10==1 ? 0 : 1;',
                    'nplurals' => 2,
                    'plural' => function($n)
                    {
                        return $n == 1 || $n % 10 == 1 ? 0 : 1;
                    }
                ];
            case 'mnk':
                return 
                [
                    'rule' => 'nplurals=3; plural=(n==0 ? 0 : n==1 ? 1 : 2);',
                    'nplurals' => 3,
                    'plural' => function($n)
                    {
                        return ($n == 0 ? 0 : ($n == 1 ? 1 : 2));
                    }
                ];
            case 'ms':
            case 'my':
                return 
                [
                    'rule' => 'nplurals=1; plural=0;',
                    'nplurals' => 1,
                    'plural' => function($n)
                    {
                        return 0;
                    }
                ];
            case 'mt':
                return 
                [
                    'rule' => 'nplurals=4; plural=(n==1 ? 0 : n==0 || ( n%100>1 && n%100<11) ? 1 : (n%100>10 && n%100<20 ) ? 2 : 3);',
                    'nplurals' => 4,
                    'plural' => function($n)
                    {
                        return ($n == 1 ? 0 : ($n == 0 || ( $n % 100 > 1 && $n % 100 < 11) ? 1 : (($n % 100 > 10 && $n % 100 < 20 ) ? 2 : 3)));
                    }
                ];
            // N
            case 'nah':
            case 'nap':
            case 'nb':
            case 'ne':
            case 'nl':
            case 'nn':
            case 'no':
            case 'nso':
                return 
                [
                    'rule' => 'nplurals=2; plural=(n != 1);',
                    'nplurals' => 2,
                    'plural' => function($n)
                    {
                        return $n != 1;
                    }
                ]; 
            // O
            case 'oc':
                return 
                [
                    'rule' => 'nplurals=2; plural=(n > 1);',
                    'nplurals' => 2,
                    'plural' => function($n)
                    {
                        return $n > 1;
                    }
                ];  
            case 'or':
                return 
                [
                    'rule' => 'nplurals=2; plural=(n != 1);',
                    'nplurals' => 2,
                    'plural' => function($n)
                    {
                        return $n != 1;
                    }
                ];  
            // P
            case 'pa':
            case 'pap':
            case 'pms':
            case 'ps':
            case 'pt':
                return 
                [
                    'rule' => 'nplurals=2; plural=(n != 1);',
                    'nplurals' => 2,
                    'plural' => function($n)
                    {
                        return $n != 1;
                    }
                ];
            case 'pl':
                return 
                [
                    'rule' => 'nplurals=3; plural=(n==1 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);',
                    'nplurals' => 3,
                    'plural' => function($n)
                    {
                        return ($n == 1 ? 0 : ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20) ? 1 : 2));
                    }
                ];
            case 'pt_BR':
                return 
                [
                    'rule' => 'nplurals=2; plural=(n > 1);',
                    'nplurals' => 2,
                    'plural' => function($n)
                    {
                        return $n > 1;
                    }
                ];
            // R
            case 'rm':
            case 'rw':
                return 
                [
                    'rule' => 'nplurals=2; plural=(n != 1);',
                    'nplurals' => 2,
                    'plural' => function($n)
                    {
                        return $n != 1;
                    }
                ];
            case 'ro':
                return 
                [
                    'rule' => 'nplurals=3; plural=(n==1 ? 0 : (n==0 || (n%100 > 0 && n%100 < 20)) ? 1 : 2);',
                    'nplurals' => 3,
                    'plural' => function($n)
                    {
                        return ($n == 1 ? 0 : (($n == 0 || ($n % 100 > 0 && $n % 100 < 20)) ? 1 : 2));
                    }
                ];
            case 'ru':
                return 
                [
                    'rule' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);',
                    'nplurals' => 3,
                    'plural' => function($n)
                    {
                        return ($n % 10 == 1 && $n % 100 != 11 ? 0 : ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20) ? 1 : 2));
                    }
                ];
            // S
            case 'sah':
            case 'su':
                return 
                [
                    'rule' => 'nplurals=1; plural=0;',
                    'nplurals' => 1,
                    'plural' => function($n)
                    {
                        return 0;
                    }
                ];
            case 'sat':
            case 'sco':
            case 'sd':
            case 'se':
            case 'si':
            case 'so':
            case 'son':
            case 'sq':
            case 'sv':
            case 'sw':
                return 
                [
                    'rule' => 'nplurals=2; plural=(n != 1);',
                    'nplurals' => 2,
                    'plural' => function($n)
                    {
                        return $n != 1;
                    }
                ];
            case 'sk':
                return 
                [
                    'rule' => 'nplurals=3; plural=(n==1) ? 0 : (n>=2 && n<=4) ? 1 : 2;',
                    'nplurals' => 3,
                    'plural' => function($n)
                    {
                        return ($n == 1) ? 0 : (($n >= 2 && $n <= 4) ? 1 : 2);
                    }
                ];
            case 'sl':
                return 
                [
                    'rule' => 'nplurals=4; plural=(n%100==1 ? 1 : n%100==2 ? 2 : n%100==3 || n%100==4 ? 3 : 0);',
                    'nplurals' => 4,
                    'plural' => function($n)
                    {
                        return ($n % 100 == 1 ? 1 : ($n % 100 == 2 ? 2 : ($n % 100 == 3 || $n % 100 == 4 ? 3 : 0)));
                    }
                ];
            case 'sr':
                return 
                [
                    'rule' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);',
                    'nplurals' => 3,
                    'plural' => function($n)
                    {
                        return ($n % 10 == 1 && $n % 100 != 11 ? 0 : ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20) ? 1 : 2));
                    }
                ];
            // T
            case 'ta':
            case 'te':
            case 'tk':
                return 
                [
                    'rule' => 'nplurals=2; plural=(n != 1);',
                    'nplurals' => 2,
                    'plural' => function($n)
                    {
                        return $n != 1;
                    }
                ];
            case 'tg':
            case 'ti':
            case 'tr':
                return 
                [
                    'rule' => 'nplurals=2; plural=(n > 1);',
                    'nplurals' => 2,
                    'plural' => function($n)
                    {
                        return $n > 1;
                    }
                ];
            case 'th':
            case 'tt':
                return 
                [
                    'rule' => 'nplurals=1; plural=0;',
                    'nplurals' => 1,
                    'plural' => function($n)
                    {
                        return 0;
                    }
                ];
            // U
            case 'ug':
                return 
                [
                    'rule' => 'nplurals=1; plural=0;',
                    'nplurals' => 1,
                    'plural' => function($n)
                    {
                        return 0;
                    }
                ];
            case 'uk':
                return 
                [
                    'rule' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);',
                    'nplurals' => 3,
                    'plural' => function($n)
                    {
                        return ($n % 10 == 1 && $n % 100 != 11 ? 0 : ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20) ? 1 : 2));
                    }
                ];
            case 'ur':
                return 
                [
                    'rule' => 'nplurals=2; plural=(n != 1);',
                    'nplurals' => 2,
                    'plural' => function($n)
                    {
                        return $n != 1;
                    }
                ];
            case 'uz':
                return 
                [
                    'rule' => 'nplurals=2; plural=(n > 1);',
                    'nplurals' => 2,
                    'plural' => function($n)
                    {
                        return $n > 1;
                    }
                ];
            // V
            case 'vi':
                return 
                [
                    'rule' => 'nplurals=1; plural=0;',
                    'nplurals' => 1,
                    'plural' => function($n)
                    {
                        return 0;
                    }
                ];
            // W
            case 'wa':
                return 
                [
                    'rule' => 'nplurals=2; plural=(n > 1);',
                    'nplurals' => 2,
                    'plural' => function($n)
                    {
                        return $n > 1;
                    }
                ];
            case 'wo':
                return 
                [
                    'rule' => 'nplurals=1; plural=0;',
                    'nplurals' => 1,
                    'plural' => function($n)
                    {
                        return 0;
                    }
                ];
            // Y
            case 'yo':
                return 
                [
                    'rule' => 'nplurals=2; plural=(n != 1);',
                    'nplurals' => 2,
                    'plural' => function($n)
                    {
                        return $n != 1;
                    }
                ];
            // Z
            case 'zh':
                return 
                [
                    'rule' => 'nplurals=1; plural=0;',
                    'nplurals' => 1,
                    'plural' => function($n)
                    {
                        return 0;
                    }
                ];
            default:
                return false;
        }
    }
    
    /**
     * @param string $pluralForm
     * @return boolean|array
     */
    public static function parse($pluralForm)
    {
        if (!preg_match('/^\s*nplurals\s*=\s*(\d+)\s*;\s*plural\s*=\s*([0-9n\s' . preg_quote('()+-/*%><=!&|?:', '/') . ']+)\s*;$/i', 
                        $pluralForm, 
                        $matches))
        {
            return false;
        }
        
        return [
            'rule' => trim($pluralForm),
            'nplurals' => $matches[1],
            'plural' => function($n) use ($matches)
            {
                return eval('return ' . str_replace('n', $n, $matches[2]) . ';');
            }
        ];
    }
    
    /**
     * @param integer $number
     * @param string|callable $plural
     * @return integer
     */
    public static function evaluate($number, $plural)
    {
        if (is_callable($plural))
        {
            return (int)call_user_func_array($plural, [$number]);
        }
        else if (false !== strpos($plural, 'plural'))
        {
            $parsed = static::parse($plural);
            
            if (false !== $parsed)
            {
                return (int)call_user_func_array($parsed['plural'], [$number]);
            }
        }
        
        return 0;
    }
}
