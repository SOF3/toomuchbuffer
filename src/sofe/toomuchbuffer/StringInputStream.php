<?php

/*
 *
 * toomuchbuffer
 *
 * Copyright (C) 2017 SOFe
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 */

declare(strict_types=1);

namespace sofe\toomuchbuffer;

class StringInputStream implements InputStream{
	/** @var string */
	private $string;
	/** @var int */
	private $offset = 0;

	public function __construct(string $string){
		$this->string = $string;
	}

	public function read(int $length) : string{
		if(strlen($this->string) < $this->offset + $length){
			throw new \UnderflowException("End of string reached");
		}
		$ret = substr($this->string, $this->offset, $length);
		$this->offset += $length;
		return $ret;
	}

	public function close(){
	}
}
