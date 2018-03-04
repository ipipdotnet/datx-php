<?php

namespace ipip\datx;

class BaseStation
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

    public function find($ip)
    {
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
            return FALSE;
        }

        fseek($this->file, $this->offset['len'] + $off['len'] - 262144);

        return explode("\t", fread($this->file, $len['len']));
    }
}

$datx = new BaseStation("c:/work/tiantexin/17mon/station_ip.datx");
var_dump($datx->find("27.128.80.57"));