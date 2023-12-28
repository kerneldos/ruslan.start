<?php

namespace consumer\components;

use Exception;
use Vaites\ApacheTika\Client;
use Vaites\ApacheTika\Clients\CLIClient;

abstract class TikaClient extends Client {
    /**
     * @param string|null $param1
     * @param             $param2
     * @param array       $options
     * @param bool        $check
     *
     * @return TikaWebClient|CLIClient
     * @throws Exception
     */
    public static function make(string $param1 = null, $param2 = null, array $options = [], bool $check = true): Client {
        if(preg_match('/\.jar$/', func_get_arg(0)))
        {
            $path = $param1 ? (string) $param1 : null;
            $java = $param2 ? (string) $param2 : null;

            return new CLIClient($path, $java, $check);
        }
        else
        {
            $host = $param1 ? (string) $param1 : null;
            $port = $param2 ? (int) $param2 : null;

            return new TikaWebClient($host, $port, $options, $check);
        }
    }

    /**
     * @param string $file
     *
     * @return string
     * @throws Exception
     */
    protected function downloadFile(string $file): string {
        $dest = tempnam(sys_get_temp_dir(), 'TIKA');

        if($dest === false)
        {
            throw new Exception("Can't create a temporary file at " . sys_get_temp_dir());
        }

        $fp = fopen($dest, 'w+');

        if($fp === false)
        {
            throw new Exception("$dest can't be opened");
        }

        $ch = curl_init($file);

        if($ch === false)
        {
            throw new Exception("$file can't be downloaded");
        }

        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($ch);

        if(curl_errno($ch))
        {
            throw new Exception(curl_error($ch));
        }

        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if($code != 200)
        {
            throw new Exception("$file can't be downloaded", $code);
        }

        return $dest;
    }

    public function checkRequest(string $type, string $file = null): ?string {
        // no checks for getters
        if(in_array($type, ['detectors', 'mime-types', 'parsers', 'version']))
        {
            //
        }
        // invalid local file
        elseif($file !== null && !preg_match('/^http/', $file) && !file_exists($file))
        {
            throw new Exception("File $file can't be opened");
        }
        // invalid remote file
//        elseif($file !== null && preg_match('/^http/', $file))
//        {
//            $headers = get_headers($file);
//
//            if(empty($headers) || !preg_match('/200/', $headers[0]))
//            {
//                throw new Exception("File $file can't be opened", 2);
//            }
//        }
        // download remote file if required only for integrated downloader
        elseif($file !== null && preg_match('/^http/', $file) && $this->downloadRemote)
        {
            $file = $this->downloadFile($file);
        }

        return $file;
    }
}