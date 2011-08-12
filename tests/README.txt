USER PROJECTS TEST CASES
========================
John has 2 active projects and 1 inactive:
    - Website has jobs associated on 2011-01-01
    - E-commerce has jobs associated on 2011-01-01
    - Services (inactive) has no jobs
Jen has 1 inactive Website with jobs associated on 2011-01-01
Jack has 1 active Services with no jobs

JOBS TEST CASES
===============

All data below is tested on John's Website and E-Commerce projects on 2011-01-01

Legend:
- : Website project - Front end task
x : Website project - Back end task
_ : E-Commerce project - Front end task
* : deletion

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