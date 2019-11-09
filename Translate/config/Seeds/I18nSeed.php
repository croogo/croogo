<?php

use Migrations\AbstractSeed;

/**
 * I18n seed.
 */
class I18nSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'id' => '1',
                'locale' => 'id',
                'model' => 'Nodes',
                'foreign_key' => '1',
                'field' => 'title',
                'content' => 'Halo Dunia',
            ],
            [
                'id' => '2',
                'locale' => 'id',
                'model' => 'Nodes',
                'foreign_key' => '1',
                'field' => 'excerpt',
                'content' => '',
            ],
            [
                'id' => '3',
                'locale' => 'id',
                'model' => 'Nodes',
                'foreign_key' => '1',
                'field' => 'body',
                'content' => '<p>Selamat Datang di Croogo. Ini adalah tulisan pertama anda. Anda dapat mengedit atau menghapusnya dari panel admin.</p>',
            ],
            [
                'id' => '4',
                'locale' => 'id',
                'model' => 'Nodes',
                'foreign_key' => '2',
                'field' => 'title',
                'content' => 'Tentang',
            ],
            [
                'id' => '5',
                'locale' => 'id',
                'model' => 'Nodes',
                'foreign_key' => '2',
                'field' => 'excerpt',
                'content' => '',
            ],
            [
                'id' => '6',
                'locale' => 'id',
                'model' => 'Nodes',
                'foreign_key' => '2',
                'field' => 'body',
                'content' => '<p>Ini merupakan contoh halaman Croogo, Anda dapat menyunting ini untuk menaruh informasi tentang diri Anda atau situs Anda.</p>',
            ],
            [
                'id' => '7',
                'locale' => 'id',
                'model' => 'Links',
                'foreign_key' => '8',
                'field' => 'title',
                'content' => 'Tentang',
            ],
            [
                'id' => '8',
                'locale' => 'id',
                'model' => 'Links',
                'foreign_key' => '8',
                'field' => 'description',
                'content' => '',
            ],
            [
                'id' => '9',
                'locale' => 'id',
                'model' => 'Links',
                'foreign_key' => '7',
                'field' => 'title',
                'content' => 'Depan',
            ],
            [
                'id' => '10',
                'locale' => 'id',
                'model' => 'Links',
                'foreign_key' => '7',
                'field' => 'description',
                'content' => '',
            ],
            [
                'id' => '11',
                'locale' => 'id',
                'model' => 'Links',
                'foreign_key' => '15',
                'field' => 'title',
                'content' => 'Kontak',
            ],
            [
                'id' => '12',
                'locale' => 'id',
                'model' => 'Links',
                'foreign_key' => '15',
                'field' => 'description',
                'content' => '',
            ],
            [
                'id' => '13',
                'locale' => 'id',
                'model' => 'Blocks',
                'foreign_key' => '7',
                'field' => 'title',
                'content' => 'Kategori',
            ],
            [
                'id' => '14',
                'locale' => 'id',
                'model' => 'Blocks',
                'foreign_key' => '7',
                'field' => 'body',
                'content' => '[vocabulary:categories type="blog"]',
            ],
            [
                'id' => '15',
                'locale' => 'id',
                'model' => 'Blocks',
                'foreign_key' => '3',
                'field' => 'title',
                'content' => 'Tentang',
            ],
            [
                'id' => '16',
                'locale' => 'id',
                'model' => 'Blocks',
                'foreign_key' => '3',
                'field' => 'body',
                'content' => 'Ini adalah isi dari blok Anda. Dapat dimodifikasi di panel admin.',
            ],
            [
                'id' => '17',
                'locale' => 'id',
                'model' => 'Blocks',
                'foreign_key' => '9',
                'field' => 'title',
                'content' => 'Tulisan Baru',
            ],
        ];

        $table = $this->table('i18n');
        $table->insert($data)->save();
    }
}
