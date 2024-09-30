<?php

namespace Hp;

//  PROJECT HONEY POT ADDRESS DISTRIBUTION SCRIPT
//  For more information visit: http://www.projecthoneypot.org/
//  Copyright (C) 2004-2024, Unspam Technologies, Inc.
//
//  This program is free software; you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation; either version 2 of the License, or
//  (at your option) any later version.
//
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License for more details.
//
//  You should have received a copy of the GNU General Public License
//  along with this program; if not, write to the Free Software
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
//  02111-1307  USA
//
//  If you choose to modify or redistribute the software, you must
//  completely disconnect it from the Project Honey Pot Service, as
//  specified under the Terms of Service Use. These terms are available
//  here:
//
//  http://www.projecthoneypot.org/terms_of_service_use.php
//
//  The required modification to disconnect the software from the
//  Project Honey Pot Service is explained in the comments below. To find the
//  instructions, search for:  *** DISCONNECT INSTRUCTIONS ***
//
//  Generated On: Mon, 30 Sep 2024 15:05:47 -0400
//  For Domain: www.kumpe3d.com
//
//

//  *** DISCONNECT INSTRUCTIONS ***
//
//  You are free to modify or redistribute this software. However, if
//  you do so you must disconnect it from the Project Honey Pot Service.
//  To do this, you must delete the lines of code below located between the
//  *** START CUT HERE *** and *** FINISH CUT HERE *** comments. Under the
//  Terms of Service Use that you agreed to before downloading this software,
//  you may not recreate the deleted lines or modify this software to access
//  or otherwise connect to any Project Honey Pot server.
//
//  *** START CUT HERE ***

define('__REQUEST_HOST', 'hpr9.projecthoneypot.org');
define('__REQUEST_PORT', '80');
define('__REQUEST_SCRIPT', '/cgi/serve.php');

//  *** FINISH CUT HERE ***

interface Response
{
    public function getBody();
    public function getLines(): array;
}

class TextResponse implements Response
{
    private $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public function getBody()
    {
        return $this->content;
    }

    public function getLines(): array
    {
        return explode("\n", $this->content);
    }
}

interface HttpClient
{
    public function request(string $method, string $url, array $headers = [], array $data = []): Response;
}

class ScriptClient implements HttpClient
{
    private $proxy;
    private $credentials;

    public function __construct(string $settings)
    {
        $this->readSettings($settings);
    }

    private function getAuthorityComponent(string $authority = null, string $tag = null)
    {
        if(is_null($authority)){
            return null;
        }
        if(!is_null($tag)){
            $authority .= ":$tag";
        }
        return $authority;
    }

    private function readSettings(string $file)
    {
        if(!is_file($file) || !is_readable($file)){
            return;
        }

        $stmts = file($file);

        $settings = array_reduce($stmts, function($c, $stmt){
            list($key, $val) = \array_pad(array_map('trim', explode(':', $stmt)), 2, null);
            $c[$key] = $val;
            return $c;
        }, []);

        $this->proxy       = $this->getAuthorityComponent($settings['proxy_host'], $settings['proxy_port']);
        $this->credentials = $this->getAuthorityComponent($settings['proxy_user'], $settings['proxy_pass']);
    }

    public function request(string $method, string $uri, array $headers = [], array $data = []): Response
    {
        $options = [
            'http' => [
                'method' => strtoupper($method),
                'header' => $headers + [$this->credentials ? 'Proxy-Authorization: Basic ' . base64_encode($this->credentials) : null],
                'proxy' => $this->proxy,
                'content' => http_build_query($data),
            ],
        ];

        $context = stream_context_create($options);
        $body = file_get_contents($uri, false, $context);

        if($body === false){
            trigger_error(
                "Unable to contact the Server. Are outbound connections disabled? " .
                "(If a proxy is required for outbound traffic, you may configure " .
                "the honey pot to use a proxy. For instructions, visit " .
                "http://www.projecthoneypot.org/settings_help.php)",
                E_USER_ERROR
            );
        }

        return new TextResponse($body);
    }
}

