<?hh // strict
namespace SYB;
use type SYB\GenericQ;
interface Data {
	public function gmapQ<Tv>(GenericQ<Tv> $query): vec<Tv>;
}