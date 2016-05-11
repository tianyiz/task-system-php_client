<?php

class taskClient {
    
    private $addr;
    private $port;
    private $connection;
    private $socket;
    
    public function __construct($addr = '')
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);;
        if (!empty($addr)) {
            $addrInfo = explode(":", $addr);
            $addrInfo = array_filter(array_map("trim", $addrInfo));
            $this->addr = $addrInfo[0];
            $this->port = $addrInfo[1];
        }
        $this->content();
    }

    public function content()
    {
        if (!empty($this->addr) && !empty($this->port)) {
            $this->connection = socket_connect($this->socket, '127.0.0.1', 7788);
        }
    }

    public function Write($data)
    {
        $l = pack("N", strlen($data));
        socket_write($this->socket, "/" . $l . $data);
    }

    public function Read()
    {
        while (true) {
            $s = socket_read($this->socket, 1);
            if ($s == '/') {
                $s = socket_read($this->socket, 4);
                $l = unpack("N", $s);
                $s = '';
                if ($l[1] > 0) {
                    $s = socket_read($this->socket, $l[1]);
                }
                return $s;
            }
        }
    }

    public function AddTask($channel, $data)
    {
        $s = sprintf("%s %s %s", "addTask", $channel, $data);
        $this->Write($s);

        return $this->Read();
    }

    public function GetReturn($key, $timeout)
    {
        $s = sprintf("%s %s", "getReturn", $key, $timeout);
        $this->Write($s);

        return $this->Read();
    }

    public function GetTask($channel)
    {
        $s = sprintf("%s %s", "getTask", $channel);
        $this->Write($s);

        return $this->Read();
    }

    public function setReturn($key, $data)
    {
        $s = sprintf("%s %s %s", "setReturn", $key, $data);
        $this->Write($s);

        return $this->Read();
    }

    public function Usr1($channel)
    {
        $s = sprintf("%s %s", "usr1", $channel);
        $this->Write($s);

        return $this->Read();
    }

}