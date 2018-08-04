# Json Stable Stringify

deterministic version of `JSON.stringify()` so you can get a consistent hash
from stringified results

You can also pass in a custom comparison function.

### Don't want to read?

```
composer require kryuu-common/json-stable-stringify
```

# example

``` php

use KryuuCommon\JsonStableStringify as Json;

$obj = json_decode(
   '{
        "c": [
            {
                "zz": 12,
                "z": {
                    "z2": "11 - hello",
                    "z1": 10
                }
            },
            "13 - hello",
            14
        ],
        "a": 1,
        "d": "15 - mullama",
        "b": {
            "bb": [
                {
                    "bbc": 9,
                    "bbb": 8,
                    "bba": [
                        5,
                        6,
                        7
                    ]
                },
                "3 - Hello",
                4
            ],
            "ba": 2
        }
    }';
);
print (new Json())->stringify($obj);
```

output:

```
{"a":1,"b":{"ba":2,"bb":[{"bba":[5,6,7],"bbb":8,"bbc":9},"3 - Hello",4]},"c":[{"z":{"z1":10,"z2":"11 - hello"},"zz":12},"13 - hello",14],"d":"15 - mullama"}
```

# methods

```
string JsonStableStringify->stringify(mixed obj [, Array opts]);
```

Return a deterministic stringified string `str` from the object/array `obj`.

## options

### cmp

If `opts` is given, you can supply an `opts['cmp']` to have a custom comparison
function for object keys. Your function `opts['cmp']` is called with these
parameters:

``` php
opts['cmp'] = function ([ key => akey, value => avalue ], [ key => bkey, value => bvalue ])
```

For example, to sort on the object key names in reverse order you could write:

``` php

use KryuuCommon\JsonStableStringify as Json;


$obj = json_decode('{ c: 8, b: [{z:6,y:5,x:4},7], a: 3 }');
$s = (new Json())->stringify((obj, function (a, b) {
    return a.key < b.key ? 1 : -1;
});

print $s;

```

which results in the output string:

```
{"c":8,"b":[{"z":6,"y":5,"x":4},7],"a":3}
```

Or if you wanted to sort on the object values in reverse order, you could write:

``` php
use KryuuCommon\JsonStableStringify as Json;


$obj = json_decode('{ d: 6, c: 5, b: [{z:3,y:2,x:1},9], a: 10 }');
$s = (new Json())->stringify((obj, function (a, b) {
    return a.key < b.key ? 1 : -1;
});

print $s;
```

which outputs:

```
{"d":6,"c":5,"b":[{"z":3,"y":2,"x":1},9],"a":10}
```


# install

With [composer](https://getcomposer.org/) do:

```
composer require kryuu-common/json-stable-stringify
```

# license

MIT
