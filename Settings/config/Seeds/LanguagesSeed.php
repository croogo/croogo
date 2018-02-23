<?php

use Phinx\Seed\AbstractSeed;
use Cake\I18n\I18n;

class LanguagesSeed extends AbstractSeed
{

    public $records = [
        [
            'id' => '1',
            'title' => 'English (United States)',
            'native' => 'English',
            'alias' => 'en',
            'locale' => 'en_US',
            'status' => '1',
            'weight' => '1',
            'updated' => '2009-11-02 21:37:38',
            'created' => '2009-11-02 20:52:00'
        ],
        [
            'id' => '2',
            'title' => 'Indonesian',
            'native' => 'Bahasa Indonesia',
            'alias' => 'id',
            'locale' => 'id_ID',
            'status' => '1',
            'weight' => '2',
            'updated' => '2017-03-29 00:00:00',
            'created' => '2017-03-29 00:00:00',
        ],
    ];

    public function run()
    {
        $Table = $this->table('languages');

        $locales = ResourceBundle::getLocales('');
        $weight = 1;
        $records = [];
        $now = new DateTime();
        I18n::setLocale('en_US');

        foreach ($locales as $locale) {
            I18n::setLocale('en_US');
            $parsed = Locale::parseLocale($locale);
            $status = in_array($locale, [
                'ar', 'bg', 'cs', 'de', 'el', 'en', 'es', 'fa', 'fr', 'hu',
                'id', 'it', 'ja', 'nl', 'pl', 'pt', 'pt-BR', 'ru', 'sk', 'zh',
            ]);
            $data = [
                'title' => Locale::getDisplayName($locale),
                'alias' => $parsed['language'],
                'locale' => $locale,
                'status' => intval($status),
                'weight' => $weight++,
                'created' => $now->format('Y-m-d H:i:s'),
                'updated' => $now->format('Y-m-d H:i:s'),
            ];
            I18n::setLocale($locale);
            $data['native'] = Locale::getDisplayRegion($locale);
            $records[] = $data;
        }

        $Table->insert($records)->save();
    }

}