trait AliasingTrait
{
    private $aliases = [];

    public function searchAliases($search, array $aliases, array $collector = [], $parent = null): array
    {
        foreach($aliases as $alias => $value){
            if(is_array($value)){
                return $this->searchAliases($search, $value, $collector, $alias);
            }
            if($search === $value){
                $collector[] = $parent ?? $alias;
            }
        }

        return $collector;
    }

    public function getAliases($search): array
    {
        $aliases = $this->searchAliases($search, $this->aliases);
    
        return !empty($aliases) ? $aliases : [$search];
    }

    public function aliasMatch($alias, $key)
    {
        return $key === $alias;
    }

    public function setAlias($key, $alias)
    {
        $this->aliases[$alias] = $key;
    }

    public function setAliases(array $array)
    {
        array_walk($array, function($v, $k){
            $this->aliases[$k] = $v;
        });
    }
}

abstract class Data
{
    protected $key;
    protected $value;

    public function __construct($key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    public function key()
    {
        return $this->key;
    }

    public function value()
    {
        return $this->value;
    }
}

class DataCollection
{
    use AliasingTrait;

    private $data;

    public function __construct(Data ...$data)
    {
        $this->data = $data;
    }

    public function set(Data ...$data)
    {
        array_map(function(Data $data){
            $index = $this->getIndexByKey($data->key());
            if(is_null($index)){
                $this->data[] = $data;
            } else {
                $this->data[$index] = $data;
            }
        }, $data);
    }

    public function getByKey($key)
    {
        $key = $this->getIndexByKey($key);
        return !is_null($key) ? $this->data[$key] : null;
    }

    public function getValueByKey($key)
    {
        $data = $this->getByKey($key);
        return !is_null($data) ? $data->value() : null;
    }

    private function getIndexByKey($key)
    {
        $result = [];
        array_walk($this->data, function(Data $data, $index) use ($key, &$result){
            if($data->key() == $key){
                $result[] = $index;
            }
        });

        return !empty($result) ? reset($result) : null;
    }
}

interface Transcriber
{
    public function transcribe(array $data): DataCollection;
    public function canTranscribe($value): bool;
}

class StringData extends Data
{
    public function __construct($key, string $value)
    {
        parent::__construct($key, $value);
    }
}

class CompressedData extends Data
{
    public function __construct($key, string $value)
    {
        parent::__construct($key, $value);
    }

    public function value()
    {
        $url_decoded = base64_decode(str_replace(['-','_'],['+','/'],$this->value));
        if(substr(bin2hex($url_decoded), 0, 6) === '1f8b08'){
            return gzdecode($url_decoded);
        } else {
            return $this->value;
        }
    }
}

class FlagData extends Data
{
    private $data;

    public function setData($data)
    {
        $this->data = $data;
    }

    public function value()
    {
        return $this->value ? ($this->data ?? null) : null;
    }
}

class CallbackData extends Data
{
    private $arguments = [];

    public function __construct($key, callable $value)
    {
        parent::__construct($key, $value);
    }

    public function setArgument($pos, $param)
    {
        $this->arguments[$pos] = $param;
    }

    public function value()
    {
        ksort($this->arguments);
        return \call_user_func_array($this->value, $this->arguments);
    }
}

class DataFactory
{
    private $data;
    private $callbacks;

    private function setData(array $data, string $class, DataCollection $dc = null)
    {
        $dc = $dc ?? new DataCollection;
        array_walk($data, function($value, $key) use($dc, $class){
            $dc->set(new $class($key, $value));
        });
        return $dc;
    }

    public function setStaticData(array $data)
    {
        $this->data = $this->setData($data, StringData::class, $this->data);
    }

    public function setCompressedData(array $data)
    {
        $this->data = $this->setData($data, CompressedData::class, $this->data);
    }

    public function setCallbackData(array $data)
    {
        $this->callbacks = $this->setData($data, CallbackData::class, $this->callbacks);
    }

