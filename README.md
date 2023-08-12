# Teste Técnico - Agence

Aqui terão os detalhes sobre tecnologias utilizadas e recursos disponíveis neste projeto.

## Visão Geral

O projeto é uma teste técnico que consiste em listar os consultores e clientes de uma determinada empresa e mostrar dados e gráficos a partir dessa listagem. Ele é composto por um frontend desenvolvido com Vue.js e Nuxt.js, um backend construído com PHP 8.1 e Laravel 10, e um banco de dados MySQL 8 para armazenamento de dados.

## Tecnologias Principais

- Backend: PHP 8.1 com Laravel 10
- Documentação da API: Swagger
- Frontend: Vue.js e Nuxt.js
- Banco de Dados: MySQL 8
- Servidor Web: Nginx
- Hospedagem do Backend: Oracle Cloud
- Hospedagem do Frontend: Vercel

## Acesso à API

A API do projeto está acessível através do seguinte endereço:
[https://agence.3am.com.br/api/](https://agence.3am.com.br/api/)

Para mais detalhes sobre como utilizar a API, consulte a documentação disponível em:
[https://agence.3am.com.br/api/documentation](https://agence.3am.com.br/api/documentation)

## Site em Produção

O site em produção do projeto pode ser acessado pelo seguinte link:
[https://site.agence.3am.com.br/](https://site.agence.3am.com.br/)

## Configuração do Backend

O servidor backend está hospedado na Oracle Cloud e utiliza o servidor web Nginx com HTTPS configurado usando Let's Encrypt.

## Configuração do Frontend

O frontend do projeto está hospedado na Vercel.

## Configuração do Banco de Dados

O banco de dados utilizado é o MySQL 8. Devido a requisitos específicos, foi necessário incluir a linha `SET sql_mode='NO_ZERO_DATE';` no arquivo de dump para permitir datas com valores inválidos. Para alimentar o bando de dados, criei uma seed no Laravel, disponível através do comando `php artisan db:seed --class=DataSeedCaol`
