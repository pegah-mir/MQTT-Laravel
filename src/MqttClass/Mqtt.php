<?php
/**
 * Created by PhpStorm.
 * User: pegah
 * Date: 2/22/19
 * Time: 1:16 PM
 */

namespace Pegah\Mqtt\MqttClass;

/*
	Licence
	Copyright (c) 2019 Pegah Zafar
	zohremirzaee93@gmail.com
	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:
	The above copyright notice and this permission notice shall be included in
	all copies or substantial portions of the Software.
	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.

*/


class Mqtt
{
    protected $host = null;
    protected $username = null;
    protected $cert_file = null;
    protected $local_cert = null;
    protected $local_pk = null;
    protected $password = null;
    protected $port = null;
    protected $timeout = 0;
    protected $debug = null;
    protected $qos = 0;
    protected $retain = 0;

    public function __construct(array $configs = [])
    {
        $this->host         = array_key_exists('host', $configs) ? $configs['host'] : config('mqtt.host');
        $this->username     = array_key_exists('username', $configs) ? $configs['username'] : config('mqtt.username');
        $this->password     = array_key_exists('password', $configs) ? $configs['password'] : config('mqtt.password');
        $this->cert_file    = array_key_exists('certfile', $configs) ? $configs['certfile'] : config('mqtt.certfile');
        $this->local_cert   = array_key_exists('localcert', $configs) ? $configs['localcert'] : config('mqtt.localcert');
        $this->local_pk     = array_key_exists('localpk', $configs) ? $configs['localpk'] : config('mqtt.localpk');
        $this->port         = array_key_exists('port', $configs) ? $configs['port'] : config('mqtt.port');
        $this->timeout      = array_key_exists('timeout', $configs) ? $configs['timeout'] : config('mqtt.timeout');
        $this->debug        = array_key_exists('debug', $configs) ? $configs['debug'] : config('mqtt.debug');
        $this->qos          = array_key_exists('qos', $configs) ? $configs['qos'] : config('mqtt.qos');
        $this->retain       = array_key_exists('retain', $configs) ? $configs['retain'] : config('mqtt.retain');
    }


    /**
     * @param $topic
     * @param $msg
     * @param null $client_id
     * @param null $retain
     * @return bool
     */
    public function ConnectAndPublish($topic, $msg, $client_id=null, $retain=null)
    {
        $id = empty($client_id) ?  rand(0,999) : $client_id;

        $client = new MqttService($this->host, $this->port, $this->timeout, $id, $this->cert_file, $this->local_cert, $this->local_pk, $this->debug);

        $retain = empty($retain) ?  $this->retain : $retain;

        if ($client->connect(true, null, $this->username, $this->password))
        {
            $client->publish($topic,$msg, $this->qos, $retain);
            $client->close();

            return true;
        }

        return false;

    }

    /**
     * @param $topic
     * @param $proc
     * @param null $client_id
     * @return boolean
     */
    public function ConnectAndSubscribe($topic, $proc, $client_id=null)
    {
        $id = empty($client_id) ?  rand(0,999) : $client_id;

        $client = new MqttService($this->host, $this->port, $this->timeout, $id, $this->cert_file, $this->local_cert, $this->local_pk, $this->debug);

        if ($client->connect(true, null, $this->username, $this->password))
        {
            $topicData = ['qos' => $this->qos];
            $topics = is_array($topic) ? $topic : [$topic];

            foreach ($topics as $topicName) {
                $topicData[$topicName] = ["qos" => 0, "function" => $proc];
            }

            $client->subscribe($topicData, $this->qos);

            while($client->proc())
            {

            }

            $client->close();

            return true;
        }

        return false;
    }
}
