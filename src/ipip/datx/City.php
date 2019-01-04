<?php

namespace ipip\datx;

/**
 * IPIP datx code (https://www.ipip.net)
 * 地级市精度版本
 * 
 * @code $datx = new ipip\datx\City("c:/work/tiantexin/17mon/mydata4vipday4.datx");
 * @code var_dump($datx->find("8.8.8.8"));
 * 
 */
class City
{
    private $file;

    private $offset;

    private $index;

    /**
     * @param string $path is file path
     */
    public function __construct($path)
    {
        if (!is_file($path))
        {
            throw new \Exception("{$path} is not exits.");
        }

        $this->file = fopen($path, 'rb');
        if (!is_resource($this->file))
        {
            throw new \Exception("{$path} fopen failed.");
        }

        $this->offset = unpack('Nlen', fread($this->file, 4));

        //$this->index = fread($this->file, $this->offset['len'] - 4);
    }

    /**
     * @param string $ip
     * 
     * @return bool|array
     */
    public function find($ip)
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === FALSE)
        {
           return FALSE; // or throw Exception?
        }

        $nip2 = pack('N', ip2long($ip));

        $ips = explode('.', $ip);

        $idx = (256 * $ips[0] + $ips[1]) * 4;


        /////////////////////////////////////////////
        //$start = unpack('Vlen', substr($this->index, $idx, 4));

        fseek($this->file, 4 + $idx); // offset 时 已经移动了4 字节，所以加4

        $start = unpack('Vlen', fread($this->file, 4));

        //print_r($start);exit;
        ////////////////////////////////////////

        $off = NULL;
        $len = NULL;

        $max = $this->offset['len'] - 262144 - 4;

        for ($start = $start['len'] * 9 + 262144; $start < $max; $start += 9)
        {

            ///////////////////////////////
            //$tmp = $this->index[$start] . $this->index[$start + 1] . $this->index[$start + 2] . $this->index[$start + 3];
            //$tmp = substr($this->index, $start, 4);

            fseek($this->file, 4 + $start); // offset 时 已经移动了4 字节，所以加4

            $tmp = fread($this->file, 4);

            //print_r(strncmp($tmp, $tmp1, 10));exit;
            ////////////////////////////


            if ($tmp >= $nip2)
            {
                ///////////////////////
                //$off = unpack('Vlen', substr($this->index, $start + 4, 3) . "\x0");

                fseek($this->file, 4 + $start + 4); // offset 时 已经移动了4 字节，所以加4

                $off = unpack('Vlen', fread($this->file, 3) . "\x0");

                //print_r($off);exit;
                ////////////////////////



                /////////////////////////
                //$len = unpack('nlen', $this->index[$start + 7] . $this->index[$start + 8]);
                //$len = unpack('nlen', substr($this->index, $start + 7, 2));

                fseek($this->file, 4 + $start + 7); // offset 时 已经移动了4 字节，所以加4

                $len = unpack('nlen', fread($this->file, 2));

                //print_r($len);exit;
                /////////////////////////
                break;
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