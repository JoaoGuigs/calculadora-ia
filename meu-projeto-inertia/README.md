<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Orcamento Inteligente (Laravel + Inertia + Gemini)

Aplicacao web para ajudar na estimativa de horas de projetos freelance.
Voce descreve o escopo, a IA (Gemini) sugere uma quantidade de horas e o sistema calcula um valor final usando sua tarifa por hora.

### Como funciona

- Tela: `GET/POST /calculadora`
- Entrada:
  - `descricao`: escopo do projeto (usado para chamar a IA)
  - `valor_hora`: valor da hora (numerico)
  - `horas`: horas estimadas (numerico)
- Validacao (Laravel):
  - `valor_hora` e `horas` sao obrigatorios e numericos (min: 0)
- Calculo:
  - `total = (valor_hora * horas) * 0.94`
- Historico:
  - o historico dos calculos fica salvo na sessao em `historico`

### Sugestao via IA (Gemini)

Se `descricao` estiver preenchida, o backend chama o Gemini e solicita um retorno somente em formato numerico:

- Exemplos: `20` ou `15-25`
- Se a descricao for muito vaga: `vago`

A sugestao e exibida na UI junto do resultado.

### Rotas

- `GET /calculadora`: renderiza a pagina com `total` e `historico`
- `POST /calculadora`: valida, calcula o total, salva historico e (se houver) consulta a IA

### Variavel de ambiente

- `GEMINI_API_KEY`: chave da API do Gemini (usada em `config/gemini.php`)

### Rodando localmente

1. PHP:
   - `composer install`
2. JavaScript:
   - `npm install`
3. Suba os servers:
   - `php artisan serve`
   - `npm run dev`

### Observacao de seguranca

O controller desativa verificacao de SSL no Guzzle (`'verify' => false`).
Isso pode ser aceitavel para desenvolvimento, mas nao recomendado para producao.
