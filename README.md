# Chef da Geladeira — Antidesperdício

O Chef da Geladeira é uma aplicação web desenvolvida em PHP estruturado, sem o uso de Inteligência Artificial ou dependências externas. O objetivo do projeto é combater o desperdício de alimentos, permitindo que o usuário informe os ingredientes disponíveis em sua casa e receba instantaneamente sugestões de receitas que priorizam o uso desses itens.

---

## Funcionalidades

* **Busca por Texto:** Permite digitar os ingredientes separados por vírgula (o sistema padroniza letras maiúsculas e remove acentos automaticamente).
* **Seleção por Cliques Rápidos:** Apresenta uma seção de tags gerada dinamicamente com base em todos os ingredientes cadastrados no sistema.
* **Cálculo de Relevância (Match %):** O sistema calcula o percentual de aproveitamento de cada prato e ordena os resultados do maior para o menor.
* **Interface Responsiva:** Design adaptável para dispositivos móveis e desktops, com distinção visual entre os ingredientes que o usuário já possui e os que estão faltando.

---

## Tecnologias Utilizadas

* **Backend:** PHP 8.x (Lógica de matrizes com array_intersect, array_diff e buscas parciais com strpos)
* **Frontend:** HTML5, CSS3 (Variáveis CSS, CSS Grid e Flexbox)
* **Ícones:** FontAwesome (via CDN)
* **Fontes:** Google Fonts (Plus Jakarta Sans)

---

## Como Executar o Projeto Localmente

Como o sistema foi construído em PHP puro, é necessário um ambiente de servidor local como XAMPP, WampServer ou Laragon.

1. Faça o download ou clone este repositório:
```bash
   git clone [https://github.com/tomasouza2007/geladeira-inteligente.git](https://github.com/tomasouza2007/geladeira-inteligente.git)
