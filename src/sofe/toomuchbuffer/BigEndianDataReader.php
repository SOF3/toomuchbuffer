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

use raklib\Binary;

class BigEndianDataReader extends Reader implements DataReader{
	public function readBool() : bool{
		return $this->stream->read(1) !== "\0";
	}

	public function readByte(bool $signed = true) : int{
		$byte = ord($this->stream->read(1));
		return $signed ? ($byte << 56 >> 56) : $byte;
	}

	public function readShort(bool $signed = true) : int{
		$short = unpack("n", $this->stream->read(2))[1];
		return $signed ? ($short << 48 >> 48) : $short;
	}

	public function readTriad() : int{
		return unpack("N", "\0" . $this->stream->read(3))[1];
	}

	public function readInt(bool $signed = true) : int{
		return unpack("N", $this->stream->read(4))[1];
	}

	public function readLong(bool $signed = true) : int{
		$int = unpack("N*", $this->stream->read(8));
		return ($int[1] << 32) | $int[2];
	}

	public function readFloat() : float{
		return unpack("f", Binary::BIG_ENDIAN === ENDIANNESS ? $this->stream->read(4) : strrev($this->stream->read(4)))[1];
	}

	public function readDouble() : float{
		return unpack("d", Binary::BIG_ENDIAN === ENDIANNESS ? $this->stream->read(8) : strrev($this->stream->read(8)))[1];
	}

	public function readVarInt(bool $signed = true) : int{
		$value = 0;
		for($i = 0; $i <= 35; $i += 7){
			$b = ord($this->stream->read(1));
			$value |= (($b & 0x7f) << $i);

			if(($b & 0x80) === 0){
				return $signed ? (((($value << 63) >> 63) ^ $value) >> 1) ^ ($value & (1 << 63)) : $value;
			}
		}

		throw new \InvalidArgumentException("VarInt did not terminate after 5 bytes!");
	}

	public function readVarLong(bool $signed = true) : int{
		$value = 0;
		for($i = 0; $i <= 63; $i += 7){
			$b = ord($this->stream->read(1));
			$value |= (($b & 0x7f) << $i);

			if(($b & 0x80) === 0){
				return $signed ? (((($value << 63) >> 63) ^ $value) >> 1) ^ ($value & (1 << 63)) : $value;
			}
		}

		throw new \InvalidArgumentException("VarLong did not terminate after 10 bytes!");
	}
}