    public function fromSourceKey($sourceKey, $key, $value)
    {
        $keys = $this->data->getAliases($key);
        $key = reset($keys);
        $data = $this->data->getValueByKey($key);

        switch($sourceKey){
            case 'directives':
                $flag = new FlagData($key, $value);
                if(!is_null($data)){
                    $flag->setData($data);
                }
                return $flag;
            case 'email':
            case 'emailmethod':
                $callback = $this->callbacks->getByKey($key);
                if(!is_null($callback)){
                    $pos = array_search($sourceKey, ['email', 'emailmethod']);
                    $callback->setArgument($pos, $value);
                    $this->callbacks->set($callback);
                    return $callback;
                }
            default:
                return new StringData($key, $value);
        }
    }
}

class DataTranscriber implements Transcriber
{
    private $template;
    private $data;
    private $factory;

    private $transcribingMode = false;

    public function __construct(DataCollection $data, DataFactory $factory)
    {
        $this->data = $data;
        $this->factory = $factory;
    }

    public function canTranscribe($value): bool
    {
        if($value == '<BEGIN>'){
            $this->transcribingMode = true;
            return false;
        }

        if($value == '<END>'){
            $this->transcribingMode = false;
        }

        return $this->transcribingMode;
    }

    public function transcribe(array $body): DataCollection
    {
        $data = $this->collectData($this->data, $body);

        return $data;
    }

    public function collectData(DataCollection $collector, array $array, $parents = []): DataCollection
    {
        foreach($array as $key => $value){
            if($this->canTranscribe($value)){
                $value = $this->parse($key, $value, $parents);
                $parents[] = $key;
                if(is_array($value)){
                    $this->collectData($collector, $value, $parents);
                } else {
                    $data = $this->factory->fromSourceKey($parents[1], $key, $value);
                    if(!is_null($data->value())){
                        $collector->set($data);
                    }
                }
                array_pop($parents);
            }
        }
        return $collector;
    }

    public function parse($key, $value, $parents = [])
    {
        if(is_string($value)){
            if(key($parents) !== NULL){
                $keys = $this->data->getAliases($key);
                if(count($keys) > 1 || $keys[0] !== $key){
                    return \array_fill_keys($keys, $value);
                }
            }

            end($parents);
            if(key($parents) === NULL && false !== strpos($value, '=')){
                list($key, $value) = explode('=', $value, 2);
                return [$key => urldecode($value)];
            }

            if($key === 'directives'){
                return explode(',', $value);
            }

        }

        return $value;
    }
}

interface Template
{
    public function render(DataCollection $data): string;
}

class ArrayTemplate implements Template
{
    public $template;

    public function __construct(array $template = [])
    {
        $this->template = $template;
    }

    public function render(DataCollection $data): string
    {
        $output = array_reduce($this->template, function($output, $key) use($data){
            $output[] = $data->getValueByKey($key) ?? null;
            return $output;
        }, []);
        ksort($output);
        return implode("\n", array_filter($output));
    }
}

class Script
{
    private $client;
    private $transcriber;
    private $template;
    private $templateData;
    private $factory;

    public function __construct(HttpClient $client, Transcriber $transcriber, Template $template, DataCollection $templateData, DataFactory $factory)
    {
        $this->client = $client;
        $this->transcriber = $transcriber;
        $this->template = $template;
        $this->templateData = $templateData;
        $this->factory = $factory;
    }

