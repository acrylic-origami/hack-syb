# hack-syb: Scrap your [Hack] Boilerplate

HHVM 4.14.0 brought `is`, which [replaced `instanceof` to refine types](https://hhvm.com/blog/2019/07/15/hhvm-4.14.0.html#instanceof-refinement) and allows code to refine arbitrary types towards fixed ones.

HHVM 4.17.0 brought [reified generics](https://docs.hhvm.com/hack/generics/reified-generics), which allows code to refine arbitrary types towards generic ones.

Together, the two typesafe coercion methods form a foundation for [generic programming](https://wiki.haskell.org/Generics), which describes a style of programming of being abstracted away from the specifics of concrete data structures, rather than the common meaning relating to polymorphism.

In particular, the new runtime power of Hack seems to have enabled significant parts of the generic programming framework ["Scrap Your Boilerplate" (SYB)](http://hackage.haskell.org/package/syb) to be implemented in Hack. SYB was developed originally by Ralf Lammel and Simon Peyton Jones for Haskell, bringing [`cast` to the language as a compiler-level feature](http://hackage.haskell.org/package/base-4.11.1.0/docs/Data-Typeable.html), as well as methods to modify and query arbitrary data structures needing only the knowledge of what you're looking for. In particular, the methods [`everywhere`](https://hackage.haskell.org/package/syb-0.7.1/docs/Data-Generics-Schemes.html#v:everywhere) (generic mutation) and [`everything`](https://hackage.haskell.org/package/syb-0.7.1/docs/Data-Generics-Schemes.html#v:everything) (generic querying) are attempted here.

---

## Usage

The signatures are kept close to the Haskell implementation. Depending on your perspective, Hack is either more or less flexible than Haskell. In the former sense, every class is `Typeable` (i.e. `<<__Enforceable>>`) and the generic methods on `Data` can be generalized to `mixed` because the method can check for `Data` instances at runtime. In the latter sense, it's likely this is true because there aren't any crazy self-recursive types or magic monads or anything like that in Hack.

To this point, `everything` is typed as so:

```hack
function everything<Tv>(
	(function(Tv, Tv): Tv) $combine,
	GenericQ<Tv> $query,
	mixed $term
): Tv;
```

Typically, `everything` is combined with `mkQ`, a method that transforms a type-specific query `a -> b` into a generic query `∀a. a -> b`. Usage usually look like this:

```hack
// given `(function(T_in): T_out) $typed_query` for concrete `T_in`, `T_out`
everything(fun("combine"), $w ==> mkQ<T_in, T_out>($default, $typed_query, $w), $root);
```

Consult [the original paper](https://www.microsoft.com/en-us/research/publication/scrap-your-boilerplate-a-practical-approach-to-generic-programming/) for this and much more justification for its design and your design.