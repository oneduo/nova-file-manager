<?php

/** @noinspection ALL */

declare(strict_types=1);

namespace Oneduo\NovaFileManager\Filesystem\Support;

use getID3 as BaseGetID3;
use getid3_exception;
use getid3_lib;

class GetID3 extends BaseGetID3
{
    /**
     * @param  string  $filename
     * @param  int  $filesize
     * @param  resource  $fp
     * @return bool
     *
     * @throws getid3_exception
     */
    public function openfile($filename, $filesize = null, $fp = null): bool
    {
        try {
            if (!empty($this->startup_error)) {
                throw new getid3_exception($this->startup_error);
            }
            if (!empty($this->startup_warning)) {
                foreach (explode("\n", $this->startup_warning) as $startup_warning) {
                    $this->warning($startup_warning);
                }
            }

            // init result array and set parameters
            $this->filename = $filename;
            $this->info = [];
            $this->info['GETID3_VERSION'] = $this->version();
            $this->info['php_memory_limit'] = (($this->memory_limit > 0) ? $this->memory_limit : false);

            // remote files not supported
            if (str_starts_with($filename, 'http://')) {
                throw new getid3_exception('Remote files are not supported - please copy the file locally first');
            }

            $filename = str_replace('/', DIRECTORY_SEPARATOR, $filename);
            //$filename = preg_replace('#(?<!gs:)('.preg_quote(DIRECTORY_SEPARATOR).'{2,})#', DIRECTORY_SEPARATOR, $filename);

            // open local file
            //if (is_readable($filename) && is_file($filename) && ($this->fp = fopen($filename, 'rb'))) { // see https://www.getid3.org/phpBB3/viewtopic.php?t=1720
            if (($fp != null) && ((get_resource_type($fp) == 'file') || (get_resource_type($fp) == 'stream'))) {
                $this->fp = $fp;
            } elseif ((is_readable($filename) || file_exists($filename)) && is_file($filename) && ($this->fp = fopen(
                $filename,
                'rb'
            ))) {
                // great
            } else {
                $errormessagelist = [];
                if (!is_readable($filename)) {
                    $errormessagelist[] = '!is_readable';
                }
                if (!is_file($filename)) {
                    $errormessagelist[] = '!is_file';
                }
                if (!file_exists($filename)) {
                    $errormessagelist[] = '!file_exists';
                }
                if (empty($errormessagelist)) {
                    $errormessagelist[] = 'fopen failed';
                }

                throw new getid3_exception('Could not open "' . $filename . '" (' . implode('; ', $errormessagelist) . ')');
            }

            $this->info['filesize'] = (!is_null($filesize) ? $filesize : filesize($filename));
            // set redundant parameters - might be needed in some include file
            // filenames / filepaths in getID3 are always expressed with forward slashes (unix-style) for both Windows and other to try and minimize confusion
            $filename = str_replace('\\', '/', $filename);
            $realpath = is_bool(realpath(dirname($filename))) ? dirname($filename) : realpath(dirname($filename));
            $this->info['filepath'] = str_replace('\\', '/', $realpath);
            $this->info['filename'] = getid3_lib::mb_basename($filename);
            $this->info['filenamepath'] = $this->info['filepath'] . '/' . $this->info['filename'];

            // set more parameters
            $this->info['avdataoffset'] = 0;
            $this->info['avdataend'] = $this->info['filesize'];
            $this->info['fileformat'] = '';                // filled in later
            $this->info['audio']['dataformat'] = '';                // filled in later, unset if not used
            $this->info['video']['dataformat'] = '';                // filled in later, unset if not used
            $this->info['tags'] = [];           // filled in later, unset if not used
            $this->info['error'] = [];           // filled in later, unset if not used
            $this->info['warning'] = [];           // filled in later, unset if not used
            $this->info['comments'] = [];           // filled in later, unset if not used
            $this->info['encoding'] = $this->encoding;   // required by id3v2 and iso modules - can be unset at the end if desired

            // option_max_2gb_check
            if ($this->option_max_2gb_check) {
                // PHP (32-bit all, and 64-bit Windows) doesn't support integers larger than 2^31 (~2GB)
                // filesize() simply returns (filesize % (pow(2, 32)), no matter the actual filesize
                // ftell() returns 0 if seeking to the end is beyond the range of unsigned integer
                $fseek = fseek($this->fp, 0, SEEK_END);
                if (($fseek < 0) || (($this->info['filesize'] != 0) && (ftell($this->fp) == 0)) ||
                    ($this->info['filesize'] < 0) ||
                    (ftell($this->fp) < 0)
                ) {
                    $real_filesize = getid3_lib::getFileSizeSyscall($this->info['filenamepath']);

                    if ($real_filesize === false) {
                        unset($this->info['filesize']);
                        fclose($this->fp);

                        throw new getid3_exception('Unable to determine actual filesize. File is most likely larger than ' . round(PHP_INT_MAX / 1073741824) . 'GB and is not supported by PHP.');
                    } elseif (getid3_lib::intValueSupported($real_filesize)) {
                        unset($this->info['filesize']);
                        fclose($this->fp);

                        throw new getid3_exception('PHP seems to think the file is larger than ' . round(PHP_INT_MAX / 1073741824) . 'GB, but filesystem reports it as ' . number_format(
                            $real_filesize / 1073741824,
                            3
                        ) . 'GB, please report to info@getid3.org');
                    }
                    $this->info['filesize'] = $real_filesize;
                    $this->warning('File is larger than ' . round(PHP_INT_MAX / 1073741824) . 'GB (filesystem reports it as ' . number_format(
                        $real_filesize / 1073741824,
                        3
                    ) . 'GB) and is not properly supported by PHP.');
                }
            }

            return true;
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }

        return false;
    }
}
