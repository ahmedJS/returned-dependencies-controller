<?php
class cls1{};
class cls2{};

return [ 
 cls2::class => new cls2,
cls1::class => new cls1,
cls1::class => new cls1
 ];