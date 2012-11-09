<?php

namespace CHH\FileUtils;

use SplStack;

class PathStack
{
    protected $root;
    protected $paths;
    protected $extensions;

    function __construct($root = null, $paths = array())
    {
        if (null === $this->root) {
            $this->root = getcwd();
        }

        $this->paths = new SplStack;
        $this->extensions = new SplStack;

        if ($paths !== null) {
            $this->appendPaths($paths);
        }
    }

    function extensions()
    {
        return $this->extensions;
    }

    function paths()
    {
        return $this->paths;
    }

    function appendPaths($paths)
    {
        return $this->push($paths);
    }

    function prependPaths($paths)
    {
        return $this->unshift($paths);
    }

    function appendExtensions($extensions)
    {
        foreach ((array) $extensions as $ext) {
            $this->extensions->push(Path::normalizeExtension($ext));
        }

        return $this;
    }

    function prependExtensions($extensions)
    {
        foreach ((array) $extensions as $ext) {
            $this->extensions->unshift(Path::normalizeExtension($ext));
        }

        return $this;
    }

    function push($paths)
    {
        $paths = (array) $paths;

        foreach ($paths as $path) {
            $this->paths->push(rtrim($path, '\/'));
        }

        return $this;
    }

    function unshift($paths)
    {
        $paths = (array) $paths;

        foreach ($paths as $path) {
            $this->paths->unshift(rtrim($path, '\/'));
        }

        return $this;
    }

    function find($file, $dirs=null)
    {
        if(is_string($dirs)){
            $dirs = array($dirs);
        }

        if(is_array($dirs) && count($dirs)){
            foreach ($dirs as $loadPath){
                $p = $this->find_file(rtrim($loadPath,'\/'), $file);
                if($p){
                    return $p;
                }
            }
        }
        foreach ($this->paths() as $loadPath){
            $p = $this->find_file($loadPath, $file);
            if($p){
                return $p;
            }
        }

        return false;
    }

    function find_file($loadPath, $file){
        $path = Path::Join( array( $loadPath,$file ) );
        if (file_exists($path)) {
            return $path;
        } else {
            foreach ($this->extensions() as $ext) {
                if (file_exists($path . $ext)) {
                    return $path . $ext;
                }
            }
        }
        return false;        
    }

    function __toString()
    {
        return join(PATH_SEPARATOR, iterator_to_array($this));
    }

    function toArray()
    {
        return iterator_to_array($this->paths);
    }
}

