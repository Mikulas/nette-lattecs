LatteCS
=======

[![Build Status](https://travis-ci.org/Mikulas/nette-lattecs.svg?branch=master)](https://travis-ci.org/Mikulas/nette-lattecs)

Coding standard validator for [Latte](https://github.com/nette/latte).

Usage
-----

```sh
composer require --dev mikulas/lattecs '~0.1.0-alpha'
php vendor/bin/lattecs app/
```

Current rule set
----------------

- each block definition must have `{** *}` doc comment with properly formatted `@param` annotations
  - blocks closed on the same line are not forced to have annotations (such as the title or scripts blocks)

Example
-------

```
➜  lattecs git:(master) ✗ php bin/lattecs tests/fixtures/
tests/fixtures/invalid_param.latte
  line 5: Invalid annotation '@param xoxo', expected '@param type $name'

tests/fixtures/invalid_param_dollar.latte
  line 5: Invalid annotation '@param string basePath', variable name must start with '$'

tests/fixtures/missing.latte
  line 3: Block #content is not properly annotated

tests/fixtures/missing_params.latte
  line 5: Block #content is annotated, but no @param was specified.

tests/fixtures/missing_star.latte
  line 5: Lattedoc does not start with two stars but it should
```

License
-------

The MIT License (MIT)

Copyright (c) 2014 Mikulas Dite

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
