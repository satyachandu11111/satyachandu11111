<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-feed
 * @version   1.0.82
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Feed\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Io extends AbstractHelper
{
    /**
     * Write content to file
     *
     * @param string $filename
     * @param string $content
     * @param string $mode
     * @return $this
     * @throws \Exception
     */
    public function write($filename, $content, $mode = 'w')
    {

        $wait = true;
        $fp = fopen($filename, $mode);
        if ($this->isWin()) {
            fwrite($fp, $content);
        } else {
            flock($fp, LOCK_SH, $wait);
            fwrite($fp, $content);
            flock($fp, LOCK_UN);
        }
        fclose($fp);

        @chmod($filename, 0777);

        if (!$this->fileExists($filename)) {
            throw new \Exception(sprintf('File %1 not created.', $filename));
        }

        return $this;
    }

    /**
     * Copy file from one place to another
     *
     * @param string $from
     * @param string $to
     * @return $this
     * @throws \Exception
     */
    public function copy($from, $to)
    {
        if (!$this->fileExists($from)) {
            throw new \Exception(sprintf('File %1 not exists.', $from));
        }

        copy($from, $to);

        @chmod($to, 0777);

        if (!$this->fileExists($to)) {
            throw new \Exception(sprintf('File %1 not copied to %2', $from, $to));
        }

        return $this;
    }

    /**
     * Is file exists?
     *
     * @param string $file
     * @return bool
     */
    public function fileExists($file)
    {
        $result = file_exists($file);
        if ($result) {
            $result = is_file($file);
        }

        return $result;
    }

    /**
     * Is directory exists?
     *
     * @param string $path
     * @return bool
     */
    public function dirExists($path)
    {
        $result = file_exists($path);
        if ($result) {
            $result = is_dir($path);
        }

        return $result;
    }

    /**
     * Remove file
     *
     * @param string $file
     * @return bool
     */
    public function unlink($file)
    {
        return @unlink($file);
    }

    /**
     * Create directory (recursive)
     *
     * @param string $dir
     * @param int    $mode
     * @param bool   $recursive
     * @return bool
     */
    public function mkdir($dir, $mode = 0777, $recursive = true)
    {
        $result = @mkdir($dir, $mode, $recursive);

        if ($result) {
            @chmod($dir, $mode);
        }

        return $result;
    }

    /**
     * Remove directory
     *
     * @param string $dir
     * @param bool   $recursive
     * @return bool
     * @throws \Exception
     */
    public function rmdir($dir, $recursive = true)
    {
        if (!$this->dirExists($dir)) {
            return true;
        }

        $result = self::rmdirRecursive($dir, $recursive);

        if (!$result) {
            throw new \Exception(__("Can't remove folder %1", $dir));
        }

        return $result;
    }

    /**
     * Remove directory
     *
     * @param string $dir
     * @param bool   $recursive
     * @return bool
     */
    public function rmdirRecursive($dir, $recursive = true)
    {
        if ($recursive) {
            if (is_dir($dir)) {
                foreach (scandir($dir) as $item) {
                    if (!strcmp($item, '.') || !strcmp($item, '..')) {
                        continue;
                    }
                    $this->rmdirRecursive($dir . '/' . $item, $recursive);
                }
                $result = @rmdir($dir);
            } else {
                $result = @unlink($dir);
            }
        } else {
            $result = @rmdir($dir);
        }

        return $result;
    }

    /**
     * Check if current OS is Windows
     *
     * @return bool
     */
    public function isWin()
    {
        return (strtolower(substr(PHP_OS, 0, 3)) == 'win') ? true : false;
    }
}
