# Simple PHP Machine Learning exercises.

https://www.youtube.com/watch?v=bw_v1Sb-R7I&list=PLIorEuqMFFjOIqlc76dlcbjTBGDjvj7in

Using some csv input data, we make some predictions based on PHP.

Start by installing the only dependency `php-ai/php-ml`.

```
composer install
```

Run this code directly in the console with:

```
php index.php
php insurance.php
php wine.php
php iris.php
```

If you have problems of permissions, you will need to grant them for the vendor folder:

`find /path/to//php-ml/vendor -type f -exec chmod +x {} \`
