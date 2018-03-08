<?php

namespace ipip\datx;

/**
 * IPIP datx code (https://www.ipip.net)
 * 国内区县版解析代码
 * 
 * @code $datx = new ipip\datx\District("c:/work/tiantexin/framework/library/ip/quxian.datx");
 * @code var_dump($datx->find("1.12.27.255"));
 */
class District
{
    private $file;

    private $offset;

    private $index;

    public function __construct($path)
    {
        $this->file = fopen($path, 'rb');

        $this->offset = unpack('Nlen', fread($this->file, 4));
        $this->index = fread($this->file, $this->offset['len'] - 4);
    }

    /**
     * @param string $ip
     * 
     * @return bool|array|null
     */
    public function find($ip)
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === FALSE)
        {
           return FALSE; // or throw Exception?
        }

        $ips = explode('.', $ip);

        $nip = pack('N', ip2long($ip));

        $idx = 256 * $ips[0] + intval($ips[1]);
        $idx = $idx * 4;
        $start = unpack('Vlen', substr($this->index, $idx, 4));

        $off = NULL;
        $len = NULL;

        for ($start = $start['len'] * 13 + 262144; $start < $this->offset['len'] - 262148; $start += 13)
        {
            if ($nip >= ($this->index[$start] . $this->index[$start + 1] . $this->index[$start + 2] . $this->index[$start + 3]))
            {
                if ($nip <= ($this->index[$start + 4] . $this->index[$start + 5] . $this->index[$start + 6] . $this->index[$start + 7]))
                {
                    $off = unpack('Vlen', substr($this->index, $start + 8, 4));
                    $len = unpack('Clen', $this->index[$start + 12]);
                    break;
                }
            }
        }

        if ($off === NULL)
        {
            return NULL;
        }

        fseek($this->file, $this->offset['len'] + $off['len'] - 262144);

        return explode("\t", fread($this->file, $len['len']));
    }
}