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

use pocketmine\utils\Binary;

class LittleEndianDataWriter extends Writer implements DataWriter{
	public function writeBool(bool $value){
		$this->stream->write($value ? "\1" : "\0");
	}

	public function writeByte(int $value){
		$this->stream->write(chr($value));
	}

	public function writeShort(int $value){
		$this->stream->write(pack("v", $value));
	}

	public function writeTriad(int $value){
		$this->stream->write(substr(pack("V", $value), 0, 3));
	}

	public function writeInt(int $value){
		$this->stream->write(pack("V", $value));
	}

	public function writeLong(int $value){
		$this->stream->write(pack("VV", $value & 0xFFFFFFFF, $value >> 32));
	}

	public function writeVarInt(int $value, bool $signed = true){
		if($signed){
			$value = $value << 32 >> 32;
			$value = ($value << 1) ^ ($value >> 31);
		}
		$value &= 0xffffffff;
		for($i = 0; $i < 5; ++$i){
			if(($value >> 7) !== 0){
				$this->writeByte($value | 0x80);
			}else{
				$this->writeByte($value & 0x7F);
				return;
			}

			$value = (($value >> 7) & (PHP_INT_MAX >> 6));
		}

		throw new \InvalidArgumentException("Value too large to be encoded as a VarInt");
	}

	public function writeVarLong(int $value, bool $signed = true){
		if($signed){
			$value = ($value << 1) ^ ($value >> 63);
		}
		for($i = 0; $i < 10; ++$i){
			if(($value >> 7) !== 0){
				$this->writeByte($value | 0x80);
			}else{
				$this->writeByte($value & 0x7F);
				return;
			}

			$value = (($value >> 7) & (PHP_INT_MAX >> 6));
		}

		throw new \InvalidArgumentException("Value too large to be encoded as a VarLong");
	}

	public function writeFloat(float $value){
		$this->stream->write(ENDIANNESS === Binary::LITTLE_ENDIAN ? pack("f", $value) : strrev(pack("f", $value)));
	}

	public function writeDouble(float $value){
		$this->stream->write(ENDIANNESS === Binary::LITTLE_ENDIAN ? pack("d", $value) : strrev(pack("d", $value)));
	}
}