    public static function run(string $host, int $port, string $script, string $settings = '')
    {
        $client = new ScriptClient($settings);

        $templateData = new DataCollection;
        $templateData->setAliases([
            'doctype'   => 0,
            'head1'     => 1,
            'robots'    => 8,
            'nocollect' => 9,
            'head2'     => 1,
            'top'       => 2,
            'legal'     => 3,
            'style'     => 5,
            'vanity'    => 6,
            'bottom'    => 7,
            'emailCallback' => ['email','emailmethod'],
        ]);

        $factory = new DataFactory;
        $factory->setStaticData([
            'doctype' => '<!DOCTYPE html>',
            'head1'   => '<html><head>',
            'head2'   => '<title>Whorish Interrogative>www.kumpe3d.com</title></head>',
            'top'     => '<body><div align="center">',
            'bottom'  => '</div></body></html>',
        ]);
        $factory->setCompressedData([
            'robots'    => 'H4sIAAAAAAAAA7PJTS1JVMhLzE21VSrKT8ovKVZSSM7PK0nNK7FVystPLErOyCxL1UnLz8nJL1eys8GvPDMvJbVCyQ4A0z75z1UAAAA',
            'nocollect' => 'H4sIAAAAAAAAA7PJTS1JVMhLzE21VcrL103NTczM0U3Oz8lJTS7JzM9TUkjOzytJzSuxVdJXsgMAKsBXli0AAAA',
            'legal'     => 'H4sIAAAAAAAAA6VabXPbNhL-fr8C49y48YzrWIlf4qPrGZ2jxLppZZ8kJ5OPEAmJaEhCA4Jy1V_fxe6CpCRK1-Q-eEyBELhYPPvss0vdOjnLlIhVlpVLGeti8cvR-ZGYGZsoi5f-1lImCd-6u3X27h-3LhGlW2fql6O5KdzPc5nrbP0vkZvC-HVUdHR3_Kp3dR6JzX-3s7tb_w0Rm8zYX15S7dSdu33jx-5u38zujotZuYxuZ7Z1NU2VeFGzEkdf9XpXkfHrve1F_lMvqgTeMi6Fz-e9yAqnaO7764iGZiU8SIQFLiIlk107NNtBz0hxDbmgBQpX7n5h0fpCD011qcrPwLj3F1HqPxfG3zuPfqY1rSz86BfYjKYRdeYH2DDw0CE_lW0__c_Zba9u-nPiH_02UrGWGfpkZAow8PptVOXSfy5pggYMlKd-YEleMwt7_OrmOsrR39IPXkeOdp9Lp8NWBjlO8567jLJd0-ZsGhwCzB6SJ4QuhSWfZ6ZQZ-Krv07xKZeRkGJFtjlDxsiFQttW-KysUsLfOH8bhVO-jMJUwC6fPcxcWKVyRcey6ftNrB7ybbzXtwMZeysucBXwobQBRfBoXZS8Vz_4-gh8fhFViY51A23AOkN0gaNlBV-9iXjRTL7geoX_DPbvWGbYsqWycwwK9snOxIQnzhUtpWy9MASWwG2AOXSGiBpFdq10DFcXVwHv-5EnZ2Z1yMMNKdgOH4do9He_GsIaTCgQe7kMdi6XhJlZVQJmpv76s4ZoN5Yw0Bjw_ioaToYYm-D4m2g88eNP_TEOfRWPY9zm8_Grq6sIYuTqJvrv8wC__8Yl_wfptbcJf56J5tbk_rqa_a7Y08L5AZt38Exw6MKsiI3Yo-AAU8FRxUx3vfMbcJG0ig698qPz_eiFwxMaCUnhIiW6tpwr65NCMHfn9P5uYISguJuEMGnxb-k4NBHduPVCfCa0-dNrompG_EJOKk-Fbm4lqtSLAi2NZZatay5NLGxGlejyGEPgXVTQ4n8qJB30mx-5jgp8WFaRawUxg3GieY4skjfGtgZCqJbgOAoDIC9gdbwWMF2oP36UW9QWt-CaAe20FXId2avo-Wy8cZy2MtGPnTbEmMrS7UT0cUAARCBy0A82V2BtwV_yplMyLXYtW7JlM2uqReOMr-BLiEvG0WLDuLzbC50bT7uws9-KTbUQAsBnauGMwOwrrShNrpAyAetDZrrCtY6W7VXswKJeDG_3PwE5vI0GA2KWXWu-sTWTwejDcPQJs-lUIIWMB_37H2GPQz76tiMBttmlAehNlGpORvtRtsbDFo7mATEhZGzhU6bE2MT8Dz71aA9efh9BsPldXkYFrpDolU6IoykdN6ZYTaBNXdmKoBfNjzSVazMN7PppcD_s_7pf_gB5Q_JpRYeGpCBMgavb8mz_ZkdBjSHdJOqPoG8SDxmgwLYlsCCGdWOzS6X3gx94IS-oZMEMkAHhwD1Jziid1bMqeOs8SpCSIRuVqslKtQT8iSUOunfpqStbN-f5t2X0fG9goGWcZfB40FPA_QJPmGK3ODoRZSrx5JJck6d04Gk0LdPKewoZR1uVMSthfDnpOqRAwGts0BspkONCUs4pFUUq8kbhzg5ueBvkfhMrj9G1oeDmKA4w0HOtYINcF_jHEDjLsMJDfyoe-hjcn1vhPoIRyKG_YfZHffAgHj8eUv4gHR5-SCQ0RznFp41_28iTQKq4S5-XEWCEOPMSxCyMM27YiQxSBRKCMEkDXtw_VEjEr8sT8ZLSbJbyBWOwoxxyLQEWAqDjiIOQVH8stwJ5eD8YTQb7QTHe1AVgjVjpkPzBZDJQfG5lfGtmJigBBE4JTrgAYWyVbD-9pGTPm_P5DmeTw5RQucRFWUYDKKEoIA3yPBH_vDwniHclwcBCZs4lBShhMQxu5P-NHJNcBAkooSm8DfGenuma-Ops1396-hXo7V1032_7pliLstIOd1wEvaiZzNCMjiKrocsbICqxIBmFghxKtVXHSTb179U1l9bdSU8GgU-1FAZcUy5ccNFQMFdaIH6g-zSci2ehVOck5FSmYmdNsQWd8eD-cfwBoutdtEtnlW0cbll-cSgsZUuYVAUOv4sqlZE6aRupOUr832AyHULoX_eiU3TnBFPbpwGc7nk0OaEx0AADH6TfFeudTB2KtP7ow_6wu38ctTFQeupNidFiPnpCr2ad5-X8l1abBNKO12UJ-lnW6po8ReAj-qfJTPQmwwWAU9ZAQSGHoWRnH7-wct86MvDheHg_HbaNtj8dqGYyTXVLq1hri_uYHiNfMtWkoORUkJ6_iZYbBvB3TNFEvpd6e2JjWTcgfKWwKZtA10OaJzo5E17czityL0lEr_lpddIe5D3QzjFJJ6o8CNzJWctB_0bhMtiQt7_2v-ziez9guK3iKybNmY6UHoSXMPZAPeatsQVgaIZ6rXAyduJ1w1YFBOlMuZcQJwD_hPysOaP6cFZUQNS6AcgI78WiVHa1jQhInO29UgWOMT2cbtCbcuLJGkfPft2ZZYKEO_KT1iKXLqZKJ2m0lZiHFT_1pw-D8XDkr6ePAj7s9-mwOf5HOKGupoqrRf4YN_HdFAD-fA-bHj6OJh1lC0OPe1PkdcpGJaWXaUogrXuGVGCQSHQof1-7AIZeZEsmOkM9hVMhK0Qvl9EUSpz7QCZxzeicKsSSGLV9imDzXo98fBzvbqgIadaLahyIM5JfiSJ-5aSdSruiECVqBtP3x-qiVq41uwCcKZ_2qOvnA08GJYhEmUKqp9YbySfKRylsmhMF-qDVLch8PtKUj3w_MjaL7dzkWbuN6_88jzdI71AcgrlEetJyymKhvdioM1Ro9MHVUT_Jj-sunFIHNEmftpNrzmEeA9IRbR0oi7DR2kT7xFFPgx4aVEbgGXQr6Ow5uH4bKSin9yvEZ4z9wRYpANNseA--GcTSMIHCAKQ82icoJZGHSRuDLqpt-ISQ2q9VJtPHMYb6h8FoOvw4RM0-RKkzOPvugN6itufJoCMMnED9cNRWHaRDBal0f42KFzR9OO0p3OHrLwq1W0l9YOB3YdpLdaeKIOKm-ARMSTlDjQrOXlSIrYMbPYL4ubiMHp5_2zkP7AuykptVDltHaI_zxbMw7dRoQx-ojlDCkHLSrg8XGBAYK51Qb447M0nStGa89RTy4LsUDx-RWFYzX6F6LDDVIac0Rbb-E0hClts7nnygo_eU3N6tSup4nCG05pZ8louwyW63f6sztJ89F51JbNnasI8z2gctexQGJb-ooq4aU2kgd3xhtDf-vVgJrAb0OoOiKE4pqPwhWrV97h3yMwRrH-TK-8uIgN0wLi4Th4fAkR-dHJIrQYq9pTLFC3fHco4ahuiC0hS1UBrtC-TQ3piO-5jXx-Ie2_ijCRbhw3pf3xHH7QSML-Uck_Nx3QvGQqZOPTT1te4g4fqtB3XESk-RE65PFHZWqAixLxokJMa4n3AcXqrgOSFwKKja57RBNp_DW4zHDeQ2dEHddONEpluZoyMrhY6rJMyvBWkwF8pYEdDftkfDCfos2bhjDsyUUofhQJ8gRAi3yZtUF3OE4eK-jS-oZPGseCoW1MrAA9gL1pWu1Q0kebndUTzca64zT7VIPWWDD9eNhZ5HuTjhbnpnoyug06AGPREEemNDDaxKnXDj2be9yFy-6asW3wzDY0Tc0DgpHpuEjs8mXhu5AbRJOpHIe8btSVi_rEp6PeYVNtd3bvdIJx-pAzXmDjai63k6mAgKtR8Lqy5mCDoIim-SD0mim1IG95XjO8ieV6YnlCBjdXRCG2xmzVT9LlB-A6dVJWfWELvohgKEHjdqWnAYd4jwjiOtuxzLJfdjHbY_ScJ6DRmbHHteTXKAIGw_zFVQF0JqQzML6m4hiIKSDwlzqTMZZGeuC12GYrMhfnigD4hAvaDMCoTK5ubaCCkr0L-kjmX4Wpc-S2u6Bnnm67i4O8eFwyMwH9dv0KntwDsTgGTF7TbxO8l-q0tM7KDgHL_fJinuX6BNOZFg_Esvvmnl7Zxdd4e9Gg2qnn6_AKXs0jh6icXy0e66o3nzK_pQEjY3-vf3iP-naR1AP4b4RsL4yqdosQiXWE-ZkgRUdmB4UdbSPaauYsNLmS4_UJP0hbm0_iGGd6QfqVNZ-IO6ztH7UWOpwSM1hc_pjpXtl54IdV12mUD2uzWFzwzLApY_HIoBvb7FyrqK7NZ1jaWKSoU0S_ih_dOWimS7HKU_6tvQPrRsSjsOmvKsPXk4_uD_je4Hwpdum6f7Bn_O9AZ_BgUXcOMvqmAaSxMlAAA',
            'style'     => 'H4sIAAAAAAAAAyXMywmAMAwA0FUEr36vrXjsHlFTGiiJpBEq4u4efAO8pdidcYUhKlRKlMDwenbJoq4NIfgobG6TfDTzdNYGlCB3Bbj0BZWiN6zWH7iLgpGwY2H07zL-7wdt-01vXwAAAA',
            'vanity'    => 'H4sIAAAAAAAAA22S207DMAyGX8XKbtk6TpOWdRViGkJIsInDBZdpk7VhIY4cb93enrSMG0CRJVuJ__-Lk5xV6QxUxrkYVGV9PRdj0ZVBaX0qSyRtqMsiH52Zi1JV25pw57UcTKfTWWs1N_LichwOM1HkTCk07JWztZ8LxvDTeBKVcB4OcJHiOsVV6vq2GJKtG5YRndX9kcFisegUE5uHk8YGPcsSnYbODxRZ5c6i8nEYDdnNrEKHJAeTyWSWnGXHFDBatuglGafY7k3SvMmzTrXIM9Z_cOGUO7NhAb_gL5PrOK2r79sqaMhs5qJhDjLL2rYdBcIPU3GD3hwD8gipzgRUTsWY-EkdbGMbxWYnisfl4-3yGVZ3sH5ePSwXr3C_elq-w3r1mmeqyEv612HnE_znqMJP8a_sS9qFe0V7E9kQrAk5AaURwJPhFmnbCSfMvdVGQ3mEt16wt-wHknWPmPW_o_gCRc3zEyUCAAA',
        ]);
        $factory->setCallbackData([
            'emailCallback' => function($email, $style = null){
                $value = $email;
                $display = 'style="display:' . ['none',' none'][random_int(0,1)] . '"';
                $style = $style ?? random_int(0,5);
                $props[] = "href=\"mailto:$email\"";
        
                $wrap = function($value, $style) use($display){
                    switch($style){
                        case 2: return "<!-- $value -->";
                        case 4: return "<span $display>$value</span>";
                        case 5:
                            $id = 'pruzuz8u3c';
                            return "<div id=\"$id\">$value</div>\n<script>document.getElementById('$id').innerHTML = '';</script>";
                        default: return $value;
                    }
                };
        
                switch($style){
                    case 0: $value = ''; break;
                    case 3: $value = $wrap($email, 2); break;
                    case 1: $props[] = $display; break;
                }
        
                $props = implode(' ', $props);
                $link = "<a $props>$value</a>";
        
                return $wrap($link, $style);
            }
        ]);

        $transcriber = new DataTranscriber($templateData, $factory);

        $template = new ArrayTemplate([
            'doctype',
            'injDocType',
            'head1',
            'injHead1HTMLMsg',
            'robots',
            'injRobotHTMLMsg',
            'nocollect',
            'injNoCollectHTMLMsg',
            'head2',
            'injHead2HTMLMsg',
            'top',
            'injTopHTMLMsg',
            'actMsg',
            'errMsg',
            'customMsg',
            'legal',
            'injLegalHTMLMsg',
            'altLegalMsg',
            'emailCallback',
            'injEmailHTMLMsg',
            'style',
            'injStyleHTMLMsg',
            'vanity',
            'injVanityHTMLMsg',
            'altVanityMsg',
            'bottom',
            'injBottomHTMLMsg',
        ]);

        $hp = new Script($client, $transcriber, $template, $templateData, $factory);
        $hp->handle($host, $port, $script);
    }

