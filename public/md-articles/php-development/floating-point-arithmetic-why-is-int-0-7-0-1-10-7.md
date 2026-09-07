---
title: "Floating-Point Arithmetic - Why is (int)((0.7+0.1)*10) = 7 ?"
description: "An explanation of why PHP's floating-point arithmetic and int casting can produce unexpected results like (int)((0.7+0.1)*10) = 7 instead of 8."
author: "Gabi DJ"
date_published: "2016-01-26"
canonical_url: "https://www.dotkernel.com/php-development/floating-point-arithmetic-why-is-int-0-7-0-1-10-7/"
category: "PHP Development"
language: "en"
---

# Floating-Point Arithmetic - Why is (int)((0.7+0.1)*10) = 7 ?

## TL;DR
This applies to PHP 5.x and PHP 7.
Floating-point arithmetic doesn't always produce the results you'd expect, especially when casting values to int, because numbers like 0.7 and 0.1 cannot be represented exactly in binary.
The result is that `(int)((0.7+0.1)*10)` evaluates to 7 instead of the mathematically expected 8.

This article applies to **PHP 5.x** but also to **PHP 7**

While using **floating-point arithmetic** you might have noticed that not all the calculus results are as expected, this can usually be observed when casting values.

So the output for **(0.7 + 0.1) * 10** is: 
`var_dump((0.7+0.1)*10); # float(8)
var_dump(intval((0.7+0.1)*10)); # int(7)`

Now let's try it with 0.6 instead of 0.7 
`var_dump((0.6+0.1)*10); # float(7)
var_dump(intval((0.6+0.1)*10)); # int(7)`

## How does the CPU understand these numbers?

The CPU makes the calculations binary, the floating point numbers are represented as follows:

IEEE Short Real: **32 bits**     1 bit for the sign, 8 bits for the exponent, and 23 bits for the mantissa. Also called single precision. IEEE Long  Real: **64 bits**     1 bit for the sign, 11 bits for the exponent, and 52 bits for the mantissa. Also called double precision.

The numbers that can easily be represented binary are: 1/(2^1). 1/(2^2) . 1/(2^3), 1/(2^4) etc. This because they have mantissa equal to 1 (enconded as 0).

The number values can only be represented exactly if they can be represented by this formula:  **exponent** * **mantissa**

The **mantissa** is the number which the exponent is multiplied to. The mantissa value is **1 + 1/rb**, where **rb** is the reverse binary interpretation

Let's take the number 3.5 for example.

The float numbers are **sign** * **exponent** * **mantissa. ** 3.5 = **1** * **2** * **1.75** .

**Mantissa: 1 + 11**000000000000000000000 -> 1/(2^0) + **1**/(2^1) + **1**/(2^2) + **0***(2^3) + ... + **0***(2^23) -> 1 + 0.5 + 0.25 -> 1.75

Some numbers cannot be represented exactly (such as  0.99999999)

To learn more about how IEEE Real numbers are formed follow [this link](http://kipirvine.com/asm/workbook/floating_tut.htm).

## Why does this happen?

Using this [IEEE 754 converter](http://www.h-schmidt.net/FloatConverter/IEEE754.html) we have found out that:

**0.7** is actually represented as **0.699999988079071**

**0.1** is actually represented as **0.10000000149011612**

If we add these two values we will obtain **0.7999999895691871**

And if we multiply it by 10 we obtain **7.999999895691871** which if casted to int is **7** the same way **3.5** is **3** if casted to int.

The other example still shows 7 because **0.6** is actually represented as **0.6000000238418579** and  (0.6000000238418579 + 0.10000000149011612)*10 is **7,00000025331974**

 

## But still ...

If we use **echo** and **var_dump** or if applying mathematical operations, PHP automatically adjusts the values, but **intval** and casting to **int** work on their bits before the values were adjusted

`var_dump((0.7+0.1)*10); # float(8)
var_dump(intval( ((0.7+0.1)*10) ) );   # int(7)
var_dump(intval( ((0.7+0.1)*10)+1 ) ); # int(9)`

If these values are very important for your project you can get the correct values we by using the [**BCMath PHP Extension**](http://php.net/manual/en/book.bc.php).

For a more technical & mathematical approach read [this document](http://docs.oracle.com/cd/E19957-01/806-3568/ncg_goldberg.html).

## FAQ

**Q: Why does (int)((0.70.1)*10) return 7 instead of 8?**
A: Because 0.7 is actually represented internally as 0.699999988079071 and 0.1 as 0.10000000149011612. Adding them gives 0.7999999895691871, and multiplying by 10 gives 7.999999895691871, which truncates to 7 when cast to int.

**Q: Does the same rounding issue happen with 0.6 instead of 0.7?**
A: Yes. 0.6 is represented as 0.6000000238418579, and (0.6000000238418579 + 0.10000000149011612) * 10 equals about 7.00000025331974, which also truncates to int(7).

**Q: Why do var_dump and echo show the expected value while intval/int casting does not?**
A: When using echo, var_dump, or mathematical operations, PHP automatically adjusts the displayed values. However, intval and casting to int operate on the underlying bits before those values were adjusted, which is why they can produce a different (truncated) result.

**Q: How are floating-point numbers represented at the CPU level?**
A: The CPU performs calculations in binary. IEEE Short Real (single precision) uses 32 bits: 1 sign bit, 8 exponent bits, and 23 mantissa bits. IEEE Long Real (double precision) uses 64 bits: 1 sign bit, 11 exponent bits, and 52 mantissa bits. A number can only be represented exactly if it can be expressed as exponent * mantissa; numbers like 0.99999999 cannot be represented exactly.

**Q: Is there a way to get accurate results for calculations like this in PHP?**
A: If precise values matter for your project, the article recommends using the BCMath PHP Extension to get correct results.
