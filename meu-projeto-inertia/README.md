# Orçamento Inteligente

Calculadora de orçamento com IA para estimativa de projetos. Descreva o escopo e use o Google Gemini para obter sugestões de tarefas e horas estimadas.

## Stack

- **Backend:** Laravel 12, PHP 8.2+
- **Frontend:** React 18, Inertia.js, TailwindCSS
- **IA:** Google Gemini API

## Requisitos

- PHP 8.2+
- Composer
- Node.js 18+
- Chave de API do Google Gemini

## Instalação

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Configure a chave do Gemini no `.env`:

```
GEMINI_API_KEY=sua_chave_aqui
```

## Executar

```bash
# Terminal 1
php artisan serve

# Terminal 2
npm install
npm run dev
```

Acesse: http://localhost:8000/calculadora

## Funcionalidades

- **Estimativa de orçamento:** informe valor/hora e horas para calcular o total
- **Sugestão por IA:** descreva o escopo e receba decomposição de tarefas com horas estimadas
- **Refinar:** ajuste a sugestão anterior com novas instruções (ex: "reduza as horas de testes")

## Rotas

| Método | Rota      | Descrição                    |
|--------|-----------|------------------------------|
| GET    | /calculadora | Exibe a calculadora        |
| POST   | /calculadora | Processa cálculo e IA      |
