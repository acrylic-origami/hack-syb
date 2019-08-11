<?hh // strict
namespace SYB;

use namespace HH\Lib\{C, Vec};
use type SYB\GenericQ;

require_once(__DIR__ . '/../vendor/hh_autoload.hh');

// <<__Enforceable>>reify T => Typeable T
function cast<<<__Enforceable>>reify T>(mixed $v): ?T {
	return $v is T ? $v : null;
}
function mkQ<<<__Enforceable>>reify T, Tv>(Tv $init, (function(T): Tv) $query, mixed $term): Tv {
	// aka `return ($term is T) ? $query($term) : $init;`, but this is how Haskell does it so let's keep it consistent
	$term_cast = cast<T>($term);
	if(!\is_null($term_cast))
		return $query($term_cast);
	else
		return $init;
}
function everything<Tv>(
	(function(Tv, Tv): Tv) $combine,
	GenericQ<Tv> $query,
	mixed $term
): Tv {
	$res = $query($term);
	if($term is Data)
		return C\reduce($term->gmapQ($v ==> everything($combine, $query, $v)), $combine, $res);
	elseif($term is Traversable<_>) // implement this common interface of what should be Data
		return C\reduce(Vec\map($term, $v ==> everything($combine, $query, $v)), $combine, $res);
	else
		return $res;
}