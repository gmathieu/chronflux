JOBS TEST CASES
==============

Legend:
-: project 1, task ID 1
x: project 1, task ID 2
_: project 2, task ID 1
*: deletion

hours                       12    1    2    3    4    5    6    7    8
======================================================================
fixture data                       ---- ---- ---- -x--      --__
create                                                             --
append                                                 --
prepend                       ----
prepend overlap               ---- -
combine existing                                       ----
engulf existing               ---- ---- ---- ---- ---- ---- ---- ----

hours                       12    1    2    3    4    5    6    7    8
======================================================================
fixture data                       ---- ---- ---- -x--      --__
append other                                           xx
append other overlap                                 x xx
prepend other                 xxxx
prepend other overlap         xxxx x
separate existing                             xx
kill multiple times                       xx xxxx xxxx xxxx xxx
overwrite                                                   xxxx
overwrite and combine                               xx xxxx xxx

hours                       12    1    2    3    4    5    6    7    8
======================================================================
fixture data                       ---- ---- ---- -x--      --__
delete multiple                    **** **** **** **** *
delete beginning                 * **
delete end                                      * *
delete middle                           ****
test delete project 1              **** **** **** **** **** ****