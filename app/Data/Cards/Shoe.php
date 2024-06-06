<?php
namespace App\Data\Cards;

/**
 * A Shoe holds multiple Decks
 * 
 * @author hoyos
 *
 */
interface Shoe {
    
    function shuffle(): Shoe;
    function draw(): Card;
    function place(Card $card): Shoe;
    function reset(): Shoe;
    
    /**
     * A comparison function, which imposes a total ordering on some collection of objects.
     *
     * The ordering imposed by a comparator c on a set of elements S is said to be consistent with
     * equals if and only if c.compare(e1, e2)==0 has the same boolean value as e1 == e2 for every
     * e1 and e2 in S.
     *
     * Compares its two arguments for order. Returns a negative integer, zero, or a positive integer
     * as the first argument is less than, equal to, or greater than the second.
     *
     * In the foregoing description, the notation sgn(expression) designates the mathematical signum
     * function, which is defined to return one of -1, 0, or 1 according to whether the value of
     * expression is negative, zero or positive.
     *
     * The implementor must ensure that sgn(compare(x, y)) == -sgn(compare(y, x)) for all x and y.
     * (This implies that compare(x, y) must throw an exception if and only if compare(y, x) throws
     * an exception.)
     *
     * The implementor must also ensure that the relation is transitive: ((compare(x, y)>0) &&
     * (compare(y, z)>0)) implies compare(x, z)>0.
     *
     * Finally, the implementor must ensure that compare(x, y)==0 implies that
     * sgn(compare(x, z))==sgn(compare(y, z)) for all z.
     *
     * It is generally the case, but not strictly required that (compare(x, y)==0) == (x == y).
     * Generally speaking, any comparator that violates this condition should clearly indicate this
     * fact. The recommended language is "Note: this comparator imposes orderings that are
     * inconsistent with equals."
     *
     * @param Card $o1 - the first card to be compared.
     * @param Card $o2 - the second object to be compared.
     *
     * @return int a negative integer, zero, or a positive integer as the first card is less
     *          than, equal to, or greater than the second.
     */
    function compare(Card $o1, Card $o2): int;
    
}

