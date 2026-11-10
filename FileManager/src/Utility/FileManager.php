<?php

namespace Croogo\FileManager\Utility;

use Cake\Core\Configure;
use Cake\Log\Log;

/**
 * FileManager Model
 *
 * @category FileManager.Model
 * @package  Croogo.FileManager.Model
 * @version  2.1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class FileManager
{

    protected $ext2MimeType = [
        'ai'      => 'application/postscript',
        'aif'     => 'audio/x-aiff',
        'aifc'    => 'audio/x-aiff',
        'aiff'    => 'audio/x-aiff',
        'asc'     => 'text/plain',
        'atom'    => 'application/atom+xml',
        'au'      => 'audio/basic',
        'avi'     => 'video/x-msvideo',
        'bcpio'   => 'application/x-bcpio',
        'bin'     => 'application/octet-stream',
        'bmp'     => 'image/bmp',
        'cdf'     => 'application/x-netcdf',
        'cgm'     => 'image/cgm',
        'class'   => 'application/octet-stream',
        'cpio'    => 'application/x-cpio',
        'cpt'     => 'application/mac-compactpro',
        'csh'     => 'application/x-csh',
        'css'     => 'text/css',
        'csv'     => 'text/csv',
        'dcr'     => 'application/x-director',
        'dir'     => 'application/x-director',
        'djv'     => 'image/vnd.djvu',
        'djvu'    => 'image/vnd.djvu',
        'dll'     => 'application/octet-stream',
        'dmg'     => 'application/octet-stream',
        'dms'     => 'application/octet-stream',
        'doc'     => 'application/msword',
        'dtd'     => 'application/xml-dtd',
        'dvi'     => 'application/x-dvi',
        'dxr'     => 'application/x-director',
        'eps'     => 'application/postscript',
        'etx'     => 'text/x-setext',
        'exe'     => 'application/octet-stream',
        'ez'      => 'application/andrew-inset',
        'gif'     => 'image/gif',
        'gram'    => 'application/srgs',
        'grxml'   => 'application/srgs+xml',
        'gtar'    => 'application/x-gtar',
        'hdf'     => 'application/x-hdf',
        'hqx'     => 'application/mac-binhex40',
        'htm'     => 'text/html',
        'html'    => 'text/html',
        'ice'     => 'x-conference/x-cooltalk',
        'ico'     => 'image/x-icon',
        'ics'     => 'text/calendar',
        'ief'     => 'image/ief',
        'ifb'     => 'text/calendar',
        'iges'    => 'model/iges',
        'igs'     => 'model/iges',
        'jpe'     => 'image/jpeg',
        'jpeg'    => 'image/jpeg',
        'jpg'     => 'image/jpeg',
        'js'      => 'application/x-javascript',
        'json'    => 'application/json',
        'kar'     => 'audio/midi',
        'latex'   => 'application/x-latex',
        'lha'     => 'application/octet-stream',
        'lzh'     => 'application/octet-stream',
        'm3u'     => 'audio/x-mpegurl',
        'man'     => 'application/x-troff-man',
        'mathml'  => 'application/mathml+xml',
        'me'      => 'application/x-troff-me',
        'mesh'    => 'model/mesh',
        'mid'     => 'audio/midi',
        'midi'    => 'audio/midi',
        'mif'     => 'application/vnd.mif',
        'mov'     => 'video/quicktime',
        'movie'   => 'video/x-sgi-movie',
        'mp2'     => 'audio/mpeg',
        'mp3'     => 'audio/mpeg',
        'mp4'     => 'video/mpeg',
        'mpe'     => 'video/mpeg',
        'mpeg'    => 'video/mpeg',
        'mpg'     => 'video/mpeg',
        'mpga'    => 'audio/mpeg',
        'ms'      => 'application/x-troff-ms',
        'msh'     => 'model/mesh',
        'mxu'     => 'video/vnd.mpegurl',
        'nc'      => 'application/x-netcdf',
        'oda'     => 'application/oda',
        'ogg'     => 'application/ogg',
        'pbm'     => 'image/x-portable-bitmap',
        'pdb'     => 'chemical/x-pdb',
        'pdf'     => 'application/pdf',
        'pgm'     => 'image/x-portable-graymap',
        'pgn'     => 'application/x-chess-pgn',
        'png'     => 'image/png',
        'pnm'     => 'image/x-portable-anymap',
        'ppm'     => 'image/x-portable-pixmap',
        'ppt'     => 'application/vnd.ms-powerpoint',
        'ps'      => 'application/postscript',
        'qt'      => 'video/quicktime',
        'ra'      => 'audio/x-pn-realaudio',
        'ram'     => 'audio/x-pn-realaudio',
        'ras'     => 'image/x-cmu-raster',
        'rdf'     => 'application/rdf+xml',
        'rgb'     => 'image/x-rgb',
        'rm'      => 'application/vnd.rn-realmedia',
        'roff'    => 'application/x-troff',
        'rss'     => 'application/rss+xml',
        'rtf'     => 'text/rtf',
        'rtx'     => 'text/richtext',
        'sgm'     => 'text/sgml',
        'sgml'    => 'text/sgml',
        'sh'      => 'application/x-sh',
        'shar'    => 'application/x-shar',
        'silo'    => 'model/mesh',
        'sit'     => 'application/x-stuffit',
        'skd'     => 'application/x-koan',
        'skm'     => 'application/x-koan',
        'skp'     => 'application/x-koan',
        'skt'     => 'application/x-koan',
        'smi'     => 'application/smil',
        'smil'    => 'application/smil',
        'snd'     => 'audio/basic',
        'so'      => 'application/octet-stream',
        'spl'     => 'application/x-futuresplash',
        'src'     => 'application/x-wais-source',
        'sv4cpio' => 'application/x-sv4cpio',
        'sv4crc'  => 'application/x-sv4crc',
        'svg'     => 'image/svg+xml',
        'svgz'    => 'image/svg+xml',
        'swf'     => 'application/x-shockwave-flash',
        't'       => 'application/x-troff',
        'tar'     => 'application/x-tar',
        'tcl'     => 'application/x-tcl',
        'tex'     => 'application/x-tex',
        'texi'    => 'application/x-texinfo',
        'texinfo' => 'application/x-texinfo',
        'tif'     => 'image/tiff',
        'tiff'    => 'image/tiff',
        'tr'      => 'application/x-troff',
        'tsv'     => 'text/tab-separated-values',
        'txt'     => 'text/plain',
        'ustar'   => 'application/x-ustar',
        'vcd'     => 'application/x-cdlink',
        'vrml'    => 'model/vrml',
        'vxml'    => 'application/voicexml+xml',
        'wav'     => 'audio/x-wav',
        'wbmp'    => 'image/vnd.wap.wbmp',
        'wbxml'   => 'application/vnd.wap.wbxml',
        'webm'    => 'video/webm',
        'wml'     => 'text/vnd.wap.wml',
        'wmlc'    => 'application/vnd.wap.wmlc',
        'wmls'    => 'text/vnd.wap.wmlscript',
        'wmlsc'   => 'application/vnd.wap.wmlscriptc',
        'wrl'     => 'model/vrml',
        'xbm'     => 'image/x-xbitmap',
        'xht'     => 'application/xhtml+xml',
        'xhtml'   => 'application/xhtml+xml',
        'xls'     => 'application/vnd.ms-excel',
        'xml'     => 'application/xml',
        'xpm'     => 'image/x-xpixmap',
        'xsl'     => 'application/xml',
        'xslt'    => 'application/xslt+xml',
        'xul'     => 'application/vnd.mozilla.xul+xml',
        'xwd'     => 'image/x-xwindowdump',
        'xyz'     => 'chemical/x-xyz',
        'zip'     => 'application/zip'
    ];

    /**
     * Checks wether given $path is editable
     *
     * A file is editable when it resides under directories registered in
     * FileManager.editablePaths
     *
     * @param string $path Path to check
     * @return bool true if file is editable
     */
    public function isEditable($path)
    {
        return $this->_isWithinAnyPath('FileManager.editablePaths', $path, true);
    }

    /**
     * Checks wether given $path is deletable
     *
     * A file is deleteable when it resides under directories registered in
     * FileManager.deletablePaths
     *
     * @param string $path Path to check
     * @return bool true when file is deletable
     */
    public function isDeletable($path)
    {
        return $this->_isWithinAnyPath('FileManager.deletablePaths', $path, false);
    }

    /**
     * Checks that $path is an existing directory new entries may be written into
     *
     * Unlike isDeletable() the configured root itself qualifies, so uploads and
     * new directories can still land directly in it.
     *
     * @param string $path Directory to check
     * @return bool True when $path is a directory under FileManager.deletablePaths
     */
    public function isWritableDirectory($path)
    {
        return $this->_isWithinAnyPath('FileManager.deletablePaths', $path, true) && is_dir($path);
    }

    /**
     * Checks whether an entry called $name may be written through the file manager
     *
     * The managed directories are served by the web server, so a name it could execute
     * or read as configuration (`x.php`, `x.php.jpg`, `.htaccess`, `.user.ini`) would turn
     * a file write into code execution. Every extension segment is checked, not just the
     * last one, because a multi-extension handler mapping runs `x.php.jpg` as PHP.
     *
     * @param string $name Bare file or directory name
     * @return bool True when the name is safe to create, write or rename to
     */
    public function isAllowedName($name)
    {
        if (!is_string($name) || $name === '' || $name[0] === '.') {
            return false;
        }
        foreach (array_slice(explode('.', $name), 1) as $extension) {
            if (preg_match('/^(php\d*|pht|phtml|phps|phar)$/i', trim($extension))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Joins a user-supplied entry name onto $directory
     *
     * The name must be a bare file/directory name that isAllowedName() accepts: a
     * separator or `..` in it would leave the directory the caller has just checked.
     * An existing symlink is refused too, dangling ones included, because writes follow
     * it out of the directory.
     *
     * @param string $directory Directory the entry belongs to
     * @param string $name Entry name as submitted by the user
     * @return string|null Joined path, or null when the entry cannot be written safely
     */
    public function childPath($directory, $name)
    {
        if (!is_string($directory) || !$this->isAllowedName($name) || strpbrk($name, "/\\\0") !== false) {
            return null;
        }

        $path = rtrim($directory, '/\\') . DIRECTORY_SEPARATOR . $name;

        return is_link($path) ? null : $path;
    }

    /**
     * Rename $oldPath to $newPath
     *
     * @param string $oldPath Old filename/directory
     * @param string $newPath New filename/directory
     * @return bool True if rename was successful
     */
    public function rename($oldPath, $newPath)
    {
        // Cake 5: Folder usunięty — rename() obsługuje też katalogi.
        return rename($oldPath, $newPath);
    }

    /**
     * Checks $path against every directory listed under the Configure key $key
     *
     * @param string $key Configure key holding the list of allowed directories
     * @param string $path Path to check
     * @param bool $allowRoot Whether a listed directory itself qualifies
     * @return bool True when $path resides under one of the listed directories
     */
    protected function _isWithinAnyPath($key, $path, $allowRoot)
    {
        foreach ((array)Configure::read($key) as $referencePath) {
            if (!$this->_isWithinPath($referencePath, $path)) {
                continue;
            }
            if ($allowRoot || realpath($referencePath) !== realpath($path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks that $pathToCheck resides under $referencePath (or is that directory)
     *
     * Fails closed: a path that does not resolve never matches, and the prefix only
     * matches on a separator boundary, so `assets` does not cover `assets-old`.
     *
     * @param string $referencePath Reference path
     * @param string $pathToCheck Path to check
     * @return bool True if $pathToCheck resides under $referencePath
     */
    protected function _isWithinPath($referencePath, $pathToCheck)
    {
        // realpath('') resolves to the working directory, so an empty entry must not reach it.
        if (!is_string($referencePath) || !is_string($pathToCheck) || $referencePath === '' || $pathToCheck === '') {
            return false;
        }
        $reference = realpath($referencePath);
        $path = realpath($pathToCheck);
        if ($reference === false || $path === false) {
            return false;
        }

        return $path === $reference
            || str_starts_with($path, rtrim($reference, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR);
    }

    public function filename2mime($filename) {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (isset($this->ext2MimeType[$ext])) {
            return $this->ext2MimeType[$ext];
        }
        Log::warning('Cannot get mimeType for ' . $filename);
        return null;
    }

}