    public function handle($host, $port, $script)
    {
        $data = [
            'tag1' => '8a8c952aa0637853d153ef343f6a0682',
            'tag2' => 'f5e86977901bbc208d9eb44938d0d42d',
            'tag3' => '3649d4e9bcfd3422fb4f9d22ae0a2a91',
            'tag4' => md5_file(__FILE__),
            'version' => "php-".phpversion(),
            'ip'      => $_SERVER['REMOTE_ADDR'],
            'svrn'    => $_SERVER['SERVER_NAME'],
            'svp'     => $_SERVER['SERVER_PORT'],
            'sn'      => $_SERVER['SCRIPT_NAME']     ?? '',
            'svip'    => $_SERVER['SERVER_ADDR']     ?? '',
            'rquri'   => $_SERVER['REQUEST_URI']     ?? '',
            'phpself' => $_SERVER['PHP_SELF']        ?? '',
            'ref'     => $_SERVER['HTTP_REFERER']    ?? '',
            'uagnt'   => $_SERVER['HTTP_USER_AGENT'] ?? '',
        ];

        $headers = [
            "User-Agent: PHPot {$data['tag2']}",
            "Content-Type: application/x-www-form-urlencoded",
            "Cache-Control: no-store, no-cache",
            "Accept: */*",
            "Pragma: no-cache",
        ];

        $subResponse = $this->client->request("POST", "http://$host:$port/$script", $headers, $data);
        $data = $this->transcriber->transcribe($subResponse->getLines());
        $response = new TextResponse($this->template->render($data));

        $this->serve($response);
    }

    public function serve(Response $response)
    {
        header("Cache-Control: no-store, no-cache");
        header("Pragma: no-cache");

        print $response->getBody();
    }
}

Script::run(__REQUEST_HOST, __REQUEST_PORT, __REQUEST_SCRIPT, __DIR__ . '/phpot_settings.php');

