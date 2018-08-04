<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


namespace KryuuCommon\JsonStableStringify;

/**
 * Description of Json
 *
 * @author spawn
 */
class Json {
    
    private $seen = [];
    private $cmp = null;
    private $space = null;
    private $replacer = null;
    private $cycles = null;
    
    public function stringify($obj, $opts = null) {
        if (!$opts) { $opts = []; }
        if (is_callable($opts)) { $opts = [ 'cmp' => $opts ]; }
        
        $this->space = array_key_exists('space', $opts) ? $opts['space'] : '';
        
        if (is_numeric($this->space)) { $this->space = implode(' ', $this->space+1); }
        
        $this->cycles = array_key_exists('cycles', $opts) && is_bool($opts['cycles']) 
                ? $opts['cycles'] : false;
        
        $this->replacer = array_key_exists('replacer', $opts) && $opts['replacer'] 
                ? $opts['replacer'] : function($key, $value) { return $value; };

        $this->cmp = array_key_exists('cmp', $opts) ? (function ($f) {
            return function ($node) use ($f) {
                return function ($a, $b) use ($node, $f) {
                    $aobj = [ 'key' => $a, 'value' => $node[$a] ];
                    $bobj = [ 'key' => $b, 'value' => $node[$b] ];
                    return $f($aobj, $bobj);
                };
            };
        })($opts['cmp']) : null;

        $this->seen = [];        
                
        return $this->encode([$obj], '', $obj, 0);
    }
    
    private function array_indexOf($array, $value) {
        foreach($array as $idx => $aValue) {
            if ($value === $aValue) {
                return $idx;
            }
        }
        
        return -1;
    }
    
    private function encode($parent, $key, $node, $level) {
        
            $indent = $this->space ? ('\n' . implode($this->space, $level + 1)) : '';
            $colonSeparator = $this->space ? ': ' : ':';

            if ($node && method_exists($node, 'toJSON') && is_callable($node['toJSON'])) {
                $callable = $node['toJSON'];
                $node = $callable();
            }
            $replacer = $this->replacer;
            $node = $replacer($key, $node);
            
            if (!isset($node)) {
                return;
            }
            if ((!is_object($node) && !is_array($node)) || $node === null) {
                return json_encode($node);
            }            
            
            if (is_array($node)) {
                $out = [];

                for ($i = 0; $i < count($node); $i++) {
                    if (!$item = $this->encode($node, $i, $node[$i], $level+1)) {
                        $item = json_encode(null);
                    }
                    array_push($out, $indent . $this->space . $item);
                }
                return '[' . implode(',', $out) . $indent . ']';
            } else {
                if (in_array($node, $this->seen)) {
                    if ($this->cycles) {
                        return json_encode('__cycle__');
                    }
                    throw new TypeError('Converting circular structure to JSON');
                } else {
                    array_push($this->seen, $node);
                }
                
                if ($this->cmp) {
                    $keys = array_keys(get_object_vars($node));
                    usort($keys, $this->cmp($node));
                } else {
                    $keys = array_keys(get_object_vars($node));
                    sort($keys);
                }
                
                        
                $out = [];
                for ($i = 0; $i < count($keys); $i++) {
                    $key = $keys[$i];
                    $value = $this->encode($node, $key, $node->{$key}, $level+1);

                    if(!$value) continue;

                    $keyValue = json_encode($key)
                        . $colonSeparator
                        . $value;
                    
                    array_push($out, $indent . $this->space . $keyValue);
                }
                array_splice($this->seen, $this->array_indexOf($this->seen, $node), 1);
                return '{' . implode(',', $out) . $indent . '}';
            }
        }
}
