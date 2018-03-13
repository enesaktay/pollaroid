# Pollaroid
Pollaroid is a Symfony and Bulma based website to quickly create polls

## Requirements
[MySQL](https://dev.mysql.com/downloads/installer/)

[PHP](https://www.google.de/search?biw=1920&bih=1006&dcr=0&ei=y_mmWpSACImjsgGOvqbIDw&q=php+7+install&oq=php+7+install&gs_l=psy-ab.3..0i20i263k1l2j0l8.1827.1827.0.1993.1.1.0.0.0.0.97.97.1.1.0....0...1.1.64.psy-ab..0.1.96....0.LO-5RghPDsw&gws_rd=cr)

[Composer](https://getcomposer.org/download/)

[NPM(Node.js)](https://nodejs.org/en/) and/or [Yarn](https://yarnpkg.com/lang/en/docs/install/) 

## Setup

install php packages:
```console
~ composer install
```


install frontend packages(with yarn):
```console
~ yarn install
~ yarn encore dev
```


install frontend packages(with npm):
```console
~ npm install
~ ./node_modules/.bin/encore dev
```

create db and do migrations:
```console
~ bin/console do:da:cr
~ bin/console do:mi:mi
```

start the server:
```console
~ bin/console server:run
```

open the link being shown in the console and you are done