<?php
namespace Krishna\API;

use Krishna\Utilities\Debugger;

final class Consumable {
	public int $count;
	public bool $has_more;
	private ?int $peeked = null;
	public function __construct(private array &$source) {
		$this->count = count($this->source);
		$this->has_more = $this->count > 0;
	}
	public function peek(int $count = 1) : ?array {
		if($this->count < $count) {
			return $this->peeked = null;
		}
		$this->peeked = $count;
		return array_slice($this->source, 0, $count);
	}
	public function consume_peeked() {
		if($this->peeked !== null) {
			array_splice($this->source, 0, $this->peeked, null);
			$this->count = count($this->source);
			$this->has_more = $this->count > 0;
			$this->peeked = null;
		}
	}
	public function consume(int $count = 1) : ?array {
		if($this->count < $count) {
			return null;
		}
		$items = array_splice($this->source, 0, $count, null);
		$this->count = count($this->source);
		$this->has_more = $this->count > 0;
		return $items;
	}
	public function log(string $name = 'Consumable') {
		Debugger::dump($name, $this->source);
	}
}