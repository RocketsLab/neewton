# Neewton

Neewton é um gerenciador de módulos para uma aplicação Laravel
com InertiaJs e VueJs.

### Propósito

Este projeto tem o objetivo de deixar uma aplicação Laravel com
InertiaJs com a capacidade de modularizar seus domínios. Um módulo
de permissões, estoque, clientes, produtos e etc...

Todo módulo deve conter os componentes necessários para gerenciar
o estado dos dados nele contido. Também podemos configurar módulos
que dependam de outros e assim garantir que um módulo possa ter
acesso aos dados de outro por esta configuração.

## Instalação

```shell
composer require rocketslab/neewton
```

> Uma observação aqui: esse pacote altera o arquivo `app.js` da sua
> aplicação. Se precisa de algo contido nele, faça um backup e depois
> integre o que precisa ao `app.js` novamente.

### Copiando os assets para a aplicação Laravel

```shell
php artisan neewton:install
```
Se não tiver o composer configurado no sistema pode indicar o caminho
para ele no comando de instalação:

```shell
php artisan neewton:install --composer=<caminho para o composer>
```

---
## Configurando para que a aplicação reconheça os módulos

O **Neewton** registra uma tag blade `@neewtonModules` para que os módulos sejam
localizados e configurados.

Adicione a tag blade no arquivo `app.blade.php` ou no seu próprio
arquivo de layout logo abaixo de `<script src="{{ mix('js/app.js') }}" defer></script>`

*app.blade.php*
```php
...
        <!--  Active neewton modules -->
        @neewtonModules
...
```
Isso requer que seja feito uma limpeza
no cache das views na primeira instalação e toda vez que houver
mudança de algum módulo, seja adicionando ou removendo e tambem
a recompilação dos assets do projeto/modúlos.

```shell
php artisan view:clear && npm run [dev|prod]
```
---

## Configuração

Para adicionar ou remover módulos publique o arquivo de configuração
do **Neewton**

```shell
php artisan vendor:publish --provider="RocketsLab\Neewton\NeewtonServiceProvider"
```

Para adicionar um módulo basta adicionar a classe que configura o módulo
no array `active_modules` no `config/neewton.php`

*Ex:*
```php
<?php

return [
    /*
     * Active modules array, put here each module registration class
     */
    'active_modules' => [
        Module\\Exemplo\\Register::class
    ]
];
```

**A documentação para criação de módulos vai estar disponível aqui:**

[Como criar seu módulos para o Neewton]()

---

®2021 Jorge [@jjsquady](https://github.com/jjsquady) Junior

[RocketsLab](https://github.com/RocketsLab)
