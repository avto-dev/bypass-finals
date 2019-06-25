<?php

namespace DG;

/**
 * Removes keyword final from source codes.
 */
class BypassFinals
{
    const PROTOCOL = 'file';

    /**
     * @var resource|null
     */
    public $context;

    /**
     * @var resource|null
     */
    private $handle;

    /**
     * @var array|null
     */
    private static $pathWhitelist = [];

    /**
     * Enable final keyword from source codes removal.
     *
     * @return void
     */
    public static function enable()
    {
        \stream_wrapper_unregister(self::PROTOCOL);
        \stream_wrapper_register(self::PROTOCOL, __CLASS__);
    }

    /**
     * Set whitelisted paths (bypass finals in specific files only).
     *
     * @param array $pathWhitelist
     *
     * @return void
     */
    public static function setWhitelist(array $pathWhitelist)
    {
        self::$pathWhitelist = $pathWhitelist;
    }

    /**
     * @return void
     */
    public function dir_closedir()
    {
        \closedir($this->handle);
    }

    /**
     * @param string $path
     * @param        $options
     *
     * @return bool
     */
    public function dir_opendir($path, $options)
    {
        $this->handle = $this->context
            ? $this->native('opendir', $path, $this->context)
            : $this->native('opendir', $path);

        return (bool) $this->handle;
    }

    /**
     * @return false|string
     */
    public function dir_readdir()
    {
        return \readdir($this->handle);
    }

    /**
     * @return void
     */
    public function dir_rewinddir()
    {
        \rewinddir($this->handle);
    }

    /**
     * @param string $path
     * @param int    $mode
     * @param int    $options
     *
     * @return bool
     */
    public function mkdir($path, $mode, $options)
    {
        $recursive = (bool) ($options & STREAM_MKDIR_RECURSIVE);

        return $this->native('mkdir', $path, $mode, $recursive, $this->context);
    }

    /**
     * @param string $pathFrom
     * @param string $pathTo
     *
     * @return mixed
     */
    public function rename($pathFrom, $pathTo)
    {
        return $this->native('rename', $pathFrom, $pathTo, $this->context);
    }

    /**
     * @param string $path
     * @param        $options
     *
     * @return mixed
     */
    public function rmdir($path, $options)
    {
        return $this->native('rmdir', $path, $this->context);
    }

    /**
     * @param $castAs
     *
     * @return resource|null
     */
    public function stream_cast($castAs)
    {
        return $this->handle;
    }

    /**
     * @return void
     */
    public function stream_close()
    {
        \fclose($this->handle);
    }

    /**
     * @return bool
     */
    public function stream_eof()
    {
        return \feof($this->handle);
    }

    /**
     * @return bool
     */
    public function stream_flush()
    {
        return \fflush($this->handle);
    }

    /**
     * @param int $operation
     *
     * @return bool
     */
    public function stream_lock($operation)
    {
        return $operation
            ? \flock($this->handle, $operation)
            : true;
    }

    /**
     * @param string $path
     * @param int    $option
     * @param        $value
     *
     * @return mixed|void
     */
    public function stream_metadata($path, $option, $value)
    {
        switch ($option) {
            case \STREAM_META_TOUCH:
                $value += [null, null];

                return $this->native('touch', $path, $value[0], $value[1]);
            case \STREAM_META_OWNER_NAME:
            case \STREAM_META_OWNER:
                return $this->native('chown', $path, $value);
            case \STREAM_META_GROUP_NAME:
            case \STREAM_META_GROUP:
                return $this->native('chgrp', $path, $value);
            case \STREAM_META_ACCESS:
                return $this->native('chmod', $path, $value);
        }
    }

    /**
     * @param string $path
     * @param bool   $mode
     * @param int    $options
     * @param        $openedPath
     *
     * @return bool
     */
    public function stream_open($path, $mode, $options, &$openedPath)
    {
        $usePath = (bool) ($options & \STREAM_USE_PATH);
        if (self::pathInWhitelist($path) && pathinfo($path, \PATHINFO_EXTENSION) === 'php') {
            $content = $this->native('file_get_contents', $path, $usePath, $this->context);
            if ($content === false) {
                return false;
            }
            $modified = self::removeFinals($content);
            if ($modified !== $content) {
                $this->handle = tmpfile();
                $this->native('fwrite', $this->handle, $modified);
                $this->native('fseek', $this->handle, 0);

                return true;
            }
        }
        $this->handle = $this->context
            ? $this->native('fopen', $path, $mode, $usePath, $this->context)
            : $this->native('fopen', $path, $mode, $usePath);

        return (bool) $this->handle;
    }

    /**
     * @param int $count
     *
     * @return bool|string
     */
    public function stream_read($count)
    {
        return \fread($this->handle, $count);
    }

    /**
     * @param int $offset
     * @param int $whence
     *
     * @return bool
     */
    public function stream_seek($offset, $whence = SEEK_SET)
    {
        return \fseek($this->handle, $offset, $whence) === 0;
    }

    /**
     * @param $option
     * @param $arg1
     * @param $arg2
     *
     * @return void
     */
    public function stream_set_option($option, $arg1, $arg2)
    {
        //
    }

    /**
     * @return array
     */
    public function stream_stat()
    {
        return \fstat($this->handle);
    }

    /**
     * @return bool|int
     */
    public function stream_tell()
    {
        return \ftell($this->handle);
    }

    /**
     * @param int $newSize
     *
     * @return bool
     */
    public function stream_truncate($newSize)
    {
        return \ftruncate($this->handle, $newSize);
    }

    /**
     * @param string $data
     *
     * @return bool|int
     */
    public function stream_write($data)
    {
        return \fwrite($this->handle, $data);
    }

    /**
     * @param string $path
     *
     * @return mixed
     */
    public function unlink($path)
    {
        return $this->native('unlink', $path);
    }

    /**
     * @param string $path
     * @param int    $flags
     *
     * @return mixed
     */
    public function url_stat($path, $flags)
    {
        $func = ($flags & \STREAM_URL_STAT_LINK)
            ? 'lstat'
            : 'stat';

        return ($flags & \STREAM_URL_STAT_QUIET)
            ? @$this->native($func, $path)
            : $this->native($func, $path);
    }

    /**
     * @param callable $func
     *
     * @return mixed
     */
    private function native($func)
    {
        \stream_wrapper_restore(self::PROTOCOL);
        $res = \call_user_func_array($func, array_slice(func_get_args(), 1));
        \stream_wrapper_unregister(self::PROTOCOL);
        \stream_wrapper_register(self::PROTOCOL, __CLASS__);

        return $res;
    }

    /**
     * @param string $code
     *
     * @return string
     */
    public static function removeFinals($code)
    {
        if (\strpos($code, 'final') !== false) {
            $tokens = \PHP_VERSION_ID >= 70000
                ? \token_get_all($code, \TOKEN_PARSE)
                : \token_get_all($code);
            $code   = '';
            foreach ($tokens as $token) {
                $code .= \is_array($token)
                    ? ($token[0] === T_FINAL
                        ? ''
                        : $token[1])
                    : $token;
            }
        }

        return $code;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    private static function pathInWhitelist($path)
    {
        if (empty(self::$pathWhitelist)) {
            return true;
        }
        foreach (self::$pathWhitelist as $whitelistItem) {
            if (\substr($path, -\strlen($whitelistItem)) === $whitelistItem) {
                return true;
            }
        }

        return false;
    }
}
