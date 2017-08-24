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

class BufferedRandomAccessFile implements InputStream, OutputStream{
	/** @var resource */
	private $stream;
	private $bufferSize;

	private $buffer = "";
	private $bufferNumber = 0;
	private $bufferPointer = 0;

	private $maxBufferNumber = 0;
	private $toppingPointer = 0;

	public function __construct(string $file, int $bufferExp = 12){
		$this->stream = fopen($file, "w+b");
		$this->bufferSize = 1 << $bufferExp;
	}

	public function write(string $bytes){
		$length = strlen($bytes);
		$remaining = $this->bufferSize - $this->bufferPointer;
		if($length > $remaining){
			$this->write(substr($bytes, 0, $remaining));
			$this->write(substr($bytes, $remaining));
			return;
		}
		$this->buffer = substr_replace($this->buffer, $bytes, $this->bufferPointer, $length);
		$this->bufferPointer += $length;
		$this->bufferPointer &= $this->bufferSize - 1;
		if($this->maxBufferNumber === $this->bufferNumber and strlen($this->buffer) === $this->bufferPointer){
			// pointer at the end of buffer
			$this->toppingPointer = $this->bufferPointer;
		}
		if($this->bufferPointer === 0){
			$this->flush();
			++$this->bufferNumber;
			if($this->bufferNumber === $this->maxBufferNumber){
				$this->buffer = $this->toppingPointer === 0 ? "" : fread($this->stream, $this->toppingPointer);
			}elseif($this->bufferNumber === $this->maxBufferNumber + 1){
				++$this->maxBufferNumber;
				$this->buffer = "";
			}else{
				$this->buffer = fread($this->stream, $this->bufferSize);
			}
		}
	}

	public function flush(){
		fseek($this->stream, $this->bufferSize * $this->bufferNumber);
		fwrite($this->stream, $this->buffer);
	}

	public function close(){
		$this->flush();
		fclose($this->stream);
	}

	public function read(int $length) : string{
		if($length === 0){
			return "";
		}

		if($length < 0){
			// read backwards
			$length = -$length;
			if($this->bufferPointer === 0){
				// feed buffer
				if($this->bufferNumber === 0){
					throw new \UnderflowException("Start of stream reached");
				}
				$this->bufferPointer = $this->bufferSize;
				--$this->bufferNumber;
				$this->seekBuffer($this->bufferNumber, $this->bufferSize);
			}

			if($length > $this->bufferPointer){
				$back = substr($this->buffer, 0, $this->bufferPointer);
				$remain = $length - $this->bufferPointer;
				$this->bufferPointer = 0;
				return $this->read(-$remain) . $back;
			}

			return substr($this->buffer, $this->bufferPointer -= $length, $length);
		}

		if($this->bufferNumber === $this->maxBufferNumber && $length > $this->toppingPointer - $this->bufferPointer){
			throw new \UnderflowException("End of stream reached");
		}
		if($length > strlen($this->buffer) - $this->bufferPointer){
			$read = strlen($this->buffer) - $this->bufferPointer;
			return $this->read($read) . $this->read($length - $read);
		}

		$ret = substr($this->buffer, $this->bufferPointer, $length);
		$this->bufferPointer += $length;

		if($this->bufferPointer === strlen($this->buffer)){
			$this->seekBuffer($this->bufferNumber + 1);
		}

		return $ret;
	}

	public function seekBuffer(int $bufferNumber, int $bufferPointer = 0){
		if($bufferNumber < 0){
			$bufferNumber += $this->maxBufferNumber + 1;
		}
		$this->bufferNumber = $bufferNumber;
		fseek($this->stream, $this->bufferSize * $bufferNumber);
		if($this->maxBufferNumber === $bufferNumber){
			$this->buffer = $this->toppingPointer === 0 ? "" : fread($this->stream, $this->toppingPointer);
		}else{
			$this->buffer = fread($this->stream, $this->bufferSize);
		}
		$this->bufferPointer = $bufferPointer;
	}

	public function getBufferSize() : int{
		return $this->bufferSize;
	}

	public function getBufferMask() : int{
		return $this->bufferSize - 1;
	}

	public function getTotalSize() : int{
		return $this->maxBufferNumber * $this->bufferSize + $this->toppingPointer;
	}

	public function tell() : int{
		return $this->bufferNumber * $this->bufferSize + $this->bufferPointer;
	}

	public function __debugInfo(){
		return [
			"stream" => $this->stream,
			"bufferSize" => $this->bufferSize,
			"bin2hex(buffer)" => bin2hex($this->buffer),
			"bufferNumber" => $this->bufferNumber,
			"bufferPointer" => $this->bufferPointer,
			"maxBufferNumber" => $this->maxBufferNumber,
			"toppingPointer" => $this->toppingPointer,
		];
	}
}
