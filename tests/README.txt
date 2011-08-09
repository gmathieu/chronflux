JOBS TEST CASES
==============

Legend:
-: job with task ID 1
x: job with task ID 2
*: deletion

hours                       12    1    2    3    4    5    6    7    8
======================================================================
fixture data                       ---- ---- ---- -x--      ----
create                                                             --
append                                                 --
prepend                       ----
prepend overlap               ---- -
combine existing                                       ----
engulf existing               ---- ---- ---- ---- ---- ---- ---- ----

hours                       12    1    2    3    4    5    6    7    8
======================================================================
fixture data                       ---- ---- ---- -x--      ----
append other                                           xx
append other overlap                                 x xx
prepend other                 xxxx
prepend other overlap         xxxx x
separate existing                             xx
kill multiple times                       xx xxxx xxxx xxxx xxx
overwrite                                                   xxxx
overwrite and combine                               xx xxxx xx

hours                       12    1    2    3    4    5    6    7    8
======================================================================
fixture data                       ---- ---- ---- -x--      ----
delete multiple                    **** **** **** **** *
delete beginning                 * **
delete end                                      * *
delete middle                           ****