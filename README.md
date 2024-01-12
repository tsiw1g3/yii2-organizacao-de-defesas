<p align="center">
    <a href="https://github.com/tsiw1g3/organizacao-de-defesas" target="_blank">
        <img src="LOGO.png" height="100px">
    </a>
    <h1 align="center">Back end - API</h1>
    <br>
</p>

Back end do projeto de Organização de Defesas com o Framework [Yii 2](http://www.yiiframework.com/) a.

O modelo contém os recursos essenciais para o funcionamento do sistema Moon, incluindo login / logout do usuário entre outros.


[![Latest Stable Version](https://img.shields.io/packagist/v/yiisoft/yii2-app-basic.svg)](https://packagist.org/packages/yiisoft/yii2-app-basic)
[![Total Downloads](https://img.shields.io/packagist/dt/yiisoft/yii2-app-basic.svg)](https://packagist.org/packages/yiisoft/yii2-app-basic)
[![build](https://github.com/yiisoft/yii2-app-basic/workflows/build/badge.svg)](https://github.com/yiisoft/yii2-app-basic/actions?query=workflow%3Abuild)

ESTRUTURA DE DIRETÓRIOS
-------------------

      config/             Contém configurações da aplicação
      controllers/        Contém classes de controladores dos Models
      models/             Contém as classes de modelo
      runtime/            Contém os arquivos gerados durante a execução. Ex.: log's
      tests/              Contém testes essenciais para o funcionamento básico da aplicação
      vendor/             Contém as dependências da aplicação
      web/                Contém o script de entrada e recursos da Web



REQUIREMENTS
------------

O requisito mínimo para este modelo de projeto que o seu servidor Web suporte a aplicação é o PHP 5.6.0.


INSTALAÇÃO
------------

### Instalação via Composer

Se você não possui o [Composer](http://getcomposer.org/), você pode instalá-lo seguindo as instruções
em [getcomposer.org](http://getcomposer.org/doc/00-intro.md#installation-nix).

Para instalar as dependências deste projeto, execute o comando abaixo no caminho organizacao-de-defesas/yii2/.

~~~
composer update
~~~

Agora você deve ser capaz de realizar requisições para API com o seguinte host:

~~~
http://localhost:8080/
~~~

CONFIGURAÇÃO
-------------

### Banco de dados

Edite o arquivo `config/db.php` com o dado correto, por exemplo:

```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=yii2basic',
    'username' => 'root',
    'password' => '1234',
    'charset' => 'utf8',
];
```

### Execução do projeto

Para executar o projeto, basta utilizar o comando `docker-compose up`.

**NOTES:**
- Yii não criará o banco de dados para você, isso tem que ser feito manualmente antes que você possa acessá-lo.
- Verifique e edite os outros arquivos no diretório `config /` para personalizar seu aplicativo conforme necessário.
- Consulte o README no diretório `tests` para informações específicas para testes de aplicativos básicos.