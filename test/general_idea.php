<?hh // strict
use namespace HH\Lib\{C, Str, Vec, Dict};

class Box<T> implements SYB\Data {
	public function __construct(public T $v) {}
	public function gmapQ<Tv>(SYB\GenericQ<Tv> $query): vec<Tv> {
		return vec[$query($this->v)];
	}
}
class Something implements SYB\Data {
	public int $foo = 42;
	public string $bar = "Hello, world.";
	public Box<vec<int>> $baz;
	public function __construct() {
		$this->baz = new Box(vec[1, 1, 2, 3, 5, 8]);
	}
	public function gmapQ<Tv>(SYB\GenericQ<Tv> $query): vec<Tv> {
		return Vec\concat(
			vec[$query($this->foo)],
			vec[$query($this->bar)],
			$this->baz->gmapQ($query)
		);
	}
}

<<__EntryPoint>>
function foo(): void {
	var_dump(SYB\everything(($x, $y) ==> $x + $y, $w ==> SYB\mkQ<int, int>(0, $v ==> $v + 2, $w), new Something()));
}