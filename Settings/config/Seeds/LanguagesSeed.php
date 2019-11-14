<?php

use Cake\I18n\I18n;
use Phinx\Seed\AbstractSeed;

class LanguagesSeed extends AbstractSeed
{

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
                'created_by' => 1,
            ];
            I18n::setLocale($locale);
            $data['native'] = Locale::getDisplayRegion($locale);
            $records[] = $data;
        }

        $Table->insert($records)->save();
    }
}
